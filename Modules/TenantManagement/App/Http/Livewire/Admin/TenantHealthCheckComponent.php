<?php

namespace Modules\TenantManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Tenant;
use Modules\TenantManagement\App\Services\RealTimeMetricsService;
use Modules\TenantManagement\App\Services\RealTimeAutoScalingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

#[Layout('admin.layout')]
class TenantHealthCheckComponent extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedTenant = null;
    public $healthStatus = [];
    public $systemMetrics = [];
    public $realtimeMetrics = [];
    
    public function mount()
    {
        $this->loadSystemMetrics();
        $this->loadHealthStatus();
    }
    
    public function loadSystemMetrics()
    {
        try {
            $this->systemMetrics = [
                'total_tenants' => Tenant::count(),
                'active_tenants' => Tenant::where('is_active', true)->count(),
                'database_connections' => $this->getDatabaseConnections(),
                'redis_connections' => $this->getRedisConnections(),
                'memory_usage' => $this->getMemoryUsage(),
                'cpu_usage' => $this->getCpuUsage(),
                'disk_usage' => $this->getDiskUsage(),
                'uptime' => $this->getSystemUptime()
            ];
        } catch (\Exception $e) {
            session()->flash('error', 'Sistem metrikleri yüklenirken hata: ' . $e->getMessage());
            $this->systemMetrics = [];
        }
    }
    
    public function loadHealthStatus()
    {
        try {
            $tenants = Tenant::when($this->search, function($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhere('tenancy_db_name', 'like', '%' . $this->search . '%')
                      ->orWhere('data->name', 'like', '%' . $this->search . '%');
            })->take(20)->get();
            
            $this->healthStatus = [];
            
            foreach ($tenants as $tenant) {
                $this->healthStatus[$tenant->id] = $this->checkTenantHealth($tenant);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Tenant sağlık durumu yüklenirken hata: ' . $e->getMessage());
            $this->healthStatus = [];
        }
    }
    
    public function checkTenantHealth($tenant)
    {
        $health = [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->title ?? $tenant->data['name'] ?? 'Tenant #' . $tenant->id,
            'tenancy_db_name' => $tenant->tenancy_db_name,
            'status' => 'healthy',
            'issues' => [],
            'metrics' => [],
            'score' => 100
        ];
        
        try {
            // Database bağlantısını kontrol et
            if (!$this->checkDatabaseConnection($tenant)) {
                $health['issues'][] = 'Database bağlantısı başarısız';
                $health['status'] = 'critical';
                $health['score'] -= 30;
            }
            
            // Redis bağlantısını kontrol et
            if (!$this->checkRedisConnection($tenant)) {
                $health['issues'][] = 'Redis bağlantısı başarısız';
                $health['status'] = 'warning';
                $health['score'] -= 15;
            }
            
            // Resource kullanımını kontrol et
            $resourceMetrics = $this->getTenantResourceMetrics($tenant);
            $health['metrics'] = $resourceMetrics;
            
            if ($resourceMetrics['memory_usage_percentage'] > 80) {
                $health['issues'][] = 'Yüksek bellek kullanımı (' . $resourceMetrics['memory_usage_percentage'] . '%)';
                $health['status'] = 'warning';
                $health['score'] -= 10;
            }
            
            if ($resourceMetrics['cpu_usage_percentage'] > 80) {
                $health['issues'][] = 'Yüksek CPU kullanımı (' . $resourceMetrics['cpu_usage_percentage'] . '%)';
                $health['status'] = 'warning';
                $health['score'] -= 10;
            }
            
            if ($resourceMetrics['connection_count'] > 40) {
                $health['issues'][] = 'Yüksek bağlantı sayısı (' . $resourceMetrics['connection_count'] . ')';
                $health['status'] = 'warning';
                $health['score'] -= 5;
            }
            
        } catch (\Exception $e) {
            $health['issues'][] = 'Sağlık kontrolü hatası: ' . $e->getMessage();
            $health['status'] = 'critical';
            $health['score'] = 0;
        }
        
        return $health;
    }
    
    private function checkDatabaseConnection($tenant)
    {
        // Tenant context'ini DEĞİŞTİRMEDEN doğrudan SQL ile kontrol et
        // Bu sayede URL::forceRootUrl() bozulmaz
        try {
            $dbName = $tenant->tenancy_db_name;
            if (empty($dbName)) {
                return false;
            }

            // Doğrudan MySQL sorgusu ile database varlığını kontrol et
            $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);

            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkRedisConnection($tenant)
    {
        try {
            $redis = Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function getTenantResourceMetrics($tenant)
    {
        try {
            $realTimeMetrics = app(RealTimeMetricsService::class);
            $snapshot = $realTimeMetrics->getTenantPerformanceSnapshot($tenant->id);
            
            if (isset($snapshot['error'])) {
                return [
                    'memory_usage_percentage' => 0,
                    'cpu_usage_percentage' => 0,
                    'connection_count' => 0,
                    'api_requests_per_hour' => 0,
                    'storage_usage_mb' => 0
                ];
            }
            
            return [
                'memory_usage_percentage' => $snapshot['resource_utilization']['memory_percentage'] ?? 0,
                'cpu_usage_percentage' => $snapshot['performance_metrics']['cpu_usage_avg'] ?? 0,
                'connection_count' => $snapshot['performance_metrics']['active_connections'] ?? 0,
                'api_requests_per_hour' => ($snapshot['performance_metrics']['api_requests_rate'] ?? 0) * 60,
                'storage_usage_mb' => $snapshot['performance_metrics']['memory_usage_mb'] ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'memory_usage_percentage' => 0,
                'cpu_usage_percentage' => 0,
                'connection_count' => 0,
                'api_requests_per_hour' => 0,
                'storage_usage_mb' => 0
            ];
        }
    }
    
    private function getDatabaseConnections()
    {
        try {
            $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return $connections[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getRedisConnections()
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('clients');
            return $info['connected_clients'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getMemoryUsage()
    {
        try {
            $memInfo = file_get_contents('/proc/meminfo');
            if ($memInfo) {
                preg_match('/MemTotal:\s+(\d+)/', $memInfo, $total);
                preg_match('/MemAvailable:\s+(\d+)/', $memInfo, $available);
                
                if ($total && $available) {
                    $totalMB = $total[1] / 1024;
                    $availableMB = $available[1] / 1024;
                    $usedMB = $totalMB - $availableMB;
                    
                    return [
                        'total' => round($totalMB, 2),
                        'used' => round($usedMB, 2),
                        'percentage' => round(($usedMB / $totalMB) * 100, 2)
                    ];
                }
            }
            
            // Fallback - PHP memory usage
            return [
                'total' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'used' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'percentage' => round((memory_get_usage(true) / memory_get_peak_usage(true)) * 100, 2)
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'used' => 0, 'percentage' => 0];
        }
    }
    
    private function getCpuUsage()
    {
        try {
            $load = sys_getloadavg();
            return $load ? round($load[0] * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getDiskUsage()
    {
        try {
            $totalBytes = disk_total_space('/');
            $freeBytes = disk_free_space('/');
            $usedBytes = $totalBytes - $freeBytes;
            
            return [
                'total' => round($totalBytes / 1024 / 1024 / 1024, 2),
                'used' => round($usedBytes / 1024 / 1024 / 1024, 2),
                'percentage' => round(($usedBytes / $totalBytes) * 100, 2)
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'used' => 0, 'percentage' => 0];
        }
    }
    
    private function getSystemUptime()
    {
        try {
            $uptime = file_get_contents('/proc/uptime');
            if ($uptime) {
                $seconds = floatval($uptime);
                $days = floor($seconds / 86400);
                $hours = floor(($seconds % 86400) / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                
                return "{$days}d {$hours}h {$minutes}m";
            }
            return 'Bilinmiyor';
        } catch (\Exception $e) {
            return 'Bilinmiyor';
        }
    }
    
    public function refreshMetrics()
    {
        $this->loadSystemMetrics();
        $this->loadHealthStatus();
        session()->flash('success', 'Metrikler güncellendi!');
    }
    
    public function selectTenant($tenantId)
    {
        $this->selectedTenant = $tenantId;
        $this->loadRealtimeMetrics($tenantId);
    }
    
    private function loadRealtimeMetrics($tenantId)
    {
        try {
            $autoScalingService = app(RealTimeAutoScalingService::class);
            $this->realtimeMetrics = $autoScalingService->getCurrentMetrics($tenantId);
        } catch (\Exception $e) {
            $this->realtimeMetrics = [];
            session()->flash('error', 'Gerçek zamanlı metrikler yüklenemedi: ' . $e->getMessage());
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadHealthStatus();
    }
    
    public function render()
    {
        return view('tenantmanagement::livewire.admin.tenant-health-check', [
            'healthStatus' => $this->healthStatus,
            'systemMetrics' => $this->systemMetrics,
            'selectedMetrics' => $this->selectedTenant ? ($this->realtimeMetrics ?: []) : []
        ]);
    }
}