<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\SilentFallbackService;
use Modules\AI\App\Services\ModelBasedCreditService;
use Modules\AI\App\Services\AIProviderManager;
use Illuminate\Support\Facades\Log;

/**
 * Silent Fallback Integration for AIService
 * 
 * Bu trait AIService içinde Silent Fallback sistemini entegre eder
 */
trait SilentFallbackIntegration
{
    /**
     * Silent Fallback ile AI isteği gerçekleştir
     * 
     * @param string $prompt
     * @param int|null $maxTokens
     * @param float|null $temperature
     * @param string|null $model
     * @param string|null $systemPrompt
     * @param array $metadata
     * @return array
     */
    protected function processRequestWithFallback(
        string $prompt,
        ?int $maxTokens = null,
        ?float $temperature = null,
        ?string $model = null,
        ?string $systemPrompt = null,
        array $metadata = []
    ): array {
        try {
            // Ana AI isteğini çalıştır
            return $this->executeAIRequest($prompt, $maxTokens, $temperature, $model, $systemPrompt, $metadata);
            
        } catch (\Exception $e) {
            Log::error('AIService processRequest error', [
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt),
                'metadata' => $metadata,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Silent Fallback dene
            Log::info('🔇 Attempting Silent Fallback after processRequest error');
            
            $fallbackResult = $this->silentFallbackService->attemptSilentFallback(
                $this->currentProvider ? $this->currentProvider->name : 'unknown',
                $model ?? ($this->currentProvider ? $this->currentProvider->default_model : 'unknown'),
                $prompt,
                [
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'system_prompt' => $systemPrompt,
                    'metadata' => $metadata
                ],
                $e->getMessage()
            );
            
            if ($fallbackResult) {
                Log::info('✅ Silent Fallback SUCCESS in processRequest', [
                    'fallback_provider' => $fallbackResult['provider']->name,
                    'fallback_model' => $fallbackResult['model']
                ]);
                
                // Fallback provider ile tekrar dene
                try {
                    $originalProvider = $this->currentProvider;
                    $originalService = $this->currentService;
                    
                    $this->currentProvider = $fallbackResult['provider'];
                    $this->currentService = $fallbackResult['service'];
                    
                    // Fallback ile tek sefer deneme
                    $result = $this->executeAIRequest($prompt, $maxTokens, $temperature, $fallbackResult['model'], $systemPrompt, $metadata);
                    
                    // Original provider'ı geri yükle (sonraki istekler için)
                    $this->currentProvider = $originalProvider;
                    $this->currentService = $originalService;
                    
                    return $result;
                    
                } catch (\Exception $fallbackException) {
                    Log::error('🔇 Silent Fallback also failed', [
                        'fallback_error' => $fallbackException->getMessage()
                    ]);
                    
                    // Original provider'ı geri yükle
                    $this->currentProvider = $originalProvider ?? $this->currentProvider;
                    $this->currentService = $originalService ?? $this->currentService;
                }
            }
            
            return [
                'success' => false,
                'error' => 'processing_error',
                'message' => 'AI processing failed: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }
    
    /**
     * Ana AI isteğini çalıştır (fallback olmadan)
     * 
     * @param string $prompt
     * @param int|null $maxTokens
     * @param float|null $temperature
     * @param string|null $model
     * @param string|null $systemPrompt
     * @param array $metadata
     * @return array
     */
    private function executeAIRequest(
        string $prompt,
        ?int $maxTokens = null,
        ?float $temperature = null,
        ?string $model = null,
        ?string $systemPrompt = null,
        array $metadata = []
    ): array {
        // Default değerler
        $maxTokens = $maxTokens ?? 2000;
        $temperature = $temperature ?? 0.7;
        
        // Provider kontrolü
        if (!$this->currentService) {
            throw new \Exception('AI Provider service not available');
        }
        
        // Model kontrolü - tenant'dan al ya da provider default'u kullan
        $tenant = tenant();
        $finalModel = $model ?? ($tenant ? $tenant->default_ai_model : null) ?? $this->currentProvider->default_model;
        
        // Model bazlı kredi kontrolü (YENİ)
        if ($tenant) {
            // Estimate input tokens
            $estimatedInputTokens = strlen($prompt) / 4; // Rough estimation: 4 chars per token
            $estimatedOutputTokens = $maxTokens * 0.5; // Conservative estimation
            
            // Model bazlı kredi hesaplama
            $requiredCredits = ai_calculate_model_credits(
                $this->currentProvider->id,
                $finalModel,
                $estimatedInputTokens,
                $estimatedOutputTokens
            );
            
            // Kredi kontrolü
            if ($requiredCredits && $tenant->credits < $requiredCredits) {
                return [
                    'success' => false,
                    'error' => 'insufficient_credits',
                    'message' => "Yetersiz kredi. Gerekli: {$requiredCredits}, Mevcut: {$tenant->credits}",
                    'required_credits' => $requiredCredits,
                    'available_credits' => $tenant->credits
                ];
            }
        }
        
        // AI service çağrısı
        $startTime = microtime(true);
        
        // Provider'a göre service çağrısı
        if (method_exists($this->currentService, 'generateCompletion')) {
            $response = $this->currentService->generateCompletion($prompt, [
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'model' => $model ?? $this->currentProvider->default_model
            ]);
        } else {
            // Claude ve diğer provider'lar için messages formatı
            $messages = [];
            
            // System prompt varsa ayrı olarak ekle
            if ($systemPrompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt
                ];
                $userPrompt = $prompt;
            } else {
                $userPrompt = $prompt;
            }
            
            // User message ekle
            $messages[] = [
                'role' => 'user',
                'content' => $userPrompt
            ];
            
            // ask metodu messages array bekliyor
            $response = $this->currentService->ask($messages, false);
        }
        
        $processingTime = microtime(true) - $startTime;
        
        // Debug: Response yapısını logla
        Log::info('🔍 AI Service Response Structure', [
            'response_type' => gettype($response),
            'has_choices' => isset($response['choices']),
            'has_response' => isset($response['response']),
            'has_content' => isset($response['content']),
            'response_keys' => is_array($response) ? array_keys($response) : 'NOT_ARRAY',
            'provider' => $this->currentProvider->name,
            'model' => $model ?? $this->currentProvider->default_model
        ]);
        
        // Model bazlı kredi düşümü (YENİ SISTEM)
        if ($tenant && isset($response['usage'])) {
            $inputTokens = $response['usage']['prompt_tokens'] ?? $response['usage']['input_tokens'] ?? 0;
            $outputTokens = $response['usage']['completion_tokens'] ?? $response['usage']['output_tokens'] ?? 0;
            
            // Model bazlı kredi hesapla ve düş
            $usedCredits = ai_use_credits_with_model(
                $tenant->id,
                $this->currentProvider->id,
                $finalModel,
                $inputTokens,
                $outputTokens,
                $metadata['source'] ?? 'ai_feature',
                $metadata['feature_id'] ?? null
            );
            
            Log::info('🔥 Model-based credit deduction', [
                'tenant_id' => $tenant->id,
                'provider' => $this->currentProvider->name,
                'model' => $finalModel,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'credits_used' => $usedCredits,
                'remaining_credits' => $tenant->fresh()->credits
            ]);
        }
        
        // Response içeriğini al - daha güvenli parsing
        $content = '';
        if (isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            Log::info('✅ Content from choices[0].message.content');
        } elseif (isset($response['response'])) {
            $content = $response['response'];
            Log::info('✅ Content from response key');
        } elseif (isset($response['content'])) {
            $content = $response['content'];
            Log::info('✅ Content from content key');
        } elseif (is_string($response)) {
            $content = $response;
            Log::info('✅ Response is string directly');
        } else {
            Log::error('❌ Could not parse AI response', [
                'response_structure' => $response
            ]);
        }
        
        return [
            'success' => true,
            'data' => [
                'content' => $content,
                'raw_response' => $response
            ],
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
            'model' => $model ?? $this->currentProvider->default_model,
            'processing_time' => $processingTime,
            'request_id' => $metadata['request_id'] ?? uniqid('ai_', true),
            'metadata' => $metadata
        ];
    }
}