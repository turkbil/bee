<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIModelCreditRate;
use Modules\AI\App\Services\ModelBasedCreditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Silent Fallback Service
 * 
 * Sessiz fallback sistemi - Kullanıcıya bildirim yapmadan
 * alternatif provider'lara geçer ve logs'a kaydeder
 */
readonly class SilentFallbackService
{
    public function __construct(
        private ModelBasedCreditService $creditService,
        private AIProviderManager $providerManager
    ) {}

    /**
     * AI isteği başarısız olduğunda sessiz fallback çalıştır
     * 
     * @param string $originalProvider Başarısız olan provider adı
     * @param string $originalModel Başarısız olan model adı
     * @param string $prompt Özgün prompt
     * @param array $options İstek seçenekleri
     * @param string $errorMessage Hata mesajı
     * @return array|null ['provider' => AIProvider, 'service' => object, 'model' => string] veya null
     */
    public function attemptSilentFallback(
        string $originalProvider,
        string $originalModel,
        string $prompt,
        array $options = [],
        string $errorMessage = ''
    ): ?array {
        // 1. Fallback stratejisi belirle
        $fallbackStrategy = $this->determineFallbackStrategy($originalProvider, $prompt);
        
        Log::info('🔇 Silent Fallback activated', [
            'original_provider' => $originalProvider,
            'original_model' => $originalModel,
            'error' => $errorMessage,
            'strategy' => $fallbackStrategy['type'],
            'candidates' => $fallbackStrategy['candidates']
        ]);

        // 2. Fallback candidate'ları dene
        foreach ($fallbackStrategy['candidates'] as $candidate) {
            try {
                $result = $this->tryFallbackCandidate($candidate, $prompt, $options);
                
                if ($result !== null) {
                    // Başarılı fallback - Log'la ve döndür
                    $this->logSuccessfulFallback($originalProvider, $candidate, $prompt);
                    return $result;
                }
                
            } catch (\Exception $e) {
                Log::warning('🔇 Fallback candidate failed', [
                    'candidate_provider' => $candidate['provider_name'],
                    'candidate_model' => $candidate['model'],
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // Tüm fallback'ler başarısız
        Log::error('🔇 All fallback attempts failed', [
            'original_provider' => $originalProvider,
            'original_model' => $originalModel,
            'tried_candidates' => count($fallbackStrategy['candidates'])
        ]);

        return null;
    }

    /**
     * Fallback stratejisi belirle
     * 
     * @param string $originalProvider
     * @param string $prompt
     * @return array ['type' => string, 'candidates' => array]
     */
    private function determineFallbackStrategy(string $originalProvider, string $prompt): array
    {
        $promptLength = strlen($prompt);
        $candidates = [];

        // Strateji 1: Provider'a göre akıllı fallback
        switch (strtolower($originalProvider)) {
            case 'anthropic':
                // Anthropic başarısızsa → OpenAI → DeepSeek
                $candidates = $this->getModelsByProvider(['OpenAI', 'DeepSeek']);
                $strategyType = 'provider_based';
                break;
                
            case 'openai':
                // OpenAI başarısızsa → Anthropic → DeepSeek
                $candidates = $this->getModelsByProvider(['Anthropic', 'DeepSeek']);
                $strategyType = 'provider_based';
                break;
                
            case 'deepseek':
                // DeepSeek başarısızsa → OpenAI → Anthropic
                $candidates = $this->getModelsByProvider(['OpenAI', 'Anthropic']);
                $strategyType = 'provider_based';
                break;
                
            default:
                // Bilinmeyen provider → Tüm available provider'lar
                $candidates = $this->getModelsByProvider(['OpenAI', 'Anthropic', 'DeepSeek']);
                $strategyType = 'all_providers';
                break;
        }

        // Strateji 2: Prompt uzunluğuna göre filtreleme
        if ($promptLength > 50000) {
            // Uzun prompt'lar için büyük context window modeller
            $candidates = array_filter($candidates, function($candidate) {
                return in_array($candidate['model'], [
                    'claude-3-5-sonnet-20241022',
                    'gpt-4o',
                    'gpt-4o-mini',
                    'deepseek-chat'
                ]);
            });
            $strategyType .= '_long_context';
        }

        // Strateji 3: Cost-based sıralama (ucuz modeller önce)
        $candidates = $this->sortCandidatesByCost($candidates);

        return [
            'type' => $strategyType,
            'candidates' => array_slice($candidates, 0, 3) // Max 3 fallback
        ];
    }

    /**
     * Provider'lara göre model listesi al
     * 
     * @param array $providerNames
     * @return array
     */
    private function getModelsByProvider(array $providerNames): array
    {
        $candidates = [];
        
        foreach ($providerNames as $providerName) {
            $provider = AIProvider::where('name', $providerName)
                                ->where('is_active', true)
                                ->first();
                                
            if (!$provider || !$provider->isAvailable()) {
                continue;
            }

            // Provider'ın modellerini al
            $models = AIModelCreditRate::where('provider_id', $provider->id)
                                    ->where('is_active', true)
                                    ->get();

            foreach ($models as $model) {
                $candidates[] = [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->name,
                    'model' => $model->model_name,
                    'input_cost' => $model->credit_per_1k_input_tokens,
                    'output_cost' => $model->credit_per_1k_output_tokens,
                    'total_cost' => $model->credit_per_1k_input_tokens + $model->credit_per_1k_output_tokens
                ];
            }
        }

        return $candidates;
    }

    /**
     * Cost'a göre candidate'ları sırala (ucuz önce)
     * 
     * @param array $candidates
     * @return array
     */
    private function sortCandidatesByCost(array $candidates): array
    {
        usort($candidates, function($a, $b) {
            return $a['total_cost'] <=> $b['total_cost'];
        });

        return $candidates;
    }

    /**
     * Fallback candidate'ı dene
     * 
     * @param array $candidate
     * @param string $prompt
     * @param array $options
     * @return array|null
     */
    private function tryFallbackCandidate(array $candidate, string $prompt, array $options): ?array
    {
        // Provider'ı al
        $provider = AIProvider::find($candidate['provider_id']);
        if (!$provider || !$provider->isAvailable()) {
            return null;
        }

        // Kredi kontrolü
        $tenant = tenant();
        if ($tenant) {
            $estimatedCost = $this->creditService->calculateEstimatedCost(
                $provider->id,
                $candidate['model'],
                $prompt,
                '' // Output henüz yok
            );

            if (!$this->creditService->hasEnoughCredits($tenant, $estimatedCost)) {
                Log::warning('🔇 Fallback candidate insufficient credits', [
                    'provider' => $candidate['provider_name'],
                    'model' => $candidate['model'],
                    'estimated_cost' => $estimatedCost,
                    'tenant_credits' => $tenant->ai_credits ?? 0
                ]);
                return null;
            }
        }

        // Service instance'ı al
        $service = $provider->getServiceInstance();
        
        return [
            'provider' => $provider,
            'service' => $service,
            'model' => $candidate['model']
        ];
    }

    /**
     * Başarılı fallback'i logla
     * 
     * @param string $originalProvider
     * @param array $fallbackCandidate
     * @param string $prompt
     */
    private function logSuccessfulFallback(
        string $originalProvider,
        array $fallbackCandidate,
        string $prompt
    ): void {
        $tenant = tenant();
        
        Log::info('✅ Silent Fallback SUCCESS', [
            'tenant_id' => $tenant?->id,
            'original_provider' => $originalProvider,
            'fallback_provider' => $fallbackCandidate['provider_name'],
            'fallback_model' => $fallbackCandidate['model'],
            'fallback_cost' => $fallbackCandidate['total_cost'],
            'prompt_length' => strlen($prompt),
            'timestamp' => now()->toISOString()
        ]);

        // Cache'de fallback istatistiklerini tut
        $cacheKey = "fallback_stats_" . ($tenant?->id ?? 'central');
        $stats = Cache::get($cacheKey, ['total' => 0, 'daily' => []]);
        
        $today = now()->format('Y-m-d');
        $stats['total']++;
        $stats['daily'][$today] = ($stats['daily'][$today] ?? 0) + 1;
        
        Cache::put($cacheKey, $stats, now()->addDays(30));
    }

    /**
     * Tenant'ın fallback istatistiklerini al
     * 
     * @param int|null $tenantId
     * @return array
     */
    public function getFallbackStats(?int $tenantId = null): array
    {
        $cacheKey = "fallback_stats_" . ($tenantId ?? 'central');
        $stats = Cache::get($cacheKey, ['total' => 0, 'daily' => []]);
        
        return [
            'total_fallbacks' => $stats['total'],
            'daily_fallbacks' => $stats['daily'],
            'last_30_days' => array_sum(array_slice($stats['daily'], -30, 30, true)),
            'today' => $stats['daily'][now()->format('Y-m-d')] ?? 0
        ];
    }

    /**
     * Fallback istatistiklerini temizle
     * 
     * @param int|null $tenantId
     */
    public function clearFallbackStats(?int $tenantId = null): void
    {
        $cacheKey = "fallback_stats_" . ($tenantId ?? 'central');
        Cache::forget($cacheKey);
    }
}