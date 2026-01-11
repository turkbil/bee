<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\FormBuilder\UniversalInputManager;
use Modules\AI\App\Services\FormBuilder\PromptMapper;
use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Cache;

/**
 * Universal Input System entegrasyonu ile AIService extension
 */
readonly class UniversalInputAIService
{
    public function __construct(
        private AIService $aiService,
        private UniversalInputManager $inputManager,
        private PromptMapper $promptMapper
    ) {}

    /**
     * Universal form input'larıyla AI çağrısı yap
     */
    public function processFormRequest(int $featureId, array $userInputs, array $options = []): array
    {
        try {
            // 1. Form validasyonu
            $validationErrors = $this->inputManager->validateInputs($userInputs, $featureId);
            if (!empty($validationErrors)) {
                return [
                    'success' => false,
                    'error_type' => 'validation',
                    'errors' => $validationErrors,
                    'message' => 'Form validation failed'
                ];
            }

            // 2. Feature bilgilerini al
            $feature = AIFeature::findOrFail($featureId);

            // 3. Dynamic prompt oluştur
            $finalPrompt = $this->promptMapper->buildFinalPrompt($featureId, $userInputs);

            // 4. Context engine ile enrichment
            $enrichedPrompt = $this->enrichPromptWithContext($finalPrompt, $feature, $userInputs, $options);

            // 5. AI service çağrısı
            $aiResponse = $this->callAIServiceWithPrompt($enrichedPrompt, $feature, $options);

            // 6. Response formatting
            return $this->formatUniversalResponse($aiResponse, $feature, $userInputs);

        } catch (\Exception $e) {
            \Log::error('UniversalInputAIService error', [
                'feature_id' => $featureId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error_type' => 'system',
                'message' => 'System error occurred while processing request',
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Context engine ile prompt enrichment
     */
    private function enrichPromptWithContext(
        string $basePrompt, 
        AIFeature $feature, 
        array $userInputs, 
        array $options
    ): string {
        $cacheKey = "enriched_prompt_" . md5($basePrompt . serialize($userInputs));
        
        return Cache::remember($cacheKey, 300, function() use ($basePrompt, $feature, $userInputs, $options) {
            $contextualPrompt = $basePrompt;

            // Tenant context ekle
            if (isset($options['tenant_context']) && $options['tenant_context']) {
                $tenantInfo = $this->getTenantContextInfo();
                $contextualPrompt = $this->injectTenantContext($contextualPrompt, $tenantInfo);
            }

            // User history context ekle
            if (isset($options['user_id'])) {
                $userContext = $this->getUserContextInfo($options['user_id'], $feature->id);
                $contextualPrompt = $this->injectUserContext($contextualPrompt, $userContext);
            }

            // Feature-specific context ekle
            $featureContext = $this->getFeatureContextInfo($feature, $userInputs);
            $contextualPrompt = $this->injectFeatureContext($contextualPrompt, $featureContext);

            return $contextualPrompt;
        });
    }

    /**
     * AI Service ile enriched prompt çağrısı
     */
    private function callAIServiceWithPrompt(string $prompt, AIFeature $feature, array $options): array
    {
        // Feature'a özel AI konfigürasyonu
        $aiConfig = $this->getFeatureAIConfig($feature, $options);

        // Token estimation
        $estimatedTokens = $this->estimateTokenUsage($prompt, $aiConfig);

        // AI service çağrısı
        return $this->aiService->processRequest(
            prompt: $prompt,
            maxTokens: $aiConfig['max_tokens'] ?? 2000,
            temperature: $aiConfig['temperature'] ?? 0.7,
            model: $aiConfig['model'] ?? null,
            systemPrompt: $aiConfig['system_prompt'] ?? null,
            metadata: [
                'feature_id' => $feature->id,
                'feature_name' => $feature->name,
                'estimated_tokens' => $estimatedTokens,
                'source' => 'universal_input_system'
            ]
        );
    }

    /**
     * Universal response formatting
     */
    private function formatUniversalResponse(array $aiResponse, AIFeature $feature, array $userInputs): array
    {
        // DEBUG: AI Response structure'ını detaylı log'la
        \Log::info('🔍 AI Response Structure Debug', [
            'ai_response_keys' => array_keys($aiResponse),
            'ai_response_success' => $aiResponse['success'] ?? 'NOT_SET',
            'ai_response_data_keys' => isset($aiResponse['data']) ? array_keys($aiResponse['data']) : 'NO_DATA_KEY',
            'ai_response_data_content' => substr($aiResponse['data']['content'] ?? 'NO_DATA_CONTENT', 0, 100),
            'ai_response_data_raw_response' => substr(json_encode($aiResponse['data']['raw_response'] ?? 'NO_RAW_RESPONSE'), 0, 200),
            'ai_response_content_direct' => substr($aiResponse['content'] ?? 'NO_CONTENT_DIRECT', 0, 100),
            'ai_response_response_direct' => substr($aiResponse['response'] ?? 'NO_RESPONSE_DIRECT', 0, 100)
        ]);
        
        if (!$aiResponse['success']) {
            return [
                'success' => false,
                'error_type' => 'ai_service',
                'message' => $aiResponse['message'] ?? 'AI service returned an error',
                'ai_error' => $aiResponse['error'] ?? null
            ];
        }

        // Feature'ın response template'ini al
        $responseTemplate = $feature->response_template ? 
            json_decode($feature->response_template, true) : null;

        // AI response'dan content'i doğru path'den al - debug'da görülen path'e göre
        $content = $aiResponse['data']['content'] ?? 
                   $aiResponse['data']['raw_response']['response'] ?? 
                   $aiResponse['data']['raw_response']['content'] ?? 
                   $aiResponse['content'] ?? 
                   $aiResponse['response'] ?? 
                   '';

        $formattedResponse = [
            'success' => true,
            'data' => [
                'content' => $content,
                'raw_response' => $aiResponse['data'],
                'feature_info' => [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'category' => $feature->category ?? 'general'
                ],
                'metadata' => [
                    'tokens_used' => $aiResponse['tokens_used'] ?? 0,
                    'model_used' => $aiResponse['model'] ?? 'unknown',
                    'processing_time' => $aiResponse['processing_time'] ?? 0,
                    'request_id' => $aiResponse['request_id'] ?? null
                ]
            ]
        ];

        // Template'a göre response formatlama
        if ($responseTemplate) {
            $formattedResponse['data'] = $this->applyResponseTemplate(
                $formattedResponse['data'], 
                $responseTemplate, 
                $userInputs
            );
        }

        return $formattedResponse;
    }

    /**
     * Response template uygula
     */
    private function applyResponseTemplate(array $data, array $template, array $userInputs): array
    {
        if (isset($template['format'])) {
            switch ($template['format']) {
                case 'structured':
                    return $this->formatStructuredResponse($data, $template, $userInputs);
                case 'sections':
                    return $this->formatSectionedResponse($data, $template, $userInputs);
                case 'json':
                    return $this->formatJSONResponse($data, $template, $userInputs);
                default:
                    return $data;
            }
        }

        return $data;
    }

    /**
     * Structured response formatting
     */
    private function formatStructuredResponse(array $data, array $template, array $userInputs): array
    {
        $structured = $data;
        $content = $data['content'] ?? '';

        if (isset($template['sections'])) {
            $structured['sections'] = [];
            
            foreach ($template['sections'] as $sectionKey => $sectionName) {
                // AI response'dan section'ları parse et
                $sectionContent = $this->extractSectionFromContent($content, $sectionName);
                $structured['sections'][$sectionKey] = [
                    'title' => $sectionName,
                    'content' => $sectionContent
                ];
            }
        }

        if (isset($template['show_original']) && $template['show_original']) {
            $structured['original_input'] = $userInputs;
        }

        return $structured;
    }

    /**
     * Feature AI konfigürasyonunu al
     */
    private function getFeatureAIConfig(AIFeature $feature, array $options): array
    {
        $config = [
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'model' => null,
            'system_prompt' => null
        ];

        // Feature'a özel konfigürasyon varsa uygula
        if ($feature->ai_config) {
            $featureConfig = is_string($feature->ai_config) ? 
                json_decode($feature->ai_config, true) : $feature->ai_config;
            
            if ($featureConfig) {
                $config = array_merge($config, $featureConfig);
            }
        }

        // Options'dan gelen override'ları uygula
        if (isset($options['ai_config'])) {
            $config = array_merge($config, $options['ai_config']);
        }

        return $config;
    }

    /**
     * Token kullanımını estimate et
     */
    private function estimateTokenUsage(string $prompt, array $config): int
    {
        // Basit token estimation (1 token ≈ 4 karakter)
        $promptTokens = (int) ceil(strlen($prompt) / 4);
        $maxResponseTokens = $config['max_tokens'] ?? 2000;
        
        return $promptTokens + $maxResponseTokens;
    }

    /**
     * Tenant context bilgilerini al
     */
    private function getTenantContextInfo(): array
    {
        return Cache::remember('tenant_context_info', 3600, function() {
            return [
                'tenant_id' => tenant('id'),
                'domain' => request()->getHost(),
                'language' => app()->getLocale(),
                'timezone' => config('app.timezone')
            ];
        });
    }

    /**
     * User context bilgilerini al
     */
    private function getUserContextInfo(int $userId, int $featureId): array
    {
        $cacheKey = "user_context_{$userId}_{$featureId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($userId, $featureId) {
            // User'ın bu feature ile olan geçmiş etkileşimlerini al
            return [
                'user_id' => $userId,
                'preferred_style' => 'professional', // Database'den al
                'previous_requests' => [], // Son N request'i al
                'success_rate' => 1.0 // Bu feature ile success rate
            ];
        });
    }

    /**
     * Feature context bilgilerini al
     */
    private function getFeatureContextInfo(AIFeature $feature, array $userInputs): array
    {
        return [
            'feature_category' => $feature->category ?? 'general',
            'complexity_level' => $this->calculateInputComplexity($userInputs),
            'feature_usage_stats' => $this->getFeatureUsageStats($feature->id)
        ];
    }

    /**
     * Input complexity hesapla
     */
    private function calculateInputComplexity(array $userInputs): string
    {
        $flatInputs = [];
        array_walk_recursive($userInputs, function($value) use (&$flatInputs) {
            if (is_string($value) || is_numeric($value)) {
                $flatInputs[] = (string) $value;
            }
        });
        
        $totalLength = array_sum(array_map('strlen', $flatInputs));
        
        if ($totalLength < 100) return 'simple';
        if ($totalLength < 500) return 'medium';
        return 'complex';
    }

    /**
     * Feature usage istatistiklerini al
     */
    private function getFeatureUsageStats(int $featureId): array
    {
        $cacheKey = "feature_usage_stats_{$featureId}";
        
        return Cache::remember($cacheKey, 3600, function() use ($featureId) {
            return [
                'total_uses' => 0, // Database'den al
                'success_rate' => 1.0,
                'avg_processing_time' => 2.5
            ];
        });
    }

    /**
     * Content'dan section extract et
     */
    private function extractSectionFromContent(string $content, string $sectionName): string
    {
        // Basit section parsing - gerçek implementasyon daha sophisticated olmalı
        $lines = explode("\n", $content);
        $inSection = false;
        $sectionContent = [];
        
        foreach ($lines as $line) {
            if (stripos($line, $sectionName) !== false) {
                $inSection = true;
                continue;
            }
            
            if ($inSection) {
                if (preg_match('/^#{1,3}\s/', $line)) {
                    // Yeni section başladı
                    break;
                }
                $sectionContent[] = $line;
            }
        }
        
        return implode("\n", $sectionContent);
    }

    /**
     * Context injection method'ları
     */
    private function injectTenantContext(string $prompt, array $tenantInfo): string
    {
        $contextPrefix = "Context: You are working for {$tenantInfo['domain']} in {$tenantInfo['language']} language.\n\n";
        return $contextPrefix . $prompt;
    }

    private function injectUserContext(string $prompt, array $userContext): string
    {
        if ($userContext['preferred_style']) {
            $styleNote = "User prefers {$userContext['preferred_style']} style responses.\n\n";
            return $styleNote . $prompt;
        }
        return $prompt;
    }

    private function injectFeatureContext(string $prompt, array $featureContext): string
    {
        $complexity = $featureContext['complexity_level'];
        $contextNote = "Complexity level: {$complexity}. Adjust response detail accordingly.\n\n";
        return $contextNote . $prompt;
    }

    // Diğer formatting method'ları
    private function formatSectionedResponse(array $data, array $template, array $userInputs): array
    {
        // Sectioned response implementation
        return $data;
    }

    private function formatJSONResponse(array $data, array $template, array $userInputs): array
    {
        // JSON response implementation
        return $data;
    }

    /**
     * 🔧 TranslatePageJob için backward compatibility
     * Legacy processFeature metodu - yeni sistemde processFormRequest kullan
     */
    public function processFeature(int $featureId, string $prompt, array $options = []): array
    {
        try {
            // Legacy format'ı yeni sisteme uyarla
            $userInputs = [
                'content' => $prompt,
                'source_language' => $options['source_language'] ?? 'tr',
                'target_language' => $options['target_language'] ?? 'en',
                'quality' => $options['quality'] ?? 'balanced'
            ];

            // Yeni universal sistem ile çağır
            $result = $this->processFormRequest($featureId, $userInputs, $options);

            // DEBUG: ProcessFormRequest result'ını log'la
            \Log::info('🔍 ProcessFormRequest Result Debug', [
                'feature_id' => $featureId,
                'result_keys' => array_keys($result),
                'result_success' => $result['success'] ?? 'NOT_SET',
                'result_content' => substr($result['content'] ?? 'NO_CONTENT', 0, 100),
                'result_response' => substr($result['response'] ?? 'NO_RESPONSE', 0, 100),
                'result_ai_response' => substr($result['ai_response'] ?? 'NO_AI_RESPONSE', 0, 100)
            ]);

            // Legacy format'a çevir
            if ($result['success'] ?? false) {
                return [
                    'success' => true,
                    'response' => $result['data']['content'] ?? '',
                    'tokens_used' => $result['data']['metadata']['tokens_used'] ?? 0,
                    'formatted_response' => $result['data'] ?? []
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['message'] ?? 'Translation failed',
                    'tokens_used' => 0
                ];
            }

        } catch (\Exception $e) {
            \Log::error('Legacy processFeature error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'tokens_used' => 0
            ];
        }
    }
}