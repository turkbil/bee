<?php

namespace Modules\TenantManagement\App\Services;

use App\Models\Tenant;
use Modules\TenantManagement\App\Models\TenantUsageLog;
use Modules\TenantManagement\App\Models\TenantResourceLimit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RealTimeResourceTracker
{
    /**
     * Gerçek sistem kaynaklarını takip et
     */
    public function trackRealResourceUsage($tenantId = null)
    {
        try {
            $tenant = $tenantId ? Tenant::find($tenantId) : $this->getCurrentTenant();
            
            if (!$tenant) {
                return false;
            }

            // Gerçek sistem metrikleri topla
            $metrics = $this->collectRealMetrics($tenant);
            
            // Veritabanına kaydet
            $this->logRealUsage($tenant->id, $metrics);
            
            return true;
        } catch (\Exception $e) {
            Log::error('RealTimeResourceTracker error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gerçek sistem metriklerini topla
     */
    private function collectRealMetrics(Tenant $tenant)
    {
        return [
            'api_requests' => $this->getRealApiRequests($tenant),
            'database_queries' => $this->getRealDatabaseQueries($tenant),
            'cache_operations' => $this->getRealCacheOperations($tenant),
            'ai_operations' => $this->getRealAiOperations($tenant),
            'cpu_usage' => $this->getRealCpuUsage(),
            'memory_usage' => $this->getRealMemoryUsage(),
            'response_time' => $this->getRealResponseTime($tenant),
            'active_connections' => $this->getRealActiveConnections($tenant),
            'storage_usage' => $this->getRealStorageUsage($tenant)
        ];
    }

    /**
     * Gerçek API isteklerini say
     */
    private function getRealApiRequests(Tenant $tenant)
    {
        try {
            // Laravel log dosyasından gerçek API isteklerini say
            $logPath = storage_path('logs/laravel.log');
            $tenantDomain = $tenant->domains->first()->domain ?? '';
            
            if (!file_exists($logPath) || empty($tenantDomain)) {
                return 0;
            }

            $currentHour = Carbon::now()->format('Y-m-d H');
            $command = "grep '{$currentHour}' {$logPath} | grep '{$tenantDomain}' | grep -c 'request'";
            $result = shell_exec($command);
            
            return (int) trim($result ?: '0');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek veritabanı sorgularını say
     */
    private function getRealDatabaseQueries(Tenant $tenant)
    {
        try {
            // Query log'dan gerçek sorgu sayısını al
            $cacheKey = "db_queries_{$tenant->id}_" . Carbon::now()->format('Y-m-d-H');
            return Cache::get($cacheKey, 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek cache işlemlerini say
     */
    private function getRealCacheOperations(Tenant $tenant)
    {
        try {
            $cacheKey = "cache_ops_{$tenant->id}_" . Carbon::now()->format('Y-m-d-H');
            return Cache::get($cacheKey, 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek AI işlemlerini say
     */
    private function getRealAiOperations(Tenant $tenant)
    {
        try {
            // AI modülünden gerçek kullanım verilerini al
            if (class_exists('\Modules\AI\App\Models\AIUsage')) {
                return \Modules\AI\App\Models\AIUsage::where('tenant_id', $tenant->id)
                    ->where('created_at', '>=', Carbon::now()->startOfHour())
                    ->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek CPU kullanımı
     */
    private function getRealCpuUsage()
    {
        try {
            // Linux/Unix sistemlerde gerçek CPU kullanımı
            if (PHP_OS_FAMILY === 'Linux' || PHP_OS_FAMILY === 'Darwin') {
                $load = sys_getloadavg();
                return round(($load[0] / 4) * 100, 2); // 4 core varsayımı
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek bellek kullanımı
     */
    private function getRealMemoryUsage()
    {
        try {
            return round(memory_get_usage(true) / 1024 / 1024, 2); // MB cinsinden
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek yanıt süresi
     */
    private function getRealResponseTime(Tenant $tenant)
    {
        try {
            $cacheKey = "response_time_{$tenant->id}_" . Carbon::now()->format('Y-m-d-H');
            return Cache::get($cacheKey, 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek aktif bağlantılar
     */
    private function getRealActiveConnections(Tenant $tenant)
    {
        try {
            return DB::select("SHOW STATUS LIKE 'Threads_connected'")[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gerçek depolama kullanımı
     */
    private function getRealStorageUsage(Tenant $tenant)
    {
        try {
            $tenantPath = storage_path("app/tenants/{$tenant->id}");
            if (!is_dir($tenantPath)) {
                return 0;
            }
            
            $size = $this->getDirectorySize($tenantPath);
            return round($size / 1024 / 1024, 2); // MB cinsinden
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Dizin boyutunu hesapla
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    /**
     * Gerçek kullanım verilerini logla
     */
    private function logRealUsage($tenantId, $metrics)
    {
        // Her kaynak türü için ayrı log kaydı oluştur
        $resourceTypes = [
            'api' => ['usage_count' => $metrics['api_requests'], 'api_requests' => $metrics['api_requests']],
            'database' => ['usage_count' => $metrics['database_queries'], 'db_queries' => $metrics['database_queries']],
            'cache' => ['usage_count' => $metrics['cache_operations']],
            'ai' => ['usage_count' => $metrics['ai_operations']],
            'system' => ['usage_count' => 1] // Sistem metrikleri için
        ];

        foreach ($resourceTypes as $type => $typeMetrics) {
            TenantUsageLog::create([
                'tenant_id' => $tenantId,
                'resource_type' => $type,
                'usage_amount' => $typeMetrics['usage_count'],
                'usage_count' => $typeMetrics['usage_count'],
                'api_requests' => $typeMetrics['api_requests'] ?? 0,
                'db_queries' => $typeMetrics['db_queries'] ?? 0,
                'cpu_usage_percent' => $metrics['cpu_usage'],
                'memory_usage_mb' => $metrics['memory_usage'],
                'response_time_ms' => $metrics['response_time'],
                'active_connections' => $metrics['active_connections'],
                'storage_usage_mb' => $metrics['storage_usage'],
                'status' => $this->determineStatus($metrics),
                'recorded_at' => Carbon::now()
            ]);
        }
    }

    /**
     * Gerçek verilere göre status belirle
     */
    private function determineStatus($metrics)
    {
        if ($metrics['cpu_usage'] > 80 || $metrics['memory_usage'] > 1024) {
            return 'critical';
        } elseif ($metrics['cpu_usage'] > 60 || $metrics['memory_usage'] > 512) {
            return 'warning';
        }
        return 'normal';
    }

    /**
     * Mevcut tenant'ı al
     */
    private function getCurrentTenant()
    {
        // Tenant context'inden mevcut tenant'ı al
        if (function_exists('tenant')) {
            return tenant();
        }
        
        // Fallback: İlk aktif tenant'ı al
        return Tenant::where('is_active', true)->first();
    }

    /**
     * Tüm tenant'lar için kaynak kullanımını takip et
     */
    public function trackAllTenants()
    {
        $tenants = Tenant::where('is_active', true)->get();
        
        foreach ($tenants as $tenant) {
            $this->trackRealResourceUsage($tenant->id);
        }
        
        Log::info('Real resource tracking completed for ' . $tenants->count() . ' tenants');
    }

    /**
     * Kaynak limitlerini gerçek verilerle oluştur
     */
    public function setupRealResourceLimits()
    {
        $tenants = Tenant::where('is_active', true)->get();
        
        foreach ($tenants as $tenant) {
            // Gerçekçi limitler - production ortamına uygun
            $limits = [
                'api' => ['limit' => 10000, 'period' => 'hourly', 'action' => 'throttle'],
                'database' => ['limit' => 50000, 'period' => 'hourly', 'action' => 'warn'],
                'cache' => ['limit' => 100000, 'period' => 'hourly', 'action' => 'warn'],
                'ai' => ['limit' => 1000, 'period' => 'daily', 'action' => 'block'],
                'storage' => ['limit' => 5000, 'period' => 'monthly', 'action' => 'warn'], // 5GB
                'cpu' => ['limit' => 80, 'period' => 'continuous', 'action' => 'throttle'], // %80
                'memory' => ['limit' => 2048, 'period' => 'continuous', 'action' => 'warn'] // 2GB
            ];

            foreach ($limits as $type => $config) {
                TenantResourceLimit::updateOrCreate([
                    'tenant_id' => $tenant->id,
                    'resource_type' => $type
                ], [
                    'limit_value' => $config['limit'],
                    'period_type' => $config['period'],
                    'enforcement_action' => $config['action'],
                    'is_active' => true
                ]);
            }
        }
        
        Log::info('Real resource limits setup completed for ' . $tenants->count() . ' tenants');
    }
}