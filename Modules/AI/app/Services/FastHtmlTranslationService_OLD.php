<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Services\UniversalTranslationService;
use Modules\AI\app\Services\AIService;
use Modules\AI\App\Services\ConversationTracker;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Services\EnhancedJavaScriptProtector;

class FastHtmlTranslationService
{
    private $aiService;
    private $creditService;
    private $universalTranslationService;

    public function __construct(
        AIService $aiService, 
        AICreditService $creditService,
        UniversalTranslationService $universalTranslationService
    ) {
        $this->aiService = $aiService;
        $this->creditService = $creditService;
        $this->universalTranslationService = $universalTranslationService;
    }

    /**
     * 🚀 SÜPER HIZLI HTML ÇEVİRİ SİSTEMİ
     * HTML'den text'leri çıkarır, toplu çevirir, geri yerleştirir
     */
    public function translateHtmlContentFast(string $html, string $fromLang, string $toLang, string $context): string
    {
        // Log::info('🚀 SÜPER HIZLI HTML çeviri başlıyor', [
        //     'html_length' => strlen($html),
        //     'from_lang' => $fromLang,
        //     'to_lang' => $toLang
        // ]);

        try {
            // 🚨 ULTRA-ENHANCED JAVASCRIPT PROTECTION - Using dedicated protector service
            $jsProtector = new EnhancedJavaScriptProtector();
            $htmlWithPlaceholders = $jsProtector->protectJavaScript($html);
            
            Log::info('🛡️ Ultra-enhanced JavaScript protection applied via EnhancedJavaScriptProtector', [
                'original_length' => strlen($html),
                'protected_length' => strlen($htmlWithPlaceholders),
                'phase_1_attributes' => 'Protected',
                'phase_2_expressions' => 'Protected'
            ]);
            
            // 1. HTML'den sadece text'leri çıkar (regex ile) - 🚨 ARABIC HTML BUG FIX
            $textMatches = [];
            // OLD BUGGY PATTERN: '/>([\s\S]*?)</' - Bu tüm template structure'ı bozuyordu!
            // Bu pattern {{TEXT_PLACEHOLDER}} gibi template marker'ları da alıyordu
            // NEW SAFE PATTERN: Sadece tag'lar arası gerçek text'leri al, HTML yapısını koru
            // 🚨 ULTRA-CRITICAL JAVASCRIPT & TEMPLATE PROTECTION
            // Exclude: curly braces {}, parentheses (), quotes "", apostrophes '', equals =
            // This protects JavaScript, template markers, and inline code
            $pattern = '/>([^<{}"\'()=]+)</';
            // Use htmlWithPlaceholders to avoid extracting JavaScript code
            preg_match_all($pattern, $htmlWithPlaceholders, $textMatches, PREG_OFFSET_CAPTURE);
            
            $textsToTranslate = [];
            $placeholders = [];
            $counter = 0;
            
            foreach ($textMatches[1] as $match) {
                $text = trim($match[0]);
                
                // 🚨 GELİŞMİŞ FİLTRELEME SİSTEMİ - SmartHtmlTranslationService ile uyumlu
                if (!$this->isTranslatableText($text)) {
                    continue;
                }
                
                $placeholder = "|||TRANSLATE_{$counter}|||";
                $textsToTranslate[] = $text;
                $placeholders[] = $placeholder;
                $counter++;
            }
            
            if (empty($textsToTranslate)) {
                Log::info('📝 Çevrilecek text bulunamadı');
                
                // 🚨 CRITICAL: Restore JavaScript even if no text to translate
                $restoredHtml = $jsProtector->restoreJavaScript($htmlWithPlaceholders);
                
                Log::info('✅ JavaScript koruması geri yüklendi (çevirilecek text yok)', [
                    'restored_length' => strlen($restoredHtml)
                ]);
                
                return $restoredHtml;
            }
            
            // Log::info('📝 Text extraction tamamlandı', [
            //     'texts_found' => count($textsToTranslate),
            //     'sample_texts' => array_slice($textsToTranslate, 0, 3)
            // ]);
            
            // 2. 🚀 ULTRA-SECURE UUID SEPARATOR SYSTEM - AI-PROOF SEPARATION
            // Generate unique UUID for this translation session to prevent AI translation
            $uniqueId = 'UUID_' . str_replace('-', '_', Str::uuid()->toString()) . '_SEPARATOR';
            $combinedText = implode("\n{$uniqueId}\n", $textsToTranslate);
            
            // Geliştirilmiş dil tanıma ve çeviri prompt sistemi
            $sourceLanguageName = $this->getLanguageNativeName($fromLang);
            $targetLanguageName = $this->getLanguageNativeName($toLang);
            
            $bulkContext = "You are a PROFESSIONAL MULTILINGUAL TRANSLATOR with expertise in {$targetLanguageName}.

🎯 CRITICAL MISSION: Translate from {$sourceLanguageName} to {$targetLanguageName}

⚠️ ZERO TOLERANCE RULES:
- SOURCE: {$fromLang} ({$sourceLanguageName})
- TARGET: {$toLang} ({$targetLanguageName})
- OUTPUT LANGUAGE: {$targetLanguageName} ONLY
- FORBIDDEN: " . $this->getForbiddenLanguages($toLang, $targetLanguageName) . "
- PENALTY: If you output forbidden languages instead of {$targetLanguageName}, you FAIL

✅ REQUIRED OUTPUT: Pure {$targetLanguageName} ({$toLang}) only

📋 TRANSLATION RULES:
1. Each text segment is separated by unique UUID markers
2. Translate EVERY segment to {$targetLanguageName}
3. Keep exact same number of segments
4. Use professional business tone in {$targetLanguageName}
5. NO English unless target language IS English
6. NO fallback to common languages

🚨 ULTRA-CRITICAL UUID SEPARATOR RULE - MANDATORY COMPLIANCE:
- NEVER TRANSLATE the UUID separator: '{$uniqueId}'
- This is a TECHNICAL SYSTEM MARKER, NOT TRANSLATABLE CONTENT
- Keep '{$uniqueId}' EXACTLY as written in your response
- Only translate the TEXT SEGMENTS between these markers
- CRITICAL: Output format must be: text1\n{$uniqueId}\ntext2\n{$uniqueId}\ntext3
- VIOLATION = SYSTEM FAILURE: If you change the UUID separator, the system breaks

🎯 TARGET LANGUAGE FOCUS:
- You MUST write in: {$targetLanguageName}
- Language code: {$toLang}
- Write naturally in {$targetLanguageName}
- Use {$targetLanguageName} grammar and structure
- Think in {$targetLanguageName}, not English

VERIFICATION: Before responding, confirm your output is 100% {$targetLanguageName}.

Content to translate:";

            $translatedCombined = $this->callDirectAIProvider(
                $combinedText,
                $fromLang,
                $toLang,
                $bulkContext
            );
            
            // 3. 🚀 ULTRA-SECURE UUID SEPARATOR PARSING - BULLETPROOF SYSTEM
            Log::info('🔍 UUID Separator Parsing Debug', [
                'translated_combined_length' => strlen($translatedCombined),
                'translated_preview' => substr($translatedCombined, 0, 500),
                'uuid_separator' => $uniqueId,
                'uuid_separator_count' => substr_count($translatedCombined, $uniqueId),
                'expected_texts' => count($textsToTranslate)
            ]);
            
            $translatedTexts = [];
            
            // 🚨 UUID SEPARATOR PARSING - Primary method only
            if (strpos($translatedCombined, $uniqueId) !== false) {
                // Split by UUID separator
                $translatedTexts = explode("\n{$uniqueId}\n", $translatedCombined);
                $translatedTexts = array_map('trim', $translatedTexts);
                Log::info('✅ UUID Separator parsing successful', [
                    'parsed_count' => count($translatedTexts),
                    'uuid_matches' => substr_count($translatedCombined, $uniqueId)
                ]);
            }
            // Fallback: try without newlines (AI might strip them)
            elseif (strpos($translatedCombined, $uniqueId) !== false) {
                $translatedTexts = explode($uniqueId, $translatedCombined);
                $translatedTexts = array_map('trim', $translatedTexts);
                Log::info('✅ UUID Separator parsing successful (without newlines)');
            }
            // Emergency fallback: treat as single text
            else {
                $translatedTexts = [$translatedCombined];
                Log::error('🚨 CRITICAL: UUID Separator completely missing from AI response!', [
                    'uuid_separator' => $uniqueId,
                    'response_preview' => substr($translatedCombined, 0, 500)
                ]);
            }
            
            // Log::info('🔍 DEBUG: Parsing sonucu', [
            //     'parsed_count' => count($translatedTexts),
            //     'expected_count' => count($textsToTranslate),
            //     'parsed_texts' => array_slice($translatedTexts, 0, 3)
            // ]);
            
            // Eğer ayrılan text sayısı uymuyor ise fallback
            if (count($translatedTexts) !== count($textsToTranslate)) {
                Log::warning('⚠️ Çevrilen text sayısı uyumsuz, fallback yapılıyor', [
                    'expected' => count($textsToTranslate),
                    'received' => count($translatedTexts),
                    'raw_response' => $translatedCombined
                ]);
                
                // Fallback: Her text'i ayrı ayrı çevir (eski sistem)
                return $this->fallbackToSlowTranslation($html, $fromLang, $toLang, $context);
            }
            
            // 4. HTML'de text'leri çevrilenleriyle değiştir
            // Start with the HTML that has placeholders
            $translatedHtml = $htmlWithPlaceholders;
            
            foreach ($textsToTranslate as $index => $originalText) {
                $translatedText = trim($translatedTexts[$index] ?? $originalText);
                
                // GÜÇLENDIRILMIŞ REPLACEMENT - Whitespace tolerance ile
                $originalTextEscaped = preg_quote($originalText, '/');
                $pattern = '/>(\s*)' . $originalTextEscaped . '(\s*)</';
                $replacement = '>$1' . $translatedText . '$2<';
                
                $translatedHtml = preg_replace($pattern, $replacement, $translatedHtml);
                
                // Log::info('🔄 Text replacement', [
                //     'original' => substr($originalText, 0, 50),
                //     'translated' => substr($translatedText, 0, 50),
                //     'pattern_matched' => preg_match($pattern, $translatedHtml) > 0
                // ]);
            }
            
            // 🚨 CRITICAL: Restore ALL protected JavaScript via EnhancedJavaScriptProtector
            $translatedHtml = $jsProtector->restoreJavaScript($translatedHtml);
            
            Log::info('✅ SÜPER HIZLI HTML çeviri tamamlandı - EnhancedJavaScriptProtector ile', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'texts_translated' => count($translatedTexts),
                'performance' => 'BULK_TRANSLATION_WITH_ENHANCED_JS_PROTECTION',
                'javascript_protection' => '2-PHASE_ENHANCED_PROTECTION_SUCCESS'
            ]);
            
            return $translatedHtml;
            
        } catch (\Exception $e) {
            Log::error('❌ SÜPER HIZLI HTML çeviri hatası', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: Eski sistem kullan
            return $this->fallbackToSlowTranslation($html, $fromLang, $toLang, $context);
        }
    }
    
