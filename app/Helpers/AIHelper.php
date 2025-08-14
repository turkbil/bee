<?php

// AI Helper Functions - Sıfırlanmış Versiyon
// Tüm eski feature helper fonksiyonları kaldırıldı
// Sadece temel fonksiyonlar korundu

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\AIFeature;
use App\Helpers\TenantHelpers;

if (!function_exists('ai_get_settings')) {
    function ai_get_settings(): array
    {
        return [
            'enabled' => config('ai.enabled', true),
            'model' => config('ai.model', 'deepseek-chat'),
            'max_tokens' => config('ai.max_tokens', 4000),
            'temperature' => config('ai.temperature', 0.7)
        ];
    }
}

if (!function_exists('ai_get_api_key')) {
    function ai_get_api_key(): ?string
    {
        try {
            // ai_providers tablosundan aktif provider'ın API key'ini çek
            $activeProvider = DB::table('ai_providers')
                ->where('is_active', true)
                ->where('is_default', true)
                ->first();
                
            if ($activeProvider && !empty($activeProvider->api_key)) {
                return $activeProvider->api_key;
            }
            
            // Fallback: herhangi bir aktif provider
            $anyActiveProvider = DB::table('ai_providers')
                ->where('is_active', true)
                ->whereNotNull('api_key')
                ->first();
                
            if ($anyActiveProvider) {
                return $anyActiveProvider->api_key;
            }
            
            // Son fallback: config'den
            return config('ai.deepseek.api_key');
        } catch (\Exception $e) {
            Log::warning('ai_get_api_key() error: ' . $e->getMessage());
            return config('ai.deepseek.api_key');
        }
    }
}

if (!function_exists('ai_get_model')) {
    function ai_get_model(): string
    {
        try {
            // ai_providers tablosundan aktif provider'ın model'ini çek
            $activeProvider = DB::table('ai_providers')
                ->where('is_active', true)
                ->where('is_default', true)
                ->first();
                
            if ($activeProvider && !empty($activeProvider->default_model)) {
                return $activeProvider->default_model;
            }
            
            // Fallback: herhangi bir aktif provider'ın modeli
            $anyActiveProvider = DB::table('ai_providers')
                ->where('is_active', true)
                ->whereNotNull('default_model')
                ->first();
                
            if ($anyActiveProvider) {
                return $anyActiveProvider->default_model;
            }
            
            // Son fallback: config'den
            return config('ai.model', 'deepseek-chat');
        } catch (\Exception $e) {
            Log::warning('ai_get_model() error: ' . $e->getMessage());
            return config('ai.model', 'deepseek-chat');
        }
    }
}

if (!function_exists('ai_is_enabled')) {
    function ai_is_enabled(): bool
    {
        return config('ai.enabled', true);
    }
}

if (!function_exists('resolve_tenant_id')) {
    function resolve_tenant_id(): ?string
    {
        if (function_exists('tenant') && tenant()) {
            return tenant('id');
        }
        
        return TenantHelpers::isCentral() ? 'central' : 'default';
    }
}

// Temel AI işlevselliği için minimal helper
if (!function_exists('ai_ask')) {
    function ai_ask(string $message, array $options = []): string
    {
        try {
            $aiService = app(AIService::class);
            return $aiService->ask($message, $options);
        } catch (Exception $e) {
            Log::error('AI Helper ask error: ' . $e->getMessage());
            return 'AI servisi şu anda kullanılamıyor.';
        }
    }
}

if (!function_exists('ai_analyze_question')) {
    function ai_analyze_question(string $question): array
    {
        // Basit soru analizi - kategori ve tür belirleme
        $question = strtolower(trim($question));
        
        $analysis = [
            'category' => 'general',
            'type' => 'question',
            'complexity' => 'simple',
            'keywords' => []
        ];
        
        // Kategori belirleme
        if (str_contains($question, 'seo') || str_contains($question, 'optimizasyon')) {
            $analysis['category'] = 'seo';
        } elseif (str_contains($question, 'çeviri') || str_contains($question, 'translate')) {
            $analysis['category'] = 'translation';
        } elseif (str_contains($question, 'içerik') || str_contains($question, 'yazı')) {
            $analysis['category'] = 'content';
        } elseif (str_contains($question, 'email') || str_contains($question, 'mail')) {
            $analysis['category'] = 'email';
        } elseif (str_contains($question, 'sosyal medya') || str_contains($question, 'social media')) {
            $analysis['category'] = 'social_media';
        }
        
        // Anahtar kelimeleri çıkar
        $keywords = array_filter(explode(' ', $question), function($word) {
            return strlen($word) > 3;
        });
        $analysis['keywords'] = array_slice($keywords, 0, 5);
        
        return $analysis;
    }
}

