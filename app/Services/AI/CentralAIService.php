<?php

namespace App\Services\AI;

use Modules\AI\App\Models\AIProvider;
// use Modules\AI\App\Models\AITokenUsage; // TODO: Create this model or use AICreditUsage
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Central AI Service - Tüm AI çağrıları buradan geçer
 * Otomatik tracking, tenant-aware provider selection, dynamic token costs
 */
class CentralAIService
{
    private $currentTenant;
    private $selectedProvider;
    
    public function __construct()
    {
        $this->currentTenant = $this->getCurrentTenant();
        $this->selectedProvider = $this->selectBestProvider();
    }
    
    /**
     * Ana AI çağrı metodu - tüm AI istekleri buradan geçer
     */
    public function executeRequest(string $prompt, array $options = []): array
    {
        $startTime = microtime(true);
        $usageType = $options['usage_type'] ?? 'general';
        $featureSlug = $options['feature_slug'] ?? null;
        $referenceId = $options['reference_id'] ?? null;

        // Override provider if specified in options
        if (isset($options['force_provider'])) {
            $this->forceProvider($options['force_provider']);
        }

        // Override model if specified in options
        if (isset($options['force_model']) && $this->selectedProvider) {
            $this->selectedProvider->default_model = $options['force_model'];
            Log::info('🎯 Forced model override', [
                'model' => $options['force_model']
            ]);
        }

        Log::info('🤖 Central AI Request Started', [
            'tenant_id' => $this->currentTenant?->id,
            'provider' => $this->selectedProvider?->name,
            'model' => $this->getSelectedModel(),
            'usage_type' => $usageType,
            'feature_slug' => $featureSlug,
            'prompt_length' => strlen($prompt)
        ]);
        
        $response = null;
        $estimatedCost = 0;

        try {
            // Credit ihtiyacını hesapla (input + estimated output)
            $estimatedCost = $this->calculateEstimatedCreditCost($prompt, $options);

            // Credit kontrolü
            if (!$this->checkCreditAvailability($estimatedCost)) {
                throw new \Exception('Yetersiz kredi bakiyesi. Gerekli: $' . number_format($estimatedCost, 4));
            }

            // AI provider service'ini çağır
            $response = $this->callProviderService($prompt, $options);

            // Gerçek credit maliyetini hesapla (input + actual output tokens)
            $actualCost = $this->calculateActualCreditCost($prompt, $response, $options);

            // Credit kullanımını kaydet
            $this->recordCreditUsage($actualCost, $usageType, $featureSlug, $referenceId, $response, $prompt);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('✅ Central AI Request Completed', [
                'tenant_id' => $this->currentTenant?->id,
                'provider' => $this->selectedProvider?->name,
                'estimated_cost' => $estimatedCost,
                'actual_cost' => $actualCost,
                'response_time_ms' => $responseTime,
                'success' => true
            ]);

            return [
                'success' => true,
                'response' => $response,
                'provider' => $this->selectedProvider->name,
                'credit_cost' => $actualCost,
                'response_time_ms' => $responseTime,
                'tenant_id' => $this->currentTenant?->id
            ];

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('🚨 Central AI Request Failed', [
                'tenant_id' => $this->currentTenant?->id,
                'provider' => $this->selectedProvider?->name,
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $this->selectedProvider?->name,
                'response_time_ms' => $responseTime
            ];
        }
    }
    
    /**
     * Tenant-aware AI provider seçimi
     */
    private function selectBestProvider(): ?AIProvider
    {
        try {
            // Tenant'ın varsayılan provider'ını kontrol et
            if ($this->currentTenant && $this->currentTenant->default_ai_provider_id) {
                $tenantProvider = AIProvider::where('id', $this->currentTenant->default_ai_provider_id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($tenantProvider) {
                    // Tenant'ın seçili modelini de al
                    $tenantModel = $this->currentTenant->getDefaultAiModel();
                    
                    Log::info('🎯 Tenant AI Provider selected', [
                        'tenant_id' => $this->currentTenant->id,
                        'provider' => $tenantProvider->name,
                        'model' => $tenantModel,
                        'source' => 'tenant_default'
                    ]);
                    
                    // Provider'ın default model'ini tenant seçimine göre ayarla
                    if ($tenantModel && $this->isValidModelForProvider($tenantModel, $tenantProvider)) {
                        $tenantProvider->setAttribute('selected_model', $tenantModel);
                    } else {
                        $tenantProvider->setAttribute('selected_model', $tenantProvider->default_model);
                    }
                    
                    return $tenantProvider;
                }
            }
            
            // Fallback: En yüksek priority'li aktif provider
            $systemProvider = AIProvider::where('is_active', true)
                ->orderBy('priority', 'desc')
                ->first();
                
            if ($systemProvider) {
                Log::info('🔄 System AI Provider selected', [
                    'tenant_id' => $this->currentTenant?->id,
                    'provider' => $systemProvider->name,
                    'priority' => $systemProvider->priority,
                    'source' => 'system_fallback'
                ]);
                return $systemProvider;
            }
            
            throw new \Exception('Hiç aktif AI provider bulunamadı');
            
        } catch (\Exception $e) {
            Log::error('AI Provider selection failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Mevcut tenant'ı al
     */
    private function getCurrentTenant(): ?Tenant
    {
        try {
            if (function_exists('tenant')) {
                return tenant();
            }
            
            // Session'dan tenant ID al
            $tenantId = session('tenant_id');
            if ($tenantId) {
                return Tenant::find($tenantId);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::warning('Tenant detection failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Model'in provider için geçerli olup olmadığını kontrol et
     */
    private function isValidModelForProvider(string $model, AIProvider $provider): bool
    {
        if (!$provider->available_models) {
            return false;
        }
        
        return array_key_exists($model, $provider->available_models);
    }
    
    /**
     * Seçili model'i al
     */
    public function getSelectedModel(): ?string
    {
        return $this->selectedProvider?->getAttribute('selected_model') ?? $this->selectedProvider?->default_model;
    }
    
    /**
     * Credit maliyetini tahmin et (input + estimated output)
     */
    private function calculateEstimatedCreditCost(string $prompt, array $options): float
    {
        if (!$this->selectedProvider) {
            return 0.01; // Default fallback: $0.01
        }
        
        // Input token sayısını hesapla
        $inputTokens = $this->calculateTokenCount($prompt);
        
        // Output token tahminini yap
        $estimatedOutputTokens = $this->estimateOutputTokens($options);
        
        // Provider'ın model pricing'ini al
        $pricing = $this->getModelPricing($this->getSelectedModel());
        
        // Input ve output maliyetlerini hesapla
        $inputCost = ($inputTokens / 1000) * $pricing['input']; // per 1K tokens
        $outputCost = ($estimatedOutputTokens / 1000) * $pricing['output']; // per 1K tokens
        
        $baseCost = $inputCost + $outputCost;
        
        // Provider bazlı çarpan uygula
        $providerMultiplier = $this->getProviderMultiplier($this->selectedProvider->name);
        
        // Feature bazlı çarpan uygula
        $featureMultiplier = $this->getFeatureMultiplier($options['feature_slug'] ?? null);
        
        // Son maliyet hesaplama
        $finalCost = $baseCost * $providerMultiplier * $featureMultiplier;
        
        return round($finalCost, 4); // 4 decimal precision
    }
    
    /**
     * Gerçek credit maliyetini hesapla (actual input + output tokens)
     */
    private function calculateActualCreditCost(string $prompt, array $response, array $options): float
    {
        // Input token sayısını hesapla (gerçek)
        $inputTokens = $this->calculateTokenCount($prompt);
        
        // Output token sayısını response'dan al
        $outputTokens = $this->getActualOutputTokens($response);
        
        // Provider'ın model pricing'ini al
        $pricing = $this->getModelPricing($this->getSelectedModel());
        
        // Input ve output maliyetlerini hesapla
        $inputCost = ($inputTokens / 1000) * $pricing['input']; // per 1K tokens
        $outputCost = ($outputTokens / 1000) * $pricing['output']; // per 1K tokens
        
        $baseCost = $inputCost + $outputCost;
        
        // Provider bazlı çarpan uygula
        $providerMultiplier = $this->getProviderMultiplier($this->selectedProvider->name);
        
        // Feature bazlı çarpan uygula
        $featureMultiplier = $this->getFeatureMultiplier($options['feature_slug'] ?? null);
        
        // Son maliyet hesaplama
        $finalCost = $baseCost * $providerMultiplier * $featureMultiplier;
        
        return round($finalCost, 4); // 4 decimal precision
    }
    
    /**
     * Credit kullanımını kontrol et
     */
    private function checkCreditAvailability(float $creditNeeded): bool
    {
        if (!$this->currentTenant) {
            return true; // No tenant = unlimited (system usage)
        }
        
        return $this->currentTenant->hasEnoughCredits($creditNeeded);
    }
    
    /**
     * Credit kullanımını kaydet - Otomatik tracking
     */
    private function recordCreditUsage(float $creditCost, string $usageType, ?string $featureSlug, ?string $referenceId, array $response, string $prompt): void
    {
        try {
            if (!$this->currentTenant) {
                return;
            }
            
            // Tenant credit balance güncelle
            $this->currentTenant->useCredits($creditCost, $usageType, 
                $this->buildUsageDescription($usageType, $featureSlug), $referenceId);
            
            // Token sayılarını hesapla
            $inputTokens = $this->calculateTokenCount($prompt);
            $outputTokens = $this->getActualOutputTokens($response);
            $totalTokens = $inputTokens + $outputTokens;
            
            // Detaylı kullanım kaydı (debug & statistics için)
            // TODO: Re-enable when AITokenUsage model is created
            /* AITokenUsage::create([
                'tenant_id' => $this->currentTenant->id,
                'ai_provider_id' => $this->selectedProvider->id,
                'provider_name' => $this->selectedProvider->name,
                'tokens_used' => $totalTokens,
                'prompt_tokens' => $inputTokens,
                'completion_tokens' => $outputTokens,
                'credit_cost' => $creditCost,
                'usage_type' => $usageType,
                'feature_slug' => $featureSlug,
                'reference_id' => $referenceId,
                'model' => $this->getSelectedModel(),
                'cost_multiplier' => $this->getProviderMultiplier($this->selectedProvider->name),
                'cost_breakdown' => json_encode([
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'provider' => $this->selectedProvider->name,
                    'model' => $this->getSelectedModel(),
                    'provider_multiplier' => $this->getProviderMultiplier($this->selectedProvider->name),
                    'feature_multiplier' => $this->getFeatureMultiplier($featureSlug),
                    'base_cost' => round($creditCost / ($this->getProviderMultiplier($this->selectedProvider->name) * $this->getFeatureMultiplier($featureSlug)), 4),
                    'final_cost' => $creditCost,
                ]),
                'response_metadata' => [
                    'response_length' => strlen(json_encode($response)),
                    'success' => true,
                    'timestamp' => now()->toISOString()
                ],
                'used_at' => now()
            ]); */
            
            Log::info('💾 Credit usage recorded', [
                'tenant_id' => $this->currentTenant->id,
                'provider' => $this->selectedProvider->name,
                'credit_cost' => $creditCost,
                'tokens' => $totalTokens,
                'type' => $usageType,
                'feature' => $featureSlug
            ]);
            
        } catch (\Exception $e) {
            Log::error('Credit usage recording failed', [
                'error' => $e->getMessage(),
                'credit_cost' => $creditCost,
                'tenant_id' => $this->currentTenant?->id
            ]);
        }
    }
    
    /**
     * Kullanım açıklaması oluştur
     */
    private function buildUsageDescription(string $usageType, ?string $featureSlug): string
    {
        if ($featureSlug) {
            return "AI Feature: {$featureSlug} ({$usageType})";
        }
        
        switch ($usageType) {
            case 'seo':
                return 'SEO analiz ve önerileri';
            case 'content':
                return 'İçerik üretimi';
            case 'chat':
                return 'AI sohbet';
            case 'translation':
                return 'Çeviri servisi';
            default:
                return "AI kullanımı: {$usageType}";
        }
    }
    
    /**
     * Provider service'ini çağır
     */
    private function callProviderService(string $prompt, array $options): array
    {
        if (!$this->selectedProvider) {
            throw new \Exception('AI Provider seçilmedi');
        }

        // Provider'a göre doğru service'i çağır
        $providerName = strtolower($this->selectedProvider->name);

        try {
            switch ($providerName) {
                case 'deepseek':
                    $service = app(\Modules\AI\App\Services\DeepSeekService::class);
                    $messages = [['role' => 'user', 'content' => $prompt]];
                    $response = $service->ask($messages, false, $options);
                    break;

                case 'openai':
                    $service = app(\Modules\AI\App\Services\OpenAIService::class);

                    // Model override varsa uygula
                    if ($this->getSelectedModel() !== $service->getProviderInfo()['model']) {
                        $service->setModel($this->getSelectedModel());
                        Log::info('🔧 OpenAI model override applied', [
                            'model' => $this->getSelectedModel()
                        ]);
                    }

                    $messages = [['role' => 'user', 'content' => $prompt]];
                    // ask() metodu string döndürür, biz streaming'den full response alacağız
                    $streamResult = $service->generateCompletionStream($messages, null, $options);

                    // Streaming result'tan gerekli bilgileri al
                    $response = $streamResult['response'] ?? '';
                    $usage = [
                        'prompt_tokens' => $streamResult['input_tokens'] ?? 0,
                        'completion_tokens' => $streamResult['output_tokens'] ?? 0,
                        'total_tokens' => $streamResult['tokens_used'] ?? 0,
                    ];
                    break;

                case 'anthropic':
                case 'claude':
                    $service = app(\Modules\AI\App\Services\AnthropicService::class);
                    $messages = [['role' => 'user', 'content' => $prompt]];
                    $response = $service->ask($messages, false, $options);
                    $usage = [];
                    break;

                default:
                    throw new \Exception("Provider '{$providerName}' desteklenmiyor");
            }

            return [
                'content' => is_string($response) ? $response : ($response['response'] ?? ''),
                'provider' => $this->selectedProvider->name,
                'timestamp' => now()->toISOString(),
                'usage' => $usage ?? []
            ];

        } catch (\Exception $e) {
            Log::error('❌ Provider service call failed', [
                'provider' => $providerName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Tenant için kullanılabilir provider'ları al
     */
    public function getAvailableProviders(): array
    {
        return AIProvider::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get(['id', 'name', 'priority', 'token_cost_multiplier', 'tokens_per_request_estimate'])
            ->toArray();
    }
    
    /**
     * Tenant AI ayarlarını güncelle
     */
    public function updateTenantAISettings(int $providerId, array $settings = []): bool
    {
        if (!$this->currentTenant) {
            return false;
        }
        
        try {
            $this->currentTenant->update([
                'default_ai_provider_id' => $providerId,
                'ai_settings' => $settings
            ]);
            
            Log::info('Tenant AI settings updated', [
                'tenant_id' => $this->currentTenant->id,
                'provider_id' => $providerId,
                'settings' => $settings
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update tenant AI settings', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->currentTenant->id
            ]);
            return false;
        }
    }
    
    // ======= CREDIT SYSTEM HELPER METHODS =======
    
    /**
     * Token sayısını hesapla (yaklaşık)
     */
    private function calculateTokenCount(string $text): int
    {
        // OpenAI'nin token hesaplama yaklaşımı: ~4 karakter = 1 token
        return max(1, intval(strlen($text) / 4));
    }
    
    /**
     * Output token tahminini yap
     */
    private function estimateOutputTokens(array $options): int
    {
        $baseEstimate = 100; // Default
        
        // Feature type'a göre ayarlama
        if (isset($options['feature_slug'])) {
            switch ($options['feature_slug']) {
                case 'seo-content-generation':
                    $baseEstimate = 200; // SEO daha uzun
                    break;
                case 'content-translation':
                    $baseEstimate = 300; // Çeviri daha uzun
                    break;
                case 'chat':
                    $baseEstimate = 80; // Chat kısa
                    break;
                default:
                    $baseEstimate = 100;
            }
        }
        
        // Max token ayarından yararlan
        if (isset($options['max_tokens'])) {
            $baseEstimate = min($baseEstimate, intval($options['max_tokens']));
        }
        
        return $baseEstimate;
    }
    
    /**
     * Gerçek output token sayısını response'dan al
     */
    private function getActualOutputTokens(array $response): int
    {
        // OpenAI API response formatı
        if (isset($response['usage']['completion_tokens'])) {
            return intval($response['usage']['completion_tokens']);
        }
        
        // Response content'inden tahmin et
        if (isset($response['content'])) {
            return $this->calculateTokenCount($response['content']);
        }
        
        // Fallback
        return 50; // Conservative estimate
    }
    
    /**
     * Model pricing bilgisini al
     */
    private function getModelPricing(string $model): array
    {
        // Gerçek 2025 fiyatları (1M token başına USD)
        $modelPricing = [
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4o' => ['input' => 5.0, 'output' => 15.0],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
            'deepseek-chat' => ['input' => 0.27, 'output' => 1.10],
            'claude-3-haiku-20240307' => ['input' => 0.25, 'output' => 1.25],
            'claude-3-sonnet-20240229' => ['input' => 3.0, 'output' => 15.0],
        ];
        
        return $modelPricing[$model] ?? ['input' => 0.50, 'output' => 1.50]; // Default pricing
    }
    
    /**
     * Model bazlı çarpan al (öncelik: model > provider > default)
     */
    private function getProviderMultiplier(string $providerName): float
    {
        $selectedModel = $this->getSelectedModel();
        
        // Önce model bazlı çarpan ara
        $modelMultipliers = config('ai.credit_management.model_multipliers', []);
        if (isset($modelMultipliers[$selectedModel])) {
            return $modelMultipliers[$selectedModel];
        }
        
        // Model bulunamazsa provider bazlı çarpan
        $providerMultipliers = config('ai.credit_management.provider_multipliers', []);
        return $providerMultipliers[$providerName] ?? $providerMultipliers['default'] ?? 3.0;
    }
    
    /**
     * Feature bazlı çarpan al
     */
    private function getFeatureMultiplier(?string $featureSlug): float
    {
        if (!$featureSlug) {
            return 1.0;
        }

        $multipliers = config('ai.credit_management.feature_multipliers', []);
        return $multipliers[$featureSlug] ?? $multipliers['default'] ?? 1.0;
    }

    /**
     * Belirli bir provider kullanmaya zorla
     */
    private function forceProvider(string $providerName): void
    {
        $provider = AIProvider::where('name', $providerName)
            ->where('is_active', true)
            ->first();

        if ($provider) {
            $this->selectedProvider = $provider;

            Log::info('🎯 Forced provider selected', [
                'provider' => $provider->name,
                'model' => $provider->default_model
            ]);
        } else {
            Log::warning('⚠️ Forced provider not found, using default', [
                'requested_provider' => $providerName
            ]);
        }
    }
}