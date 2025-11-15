<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use Modules\AI\App\Services\OpenAIService;
use Modules\AI\App\Services\AIImageGenerationService;
use App\Services\AI\TenantBlogPromptEnhancer;
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
    protected TenantBlogPromptEnhancer $tenantEnhancer;

    public function __construct(
        TenantPromptLoader $promptLoader,
        TenantBlogPromptEnhancer $tenantEnhancer
    ) {
        $this->promptLoader = $promptLoader;
        $this->tenantEnhancer = $tenantEnhancer;
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
            // Slug oluştur (başlıktan) - JSON array formatında
            $slug = \Illuminate\Support\Str::slug($blogData['title']);

            // Blog oluştur
            $blog = Blog::create([
                'title' => ['tr' => $blogData['title']],
                'slug' => ['tr' => $slug], // FIX: Slug array olmalı (JSON column)
                'body' => ['tr' => $blogData['content']],
                'excerpt' => ['tr' => $blogData['excerpt']],
                'faq_data' => $blogData['faq_data'], // Universal Schema: FAQ
                'howto_data' => $blogData['howto_data'], // Universal Schema: HowTo
                'is_active' => true, // Yayınla (aktif hale getir)
                'is_featured' => false,
                'published_at' => now(), // Yayınlanma tarihi (null ise hemen yayında)
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
                'titles' => ['tr' => $blogData['title']],
                'descriptions' => ['tr' => $draft->meta_description ?? $blogData['excerpt']],
                'status' => 'active',
            ]);

            // 🎨 AI Image Generation: DISABLED (MediaLibrary tenant context issue)
            // TODO: Fix MediaLibrary PerformConversionsJob tenant context
            /*
            try {
                $imageService = app(AIImageGenerationService::class);
                $mediaItem = $imageService->generateForBlog(
                    $blogData['title'],
                    $blogData['content']
                );
                $media = $mediaItem->getFirstMedia('library');
                if ($media) {
                    $blogTitle = $blogData['title'];
                    $media->setCustomProperty('alt_text', ['tr' => $blogTitle]);
                    $media->setCustomProperty('title', ['tr' => $blogTitle . ' - Ana Görsel']);
                    $media->setCustomProperty('description', ['tr' => $blogData['excerpt']]);
                    $media->setCustomProperty('width', 1200);
                    $media->setCustomProperty('height', 630);
                    $media->setCustomProperty('seo_optimized', true);
                    $media->save();
                    $blog->addMedia($media->getPath())
                        ->preservingOriginal()
                        ->withCustomProperties([
                            'alt_text' => ['tr' => $blogTitle],
                            'title' => ['tr' => $blogTitle . ' - Blog Görseli'],
                            'description' => ['tr' => $blogData['excerpt']],
                            'width' => 1200,
                            'height' => 630,
                            'seo_optimized' => true,
                            'og_image' => true,
                        ])
                        ->toMediaCollection('featured');
                    Log::info('Blog AI Featured Image Generated (SEO Optimized)', [
                        'blog_id' => $blog->blog_id,
                        'media_library_id' => $mediaItem->id,
                        'media_id' => $media->id,
                        'prompt' => $mediaItem->generation_prompt,
                        'seo_alt' => $blogTitle,
                        'seo_title' => $blogTitle . ' - Ana Görsel',
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Blog AI Featured Image Generation Failed', [
                    'blog_id' => $blog->blog_id,
                    'error' => $e->getMessage(),
                ]);
            }
            */

            // 🖼️ Content İçi Görseller: DISABLED (MediaLibrary tenant context issue)
            // TODO: Fix MediaLibrary PerformConversionsJob tenant context
            /*
            try {
                $updatedContent = $this->generateInlineImages($blog, $blogData['content']);
                if ($updatedContent !== $blogData['content']) {
                    $blog->update(['body' => ['tr' => $updatedContent]]);
                    Log::info('Blog AI Inline Images Added', [
                        'blog_id' => $blog->blog_id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Blog AI Inline Images Failed', [
                    'blog_id' => $blog->blog_id,
                    'error' => $e->getMessage(),
                ]);
            }
            */

            // Credit düş - 1 blog = 1.0 kredi
            ai_use_credits(1.0, null, [
                'usage_type' => 'blog_content_generation',
                'blog_id' => $blog->blog_id,
                'draft_id' => $draft->id,
                'tenant_id' => tenant('id'),
            ]);

            DB::commit();

            // Draft'ı güncelle (transaction dışında - foreign key koruması için)
            $draft->update([
                'is_generated' => true,
                'generated_blog_id' => $blog->blog_id,
            ]);

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

        // Firma Adı - SADECE MARKA ADI
        $companyName = $context['company_info']['name'] ?? 'FİRMA ADI';

        // 🔍 DEBUG: Context'i logla
        Log::info('🔍 Blog AI Company Context Debug', [
            'draft_id' => $draft->id,
            'company_name' => $companyName,
        ]);

        // 🚨 SORUN ÇÖZÜMÜ: AI'ın context içindeki uzun adı kullanmasını engelle
        // Context'teki TÜM firma adı referanslarını kısa ad ile override et
        $shortName = $context['company_info']['title'] ?? $companyName;

        // Uzun ad varsa onu da kaydet (validation için)
        $longName = $context['company_info']['company_name'] ?? null;

        $companyContext = "\n\n" . str_repeat('=', 60) . "\n";
        $companyContext .= "🔴 KRİTİK: FİRMA ADI - MUTLAKA KULLAN!\n";
        $companyContext .= str_repeat('=', 60) . "\n\n";
        $companyContext .= "MARKA ADI: {$shortName}\n";
        $companyContext .= "⚠️ SADECE bu kısa adı kullan: '{$shortName}'\n";
        if ($longName) {
            $companyContext .= "❌ UZUN ADI KULLANMA: '{$longName}'\n";
        }
        $companyContext .= ">>> Bu MARKA adını blog içinde EN AZ 3 KEZ kullanacaksın!\n";
        $companyContext .= ">>> Örnek: \"{$shortName} olarak...\"\n";
        $companyContext .= ">>> Örnek: \"{$shortName} ekibi...\"\n";
        $companyContext .= ">>> Örnek: \"Detaylı bilgi için {$shortName} ile iletişime geçin.\"\n";
        $companyContext .= str_repeat('=', 60) . "\n";

        // Tenant-specific enhancement al (varsa)
        $tenantEnhancement = $this->tenantEnhancer->getEnhancement();
        $tenantContext = '';
        if (!empty($tenantEnhancement)) {
            // Tenant-specific context'i format'la ve ekle
            $tenantContext = $this->tenantEnhancer->buildPromptContext($tenantEnhancement);
        }

        // 📝 Detaylı blog yazma talimatları al (2000+ kelime, FAQ, HowTo kuralları)
        $blogContentPrompt = $this->promptLoader->getBlogContentPrompt();

        // System message - Detaylı talimatlar + Context
        $systemMessage = $blogContentPrompt . "\n\n" .
                        "---\n\n" .
                        $companyContext .
                        $tenantContext .
                        "\n\n**TASLAK:**\n" .
                        json_encode($draftContext, JSON_UNESCAPED_UNICODE);

        // 🔁 RETRY MEKANIZMASI: Boş veya kısa response için 3 deneme
        $maxRetries = 3;
        $attempt = 0;
        $blogData = null;
        $validData = false;  // ✅ Validation flag

        while ($attempt < $maxRetries && !$validData) {
            $attempt++;

            if ($attempt > 1) {
                Log::warning("Blog AI Content Generation Retry", [
                    'draft_id' => $draft->id,
                    'attempt' => $attempt,
                ]);
                sleep(2); // 2 saniye bekle
            }

            try {
                // 🔄 ITERATIVE APPROACH: Her bölümü ayrı ayrı genişlet (2500+ kelime için)
                Log::info('🔄 Iterative blog generation başlıyor', ['draft_id' => $draft->id]);

                // 1. Outline oluştur (H2 başlıklar)
                $outlinePrompt = "'{$draftContext['topic_keyword']}' konusu için blog outline'ı oluştur.

6-8 H2 başlık belirle. JSON array döndür:
[\"Başlık 1\", \"Başlık 2\", \"Başlık 3\", ...]

Sadece JSON array döndür, başka bir şey yazma.";

                $outlineResponse = $this->openaiService->ask($outlinePrompt, false, [
                    'custom_prompt' => "Sen bir blog içerik planlamacısısın. Verilen konu için SEO-uyumlu H2 başlıkları belirle.",
                    'temperature' => 0.7,
                    'max_tokens' => 1000,
                    'model' => 'gpt-4o',
                ]);

                // Outline parse et
                $outline = json_decode(trim($outlineResponse), true);
                if (!is_array($outline) || empty($outline)) {
                    // Fallback: Tenant-aware outline
                    $outline = $this->promptLoader->getFallbackOutline($draftContext['topic_keyword']);
                    Log::info('📋 Fallback outline kullanılıyor (tenant-aware)', [
                        'h2_count' => count($outline),
                    ]);
                }

                Log::info('📝 Outline oluşturuldu', ['h2_count' => count($outline)]);

                // 2. Her H2 bölümünü genişlet
                $fullContent = '';
                foreach ($outline as $index => $h2Title) {
                    $sectionPrompt = "'{$h2Title}' konusunda detaylı bölüm yaz.

- 3-4 paragraf (her biri 100-150 kelime)
- 2-3 H3 alt başlık ekle
- Örnekler, sayısal veriler, karşılaştırma kullan
- Firma adı: '{$shortName}' (ilk/son bölümde kullan)

HTML çıktı döndür:
<h2>{$h2Title}</h2>
<p>...</p>
<h3>Alt başlık</h3>
<p>...</p>";

                    $sectionResponse = $this->openaiService->ask($sectionPrompt, false, [
                        'custom_prompt' => $systemMessage,
                        'temperature' => 0.8,
                        'max_tokens' => 4000,  // ⬆️ Increased from 2000 to prevent truncation
                        'model' => 'gpt-4o',
                    ]);

                    $fullContent .= "\n\n" . trim($sectionResponse);

                    $currentSection = $index + 1;
                    $totalSections = count($outline);
                    Log::info("✅ Bölüm {$currentSection}/{$totalSections} oluşturuldu", [
                        'h2' => $h2Title,
                        'length' => strlen($sectionResponse),
                    ]);

                    sleep(1); // Rate limit için
                }

                // 3. FAQ üret
                $faqPrompt = "'{$draftContext['topic_keyword']}' konusunda 10 sık sorulan soru ve cevapları oluştur.

Her cevap 50-80 kelime olsun. JSON array döndür:
[{\"question\": {\"tr\": \"Soru?\"}, \"answer\": {\"tr\": \"Cevap...\"}}]";

                $faqResponse = $this->openaiService->ask($faqPrompt, false, [
                    'temperature' => 0.7,
                    'max_tokens' => 3000,  // ⬆️ Increased for 10 FAQ items
                    'model' => 'gpt-4o',
                ]);

                // Extract JSON from code block if wrapped
                $faqResponseClean = trim($faqResponse);
                if (preg_match('/```json\s*(.*?)\s*```/s', $faqResponseClean, $matches)) {
                    $faqResponseClean = $matches[1];
                } elseif (preg_match('/```\s*(.*?)\s*```/s', $faqResponseClean, $matches)) {
                    $faqResponseClean = $matches[1];
                }

                $faqData = json_decode(trim($faqResponseClean), true);
                if (!is_array($faqData)) {
                    Log::warning('FAQ generation failed to parse', [
                        'draft_id' => $draft->id,
                        'response_preview' => substr($faqResponse, 0, 500),
                        'json_error' => json_last_error_msg(),
                    ]);
                    $faqData = [];
                }

                // 4. HowTo üret
                $howtoPrompt = "'{$draftContext['topic_keyword']}' için 7 adımlı 'Nasıl Yapılır' rehberi oluştur.

Her adım 80-100 kelime olsun. JSON döndür:
{\"name\": {\"tr\": \"Başlık\"}, \"description\": {\"tr\": \"Açıklama\"}, \"steps\": [{\"name\": {\"tr\": \"Adım\"}, \"text\": {\"tr\": \"Detay\"}}]}";

                $howtoResponse = $this->openaiService->ask($howtoPrompt, false, [
                    'temperature' => 0.7,
                    'max_tokens' => 3000,  // ⬆️ Increased for 7 HowTo steps
                    'model' => 'gpt-4o',
                ]);

                // Extract JSON from code block if wrapped
                $howtoResponseClean = trim($howtoResponse);
                if (preg_match('/```json\s*(.*?)\s*```/s', $howtoResponseClean, $matches)) {
                    $howtoResponseClean = $matches[1];
                } elseif (preg_match('/```\s*(.*?)\s*```/s', $howtoResponseClean, $matches)) {
                    $howtoResponseClean = $matches[1];
                }

                $howtoData = json_decode(trim($howtoResponseClean), true);
                if (!is_array($howtoData)) {
                    Log::warning('HowTo generation failed to parse', [
                        'draft_id' => $draft->id,
                        'response_preview' => substr($howtoResponse, 0, 500),
                        'json_error' => json_last_error_msg(),
                    ]);
                    $howtoData = [];
                }

                // 5. Birleştir
                $blogData = [
                    'title' => $draftContext['topic_keyword'],
                    'content' => $fullContent,
                    'excerpt' => $draftContext['meta_description'] ?? substr(strip_tags($fullContent), 0, 200),
                    'faq_data' => $faqData,
                    'howto_data' => $howtoData,
                ];

                $wordCount = str_word_count(strip_tags($fullContent));
                Log::info('🎉 Iterative generation tamamlandı', [
                    'word_count' => $wordCount,
                    'h2_count' => count($outline),
                    'faq_count' => count($faqData),
                    'howto_steps' => count($howtoData['steps'] ?? []),
                ]);

                // ✅ Iterative generation - data zaten hazır, parse'a gerek yok!
                $parsedData = $blogData;

                // Validation: Boş veya çok kısa içerik kontrolü
                if (empty($parsedData['title']) || empty($parsedData['content'])) {
                    Log::warning("AI response missing fields (attempt {$attempt})", [
                        'draft_id' => $draft->id,
                    ]);
                    continue; // Retry
                }

                // Kelime sayısı kontrolü (minimum 1500 kelime - prompt kurallarına uygun)
                $wordCount = str_word_count(strip_tags($parsedData['content']));
                if ($wordCount < 1500) {
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

                $validData = true;  // ✅ Validation passed, exit retry loop

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

    /**
     * Content içine H2 başlıklarından sonra AI görselleri ekle
     *
     * @param Blog $blog
     * @param string $content HTML içeriği
     * @return string Görsellerle güncellenmiş HTML
     */
    protected function generateInlineImages(Blog $blog, string $content): string
    {
        // DOMDocument ile HTML parse et
        $dom = new \DOMDocument('1.0', 'UTF-8');

        // HTML5 ve UTF-8 sorunlarını önle
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // H2 başlıklarını bul
        $h2Tags = $dom->getElementsByTagName('h2');

        if ($h2Tags->length === 0) {
            Log::info('No H2 tags found for inline images', ['blog_id' => $blog->blog_id]);
            return $content;
        }

        $imageService = app(AIImageGenerationService::class);
        $imagesAdded = 0;

        // H2'leri array'e al (DOMNodeList değişken boyutlu olduğu için)
        $h2Array = [];
        foreach ($h2Tags as $h2) {
            $h2Array[] = $h2;
        }

        // ⚠️ KRİTİK: Sadece ilk 2 H2'ye görsel ekle (toplam 3 görsel: 1 featured + 2 inline)
        $maxImages = 2;
        $h2Array = array_slice($h2Array, 0, $maxImages);

        Log::info('Inline images will be generated', [
            'blog_id' => $blog->blog_id,
            'total_h2_count' => $h2Tags->length,
            'selected_h2_count' => count($h2Array),
            'max_images' => $maxImages,
        ]);

        // İlk 3 H2 için görsel üret ve ekle
        foreach ($h2Array as $index => $h2) {
            // H2 başlık metnini al
            $h2Text = trim($h2->textContent);

            if (empty($h2Text)) {
                continue;
            }

            try {
                // AI görsel prompt oluştur (H2 başlığından)
                $imagePrompt = "Professional illustration for blog section: {$h2Text}. " .
                              "Modern, clean style, landscape orientation (16:9), " .
                              "high quality, suitable for blog article. " .
                              "Related to industrial equipment and machinery.";

                // Görsel üret (yatay 16:9)
                $mediaItem = $imageService->generate($imagePrompt, [
                    'width' => 1200,
                    'height' => 675, // 16:9 ratio
                    'model' => 'dall-e-3', // veya stable-diffusion
                ]);

                if ($mediaItem) {
                    $media = $mediaItem->getFirstMedia('library');

                    if ($media) {
                        // SEO Meta Data ekle (Media model'e)
                        $media->setCustomProperty('alt_text', ['tr' => $h2Text]);
                        $media->setCustomProperty('title', ['tr' => $h2Text]);
                        $media->setCustomProperty('description', ['tr' => "Blog görseli: {$h2Text} - {$blog->getTranslated('title', 'tr')}"]);
                        $media->setCustomProperty('width', 1200);
                        $media->setCustomProperty('height', 675);
                        $media->save();

                        // Blog'a gallery olarak attach et
                        $blog->addMedia($media->getPath())
                            ->preservingOriginal()
                            ->withCustomProperties([
                                'alt_text' => ['tr' => $h2Text],
                                'title' => ['tr' => $h2Text],
                                'description' => ['tr' => "Blog içi görsel: {$h2Text}"],
                                'width' => 1200,
                                'height' => 675,
                                'seo_optimized' => true,
                            ])
                            ->toMediaCollection('gallery');

                        // Görsel URL'ini al
                        $imageUrl = $media->getUrl();

                        // Figure elementi oluştur (responsive + SEO friendly)
                        $figure = $dom->createElement('figure');
                        $figure->setAttribute('class', 'blog-inline-image my-8');
                        $figure->setAttribute('style', 'margin: 2rem 0;');

                        $img = $dom->createElement('img');
                        $img->setAttribute('src', $imageUrl);
                        $img->setAttribute('alt', $h2Text); // SEO: Alt text
                        $img->setAttribute('title', $h2Text); // SEO: Title
                        $img->setAttribute('loading', 'lazy'); // Performance: Lazy loading
                        $img->setAttribute('width', '1200'); // SEO: Explicit dimensions
                        $img->setAttribute('height', '675'); // SEO: Explicit dimensions
                        $img->setAttribute('decoding', 'async'); // Performance: Async decode
                        $img->setAttribute('fetchpriority', 'low'); // Performance: Low priority (inline images)
                        $img->setAttribute('itemprop', 'image'); // Schema.org: Image property
                        $img->setAttribute('style', 'width: 100%; height: auto; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);');

                        $figure->appendChild($img);

                        // Figcaption ekle (başlık metni)
                        $figcaption = $dom->createElement('figcaption', $h2Text);
                        $figcaption->setAttribute('style', 'margin-top: 0.75rem; text-align: center; font-size: 0.875rem; color: #6b7280; font-style: italic;');
                        $figure->appendChild($figcaption);

                        // H2'den sonra ekle
                        $h2->parentNode->insertBefore($figure, $h2->nextSibling);

                        $imagesAdded++;

                        Log::info('Inline image added after H2', [
                            'blog_id' => $blog->blog_id,
                            'h2_text' => $h2Text,
                            'image_url' => $imageUrl,
                        ]);
                    }
                }

            } catch (\Exception $e) {
                Log::warning('Inline image generation failed for H2', [
                    'blog_id' => $blog->blog_id,
                    'h2_text' => $h2Text,
                    'error' => $e->getMessage(),
                ]);
                // Hata olsa bile diğer H2'lere devam et
                continue;
            }
        }

        if ($imagesAdded === 0) {
            return $content; // Hiç görsel eklenemediyse orijinali döndür
        }

        // Güncellenmiş HTML'i döndür
        $updatedContent = $dom->saveHTML();

        // XML encoding prefix'ini kaldır
        $updatedContent = str_replace('<?xml encoding="UTF-8">', '', $updatedContent);

        Log::info('Inline images generation completed', [
            'blog_id' => $blog->blog_id,
            'images_added' => $imagesAdded,
            'h2_count' => count($h2Array),
        ]);

        return $updatedContent;
    }
}