// UNIVERSAL AI FEATURE HELPER - Database-driven Dynamic System
if (!function_exists('ai_feature')) {
    /**
     * Universal AI Feature Helper - Database'den dinamik çalışır
     * Tüm AI feature'ları için tek helper function
     * 
     * @param string $featureSlug Feature slug (database'deki slug)
     * @param string $input Kullanıcı input'u
     * @param array $options Ek seçenekler
     * @return string AI response
     */
    function ai_feature(string $featureSlug, string $input, array $options = []): string
    {
        try {
            // Feature'ı database'den slug ile bul
            $feature = \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)
                ->where('status', 'active')
                ->first();
            
            if (!$feature) {
                throw new \Exception("AI Feature '{$featureSlug}' bulunamadı veya aktif değil.");
            }
            
            // AI Service'ini kullanarak feature'ı çalıştır
            $aiService = app(\Modules\AI\App\Services\AIService::class);
            
            $result = $aiService->processFeature($feature, $input, $options);
            
            return $result['response'] ?? 'AI yanıtı alınamadı.';
            
        } catch (\Exception $e) {
            \Log::error("AI Feature Helper Error: {$featureSlug}", [
                'input' => $input,
                'options' => $options,
                'error' => $e->getMessage()
            ]);
            
            return "AI işlemi başarısız: " . $e->getMessage();
        }
    }
}

// CATEGORY-BASED AI HELPERS - Database-driven
if (!function_exists('ai_seo_tools')) {
    /**
     * SEO kategorisindeki AI feature'ları için helper
     */
    function ai_seo_tools(string $feature, string $input, array $options = []): string
    {
        // SEO kategorisinden feature bul ve çalıştır
        $fullFeature = \Modules\AI\App\Models\AIFeature::whereHas('category', function($query) {
            $query->where('slug', 'seo-tools');
        })->where('slug', $feature)->first();
        
        if (!$fullFeature) {
            return ai_feature($feature, $input, $options); // Fallback
        }
        
        return ai_feature($fullFeature->slug, $input, $options);
    }
}

if (!function_exists('ai_smart_execute')) {
    /**
     * Smart AI execution - analyzing question and routing to appropriate feature
     */
    function ai_smart_execute(string $userQuestion, array $options = []): array
    {
        try {
            // Simple fallback: use generic AI service
            $aiService = app(AIService::class);
            $response = $aiService->askStream($userQuestion, $options);
            
            return [
                'success' => true,
                'content' => $response ?? '',
                'method' => 'ai_smart_execute_fallback'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'content' => ''
            ];
        }
    }
}

if (!function_exists('ai_content_creation')) {
    /**
     * Content Creation kategorisindeki AI feature'ları için helper
     */
    function ai_content_creation(string $feature, string $input, array $options = []): string
    {
        $fullFeature = \Modules\AI\App\Models\AIFeature::whereHas('category', function($query) {
            $query->where('slug', 'content-creation');
        })->where('slug', $feature)->first();
        
        if (!$fullFeature) {
            return ai_feature($feature, $input, $options);
        }
        
        return ai_feature($fullFeature->slug, $input, $options);
    }
}

if (!function_exists('ai_smart_execute')) {
    /**
     * Smart AI execution - analyzing question and routing to appropriate feature
     */
    function ai_smart_execute(string $userQuestion, array $options = []): array
    {
        try {
            // Simple fallback: use generic AI service
            $aiService = app(AIService::class);
            $response = $aiService->askStream($userQuestion, $options);
            
            return [
                'success' => true,
                'content' => $response ?? '',
                'method' => 'ai_smart_execute_fallback'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'content' => ''
            ];
        }
    }
}

if (!function_exists('ai_translation_tools')) {
    /**
     * Translation kategorisindeki AI feature'ları için helper
     */
    function ai_translation_tools(string $feature, string $input, array $options = []): string
    {
        $fullFeature = \Modules\AI\App\Models\AIFeature::whereHas('category', function($query) {
            $query->where('slug', 'translation-tools');
        })->where('slug', $feature)->first();
        
        if (!$fullFeature) {
            return ai_feature($feature, $input, $options);
        }
        
        return ai_feature($fullFeature->slug, $input, $options);
    }
}

if (!function_exists('ai_smart_execute')) {
    /**
     * Smart AI execution - analyzing question and routing to appropriate feature
     */
    function ai_smart_execute(string $userQuestion, array $options = []): array
    {
        try {
            // Simple fallback: use generic AI service
            $aiService = app(AIService::class);
            $response = $aiService->askStream($userQuestion, $options);
            
            return [
                'success' => true,
                'content' => $response ?? '',
                'method' => 'ai_smart_execute_fallback'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'content' => ''
            ];
        }
    }
}