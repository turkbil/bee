<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Core;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Services\ConversationTracker;
use Modules\AI\App\Services\AIProviderManager;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;

/**
 * 🎯 CENTRALIZED AI SERVICE WRAPPER
 * 
 * Ana AI kontrol merkezi - Tüm AI işlemleri buradan geçer
 * 
 * Otomatik Özellikler:
 * - 💰 Credit Deduction (operation-based pricing)
 * - 📊 Logging & Debug
 * - 💬 Conversation Save
 * - ⚡ Performance Tracking
 * - 🛡️ Error Handling
 * - 📈 Analytics & Reporting
 * 
 * @package Modules\AI\App\Services\Core
 * @author AI System v3.0
 * @version 3.0.0
 */
class AIServiceWrapper
{
    private $creditService;
    private $providerManager;
    private $conversationTracker;
    
    /**
     * 💰 UNIFIED PRICING STRATEGY
     * Kar marjı: %500-1000 (OpenAI cost ~$0.002/1000 token)
     */
    private const PRICING_STRATEGY = [
        'translation' => [
            'per_language' => 0.5,         // 0.5 kredi/dil (makul fiyat)
            'token_multiplier' => 0.001,   // Token/1000 ek
            'min_cost' => 0.1,             // Minimum kredi
        ],
        'chat' => [
            'token_multiplier' => 0.001,   // Token/1000 
            'base_cost' => 0.1,            // Base conversation cost
        ],
        'feature' => [
            'base_cost' => 1.0,            // AI Feature base cost
            'token_multiplier' => 0.0015,  // Premium multiplier
        ],
        'bulk' => [
            'discount_threshold' => 10,    // 10+ operation için indirim
            'discount_rate' => 0.8,        // %20 indirim
        ]
    ];

    public function __construct(
        AICreditService $creditService,
        AIProviderManager $providerManager,
        ConversationTracker $conversationTracker
    ) {
        $this->creditService = $creditService;
        $this->providerManager = $providerManager;
        $this->conversationTracker = $conversationTracker;
    }

    /**
     * 🚀 MAIN AI EXECUTION METHOD
     * 
     * Tüm AI işlemleri bu metod üzerinden yapılır
     */
    public function executeAI(string $operation, array $data, array $options = []): array
    {
        $startTime = microtime(true);
        $executionId = uniqid('ai_exec_');
        
        // Context hazırla
        $context = $this->prepareContext($operation, $data, $options, $executionId);
        
        try {
            Log::info('🚀 AI Operation Started', [
                'execution_id' => $executionId,
                'operation' => $operation,
                'tenant_id' => $context['tenant_id'],
                'user_id' => $context['user_id'],
                'input_size' => strlen(json_encode($data))
            ]);

            // 1. PRE-EXECUTION: Credit check & validation
            $this->preExecution($context);

            // 2. EXECUTE: Main AI operation
            $result = $this->executeOperation($operation, $data, $options, $context);

            // 3. POST-EXECUTION: Credit deduction, logging, conversation save
            $this->postExecution($result, $context);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('✅ AI Operation Completed', [
                'execution_id' => $executionId,
                'operation' => $operation,
                'execution_time_ms' => $executionTime,
                'credits_used' => $result['credits_used'] ?? 0,
                'success' => true
            ]);

            return array_merge($result, [
                'execution_id' => $executionId,
                'execution_time_ms' => $executionTime,
                'success' => true
            ]);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('❌ AI Operation Failed', [
                'execution_id' => $executionId,
                'operation' => $operation,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTime,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_id' => $executionId,
                'execution_time_ms' => $executionTime
            ];
        }
    }

    /**
     * 📋 Context hazırlama
     */
    private function prepareContext(string $operation, array $data, array $options, string $executionId): array
    {
        $tenant = tenancy()->tenant;
        $user = Auth::user();
        
        return [
            'execution_id' => $executionId,
            'operation' => $operation,
            'tenant_id' => $tenant?->id ?? 1,
            'user_id' => $user?->id ?? null,
            'data' => $data,
            'options' => $options,
            'timestamp' => now(),
            'input_tokens' => 0,
            'output_tokens' => 0,
            'provider_used' => null,
            'model_used' => null
        ];
    }

    /**
     * 🛡️ PRE-EXECUTION: Credit check & validation with notifications
     */
    private function preExecution(array &$context): void
    {
        $operation = $context['operation'];
        $tenantId = $context['tenant_id'];
        
        // Estimated credit cost hesapla
        $estimatedCost = $this->calculateEstimatedCost($operation, $context['data']);
        
        Log::debug('💰 Credit check', [
            'execution_id' => $context['execution_id'],
            'estimated_cost' => $estimatedCost,
            'tenant_id' => $tenantId
        ]);

        // Tenant ve credit balance kontrolü
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            throw new \Exception("Tenant not found: {$tenantId}");
        }

        $currentBalance = ai_credit_balance($tenant);
        $creditCheckResult = $this->checkCreditStatus($currentBalance, $estimatedCost, $tenant);
        
        // Yetersiz kredi durumu
        if (!$creditCheckResult['can_proceed']) {
            $this->handleInsufficientCredits($creditCheckResult, $context);
            throw new \Exception($creditCheckResult['error_message']);
        }
        
        // Düşük kredi uyarısı (opsiyonel)
        if ($creditCheckResult['low_credit_warning']) {
            $this->triggerLowCreditWarning($creditCheckResult, $context);
        }

        $context['estimated_cost'] = $estimatedCost;
        $context['current_balance'] = $currentBalance;
        $context['credit_warnings'] = $creditCheckResult['warnings'];
    }