    /**
     * Dil kodunu native ismine çevir
     */
    private function getLanguageNativeName(string $langCode): string
    {
        // TenantLanguageProvider'dan dil ismini al
        try {
            $languageService = app(\App\Services\TenantLanguageProvider::class);
            $languageName = $languageService::getLanguageName($langCode);
            
            // Eğer aynı ise, bilinen dil isimleri kullan
            if ($languageName === $langCode) {
                return $this->getFallbackLanguageName($langCode);
            }
            
            return $languageName;
        } catch (\Exception $e) {
            return $this->getFallbackLanguageName($langCode);
        }
    }
    
    /**
     * Hedef dile göre yasaklı dilleri belirle
     */
    private function getForbiddenLanguages(string $targetLang, string $targetLanguageName): string
    {
        $commonLanguages = ['en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch', 'bg' => 'Български', 'tr' => 'Türkçe'];
        
        // Hedef dil hariç diğer yaygın dilleri yasakla
        $forbidden = collect($commonLanguages)
            ->reject(fn($name, $code) => $code === $targetLang)
            ->values()
            ->join(', ');
            
        return $forbidden ?: "any other language except {$targetLanguageName}";
    }

    /**
     * Fallback dil isimleri
     */
    private function getFallbackLanguageName(string $langCode): string
    {
        $languageMap = [
            'tr' => 'Türkçe (Turkish)',
            'en' => 'English', 
            'ar' => 'العربية (Arabic)',
            'de' => 'Deutsch (German)',
            'fr' => 'Français (French)',
            'es' => 'Español (Spanish)',
            'it' => 'Italiano (Italian)',
            'pt' => 'Português (Portuguese)',
            'ru' => 'Русский (Russian)',
            'zh' => '中文 (Chinese)',
            'ja' => '日本語 (Japanese)',
            'ko' => '한국어 (Korean)',
            'hi' => 'हिन्दी (Hindi)',
            'el' => 'Ελληνικά (Greek)',
            'he' => 'עברית (Hebrew)',
            'fa' => 'فارسی (Persian)',
            'nl' => 'Nederlands (Dutch)',
            'sv' => 'Svenska (Swedish)',
            'da' => 'Dansk (Danish)',
            'no' => 'Norsk (Norwegian)',
            'fi' => 'Suomi (Finnish)',
            'pl' => 'Polski (Polish)',
            'cs' => 'Čeština (Czech)',
            'sk' => 'Slovenčina (Slovak)',
            'hu' => 'Magyar (Hungarian)',
            'ro' => 'Română (Romanian)',
            'bg' => 'Български (Bulgarian)',
            'hr' => 'Hrvatski (Croatian)',
            'sr' => 'Српски (Serbian)',
            'sl' => 'Slovenščina (Slovenian)',
            'uk' => 'Українська (Ukrainian)',
            'et' => 'Eesti (Estonian)',
            'lv' => 'Latviešu (Latvian)',
            'lt' => 'Lietuvių (Lithuanian)',
        ];
        
        return $languageMap[$langCode] ?? strtoupper($langCode) . ' Language';
    }

