<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIModelCreditRate;
use Modules\AI\App\Services\ModelBasedCreditService;
use Modules\AI\App\Services\SilentFallbackService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Central Fallback Service
 * 
 * Merkezi fallback yönetimi - Tüm tenant'lar için global fallback stratejileri
 */
readonly class CentralFallbackService
{
    // Fallback strategy türleri
    const STRATEGIES = [
        'cost_optimized' => 'Maliyet Optimize',
        'performance_optimized' => 'Performans Optimize', 
        'reliability_optimized' => 'Güvenilirlik Optimize',
        'balanced' => 'Dengeli Yaklaşım'
    ];
    
    // Provider sağlık durumları
    const HEALTH_STATUS = [
        'healthy' => 'Sağlıklı',
        'degraded' => 'Performans Düşüklüğü',
        'unavailable' => 'Kullanılamaz',
        'maintenance' => 'Bakım'
    ];

    public function __construct(
        private ModelBasedCreditService $creditService,
        private SilentFallbackService $silentFallbackService
    ) {}

    /**
     * Global fallback stratejisi belirle
     * 
     * @param string $originalProvider
     * @param string $originalModel
     * @param array $context
     * @return array
     */
    public function determineFallbackStrategy(
        string $originalProvider,
        string $originalModel,
        array $context = []
    ): array {
        // Context analizi
        $promptLength = $context['prompt_length'] ?? 0;
        $userPriority = $context['user_priority'] ?? 'balanced';
        $tenantPlan = $context['tenant_plan'] ?? 'standard';
        
        // Global provider health durumu
        $healthReport = $this->getGlobalProviderHealth();
        
        // Strategy seç
        $strategy = $this->selectOptimalStrategy($userPriority, $tenantPlan, $healthReport);
        
        // Fallback candidate'ları oluştur
        $candidates = $this->generateFallbackCandidates($originalProvider, $strategy, $promptLength, $healthReport);
        
        Log::info('🌐 Central Fallback Strategy Determined', [
            'original_provider' => $originalProvider,
            'original_model' => $originalModel,
            'strategy' => $strategy,
            'candidates_count' => count($candidates),
            'context' => $context
        ]);
        
        return [
            'strategy' => $strategy,
            'candidates' => $candidates,
            'health_report' => $healthReport,
            'context' => $context
        ];
    }

    /**
     * Optimal strateji seç
     * 
     * @param string $userPriority
     * @param string $tenantPlan
     * @param array $healthReport
     * @return string
     */
    private function selectOptimalStrategy(string $userPriority, string $tenantPlan, array $healthReport): string
    {
        // Enterprise plan'lar için özel strateji
        if ($tenantPlan === 'enterprise') {
            return 'performance_optimized';
        }
        
        // Basic plan'lar için maliyet optimize
        if ($tenantPlan === 'basic') {
            return 'cost_optimized';
        }
        
        // Provider sağlık durumuna göre
        $unhealthyProviders = array_filter($healthReport, function($status) {
            return in_array($status['status'], ['degraded', 'unavailable']);
        });
        
        if (count($unhealthyProviders) > 1) {
            return 'reliability_optimized';
        }
        
        // User priority'ye göre
        return match($userPriority) {
            'cost' => 'cost_optimized',
            'speed' => 'performance_optimized',
            'stability' => 'reliability_optimized',
            default => 'balanced'
        };
    }

    /**
     * Fallback candidate'ları oluştur
     * 
     * @param string $originalProvider
     * @param string $strategy
     * @param int $promptLength
     * @param array $healthReport
     * @return array
     */
    private function generateFallbackCandidates(
        string $originalProvider, 
        string $strategy, 
        int $promptLength, 
        array $healthReport
    ): array {
        // Tüm aktif provider'ları al
        $allProviders = AIProvider::where('is_active', true)
                                 ->with(['modelCreditRates' => function($query) {
                                     $query->where('is_active', true);
                                 }])
                                 ->get();
        
        $candidates = [];
        
        foreach ($allProviders as $provider) {
            // Original provider'ı atla
            if ($provider->name === $originalProvider) {
                continue;
            }
            
            // Provider sağlık kontrolü
            $providerHealth = $healthReport[$provider->name] ?? ['status' => 'unknown'];
            if ($providerHealth['status'] === 'unavailable') {
                continue;
            }
            
            // Modelleri ekle
            foreach ($provider->modelCreditRates as $modelRate) {
                $candidate = $this->createCandidate($provider, $modelRate, $strategy, $promptLength, $providerHealth);
                
                if ($candidate) {
                    $candidates[] = $candidate;
                }
            }
        }
        
        // Strategy'ye göre sırala
        return $this->sortCandidatesByStrategy($candidates, $strategy);
    }

    /**
     * Candidate oluştur
     * 
     * @param AIProvider $provider
     * @param AIModelCreditRate $modelRate
     * @param string $strategy
     * @param int $promptLength
     * @param array $providerHealth
     * @return array|null
     */
    private function createCandidate(
        AIProvider $provider,
        AIModelCreditRate $modelRate,
        string $strategy,
        int $promptLength,
        array $providerHealth
    ): ?array {
        // Uzun prompt'lar için uygun model kontrolü
        if ($promptLength > 50000) {
            $longContextModels = [
                'claude-3-5-sonnet-20241022',
                'gpt-4o',
                'gpt-4o-mini',
                'deepseek-chat'
            ];
            
            if (!in_array($modelRate->model_name, $longContextModels)) {
                return null;
            }
        }
        
        // Maliyet hesapla
        $inputCost = (float) $modelRate->credit_per_1k_input_tokens;
        $outputCost = (float) $modelRate->credit_per_1k_output_tokens;
        $totalCost = $inputCost + $outputCost;
        
        // Performans skoru hesapla
        $performanceScore = $this->calculatePerformanceScore($modelRate->model_name, $provider->name);
        
        // Güvenilirlik skoru
        $reliabilityScore = $this->calculateReliabilityScore($provider->name, $providerHealth);
        
        // Strategy'ye göre final score
        $finalScore = $this->calculateFinalScore($totalCost, $performanceScore, $reliabilityScore, $strategy);
        
        return [
            'provider_id' => $provider->id,
            'provider_name' => $provider->name,
            'model' => $modelRate->model_name,
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'total_cost' => $totalCost,
            'performance_score' => $performanceScore,
            'reliability_score' => $reliabilityScore,
            'final_score' => $finalScore,
            'health_status' => $providerHealth['status'] ?? 'unknown',
            'context_window' => $this->getModelContextWindow($modelRate->model_name),
            'suitable_for_long_context' => $promptLength <= $this->getModelContextWindow($modelRate->model_name)
        ];
    }

    /**
     * Performans skoru hesapla
     * 
     * @param string $model
     * @param string $provider
     * @return float
     */
    private function calculatePerformanceScore(string $model, string $provider): float
    {
        // Model bazlı performans skoru (0-100)
        $modelScores = [
            'gpt-4o' => 95,
            'claude-3-5-sonnet-20241022' => 93,
            'gpt-4o-mini' => 85,
            'claude-3-haiku-20240307' => 82,
            'deepseek-chat' => 78,
            'gpt-3.5-turbo' => 75
        ];
        
        $baseScore = $modelScores[$model] ?? 70;
        
        // Provider bazlı modifikasyon
        $providerMultiplier = match($provider) {
            'OpenAI' => 1.0,
            'Anthropic' => 0.98,
            'DeepSeek' => 0.90,
            default => 0.85
        };
        
        return $baseScore * $providerMultiplier;
    }

    /**
     * Güvenilirlik skoru hesapla
     * 
     * @param string $provider
     * @param array $healthData
     * @return float
     */
    private function calculateReliabilityScore(string $provider, array $healthData): float
    {
        $baseScore = match($healthData['status'] ?? 'unknown') {
            'healthy' => 100,
            'degraded' => 75,
            'maintenance' => 50,
            'unavailable' => 0,
            default => 60
        };
        
        // Recent failure rate'i dikkate al
        $recentFailures = $healthData['recent_failures'] ?? 0;
        $failurePenalty = min($recentFailures * 5, 30); // Max 30 point penalty
        
        return max($baseScore - $failurePenalty, 0);
    }

    /**
     * Final score hesapla (strategy'ye göre)
     * 
     * @param float $cost
     * @param float $performance
     * @param float $reliability
     * @param string $strategy
     * @return float
     */
    private function calculateFinalScore(float $cost, float $performance, float $reliability, string $strategy): float
    {
        // Cost'u ters çevir (düşük cost = yüksek score)
        $costScore = max(100 - ($cost / 10), 0);
        
        return match($strategy) {
            'cost_optimized' => ($costScore * 0.6) + ($performance * 0.2) + ($reliability * 0.2),
            'performance_optimized' => ($performance * 0.6) + ($reliability * 0.3) + ($costScore * 0.1),
            'reliability_optimized' => ($reliability * 0.6) + ($performance * 0.3) + ($costScore * 0.1),
            'balanced' => ($performance * 0.35) + ($reliability * 0.35) + ($costScore * 0.3),
            default => ($performance + $reliability + $costScore) / 3
        };
    }

    /**
     * Strategy'ye göre candidate'ları sırala
     * 
     * @param array $candidates
     * @param string $strategy
     * @return array
     */
    private function sortCandidatesByStrategy(array $candidates, string $strategy): array
    {
        usort($candidates, function($a, $b) use ($strategy) {
            return match($strategy) {
                'cost_optimized' => $a['total_cost'] <=> $b['total_cost'],
                'performance_optimized' => $b['performance_score'] <=> $a['performance_score'],
                'reliability_optimized' => $b['reliability_score'] <=> $a['reliability_score'],
                default => $b['final_score'] <=> $a['final_score']
            };
        });
        
        // En iyi 5 candidate'ı döndür
        return array_slice($candidates, 0, 5);
    }

    /**
     * Global provider sağlık durumu
     * 
     * @return array
     */
    public function getGlobalProviderHealth(): array
    {
        $providers = AIProvider::where('is_active', true)->get();
        $healthReport = [];
        
        foreach ($providers as $provider) {
            $health = $this->getProviderHealthStatus($provider->name);
            $healthReport[$provider->name] = $health;
        }
        
        // Cache'le (5 dakika)
        Cache::put('global_provider_health', $healthReport, now()->addMinutes(5));
        
        return $healthReport;
    }

    /**
     * Tek provider'ın sağlık durumu
     * 
     * @param string $providerName
     * @return array
     */
    private function getProviderHealthStatus(string $providerName): array
    {
        $cacheKey = "provider_health_{$providerName}";
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return $cached;
        }
        
        // Son 1 saatteki başarısızlık oranını kontrol et
        $recentFailures = $this->getRecentFailureCount($providerName);
        $recentRequests = $this->getRecentRequestCount($providerName);
        
        $failureRate = $recentRequests > 0 ? ($recentFailures / $recentRequests) * 100 : 0;
        
        // Response time kontrolü
        $avgResponseTime = $this->getAverageResponseTime($providerName);
        
        // Sağlık durumu belirle
        $status = 'healthy';
        if ($failureRate > 20 || $avgResponseTime > 10000) {
            $status = 'unavailable';
        } elseif ($failureRate > 10 || $avgResponseTime > 5000) {
            $status = 'degraded';
        }
        
        $health = [
            'status' => $status,
            'failure_rate' => $failureRate,
            'recent_failures' => $recentFailures,
            'recent_requests' => $recentRequests,
            'avg_response_time' => $avgResponseTime,
            'last_checked' => now()->toISOString()
        ];
        
        // 2 dakika cache
        Cache::put($cacheKey, $health, now()->addMinutes(2));
        
        return $health;
    }

    /**
     * Model'ın context window'unu al
     * 
     * @param string $model
     * @return int
     */
    private function getModelContextWindow(string $model): int
    {
        return match($model) {
            'claude-3-5-sonnet-20241022' => 200000,
            'gpt-4o' => 128000,
            'gpt-4o-mini' => 128000,
            'deepseek-chat' => 64000,
            'claude-3-haiku-20240307' => 200000,
            'gpt-3.5-turbo' => 16000,
            default => 8000
        };
    }

    /**
     * Son başarısızlık sayısı
     * 
     * @param string $providerName
     * @return int
     */
    private function getRecentFailureCount(string $providerName): int
    {
        $cacheKey = "provider_failures_{$providerName}_" . now()->format('YmdH');
        return Cache::get($cacheKey, 0);
    }

    /**
     * Son istek sayısı
     * 
     * @param string $providerName
     * @return int
     */
    private function getRecentRequestCount(string $providerName): int
    {
        $cacheKey = "provider_requests_{$providerName}_" . now()->format('YmdH');
        return Cache::get($cacheKey, 0);
    }

    /**
     * Ortalama yanıt süresi
     * 
     * @param string $providerName
     * @return float (milliseconds)
     */
    private function getAverageResponseTime(string $providerName): float
    {
        $cacheKey = "provider_avg_response_{$providerName}";
        return Cache::get($cacheKey, 1500.0); // Default 1.5s
    }

    /**
     * Provider başarısızlığını kaydet
     * 
     * @param string $providerName
     */
    public function recordProviderFailure(string $providerName): void
    {
        $cacheKey = "provider_failures_{$providerName}_" . now()->format('YmdH');
        $current = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $current + 1, now()->addHours(2));
        
        Log::warning('🔴 Provider Failure Recorded', [
            'provider' => $providerName,
            'hourly_failures' => $current + 1,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Provider isteğini kaydet
     * 
     * @param string $providerName
     * @param float $responseTime
     */
    public function recordProviderRequest(string $providerName, float $responseTime): void
    {
        // İstek sayısı
        $requestsKey = "provider_requests_{$providerName}_" . now()->format('YmdH');
        $currentRequests = Cache::get($requestsKey, 0);
        Cache::put($requestsKey, $currentRequests + 1, now()->addHours(2));
        
        // Ortalama response time güncelle
        $avgKey = "provider_avg_response_{$providerName}";
        $currentAvg = Cache::get($avgKey, $responseTime);
        $newAvg = ($currentAvg + $responseTime) / 2;
        Cache::put($avgKey, $newAvg, now()->addHour());
    }

    /**
     * Merkezi fallback istatistikleri
     * 
     * @return array
     */
    public function getCentralFallbackStatistics(): array
    {
        $providers = AIProvider::where('is_active', true)->get();
        $stats = [];
        
        foreach ($providers as $provider) {
            $health = $this->getProviderHealthStatus($provider->name);
            $stats[$provider->name] = [
                'provider_id' => $provider->id,
                'name' => $provider->name,
                'health' => $health,
                'model_count' => $provider->modelCreditRates()->where('is_active', true)->count(),
                'total_requests_today' => $this->getTodayRequestCount($provider->name),
                'total_failures_today' => $this->getTodayFailureCount($provider->name)
            ];
        }
        
        return [
            'providers' => $stats,
            'global_health_score' => $this->calculateGlobalHealthScore($stats),
            'last_updated' => now()->toISOString(),
            'total_active_providers' => count($stats)
        ];
    }

    /**
     * Bugünkü toplam istek sayısı
     * 
     * @param string $providerName
     * @return int
     */
    private function getTodayRequestCount(string $providerName): int
    {
        $total = 0;
        for ($hour = 0; $hour < 24; $hour++) {
            $cacheKey = "provider_requests_{$providerName}_" . now()->format('Ymd') . str_pad((string)$hour, 2, '0', STR_PAD_LEFT);
            $total += Cache::get($cacheKey, 0);
        }
        return $total;
    }

    /**
     * Bugünkü toplam başarısızlık sayısı
     * 
     * @param string $providerName
     * @return int
     */
    private function getTodayFailureCount(string $providerName): int
    {
        $total = 0;
        for ($hour = 0; $hour < 24; $hour++) {
            $cacheKey = "provider_failures_{$providerName}_" . now()->format('Ymd') . str_pad((string)$hour, 2, '0', STR_PAD_LEFT);
            $total += Cache::get($cacheKey, 0);
        }
        return $total;
    }

    /**
     * Global sağlık skoru hesapla
     * 
     * @param array $providerStats
     * @return float
     */
    private function calculateGlobalHealthScore(array $providerStats): float
    {
        if (empty($providerStats)) {
            return 0;
        }
        
        $totalScore = 0;
        $count = 0;
        
        foreach ($providerStats as $stats) {
            $healthStatus = $stats['health']['status'];
            $score = match($healthStatus) {
                'healthy' => 100,
                'degraded' => 60,
                'maintenance' => 30,
                'unavailable' => 0,
                default => 50
            };
            $totalScore += $score;
            $count++;
        }
        
        return $count > 0 ? round($totalScore / $count, 1) : 0;
    }

    /**
     * Central fallback configuration'ı al
     * 
     * @return array
     */
    public function getCentralFallbackConfig(): array
    {
        $cacheKey = 'central_fallback_config';
        
        return Cache::get($cacheKey, [
            'fallback_enabled' => true,
            'max_fallback_attempts' => 3,
            'fallback_timeout' => 30,
            'preferred_provider_order' => ['OpenAI', 'Anthropic', 'DeepSeek'],
            'cost_preference' => 'balanced',
            'retry_failed_providers' => false,
            'log_fallback_decisions' => true,
            'strategies' => self::STRATEGIES,
            'health_check_interval' => 300, // 5 minutes
            'emergency_fallback_enabled' => true
        ]);
    }

    /**
     * Provider fallback order'ı al
     * 
     * @return array
     */
    public function getFallbackProviderOrder(): array
    {
        $config = $this->getCentralFallbackConfig();
        return $config['preferred_provider_order'] ?? ['OpenAI', 'Anthropic', 'DeepSeek'];
    }

    /**
     * Fallback statistics'i al
     * 
     * @return array
     */
    public function getFallbackStatistics(): array
    {
        $cacheKey = 'central_fallback_statistics';
        
        return Cache::get($cacheKey, [
            'total_requests' => 0,
            'fallback_requests' => 0,
            'successful_fallbacks' => 0,
            'failed_fallbacks' => 0,
            'fallback_rate' => 0.0,
            'fallback_success_rate' => 0.0,
            'daily_stats' => [],
            'provider_usage' => [],
            'model_usage' => []
        ]);
    }

    /**
     * Model recommendations'ı al
     * 
     * @param string $originalModel
     * @param string $requestType
     * @return array
     */
    public function getFallbackModelRecommendations(string $originalModel, string $requestType): array
    {
        $recommendations = [];
        
        // Request type'a göre öneriler
        switch ($requestType) {
            case 'general':
                $recommendations = [
                    'gpt-4o-mini',
                    'claude-3-haiku-20240307',
                    'deepseek-chat'
                ];
                break;
            case 'creative':
                $recommendations = [
                    'claude-3-5-sonnet-20241022',
                    'gpt-4o',
                    'deepseek-chat'
                ];
                break;
            case 'analytical':
                $recommendations = [
                    'gpt-4o',
                    'claude-3-5-sonnet-20241022',
                    'gpt-4o-mini'
                ];
                break;
            default:
                $recommendations = [
                    'gpt-4o-mini',
                    'claude-3-haiku-20240307'
                ];
        }
        
        // Original model'i listeden çıkar
        $recommendations = array_filter($recommendations, fn($model) => $model !== $originalModel);
        
        return array_values($recommendations);
    }

    /**
     * Test fallback configuration
     * 
     * @return array
     */
    public function testFallbackConfiguration(): array
    {
        $startTime = microtime(true);
        $config = $this->getCentralFallbackConfig();
        
        // Provider'ları test et
        $providerTests = [];
        foreach ($config['preferred_provider_order'] as $providerName) {
            $health = $this->getProviderHealthStatus($providerName);
            $providerTests[$providerName] = [
                'available' => $health['status'] === 'healthy',
                'health_status' => $health['status'],
                'response_time' => $health['avg_response_time'] ?? 0
            ];
        }
        
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        return [
            'config_valid' => true,
            'execution_time_ms' => $executionTime,
            'provider_tests' => $providerTests,
            'tested_at' => now()->toISOString()
        ];
    }

    /**
     * Clear config cache
     * 
     * @return void
     */
    public function clearConfigCache(): void
    {
        Cache::forget('central_fallback_config');
        Cache::forget('central_fallback_statistics');
        Cache::forget('global_provider_health');
    }
}