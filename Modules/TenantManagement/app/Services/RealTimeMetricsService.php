<?php

namespace Modules\TenantManagement\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\Tenant;
use Modules\TenantManagement\App\Models\TenantUsageLog;
use Carbon\Carbon;

/**
 * Gerçek zamanlı sistem metrikleri servisi
 * Bu servis demo veri değil, CANLI sistem verilerini toplar
 */
class RealTimeMetricsService
{
    /**
     * Gerçek sistem toplam memory'sini al
     */
    public function getSystemTotalMemory(): int
    {
        try {
            // Linux sistemlerde /proc/meminfo'dan okuma
            if (file_exists('/proc/meminfo')) {
                $meminfo = file_get_contents('/proc/meminfo');
                if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $matches)) {
                    return intval($matches[1] / 1024); // MB cinsinden
                }
            }
            
            // Alternatif: free komutu ile
            $output = shell_exec('free -m | grep "^Mem:" | awk \'{print $2}\'');
            if ($output) {
                return intval(trim($output));
            }
            
            // Default fallback
            return 8192; // 8GB
            
        } catch (\Exception $e) {
            \Log::warning('System memory detection failed: ' . $e->getMessage());
            return 8192;
        }
    }

    /**
     * Sistem maksimum database connection sayısını al
     */
    public function getSystemMaxConnections(): int
    {
        try {
            // MySQL max_connections değerini gerçekten al
            $result = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            if (isset($result[0])) {
                return intval($result[0]->Value);
            }
            
            return 1000; // Default
            
        } catch (\Exception $e) {
            \Log::warning('Max connections detection failed: ' . $e->getMessage());
            return 1000;
        }
    }

    /**
     * Gerçek memory kullanım yüzdesi hesapla
     */
    public function calculateRealMemoryUsagePercentage($logs, int $tenantId): float
    {
        try {
            // Tenant'a özel memory kullanımı
            $memoryLogs = $logs->whereNotNull('memory_usage_mb');
            if ($memoryLogs->isEmpty()) {
                return 0;
            }
            
            $avgMemoryUsageMB = $memoryLogs->avg('memory_usage_mb');
            
            // Standard memory limit
            $memoryLimit = 512; // 512MB standard limit
            $percentage = ($avgMemoryUsageMB / $memoryLimit) * 100;
            
            return min(100, $percentage); // Max %100
            
        } catch (\Exception $e) {
            \Log::warning("Memory calculation failed for tenant {$tenantId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gerçek connection kullanım yüzdesi hesapla
     */
    public function calculateRealConnectionUsagePercentage($logs, int $tenantId): float
    {
        try {
            $connectionLogs = $logs->whereNotNull('active_connections');
            if ($connectionLogs->isEmpty()) {
                return 0;
            }
            
            $maxActiveConnections = $connectionLogs->max('active_connections');
            
            // Standard connection limit
            $connectionLimit = 50; // 50 connections standard limit
            $percentage = ($maxActiveConnections / $connectionLimit) * 100;
            
            return min(100, $percentage);
            
        } catch (\Exception $e) {
            \Log::warning("Connection calculation failed for tenant {$tenantId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gerçek API kullanım yüzdesi hesapla
     */
    public function calculateRealApiUsagePercentage($logs, int $tenantId): float
    {
        try {
            $apiLogs = $logs->whereNotNull('api_requests');
            if ($apiLogs->isEmpty()) {
                return 0;
            }
            
            // Son 10 dakikadaki toplam API istekleri
            $totalApiRequests = $apiLogs->sum('api_requests');
            $apiRequestsPerHour = ($totalApiRequests / 10) * 60; // 10 dakikadan saatlik hesaplama
            
            // Standard API limit
            $apiLimit = 500; // 500/hour standard limit
            $percentage = ($apiRequestsPerHour / $apiLimit) * 100;
            
            return min(100, $percentage);
            
        } catch (\Exception $e) {
            \Log::warning("API calculation failed for tenant {$tenantId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gerçek storage kullanım yüzdesi hesapla
     */
    public function calculateRealStorageUsagePercentage(int $tenantId): float
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return 0;
            }
            
            // Tenant'ın gerçek database boyutunu hesapla
            $dbName = $tenant->data['db_name'] ?? "tenant_{$tenantId}";
            
            $storageQuery = "
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ";
            
            $result = DB::select($storageQuery, [$dbName]);
            $usedStorageMB = $result[0]->size_mb ?? 0;
            
            // Standard storage limit
            $storageLimit = 500; // 500MB standard limit
            $percentage = ($usedStorageMB / $storageLimit) * 100;
            
            return min(100, $percentage);
            
        } catch (\Exception $e) {
            \Log::warning("Storage calculation failed for tenant {$tenantId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Şu anki aktif bağlantı sayısını al
     */
    public function getCurrentActiveConnections(int $tenantId): int
    {
        try {
            // Redis'den tenant'ın aktif connection sayısını al
            $cacheKey = "tenant_{$tenantId}_active_connections";
            $activeConnections = Cache::get($cacheKey);
            
            if ($activeConnections !== null) {
                return intval($activeConnections);
            }
            
            // Alternatif: Database'den processlist kontrolü
            $tenant = Tenant::find($tenantId);
            if ($tenant && !empty($tenant->data['db_name'])) {
                $dbName = $tenant->data['db_name'];
                $processes = DB::select("SHOW PROCESSLIST");
                
                $tenantConnections = collect($processes)->filter(function ($process) use ($dbName) {
                    return $process->db === $dbName;
                })->count();
                
                // Cache'e kaydet
                Cache::put($cacheKey, $tenantConnections, 60); // 1 dakika cache
                
                return $tenantConnections;
            }
            
            return 0;
            
        } catch (\Exception $e) {
            \Log::warning("Active connections calculation failed for tenant {$tenantId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gerçek API rate hesapla
     */
    public function calculateRealApiRate($logs): float
    {
        try {
            $apiLogs = $logs->whereNotNull('api_requests');
            if ($apiLogs->isEmpty()) {
                return 0;
            }
            
            $totalRequests = $apiLogs->sum('api_requests');
            $timeSpanMinutes = 10; // Son 10 dakika
            
            return round($totalRequests / $timeSpanMinutes, 2);
            
        } catch (\Exception $e) {
            \Log::warning('API rate calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gerçek DB query rate hesapla
     */
    public function calculateRealDbQueryRate($logs): float
    {
        try {
            $dbLogs = $logs->whereNotNull('db_queries');
            if ($dbLogs->isEmpty()) {
                return 0;
            }
            
            $totalQueries = $dbLogs->sum('db_queries');
            $timeSpanMinutes = 10; // Son 10 dakika
            
            return round($totalQueries / $timeSpanMinutes, 2);
            
        } catch (\Exception $e) {
            \Log::warning('DB rate calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sistem yükü hesapla (composite metric)
     */
    public function calculateSystemLoad(float $cpuUsage, float $memoryUsage, float $dbUsage): float
    {
        try {
            // Weighted average: CPU %40, Memory %35, DB %25
            $systemLoad = ($cpuUsage * 0.4) + ($memoryUsage * 0.35) + ($dbUsage * 0.25);
            
            return round($systemLoad, 2);
            
        } catch (\Exception $e) {
            \Log::warning('System load calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Disk I/O kullanımı hesapla
     */
    public function calculateDiskIOUsage(): float
    {
        try {
            // iostat komutu ile disk I/O al
            $output = shell_exec('iostat -x 1 1 | grep -E "^[a-z]" | tail -1 | awk \'{print $10}\'');
            if ($output) {
                return floatval(trim($output));
            }
            
            // Alternatif: /proc/diskstats okuma
            if (file_exists('/proc/diskstats')) {
                $diskstats = file_get_contents('/proc/diskstats');
                // Disk I/O parsing implementasyonu buraya eklenebilir
            }
            
            return 0;
            
        } catch (\Exception $e) {
            \Log::warning('Disk I/O calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Network kullanımı hesapla (Mbps)
     */
    public function calculateNetworkUsage(): float
    {
        try {
            // iftop veya nethogs ile network usage
            $output = shell_exec('cat /proc/net/dev | grep eth0 | awk \'{print ($2 + $10) / 1024 / 1024}\'');
            if ($output) {
                return floatval(trim($output));
            }
            
            return 0;
            
        } catch (\Exception $e) {
            \Log::warning('Network usage calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tenant için anlık performance snapshot'ı al
     */
    public function getTenantPerformanceSnapshot(int $tenantId): array
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return ['error' => 'Tenant not found'];
            }
            
            // Son 5 dakikanın gerçek verileri
            $recentLogs = TenantUsageLog::where('tenant_id', $tenantId)
                ->where('recorded_at', '>=', Carbon::now()->subMinutes(5))
                ->orderBy('recorded_at', 'desc')
                ->get();
            
            if ($recentLogs->isEmpty()) {
                return [
                    'tenant_id' => $tenantId,
                    'status' => 'no_recent_activity',
                    'snapshot_time' => Carbon::now()->toISOString()
                ];
            }
            
            return [
                'tenant_id' => $tenantId,
                'tier' => 'standard',
                'performance_metrics' => [
                    'cpu_usage_avg' => round($recentLogs->avg('cpu_usage_percent'), 2),
                    'memory_usage_mb' => round($recentLogs->avg('memory_usage_mb'), 2),
                    'active_connections' => $recentLogs->max('active_connections'),
                    'response_time_avg' => round($recentLogs->avg('response_time_ms'), 2),
                    'api_requests_rate' => round($recentLogs->sum('api_requests') / 5, 2), // per minute
                    'db_queries_rate' => round($recentLogs->sum('db_queries') / 5, 2)
                ],
                'resource_utilization' => [
                    'memory_percentage' => $this->calculateRealMemoryUsagePercentage($recentLogs, $tenantId),
                    'connection_percentage' => $this->calculateRealConnectionUsagePercentage($recentLogs, $tenantId),
                    'api_percentage' => $this->calculateRealApiUsagePercentage($recentLogs, $tenantId),
                    'storage_percentage' => $this->calculateRealStorageUsagePercentage($tenantId)
                ],
                'data_points' => $recentLogs->count(),
                'snapshot_time' => Carbon::now()->toISOString(),
                'data_freshness' => 'realtime'
            ];
            
        } catch (\Exception $e) {
            \Log::error("Performance snapshot failed for tenant {$tenantId}: " . $e->getMessage());
            
            return [
                'tenant_id' => $tenantId,
                'error' => 'Performance snapshot failed',
                'message' => $e->getMessage(),
                'snapshot_time' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Sistem geneli anlık durum
     */
    public function getSystemHealthSnapshot(): array
    {
        try {
            $allActiveTenants = Tenant::where('is_active', true)->count();
            
            // Son 1 dakikanın sistem verileri
            $systemLogs = TenantUsageLog::where('recorded_at', '>=', Carbon::now()->subMinute())
                ->whereNotNull('cpu_usage_percent')
                ->get();
            
            $systemHealth = [
                'status' => 'healthy',
                'active_tenants' => $allActiveTenants,
                'system_metrics' => [
                    'avg_cpu_usage' => $systemLogs->avg('cpu_usage_percent'),
                    'total_memory_usage_mb' => $systemLogs->sum('memory_usage_mb'),
                    'total_active_connections' => $systemLogs->sum('active_connections'),
                    'total_api_requests_per_minute' => $systemLogs->sum('api_requests')
                ],
                'resource_status' => [
                    'memory_total_mb' => $this->getSystemTotalMemory(),
                    'max_connections' => $this->getSystemMaxConnections(),
                    'disk_io_usage' => $this->calculateDiskIOUsage(),
                    'network_usage_mbps' => $this->calculateNetworkUsage()
                ],
                'data_points' => $systemLogs->count(),
                'snapshot_time' => Carbon::now()->toISOString(),
                'uptime_minutes' => $this->getSystemUptime()
            ];
            
            // Health status belirleme
            $avgCpu = $systemHealth['system_metrics']['avg_cpu_usage'];
            $memoryUsagePercent = ($systemHealth['system_metrics']['total_memory_usage_mb'] / $systemHealth['resource_status']['memory_total_mb']) * 100;
            
            if ($avgCpu > 90 || $memoryUsagePercent > 90) {
                $systemHealth['status'] = 'critical';
            } elseif ($avgCpu > 75 || $memoryUsagePercent > 75) {
                $systemHealth['status'] = 'warning';
            }
            
            return $systemHealth;
            
        } catch (\Exception $e) {
            \Log::error('System health snapshot failed: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'error' => 'Health snapshot failed',
                'message' => $e->getMessage(),
                'snapshot_time' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Sistem uptime'ı al (dakika cinsinden)
     */
    private function getSystemUptime(): int
    {
        try {
            $uptime = file_get_contents('/proc/uptime');
            if ($uptime) {
                $uptimeSeconds = floatval(explode(' ', trim($uptime))[0]);
                return intval($uptimeSeconds / 60); // Dakikaya çevir
            }
            
            return 0;
            
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * TenantHealthCheckComponent için gerekli getCurrentMetrics metodu
     */
    public function getCurrentMetrics(): array
    {
        try {
            // Sistem geneli anlık metrikler
            $systemHealth = $this->getSystemHealthSnapshot();
            
            return [
                'system_status' => $systemHealth['status'] ?? 'unknown',
                'active_tenants' => $systemHealth['active_tenants'] ?? 0,
                'total_memory_usage_mb' => $systemHealth['system_metrics']['total_memory_usage_mb'] ?? 0,
                'avg_cpu_usage' => round($systemHealth['system_metrics']['avg_cpu_usage'] ?? 0, 2),
                'total_connections' => $systemHealth['system_metrics']['total_active_connections'] ?? 0,
                'api_requests_per_minute' => $systemHealth['system_metrics']['total_api_requests_per_minute'] ?? 0,
                'memory_total_mb' => $systemHealth['resource_status']['memory_total_mb'] ?? 8192,
                'max_connections' => $systemHealth['resource_status']['max_connections'] ?? 1000,
                'uptime_minutes' => $systemHealth['uptime_minutes'] ?? 0,
                'last_updated' => Carbon::now()->toISOString()
            ];
            
            // Alert System - Critical Threshold Check
            $this->checkCriticalThresholds($metrics);
            
            return $metrics;
            
        } catch (\Exception $e) {
            \Log::error('getCurrentMetrics failed: ' . $e->getMessage());
            
            return [
                'system_status' => 'error',
                'active_tenants' => 0,
                'total_memory_usage_mb' => 0,
                'avg_cpu_usage' => 0,
                'total_connections' => 0,
                'api_requests_per_minute' => 0,
                'memory_total_mb' => 8192,
                'max_connections' => 1000,
                'uptime_minutes' => 0,
                'last_updated' => Carbon::now()->toISOString(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Kritik threshold kontrolleri ve alert sistemi
     */
    private function checkCriticalThresholds(array $metrics): void
    {
        try {
            $alerts = [];
            
            // Memory Threshold Check (>90% critical, >75% warning)
            $memoryUsagePercent = ($metrics['total_memory_usage_mb'] / $metrics['memory_total_mb']) * 100;
            if ($memoryUsagePercent > 90) {
                $alerts[] = [
                    'type' => 'critical',
                    'metric' => 'memory',
                    'value' => round($memoryUsagePercent, 2),
                    'threshold' => 90,
                    'message' => "Critical memory usage: {$memoryUsagePercent}% (threshold: 90%)"
                ];
            } elseif ($memoryUsagePercent > 75) {
                $alerts[] = [
                    'type' => 'warning',
                    'metric' => 'memory',
                    'value' => round($memoryUsagePercent, 2),
                    'threshold' => 75,
                    'message' => "High memory usage: {$memoryUsagePercent}% (threshold: 75%)"
                ];
            }
            
            // CPU Threshold Check (>85% critical, >70% warning)
            if ($metrics['avg_cpu_usage'] > 85) {
                $alerts[] = [
                    'type' => 'critical',
                    'metric' => 'cpu',
                    'value' => $metrics['avg_cpu_usage'],
                    'threshold' => 85,
                    'message' => "Critical CPU usage: {$metrics['avg_cpu_usage']}% (threshold: 85%)"
                ];
            } elseif ($metrics['avg_cpu_usage'] > 70) {
                $alerts[] = [
                    'type' => 'warning',
                    'metric' => 'cpu',
                    'value' => $metrics['avg_cpu_usage'],
                    'threshold' => 70,
                    'message' => "High CPU usage: {$metrics['avg_cpu_usage']}% (threshold: 70%)"
                ];
            }
            
            // Connections Threshold Check (>80% critical, >60% warning)
            $connectionUsagePercent = ($metrics['total_connections'] / $metrics['max_connections']) * 100;
            if ($connectionUsagePercent > 80) {
                $alerts[] = [
                    'type' => 'critical',
                    'metric' => 'connections',
                    'value' => round($connectionUsagePercent, 2),
                    'threshold' => 80,
                    'message' => "Critical connection usage: {$connectionUsagePercent}% (threshold: 80%)"
                ];
            } elseif ($connectionUsagePercent > 60) {
                $alerts[] = [
                    'type' => 'warning',
                    'metric' => 'connections',
                    'value' => round($connectionUsagePercent, 2),
                    'threshold' => 60,
                    'message' => "High connection usage: {$connectionUsagePercent}% (threshold: 60%)"
                ];
            }
            
            // Alert'leri işle
            if (!empty($alerts)) {
                $this->processAlerts($alerts);
            }
            
        } catch (\Exception $e) {
            \Log::error('Critical threshold check failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Alert'leri işle (log, email, Slack, vb.)
     */
    private function processAlerts(array $alerts): void
    {
        try {
            foreach ($alerts as $alert) {
                // Log alert
                if ($alert['type'] === 'critical') {
                    \Log::critical("🚨 SYSTEM ALERT: {$alert['message']}", [
                        'metric' => $alert['metric'],
                        'value' => $alert['value'],
                        'threshold' => $alert['threshold'],
                        'timestamp' => Carbon::now()->toISOString()
                    ]);
                } else {
                    \Log::warning("⚠️  SYSTEM WARNING: {$alert['message']}", [
                        'metric' => $alert['metric'],
                        'value' => $alert['value'],
                        'threshold' => $alert['threshold'],
                        'timestamp' => Carbon::now()->toISOString()
                    ]);
                }
                
                // Cache alert for dashboard
                $cacheKey = "system_alert_{$alert['metric']}_{$alert['type']}";
                Cache::put($cacheKey, $alert, 300); // 5 dakika cache
                
                // TODO: Email/Slack notification can be added here
                // $this->sendEmailAlert($alert);
                // $this->sendSlackAlert($alert);
            }
            
        } catch (\Exception $e) {
            \Log::error('Alert processing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Aktif alert'leri al (Dashboard için)
     */
    public function getActiveAlerts(): array
    {
        try {
            $activeAlerts = [];
            
            $alertTypes = ['memory', 'cpu', 'connections'];
            $severityLevels = ['critical', 'warning'];
            
            foreach ($alertTypes as $type) {
                foreach ($severityLevels as $severity) {
                    $cacheKey = "system_alert_{$type}_{$severity}";
                    $alert = Cache::get($cacheKey);
                    
                    if ($alert) {
                        $activeAlerts[] = $alert;
                    }
                }
            }
            
            // Son alert'e göre sırala (en kritikten en az kritik)
            usort($activeAlerts, function($a, $b) {
                if ($a['type'] === 'critical' && $b['type'] !== 'critical') {
                    return -1;
                }
                if ($b['type'] === 'critical' && $a['type'] !== 'critical') {
                    return 1;
                }
                return $b['value'] <=> $a['value'];
            });
            
            return $activeAlerts;
            
        } catch (\Exception $e) {
            \Log::error('Get active alerts failed: ' . $e->getMessage());
            return [];
        }
    }
}