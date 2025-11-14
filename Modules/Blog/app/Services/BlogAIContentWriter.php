<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use Modules\AI\App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Blog AI Content Writer Service
 *
 * Seçilmiş taslakları tam blog yazısına dönüştürür
 * SEO ayarları ekler, kategorileri attach eder
 */
class BlogAIContentWriter
{
    protected TenantPromptLoader $promptLoader;
    protected OpenAIService $openaiService;

    public function __construct(TenantPromptLoader $promptLoader)
    {
        $this->promptLoader = $promptLoader;
        // Mevcut AI sistemi - AIProvider modelinden API key çeker
        $this->openaiService = new OpenAIService();
    }

    /**
     * Taslaktan tam blog yazısı oluştur
     *
     * @param BlogAIDraft $draft
     * @return Blog
     */
    public function generateBlogFromDraft(BlogAIDraft $draft): Blog
    {
        // Credit kontrolü - 1 blog = 1.0 kredi
        if (!ai_can_use_credits(1.0)) {
            throw new \Exception('Yetersiz AI kredisi. Lütfen kredi satın alın.');
        }

        // AI ile blog içeriği oluştur
        $blogData = $this->generateContent($draft);

        // Database transaction ile blog + SEO oluştur
        DB::beginTransaction();

        try {
            // Slug oluştur (başlıktan)
            $slug = \Illuminate\Support\Str::slug($blogData['title']);

            // Blog oluştur
            $blog = Blog::create([
                'title' => ['tr' => $blogData['title']],
                'slug' => $slug,
                'body' => ['tr' => $blogData['content']],
                'excerpt' => ['tr' => $blogData['excerpt']],
                'faq_data' => $blogData['faq_data'], // Universal Schema: FAQ
                'howto_data' => $blogData['howto_data'], // Universal Schema: HowTo
                'status' => 'draft', // Admin onayına sunulacak
                'is_active' => false,
                'is_featured' => false,
            ]);

            // Kategorileri attach et
            if (!empty($draft->category_suggestions)) {
                // İlk kategori primary olarak blog_category_id'ye
                $blog->update(['blog_category_id' => $draft->category_suggestions[0]]);

                // Diğer kategorileri ilişkilendir (eğer ManyToMany varsa)
                // $blog->categories()->attach($draft->category_suggestions);
            }

            // SEO ayarları ekle (HasSeo trait)
            $blog->seoSetting()->create([
                'title' => ['tr' => $blogData['title']],
                'description' => ['tr' => $draft->meta_description ?? $blogData['excerpt']],
                'keywords' => $draft->seo_keywords ?? [],
                'status' => 'active',
            ]);

            // Draft'ı güncelle
            $draft->update([
                'is_generated' => true,
                'generated_blog_id' => $blog->blog_id,
            ]);

            // Credit düş - 1 blog = 1.0 kredi
            ai_use_credits(1.0, null, [
                'usage_type' => 'blog_content_generation',
                'blog_id' => $blog->blog_id,
                'draft_id' => $draft->id,
                'tenant_id' => tenant('id'),
            ]);

            DB::commit();

            Log::info('Blog AI Content Generated', [
                'blog_id' => $blog->blog_id,
                'draft_id' => $draft->id,
                'tenant_id' => tenant('id'),
            ]);

            return $blog;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Blog AI Content Generation Failed', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);

            throw $e;
        }
    }

    /**
     * AI ile blog içeriği oluştur
     */
    protected function generateContent(BlogAIDraft $draft): array
    {
        $prompt = $this->promptLoader->getBlogContentPrompt();
        $context = $this->promptLoader->getTenantContext();

        // Taslak bilgilerini context'e ekle
        $draftContext = [
            'topic_keyword' => $draft->topic_keyword,
            'outline' => $draft->outline,
            'seo_keywords' => $draft->seo_keywords,
            'meta_description' => $draft->meta_description,
        ];

        // Firma & İletişim Bilgileri
        $companyContext = '';
        if (!empty($context['company_info'])) {
            $companyContext .= "\n\n**FİRMA BİLGİLERİ (Blog yazısında kullanılacak):**\n";
            $companyContext .= "- Firma Adı: " . ($context['company_info']['name'] ?? 'N/A') . "\n";
            $companyContext .= "- Site Başlığı: " . ($context['company_info']['title'] ?? 'N/A') . "\n";
            $companyContext .= "- Website: " . ($context['company_info']['website'] ?? 'N/A') . "\n";
        }
        if (!empty($context['contact_info'])) {
            $companyContext .= "\n**İLETİŞİM BİLGİLERİ (CTA'da kullanılacak):**\n";
            $companyContext .= "- Email: " . ($context['contact_info']['email'] ?? 'N/A') . "\n";
            $companyContext .= "- Telefon: " . ($context['contact_info']['phone'] ?? 'N/A') . "\n";
        }

        $systemMessage = $prompt . $companyContext . "\n\n**TASLAK BİLGİLERİ:**\n" . json_encode($draftContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        try {
            $userPrompt = "Lütfen bu taslak için tam blog yazısı oluştur. JSON formatında döndür: {title, content, excerpt, faq_data, howto_data}. \n\nfaq_data: En az 5-10 adet soru-cevap içermeli. Format: [{\"question\":{\"tr\":\"...\"}, \"answer\":{\"tr\":\"...\"}}]\n\nhowto_data: Adım adım kılavuz. Format: {\"name\":{\"tr\":\"...\"}, \"description\":{\"tr\":\"...\"}, \"steps\":[{\"name\":{\"tr\":\"...\"}, \"text\":{\"tr\":\"...\"}}]}";
            $response = $this->openaiService->ask($userPrompt, false, [
                'custom_prompt' => $systemMessage,
                'temperature' => 0.8,
                'max_tokens' => 8000,
            ]);

            // ask() metodu direkt string döndürür
            $content = $response;

            // JSON parse
            $blogData = $this->parseAIResponse($content);

            // Validation
            if (empty($blogData['title']) || empty($blogData['content'])) {
                throw new \Exception('AI response missing required fields (title or content)');
            }

            return $blogData;

        } catch (\Exception $e) {
            Log::error('Blog AI Content API Failed', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * AI response'u parse et
     */
    protected function parseAIResponse(string $content): array
    {
        // JSON extract (markdown code block içinde olabilir)
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('AI response JSON parse error: ' . json_last_error_msg());
        }

        // Varsayılan değerler
        return [
            'title' => $decoded['title'] ?? 'Başlıksız Blog',
            'content' => $decoded['content'] ?? '',
            'excerpt' => $decoded['excerpt'] ?? substr(strip_tags($decoded['content'] ?? ''), 0, 200),
            'faq_data' => $decoded['faq_data'] ?? null,
            'howto_data' => $decoded['howto_data'] ?? null,
        ];
    }
}
