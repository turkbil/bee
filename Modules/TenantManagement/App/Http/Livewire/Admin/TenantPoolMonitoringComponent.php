<?php

namespace Modules\TenantManagement\App\Http\Livewire\Admin;

use App\Services\DatabaseConnectionPoolService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TenantPoolMonitoringComponent extends Component
{
    public $poolStats = [];
    public $refreshInterval = 5; // 5 saniye
    public $isAutoRefresh = true;
    
    protected $poolService;

    public function mount()
    {
        try {
            $this->poolService = app(DatabaseConnectionPoolService::class);
            $this->loadPoolStats();
        } catch (\Exception $e) {
            Log::error('DatabaseConnectionPoolService not available', [
                'error' => $e->getMessage()
            ]);
            
            // Service yoksa mock data kullan
            $this->poolStats = [
                'total_pools' => 0,
                'total_active_connections' => 0,
                'total_idle_connections' => 0,
                'pools' => [],
                'updated_at' => now(),
                'error' => 'Service not available'
            ];
        }
    }

    public function render()
    {
        return view('tenantmanagement::livewire.admin.tenant-pool-monitoring-component', [
            'poolStats' => $this->poolStats,
            'isAutoRefresh' => $this->isAutoRefresh,
            'refreshInterval' => $this->refreshInterval,
        ])->layout('admin.layout');
    }

    /**
     * Pool istatistiklerini yükle
     */
    public function loadPoolStats()
    {
        try {
            if ($this->poolService) {
                $this->poolStats = $this->poolService->getPoolStats();
            } else {
                // Service yoksa mock data kullan
                $this->poolStats = [
                    'total_pools' => 0,
                    'total_active_connections' => 0,
                    'total_idle_connections' => 0,
                    'pools' => [],
                    'updated_at' => now(),
                    'error' => 'Service not available'
                ];
            }
            
            // Real-time güncellemesi için cache'e kaydet
            Cache::put('tenant_pool_monitoring_last_update', now(), 60);
            
        } catch (\Exception $e) {
            Log::error('Pool stats loading failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Pool istatistikleri yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Otomatik yenileme toggle
     */
    public function toggleAutoRefresh()
    {
        $this->isAutoRefresh = !$this->isAutoRefresh;
        
        if ($this->isAutoRefresh) {
            $this->loadPoolStats();
        }
    }

    /**
     * Refresh interval değiştir
     */
    public function updateRefreshInterval($interval)
    {
        if (in_array($interval, [1, 3, 5, 10, 30])) {
            $this->refreshInterval = $interval;
        }
    }

    /**
     * Manuel refresh
     */
    public function refresh()
    {
        $this->loadPoolStats();
        session()->flash('success', 'Pool istatistikleri güncellendi.');
    }

    /**
     * Idle connection'ları temizle
     */
    public function cleanupIdleConnections()
    {
        try {
            if ($this->poolService) {
                $this->poolService->cleanupIdleConnections();
                $this->loadPoolStats();
                session()->flash('success', 'Idle connection\'lar temizlendi.');
            } else {
                session()->flash('error', 'Pool service mevcut değil.');
            }
            
        } catch (\Exception $e) {
            Log::error('Cleanup idle connections failed', [
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Cleanup işlemi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Pool performans metrikleri
     */
    public function getPerformanceMetrics()
    {
        if (empty($this->poolStats)) {
            return [];
        }

        $totalConnections = $this->poolStats['total_active_connections'] + $this->poolStats['total_idle_connections'];
        
        return [
            'connection_utilization' => $totalConnections > 0 
                ? round(($this->poolStats['total_active_connections'] / $totalConnections) * 100, 2) 
                : 0,
            'pool_efficiency' => count($this->poolStats['pools']) > 0 
                ? round($this->poolStats['total_active_connections'] / count($this->poolStats['pools']), 2) 
                : 0,
            'idle_ratio' => $totalConnections > 0 
                ? round(($this->poolStats['total_idle_connections'] / $totalConnections) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Tenant pool detaylarını al
     */
    public function getTenantPoolDetails()
    {
        if (empty($this->poolStats['pools'])) {
            return [];
        }

        $details = [];
        foreach ($this->poolStats['pools'] as $tenantKey => $pool) {
            $details[] = [
                'tenant' => $tenantKey,
                'active' => $pool['active_connections'],
                'idle' => $pool['idle_connections'],
                'max' => $pool['max_connections'],
                'utilization' => $pool['max_connections'] > 0 
                    ? round(($pool['active_connections'] / $pool['max_connections']) * 100, 2) 
                    : 0,
                'created_at' => isset($pool['created_at']) ? Carbon::parse($pool['created_at'])->format('H:i:s') : '-',
                'last_activity' => isset($pool['last_activity']) ? Carbon::parse($pool['last_activity'])->format('H:i:s') : '-',
            ];
        }

        return collect($details)->sortByDesc('utilization')->toArray();
    }

    /**
     * Sistem health durumu
     */
    public function getSystemHealth()
    {
        $metrics = $this->getPerformanceMetrics();
        
        if ($metrics['connection_utilization'] > 90) {
            return [
                'status' => 'critical',
                'message' => 'Connection kullanımı kritik seviyede (%' . $metrics['connection_utilization'] . ')',
                'color' => 'danger'
            ];
        } elseif ($metrics['connection_utilization'] > 75) {
            return [
                'status' => 'warning',
                'message' => 'Connection kullanımı yüksek (%' . $metrics['connection_utilization'] . ')',
                'color' => 'warning'
            ];
        } else {
            return [
                'status' => 'healthy',
                'message' => 'System sağlıklı çalışıyor',
                'color' => 'success'
            ];
        }
    }
}