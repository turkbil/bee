<?php

namespace Modules\AI\App\Services\Core;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Services\ModelBasedCreditService;
use Modules\AI\App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 AI FEATURE SERVICE - Feature test ve execution işlemleri
 */
class AIFeatureService
{
    protected $aiService;
    
    public function __construct($aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🚀 AI FEATURE TEST & EXECUTION
     */
    public function askFeature($feature, string $userInput, array $options = []): array
    {
        $startTime = microtime(true);
        $featureStartTime = microtime(true);
        
        // Feature modeli al
        if (is_string($feature)) {
            $feature = AIFeature::where('slug', $feature)->first();
        }
        
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Feature bulunamadı',
                'data' => []
            ];
        }

        Log::info('🎯 AI Feature test başlıyor', [
            'feature_slug' => $feature->slug,
            'feature_name' => $feature->name,
            'input_length' => strlen($userInput)
        ]);

        // Tenant bilgisini al
        $tenant = null;
        $tenantId = TenantHelpers::getTenantId();
        if ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
        }

        try {
            // Build feature prompt
            $prompt = $this->buildFeaturePrompt($feature, $userInput, $options);
            
            // Provider performance tracking
            if ($this->aiService->currentProvider) {
                $responseTime = (microtime(true) - $featureStartTime) * 1000;
                $this->aiService->providerManager->updateProviderPerformance($this->aiService->currentProvider->name, $responseTime);
            }

            $conversationData = [
                'tenant_id' => $tenantId,
                'user_id' => auth()->id() ?? 1,
                'session_id' => 'feature_' . uniqid(),
                'title' => "Feature Test: {$feature->name}",
                'type' => 'feature_test',
                'feature_name' => $feature->slug,
                'is_demo' => false,
                'prompt_id' => 1,
                'metadata' => [
                    'feature_id' => $feature->id,
                    'feature_name' => $feature->name,
                    'source' => 'feature_test'
                ]
            ];

            $response = $this->aiService->processRequest(
                $prompt,
                4000,
                0.7,
                null,
                null,
                $conversationData
            );

            if ($response['success']) {
                $aiResponse = $response['data']['content'];
                
                // Feature usage statistics
                $feature->incrementUsage();
                
                // Credit deduction system
                $this->deductFeatureCredits($tenant, $feature, $userInput, $aiResponse, $response);
                
                // Conversation tracking
                $this->aiService->createConversationRecord($userInput, $aiResponse, 'feature_test', [
                    'feature_id' => $feature->id,
                    'feature_name' => $feature->name,
                    'source' => 'feature_test'
                ]);
                
                $executionTime = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('✅ AI Feature başarılı', [
                    'feature_slug' => $feature->slug,
                    'execution_time_ms' => $executionTime,
                    'response_length' => strlen($aiResponse)
                ]);
                
                return [
                    'success' => true,
                    'data' => [
                        'content' => $aiResponse,
                        'feature' => $feature,
                        'execution_time' => $executionTime
                    ]
                ];
            }
            
            Log::error('❌ AI Feature başarısız', [
                'feature_slug' => $feature->slug,
                'error' => $response['error'] ?? 'Unknown error'
            ]);
            
            return [
                'success' => false,
                'error' => $response['error'] ?? 'AI response failed',
                'data' => []
            ];
            
        } catch (\Exception $e) {
            Log::error('🚨 AI Feature exception', [
                'feature_slug' => $feature->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Feature execution failed: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * 📝 BUILD FEATURE PROMPT
     */
    private function buildFeaturePrompt(AIFeature $feature, string $userInput, array $options = []): string
    {
        $context = $options['context'] ?? '';
        $additionalInstructions = $options['instructions'] ?? '';
        
        $prompt = "FEATURE: {$feature->name}\n";
        $prompt .= "DESCRIPTION: {$feature->description}\n\n";
        
        if ($feature->system_prompt) {
            $prompt .= "SYSTEM INSTRUCTIONS:\n{$feature->system_prompt}\n\n";
        }
        
        if (!empty($context)) {
            $prompt .= "CONTEXT: {$context}\n\n";
        }
        
        if (!empty($additionalInstructions)) {
            $prompt .= "ADDITIONAL INSTRUCTIONS:\n{$additionalInstructions}\n\n";
        }
        
        $prompt .= "USER INPUT:\n{$userInput}\n\n";
        $prompt .= "Please provide a helpful and accurate response based on the feature requirements.";
        
        return $prompt;
    }

    /**
     * 💰 FEATURE CREDIT DEDUCTION
     */
    private function deductFeatureCredits($tenant, AIFeature $feature, string $input, string $output, array $response): void
    {
        if (!$tenant && !tenant('id')) {
            return; // Skip if no tenant context
        }
        
        $effectiveTenant = $tenant ?: (object)["id" => 1, "ai_credits" => 999999];
        
        // Token calculation
        $apiResponse = $response['data'] ?? [];
        $tokenData = is_array($apiResponse) ? $apiResponse : [];
        
        $providerName = $this->aiService->currentProvider ? $this->aiService->currentProvider->name : 'unknown';
        $providerID = $this->aiService->currentProvider ? $this->aiService->currentProvider->id : 1;
        $currentModel = $effectiveTenant->default_ai_model ?? $this->aiService->currentProvider->default_model ?? 'unknown';
        
        $inputTokens = $tokenData['input_tokens'] ?? $tokenData['usage']['prompt_tokens'] ?? 0;
        $outputTokens = $tokenData['output_tokens'] ?? $tokenData['usage']['completion_tokens'] ?? 0;
        $totalTokens = $tokenData['total_tokens'] ?? $tokenData['usage']['total_tokens'] ?? ($inputTokens + $outputTokens);
        
        if ($totalTokens == 0) {
            $inputTokens = (int) ceil(strlen($input) / 4);
            $outputTokens = (int) ceil(strlen($output) / 4);
            $totalTokens = $inputTokens + $outputTokens;
        }
        
        // Credit deduction
        $creditService = app(ModelBasedCreditService::class);
        $usedCredits = $creditService->deductCredits(
            $effectiveTenant,
            $providerID,
            $currentModel,
            $inputTokens,
            $outputTokens,
            'ai_feature',
            $feature->id
        );
        
        Log::info('🎯 AI Feature kredi düşümü', [
            'tenant_id' => $effectiveTenant->id,
            'feature_slug' => $feature->slug,
            'provider' => $providerName,
            'model' => $currentModel,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $totalTokens,
            'credits_used' => $usedCredits,
            'source' => 'ai_feature_service'
        ]);
    }
}