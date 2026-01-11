<?php

namespace Modules\AI\App\Services\Core;

use Modules\AI\App\Services\UltraAssertiveTranslationPrompt;
use Modules\AI\App\Services\FastHtmlTranslationService;
use Modules\AI\App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Log;

/**
 * 🌐 AI TRANSLATION SERVICE - Çeviri işlemlerine odaklanmış temiz servis
 */
class AITranslationService
{
    protected $aiService;
    
    public function __construct($aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🔥 ULTRA ASSERTIVE TRANSLATION - Main translation method
     */
    public function translateText(string $text, string $fromLang, string $toLang, array $options = []): string
    {
        $context = $options['context'] ?? 'general';
        $preserveHtml = $options['preserve_html'] ?? false;
        
        Log::info('🌐 translateText BAŞLADI', [
            'text_length' => strlen($text),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'context' => $context
        ]);

        // Boş metin kontrolü
        if (empty(trim($text))) {
            Log::warning('⚠️ Boş metin çeviri isteği');
            return $text;
        }

        // Aynı dil kontrolü
        if ($fromLang === $toLang) {
            Log::info('✅ Aynı dil - çeviri gerekmiyor');
            return $text;
        }

        // Fast HTML translation için özel route
        if (isset($options['use_fast_html']) && $options['use_fast_html']) {
            $fastTranslator = new FastHtmlTranslationService($this->aiService);
            return $fastTranslator->translateHtmlContentFast($text, $fromLang, $toLang, $context, $options);
        }

        // 🔥 ULTRA ASSERTIVE PROMPT SİSTEMİ - Zero refusal tolerance
        $prompt = UltraAssertiveTranslationPrompt::buildPrompt($text, $fromLang, $toLang, $context, $preserveHtml);
        
        Log::info('📝 Translation prompt hazırlandı', [
            'prompt_length' => strlen($prompt),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'context' => $context
        ]);

        try {
            // Conversation data
            $conversationData = [
                'tenant_id' => TenantHelpers::getTenantId(),
                'user_id' => auth()->id() ?? 1,
                'session_id' => 'translation_' . uniqid(),
                'title' => "Translation: {$fromLang} → {$toLang}",
                'type' => 'translation',
                'feature_name' => 'ai_translate',
                'is_demo' => false,
                'prompt_id' => 1,
                'metadata' => [
                    'source' => 'translation_system',
                    'text_length' => strlen($text),
                    'estimated_tokens' => ceil(strlen($text) / 4)
                ]
            ];

            $response = $this->aiService->processRequest(
                $prompt, 
                4000,
                0.3,
                null,
                null,
                $conversationData,
                [
                    'context_type' => 'translation',
                    'bypass_all_filters' => true
                ]
            );

            Log::info('🔍 Translation response received', [
                'success' => $response['success'],
                'has_content' => isset($response['data']['content']),
                'content_length' => isset($response['data']['content']) ? strlen($response['data']['content']) : 0,
                'content_preview' => isset($response['data']['content']) ? substr($response['data']['content'], 0, 100) : 'NO CONTENT'
            ]);

            if ($response['success']) {
                $translatedText = $response['data']['content'];
                
                if (empty(trim($translatedText))) {
                    Log::error('❌ Çeviri boş geldi!', [
                        'response' => $response,
                        'original_text' => substr($text, 0, 200)
                    ]);
                    return $text; // Fallback
                }

                Log::info('✅ Translation başarılı', [
                    'original_length' => strlen($text),
                    'translated_length' => strlen($translatedText),
                    'from_lang' => $fromLang,
                    'to_lang' => $toLang
                ]);
                
                return trim($translatedText);
            }

            Log::error('❌ Translation başarısız', [
                'response' => $response,
                'text_preview' => substr($text, 0, 200)
            ]);
            
            return $text; // Fallback
            
        } catch (\Exception $e) {
            Log::error('🚨 Translation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'text_preview' => substr($text, 0, 200)
            ]);
            
            return $text; // Fallback
        }
    }

    /**
     * 🔥 UZUN HTML İÇERİK ÇEVİRİ SİSTEMİ
     */
    public function translateLongHtmlContent(string $html, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🔧 Uzun HTML chunk çeviri başlıyor', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang
        ]);

        try {
            // HTML'deki tüm text nodeları bul ve çevir
            $dom = new \DOMDocument('1.0', 'UTF-8');
            
            $originalErrorSetting = libxml_use_internal_errors(true);
            
            $htmlWithMeta = '<meta charset="UTF-8">' . $html;
            $dom->loadHTML($htmlWithMeta, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            $xpath = new \DOMXPath($dom);
            $textNodes = $xpath->query('//text()[normalize-space()]');
            
            foreach ($textNodes as $textNode) {
                $originalText = trim($textNode->nodeValue);
                
                if (strlen($originalText) < 3) {
                    continue;
                }
                
                if (preg_match('/^[\d\s\-\.\,\+\*\/\=\(\)]+$/', $originalText)) {
                    continue;
                }
                
                $translatedText = $this->translateText(
                    $originalText,
                    $fromLang,
                    $toLang,
                    [
                        'context' => 'html_text_node',
                        'preserve_html' => false
                    ]
                );
                
                $textNode->nodeValue = $translatedText;
            }
            
            $translatedHtml = $dom->saveHTML();
            $translatedHtml = preg_replace('/<meta charset="UTF-8">/', '', $translatedHtml);
            
            libxml_use_internal_errors($originalErrorSetting);
            
            Log::info('✅ Uzun HTML çeviri tamamlandı');
            return $translatedHtml;
            
        } catch (\Exception $e) {
            Log::error('❌ Uzun HTML çeviri hatası', [
                'error' => $e->getMessage(),
                'html_preview' => substr($html, 0, 200)
            ]);
            
            return $this->translateText($html, $fromLang, $toLang, ['context' => $context, 'preserve_html' => true]);
        }
    }
}