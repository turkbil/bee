<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Jobs\EnhancedChunkTranslationJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;

/**
 * 🚀 ENHANCED HTML TRANSLATION SERVICE V2.0
 * A1'deki başarılı sistemin Queue + Chunk + Separate versiyonu
 * 
 * Features:
 * - HTML structure preservation with exact whitespace matching
 * - Bulk translation for efficiency (separator-based approach)
 * - Smart chunking for very large content
 * - Queue-based background processing
 * - Real-time progress tracking
 * - Fallback mechanisms for error recovery
 */
class EnhancedHtmlTranslationService
{
    private $aiService;
    private const MAX_CHUNK_SIZE = 8000; // Güvenli chunk boyutu
    private const SEPARATOR = "\n---TRANSLATE-SEPARATOR---\n";
    
    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🎯 MAIN ENTRY POINT - Queue ile async çeviri başlatır
     */
    public function translateHtmlAsync(
        string $entityType, 
        int $entityId, 
        string $field,
        string $htmlContent,
        string $sourceLanguage,
        string $targetLanguage,
        array $options = []
    ): string {
        $translationId = $this->generateTranslationId($entityType, $entityId, $field, $targetLanguage);
        
        Log::info('🚀 Enhanced HTML Async Translation başlatıldı', [
            'translation_id' => $translationId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'field' => $field,
            'html_length' => strlen($htmlContent),
            'source' => $sourceLanguage,
            'target' => $targetLanguage
        ]);

        // Session'da progress tracking başlat
        $this->initializeProgressTracking($translationId);

        // HTML analiz et ve chunk'lara ayır
        $analysisResult = $this->analyzeHtmlContent($htmlContent);
        
        if ($analysisResult['should_chunk']) {
            // Büyük içerik - chunk'lara ayır ve queue'ya gönder
            $this->processLargeHtmlContent($translationId, $htmlContent, $sourceLanguage, $targetLanguage, $options);
        } else {
            // Küçük içerik - direkt bulk translation
            $this->processBulkTranslation($translationId, $htmlContent, $sourceLanguage, $targetLanguage, $options);
        }

        return $translationId;
    }

    /**
     * 🔍 HTML İçerik Analizi - Chunk gerekli mi?
     */
    private function analyzeHtmlContent(string $html): array
    {
        $textMatches = [];
        $pattern = '/>([\\s\\S]*?)</';
        preg_match_all($pattern, $html, $textMatches, PREG_OFFSET_CAPTURE);
        
        $textsToTranslate = [];
        foreach ($textMatches[1] as $match) {
            $text = trim($match[0]);
            if (strlen($text) >= 3 && !preg_match('/^[\\s\\d\\-\\.\\,\\+\\*\\/\\=\\(\\)]+$/', $text) 
                && !preg_match('/^[^\\p{L}]+$/u', $text)) {
                $textsToTranslate[] = $text;
            }
        }
        
        $combinedTextLength = strlen(implode(self::SEPARATOR, $textsToTranslate));
        
        $analysis = [
            'html_length' => strlen($html),
            'text_count' => count($textsToTranslate),
            'combined_text_length' => $combinedTextLength,
            'should_chunk' => $combinedTextLength > self::MAX_CHUNK_SIZE,
            'estimated_chunks' => $combinedTextLength > self::MAX_CHUNK_SIZE ? 
                ceil($combinedTextLength / self::MAX_CHUNK_SIZE) : 1,
            'translatable_texts' => $textsToTranslate
        ];
        
        Log::info('📊 HTML Content Analysis', $analysis);
        
        return $analysis;
    }

    /**
     * 🧩 Büyük İçerik - Chunk İşleme
     */
    private function processLargeHtmlContent(
        string $translationId,
        string $html,
        string $sourceLanguage,
        string $targetLanguage,
        array $options
    ): void {
        // HTML'i semantic chunk'lara ayır
        $chunks = $this->createSemanticChunks($html);
        
        Log::info('📦 Large HTML content chunked', [
            'translation_id' => $translationId,
            'total_chunks' => count($chunks),
            'chunk_sizes' => array_map('strlen', $chunks)
        ]);

        // Her chunk için ayrı job oluştur
        foreach ($chunks as $index => $chunk) {
            EnhancedChunkTranslationJob::dispatch(
                $translationId,
                $index,
                count($chunks),
                $chunk,
                $sourceLanguage,
                $targetLanguage,
                $options
            )->onQueue('tenant_isolated');
        }

        // Progress başlangıç durumu
        $this->updateProgress($translationId, 5, "İçerik {" . count($chunks) . "} chunk'a ayrıldı, çeviri başlatılıyor...");
    }

    /**
     * ⚡ Küçük İçerik - Direkt Bulk Translation
     */
    private function processBulkTranslation(
        string $translationId,
        string $html,
        string $sourceLanguage,
        string $targetLanguage,
        array $options
    ): void {
        EnhancedChunkTranslationJob::dispatch(
            $translationId,
            0,
            1,
            $html,
            $sourceLanguage,
            $targetLanguage,
            $options
        )->onQueue('tenant_isolated');

        $this->updateProgress($translationId, 10, "Bulk çeviri başlatıldı...");
    }

