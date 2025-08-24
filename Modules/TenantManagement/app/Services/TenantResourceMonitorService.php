<?php

namespace Modules\TenantManagement\App\Services;

use Modules\TenantManagement\App\Models\TenantResourceLimit;
use Modules\TenantManagement\App\Models\TenantUsageLog;
use Modules\TenantManagement\App\Models\TenantRateLimit;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class TenantResourceMonitorService
{
    private const CACHE_PREFIX = 'tenant_monitor:';
    private const CACHE_TTL = 300; // 5 dakika

    /**
     * Tenant'ın gerçek zamanlı kaynak kullanımını al
     */
    public function getCurrentUsage(int $tenantId): array
    {
        $cacheKey = self::CACHE_PREFIX . "current_usage:{$tenantId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return [];
            }

            return [
                'api' => $this->getApiUsage($tenantId),
                'database' => $this->getDatabaseUsage($tenantId),
                'cache' => $this->getCacheUsage($tenantId),
                'storage' => $this->getStorageUsage($tenantId),
                'ai' => $this->getAiUsage($tenantId),
                'cpu' => $this->getCpuUsage($tenantId),
                'memory' => $this->getMemoryUsage($tenantId),
                'connections' => $this->getConnectionUsage($tenantId),
            ];
        });
    }

    /**
     * API kullanımını hesapla
     */
    private function getApiUsage(int $tenantId): array
    {
        $hourlyKey = "tenant:{$tenantId}:api:hourly:" . Carbon::now()->format('Y-m-d-H');
        $dailyKey = "tenant:{$tenantId}:api:daily:" . Carbon::now()->format('Y-m-d');
        $monthlyKey = "tenant:{$tenantId}:api:monthly:" . Carbon::now()->format('Y-m');

        try {
            $redis = Redis::connection();
            
            return [
                'hourly' => (int) $redis->get($hourlyKey) ?: 0,
                'daily' => (int) $redis->get($dailyKey) ?: 0,
                'monthly' => (int) $redis->get($monthlyKey) ?: 0,
                'concurrent' => (int) $redis->get("tenant:{$tenantId}:api:concurrent") ?: 0,
            ];
        } catch (\Exception $e) {
            return ['hourly' => 0, 'daily' => 0, 'monthly' => 0, 'concurrent' => 0];
        }
    }

    /**
     * Veritabanı kullanımını hesapla
     */
    private function getDatabaseUsage(int $tenantId): array
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant || !$tenant->tenancy_db_name) {
                return ['queries_per_hour' => 0, 'active_connections' => 0, 'avg_response_time' => 0];
            }

            // Son 1 saatteki log kayıtlarından veritabanı kullanımını al
            $hourlyLogs = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('resource_type', 'database')
                ->where('recorded_at', '>=', Carbon::now()->subHour())
                ->get();

            return [
                'queries_per_hour' => $hourlyLogs->sum('db_queries'),
                'active_connections' => $hourlyLogs->max('active_connections') ?: 0,
                'avg_response_time' => round($hourlyLogs->avg('response_time_ms'), 2),
                'total_queries' => $hourlyLogs->sum('usage_count'),
            ];
        } catch (\Exception $e) {
            return ['queries_per_hour' => 0, 'active_connections' => 0, 'avg_response_time' => 0, 'total_queries' => 0];
        }
    }

    /**
     * Cache kullanımını hesapla
     */
    private function getCacheUsage(int $tenantId): array
    {
        try {
            $redis = Redis::connection();
            $pattern = "tenant_{$tenantId}:*";
            $keys = $redis->keys($pattern);
            
            $totalSize = 0;
            $keyCount = count($keys);
            
            if ($keyCount > 0) {
                // Memory usage hesaplama (tahmini)
                foreach (array_slice($keys, 0, 100) as $key) { // İlk 100 key için sample
                    try {
                        $size = $redis->memory('usage', $key);
                        $totalSize += $size;
                    } catch (\Exception $e) {
                        // Ignore individual key errors
                    }
                }
                
                // Ortalama key size * toplam key sayısı
                if ($keyCount > 100) {
                    $avgSize = $totalSize / 100;
                    $totalSize = $avgSize * $keyCount;
                }
            }

            return [
                'memory_usage_mb' => round($totalSize / 1024 / 1024, 2),
                'key_count' => $keyCount,
                'hit_rate' => $this->getCacheHitRate($tenantId),
            ];
        } catch (\Exception $e) {
            return ['memory_usage_mb' => 0, 'key_count' => 0, 'hit_rate' => 0];
        }
    }

    /**
     * Cache hit rate hesapla
     */
    private function getCacheHitRate(int $tenantId): float
    {
        try {
            $redis = Redis::connection();
            $hits = (int) $redis->get("tenant:{$tenantId}:cache:hits") ?: 0;
            $misses = (int) $redis->get("tenant:{$tenantId}:cache:misses") ?: 0;
            
            $total = $hits + $misses;
            return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Depolama kullanımını hesapla
     */
    private function getStorageUsage(int $tenantId): array
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return ['total_mb' => 0, 'files_count' => 0, 'uploads_today' => 0];
            }

            $storagePath = storage_path("tenant{$tenantId}");
            $publicPath = public_path("storage/tenant{$tenantId}");
            
            $totalSize = 0;
            $fileCount = 0;
            
            // Storage dizini
            if (is_dir($storagePath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($storagePath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $totalSize += $file->getSize();
                        $fileCount++;
                    }
                }
            }
            
            // Public storage dizini
            if (is_dir($publicPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $totalSize += $file->getSize();
                        $fileCount++;
                    }
                }
            }

            // Bugünkü upload sayısını log'lardan al
            $uploadsToday = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('resource_type', 'storage')
                ->where('recorded_at', '>=', Carbon::today())
                ->sum('usage_count');

            return [
                'total_mb' => round($totalSize / 1024 / 1024, 2),
                'files_count' => $fileCount,
                'uploads_today' => $uploadsToday,
            ];
        } catch (\Exception $e) {
            return ['total_mb' => 0, 'files_count' => 0, 'uploads_today' => 0];
        }
    }

    /**
     * AI kullanımını hesapla
     */
    private function getAiUsage(int $tenantId): array
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return ['tokens_used_today' => 0, 'tokens_remaining' => 0, 'requests_today' => 0];
            }

            // AI kullanım loglarından bugünkü kullanımı al
            $todayLogs = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('resource_type', 'ai')
                ->where('recorded_at', '>=', Carbon::today())
                ->get();

            $tokensUsedToday = $todayLogs->sum('usage_count');
            $requestsToday = $todayLogs->count();

            return [
                'tokens_used_today' => $tokensUsedToday,
                'tokens_remaining' => max(0, $tenant->ai_tokens_balance - $tokensUsedToday),
                'requests_today' => $requestsToday,
                'monthly_usage' => $tenant->ai_tokens_used_this_month ?: 0,
                'monthly_limit' => $tenant->ai_monthly_token_limit ?: 0,
            ];
        } catch (\Exception $e) {
            return ['tokens_used_today' => 0, 'tokens_remaining' => 0, 'requests_today' => 0, 'monthly_usage' => 0, 'monthly_limit' => 0];
        }
    }

    /**
     * CPU kullanımını hesapla (simulated)
     */
    private function getCpuUsage(int $tenantId): array
    {
        // Gerçek CPU kullanımı sistem seviyesinde monitoring gerektirir
        // Bu basit bir simülasyon
        try {
            $recent = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('recorded_at', '>=', Carbon::now()->subMinutes(5))
                ->avg('cpu_usage_percent');

            return [
                'current_percent' => round($recent ?: 0, 2),
                'avg_last_hour' => round(
                    TenantUsageLog::where('tenant_id', $tenantId)
                        ->where('recorded_at', '>=', Carbon::now()->subHour())
                        ->avg('cpu_usage_percent') ?: 0, 
                    2
                ),
            ];
        } catch (\Exception $e) {
            return ['current_percent' => 0, 'avg_last_hour' => 0];
        }
    }

    /**
     * Memory kullanımını hesapla
     */
    private function getMemoryUsage(int $tenantId): array
    {
        try {
            $recent = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('recorded_at', '>=', Carbon::now()->subMinutes(5))
                ->avg('memory_usage_mb');

            return [
                'current_mb' => round($recent ?: 0, 2),
                'peak_last_hour' => round(
                    TenantUsageLog::where('tenant_id', $tenantId)
                        ->where('recorded_at', '>=', Carbon::now()->subHour())
                        ->max('memory_usage_mb') ?: 0, 
                    2
                ),
            ];
        } catch (\Exception $e) {
            return ['current_mb' => 0, 'peak_last_hour' => 0];
        }
    }

    /**
     * Bağlantı kullanımını hesapla
     */
    private function getConnectionUsage(int $tenantId): array
    {
        try {
            $redis = Redis::connection();
            $activeConnections = (int) $redis->get("tenant:{$tenantId}:connections:active") ?: 0;
            
            $peakToday = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('recorded_at', '>=', Carbon::today())
                ->max('active_connections') ?: 0;

            return [
                'active' => $activeConnections,
                'peak_today' => $peakToday,
            ];
        } catch (\Exception $e) {
            return ['active' => 0, 'peak_today' => 0];
        }
    }

    /**
     * Tenant için limit kontrolleri yap
     */
    public function checkLimits(int $tenantId): array
    {
        $usage = $this->getCurrentUsage($tenantId);
        $limits = TenantResourceLimit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get()
            ->keyBy('resource_type');

        $violations = [];

        foreach ($usage as $resourceType => $resourceUsage) {
            if (!isset($limits[$resourceType])) {
                continue;
            }

            $limit = $limits[$resourceType];
            
            // Her limit türü için kontrol
            foreach (['hourly', 'daily', 'monthly'] as $period) {
                $usageKey = $period;
                $limitKey = $period . '_limit';
                
                if (isset($resourceUsage[$usageKey]) && $limit->{$limitKey}) {
                    $check = $limit->checkLimit($resourceUsage[$usageKey], $period);
                    
                    if ($check['exceeded']) {
                        $violations[] = [
                            'resource_type' => $resourceType,
                            'period' => $period,
                            'usage' => $resourceUsage[$usageKey],
                            'limit' => $check['limit'],
                            'action' => $check['action'],
                            'severity' => $this->getSeverity($resourceUsage[$usageKey], $check['limit']),
                        ];
                    }
                }
            }
        }

        return [
            'usage' => $usage,
            'limits' => $limits->toArray(),
            'violations' => $violations,
            'status' => empty($violations) ? 'normal' : 'exceeded',
        ];
    }

    /**
     * İhlal şiddetini belirle
     */
    private function getSeverity(int $usage, int $limit): string
    {
        $ratio = $usage / $limit;
        
        if ($ratio >= 2.0) return 'critical';
        if ($ratio >= 1.5) return 'high';
        if ($ratio >= 1.2) return 'medium';
        return 'low';
    }

    /**
     * Kullanım verilerini kaydet
     */
    public function recordUsage(int $tenantId, string $resourceType, array $metrics): void
    {
        try {
            TenantUsageLog::recordUsage($tenantId, $resourceType, $metrics);
            
            // Cache'i güncelle
            $cacheKey = self::CACHE_PREFIX . "current_usage:{$tenantId}";
            Cache::forget($cacheKey);
            
        } catch (\Exception $e) {
            \Log::error("Failed to record tenant usage", [
                'tenant_id' => $tenantId,
                'resource_type' => $resourceType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sistem geneli özet istatistikler
     */
    public function getSystemSummary(): array
    {
        return Cache::remember(self::CACHE_PREFIX . 'system_summary', self::CACHE_TTL, function() {
            return [
                'total_tenants' => Tenant::count(),
                'active_tenants' => Tenant::where('is_active', true)->count(),
                'usage_logs_24h' => TenantUsageLog::where('recorded_at', '>=', Carbon::now()->subDay())->count(),
                'critical_violations' => TenantUsageLog::where('status', 'critical')
                    ->where('recorded_at', '>=', Carbon::now()->subDay())
                    ->count(),
                'system_wide_stats' => TenantUsageLog::getSystemWideSummary(24),
            ];
        });
    }

    /**
     * Cache'leri temizle
     */
    public function clearCache(int $tenantId = null): void
    {
        if ($tenantId) {
            $patterns = [
                self::CACHE_PREFIX . "current_usage:{$tenantId}",
                self::CACHE_PREFIX . "limits:{$tenantId}",
            ];
            
            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        } else {
            // Tüm monitoring cache'ini temizle
            Cache::tags(['tenant_monitoring'])->flush();
        }
    }
}