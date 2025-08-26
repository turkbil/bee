<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Announcement\App\Models\Announcement;
use Modules\AI\app\Services\AIService;
use Modules\AI\App\Services\ConversationTracker;

class FastHtmlTranslationService
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🚀 ASYNC ONLY - NO MORE SYNC TRANSLATION
     * Forces all translations to use background jobs
     */
    public function translateHtmlContentFast(string $html, string $fromLang, string $toLang, string $context, array $options = []): string
    {
        // 🔎 ENHANCED ASYNC JOB DETECTION - Multiple methods
        $isAsyncJob = ($options['source'] ?? '') === 'async_job';
        
        // 🔎 Queue job detection - More permissive approach
        $isConsole = app()->runningInConsole();
        $sapi = php_sapi_name();
        $isQueueWorker = $isConsole && (strpos($sapi, 'cli') !== false || in_array($sapi, ['cli', 'phpdbg', 'embed']));
        
        // 🔎 Additional queue context checks
        $hasQueueConnection = !empty(config('queue.default')) && config('queue.default') !== 'sync';
        
        // 🎯 ALLOW IF ANY ASYNC INDICATOR IS TRUE
        // ADDITIONAL CHECK: Allow if running via php artisan queue:work
        $isRunningViaQueueWork = $isConsole && (strpos(implode(' ', $_SERVER['argv'] ?? []), 'queue:work') !== false);
        
        $isAllowedAsync = $isAsyncJob || $isQueueWorker || ($isConsole && $hasQueueConnection) || $isRunningViaQueueWork;
        
        // 🔍 ULTRA DEBUG - Her zaman log
        Log::info('🔍 FastHtmlTranslationService - Detection Debug', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'is_console' => $isConsole,
            'php_sapi' => $sapi,
            'is_async_job' => $isAsyncJob,
            'is_queue_worker' => $isQueueWorker,
            'queue_default' => config('queue.default'),
            'has_queue_connection' => $hasQueueConnection,
            'is_running_via_queue_work' => $isRunningViaQueueWork,
            'is_allowed_async' => $isAllowedAsync,
            'sapi_contains_cli' => strpos($sapi, 'cli') !== false,
            'sapi_in_array' => in_array($sapi, ['cli', 'phpdbg', 'embed']),
            'options_source' => $options['source'] ?? 'not_set',
            'server_argv' => implode(' ', $_SERVER['argv'] ?? [])
        ]);
        
        if (!$isAllowedAsync) {
            // 🚫 SYNC TRANSLATION BLOCKED - FORCE ASYNC ONLY
            Log::error('🚫 SYNC TRANSLATION BLOCKED - Use TranslatePageJob instead', [
                'html_length' => strlen($html),
                'from_lang' => $fromLang,
                'to_lang' => $toLang,
                'blocked_reason' => '504_timeout_prevention',
                'is_console' => $isConsole,
                'php_sapi' => $sapi,
                'is_async_job' => $isAsyncJob,
                'is_queue_worker' => $isQueueWorker,
                'queue_default' => config('queue.default'),
                'has_queue_connection' => $hasQueueConnection
            ]);
            
            throw new \Exception("SYNC translation blocked! Use async TranslatePageJob to prevent 504 errors.");
        }
        
        // 🚀 ASYNC JOB DETECTED - PROCEED WITH TRANSLATION
        Log::info('🚀 Async translation allowed', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'source' => 'async_job'
        ]);
        
        Log::info('🚀 SÜPER HIZLI HTML çeviri başlıyor', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'timeout_set' => '300s'
        ]);

        try {
            // 1. HTML'den sadece text'leri çıkar (regex ile)
            $textMatches = [];
            $pattern = '/>([\s\S]*?)</';
            preg_match_all($pattern, $html, $textMatches, PREG_OFFSET_CAPTURE);
            
            $textsToTranslate = [];
            $placeholders = [];
            $counter = 0;
            
            foreach ($textMatches[1] as $match) {
                $text = trim($match[0]);
                
                // Boş, kısa veya sadece sembol olan text'leri atla
                if (strlen($text) < 3 || 
                    preg_match('/^[\s\d\-\.\,\+\*\/\=\(\)]+$/', $text) ||
                    preg_match('/^[^\p{L}]+$/u', $text)) {
                    continue;
                }
                
                $placeholder = "|||TRANSLATE_{$counter}|||";
                $textsToTranslate[] = $text;
                $placeholders[] = $placeholder;
                $counter++;
            }
            
            if (empty($textsToTranslate)) {
                Log::info('📝 Çevrilecek text bulunamadı');
                return $html;
            }
            
            Log::info('📝 Text extraction tamamlandı', [
                'texts_found' => count($textsToTranslate),
                'sample_texts' => array_slice($textsToTranslate, 0, 3)
            ]);
            
            // 2. BATCH SIZE KONTROLÜ - Maksimum 10 text per batch
            $maxTextsPerBatch = 10;
            if (count($textsToTranslate) > $maxTextsPerBatch) {
                Log::info('📊 Batch processing gerekli', [
                    'total_texts' => count($textsToTranslate),
                    'max_per_batch' => $maxTextsPerBatch
                ]);
                return $this->processBatchTranslation($html, $textsToTranslate, $fromLang, $toLang, $context);
            }
            
            // 2. Tüm text'leri birleştir ve tek seferde çevir
            $combinedText = implode("\n---SEPARATOR---\n", $textsToTranslate);
            
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
1. Each text segment is separated by '---SEPARATOR---'
2. Translate EVERY segment to {$targetLanguageName}
3. Keep exact same number of segments
4. Use professional business tone in {$targetLanguageName}
5. NO English unless target language IS English
6. NO fallback to common languages

🎯 TARGET LANGUAGE FOCUS:
- You MUST write in: {$targetLanguageName}
- Language code: {$toLang}
- Write naturally in {$targetLanguageName}
- Use {$targetLanguageName} grammar and structure
- Think in {$targetLanguageName}, not English

VERIFICATION: Before responding, confirm your output is 100% {$targetLanguageName}.

Content to translate:";

            // TIMEOUT KORUNMASI İLE AI ÇAĞRISI
            $startTime = time();
            $translatedCombined = $this->callDirectAIProvider(
                $combinedText,
                $fromLang,
                $toLang,
                $bulkContext
            );
            $processingTime = time() - $startTime;
            
            Log::info('⏱️ AI çağrı süresi', [
                'processing_time' => $processingTime . 's',
                'timeout_remaining' => (300 - $processingTime) . 's'
            ]);
            
            // 3. Çevrilen text'leri ayır
            Log::info('🔍 DEBUG: Çevrilen text parsing', [
                'translated_combined_length' => strlen($translatedCombined),
                'translated_preview' => substr($translatedCombined, 0, 500),
                'separator_count' => substr_count($translatedCombined, "\n---SEPARATOR---\n"),
                'expected_texts' => count($textsToTranslate)
            ]);
            
            $translatedTexts = explode("\n---SEPARATOR---\n", $translatedCombined);
            
            Log::info('🔍 DEBUG: Parsing sonucu', [
                'parsed_count' => count($translatedTexts),
                'expected_count' => count($textsToTranslate),
                'parsed_texts' => array_slice($translatedTexts, 0, 3)
            ]);
            
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
            $translatedHtml = $html;
            
            foreach ($textsToTranslate as $index => $originalText) {
                $translatedText = trim($translatedTexts[$index] ?? $originalText);
                
                // GÜÇLENDIRILMIŞ REPLACEMENT - Whitespace tolerance ile
                $originalTextEscaped = preg_quote($originalText, '/');
                $pattern = '/>(\s*)' . $originalTextEscaped . '(\s*)</';
                $replacement = '>$1' . $translatedText . '$2<';
                
                $translatedHtml = preg_replace($pattern, $replacement, $translatedHtml);
                
                Log::info('🔄 Text replacement', [
                    'original' => substr($originalText, 0, 50),
                    'translated' => substr($translatedText, 0, 50),
                    'pattern_matched' => preg_match($pattern, $translatedHtml) > 0
                ]);
            }
            
            Log::info('✅ SÜPER HIZLI HTML çeviri tamamlandı', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'texts_translated' => count($translatedTexts),
                'performance' => 'BULK_TRANSLATION'
            ]);
            
            return $translatedHtml;
            
        } catch (\Exception $e) {
            Log::error('❌ SÜPER HIZLI HTML çeviri hatası', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html)
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
     * BYPASS AIService completely to prevent recursion
     */
    private function callDirectAIProvider(string $text, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🚀 Direkt AI provider çağrısı - BYPASS MODE', [
            'text_length' => strlen($text),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'context' => substr($context, 0, 100) . '...'
        ]);
        
        try {
            // 🔥 INFINITE LOOP PREVENTION: Direct AI provider call
            $providerManager = app(\Modules\AI\app\Services\AIProviderManager::class);
            $activeProviders = $providerManager->getActiveProviders();
            $activeProvider = $activeProviders->first();
            
            if (!$activeProvider) {
                throw new \Exception('Aktif AI provider bulunamadı');
            }
            
            Log::info('🔄 Direct provider bypass', [
                'provider' => $activeProvider->name,
                'bypass_reason' => 'Preventing FastHtml recursion'
            ]);
            
            // 🚀 DIRECT PROVIDER CALL - NO AISERVICE!
            $prompt = $this->buildTranslationPrompt($text, $fromLang, $toLang, $context);
            
            // OpenAI direkt çağrı
            if ($activeProvider->name === 'openai') {
                $response = $this->callOpenAIDirect($prompt, $activeProvider);
            } else {
                // Fallback: Basit prompt ile AIService ama SHORT text olarak
                $shortPrompt = "Translate to {$toLang}: " . substr($text, 0, 200);
                $response = $this->aiService->generateTextWithPrompt($shortPrompt, ['max_tokens' => 500]);
                $response = $response['content'] ?? $text;
            }
            
            // 📊 CONVERSATION KAYIT - claude_ai.md uyumlu
            ConversationTracker::saveTranslation(
                $text, 
                $fromLang, 
                $toLang, 
                $response, 
                ['tokens_used' => 0, 'model' => 'bulk_translation_direct'], 
                $context, 
                false
            );
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('❌ Direkt provider çağrısı hatası', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text)
            ]);
            
            // 🚨 EMERGENCY FALLBACK: Return original text
            return $text;
        }
    }
    
    /**
     * Build basic translation prompt
     */
    private function buildTranslationPrompt(string $text, string $fromLang, string $toLang, string $context): string
    {
        return "Translate the following text from {$fromLang} to {$toLang}. Maintain the original format and structure.\n\nText to translate:\n{$text}";
    }
    
    /**
     * Direct OpenAI call (bypass AIService)
     */
    private function callOpenAIDirect(string $prompt, $provider): string
    {
        // Basit OpenAI çağrısı
        $data = [
            'model' => $provider->model ?? 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.3
        ];
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $provider->api_key
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception("OpenAI API error: HTTP {$httpCode}");
        }
        
        $decoded = json_decode($response, true);
        return $decoded['choices'][0]['message']['content'] ?? 'Translation failed';
    }

    /**
     * 🔄 BATCH PROCESSING SİSTEMİ - Büyük HTML'leri parçalara bölerek çevir
     */
    private function processBatchTranslation(string $html, array $textsToTranslate, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🔄 Batch processing başlatılıyor', [
            'total_texts' => count($textsToTranslate),
            'html_length' => strlen($html)
        ]);

        $maxTextsPerBatch = 10;
        $batches = array_chunk($textsToTranslate, $maxTextsPerBatch);
        $allTranslatedTexts = [];
        
        foreach ($batches as $batchIndex => $batch) {
            try {
                Log::info("📦 Batch {$batchIndex} işleniyor", [
                    'batch_size' => count($batch),
                    'progress' => ($batchIndex + 1) . '/' . count($batches)
                ]);
                
                $combinedText = implode("\n---SEPARATOR---\n", $batch);
                
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
1. Each text segment is separated by '---SEPARATOR---'
2. Translate EVERY segment to {$targetLanguageName}
3. Keep exact same number of segments
4. Use professional business tone in {$targetLanguageName}
5. NO English unless target language IS English
6. NO fallback to common languages

Content to translate:";

                $translatedCombined = $this->callDirectAIProvider(
                    $combinedText,
                    $fromLang,
                    $toLang,
                    $bulkContext
                );
                
                $translatedTexts = explode("\n---SEPARATOR---\n", $translatedCombined);
                
                if (count($translatedTexts) !== count($batch)) {
                    Log::warning("⚠️ Batch {$batchIndex} çeviri sayısı uyumsuz", [
                        'expected' => count($batch),
                        'received' => count($translatedTexts)
                    ]);
                    
                    // Bu batch için fallback: Her text'i ayrı çevir
                    $translatedTexts = [];
                    foreach ($batch as $text) {
                        $translatedTexts[] = $this->aiService->translateText($text, $fromLang, $toLang, [
                            'context' => $context,
                            'preserve_html' => false
                        ]);
                        sleep(1); // API rate limit koruması
                    }
                }
                
                $allTranslatedTexts = array_merge($allTranslatedTexts, $translatedTexts);
                
                Log::info("✅ Batch {$batchIndex} tamamlandı");
                
                // Batch'ler arası kısa bekleme
                if ($batchIndex < count($batches) - 1) {
                    sleep(2);
                }
                
            } catch (\Exception $e) {
                Log::error("❌ Batch {$batchIndex} hatası", [
                    'error' => $e->getMessage()
                ]);
                
                // Bu batch için fallback
                foreach ($batch as $text) {
                    $allTranslatedTexts[] = $this->aiService->translateText($text, $fromLang, $toLang, [
                        'context' => $context,
                        'preserve_html' => false
                    ]);
                }
            }
        }
        
        // HTML'de text'leri çevrilenleriyle değiştir
        $translatedHtml = $html;
        
        foreach ($textsToTranslate as $index => $originalText) {
            $translatedText = trim($allTranslatedTexts[$index] ?? $originalText);
            
            $originalTextEscaped = preg_quote($originalText, '/');
            $pattern = '/>(\s*)' . $originalTextEscaped . '(\s*)</';
            $replacement = '>$1' . $translatedText . '$2<';
            
            $translatedHtml = preg_replace($pattern, $replacement, $translatedHtml);
        }
        
        Log::info('✅ Batch processing tamamlandı', [
            'total_batches' => count($batches),
            'total_texts_translated' => count($allTranslatedTexts),
            'performance' => 'BATCH_TRANSLATION'
        ]);
        
        return $translatedHtml;
    }

    /**
     * Fallback: Yavaş ama güvenli çeviri
     */
    private function fallbackToSlowTranslation(string $html, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🐌 Fallback: Normal çeviri sistemi kullanılıyor');
        
        // Normal çeviri yap (kesilse bile)
        return $this->aiService->translateText($html, $fromLang, $toLang, [
            'context' => $context, 
            'preserve_html' => true
        ]);
    }

    /**
     * Entity çeviri işlemi - Queue Job için
     */
    public function translateEntity(string $entityType, int $entityId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            Log::info("🌍 FastHtmlTranslationService - Entity çevirisi başlatıldı", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // Entity type'a göre işlem yap
            switch ($entityType) {
                case 'page':
                    return $this->translatePage($entityId, $sourceLanguage, $targetLanguage);
                
                case 'portfolio':
                    return $this->translatePortfolio($entityId, $sourceLanguage, $targetLanguage);
                
                case 'portfolio_category':
                    return $this->translatePortfolioCategory($entityId, $sourceLanguage, $targetLanguage);
                
                case 'announcement':
                    return $this->translateAnnouncement($entityId, $sourceLanguage, $targetLanguage);
                
                default:
                    throw new \Exception("Desteklenmeyen entity type: {$entityType}");
            }

        } catch (\Exception $e) {
            Log::error("❌ FastHtmlTranslationService hatası", [
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
     * Page çeviri işlemi
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

            // Title çevir
            if (!empty($sourceData['title'])) {
                $translatedTitle = $this->aiService->translateText(
                    $sourceData['title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'page_title', 'max_length' => 255]
                );
                $translatedData['title'] = $translatedTitle;
                Log::info("✅ Title çevrildi: {$sourceLanguage} → {$targetLanguage}");
            }

            // Body çevir (HTML korunarak)
            if (!empty($sourceData['body'])) {
                $optimizedContext = "You are a professional translator. Translate the following HTML content to {$targetLanguage}. Requirements:
1. COMPLETE TRANSLATION - NO {$sourceLanguage} words should remain in the output
2. Preserve all HTML tags and structure exactly
3. Use professional business language
4. Ensure natural expression in target language
5. Translate ALL text content including headings, paragraphs, lists, etc.

Content type: Website page content";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext,
                    ['source' => 'async_job']
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

            // SEO Title çevir (eğer mevcut ise)
            if (!empty($seoSourceData['seo_title'])) {
                $translatedSeoTitle = $this->aiService->translateText(
                    $seoSourceData['seo_title'],
                    $sourceLanguage,
                    $targetLanguage,
                    ['context' => 'seo_title', 'max_length' => 60]
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
                $optimizedContext = "You are a professional translator. Translate the following HTML content to {$targetLanguage}. Requirements:
1. COMPLETE TRANSLATION - NO {$sourceLanguage} words should remain in the output
2. Preserve all HTML tags and structure exactly
3. Use professional business language
4. Ensure natural expression in target language
5. Translate ALL text content including headings, paragraphs, lists, etc.

Content type: Portfolio project description";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext,
                    ['source' => 'async_job']
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
                $optimizedContext = "You are a professional translator. Translate the following HTML content to {$targetLanguage}. Requirements:
1. COMPLETE TRANSLATION - NO {$sourceLanguage} words should remain in the output
2. Preserve all HTML tags and structure exactly
3. Use professional business language
4. Ensure natural expression in target language
5. Translate ALL text content including headings, paragraphs, lists, etc.

Content type: Portfolio category description";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext,
                    ['source' => 'async_job']
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
                $optimizedContext = "You are a professional translator. Translate the following HTML content to {$targetLanguage}. Requirements:
1. COMPLETE TRANSLATION - NO {$sourceLanguage} words should remain in the output
2. Preserve all HTML tags and structure exactly
3. Use professional business language
4. Ensure natural expression in target language
5. Translate ALL text content including headings, paragraphs, lists, etc.

Content type: Announcement content";

                $translatedBody = $this->translateHtmlContentFast(
                    $sourceData['body'],
                    $sourceLanguage,
                    $targetLanguage,
                    $optimizedContext,
                    ['source' => 'async_job']
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
     * 🌍 ENTERPRISE STREAMING TRANSLATION METHOD
     * Context-aware translation with progress callback support
     */
    public function translateWithContext(
        string $content,
        string $targetLanguage,
        array $context = [],
        callable $progressCallback = null
    ): string {
        Log::info('🌍 StreamingTranslation: translateWithContext started', [
            'content_length' => strlen($content),
            'target_language' => $targetLanguage,
            'context_keys' => array_keys($context)
        ]);

        try {
            // Progress callback - başlangıç
            if ($progressCallback) {
                $progressCallback(10);
            }

            // Context'ten prompt oluştur
            $contextPrompt = $this->buildContextPrompt($context);
            
            // Progress callback - context hazır
            if ($progressCallback) {
                $progressCallback(20);
            }

            // AI servis ile çeviri yap
            $translatedContent = $this->performContextualTranslation($content, $targetLanguage, $contextPrompt);
            
            // Progress callback - çeviri tamamlandı
            if ($progressCallback) {
                $progressCallback(90);
            }

            // Final cleanup
            $cleanedContent = $this->cleanupTranslatedContent($translatedContent);
            
            // Progress callback - temizlik tamamlandı
            if ($progressCallback) {
                $progressCallback(100);
            }

            Log::info('✅ StreamingTranslation: translateWithContext completed', [
                'original_length' => strlen($content),
                'translated_length' => strlen($cleanedContent),
                'target_language' => $targetLanguage
            ]);

            return $cleanedContent;

        } catch (\Exception $e) {
            Log::error('❌ StreamingTranslation: translateWithContext failed', [
                'content_length' => strlen($content),
                'target_language' => $targetLanguage,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Context-aware translation failed: " . $e->getMessage());
        }
    }

    /**
     * Context'ten prompt oluştur
     */
    private function buildContextPrompt(array $context): string
    {
        $prompt = "Sen profesyonel bir çevirmensin. Aşağıdaki context bilgilerini kullanarak doğru çeviri yap:\n\n";
        
        if (!empty($context['chunk_type'])) {
            $prompt .= "İçerik Tipi: " . $context['chunk_type'] . "\n";
        }
        
        if (!empty($context['semantic_context'])) {
            $prompt .= "Anlam Bağlamı: " . $context['semantic_context'] . "\n";
        }
        
        if (!empty($context['surrounding_text'])) {
            $prompt .= "Çevredeki Metin: " . $context['surrounding_text'] . "\n";
        }
        
        if (!empty($context['html_tags'])) {
            $prompt .= "HTML Etiketleri: " . implode(', ', $context['html_tags']) . "\n";
        }
        
        $prompt .= "\nKurallar:\n";
        $prompt .= "- HTML etiketlerini olduğu gibi koru\n";
        $prompt .= "- Bağlamsal anlam bütünlüğünü koru\n";
        $prompt .= "- Teknik terimleri uygun şekilde çevir\n";
        $prompt .= "- Tutarlı terminoloji kullan\n\n";
        
        return $prompt;
    }

    /**
     * Contextual translation gerçekleştir
     */
    private function performContextualTranslation(string $content, string $targetLanguage, string $contextPrompt): string
    {
        $fullPrompt = $contextPrompt . "Çevrilecek içerik:\n" . $content;
        
        // AI Service ile çeviri
        $response = $this->aiService->generateTextWithPrompt(
            $targetLanguage . " diline çevir: " . $fullPrompt,
            [
                'max_tokens' => 2048,
                'temperature' => 0.3,
                'context_aware' => true
            ]
        );

        return $response['content'] ?? $content;
    }

    /**
     * Çevrilmiş içeriği temizle
     */
    private function cleanupTranslatedContent(string $content): string
    {
        // Gereksiz boşlukları temizle
        $content = preg_replace('/\s+/', ' ', $content);
        
        // HTML etiketlerinin etrafındaki boşlukları düzelt
        $content = preg_replace('/>\s+</', '><', $content);
        
        // Baş ve son boşlukları temizle
        $content = trim($content);
        
        return $content;
    }
}