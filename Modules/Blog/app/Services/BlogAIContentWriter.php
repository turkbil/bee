<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use OpenAI;
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
    protected $openai;

    public function __construct(TenantPromptLoader $promptLoader)
    {
        $this->promptLoader = $promptLoader;
        $this->openai = OpenAI::client(config('services.openai.api_key'));
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
            // Blog oluştur
            $blog = Blog::create([
                'title' => ['tr' => $blogData['title']],
                'body' => ['tr' => $blogData['content']],
                'excerpt' => ['tr' => $blogData['excerpt']],
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

        $systemMessage = $prompt . "\n\n**TASLAK BİLGİLERİ:**\n" . json_encode($draftContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        try {
            $response = $this->openai->chat()->create([
                'model' => config('modules.blog.openai.model', 'gpt-4-turbo-preview'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => "Lütfen bu taslak için tam blog yazısı oluştur. JSON formatında döndür: {title, content, excerpt}"],
                ],
                'temperature' => config('modules.blog.openai.blog_temperature', 0.8),
                'max_tokens' => config('modules.blog.openai.blog_max_tokens', 8000),
            ]);

            $content = $response->choices[0]->message->content;

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
        ];
    }
}