    /**
     * 🔧 Semantic HTML Chunking - Yapısal bütünlüğü korur
     */
    private function createSemanticChunks(string $html): array
    {
        $chunks = [];
        $currentChunk = '';
        $elementDepth = 0;
        $position = 0;
        $length = strlen($html);

        while ($position < $length) {
            // Tag başlangıcı ara
            $nextTagStart = strpos($html, '<', $position);
            
            if ($nextTagStart === false) {
                // Son kalan text'i ekle
                $currentChunk .= substr($html, $position);
                break;
            }

            // Text kısmını ekle
            $textPart = substr($html, $position, $nextTagStart - $position);
            $currentChunk .= $textPart;

            // Tag'i bul
            $nextTagEnd = strpos($html, '>', $nextTagStart);
            if ($nextTagEnd === false) {
                $currentChunk .= substr($html, $nextTagStart);
                break;
            }

            $tag = substr($html, $nextTagStart, $nextTagEnd - $nextTagStart + 1);
            $currentChunk .= $tag;

            // Depth tracking (opening/closing tags)
            if (preg_match('/^<\//', $tag)) {
                $elementDepth--;
            } elseif (!preg_match('/\/>$/', $tag) && !preg_match('/^<(br|hr|img|input|meta|link)/', $tag)) {
                $elementDepth++;
            }

            // Chunk size kontrolü ve semantic break point
            if (strlen($currentChunk) > self::MAX_CHUNK_SIZE && $elementDepth === 0) {
                // Güvenli kesim noktası - eleman kapalı
                $chunks[] = $currentChunk;
                $currentChunk = '';
            }

            $position = $nextTagEnd + 1;
        }

        // Son chunk'ı ekle
        if (!empty(trim($currentChunk))) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    /**
     * 🎨 A1 Style Bulk HTML Translation - Çekirdek algoritma
     */
    public function translateHtmlContentBulk(
        string $html,
        string $fromLang,
        string $toLang,
        string $context = ''
    ): string {
        Log::info('🎯 Bulk HTML Translation başlıyor', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang
        ]);

        try {
            // 1. HTML'den sadece text'leri çıkar (A1 algoritması)
            $textMatches = [];
            $pattern = '/>([\\s\\S]*?)</';
            preg_match_all($pattern, $html, $textMatches, PREG_OFFSET_CAPTURE);
            
            $textsToTranslate = [];
            $placeholders = [];
            $counter = 0;
            
            foreach ($textMatches[1] as $match) {
                $text = trim($match[0]);
                
                // Boş, kısa veya sadece sembol olan text'leri atla
                if (strlen($text) < 3 || 
                    preg_match('/^[\\s\\d\\-\\.\\,\\+\\*\\/\\=\\(\\)]+$/', $text) ||
                    preg_match('/^[^\\p{L}]+$/u', $text)) {
                    continue;
                }
                
                $textsToTranslate[] = $text;
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
            
            // 2. Tüm text'leri birleştir ve tek seferde çevir
            $combinedText = implode(self::SEPARATOR, $textsToTranslate);
            
            // Enhanced translation context
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
1. Each text segment is separated by '---TRANSLATE-SEPARATOR---'
2. Translate EVERY segment to {$targetLanguageName}
3. Keep exact same number of segments
4. Use professional business tone in {$targetLanguageName}
5. NO English unless target language IS English
6. NO fallback to common languages
7. COMPLETE TRANSLATION - NO {$fromLang} words should remain

🎯 TARGET LANGUAGE FOCUS:
- You MUST write in: {$targetLanguageName}
- Language code: {$toLang}
- Write naturally in {$targetLanguageName}
- Use {$targetLanguageName} grammar and structure
- Think in {$targetLanguageName}, not English

VERIFICATION: Before responding, confirm your output is 100% {$targetLanguageName}.

Content to translate:";

            $translatedCombined = $this->aiService->translateText(
                $combinedText,
                $fromLang,
                $toLang,
                [
                    'context' => $bulkContext,
                    'preserve_html' => false,
                    'enhanced_mode' => true
                ]
            );
            
            // 3. Çevrilen text'leri ayır
            $translatedTexts = explode(self::SEPARATOR, $translatedCombined);
            
            Log::info('🔍 Translation parsing result', [
                'expected_texts' => count($textsToTranslate),
                'received_texts' => count($translatedTexts),
                'sample_translations' => array_slice($translatedTexts, 0, 3)
            ]);
            
            // Eğer ayrılan text sayısı uymuyor ise fallback
            if (count($translatedTexts) !== count($textsToTranslate)) {
                Log::warning('⚠️ Text count mismatch - using fallback approach', [
                    'expected' => count($textsToTranslate),
                    'received' => count($translatedTexts)
                ]);
                
                return $this->fallbackIndividualTranslation($html, $fromLang, $toLang, $context);
            }
            
            // 4. HTML'de text'leri çevrilenleriyle değiştir (A1 exact matching)
            $translatedHtml = $html;
            
            foreach ($textsToTranslate as $index => $originalText) {
                $translatedText = trim($translatedTexts[$index] ?? $originalText);
                
                // Enhanced replacement with exact whitespace preservation
                $originalTextEscaped = preg_quote($originalText, '/');
                $pattern = '/>(\\s*)' . $originalTextEscaped . '(\\s*)</';
                $replacement = '>$1' . $translatedText . '$2<';
                
                $replacementCount = 0;
                $translatedHtml = preg_replace($pattern, $replacement, $translatedHtml, 1, $replacementCount);
                
                Log::info('🔄 Text replacement', [
                    'index' => $index,
                    'original' => substr($originalText, 0, 50),
                    'translated' => substr($translatedText, 0, 50),
                    'replaced' => $replacementCount > 0
                ]);
            }
            
            Log::info('✅ Bulk HTML translation completed', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'texts_translated' => count($translatedTexts)
            ]);
            
            return $translatedHtml;
            
        } catch (\Exception $e) {
            Log::error('❌ Bulk HTML translation failed', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html)
            ]);
            
