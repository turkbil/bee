<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

/**
 * Conversation Tracker - claude_ai.md uyumlu
 * Her AI kullanımında otomatik conversation kaydı
 */
class ConversationTracker
{    
    /**
     * Genel conversation kaydet (HER AI KULLANIMI İÇİN)
     */
    public static function saveConversation(
        string $prompt,
        string $response,
        string $feature = 'ai_feature',
        array $metadata = [],
        string $status = 'completed'
    ): void {
        try {
            DB::table('ai_conversations')->insert([
                'tenant_id' => TenantHelpers::getTenantId(),
                'user_id' => self::getUserId(), // Safe user ID retrieval
                'session_id' => $feature . '_' . uniqid(),
                'title' => self::generateTitle($feature, $prompt),
                'type' => $feature,
                'feature_name' => $feature,
                'is_demo' => false,
                'prompt_id' => 1, // Default prompt ID
                'total_tokens_used' => $metadata['total_tokens'] ?? ($metadata['input_tokens'] ?? 0) + ($metadata['output_tokens'] ?? 0),
                'metadata' => json_encode([
                    'input_data' => [
                        'prompt' => substr($prompt, 0, 1000),
                        'prompt_length' => strlen($prompt),
                        'provider' => $metadata['provider'] ?? 'unknown',
                        'model' => $metadata['model'] ?? 'unknown',
                        'input_tokens' => $metadata['input_tokens'] ?? 0,
                        'output_tokens' => $metadata['output_tokens'] ?? 0,
                        'system_prompt' => $metadata['system_prompt'] ?? null,
                        'error' => $metadata['error'] ?? false
                    ],
                    'output_data' => [
                        'response' => substr($response, 0, 1000),
                        'response_length' => strlen($response),
                        'credits_used' => $metadata['credits_used'] ?? 0,
                        'processing_time' => $metadata['processing_time'] ?? null
                    ],
                    'full_metadata' => $metadata
                ]) ?: '{}', // JSON encode fail durumunda fallback
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('📊 AI Conversation kaydedildi', [
                'feature' => $feature,
                'tenant_id' => TenantHelpers::getTenantId(),
                'tokens' => $metadata['total_tokens'] ?? 0,
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ ConversationTracker::saveConversation error', [
                'feature' => $feature,
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt)
            ]);
        }
    }
    
    /**
     * Safe user ID retrieval - CLI ve web context için güvenli
     */
    private static function getUserId(): int
    {
        try {
            // Web context - normal auth
            if (auth()->check()) {
                return auth()->id();
            }
            
            // CLI context - default user
            if (app()->runningInConsole()) {
                return 1; // Default admin user
            }
            
            // Fallback - guest
            return 1;
            
        } catch (\Exception $e) {
            Log::debug('getUserId fallback to 1', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    /**
     * Title oluşturucu
     */
    private static function generateTitle(string $feature, string $prompt): string
    {
        $titles = [
            'ai_translate' => 'AI Translation',
            'ai_feature' => 'AI Feature',
            'ai_chat' => 'AI Chat',
            'content_generation' => 'Content Generation',
            'seo_analysis' => 'SEO Analysis'
        ];
        
        $baseTitle = $titles[$feature] ?? ucfirst(str_replace('_', ' ', $feature));
        $shortPrompt = substr($prompt, 0, 50);
        
        return $baseTitle . ': ' . $shortPrompt;
    }

    /**
     * Translation conversation kaydet
     */
    public static function saveTranslation(
        string $text,
        string $fromLang,
        string $toLang,
        string $translatedText,
        array $response,
        string $context = 'general',
        bool $preserveHtml = false
    ): void {
        try {
            DB::table('ai_conversations')->insert([
                'tenant_id' => TenantHelpers::getTenantId(),
                'user_id' => self::getUserId(), // Safe user ID retrieval
                'session_id' => 'translation_' . uniqid(),
                'title' => "Translation: {$fromLang} → {$toLang}",
                'type' => 'translation',
                'feature_name' => 'ai_translate',
                'is_demo' => false,
                'prompt_id' => 1, // Default prompt ID
                'total_tokens_used' => $response['tokens_used'] ?? 0,
                'metadata' => json_encode([
                    'input_data' => [
                        'text' => substr($text, 0, 500),
                        'from_language' => $fromLang,
                        'to_language' => $toLang,
                        'context' => $context,
                        'preserve_html' => $preserveHtml,
                        'text_length' => strlen($text)
                    ],
                    'output_data' => [
                        'translated_text' => substr($translatedText, 0, 500),
                        'original_length' => strlen($text),
                        'translated_length' => strlen($translatedText),
                        'success' => true
                    ],
                    'provider_used' => app(\Modules\AI\App\Services\AIProviderManager::class)->getActiveProviders()->first()->name ?? 'unknown',
                    'model_used' => $response['model'] ?? 'unknown',
                    'processing_time' => $response['processing_time'] ?? 0
                ]) ?: '{}', // JSON encode fail durumunda fallback
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('📊 Translation conversation kaydedildi', [
                'type' => 'translation',
                'tenant_id' => TenantHelpers::getTenantId(),
                'from_lang' => $fromLang,
                'to_lang' => $toLang,
                'tokens' => $response['tokens_used'] ?? 0,
                'text_length' => strlen($text)
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Translation conversation kayıt hatası', [
                'error' => $e->getMessage(),
                'from_lang' => $fromLang,
                'to_lang' => $toLang
            ]);
            // Hata olsa bile çeviri çalışmaya devam etsin
        }
    }
}