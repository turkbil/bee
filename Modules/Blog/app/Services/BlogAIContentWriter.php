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
                'descriptions' => ['tr' => $this->cleanMetaDescription(
                    $draft->meta_description ?? $blogData['excerpt']
                )],
                'status' => 'active',
            ]);

            // 🎨 AI Image Generation (AA.pdf Professional Rules + Horizontal + NO TEXT)
            try {
                $imageService = app(AIImageGenerationService::class);

                // 1️⃣ Basit prompt oluştur (tenant-aware)
                $simplePrompt = $this->buildSimplePromptForBlog($blogData['title']);

                // 2️⃣ Check if tenant-specific prompt is already detailed (Tenant2)
                $isTenantDetailedPrompt = (tenant('id') == 2 && strlen($simplePrompt) > 200);

                // 3️⃣ AIPromptEnhancer ile ultra detaylı prompt'a çevir (GPT-4o + NO TEXT kuralları)
                // SKIP for Tenant2 if already detailed (buildImagePromptForBlog returns full prompt)
                if ($isTenantDetailedPrompt) {
                    $finalPrompt = $simplePrompt;
                    Log::info('🎨 Blog AI Image: Using tenant-specific detailed prompt (skipping enhancer)', [
                        'tenant_id' => tenant('id'),
                        'prompt_length' => strlen($finalPrompt),
                    ]);
                } else {
                    $enhancer = app(\Modules\AI\App\Services\AIPromptEnhancer::class);
                    $finalPrompt = $enhancer->enhancePrompt(
                        $simplePrompt,
                        'commercial_photography', // Style: Professional commercial photography
                        '1792x1024' // Size: Horizontal landscape 16:9
                    );
                    Log::info('🎨 Blog AI Image: Prompt enhanced via AIPromptEnhancer', [
                        'simple_prompt' => $simplePrompt,
                        'enhanced_prompt_length' => strlen($finalPrompt),
                    ]);
                }

                // 4️⃣ Zenginleştirilmiş/tenant-specific prompt ile görsel üret
                $mediaItem = $imageService->generate(
                    $finalPrompt,
                    [
                        'size' => '1792x1024',  // Horizontal landscape 16:9 ratio
                        'quality' => 'hd'       // HD quality
                    ]
                );
                $media = $mediaItem->getFirstMedia('library');
                if ($media) {
                    $blogTitle = $blogData['title'];
                    $media->setCustomProperty('alt_text', ['tr' => $blogTitle]);
                    $media->setCustomProperty('title', ['tr' => $blogTitle . ' - Ana Görsel']);
                    $media->setCustomProperty('description', ['tr' => $blogData['excerpt']]);
                    $media->setCustomProperty('width', 1792);
                    $media->setCustomProperty('height', 1024);
                    $media->setCustomProperty('seo_optimized', true);
                    $media->setCustomProperty('og_image', true);
                    $media->save();

                    // ✅ FIX: Move media to blog (no duplicate!)
                    // OLD: $blog->addMedia()->toMediaCollection('featured') → Creates duplicate!
                    // NEW: $media->move() → Moves media from MediaLibraryItem to Blog
                    // 🔧 FIX: Conversion'ları SYNC yap (queue'ya atma - tenant context sorunu!)
                    // performConversions() sync modda çalıştır, sonra move yap
                    $media->setCustomProperty('skip_conversions', true);
                    $media->save();
                    $media->move($blog, 'featured_image');
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

            // 🖼️ Content İçi Görseller: DISABLED (User requested only 1 featured image)
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

        // 📝 YAZIM STİLİ RANDOM SEÇİMİ
        $writingStyle = $this->selectWritingStyle();
        Log::info('✍️ Yazım stili seçildi', [
            'draft_id' => $draft->id,
            'style' => $writingStyle['name'],
        ]);

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

        // ✍️ Yazım stili talimatını ekle
        $styleInstructions = "\n\n" . str_repeat('=', 60) . "\n";
        $styleInstructions .= "✍️ YAZIM STİLİ - ZORUNLU!\n";
        $styleInstructions .= str_repeat('=', 60) . "\n\n";
        $styleInstructions .= "**STİL:** {$writingStyle['name']}\n";
        $styleInstructions .= "**AÇIKLAMA:** {$writingStyle['description']}\n\n";
        $styleInstructions .= "**KULLANIM KURALLARI:**\n";
        foreach ($writingStyle['rules'] as $rule) {
            $styleInstructions .= "- {$rule}\n";
        }
        $styleInstructions .= "\n**ÖRNEK CÜMLELER:**\n";
        foreach ($writingStyle['examples'] as $example) {
            $styleInstructions .= "  {$example}\n";
        }
        $styleInstructions .= str_repeat('=', 60) . "\n";

        // System message - Detaylı talimatlar + Yazım Stili + Company Context + Tenant Context
        $systemMessage = $blogContentPrompt . "\n\n" .
                        "---\n\n" .
                        $styleInstructions .
                        "\n" .
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

4-5 H2 başlık belirle. JSON array döndür:
[\"Başlık 1\", \"Başlık 2\", \"Başlık 3\", ...]

⚠️ ÖNEMLİ KURALLAR:
- Sadece 4-5 ana başlık belirle (daha fazla değil!)
- Her ana başlık detaylı alt başlıklarla genişletilecek
- Başlıklar DOĞAL ve MANİDAR olmalı (AI yazısı belli etmemeli!)
- Sadece JSON array döndür, başka bir şey yazma

🚨 YASAKLI BAŞLIKLAR (ASLA KULLANMA - AI yazısı belli eder!):
- ❌ \"Giriş\" - YASAK! (AI şablonu belli eder)
- ❌ \"Sonuç\" - YASAK! (AI şablonu belli eder)
- ❌ \"Özet\" - YASAK!
- ❌ \"Hakkında\" - YASAK!
- ❌ \"Hakkımızda\" - YASAK!
- ❌ \"İletişim\" - YASAK!
- ❌ \"Sık Sorulan Sorular\" - YASAK! (FAQ section ayrı var zaten)
- ❌ Jenerik, belirsiz başlıklar - YASAK!

✅ DOĞAL VE MANİDAR BAŞLIKLAR KULLAN:
- ✅ \"Transpalet Nedir ve Nasıl Çalışır?\"
- ✅ \"Manuel vs Elektrikli Transpalet: Hangi Tür Size Uygun?\"
- ✅ \"Transpalet Kullanırken Dikkat Edilmesi Gerekenler\"
- ✅ \"İş Güvenliği: Transpalet Kazalarını Önleme Yöntemleri\"
- ✅ \"Transpalet Bakım Periyotları ve Maliyetleri\"

❌ YANLIŞ (AI belli eder): \"Giriş\", \"Sonuç\", \"Hakkında\", \"Genel Bilgiler\"
✅ DOĞRU (Doğal ve manidar): \"Transpalet Nedir?\", \"Hangi Sektörlerde Kullanılır?\", \"Bakım ve Onarım İpuçları\"

**KRİTİK:** Başlıklar spesifik, bilgilendirici ve doğal olmalı. Okuyucu başlığı görünce içeriği tahmin edebilmeli!

Sadece JSON array döndür!";

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

                // ✅ OUTLINE VALIDATION: Duplicate ve yasaklı başlıkları temizle
                $originalCount = count($outline);
                $outline = $this->validateAndCleanOutline($outline);
                $cleanedCount = count($outline);

                if ($originalCount !== $cleanedCount) {
                    Log::warning('🧹 Outline temizlendi', [
                        'draft_id' => $draft->id,
                        'original_count' => $originalCount,
                        'cleaned_count' => $cleanedCount,
                        'removed_count' => $originalCount - $cleanedCount,
                    ]);
                }

                Log::info('📝 Outline oluşturuldu', ['h2_count' => count($outline)]);

                // 2. Her H2 bölümünü genişlet
                $fullContent = '';
                foreach ($outline as $index => $h2Title) {
                    $sectionPrompt = "'{$h2Title}' konusunda detaylı bölüm yaz.

- 3-4 paragraf (her biri 100-120 kelime)
- 2-4 H3 alt başlık ekle (her H2'ye birden fazla H3 olmalı!)
- Örnekler, sayısal veriler ekle
- Firma adı: '{$shortName}' (ilk/son bölümde kullan)

⚠️ ÖNEMLİ: İkon kullanma! Sadece düz HTML döndür.
⚠️ KRİTİK: Her H2 başlığına en az 2-4 tane H3 alt başlık ekle!

HTML çıktı döndür:
<h2>{$h2Title}</h2>
<p>...</p>
<h3>Alt başlık 1</h3>
<p>...</p>
<h3>Alt başlık 2</h3>
<p>...</p>
<h3>Alt başlık 3</h3>
<p>...</p>";

                    $sectionResponse = $this->openaiService->ask($sectionPrompt, false, [
                        'custom_prompt' => $systemMessage,
                        'temperature' => 0.8,
                        'max_tokens' => 2000,  // 🔧 FIX: Optimized -50% for faster generation
                        'model' => 'gpt-4o',
                    ]);

                    // 🧹 Clean HTML wrapper and entity decode
                    $cleanedResponse = $this->cleanHtmlResponse(trim($sectionResponse));
                    $fullContent .= "\n\n" . $cleanedResponse;

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
[{\"question\": {\"tr\": \"Soru?\"}, \"answer\": {\"tr\": \"Cevap...\"}, \"icon\": \"fas fa-question-circle\"}]

⚠️ ÖNEMLİ: Her soru için uygun FontAwesome icon seç!
Örnekler: fas fa-question-circle, fas fa-info-circle, fas fa-lightbulb, fas fa-wrench, fas fa-shield-alt, fas fa-chart-bar, fas fa-cog, fas fa-dollar-sign, fas fa-check-circle
Her soruya farklı ve konuya uygun icon seç.";

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
{\"name\": {\"tr\": \"Başlık\"}, \"description\": {\"tr\": \"Açıklama\"}, \"steps\": [{\"name\": {\"tr\": \"Adım\"}, \"text\": {\"tr\": \"Detay\"}, \"icon\": \"fas fa-check-circle\"}]}

⚠️ ÖNEMLİ: Her adım için uygun FontAwesome icon seç!
Örnekler: fas fa-check-circle, fas fa-clipboard-check, fas fa-tools, fas fa-cogs, fas fa-search, fas fa-lightbulb, fas fa-chart-line, fas fa-shield-alt
Her adıma farklı ve konuya uygun icon seç.";

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
                    'excerpt' => $this->cleanMetaDescription(
                        $draftContext['meta_description'] ?? substr(strip_tags($fullContent), 0, 200)
                    ),
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
        $content = $decoded['content'] ?? '';

        // 🧹 Content temizliği: \n ve ```html ``` taglarını temizle
        $content = $this->cleanBlogContent($content);

        return [
            'title' => $decoded['title'] ?? 'Başlıksız Blog',
            'content' => $content,
            'excerpt' => $decoded['excerpt'] ?? substr(strip_tags($content), 0, 200),
            'faq_data' => $decoded['faq_data'] ?? null,
            'howto_data' => $decoded['howto_data'] ?? null,
        ];
    }

    /**
     * Blog content temizliği: \n, ```html ``` ve diğer sorunları düzelt
     */
    protected function cleanBlogContent(string $content): string
    {
        // 1. ```html ... ``` code block'larını kaldır (sadece içeriği bırak)
        $content = preg_replace('/```html\s*(.*?)\s*```/s', '$1', $content);
        $content = preg_replace('/```\s*(.*?)\s*```/s', '$1', $content);

        // 2. Literal \n karakterlerini gerçek newline'a çevir
        $content = str_replace('\\n', "\n", $content);

        // 3. Fazla boşlukları temizle
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        return trim($content);
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

    /**
     * Build SIMPLE prompt for blog featured image (will be enhanced by AIPromptEnhancer)
     *
     * @param string $blogTitle Blog title (subject)
     * @return string Simple subject prompt (AIPromptEnhancer will add details)
     */
    protected function buildSimplePromptForBlog(string $blogTitle): string
    {
        // 🎯 TENANT2 ÖZELİ: Yaratıcı iş hayatı sahneleri
        // Forklift/transpalet dolaylı anlatım - fabrika, lojistik, depo sahneleri
        if (tenant('id') == 2) {
            $tenantClass = $this->promptLoader->getProviderClass();
            $tenantPrompts = new $tenantClass();
            if (method_exists($tenantPrompts, 'buildImagePromptForBlog')) {
                // Tenant-specific prompt (already detailed, don't enhance further)
                return $tenantPrompts->buildImagePromptForBlog($blogTitle);
            }
        }

        // 🔄 FALLBACK: Simple generic blog prompt (AIPromptEnhancer will enrich it)
        // AIPromptEnhancer will add: camera, lens, lighting, texture, NO TEXT rules
        return "Professional blog featured image about: {$blogTitle}";
    }

    /**
     * 🧹 Clean HTML response from code block wrappers and decode HTML entities
     *
     * @param string $html Raw HTML response from AI
     * @return string Clean HTML without wrappers, with decoded entities
     */
    protected function cleanHtmlResponse(string $html): string
    {
        // Remove ```html wrapper
        $clean = preg_replace('/```html\s*(.*?)\s*```/s', '$1', $html);

        // Remove plain ``` wrapper
        $clean = preg_replace('/```\s*(.*?)\s*```/s', '$1', $clean);

        // 🔧 FIX: Remove JSON wrapper blocks (AI bazen JSON formatında yanıt veriyor)
        // Pattern 1: ```json { "content": "<h2>..." } ```
        $clean = preg_replace('/```json\s*\{.*?\}\s*```/s', '', $clean);

        // Pattern 2: json { "title": "...", "content": "...", "excerpt": "..." }
        $clean = preg_replace('/json\s*\{[^}]*"title"[^}]*"content"[^}]*"excerpt"[^}]*\}/si', '', $clean);

        // Pattern 3: json { "content": "..." } (basit format)
        $clean = preg_replace('/json\s*\{\s*"[^"]*content[^"]*"\s*:\s*"(.*?)"\s*\}/s', '$1', $clean);

        // Pattern 4: Tek satır JSON blokları (başta/sonda)
        $clean = preg_replace('/^\s*json\s*\{.*?\}\s*$/mi', '', $clean);

        // Pattern 5: JSON key-value pairs (orphan JSON fragments)
        $clean = preg_replace('/"(title|content|excerpt)"\s*:\s*"[^"]*"/i', '', $clean);

        // 🔧 FIX: Remove markdown blockquote ("> " at start of lines)
        // AI bazen markdown quote formatı kullanıyor
        $clean = preg_replace('/^>\s+/m', '', $clean);

        // 🔧 FIX: Remove HTML entities like &gt; at start of content
        // AI bazen HTML entity olarak ">" karakteri ekliyor
        $clean = preg_replace('/^&gt;\s*/m', '', $clean);

        // 🔧 FIX: Replace literal \n\n with actual newlines (AI bazen literal string olarak yazıyor)
        $clean = str_replace('\\n\\n', "\n\n", $clean);
        $clean = str_replace('\\n', "\n", $clean);

        // 🔧 FIX: Remove excessive newlines (3+ consecutive newlines → 2 newlines)
        $clean = preg_replace('/\n{3,}/', "\n\n", $clean);

        // 🔧 FIX: Remove newlines between HTML tags (clean HTML structure)
        // <h2>\n\nMetin → <h2>Metin
        $clean = preg_replace('/>(\s*\n\s*)+/', '>', $clean);
        $clean = preg_replace('/(\s*\n\s*)+</', '<', $clean);

        // 🔧 FIX: Remove excessive whitespace inside tags
        // <p>  Text  </p> → <p>Text</p>
        $clean = preg_replace('/<(p|h[1-6]|li|div)>\s+/', '<$1>', $clean);
        $clean = preg_replace('/\s+<\/(p|h[1-6]|li|div)>/', '</$1>', $clean);

        // Decode HTML entities in H2/H3 tags (İxtif > gibi karakterler için)
        $clean = preg_replace_callback('/<(h[23])[^>]*>(.*?)<\/\1>/i', function($matches) {
            $tag = $matches[1];
            $content = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return "<{$tag}>" . $content . "</{$tag}>";
        }, $clean);

        // 🔧 FIX: Decode ALL HTML entities (for complete clean HTML)
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 🔧 FIX: Remove leading ">" character if still present after all cleaning
        $clean = ltrim($clean, '> ');

        // 🔧 FIX: Wrong HTML tags (AI bazen <h> yerine <h2>/<h3> kullanmalı)
        // Örnek: <h>Başlık<p>Metin → <h3>Başlık</h3><p>Metin
        $clean = preg_replace('/<h>(.*?)<p>/i', '<h3>$1</h3><p>', $clean);
        $clean = preg_replace('/<h>(.*?)<\/h>/i', '<h3>$1</h3>', $clean);

        // 🔧 FIX: Final whitespace normalization
        $clean = preg_replace('/[ \t]+/', ' ', $clean); // Multiple spaces → single space
        $clean = preg_replace('/\n{3,}/', "\n\n", $clean); // Max 2 consecutive newlines

        return trim($clean);
    }

    /**
     * 🧹 Clean meta description from JSON wrappers and truncate to SEO length
     *
     * @param string $description Raw meta description from AI
     * @return string Clean, SEO-friendly meta description (max 155 chars)
     */
    protected function cleanMetaDescription(string $description): string
    {
        // Remove JSON code block wrapper (```json ... ```)
        $clean = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $description);

        // Remove plain code block wrapper (``` ... ```)
        $clean = preg_replace('/```\s*(.*?)\s*```/s', '$1', $clean);

        // If still JSON format, try to extract content
        if (str_starts_with(trim($clean), '{')) {
            $json = json_decode($clean, true);
            if (isset($json['content'])) {
                $clean = $json['content'];
            } elseif (isset($json['description'])) {
                $clean = $json['description'];
            } elseif (isset($json['meta_description'])) {
                $clean = $json['meta_description'];
            }
        }

        // Strip HTML tags and extra whitespace
        $clean = strip_tags(trim($clean));

        // Truncate to SEO-friendly length (155 characters max)
        return mb_substr($clean, 0, 155);
    }

    /**
     * ✅ Validate and clean outline: Remove duplicates, banned headings, and limit count
     *
     * @param array $outline Raw outline array from AI
     * @return array Cleaned outline array
     */
    protected function validateAndCleanOutline(array $outline): array
    {
        // 🚨 Yasaklı başlıklar (amateur/generic headings)
        $bannedHeadings = [
            'Giriş',
            'giriş',
            'GİRİŞ',
            'Sonuç',
            'sonuç',
            'SONUÇ',
            'Hakkında',
            'hakkında',
            'HAKKINDA',
            'Hakkımızda',
            'hakkımızda',
            'HAKKIMIZDA',
            'İletişim',
            'iletişim',
            'İLETİŞİM',
            'İletişime Geçin',
            'Introduction',
            'Conclusion',
            'About',
            'Contact',
        ];

        // 1️⃣ Trim whitespace
        $outline = array_map('trim', $outline);

        // 2️⃣ Remove banned headings
        $outline = array_filter($outline, function($heading) use ($bannedHeadings) {
            return !in_array($heading, $bannedHeadings);
        });

        // 3️⃣ Remove duplicates (case-insensitive)
        $seen = [];
        $outline = array_filter($outline, function($heading) use (&$seen) {
            $lower = mb_strtolower($heading);
            if (in_array($lower, $seen)) {
                return false; // Duplicate, remove
            }
            $seen[] = $lower;
            return true;
        });

        // 4️⃣ Limit to maximum 5 H2 headings
        $outline = array_slice($outline, 0, 5);

        // 5️⃣ Re-index array (remove gaps)
        return array_values($outline);
    }

    /**
     * 📝 Yazım Stili Seç (Random veya Sadece Profesyonel)
     *
     * Settings'ten professional_only kontrol eder:
     * - true ise → Sadece Profesyonel/Uzman arasında seçim
     * - false ise → Profesyonel/Samimi/Uzman arasında random
     *
     * @return array Writing style definition with name, description, rules, examples
     */
    protected function selectWritingStyle(): array
    {
        // Settings'ten professional_only kontrol et
        $professionalOnly = $this->getTenantSetting('blog_ai_professional_only', '0');
        $professionalOnly = ($professionalOnly === '1' || $professionalOnly === 1 || $professionalOnly === true);

        // Tüm yazım stilleri tanımları
        $allStyles = $this->getWritingStyles();

        // Professional-only modda Samimi stilini hariç tut
        if ($professionalOnly) {
            $availableStyles = ['profesyonel', 'uzman'];
            Log::info('📝 Professional-only mode: Samimi stil hariç', [
                'available_styles' => $availableStyles,
            ]);
        } else {
            $availableStyles = ['profesyonel', 'samimi', 'uzman'];
            Log::info('📝 All styles mode: Tüm stiller kullanılabilir', [
                'available_styles' => $availableStyles,
            ]);
        }

        // Random stil seç
        $selectedStyleKey = $availableStyles[array_rand($availableStyles)];

        return $allStyles[$selectedStyleKey];
    }

    /**
     * 📋 Tüm yazım stillerinin tanımları
     *
     * @return array All writing styles with their rules and examples
     */
    protected function getWritingStyles(): array
    {
        return [
            'profesyonel' => [
                'name' => 'Profesyonel',
                'description' => 'Kurumsal, resmi, teknik, bilgilendirici ton. B2B sektörler için ideal.',
                'rules' => [
                    'Kurumsal ve resmi dil kullan',
                    'Teknik terimleri açıkla',
                    'Nesnel ve bilgilendirici ol',
                    'Pasif yapılar kullanabilirsin',
                    'Ölçülü ve itibarlı bir ton kullan',
                    'Sektör standartlarına atıfta bulun',
                ],
                'examples' => [
                    '✅ "Endüstriyel ekipman seçiminde dikkate alınması gereken kriterler..."',
                    '✅ "ISO standartlarına uygun bakım prosedürleri uygulanmalıdır."',
                    '✅ "Operasyonel verimliliği artırmak için..."',
                ],
            ],
            'samimi' => [
                'name' => 'Samimi',
                'description' => 'Dostça, yakın, konuşur gibi ton. Okuyucuyla bağ kurar, B2C için uygun.',
                'rules' => [
                    'Okuyucuyla doğrudan konuş ("siz", "sizin" kullan)',
                    'Konuşur gibi doğal cümleler',
                    'Örnekler ve hikayelerle açıkla',
                    'Karmaşık terimleri günlük dille basitleştir',
                    'Samimi ama profesyonelliği koruyarak',
                    'Okuyucunun sorunlarını anladığını göster',
                ],
                'examples' => [
                    '✅ "Forklift seçerken kafanız mı karıştı? Endişelenmeyin, birlikte bakalım!"',
                    '✅ "Transpaletin bakımını kendiniz yapabilirsiniz. Hadi adım adım gösterelim."',
                    '✅ "Deponuzda yer sorunu mu yaşıyorsunuz? İşte pratik çözümler..."',
                ],
            ],
            'uzman' => [
                'name' => 'Uzman',
                'description' => 'Derinlemesine teknik, akademik ton. Sektör uzmanları için detaylı analiz.',
                'rules' => [
                    'İleri düzey teknik detaylara gir',
                    'Spesifikasyonları ve standartları belirt',
                    'Karşılaştırmalı analizler yap',
                    'Endüstri trendlerini ve inovasyonları ele al',
                    'Veri ve istatistiklerle destekle',
                    'Uzman jargonu kullanabilirsin (ama açıkla)',
                ],
                'examples' => [
                    '✅ "AC motor tork karakteristikleri, yük profillerine göre optimize edilmelidir."',
                    '✅ "Hidrolik sistem basınç dengesi 150-200 bar arasında kalibre edilir."',
                    '✅ "EN 15000 standardına göre yük merkezi hesaplamaları kritik öneme sahiptir."',
                ],
            ],
        ];
    }

    /**
     * Tenant setting değerini çek (CategoryBasedDraftGenerator ile aynı mantık)
     *
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed Setting value
     */
    protected function getTenantSetting(string $key, $default = null)
    {
        try {
            // Central DB'den Setting'i bul
            $setting = \Modules\SettingManagement\App\Models\Setting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            // Tenant DB'den value'yu çek
            if (tenant()) {
                $settingValue = \Modules\SettingManagement\App\Models\SettingValue::on('tenant')
                    ->where('setting_id', $setting->id)
                    ->first();

                if ($settingValue && $settingValue->value !== null) {
                    return $settingValue->value;
                }
            }

            // Default value
            return $setting->default_value ?? $default;

        } catch (\Exception $e) {
            Log::warning('⚠️ Failed to get tenant setting', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }
}