            // Fallback: Individual translation
            return $this->fallbackIndividualTranslation($html, $fromLang, $toLang, $context);
        }
    }

    /**
     * 🔄 Fallback - Individual Text Translation (A1 HtmlTranslationService approach)
     */
    private function fallbackIndividualTranslation(
        string $html, 
        string $fromLang, 
        string $toLang, 
        string $context
    ): string {
        Log::info('🐌 Fallback: Individual translation mode started');
        
        try {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $originalErrorSetting = libxml_use_internal_errors(true);
            
            $htmlWithMeta = '<meta charset="UTF-8">' . $html;
            $dom->loadHTML($htmlWithMeta, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            $xpath = new \DOMXPath($dom);
            $textNodes = $xpath->query('//text()[normalize-space()]');
            
            foreach ($textNodes as $textNode) {
                $originalText = trim($textNode->nodeValue);
                
                if (strlen($originalText) < 3 || 
                    preg_match('/^[\\d\\s\\-\\.\\,\\+\\*\\/\\=\\(\\)]+$/', $originalText)) {
                    continue;
                }
                
                $translatedText = $this->aiService->translateText(
                    $originalText,
                    $fromLang,
                    $toLang,
                    ['context' => 'html_text_node', 'preserve_html' => false]
                );
                
                $textNode->nodeValue = $translatedText;
            }
            
            $translatedHtml = $dom->saveHTML();
            $translatedHtml = preg_replace('/<meta charset="UTF-8">/', '', $translatedHtml);
            
            libxml_use_internal_errors($originalErrorSetting);
            
            return trim($translatedHtml);
            
        } catch (\Exception $e) {
            Log::error('❌ Fallback translation also failed', ['error' => $e->getMessage()]);
            return $html; // Son çare: orijinal HTML döndür
        }
    }

    /**
     * Progress tracking methods
     */
    private function generateTranslationId(string $entityType, int $entityId, string $field, string $targetLanguage): string
    {
        return "enhanced_" . md5("{$entityType}_{$entityId}_{$field}_{$targetLanguage}_" . time());
    }

    private function initializeProgressTracking(string $translationId): void
    {
        $progressData = [
            'translation_id' => $translationId,
            'status' => 'initializing',
            'progress_percentage' => 0,
            'message' => 'Çeviri başlatılıyor...',
            'started_at' => now(),
            'chunks_completed' => 0,
            'total_chunks' => 0
        ];

        Cache::put("enhanced_translation_{$translationId}", $progressData, now()->addHours(2));
        session()->put("translation_progress_{$translationId}", $progressData);
    }

    public function updateProgress(string $translationId, float $percentage, string $message): void
    {
        $progressData = Cache::get("enhanced_translation_{$translationId}", []);
        $progressData['progress_percentage'] = round($percentage, 2);
        $progressData['message'] = $message;
        $progressData['updated_at'] = now();

        Cache::put("enhanced_translation_{$translationId}", $progressData, now()->addHours(2));
        session()->put("translation_progress_{$translationId}", $progressData);
    }

    public function getProgress(string $translationId): array
    {
        return Cache::get("enhanced_translation_{$translationId}", [
            'status' => 'not_found',
            'progress_percentage' => 0,
            'message' => 'Çeviri bulunamadı'
        ]);
    }

    // Helper methods from A1
    private function getLanguageNativeName(string $langCode): string
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

    private function getForbiddenLanguages(string $targetLang, string $targetLanguageName): string
    {
        $commonLanguages = [
            'en' => 'English', 
            'es' => 'Español', 
            'fr' => 'Français', 
            'de' => 'Deutsch', 
            'bg' => 'Български', 
            'tr' => 'Türkçe'
        ];
        
        $forbidden = collect($commonLanguages)
            ->reject(fn($name, $code) => $code === $targetLang)
            ->values()
            ->join(', ');
            
        return $forbidden ?: "any other language except {$targetLanguageName}";
    }
}