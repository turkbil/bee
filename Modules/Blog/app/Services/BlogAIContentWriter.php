<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use Modules\AI\App\Services\OpenAIService;
use Modules\AI\App\Services\AIImageGenerationService;
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

            // 🎨 AI Image Generation: Featured image oluştur
            try {
                $imageService = app(AIImageGenerationService::class);
                $featuredImage = $imageService->generateForBlog(
                    $blogData['title'],
                    $blogData['content']
                );

                // Medyayı blog'a attach et (featured image olarak)
                $blog->addMedia($featuredImage->getFirstMedia('library')->getPath())
                    ->preservingOriginal()
                    ->toMediaCollection('featured');

                Log::info('Blog AI Featured Image Generated', [
                    'blog_id' => $blog->blog_id,
                    'media_id' => $featuredImage->id,
                ]);
            } catch (\Exception $e) {
                // Image generation hatası blog oluşumunu engellemesin
                Log::warning('Blog AI Featured Image Generation Failed', [
                    'blog_id' => $blog->blog_id,
                    'error' => $e->getMessage(),
                ]);
            }

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

        // Firma & İletişim Bilgileri - ULTRA VURGULU
        $companyName = $context['company_info']['name'] ?? 'FİRMA ADI';
        $companyEmail = $context['contact_info']['email'] ?? 'EMAIL';
        $companyPhone = $context['contact_info']['phone'] ?? 'TELEFON';

        // 🔍 DEBUG: Context'i logla
        Log::info('🔍 Blog AI Company Context Debug', [
            'draft_id' => $draft->id,
            'company_name' => $companyName,
            'full_context' => $context,
        ]);

        // 🚨 SORUN ÇÖZÜMÜ: AI'ın context içindeki uzun adı kullanmasını engelle
        // Context'teki TÜM firma adı referanslarını kısa ad ile override et
        $shortName = $context['company_info']['title'] ?? $companyName;

        // Uzun ad varsa onu da kaydet (validation için)
        $longName = $context['company_info']['company_name'] ?? null;

        $companyContext = "\n\n" . str_repeat('=', 60) . "\n";
        $companyContext .= "🔴 KRİTİK: FİRMA BİLGİLERİ - MUTLAKA KULLAN!\n";
        $companyContext .= str_repeat('=', 60) . "\n\n";
        $companyContext .= "FİRMA ADI: {$shortName}\n";
        $companyContext .= "⚠️ SADECE bu kısa adı kullan: '{$shortName}'\n";
        if ($longName) {
            $companyContext .= "❌ UZUN ADI KULLANMA: '{$longName}'\n";
        }
        $companyContext .= ">>> Bu KISA adı blog içinde EN AZ 3 KEZ kullanacaksın!\n";
        $companyContext .= ">>> Örnek: \"{$shortName} olarak...\"\n";
        $companyContext .= ">>> Örnek: \"{$shortName} ekibi...\"\n\n";
        $companyContext .= "İLETİŞİM:\n";
        $companyContext .= "Email: {$companyEmail}\n";
        $companyContext .= "Telefon: {$companyPhone}\n";
        $companyContext .= ">>> CTA bölümünde bu bilgileri HTML liste olarak ekle!\n";
        $companyContext .= str_repeat('=', 60) . "\n";

        // System message'ı basitleştir - KISA firma adı vurgulu!
        $systemMessage = "Sen bir blog yazarısın. Yazarken SADECE ve SADECE FİRMA ADI '{$shortName}' kullanacaksın!\n\n" .
                        $companyContext .
                        "\n\n**TASLAK:**\n" .
                        json_encode($draftContext, JSON_UNESCAPED_UNICODE);

        // 🔁 RETRY MEKANIZMASI: Boş veya kısa response için 3 deneme
        $maxRetries = 3;
        $attempt = 0;
        $blogData = null;

        while ($attempt < $maxRetries && !$blogData) {
            $attempt++;

            if ($attempt > 1) {
                Log::warning("Blog AI Content Generation Retry", [
                    'draft_id' => $draft->id,
                    'attempt' => $attempt,
                ]);
                sleep(2); // 2 saniye bekle
            }

            try {
                // Basit ve direkt prompt - KISA firma adını direkt ekle
                $userPrompt = "Detaylı blog yazısı oluştur (1500+ kelime, Türkçe).

🔴 ZORUNLU: SADECE '{$shortName}' firma adını kullan - EN AZ 3 KEZ!
❌ '{$longName}' gibi uzun firma adı KULLANMA!

ÖRNEK KULLANIM (SADECE KISA AD):
- '{$shortName} olarak, endüstriyel ekipman sektöründe deneyimimizle...'
- '{$shortName} uzman ekibi size destek sağlar.'
- 'Detaylı bilgi için {$shortName} ile iletişime geçin.'

İLETİŞİM BÖLÜMÜ önyazı (HTML):
<h2>İletişim</h2>
<p>{$shortName} olarak profesyonel destek:</p>
<ul><li><strong>Tel:</strong> {$companyPhone}</li><li><strong>Email:</strong> {$companyEmail}</li></ul>

JSON ÇIKTI:
{\"title\": \"başlık\", \"content\": \"<h2>...</h2><p>...{$shortName}...</p>\", \"excerpt\": \"özet\", \"faq_data\": [{\"question\": {\"tr\": \"?\"}, \"answer\": {\"tr\": \"cevap\"}}], \"howto_data\": {\"name\": {\"tr\": \"nasıl\"}, \"description\": {\"tr\": \"açıklama\"}, \"steps\": [{\"name\": {\"tr\": \"adım\"}, \"text\": {\"tr\": \"detay\"}}]}}";

            $response = $this->openaiService->ask($userPrompt, false, [
                'custom_prompt' => $systemMessage,
                'temperature' => 0.4, // Tutarlı output için düşük temperature
                'max_tokens' => 16000,
            ]);

            // ask() metodu direkt string döndürür
            $content = $response;

                // JSON parse
                $parsedData = $this->parseAIResponse($content);

                // Validation: Boş veya çok kısa içerik kontrolü
                if (empty($parsedData['title']) || empty($parsedData['content'])) {
                    Log::warning("AI response missing fields (attempt {$attempt})", [
                        'draft_id' => $draft->id,
                    ]);
                    continue; // Retry
                }

                // Kelime sayısı kontrolü (minimum 500 kelime - gerçekçi hedef)
                $wordCount = str_word_count(strip_tags($parsedData['content']));
                if ($wordCount < 500) {
                    Log::warning("AI response too short: {$wordCount} words (attempt {$attempt})", [
                        'draft_id' => $draft->id,
                    ]);
                    continue; // Retry
                }

                // 🏢 KRİTİK: Firma adı kontrolü - hem kısa hem uzun adı kontrol et
                $shortMentions = substr_count($parsedData['content'], $shortName);
                $longMentions = $longName ? substr_count($parsedData['content'], $longName) : 0;
                $totalMentions = $shortMentions + $longMentions;

                Log::info("🔍 Company Name Validation", [
                    'draft_id' => $draft->id,
                    'attempt' => $attempt,
                    'short_name' => $shortName,
                    'short_mentions' => $shortMentions,
                    'long_name' => $longName,
                    'long_mentions' => $longMentions,
                    'total_mentions' => $totalMentions,
                    'content_preview' => substr($parsedData['content'], 0, 300),
                ]);

                // ⚠️ İdeal: Sadece kısa ad kullanılmalı (min 3 kez)
                // ✅ Kabul: Uzun ad da kullanılmış olabilir (min 1 toplam)
                if ($totalMentions < 1) {
                    Log::warning("AI response missing company name (attempt {$attempt})", [
                        'draft_id' => $draft->id,
                        'short_name' => $shortName,
                        'long_name' => $longName,
                        'total_mentions' => $totalMentions,
                    ]);
                    continue; // Retry
                }

                // 🎯 İdeal durum: Kısa ad 3+ kez kullanılmış
                if ($shortMentions >= 3) {
                    Log::info("✅ Perfect! Short company name used {$shortMentions} times", [
                        'draft_id' => $draft->id,
                        'short_name' => $shortName,
                    ]);
                } elseif ($longMentions > 0) {
                    Log::warning("⚠️ AI used long company name ({$longMentions}x) instead of short ({$shortMentions}x)", [
                        'draft_id' => $draft->id,
                        'short_name' => $shortName,
                        'long_name' => $longName,
                    ]);
                }

                // ✅ Başarılı! Placeholder replace yap ve döndür
                $blogData = $this->replacePlaceholders($parsedData, $context);

                Log::info("Blog AI Content Generated Successfully", [
                    'draft_id' => $draft->id,
                    'word_count' => $wordCount,
                    'attempts' => $attempt,
                ]);

            } catch (\Exception $e) {
                Log::error("Blog AI Content API Failed (attempt {$attempt})", [
                    'draft_id' => $draft->id,
                    'error' => $e->getMessage(),
                ]);

                if ($attempt >= $maxRetries) {
                    throw $e; // Son deneme de başarısız oldu
                }
                // Retry devam eder
            }
        }

        // Retry loop bitti ama başarılı sonuç yok
        if (!$blogData) {
            throw new \Exception("AI blog generation failed after {$maxRetries} attempts");
        }

        return $blogData;
    }

    /**
     * AI response'u parse et
     */
    protected function parseAIResponse(string $content): array
    {
        // 🔍 ULTRA DEBUG: Raw content'i dosyaya yaz
        $debugFile = '/tmp/ai-response-debug-' . time() . '.txt';
        file_put_contents($debugFile, "=== ORIGINAL RESPONSE ===\n" . $content . "\n\n");

        // JSON extract (markdown code block içinde olabilir)
        $originalContent = $content;
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
            file_put_contents($debugFile, "=== EXTRACTED (json block) ===\n" . $content . "\n\n", FILE_APPEND);
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
            file_put_contents($debugFile, "=== EXTRACTED (code block) ===\n" . $content . "\n\n", FILE_APPEND);
        } else {
            file_put_contents($debugFile, "=== NO CODE BLOCK FOUND ===\n", FILE_APPEND);
        }

        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Debug: Log the raw content
            file_put_contents($debugFile, "=== JSON ERROR ===\n" . json_last_error_msg() . "\n\n", FILE_APPEND);
            Log::error('AI Response JSON Parse Error', [
                'error' => json_last_error_msg(),
                'raw_content_length' => strlen($content),
                'raw_content_preview' => substr($content, 0, 500),
                'debug_file' => $debugFile,
            ]);
            throw new \Exception('AI response JSON parse error: ' . json_last_error_msg() . ' (Check: ' . $debugFile . ')');
        }

        file_put_contents($debugFile, "=== DECODED SUCCESS ===\n" . print_r($decoded, true) . "\n", FILE_APPEND);
        echo "✅ Debug file: $debugFile\n";

        // Varsayılan değerler
        return [
            'title' => $decoded['title'] ?? 'Başlıksız Blog',
            'content' => $decoded['content'] ?? '',
            'excerpt' => $decoded['excerpt'] ?? substr(strip_tags($decoded['content'] ?? ''), 0, 200),
            'faq_data' => $decoded['faq_data'] ?? null,
            'howto_data' => $decoded['howto_data'] ?? null,
        ];
    }

    /**
     * Replace placeholders with real company/contact info
     */
    protected function replacePlaceholders(array $blogData, array $context): array
    {
        // Placeholder → Real value mapping
        $replacements = [
            '{company_info.name}' => $context['company_info']['name'] ?? 'Our Company',
            '{company_info.title}' => $context['company_info']['title'] ?? '',
            '{company_info.website}' => $context['company_info']['website'] ?? '',
            '{contact_info.email}' => $context['contact_info']['email'] ?? 'info@example.com',
            '{contact_info.phone}' => $context['contact_info']['phone'] ?? '+90 XXX XXX XX XX',
            '{contact_info.address}' => $context['contact_info']['address'] ?? '',
        ];

        // Replace in content
        if (!empty($blogData['content'])) {
            $blogData['content'] = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $blogData['content']
            );
        }

        // Replace in excerpt
        if (!empty($blogData['excerpt'])) {
            $blogData['excerpt'] = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $blogData['excerpt']
            );
        }

        Log::info('🔧 Placeholders replaced', [
            'replacements_count' => count($replacements),
            'company_name' => $replacements['{company_info.name}'],
        ]);

        return $blogData;
    }
}