    /**
     * ⚡ Ana AI operasyon execution
     */
    private function executeOperation(string $operation, array $data, array $options, array &$context): array
    {
        switch ($operation) {
            case 'chat':
                return $this->executeChatOperation($data, $options, $context);
                
            case 'translation':
                return $this->executeTranslationOperation($data, $options, $context);
                
            case 'feature':
                return $this->executeFeatureOperation($data, $options, $context);
                
            default:
                throw new \Exception("Unsupported AI operation: {$operation}");
        }
    }

    /**
     * 💬 Chat operasyonu
     */
    private function executeChatOperation(array $data, array $options, array &$context): array
    {
        $message = $data['message'] ?? '';
        $conversationId = $data['conversation_id'] ?? null;
        
        // Input token hesapla
        $context['input_tokens'] = (int) (strlen($message) / 4);
        
        // AI Provider'dan yanıt al
        $providers = $this->providerManager->getActiveProviders();
        $provider = $providers->first();
        
        if (!$provider) {
            throw new \Exception('No active AI provider found');
        }
        
        $providerService = $this->getProviderService($provider);
        $response = $providerService->ask($message, $options);
        
        // Response parse et
        $aiResponse = is_string($response) ? $response : ($response['content'] ?? '');
        $context['output_tokens'] = (int) (strlen($aiResponse) / 4);
        $context['provider_used'] = $provider->name;
        $context['model_used'] = $provider->default_model;
        
        return [
            'response' => $aiResponse,
            'conversation_id' => $conversationId,
            'provider' => $provider->name,
            'model' => $provider->default_model
        ];
    }

    /**
     * 🌍 Translation operasyonu
     */
    private function executeTranslationOperation(array $data, array $options, array &$context): array
    {
        $text = $data['text'] ?? '';
        $fromLang = $data['from_lang'] ?? 'en';
        $toLang = $data['to_lang'] ?? 'tr';
        $operationType = $data['type'] ?? 'text'; // text, html, entity
        
        // Input token hesapla
        $context['input_tokens'] = (int) (strlen($text) / 4);
        
        // Translation service çağır (existing logic'i kullan)
        $translationResult = $this->performTranslation($text, $fromLang, $toLang, $operationType, $options);
        
        // Output token hesapla
        $translatedText = $translationResult['translated_text'] ?? '';
        $context['output_tokens'] = (int) (strlen($translatedText) / 4);
        $context['provider_used'] = $translationResult['provider'] ?? 'unknown';
        $context['model_used'] = $translationResult['model'] ?? 'unknown';
        
        return [
            'translated_text' => $translatedText,
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'type' => $operationType,
            'provider' => $context['provider_used']
        ];
    }

