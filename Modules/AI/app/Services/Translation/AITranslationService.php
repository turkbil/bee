<?php

namespace Modules\AI\App\Services\Translation;

use Modules\AI\App\Services\UltraAssertiveTranslationPrompt;
use Modules\AI\App\Services\FastHtmlTranslationService;
use Modules\AI\App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Log;

/**
 * 🌐 AI TRANSLATION SERVICE - Ultra Assertive System
 * 
 * Sadece çeviri işlemlerine odaklanmış temiz servis.
 * Artık "I'm sorry, I can't assist" yok!
 */
class AITranslationService
{
    protected $aiService;
    
    public function __construct($aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🔥 ULTRA ASSERTIVE TRANSLATION - Zero Refusal System
     */
    public function translateText(string $text, string $fromLang, string $toLang, array $options = []): string
    {
        $context = $options['context'] ?? 'general';
        $preserveHtml = $options['preserve_html'] ?? false;
        
        // Fast HTML translation için özel route
        if (isset($options['use_fast_html']) && $options['use_fast_html']) {
            $fastTranslator = new FastHtmlTranslationService($this->aiService);
            return $fastTranslator->translateHtmlContentFast($text, $fromLang, $toLang, $context, $options);
        }

        // 🔥 ULTRA ASSERTIVE PROMPT SİSTEMİ - Zero refusal tolerance
        $prompt = UltraAssertiveTranslationPrompt::buildPrompt($text, $fromLang, $toLang, $context, $preserveHtml);
        
        Log::info('🔥 Ultra Assertive Translation başlatılıyor', [
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
                'title' => "🔥 Ultra Translation: {$fromLang} → {$toLang}",
                'type' => 'ultra_translation',
                'feature_name' => 'ultra_assertive_translate',
                'is_demo' => false,
                'prompt_id' => 1,
                'metadata' => [
                    'source' => 'ultra_assertive_translation_system',
                    'text_length' => strlen($text),
                    'estimated_tokens' => ceil(strlen($text) / 4),
                    'assertive_mode' => true
                ]
            ];

            $response = $this->aiService->processRequest(
                $prompt, 
                4000, // maxTokens - Yüksek limit
                0.2,  // temperature - Düşük = tutarlı
                null, // model - use default
                null, // systemPrompt
                $conversationData,
                [
                    'context_type' => 'ultra_translation', // Bypass filtering
                    'bypass_all_filters' => true
                ]
            );

            Log::info('🎯 Ultra Translation response alındı', [
                'success' => $response['success'],
                'has_content' => isset($response['data']['content']),
                'content_length' => isset($response['data']['content']) ? strlen($response['data']['content']) : 0
            ]);

            if ($response['success']) {
                $translatedText = $response['data']['content'];
                
                if (empty(trim($translatedText))) {
                    Log::error('❌ Çeviri boş geldi!', [
                        'response' => $response,
                        'original_text' => substr($text, 0, 200)
                    ]);
                    return $text; // Fallback to original
                }

                Log::info('✅ Ultra Translation başarılı', [
                    'original_length' => strlen($text),
                    'translated_length' => strlen($translatedText),
                    'from_lang' => $fromLang,
                    'to_lang' => $toLang
                ]);
                
                return trim($translatedText);
            }

            Log::error('❌ Ultra Translation başarısız', [
                'response' => $response,
                'text_preview' => substr($text, 0, 200)
            ]);
            
            return $text; // Fallback
            
        } catch (\Exception $e) {
            Log::error('🚨 Ultra Translation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'text_preview' => substr($text, 0, 200)
            ]);
            
            return $text; // Fallback
        }
    }

    /**
     * 🎯 CHALLENGING CONTENT TRANSLATION
     * 
     * Emoji, argo, özel karakterler için özel assertive prompt
     */
    public function translateChallengingContent(string $text, string $fromLang, string $toLang): string
    {
        $prompt = UltraAssertiveTranslationPrompt::buildChallengingContentPrompt($text, $fromLang, $toLang);
        
        $conversationData = [
            'tenant_id' => TenantHelpers::getTenantId(),
            'user_id' => auth()->id() ?? 1,
            'session_id' => 'challenging_translation_' . uniqid(),
            'title' => "🔥 Challenge Mode: {$fromLang} → {$toLang}",
            'type' => 'challenging_translation',
            'feature_name' => 'challenging_translate',
            'metadata' => [
                'source' => 'challenging_content_system',
                'content_type' => 'challenging',
                'override_safety' => true
            ]
        ];

        try {
            $response = $this->aiService->processRequest(
                $prompt, 
                4000,
                0.3, // Slightly higher for creativity
                null,
                null,
                $conversationData,
                [
                    'context_type' => 'challenging_translation',
                    'bypass_all_filters' => true,
                    'emergency_mode' => true
                ]
            );

            if ($response['success'] && !empty(trim($response['data']['content']))) {
                return trim($response['data']['content']);
            }
            
            return $text; // Fallback
            
        } catch (\Exception $e) {
            Log::error('🚨 Challenging translation exception', [
                'error' => $e->getMessage(),
                'text_preview' => substr($text, 0, 100)
            ]);
            
            return $text;
        }
    }

    /**
     * 🚀 BULK TRANSLATION - Çoklu metin çevirisi
     */
    public function translateBulk(array $texts, string $fromLang, string $toLang, array $options = []): array
    {
        $results = [];
        
        foreach ($texts as $key => $text) {
            if (empty(trim($text))) {
                $results[$key] = $text;
                continue;
            }
            
            $results[$key] = $this->translateText($text, $fromLang, $toLang, $options);
        }
        
        return $results;
    }
}