    /**
     * Direkt AI provider çağrısı (infinite loop önleme için)
     */
    private function callDirectAIProvider(string $text, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🚀 Direkt AI provider çağrısı', [
            'text_length' => strlen($text),
            'from_lang' => $fromLang,
            'to_lang' => $toLang
        ]);
        
        // AIProviderManager'ı kullan
        try {
            $providerManager = app(\Modules\AI\App\Services\AIProviderManager::class);
            $activeProviders = $providerManager->getActiveProviders();
            $activeProvider = $activeProviders->first();
            
            if (!$activeProvider) {
                throw new \Exception('Aktif AI provider bulunamadı');
            }
            
            // Provider'a göre direkt çağrı - Fallback: Her durumda AIService kullan
            Log::info('🔄 AIService fallback kullanılıyor', [
                'provider' => $activeProvider->name,
                'reason' => 'Unified translation method'
            ]);
            
            // 🚨 INFINITE LOOP PREVENTION - Direct AI provider call bypass
            // DON'T use $this->aiService->translateText() because it calls translateHtmlContentFast again!
            
            // Get provider with failover capability 
            $providerManager = app(\Modules\AI\App\Services\AIProviderManager::class);
            
            // Try providers in order with failover
            $translatedText = $this->tryProvidersWithFailover($providerManager, $context . "\n\n" . $text);
            
            return $translatedText;
            
        } catch (\Exception $e) {
            Log::error('❌ Direkt provider çağrısı hatası', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Fallback: Yavaş ama güvenli çeviri
     */
    private function fallbackToSlowTranslation(string $html, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🐌 Fallback: HTML çeviri geri dönüş sistemi');
        
        // 🚨 INFINITE LOOP PREVENTION - Return original if translation fails
        // Instead of calling aiService->translateText again, just return the original
        Log::warning('⚠️ HTML çeviri başarısız - orijinal içerik döndürülüyor');
        return $html;
    }

    /**
     * 🚀 UNIVERSAL Entity çeviri işlemi - Queue Job için
     * 💰 PER-LANGUAGE CREDIT SYSTEM: Her dil için ayrı kredi düşümü
     * 🌍 DYNAMIC MODULE SUPPORT: Tüm modüller otomatik desteklenir
     */
    public function translateEntity(string $entityType, int $entityId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            Log::info("🌍 UNIVERSAL FastHtml Entity çevirisi başlatıldı", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // 💰 Pre-translation credit deduction - Per language basis
            try {
                $tenantId = tenancy()->tenant?->id ?? 1;
                $tenant = \App\Models\Tenant::find($tenantId);
                
                if ($tenant) {
                    // Per-language credit cost (1 credit per language translation)
                    $perLanguageCost = 1.0;
                    
                    ai_use_credits($perLanguageCost, $tenant->id, [
                        'usage_type' => 'translation',
                        'description' => "Universal AI Translation: {$entityType} #{$entityId} ({$sourceLanguage} → {$targetLanguage})",
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'source_language' => $sourceLanguage,
                        'target_language' => $targetLanguage,
                        'provider_name' => 'universal_translation_service'
                    ]);
                    
                    Log::info('💰 KREDİ DÜŞÜRÜLDİ: UNIVERSAL 1 DİL = 1 KREDİ', [
                        'tenant_id' => $tenant->id,
                        'credits_used' => $perLanguageCost,
                        'credit_rule' => 'UNIVERSAL 1 DİL = 1 KREDİ',
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'language_pair' => "{$sourceLanguage} → {$targetLanguage}",
                        'remaining_credits' => $tenant->fresh()->ai_credits_balance ?? 'unknown'
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Per-language credit deduction failed', [
                    'error' => $e->getMessage(),
                    'entity_type' => $entityType,
                    'entity_id' => $entityId
                ]);
                // Continue with translation even if credit deduction fails
            }

            // 🚀 UNIVERSAL TRANSLATION: Dinamik modül desteği
            $result = $this->universalTranslationService->translateEntity(
                $entityType, 
                $entityId, 
                $sourceLanguage, 
                $targetLanguage
            );

            if ($result['success']) {
                Log::info('✅ UNIVERSAL çeviri başarılı', [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'translated_fields' => array_keys($result['data'])
                ]);

                return [
                    'success' => true,
                    'translated_data' => $result['data'],
                    'target_language' => $targetLanguage
                ];
            } else {
                throw new \Exception($result['error']);
            }

        } catch (\Exception $e) {
            Log::error("❌ UNIVERSAL FastHtmlTranslationService hatası", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @deprecated Artık UniversalTranslationService kullanın
     * Page çeviri işlemi - DEPRECATED
     */
    protected function translatePage(int $pageId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            // Page'i bul ve SEO setting'i yükle
            $page = Page::with('seoSetting')->find($pageId);
            if (!$page) {
                throw new \Exception("Page bulunamadı: {$pageId}");
            }

            Log::info("📄 Page çevirisi başlatıldı", [
                'page_id' => $pageId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // Kaynak dil verilerini al
            $sourceData = [
                'title' => $page->getTranslated('title', $sourceLanguage),
                'body' => $page->getTranslated('body', $sourceLanguage),
                'slug' => $page->getTranslated('slug', $sourceLanguage)
            ];

            // SEO verilerini al (eğer mevcut ise)
            $seoSetting = $page->seoSetting;
            $seoSourceData = [];
            if ($seoSetting) {
                $seoSourceData = [
                    'seo_title' => $seoSetting->getTranslated('titles', $sourceLanguage),
                    'seo_description' => $seoSetting->getTranslated('descriptions', $sourceLanguage),
                    'seo_keywords' => $seoSetting->getTranslated('keywords', $sourceLanguage),
                ];
            }

            // Kaynak verilerini kontrol et
            if (empty($sourceData['title']) && empty($sourceData['body'])) {
                throw new \Exception("Kaynak dil ({$sourceLanguage}) verileri bulunamadı");
            }

            $translatedData = [];
            $translatedSeoData = [];

            // Title çevir (INFINITE LOOP PREVENTION - Direct provider call)
            if (!empty($sourceData['title'])) {
                $titleContext = "Sen profesyonel bir çevirmensin. Aşağıdaki sayfa başlığını {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. Profesyonel iş dili kullan
3. Hedef dilde doğal ifade et
4. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: Website page title";

                $translatedTitle = $this->callDirectAIProvider(
                    $sourceData['title'],
                    $sourceLanguage,
                    $targetLanguage,
                    $titleContext
                );
                $translatedData['title'] = $translatedTitle;
                Log::info("✅ Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Body çevir (HTML korunarak)
            if (!empty($sourceData['body'])) {
                $optimizedContext = "Sen profesyonel bir çevirmensin. Aşağıdaki HTML içeriği {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. HTML etiketlerini aynen koru, sadece metin içeriğini çevir
3. Profesyonel iş dili kullan
4. Hedef dilde doğal ifade et
5. Başlıklar, paragraflar, listeler dahil TÜM metinleri çevir
6. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: Website page content";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext
                );
                $translatedData['body'] = $translatedBody;
                Log::info("✅ Body çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Slug oluştur (title'dan)
            if (!empty($translatedData['title'])) {
                $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                    Page::class,
                    $translatedData['title'],
                    $targetLanguage,
                    'slug',
                    'page_id',
                    $pageId
                );
                Log::info("✅ Slug oluşturuldu: {$translatedData['slug']}");
            }

            // SEO Title çevir (eğer mevcut ise) - INFINITE LOOP PREVENTION
            if (!empty($seoSourceData['seo_title'])) {
                $seoTitleContext = "Sen profesyonel bir çevirmensin. Aşağıdaki SEO başlığını {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. SEO optimizasyonu için kısa ve etkili ol (maksimum 60 karakter)
3. Profesyonel iş dili kullan
4. Hedef dilde doğal ifade et
5. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: SEO page title";

                $translatedSeoTitle = $this->callDirectAIProvider(
                    $seoSourceData['seo_title'],
                    $sourceLanguage,
                    $targetLanguage,
                    $seoTitleContext
                );
                $translatedSeoData['seo_title'] = $translatedSeoTitle;
                Log::info("✅ SEO Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Description çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_description'])) {
                $translatedSeoDescription = $this->aiService->translateText(
                    $seoSourceData['seo_description'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'seo_description', 'max_length' => 160]
                );
                $translatedSeoData['seo_description'] = $translatedSeoDescription;
                Log::info("✅ SEO Description çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Keywords çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_keywords'])) {
                $keywordsContext = "Translate these SEO keywords to {$targetLanguage}. Keep them relevant and concise. Separate with commas.";
                $translatedSeoKeywords = $this->aiService->translateText(
                    $seoSourceData['seo_keywords'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => $keywordsContext, 'max_length' => 255]
                );
                $translatedSeoData['seo_keywords'] = $translatedSeoKeywords;
                Log::info("✅ SEO Keywords çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Çevrilmiş verileri kaydet
            if (!empty($translatedData)) {
                // Title
                if (isset($translatedData['title'])) {
                    $titles = $page->title ?? [];
                    $titles[$targetLanguage] = $translatedData['title'];
                    $page->title = $titles;
                }

                // Body
                if (isset($translatedData['body'])) {
                    $bodies = $page->body ?? [];
                    $bodies[$targetLanguage] = $translatedData['body'];
                    $page->body = $bodies;
                }

                // Slug
                if (isset($translatedData['slug'])) {
                    $slugs = $page->slug ?? [];
                    $slugs[$targetLanguage] = $translatedData['slug'];
                    $page->slug = $slugs;
                }

                // Kaydet
                $page->save();
            }

            // SEO çevrilmiş verilerini kaydet (eğer mevcut ise)
            if (!empty($translatedSeoData) && $seoSetting) {
                // SEO Title
                if (isset($translatedSeoData['seo_title'])) {
                    $seoTitles = $seoSetting->titles ?? [];
                    $seoTitles[$targetLanguage] = $translatedSeoData['seo_title'];
                    $seoSetting->titles = $seoTitles;
                }

                // SEO Description
                if (isset($translatedSeoData['seo_description'])) {
                    $seoDescriptions = $seoSetting->descriptions ?? [];
                    $seoDescriptions[$targetLanguage] = $translatedSeoData['seo_description'];
                    $seoSetting->descriptions = $seoDescriptions;
                }

                // SEO Keywords
                if (isset($translatedSeoData['seo_keywords'])) {
                    $seoKeywords = $seoSetting->keywords ?? [];
                    $seoKeywords[$targetLanguage] = $translatedSeoData['seo_keywords'];
                    $seoSetting->keywords = $seoKeywords;
                }

                // SEO Kaydet
                $seoSetting->save();

                Log::info("✅ SEO çevirisi kaydedildi", [
                    'page_id' => $pageId,
                    'target_language' => $targetLanguage,
                    'translated_seo_fields' => array_keys($translatedSeoData)
                ]);
            }

            // Sonuç hazırla
            $allTranslatedData = $translatedData;
            if (!empty($translatedSeoData)) {
                $allTranslatedData = array_merge($allTranslatedData, $translatedSeoData);
            }

            if (!empty($allTranslatedData)) {
                Log::info("✅ Page ve SEO çevirisi tamamlandı", [
                    'page_id' => $pageId,
                    'target_language' => $targetLanguage,
                    'translated_fields' => array_keys($allTranslatedData)
                ]);

                return [
                    'success' => true,
                    'translated_data' => $allTranslatedData,
                    'target_language' => $targetLanguage
                ];
            }

            throw new \Exception("Çevrilecek veri bulunamadı");

        } catch (\Exception $e) {
            Log::error("❌ Page çeviri hatası", [
                'page_id' => $pageId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Portfolio çeviri işlemi
     */
    protected function translatePortfolio(int $portfolioId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            // Portfolio'yu bul ve SEO setting'i yükle
            $portfolio = Portfolio::with('seoSetting')->find($portfolioId);
            if (!$portfolio) {
                throw new \Exception("Portfolio bulunamadı: {$portfolioId}");
            }

            Log::info("🎨 Portfolio çevirisi başlatıldı", [
                'portfolio_id' => $portfolioId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // Kaynak dil verilerini al
            $sourceData = [
                'title' => $portfolio->getTranslated('title', $sourceLanguage),
                'body' => $portfolio->getTranslated('body', $sourceLanguage),
                'slug' => $portfolio->getTranslated('slug', $sourceLanguage)
            ];

            // SEO verilerini al (eğer mevcut ise)
            $seoSetting = $portfolio->seoSetting;
            $seoSourceData = [];
            if ($seoSetting) {
                $seoSourceData = [
                    'seo_title' => $seoSetting->getTranslated('titles', $sourceLanguage),
                    'seo_description' => $seoSetting->getTranslated('descriptions', $sourceLanguage),
                    'seo_keywords' => $seoSetting->getTranslated('keywords', $sourceLanguage),
                ];
            }

            // Kaynak verilerini kontrol et
            if (empty($sourceData['title']) && empty($sourceData['body'])) {
                throw new \Exception("Kaynak dil ({$sourceLanguage}) verileri bulunamadı");
            }

            $translatedData = [];
            $translatedSeoData = [];

            // Title çevir
            if (!empty($sourceData['title'])) {
                $translatedTitle = $this->aiService->translateText(
                    $sourceData['title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_title', 'max_length' => 255]
                );
                $translatedData['title'] = $translatedTitle;
                Log::info("✅ Portfolio Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Body çevir (HTML korunarak)
            if (!empty($sourceData['body'])) {
                $optimizedContext = "Sen profesyonel bir çevirmensin. Aşağıdaki HTML içeriği {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. HTML etiketlerini aynen koru, sadece metin içeriğini çevir
3. Profesyonel iş dili kullan
4. Hedef dilde doğal ifade et
5. Başlıklar, paragraflar, listeler dahil TÜM metinleri çevir
6. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: Portfolio project description";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext
                );
                $translatedData['body'] = $translatedBody;
                Log::info("✅ Portfolio Body çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Slug oluştur (title'dan)
            if (!empty($translatedData['title'])) {
                $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                    Portfolio::class,
                    $translatedData['title'],
                    $targetLanguage,
                    'slug',
                    'portfolio_id',
                    $portfolioId
                );
                Log::info("✅ Portfolio Slug oluşturuldu: {$translatedData['slug']}");
            }

            // SEO Title çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_title'])) {
                $translatedSeoTitle = $this->aiService->translateText(
                    $seoSourceData['seo_title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_seo_title', 'max_length' => 60]
                );
                $translatedSeoData['seo_title'] = $translatedSeoTitle;
                Log::info("✅ Portfolio SEO Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Description çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_description'])) {
                $translatedSeoDescription = $this->aiService->translateText(
                    $seoSourceData['seo_description'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_seo_description', 'max_length' => 160]
                );
                $translatedSeoData['seo_description'] = $translatedSeoDescription;
                Log::info("✅ Portfolio SEO Description çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Keywords çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_keywords'])) {
                $keywordsContext = "Translate these portfolio SEO keywords to {$targetLanguage}. Keep them relevant and concise. Separate with commas.";
                $translatedSeoKeywords = $this->aiService->translateText(
                    $seoSourceData['seo_keywords'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => $keywordsContext, 'max_length' => 255]
                );
                $translatedSeoData['seo_keywords'] = $translatedSeoKeywords;
                Log::info("✅ Portfolio SEO Keywords çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Çevrilmiş verileri kaydet
            if (!empty($translatedData)) {
                // Title
                if (isset($translatedData['title'])) {
                    $titles = $portfolio->title ?? [];
                    $titles[$targetLanguage] = $translatedData['title'];
                    $portfolio->title = $titles;
                }

                // Body
                if (isset($translatedData['body'])) {
                    $bodies = $portfolio->body ?? [];
                    $bodies[$targetLanguage] = $translatedData['body'];
                    $portfolio->body = $bodies;
                }

                // Slug
                if (isset($translatedData['slug'])) {
                    $slugs = $portfolio->slug ?? [];
                    $slugs[$targetLanguage] = $translatedData['slug'];
                    $portfolio->slug = $slugs;
                }

                // Kaydet
                $portfolio->save();
            }

            // SEO çevrilmiş verilerini kaydet (eğer mevcut ise)
            if (!empty($translatedSeoData) && $seoSetting) {
                // SEO Title
                if (isset($translatedSeoData['seo_title'])) {
                    $seoTitles = $seoSetting->titles ?? [];
                    $seoTitles[$targetLanguage] = $translatedSeoData['seo_title'];
                    $seoSetting->titles = $seoTitles;
                }

                // SEO Description
                if (isset($translatedSeoData['seo_description'])) {
                    $seoDescriptions = $seoSetting->descriptions ?? [];
                    $seoDescriptions[$targetLanguage] = $translatedSeoData['seo_description'];
                    $seoSetting->descriptions = $seoDescriptions;
                }

                // SEO Keywords
                if (isset($translatedSeoData['seo_keywords'])) {
                    $seoKeywords = $seoSetting->keywords ?? [];
                    $seoKeywords[$targetLanguage] = $translatedSeoData['seo_keywords'];
                    $seoSetting->keywords = $seoKeywords;
                }

                // SEO Kaydet
                $seoSetting->save();

                Log::info("✅ Portfolio SEO çevirisi kaydedildi", [
                    'portfolio_id' => $portfolioId,
                    'target_language' => $targetLanguage,
                    'translated_seo_fields' => array_keys($translatedSeoData)
                ]);
            }

            // Sonuç hazırla
            $allTranslatedData = $translatedData;
            if (!empty($translatedSeoData)) {
                $allTranslatedData = array_merge($allTranslatedData, $translatedSeoData);
            }

            if (!empty($allTranslatedData)) {
                Log::info("✅ Portfolio ve SEO çevirisi tamamlandı", [
                    'portfolio_id' => $portfolioId,
                    'target_language' => $targetLanguage,
                    'translated_fields' => array_keys($allTranslatedData)
                ]);

                return [
                    'success' => true,
                    'translated_data' => $allTranslatedData,
                    'target_language' => $targetLanguage
                ];
            }

            throw new \Exception("Çevrilecek veri bulunamadı");

        } catch (\Exception $e) {
            Log::error("❌ Portfolio çeviri hatası", [
                'portfolio_id' => $portfolioId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Portfolio Category çeviri işlemi
     */
    protected function translatePortfolioCategory(int $categoryId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            // Portfolio Category'yi bul ve SEO setting'i yükle
            $category = PortfolioCategory::with('seoSetting')->find($categoryId);
            if (!$category) {
                throw new \Exception("Portfolio Category bulunamadı: {$categoryId}");
            }

            Log::info("📂 Portfolio Category çevirisi başlatıldı", [
                'category_id' => $categoryId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // Kaynak dil verilerini al
            $sourceData = [
                'title' => $category->getTranslated('title', $sourceLanguage),
                'body' => $category->getTranslated('body', $sourceLanguage),
                'slug' => $category->getTranslated('slug', $sourceLanguage)
            ];

            // SEO verilerini al (eğer mevcut ise)
            $seoSetting = $category->seoSetting;
            $seoSourceData = [];
            if ($seoSetting) {
                $seoSourceData = [
                    'seo_title' => $seoSetting->getTranslated('titles', $sourceLanguage),
                    'seo_description' => $seoSetting->getTranslated('descriptions', $sourceLanguage),
                    'seo_keywords' => $seoSetting->getTranslated('keywords', $sourceLanguage),
                ];
            }

            // Kaynak verilerini kontrol et
            if (empty($sourceData['title']) && empty($sourceData['body'])) {
                throw new \Exception("Kaynak dil ({$sourceLanguage}) verileri bulunamadı");
            }

            $translatedData = [];
            $translatedSeoData = [];

            // Title çevir
            if (!empty($sourceData['title'])) {
                $translatedTitle = $this->aiService->translateText(
                    $sourceData['title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_category_title', 'max_length' => 255]
                );
                $translatedData['title'] = $translatedTitle;
                Log::info("✅ Portfolio Category Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Body çevir (HTML korunarak)
            if (!empty($sourceData['body'])) {
                $optimizedContext = "Sen profesyonel bir çevirmensin. Aşağıdaki HTML içeriği {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. HTML etiketlerini aynen koru, sadece metin içeriğini çevir
3. Profesyonel iş dili kullan
4. Hedef dilde doğal ifade et
5. Başlıklar, paragraflar, listeler dahil TÜM metinleri çevir
6. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: Portfolio category description";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext
                );
                $translatedData['body'] = $translatedBody;
                Log::info("✅ Portfolio Category Body çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Slug oluştur (title'dan)
            if (!empty($translatedData['title'])) {
                $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                    PortfolioCategory::class,
                    $translatedData['title'],
                    $targetLanguage,
                    'slug',
                    'portfolio_category_id',
                    $categoryId
                );
                Log::info("✅ Portfolio Category Slug oluşturuldu: {$translatedData['slug']}");
            }

            // SEO Title çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_title'])) {
                $translatedSeoTitle = $this->aiService->translateText(
                    $seoSourceData['seo_title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_category_seo_title', 'max_length' => 60]
                );
                $translatedSeoData['seo_title'] = $translatedSeoTitle;
                Log::info("✅ Portfolio Category SEO Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Description çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_description'])) {
                $translatedSeoDescription = $this->aiService->translateText(
                    $seoSourceData['seo_description'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'portfolio_category_seo_description', 'max_length' => 160]
                );
                $translatedSeoData['seo_description'] = $translatedSeoDescription;
                Log::info("✅ Portfolio Category SEO Description çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Keywords çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_keywords'])) {
                $keywordsContext = "Translate these portfolio category SEO keywords to {$targetLanguage}. Keep them relevant and concise. Separate with commas.";
                $translatedSeoKeywords = $this->aiService->translateText(
                    $seoSourceData['seo_keywords'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => $keywordsContext, 'max_length' => 255]
                );
                $translatedSeoData['seo_keywords'] = $translatedSeoKeywords;
                Log::info("✅ Portfolio Category SEO Keywords çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Çevrilmiş verileri kaydet
            if (!empty($translatedData)) {
                // Title
                if (isset($translatedData['title'])) {
                    $titles = $category->title ?? [];
                    $titles[$targetLanguage] = $translatedData['title'];
                    $category->title = $titles;
                }

                // Body
                if (isset($translatedData['body'])) {
                    $bodies = $category->body ?? [];
                    $bodies[$targetLanguage] = $translatedData['body'];
                    $category->body = $bodies;
                }

                // Slug
                if (isset($translatedData['slug'])) {
                    $slugs = $category->slug ?? [];
                    $slugs[$targetLanguage] = $translatedData['slug'];
                    $category->slug = $slugs;
                }

                // Kaydet
                $category->save();
            }

            // SEO çevrilmiş verilerini kaydet (eğer mevcut ise)
            if (!empty($translatedSeoData) && $seoSetting) {
                // SEO Title
                if (isset($translatedSeoData['seo_title'])) {
                    $seoTitles = $seoSetting->titles ?? [];
                    $seoTitles[$targetLanguage] = $translatedSeoData['seo_title'];
                    $seoSetting->titles = $seoTitles;
                }

                // SEO Description
                if (isset($translatedSeoData['seo_description'])) {
                    $seoDescriptions = $seoSetting->descriptions ?? [];
                    $seoDescriptions[$targetLanguage] = $translatedSeoData['seo_description'];
                    $seoSetting->descriptions = $seoDescriptions;
                }

                // SEO Keywords
                if (isset($translatedSeoData['seo_keywords'])) {
                    $seoKeywords = $seoSetting->keywords ?? [];
                    $seoKeywords[$targetLanguage] = $translatedSeoData['seo_keywords'];
                    $seoSetting->keywords = $seoKeywords;
                }

                // SEO Kaydet
                $seoSetting->save();

                Log::info("✅ Portfolio Category SEO çevirisi kaydedildi", [
                    'category_id' => $categoryId,
                    'target_language' => $targetLanguage,
                    'translated_seo_fields' => array_keys($translatedSeoData)
                ]);
            }

            // Sonuç hazırla
            $allTranslatedData = $translatedData;
            if (!empty($translatedSeoData)) {
                $allTranslatedData = array_merge($allTranslatedData, $translatedSeoData);
            }

            if (!empty($allTranslatedData)) {
                Log::info("✅ Portfolio Category ve SEO çevirisi tamamlandı", [
                    'category_id' => $categoryId,
                    'target_language' => $targetLanguage,
                    'translated_fields' => array_keys($allTranslatedData)
                ]);

                return [
                    'success' => true,
                    'translated_data' => $allTranslatedData,
                    'target_language' => $targetLanguage
                ];
            }

            throw new \Exception("Çevrilecek veri bulunamadı");

        } catch (\Exception $e) {
            Log::error("❌ Portfolio Category çeviri hatası", [
                'category_id' => $categoryId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Announcement çeviri işlemi
     */
    protected function translateAnnouncement(int $announcementId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            // Announcement'ı bul ve SEO setting'i yükle
            $announcement = Announcement::with('seoSetting')->find($announcementId);
            if (!$announcement) {
                throw new \Exception("Announcement bulunamadı: {$announcementId}");
            }

            Log::info("📢 Announcement çevirisi başlatıldı", [
                'announcement_id' => $announcementId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // Kaynak dil verilerini al
            $sourceData = [
                'title' => $announcement->getTranslated('title', $sourceLanguage),
                'body' => $announcement->getTranslated('body', $sourceLanguage),
                'slug' => $announcement->getTranslated('slug', $sourceLanguage)
            ];

            // SEO verilerini al (eğer mevcut ise)
            $seoSetting = $announcement->seoSetting;
            $seoSourceData = [];
            if ($seoSetting) {
                $seoSourceData = [
                    'seo_title' => $seoSetting->getTranslated('titles', $sourceLanguage),
                    'seo_description' => $seoSetting->getTranslated('descriptions', $sourceLanguage),
                    'seo_keywords' => $seoSetting->getTranslated('keywords', $sourceLanguage),
                ];
            }

            // Kaynak verilerini kontrol et
            if (empty($sourceData['title']) && empty($sourceData['body'])) {
                throw new \Exception("Kaynak dil ({$sourceLanguage}) verileri bulunamadı");
            }

            $translatedData = [];
            $translatedSeoData = [];

            // Title çevir
            if (!empty($sourceData['title'])) {
                $translatedTitle = $this->aiService->translateText(
                    $sourceData['title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'announcement_title', 'max_length' => 255]
                );
                $translatedData['title'] = $translatedTitle;
                Log::info("✅ Announcement Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Body çevir (HTML korunarak)
            if (!empty($sourceData['body'])) {
                $optimizedContext = "Sen profesyonel bir çevirmensin. Aşağıdaki HTML içeriği {$sourceLanguage} dilinden {$targetLanguage} diline çevir. Kurallar:
1. TAMAMEN ÇEVİR - Çıktıda {$sourceLanguage} kelime kalmasın
2. HTML etiketlerini aynen koru, sadece metin içeriğini çevir
3. Profesyonel iş dili kullan
4. Hedef dilde doğal ifade et
5. Başlıklar, paragraflar, listeler dahil TÜM metinleri çevir
6. Sadece çeviriyi döndür, başka açıklama ekleme

Content type: Announcement content";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext
                );
                $translatedData['body'] = $translatedBody;
                Log::info("✅ Announcement Body çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Slug oluştur (title'dan)
            if (!empty($translatedData['title'])) {
                $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                    Announcement::class,
                    $translatedData['title'],
                    $targetLanguage,
                    'slug',
                    'announcement_id',
                    $announcementId
                );
                Log::info("✅ Announcement Slug oluşturuldu: {$translatedData['slug']}");
            }

            // SEO Title çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_title'])) {
                $translatedSeoTitle = $this->aiService->translateText(
                    $seoSourceData['seo_title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'announcement_seo_title', 'max_length' => 60]
                );
                $translatedSeoData['seo_title'] = $translatedSeoTitle;
                Log::info("✅ Announcement SEO Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Description çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_description'])) {
                $translatedSeoDescription = $this->aiService->translateText(
                    $seoSourceData['seo_description'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'announcement_seo_description', 'max_length' => 160]
                );
                $translatedSeoData['seo_description'] = $translatedSeoDescription;
                Log::info("✅ Announcement SEO Description çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // SEO Keywords çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_keywords'])) {
                $keywordsContext = "Translate these announcement SEO keywords to {$targetLanguage}. Keep them relevant and concise. Separate with commas.";
                $translatedSeoKeywords = $this->aiService->translateText(
                    $seoSourceData['seo_keywords'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => $keywordsContext, 'max_length' => 255]
                );
                $translatedSeoData['seo_keywords'] = $translatedSeoKeywords;
                Log::info("✅ Announcement SEO Keywords çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Çevrilmiş verileri kaydet
            if (!empty($translatedData)) {
                // Title
                if (isset($translatedData['title'])) {
                    $titles = $announcement->title ?? [];
                    $titles[$targetLanguage] = $translatedData['title'];
                    $announcement->title = $titles;
                }

                // Body
                if (isset($translatedData['body'])) {
                    $bodies = $announcement->body ?? [];
                    $bodies[$targetLanguage] = $translatedData['body'];
                    $announcement->body = $bodies;
                }

                // Slug
                if (isset($translatedData['slug'])) {
                    $slugs = $announcement->slug ?? [];
                    $slugs[$targetLanguage] = $translatedData['slug'];
                    $announcement->slug = $slugs;
                }

                // Kaydet
                $announcement->save();
            }

            // SEO çevrilmiş verilerini kaydet (eğer mevcut ise)
            if (!empty($translatedSeoData) && $seoSetting) {
                // SEO Title
                if (isset($translatedSeoData['seo_title'])) {
                    $seoTitles = $seoSetting->titles ?? [];
                    $seoTitles[$targetLanguage] = $translatedSeoData['seo_title'];
                    $seoSetting->titles = $seoTitles;
                }

                // SEO Description
                if (isset($translatedSeoData['seo_description'])) {
                    $seoDescriptions = $seoSetting->descriptions ?? [];
                    $seoDescriptions[$targetLanguage] = $translatedSeoData['seo_description'];
                    $seoSetting->descriptions = $seoDescriptions;
                }

                // SEO Keywords
                if (isset($translatedSeoData['seo_keywords'])) {
                    $seoKeywords = $seoSetting->keywords ?? [];
                    $seoKeywords[$targetLanguage] = $translatedSeoData['seo_keywords'];
                    $seoSetting->keywords = $seoKeywords;
                }

                // SEO Kaydet
                $seoSetting->save();

                Log::info("✅ Announcement SEO çevirisi kaydedildi", [
                    'announcement_id' => $announcementId,
                    'target_language' => $targetLanguage,
                    'translated_seo_fields' => array_keys($translatedSeoData)
                ]);
            }

            // Sonuç hazırla
            $allTranslatedData = $translatedData;
            if (!empty($translatedSeoData)) {
                $allTranslatedData = array_merge($allTranslatedData, $translatedSeoData);
            }

            if (!empty($allTranslatedData)) {
                Log::info("✅ Announcement ve SEO çevirisi tamamlandı", [
                    'announcement_id' => $announcementId,
                    'target_language' => $targetLanguage,
                    'translated_fields' => array_keys($allTranslatedData)
                ]);

                return [
                    'success' => true,
                    'translated_data' => $allTranslatedData,
                    'target_language' => $targetLanguage
                ];
            }

            throw new \Exception("Çevrilecek veri bulunamadı");

        } catch (\Exception $e) {
            Log::error("❌ Announcement çeviri hatası", [
                'announcement_id' => $announcementId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 🔄 DİNAMİK PROVIDER FAILOVER SİSTEMİ
     * Provider sırasına göre çeviriye çalışır, başarısız olursa sonrakine geçer
     */
    private function tryProvidersWithFailover($providerManager, $fullPrompt): string
    {
        // Aktif provider'ları al (öncelik sırasına göre)
        $activeProviders = $providerManager->getActiveProviders()->sortBy('priority');
        
        if ($activeProviders->isEmpty()) {
            throw new \Exception('Hiç aktif AI provider bulunamadı');
        }
        
        $lastException = null;
        
        // Her provider'ı sırayla dene
        foreach ($activeProviders as $provider) {
            try {
                Log::info("🤖 Provider deneniyor: {$provider->name}", [
                    'provider_id' => $provider->id,
                    'priority' => $provider->priority ?? 0
                ]);
                
                // Provider service'i al ve çeviriyi dene
                $providerService = $this->getProviderService($provider);
                
                if (!$providerService) {
                    Log::warning("⚠️ Provider service bulunamadı: {$provider->name}");
                    continue;
                }
                
                // Provider ile çeviriye çalış - Format'ı provider'a göre ayarla
                if (in_array(strtolower($provider->name), ['deepseek'])) {
                    // DeepSeek array messages bekliyor
                    $response = $providerService->ask([
                        ['role' => 'user', 'content' => $fullPrompt]
                    ], false);
                } else {
                    // OpenAI, Anthropic string bekliyor
                    $response = $providerService->ask($fullPrompt, [
                        'model' => $provider->default_model ?? null,
                        'temperature' => 0.1,
                        'max_tokens' => 4000
                    ]);
                }
                
                // Response'u temizle ve döndür
                $translatedText = $this->extractResponseText($response);
                
                // ENHANCED VALIDATION - Hatalı yanıtları reddet
                if (!empty(trim($translatedText)) && $this->isValidTranslation($translatedText)) {
                    Log::info("✅ Çeviri başarılı: {$provider->name}");
                    
                    // 💰 CREDIT DEDUCTION - Per translation operation
                    try {
                        $inputTokens = (int) (strlen($fullPrompt) / 4);
                        $outputTokens = (int) (strlen($translatedText) / 4);
                        $totalTokens = $inputTokens + $outputTokens;
                        
                        // Get current tenant for credit deduction
                        $tenantId = tenancy()->tenant?->id ?? 1;
                        $tenant = \App\Models\Tenant::find($tenantId);
                        
                        if ($tenant) {
                            // Use ai_use_credits helper for unified credit deduction
                            ai_use_credits($totalTokens / 1000, $tenant->id, [
                                'usage_type' => 'translation',
                                'description' => 'AI Translation: Bulk content translation',
                                'input_tokens' => $inputTokens,
                                'output_tokens' => $outputTokens,
                                'provider_name' => $provider->name,
                                'model' => $provider->default_model ?? 'unknown'
                            ]);
                            
                            Log::info('💰 KREDİ DÜŞÜRÜLDİ: 1 DİL = 1 KREDİ (TOKEN BAZLI) - Translation credit deducted', [
                                'tenant_id' => $tenant->id,
                                'credits_used' => $totalTokens / 1000,
                                'credit_rule' => '1 DİL = 1 KREDİ (TOKEN BAZLI)',
                                'input_tokens' => $inputTokens,
                                'output_tokens' => $outputTokens,
                                'provider' => $provider->name,
                                'remaining_credits' => $tenant->fresh()->ai_credits_balance ?? 'unknown'
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('⚠️ Credit deduction failed for translation', [
                            'error' => $e->getMessage(),
                            'provider' => $provider->name
                        ]);
                        // Continue with translation even if credit deduction fails
                    }
                    
                    // 📊 CONVERSATION TRACKER - AI çeviri kaydı
                    try {
                        ConversationTracker::saveTranslation(
                            substr($fullPrompt, 0, 500), // Original text
                            'tr', // Default source lang
                            'ar', // Default target lang 
                            substr($translatedText, 0, 500), // Translated text
                            [
                                'tokens_used' => ($inputTokens + $outputTokens),
                                'model' => $provider->default_model ?? 'unknown',
                                'processing_time' => 0 // No timing available
                            ],
                            'bulk_translation', // Context
                            false // preserve_html
                        );
                        Log::info('📊 ConversationTracker kaydı tamamlandı', [
                            'provider' => $provider->name,
                            'tokens' => ($inputTokens + $outputTokens)
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('⚠️ ConversationTracker kayıt hatası', [
                            'error' => $e->getMessage(),
                            'provider' => $provider->name
                        ]);
                        // Hata olsa bile çeviri çalışmaya devam etsin
                    }

                    return $translatedText;
                } else {
                    Log::warning("❌ Geçersiz çeviri yanıtı - FALLBACK: Original text korunuyor", [
                        'provider' => $provider->name,
                        'response_preview' => substr($translatedText, 0, 100),
                        'original_text' => substr($fullPrompt, 0, 100)
                    ]);
                    // 🛡️ FALLBACK: AI çeviremezse original text'i koru
                    return $fullPrompt;
                }
                
            } catch (\Exception $e) {
                Log::warning("❌ Provider başarısız: {$provider->name}", [
                    'error' => $e->getMessage(),
                    'provider_id' => $provider->id
                ]);
                
                $lastException = $e;
                continue; // Sonraki provider'ı dene
            }
        }
        
        // Tüm provider'lar başarısız oldu
        $errorMessage = "Tüm AI provider'lar başarısız oldu";
        if ($lastException) {
            $errorMessage .= ". Son hata: " . $lastException->getMessage();
        }
        
        Log::error("💥 PROVIDER FAILOVER BAŞARISIZ", [
            'tried_providers' => $activeProviders->pluck('name')->toArray(),
            'last_error' => $lastException ? $lastException->getMessage() : 'Unknown'
        ]);
        
        throw new \Exception($errorMessage);
    }
    
    /**
     * Provider'a göre service instance'ını al
     */
    private function getProviderService($provider)
    {
        try {
            switch (strtolower($provider->name)) {
                case 'openai':
                    return app(\Modules\AI\App\Services\OpenAIService::class);
                    
                case 'anthropic':
                case 'claude':
                    return app(\Modules\AI\App\Services\AnthropicService::class);
                    
                case 'deepseek':
                    return app(\Modules\AI\App\Services\DeepSeekService::class);
                    
                // XAI/Grok support not implemented yet
                // case 'xai':
                // case 'grok':
                //     return app(\Modules\AI\App\Services\XAIService::class);
                    
                default:
                    Log::warning("⚠️ Bilinmeyen provider: {$provider->name}");
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("❌ Provider service oluşturulamadı: {$provider->name}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Response'dan text'i çıkar (array/string format handling)
     */
    private function extractResponseText($response): string
    {
        if (is_string($response)) {
            return trim($response);
        }
        
        if (is_array($response)) {
            // Farklı response formatlarını kontrol et
            $possibleKeys = ['response', 'content', 'text', 'message', 'data', 'result'];
            
            foreach ($possibleKeys as $key) {
                if (isset($response[$key]) && is_string($response[$key])) {
                    return trim($response[$key]);
                }
            }
            
            // Eğer array içinde nested array varsa
            if (isset($response[0]) && is_string($response[0])) {
                return trim($response[0]);
            }
        }
        
        Log::warning("⚠️ Response formatı tanınmadı", [
            'response_type' => gettype($response),
            'response_preview' => is_scalar($response) ? $response : json_encode($response)
        ]);
        
        return '';
    }
    
    /**
     * Çeviri yanıtının geçerli olup olmadığını kontrol et
     * SADECE AI'ın gerçek hata mesajlarını tespit eder, normal metin içindeki kelimeleri engellemez
     */
    private function isValidTranslation(string $text): bool
    {
        // SADECE AI'ın gerçek hata/ret mesajlarını tespit et
        $aiErrorPatterns = [
            '/^(i\'m\s+)?sorry,?\s+(but\s+)?i\s+(can\'?t|cannot)\s+(assist|help|provide)/i',
            '/^üzgünüm,?\s+(ama\s+)?bu\s+(isteği?|metni?)\s+çeviremiyorum/i',
            '/^i\s+(can\'?t|cannot)\s+assist\s+with\s+that/i',
            '/^şu\s+anda\s+(cevap\s+üretemiyorum|çeviri\s+yapamıyorum)/i',
            '/authentication\s+(failed?|error)/i',
            '/api\s+key\s+(invalid|missing|error)/i',
            '/^(error|hata)\s*:\s*invalid\s+request/i'
        ];
        
        $trimmedText = trim($text);
        
        // Çok kısa yanıtları reddet
        if (strlen($trimmedText) < 3) {
            return false;
        }
        
        // AI'ın gerçek hata mesajlarını pattern matching ile tespit et
        foreach ($aiErrorPatterns as $pattern) {
            if (preg_match($pattern, $trimmedText)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Text'in çevrilebilir olup olmadığını kontrol eder
     * 🚨 KRİTİK: HTML ETİKETLERİ KORUMA SİSTEMİ - SmartHtmlTranslationService ile uyumlu
     */
    private function isTranslatableText(string $text): bool
    {
        $trimmedText = trim($text);
        
        // Boş veya sadece whitespace
        if ($trimmedText === '') {
            return false;
        }

        // 🚨 HTML ETİKETLERİ KORUMA (En üst öncelik)
        // HTML etiketleri (açık/kapalı)
        if (preg_match('/<[^>]+>/', $trimmedText)) {
            return false;
        }

        // HTML entity'leri (&amp; &lt; &gt; &quot; vb.)
        if (preg_match('/&[a-zA-Z0-9]+;/', $trimmedText)) {
            return false;
        }

        // CSS class/id pattern'leri (class="xyz", #id, .class)
        if (preg_match('/^[\.#][a-zA-Z0-9\-\_\s]+$/', $trimmedText)) {
            return false;
        }

        // CSS properties ve values
        if (preg_match('/^[a-zA-Z\-]+\s*:\s*[^;]+;?$/', $trimmedText)) {
            return false;
        }

        // JavaScript kod pattern'leri
        if (preg_match('/(function\s*\(|var\s+|let\s+|const\s+|if\s*\(|for\s*\(|while\s*\()/', $trimmedText)) {
            return false;
        }

        // HTML attribute pattern'leri (class="", id="", data-* vb.)
        if (preg_match('/^[a-zA-Z\-]+\s*=\s*["\'][^"\']*["\']$/', $trimmedText)) {
            return false;
        }

        // Bootstrap/Tailwind CSS class pattern'leri
        if (preg_match('/^(btn|col|row|container|flex|grid|text|bg|border|rounded|shadow|p|m|w|h|justify|items|space)-/', $trimmedText)) {
            return false;
        }

        // 🔥 ENHANCED HTML TAG PROTECTION - Comprehensive list (SmartHtmlTranslationService ile uyumlu)
        $htmlTags = [
            // Basic HTML tags
            'div', 'span', 'section', 'article', 'header', 'footer', 'nav', 'main', 'aside',
            'ul', 'ol', 'li', 'table', 'tr', 'td', 'th', 'thead', 'tbody', 'tfoot', 'form', 'input',
            'button', 'select', 'option', 'textarea', 'label', 'fieldset', 'legend', 'img',
            'video', 'audio', 'canvas', 'svg', 'path', 'circle', 'rect', 'line', 'polygon',
            'body', 'html', 'head', 'title', 'meta', 'link', 'script', 'style', 'figure',
            'figcaption', 'picture', 'source', 'iframe', 'embed', 'object', 'param',
            // Additional HTML5 tags
            'address', 'blockquote', 'cite', 'code', 'pre', 'small', 'strong', 'em', 'mark',
            'del', 'ins', 'sub', 'sup', 'abbr', 'dfn', 'time', 'kbd', 'samp', 'var',
            'details', 'summary', 'dialog', 'menu', 'menuitem', 'output', 'progress', 'meter',
            // Framework specific classes/tags  
            'container', 'container-fluid', 'row', 'col', 'card', 'modal', 'dropdown',
            'navbar', 'sidebar', 'wrapper', 'overlay', 'backdrop',
            // 🚨 CONTENT WORDS REMOVED: hero, banner, content - These are content words!
            // Bootstrap/Tailwind common classes
            'btn', 'form-control', 'form-group', 'input-group', 'breadcrumb',
            'carousel', 'collapse', 'dropdown-menu', 'list-group', 'nav-item', 'nav-link',
            'pagination', 'popover', 'progress-bar', 'spinner', 'tooltip'
            // 🚨 CONTENT WORDS REMOVED: alert, badge, toast - These can be content words!
        ];
        
        // 🚨 ARABIC HTML TAG TRANSLATIONS PROTECTION
        // Common HTML tag translations in Arabic that should not be translated back
        $arabicHtmlTranslations = [
            'قسم', 'جانب', 'رأس', 'تذييل', 'تنقل', 'رئيسي', 'جانبي', 'حاوي',
            'قائمة', 'عنصر', 'جدول', 'صف', 'خلية', 'نموذج', 'إدخال', 'زر', 'اختيار',
            'خيار', 'منطقة', 'شريط',
            // 🚨 MORE CONTENT WORDS REMOVED: 'بطل' (hero), 'لافتة' (banner), 'محتوى' (content), 'صورة' (image), 'فيديو' (video), 'تسمية' (label), 'غطاء' (cover) - These are content words!
            'قائمة منسدلة', 'شريط تنقل', 'شريط جانبي', 'عنصر قائمة', 'رابط تنقل'
        ];
        
        // Check against HTML tags (case-insensitive)
        if (in_array(strtolower($trimmedText), $htmlTags)) {
            return false;
        }
        
        // Check against Arabic HTML translations
        if (in_array($trimmedText, $arabicHtmlTranslations)) {
            return false;
        }

        // CSS unit'leri (px, em, rem, %, vw, vh vb.)
        if (preg_match('/^\d+(\.\d+)?(px|em|rem|%|vw|vh|pt|pc|in|cm|mm|ex|ch|vmin|vmax)$/', $trimmedText)) {
            return false;
        }

        // RGB/HEX renk kodları
        if (preg_match('/^(#[0-9a-fA-F]{3,8}|rgb\(|rgba\(|hsl\(|hsla\()/', $trimmedText)) {
            return false;
        }

        // JavaScript/JSON benzeri yapılar
        if (preg_match('/^[\{\[\"\']+.*[\}\]\"\']+$/', $trimmedText)) {
            return false;
        }

        // Sadece sayılar, semboller ve whitespace - ESNEKLEŞTİRİLDİ
        if (preg_match('/^[\d\s\W]*$/', $trimmedText)) {
            // Eğer hiç harf yoksa atla
            if (!preg_match('/[\p{L}]/u', $trimmedText)) {
                return false;
            }
        }

        // Çok kısa text'ler - UTF-8 karakter desteği ile
        if (mb_strlen($trimmedText, 'UTF-8') < 2) {
            return false;
        }

        // URL, email, kod benzeri pattern'ler
        if (preg_match('/^(https?:\/\/|www\.|@|\{|\[|#[a-zA-Z]|[a-zA-Z]+\([^\)]*\))/', $trimmedText)) {
            return false;
        }

        return true;
    }
}