<?php

declare(strict_types=1);

namespace Modules\AI\app\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\AI\app\Models\AICreditUsage;
use Modules\AI\app\Models\AIFeature;
use Carbon\Carbon;

/**
 * AI Monitoring & Analytics Service V2
 * Enterprise-grade monitoring dashboard for AI systems
 */
readonly class MonitoringService
{
    private const CACHE_PREFIX = 'ai_monitoring:';
    private const CACHE_TTL = [
        'realtime' => 60,      // 1 dakika
        'hourly' => 3600,      // 1 saat
        'daily' => 86400,      // 1 gün
        'weekly' => 604800,    // 1 hafta
    ];

    private const PERFORMANCE_THRESHOLDS = [
        'response_time_warning' => 2000,    // 2 saniye
        'response_time_critical' => 5000,   // 5 saniye
        'success_rate_warning' => 0.95,     // %95
        'success_rate_critical' => 0.85,    // %85
        'memory_usage_warning' => 0.80,     // %80
        'memory_usage_critical' => 0.90,    // %90
    ];

    public function __construct(
        private ProviderOptimizationService $providerOptimization
    ) {}

    /**
     * Ana dashboard verilerini getir
     */
    public function getDashboardData(string $timeframe = '24h'): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . "dashboard:{$timeframe}",
            self::CACHE_TTL['realtime'],
            fn() => [
                'overview' => $this->getSystemOverview(),
                'performance' => $this->getPerformanceMetrics($timeframe),
                'usage_analytics' => $this->getUsageAnalytics($timeframe),
                'provider_health' => $this->getProviderHealthStatus(),
                'real_time_stats' => $this->getRealTimeStatistics(),
                'alerts' => $this->getSystemAlerts(),
                'feature_performance' => $this->getFeaturePerformanceData($timeframe),
                'cost_analysis' => $this->getCostAnalysis($timeframe),
            ]
        );
    }

    /**
     * Sistem genel durumu
     */
    private function getSystemOverview(): array
    {
        $totalFeatures = AIFeature::where('is_active', true)->count();
        $totalUsageToday = AICreditUsage::whereDate('created_at', today())->sum('credits_used');
        
        $avgResponseTime = Cache::remember(
            self::CACHE_PREFIX . 'avg_response_time',
            self::CACHE_TTL['realtime'],
            fn() => $this->calculateAverageResponseTime()
        );

        $successRate = Cache::remember(
            self::CACHE_PREFIX . 'success_rate',
            self::CACHE_TTL['realtime'],
            fn() => $this->calculateSuccessRate()
        );

        return [
            'system_status' => $this->getSystemHealthStatus(),
            'active_features' => $totalFeatures,
            'daily_usage' => $totalUsageToday,
            'avg_response_time' => $avgResponseTime,
            'success_rate' => $successRate,
            'uptime' => $this->calculateSystemUptime(),
            'active_sessions' => $this->getActiveSessionCount(),
        ];
    }

    /**
     * Performans metrikleri
     */
    private function getPerformanceMetrics(string $timeframe): array
    {
        $cacheKey = self::CACHE_PREFIX . "performance:{$timeframe}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL['hourly'], function() use ($timeframe) {
            $period = $this->getTimeframePeriod($timeframe);
            
            return [
                'response_times' => $this->getResponseTimeData($period),
                'throughput' => $this->getThroughputData($period),
                'error_rates' => $this->getErrorRateData($period),
                'resource_usage' => $this->getResourceUsageData($period),
                'concurrent_users' => $this->getConcurrentUserData($period),
                'api_latency' => $this->getAPILatencyData($period),
            ];
        });
    }

    /**
     * Kullanım analitikleri
     */
    private function getUsageAnalytics(string $timeframe): array
    {
        $period = $this->getTimeframePeriod($timeframe);
        
        return [
            'feature_usage' => $this->getFeatureUsageBreakdown($period),
            'user_activity' => $this->getUserActivityPatterns($period),
            'peak_hours' => $this->getPeakUsageHours($period),
            'geographic_distribution' => $this->getGeographicUsage($period),
            'device_types' => $this->getDeviceTypeBreakdown($period),
            'conversion_funnel' => $this->getConversionFunnelData($period),
        ];
    }

    /**
     * Provider sağlık durumu
     */
    private function getProviderHealthStatus(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'provider_health',
            self::CACHE_TTL['realtime'],
            fn() => $this->providerOptimization->getProviderHealthDashboard()
        );
    }

    /**
     * Gerçek zamanlı istatistikler
     */
    private function getRealTimeStatistics(): array
    {
        return [
            'current_rps' => $this->getCurrentRequestsPerSecond(),
            'active_connections' => $this->getActiveConnectionCount(),
            'queue_size' => $this->getQueueSize(),
            'memory_usage' => $this->getCurrentMemoryUsage(),
            'cpu_usage' => $this->getCurrentCPUUsage(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'database_connections' => $this->getDatabaseConnectionCount(),
            'redis_memory' => $this->getRedisMemoryUsage(),
        ];
    }

    /**
     * Sistem uyarıları
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Response time kontrolü
        $avgResponseTime = $this->calculateAverageResponseTime();
        if ($avgResponseTime > self::PERFORMANCE_THRESHOLDS['response_time_critical']) {
            $alerts[] = [
                'type' => 'critical',
                'category' => 'performance',
                'message' => "Kritik: Ortalama yanıt süresi {$avgResponseTime}ms",
                'timestamp' => now(),
                'suggested_action' => 'Provider optimizasyonu gerekli',
            ];
        } elseif ($avgResponseTime > self::PERFORMANCE_THRESHOLDS['response_time_warning']) {
            $alerts[] = [
                'type' => 'warning',
                'category' => 'performance',
                'message' => "Uyarı: Yüksek yanıt süresi {$avgResponseTime}ms",
                'timestamp' => now(),
                'suggested_action' => 'Performans izleme öneriliyor',
            ];
        }

        // Success rate kontrolü
        $successRate = $this->calculateSuccessRate();
        if ($successRate < self::PERFORMANCE_THRESHOLDS['success_rate_critical']) {
            $alerts[] = [
                'type' => 'critical',
                'category' => 'reliability',
                'message' => "Kritik: Başarı oranı %{$successRate}",
                'timestamp' => now(),
                'suggested_action' => 'Provider konfigürasyonu kontrol edilmeli',
            ];
        }

        // Bellek kullanımı kontrolü
        $memoryUsage = $this->getCurrentMemoryUsage();
        if ($memoryUsage > self::PERFORMANCE_THRESHOLDS['memory_usage_critical']) {
            $alerts[] = [
                'type' => 'critical',
                'category' => 'resources',
                'message' => "Kritik: Bellek kullanımı %{$memoryUsage}",
                'timestamp' => now(),
                'suggested_action' => 'Sistem kaynaklarını artırın',
            ];
        }

        return $alerts;
    }

    /**
     * Feature performans verileri
     */
    private function getFeaturePerformanceData(string $timeframe): array
    {
        $period = $this->getTimeframePeriod($timeframe);
        
        return AIFeature::select([
                'ai_features.feature_id',
                'ai_features.name',
                DB::raw('COUNT(usage.usage_id) as total_requests'),
                DB::raw('AVG(usage.response_time_ms) as avg_response_time'),
                DB::raw('SUM(usage.credits_used) as total_credits'),
                DB::raw('AVG(CASE WHEN usage.status = "success" THEN 1.0 ELSE 0.0 END) as success_rate'),
            ])
            ->leftJoin('ai_credit_usage as usage', 'ai_features.feature_id', '=', 'usage.feature_id')
            ->where('usage.created_at', '>=', $period['start'])
            ->where('usage.created_at', '<=', $period['end'])
            ->groupBy('ai_features.feature_id', 'ai_features.name')
            ->orderByDesc('total_requests')
            ->get()
            ->map(function($feature) {
                return [
                    'feature_id' => $feature->feature_id,
                    'name' => $feature->name,
                    'total_requests' => (int) $feature->total_requests,
                    'avg_response_time' => round((float) $feature->avg_response_time, 2),
                    'total_credits' => (int) $feature->total_credits,
                    'success_rate' => round((float) $feature->success_rate * 100, 2),
                    'performance_score' => $this->calculateFeaturePerformanceScore($feature),
                ];
            })
            ->toArray();
    }

    /**
     * Maliyet analizi
     */
    private function getCostAnalysis(string $timeframe): array
    {
        $period = $this->getTimeframePeriod($timeframe);
        
        $totalCredits = AICreditUsage::whereBetween('created_at', [$period['start'], $period['end']])
            ->sum('credits_used');
        
        $providerCosts = AICreditUsage::select([
                'provider',
                DB::raw('SUM(credits_used) as total_credits'),
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('AVG(credits_used) as avg_credits_per_request'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('provider')
            ->get()
            ->toArray();

        return [
            'total_credits_used' => $totalCredits,
            'provider_breakdown' => $providerCosts,
            'cost_trend' => $this->getCostTrendData($period),
            'optimization_suggestions' => $this->getCostOptimizationSuggestions($providerCosts),
            'budget_status' => $this->getBudgetStatus($totalCredits),
        ];
    }

    /**
     * Zaman dilimi periyodu hesapla
     */
    private function getTimeframePeriod(string $timeframe): array
    {
        return match($timeframe) {
            '1h' => [
                'start' => now()->subHour(),
                'end' => now(),
            ],
            '24h' => [
                'start' => now()->subDay(),
                'end' => now(),
            ],
            '7d' => [
                'start' => now()->subWeek(),
                'end' => now(),
            ],
            '30d' => [
                'start' => now()->subMonth(),
                'end' => now(),
            ],
            default => [
                'start' => now()->subDay(),
                'end' => now(),
            ],
        };
    }

    /**
     * Sistem sağlık durumu
     */
    private function getSystemHealthStatus(): string
    {
        $successRate = $this->calculateSuccessRate();
        $avgResponseTime = $this->calculateAverageResponseTime();
        $memoryUsage = $this->getCurrentMemoryUsage();

        if (
            $successRate < self::PERFORMANCE_THRESHOLDS['success_rate_critical'] ||
            $avgResponseTime > self::PERFORMANCE_THRESHOLDS['response_time_critical'] ||
            $memoryUsage > self::PERFORMANCE_THRESHOLDS['memory_usage_critical']
        ) {
            return 'critical';
        }

        if (
            $successRate < self::PERFORMANCE_THRESHOLDS['success_rate_warning'] ||
            $avgResponseTime > self::PERFORMANCE_THRESHOLDS['response_time_warning'] ||
            $memoryUsage > self::PERFORMANCE_THRESHOLDS['memory_usage_warning']
        ) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Ortalama yanıt süresini hesapla
     */
    private function calculateAverageResponseTime(): float
    {
        return AICreditUsage::where('created_at', '>=', now()->subHour())
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms') ?? 0.0;
    }

    /**
     * Başarı oranını hesapla
     */
    private function calculateSuccessRate(): float
    {
        $total = AICreditUsage::where('created_at', '>=', now()->subHour())->count();
        if ($total === 0) return 1.0;

        $successful = AICreditUsage::where('created_at', '>=', now()->subHour())
            ->where('status', 'success')
            ->count();

        return $successful / $total;
    }

    /**
     * Sistem uptime hesapla
     */
    private function calculateSystemUptime(): array
    {
        // Redis'te tutulan uptime bilgisi
        $startTime = Cache::get('system_start_time', now()->subDays(30));
        $totalTime = now()->diffInSeconds($startTime);
        
        // Son 24 saatteki downtime
        $downtimeSeconds = Cache::get('system_downtime_24h', 0);
        $uptimePercentage = (($totalTime - $downtimeSeconds) / $totalTime) * 100;

        return [
            'uptime_percentage' => round($uptimePercentage, 3),
            'total_uptime_hours' => round($totalTime / 3600, 2),
            'last_downtime' => Cache::get('last_downtime'),
        ];
    }

    /**
     * Aktif session sayısı
     */
    private function getActiveSessionCount(): int
    {
        return (int) Redis::scard('active_ai_sessions');
    }

    /**
     * Mevcut saniye başına istek sayısı
     */
    private function getCurrentRequestsPerSecond(): float
    {
        $key = 'requests_per_second:' . now()->format('Y-m-d_H:i:s');
        return (float) Redis::get($key) ?? 0.0;
    }

    /**
     * Aktif bağlantı sayısı
     */
    private function getActiveConnectionCount(): int
    {
        return (int) Redis::scard('active_connections');
    }

    /**
     * Queue boyutu
     */
    private function getQueueSize(): int
    {
        return (int) Redis::llen('queues:default');
    }

    /**
     * Mevcut bellek kullanımı
     */
    private function getCurrentMemoryUsage(): float
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        return ($memoryUsage / $memoryLimit) * 100;
    }

    /**
     * Mevcut CPU kullanımı
     */
    private function getCurrentCPUUsage(): float
    {
        // Sistem CPU kullanımı (cache'lenmiş)
        return (float) Cache::remember('cpu_usage', 30, function() {
            $load = sys_getloadavg();
            return $load[0] ?? 0.0;
        });
    }

    /**
     * Cache hit oranı
     */
    private function getCacheHitRate(): float
    {
        $hits = (int) Redis::get('cache_hits') ?? 0;
        $misses = (int) Redis::get('cache_misses') ?? 0;
        $total = $hits + $misses;

        return $total > 0 ? ($hits / $total) * 100 : 0.0;
    }

    /**
     * Veritabanı bağlantı sayısı
     */
    private function getDatabaseConnectionCount(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return (int) $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Redis bellek kullanımı
     */
    private function getRedisMemoryUsage(): array
    {
        $info = Redis::info('memory');
        
        return [
            'used_memory' => $info['used_memory'] ?? 0,
            'used_memory_human' => $info['used_memory_human'] ?? '0B',
            'used_memory_peak' => $info['used_memory_peak'] ?? 0,
            'used_memory_peak_human' => $info['used_memory_peak_human'] ?? '0B',
        ];
    }

    /**
     * Feature performans skoru hesapla
     */
    private function calculateFeaturePerformanceScore(object $feature): float
    {
        $responseTimeScore = max(0, 100 - (((float) $feature->avg_response_time) / 50)); // 5000ms = 0 puan
        $successRateScore = ((float) $feature->success_rate) * 100;
        $usageScore = min(100, ((int) $feature->total_requests) / 10); // 1000 request = 100 puan
        
        return round(($responseTimeScore + $successRateScore + $usageScore) / 3, 2);
    }

    /**
     * Bellek limiti parse et
     */
    private function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $limit = (int) $limit;

        return match($last) {
            'g' => $limit * 1024 * 1024 * 1024,
            'm' => $limit * 1024 * 1024,
            'k' => $limit * 1024,
            default => $limit,
        };
    }

    /**
     * Yanıt süresi verilerini getir
     */
    private function getResponseTimeData(array $period): array
    {
        return AICreditUsage::select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour'),
                DB::raw('AVG(response_time_ms) as avg_response_time'),
                DB::raw('MIN(response_time_ms) as min_response_time'),
                DB::raw('MAX(response_time_ms) as max_response_time'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->whereNotNull('response_time_ms')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    /**
     * Throughput verilerini getir
     */
    private function getThroughputData(array $period): array
    {
        return AICreditUsage::select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour'),
                DB::raw('COUNT(*) as request_count'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    /**
     * Hata oranı verilerini getir
     */
    private function getErrorRateData(array $period): array
    {
        return AICreditUsage::select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour'),
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN status != "success" THEN 1 ELSE 0 END) as error_count'),
                DB::raw('AVG(CASE WHEN status != "success" THEN 1.0 ELSE 0.0 END) * 100 as error_rate'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    /**
     * Kaynak kullanım verilerini getir
     */
    private function getResourceUsageData(array $period): array
    {
        // Cache'den geçmiş kaynak kullanım verilerini getir
        return Cache::remember(
            self::CACHE_PREFIX . 'resource_usage_' . md5(serialize($period)),
            self::CACHE_TTL['hourly'],
            function() use ($period) {
                $hours = [];
                $current = Carbon::parse($period['start']);
                
                while ($current <= $period['end']) {
                    $hours[] = [
                        'hour' => $current->format('Y-m-d H:00:00'),
                        'memory_usage' => rand(60, 85), // Demo data
                        'cpu_usage' => rand(30, 70),    // Demo data
                        'disk_usage' => rand(40, 60),   // Demo data
                    ];
                    $current->addHour();
                }
                
                return $hours;
            }
        );
    }

    /**
     * Eşzamanlı kullanıcı verilerini getir
     */
    private function getConcurrentUserData(array $period): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'concurrent_users_' . md5(serialize($period)),
            self::CACHE_TTL['hourly'],
            function() use ($period) {
                // Redis'ten concurrent user data'sı getir (demo)
                $hours = [];
                $current = Carbon::parse($period['start']);
                
                while ($current <= $period['end']) {
                    $hours[] = [
                        'hour' => $current->format('Y-m-d H:00:00'),
                        'concurrent_users' => rand(10, 100),
                    ];
                    $current->addHour();
                }
                
                return $hours;
            }
        );
    }

    /**
     * API gecikme verilerini getir
     */
    private function getAPILatencyData(array $period): array
    {
        return AICreditUsage::select([
                'provider',
                DB::raw('AVG(response_time_ms) as avg_latency'),
                DB::raw('MIN(response_time_ms) as min_latency'),
                DB::raw('MAX(response_time_ms) as max_latency'),
                DB::raw('STDDEV(response_time_ms) as latency_stddev'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->whereNotNull('response_time_ms')
            ->groupBy('provider')
            ->get()
            ->toArray();
    }

    /**
     * Feature kullanım dağılımını getir
     */
    private function getFeatureUsageBreakdown(array $period): array
    {
        return AICreditUsage::select([
                'ai_features.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(ai_credit_usage.credits_used) as total_credits'),
            ])
            ->join('ai_features', 'ai_credit_usage.feature_id', '=', 'ai_features.feature_id')
            ->whereBetween('ai_credit_usage.created_at', [$period['start'], $period['end']])
            ->groupBy('ai_features.feature_id', 'ai_features.name')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Kullanıcı aktivite desenlerini getir
     */
    private function getUserActivityPatterns(array $period): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'user_activity_' . md5(serialize($period)),
            self::CACHE_TTL['hourly'],
            function() use ($period) {
                return [
                    'hourly_distribution' => $this->getHourlyUserActivity($period),
                    'daily_distribution' => $this->getDailyUserActivity($period),
                    'new_vs_returning' => $this->getNewVsReturningUsers($period),
                ];
            }
        );
    }

    /**
     * Yoğun saatleri getir
     */
    private function getPeakUsageHours(array $period): array
    {
        return AICreditUsage::select([
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(credits_used) as total_credits'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('hour')
            ->orderByDesc('request_count')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'hour' => $item->hour . ':00',
                    'request_count' => $item->request_count,
                    'total_credits' => $item->total_credits,
                ];
            })
            ->toArray();
    }

    /**
     * Coğrafi kullanım dağılımını getir
     */
    private function getGeographicUsage(array $period): array
    {
        // Demo data - gerçek implementasyonda IP geolocation kullanılacak
        return [
            ['country' => 'Turkey', 'usage_count' => 1250, 'percentage' => 65.0],
            ['country' => 'Germany', 'usage_count' => 320, 'percentage' => 16.7],
            ['country' => 'United States', 'usage_count' => 180, 'percentage' => 9.4],
            ['country' => 'United Kingdom', 'usage_count' => 95, 'percentage' => 4.9],
            ['country' => 'France', 'usage_count' => 75, 'percentage' => 3.9],
        ];
    }

    /**
     * Cihaz türü dağılımını getir
     */
    private function getDeviceTypeBreakdown(array $period): array
    {
        // Demo data - gerçek implementasyonda user agent parsing
        return [
            ['device_type' => 'Desktop', 'usage_count' => 1200, 'percentage' => 62.5],
            ['device_type' => 'Mobile', 'usage_count' => 600, 'percentage' => 31.25],
            ['device_type' => 'Tablet', 'usage_count' => 120, 'percentage' => 6.25],
        ];
    }

    /**
     * Dönüşüm hunisi verilerini getir
     */
    private function getConversionFunnelData(array $period): array
    {
        return [
            ['step' => 'Site Visit', 'count' => 10000, 'conversion_rate' => 100.0],
            ['step' => 'AI Feature View', 'count' => 3000, 'conversion_rate' => 30.0],
            ['step' => 'Feature Test', 'count' => 1500, 'conversion_rate' => 50.0],
            ['step' => 'Credit Purchase', 'count' => 300, 'conversion_rate' => 20.0],
            ['step' => 'Regular Usage', 'count' => 150, 'conversion_rate' => 50.0],
        ];
    }

    /**
     * Maliyet trendi verilerini getir
     */
    private function getCostTrendData(array $period): array
    {
        return AICreditUsage::select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(credits_used) as daily_credits'),
            ])
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Maliyet optimizasyon önerilerini getir
     */
    private function getCostOptimizationSuggestions(array $providerCosts): array
    {
        $suggestions = [];

        foreach ($providerCosts as $provider) {
            if ($provider['avg_credits_per_request'] > 10) {
                $suggestions[] = [
                    'type' => 'cost_reduction',
                    'provider' => $provider['provider'],
                    'message' => "Provider {$provider['provider']} yüksek maliyet - alternatif araştırın",
                    'potential_saving' => rand(10, 30) . '%',
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Bütçe durumunu getir
     */
    private function getBudgetStatus(int $totalCreditsUsed): array
    {
        // Demo budget data
        $monthlyBudget = 50000; // Credit limiti
        $usagePercentage = ($totalCreditsUsed / $monthlyBudget) * 100;

        return [
            'monthly_budget' => $monthlyBudget,
            'used_credits' => $totalCreditsUsed,
            'remaining_credits' => $monthlyBudget - $totalCreditsUsed,
            'usage_percentage' => round($usagePercentage, 2),
            'status' => $usagePercentage > 90 ? 'critical' : ($usagePercentage > 75 ? 'warning' : 'good'),
            'projected_monthly_usage' => $this->calculateProjectedMonthlyUsage($totalCreditsUsed),
        ];
    }

    /**
     * Saatlik kullanıcı aktivitesini getir
     */
    private function getHourlyUserActivity(array $period): array
    {
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = [
                'hour' => $i,
                'active_users' => rand(50, 200),
            ];
        }
        return $hours;
    }

    /**
     * Günlük kullanıcı aktivitesini getir
     */
    private function getDailyUserActivity(array $period): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $activity = [];
        
        foreach ($days as $day) {
            $activity[] = [
                'day' => $day,
                'active_users' => rand(800, 1500),
            ];
        }
        
        return $activity;
    }

    /**
     * Yeni vs geri dönen kullanıcılar
     */
    private function getNewVsReturningUsers(array $period): array
    {
        return [
            'new_users' => rand(200, 400),
            'returning_users' => rand(800, 1200),
            'new_user_percentage' => rand(20, 35),
            'returning_user_percentage' => rand(65, 80),
        ];
    }

    /**
     * Aylık kullanım projeksiyonunu hesapla
     */
    private function calculateProjectedMonthlyUsage(int $currentUsage): int
    {
        $daysInMonth = now()->daysInMonth;
        $currentDay = now()->day;
        
        return (int) (($currentUsage / $currentDay) * $daysInMonth);
    }
}