    /**
     * 🚀 AI Feature operasyonu
     */
    private function executeFeatureOperation(array $data, array $options, array &$context): array
    {
        $featureSlug = $data['feature_slug'] ?? '';
        $userInput = $data['input'] ?? '';
        
        // Input token hesapla
        $context['input_tokens'] = (int) (strlen($userInput) / 4);
        
        // AI Feature service çağır (existing AIFeatureService logic)
        $featureResult = $this->performFeatureExecution($featureSlug, $userInput, $options);
        
        // Output token hesapla
        $output = $featureResult['output'] ?? '';
        $context['output_tokens'] = (int) (strlen($output) / 4);
        $context['provider_used'] = $featureResult['provider'] ?? 'unknown';
        $context['model_used'] = $featureResult['model'] ?? 'unknown';
        
        return [
            'output' => $output,
            'feature_slug' => $featureSlug,
            'provider' => $context['provider_used'],
            'metadata' => $featureResult['metadata'] ?? []
        ];
    }

    /**
     * 📊 POST-EXECUTION: Credit deduction, logging, conversation save
     */
    private function postExecution(array &$result, array $context): void
    {
        $operation = $context['operation'];
        $tenantId = $context['tenant_id'];
        $userId = $context['user_id'];
        
        // Final credit cost hesapla
        $finalCost = $this->calculateFinalCost($operation, $context);
        
        // Credit düş
        if ($finalCost > 0) {
            ai_use_credits($finalCost, $tenantId, [
                'usage_type' => $operation,
                'description' => $this->getOperationDescription($operation, $context),
                'execution_id' => $context['execution_id'],
                'input_tokens' => $context['input_tokens'],
                'output_tokens' => $context['output_tokens'],
                'provider_name' => $context['provider_used'],
                'model' => $context['model_used']
            ]);
            
            Log::info('💰 Credits deducted', [
                'execution_id' => $context['execution_id'],
                'operation' => $operation,
                'credits_used' => $finalCost,
                'tenant_id' => $tenantId
            ]);
        }
        
        // Conversation save (chat operations için)
        if ($operation === 'chat' && $userId) {
            try {
                $this->conversationTracker->saveConversation(
                    $context['data']['message'] ?? '',
                    $result['response'] ?? '',
                    [
                        'execution_id' => $context['execution_id'],
                        'provider' => $context['provider_used'],
                        'model' => $context['model_used'],
                        'tokens_used' => $context['input_tokens'] + $context['output_tokens']
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('⚠️ Conversation save failed', [
                    'execution_id' => $context['execution_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Result'a credit info ekle
        $result['credits_used'] = $finalCost;
        $result['tokens'] = [
            'input' => $context['input_tokens'],
            'output' => $context['output_tokens'],
            'total' => $context['input_tokens'] + $context['output_tokens']
        ];
    }

    /**
     * 💰 Estimated cost hesaplama
     */
    private function calculateEstimatedCost(string $operation, array $data): float
    {
        $pricing = self::PRICING_STRATEGY;
        
        switch ($operation) {
            case 'translation':
                return $pricing['translation']['per_language'];
                
            case 'chat':
                $inputLength = strlen($data['message'] ?? '');
                $estimatedTokens = (int) ($inputLength / 4) * 1.5; // Input + estimated output
                return max($pricing['chat']['base_cost'], $estimatedTokens * $pricing['chat']['token_multiplier']);
                
            case 'feature':
                return $pricing['feature']['base_cost'];
                
            default:
                return 1.0; // Default cost
        }
    }

    /**
     * 💰 Final cost hesaplama
     */
    private function calculateFinalCost(string $operation, array $context): float
    {
        $pricing = self::PRICING_STRATEGY;
        $inputTokens = $context['input_tokens'];
        $outputTokens = $context['output_tokens'];
        $totalTokens = $inputTokens + $outputTokens;
        
        switch ($operation) {
            case 'translation':
                $baseCost = $pricing['translation']['per_language'];
                $tokenCost = $totalTokens * $pricing['translation']['token_multiplier'];
                return max($baseCost + $tokenCost, $pricing['translation']['min_cost']);
                
            case 'chat':
                $baseCost = $pricing['chat']['base_cost'];
                $tokenCost = $totalTokens * $pricing['chat']['token_multiplier'];
                return $baseCost + $tokenCost;
                
            case 'feature':
                $baseCost = $pricing['feature']['base_cost'];
                $tokenCost = $totalTokens * $pricing['feature']['token_multiplier'];
                return $baseCost + $tokenCost;
                
            default:
                return $totalTokens * 0.001; // Default: token/1000
        }
    }

    /**
     * 📝 Operation description generator
     */
    private function getOperationDescription(string $operation, array $context): string
    {
        switch ($operation) {
            case 'chat':
                return 'AI Chat: ' . \Str::limit($context['data']['message'] ?? '', 50);
                
            case 'translation':
                $fromLang = $context['data']['from_lang'] ?? 'unknown';
                $toLang = $context['data']['to_lang'] ?? 'unknown';
                return "AI Translation: {$fromLang} → {$toLang}";
                
            case 'feature':
                $featureSlug = $context['data']['feature_slug'] ?? 'unknown';
                return "AI Feature: {$featureSlug}";
                
            default:
                return "AI Operation: {$operation}";
        }
    }

    /**
     * 💳 CREDIT STATUS CHECK with detailed analysis
     */
    private function checkCreditStatus(float $currentBalance, float $estimatedCost, Tenant $tenant): array
    {
        $canProceed = can_use_ai_credits($estimatedCost, $tenant);
        $lowCreditThreshold = $this->getLowCreditThreshold($tenant);
        $criticalCreditThreshold = $lowCreditThreshold * 0.5; // Kritik seviye
        
        $warnings = [];
        $lowCreditWarning = false;
        $errorMessage = null;
        
        // Yetersiz kredi kontrolü
        if (!$canProceed) {
            $errorMessage = sprintf(
                "⚠️ Yetersiz AI kredisi! Gerekli: %.2f kredi, Mevcut: %.2f kredi. Lütfen kredi satın alın.",
                $estimatedCost,
                $currentBalance
            );
            
            Log::warning('🚫 Insufficient credits', [
                'tenant_id' => $tenant->id,
                'required' => $estimatedCost,
                'available' => $currentBalance,
                'deficit' => $estimatedCost - $currentBalance
            ]);
        }
        // Kritik düşük kredi uyarısı
        elseif ($currentBalance <= $criticalCreditThreshold) {
            $warnings[] = [
                'type' => 'critical',
                'message' => sprintf(
                    "🔴 Kritik seviyede düşük AI kredisi! Mevcut: %.2f kredi. Acil kredi alımı yapmanız öneriliyor.",
                    $currentBalance
                ),
                'balance' => $currentBalance,
                'threshold' => $criticalCreditThreshold
            ];
            $lowCreditWarning = true;
        }
        // Düşük kredi uyarısı
        elseif ($currentBalance <= $lowCreditThreshold) {
            $warnings[] = [
                'type' => 'warning',
                'message' => sprintf(
                    "🟡 Düşük AI kredisi uyarısı! Mevcut: %.2f kredi. Yakında kredi alımı yapmanız öneriliyor.",
                    $currentBalance
                ),
                'balance' => $currentBalance,
                'threshold' => $lowCreditThreshold
            ];
            $lowCreditWarning = true;
        }
        
        return [
            'can_proceed' => $canProceed,
            'current_balance' => $currentBalance,
            'estimated_cost' => $estimatedCost,
            'low_credit_warning' => $lowCreditWarning,
            'warnings' => $warnings,
            'error_message' => $errorMessage,
            'thresholds' => [
                'low_credit' => $lowCreditThreshold,
                'critical_credit' => $criticalCreditThreshold
            ]
        ];
    }
    
    /**
     * 📊 Tenant bazında düşük kredi threshold hesaplama
     */
    private function getLowCreditThreshold(Tenant $tenant): float
    {
        // Tenant'ın günlük ortalama kullanımına göre dinamik threshold
        // Şu an için sabit değer - sonra optimize edilecek
        return 10.0; // 10 kredi altında uyarı
    }
    
    /**
     * 🚫 INSUFFICIENT CREDITS handler
     */
    private function handleInsufficientCredits(array $creditCheckResult, array $context): void
    {
        $tenant = Tenant::find($context['tenant_id']);
        $user = Auth::user();
        
        // Kritik log kaydet
        Log::critical('🚫 AI Operation Blocked - Insufficient Credits', [
            'execution_id' => $context['execution_id'],
            'operation' => $context['operation'],
            'tenant_id' => $context['tenant_id'],
            'user_id' => $context['user_id'],
            'required_credits' => $creditCheckResult['estimated_cost'],
            'available_credits' => $creditCheckResult['current_balance'],
            'deficit' => $creditCheckResult['estimated_cost'] - $creditCheckResult['current_balance']
        ]);
        
        // Real-time notification gönder (session flash message)
        if (session()) {
            session()->flash('ai_credit_error', [
                'type' => 'insufficient_credits',
                'message' => $creditCheckResult['error_message'],
                'required' => $creditCheckResult['estimated_cost'],
                'available' => $creditCheckResult['current_balance'],
                'buy_credits_url' => $this->getBuyCreditUrl($tenant)
            ]);
        }
        
        // Email/SMS/Push notification sistemi (gelecekte)
        // $this->notificationService->sendCriticalCreditAlert($tenant, $user, $creditCheckResult);
    }
    
    /**
     * ⚠️ LOW CREDIT WARNING handler
     */
    private function triggerLowCreditWarning(array $creditCheckResult, array $context): void
    {
        $tenant = Tenant::find($context['tenant_id']);
        
        // Warning log kaydet
        Log::warning('⚠️ Low Credit Warning Triggered', [
            'execution_id' => $context['execution_id'],
            'operation' => $context['operation'],
            'tenant_id' => $context['tenant_id'],
            'current_balance' => $creditCheckResult['current_balance'],
            'warnings' => $creditCheckResult['warnings']
        ]);
        
        // Session warning message
        if (session()) {
            $warning = $creditCheckResult['warnings'][0] ?? null;
            if ($warning) {
                session()->flash('ai_credit_warning', [
                    'type' => $warning['type'], // 'warning' veya 'critical'
                    'message' => $warning['message'],
                    'balance' => $warning['balance'],
                    'threshold' => $warning['threshold'],
                    'buy_credits_url' => $this->getBuyCreditUrl($tenant)
                ]);
            }
        }
        
        // Günlük bir kez uyarı (spam önleme)
        $cacheKey = "low_credit_warning_{$tenant->id}_" . now()->format('Y-m-d');
        if (!cache()->has($cacheKey)) {
            cache()->put($cacheKey, true, now()->addDay());
            
            // Future: Email/SMS notification
            // $this->notificationService->sendLowCreditWarning($tenant, $creditCheckResult);
        }
    }
    
    /**
     * 🛒 Buy credits URL generator
     */
    private function getBuyCreditUrl(Tenant $tenant): string
    {
        // Admin panel kredi satın alma sayfası URL'i
        return route('admin.ai.credits.purchase', ['tenant' => $tenant->id]);
    }

    /**
     * Helper methods (existing logic'i entegre et)
     */
    private function getProviderService($provider)
    {
        // Existing getProviderService logic from FastHtmlTranslationService
        switch (strtolower($provider->name)) {
            case 'openai':
                return app(\Modules\AI\App\Services\OpenAIService::class);
            case 'anthropic':
            case 'claude':
                return app(\Modules\AI\App\Services\AnthropicService::class);
            case 'deepseek':
                return app(\Modules\AI\App\Services\DeepSeekService::class);
            default:
                throw new \Exception("Unknown provider: {$provider->name}");
        }
    }

    private function performTranslation(string $text, string $fromLang, string $toLang, string $type, array $options): array
    {
        // Existing translation logic'i buraya taşı
        // Bu geçici implementation - sonra refactor edilecek
        return [
            'translated_text' => "TRANSLATED: {$text}",
            'provider' => 'openai',
            'model' => 'gpt-4o-mini'
        ];
    }

    private function performFeatureExecution(string $featureSlug, string $input, array $options): array
    {
        // Existing AIFeatureService logic'i buraya taşı
        // Bu geçici implementation - sonra refactor edilecek
        return [
            'output' => "FEATURE OUTPUT: {$input}",
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'metadata' => []
        ];
    }
}