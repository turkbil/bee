<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\TenantManagement\App\Services\TenantResourceMonitorService;
use Modules\TenantManagement\App\Models\TenantUsageLog;
use Modules\TenantManagement\App\Models\TenantResourceLimit;
use App\Models\Tenant;
use Carbon\Carbon;

#[Layout('admin.layout')]
class TenantMonitoringComponent extends Component
{
    use WithPagination;

    public $selectedTenantId = null;
    public $selectedTenant = null;
    public $selectedResourceType = 'all';
    public $selectedStatus = 'all';
    public $timeRange = '24'; // hours
    public $resourceType = 'all';
    public $autoRefresh = true;
    public $showDetails = false;
    public $selectedMetric = 'cpu';
    
    // Filtering
    public $statusFilter = 'all';
    public $search = '';
    
    // Real-time data
    public $systemSummary = [];
    public $tenantUsage = [];
    public $alerts = [];
    public $chartData = [];

    protected $listeners = [
        'refreshData' => 'loadData',
        'tenantSelected' => 'selectTenant',
        'clearAlerts' => 'clearAlerts'
    ];

    protected $queryString = [
        'selectedTenantId' => ['except' => null],
        'timeRange' => ['except' => '24'],
        'resourceType' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all']
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        try {
            // Real system-wide statistics - veritabanından gerçek veriler çek
            $this->loadRealSystemSummary();
            
            // Seçili tenant varsa detaylarını yükle
            if ($this->selectedTenantId) {
                $this->loadRealTenantUsage();
                $this->loadChartData();
            }
            
            // Sistem geneli uyarılar
            $this->loadAlerts();
            
        } catch (\Exception $e) {
            // Hata durumunda da gerçek verileri göster, sadece boş değerlerle
            \Log::error('TenantMonitoringComponent loadData error: ' . $e->getMessage());
            $this->systemSummary = [
                'system_wide_stats' => [
                    'api_calls' => ['total' => 0, 'percentage' => 0, 'trend' => 'stable'],
                    'database_queries' => ['total' => 0, 'percentage' => 0, 'trend' => 'stable'],
                    'cache_operations' => ['total' => 0, 'percentage' => 0, 'trend' => 'stable'],
                    'ai_operations' => ['total' => 0, 'percentage' => 0, 'trend' => 'stable']
                ]
            ];
            $this->alerts = [];
        }
    }

    public function selectTenant($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $this->showDetails = true;
        $this->loadData();
    }

    public function clearTenantSelection()
    {
        $this->selectedTenantId = null;
        $this->showDetails = false;
        $this->tenantUsage = [];
        $this->chartData = [];
    }

    public function updatedTimeRange()
    {
        $this->loadData();
        $this->resetPage();
    }

    public function updatedResourceType()
    {
        $this->loadData();
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedMetric()
    {
        $this->loadChartData();
    }

    public function updatedSelectedTenant()
    {
        if ($this->selectedTenant) {
            $this->selectedTenantId = $this->selectedTenant;
            $this->selectTenant($this->selectedTenant);
        } else {
            $this->clearTenantSelection();
        }
    }

    public function updatedSelectedResourceType()
    {
        $this->resourceType = $this->selectedResourceType;
        $this->loadData();
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->statusFilter = $this->selectedStatus;
        $this->resetPage();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        
        if ($this->autoRefresh) {
            $this->dispatch('startAutoRefresh');
        } else {
            $this->dispatch('stopAutoRefresh');
        }
    }

    public function refreshData()
    {
        try {
            $this->loadData();
            
            $this->dispatch('toast', [
                'title' => 'Güncellendi',
                'message' => 'Monitoring verileri yenilendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Veri yenileme hatası: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearCache($tenantId = null)
    {
        try {
            // Manual cache clearing without service dependency
            if ($tenantId) {
                \Cache::forget("tenant_monitoring_{$tenantId}");
                \Cache::forget("tenant_usage_{$tenantId}");
            } else {
                \Cache::flush();
            }
            
            $this->loadData();
            
            $this->dispatch('toast', [
                'title' => 'Cache Temizlendi',
                'message' => $tenantId ? "Tenant #{$tenantId} cache'i temizlendi." : 'Sistem cache temizlendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache temizleme hatası: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function exportData()
    {
        $this->exportReport();
    }

    public function exportReport()
    {
        try {
            $data = [
                'system_summary' => $this->systemSummary,
                'export_date' => Carbon::now()->toISOString(),
                'time_range' => $this->timeRange . ' saat',
                'tenants_data' => []
            ];

            if ($this->selectedTenantId) {
                $tenant = Tenant::find($this->selectedTenantId);
                $data['tenants_data'][$this->selectedTenantId] = [
                    'tenant_info' => [
                        'id' => $tenant->id,
                        'title' => $tenant->title,
                        'is_active' => $tenant->is_active
                    ],
                    'usage_data' => $this->tenantUsage,
                    'chart_data' => $this->chartData
                ];
            }

            $filename = 'tenant_monitoring_report_' . Carbon::now()->format('Y_m_d_H_i') . '.json';
            $filepath = storage_path('app/reports/' . $filename);
            
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
            
            $this->dispatch('toast', [
                'title' => 'Rapor Oluşturuldu',
                'message' => "Rapor {$filename} olarak kaydedildi.",
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Rapor oluşturulurken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function getTenantListProperty()
    {
        $query = Tenant::query();
        
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
        }
        
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }
        
        return $query->orderBy('title')->paginate(20);
    }

    public function getFilteredUsageLogsProperty()
    {
        if (!$this->selectedTenantId) {
            return collect();
        }

        $query = TenantUsageLog::where('tenant_id', $this->selectedTenantId)
            ->where('recorded_at', '>=', Carbon::now()->subHours($this->timeRange))
            ->orderBy('recorded_at', 'desc');

        if ($this->resourceType !== 'all') {
            $query->where('resource_type', $this->resourceType);
        }

        return $query->paginate(50);
    }

    private function loadAlerts()
    {
        $alerts = [];
        
        // Kritik durumlar
        $criticalLogs = TenantUsageLog::where('status', 'critical')
            ->where('recorded_at', '>=', Carbon::now()->subHours(1))
            ->with('tenant')
            ->get();

        foreach ($criticalLogs as $log) {
            $alerts[] = [
                'type' => 'critical',
                'title' => 'Kritik Kaynak Kullanımı',
                'message' => "Tenant #{$log->tenant_id} ({$log->tenant->title}) - {$log->resource_type} kullanımı kritik seviyede",
                'tenant_id' => $log->tenant_id,
                'resource_type' => $log->resource_type,
                'recorded_at' => $log->recorded_at->diffForHumans(),
                'data' => [
                    'cpu_percent' => $log->cpu_usage_percent,
                    'memory_mb' => $log->memory_usage_mb,
                    'response_time' => $log->response_time_ms
                ]
            ];
        }

        // Limit aşımları - manual check without service dependency
        $tenants = Tenant::where('is_active', true)->get();
        
        foreach ($tenants->take(10) as $tenant) { // İlk 10 tenant için kontrol
            try {
                // Manual limit checking from TenantResourceLimit table
                $limits = TenantResourceLimit::where('tenant_id', $tenant->id)->get();
                $usageLogs24h = TenantUsageLog::where('tenant_id', $tenant->id)
                    ->where('recorded_at', '>=', Carbon::now()->subHours(24))
                    ->get();
                
                foreach ($limits as $limit) {
                    $currentUsage = $usageLogs24h->where('resource_type', $limit->resource_type)->sum('usage_amount');
                    
                    if ($currentUsage > $limit->limit_value) {
                        $alerts[] = [
                            'type' => 'warning',
                            'title' => 'Limit Aşımı',
                            'message' => "Tenant #{$tenant->id} ({$tenant->title}) - {$limit->resource_type} limiti aşıldı",
                            'tenant_id' => $tenant->id,
                            'resource_type' => $limit->resource_type,
                            'recorded_at' => 'Şimdi',
                            'data' => [
                                'usage' => $currentUsage,
                                'limit' => $limit->limit_value,
                                'period' => $limit->period_type ?? '24h',
                                'action' => $limit->enforcement_action ?? 'warning'
                            ]
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Hata durumunda devam et
                \Log::error('TenantMonitoringComponent loadAlerts error for tenant ' . $tenant->id . ': ' . $e->getMessage());
            }
        }

        // En yeni 20 uyarı
        $this->alerts = array_slice($alerts, 0, 20);
    }

    private function loadChartData()
    {
        if (!$this->selectedTenantId) {
            return;
        }

        $hours = min(168, intval($this->timeRange)); // Max 1 hafta
        $logs = TenantUsageLog::where('tenant_id', $this->selectedTenantId)
            ->where('recorded_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('recorded_at')
            ->get();

        $chartData = [
            'labels' => [],
            'datasets' => []
        ];

        // Zaman etiketleri
        $groupBy = $hours > 48 ? 'H' : 'H:i'; // 48 saatten fazlaysa saatlik, değilse dakika
        $grouped = $logs->groupBy(function($log) use ($groupBy) {
            return $log->recorded_at->format($groupBy);
        });

        $labels = $grouped->keys()->toArray();
        $chartData['labels'] = $labels;

        // Metrik bazında dataset oluştur
        $metrics = [
            'cpu' => ['label' => 'CPU (%)', 'color' => '#ff6384', 'field' => 'cpu_usage_percent'],
            'memory' => ['label' => 'RAM (MB)', 'color' => '#36a2eb', 'field' => 'memory_usage_mb'],
            'response_time' => ['label' => 'Yanıt Süresi (ms)', 'color' => '#ffce56', 'field' => 'response_time_ms'],
            'connections' => ['label' => 'Bağlantılar', 'color' => '#4bc0c0', 'field' => 'active_connections'],
            'queries' => ['label' => 'DB Sorguları', 'color' => '#9966ff', 'field' => 'db_queries']
        ];

        if ($this->selectedMetric === 'all') {
            foreach ($metrics as $key => $metric) {
                $data = [];
                foreach ($labels as $label) {
                    $avg = $grouped[$label]->avg($metric['field']);
                    $data[] = round($avg ?: 0, 2);
                }

                $chartData['datasets'][] = [
                    'label' => $metric['label'],
                    'data' => $data,
                    'borderColor' => $metric['color'],
                    'backgroundColor' => $metric['color'] . '20',
                    'tension' => 0.4
                ];
            }
        } else {
            if (isset($metrics[$this->selectedMetric])) {
                $metric = $metrics[$this->selectedMetric];
                $data = [];
                foreach ($labels as $label) {
                    $avg = $grouped[$label]->avg($metric['field']);
                    $data[] = round($avg ?: 0, 2);
                }

                $chartData['datasets'][] = [
                    'label' => $metric['label'],
                    'data' => $data,
                    'borderColor' => $metric['color'],
                    'backgroundColor' => $metric['color'] . '20',
                    'fill' => true,
                    'tension' => 0.4
                ];
            }
        }

        $this->chartData = $chartData;
    }

    public function clearAlerts()
    {
        $this->alerts = [];
        
        $this->dispatch('toast', [
            'title' => 'Uyarılar Temizlendi',
            'message' => 'Tüm uyarılar temizlendi.',
            'type' => 'info'
        ]);
    }

    private function loadRealSystemSummary()
    {
        // Real system statistics from database
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $inactiveTenants = $totalTenants - $activeTenants;
        
        // API calls statistics from usage logs (son 24 saat)
        $apiCallsTotal = TenantUsageLog::where('resource_type', 'api')
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->sum('api_requests') ?? 0;
        
        // Database queries statistics
        $dbQueriesTotal = TenantUsageLog::where('resource_type', 'database')
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->sum('db_queries') ?? 0;
        
        // Cache operations statistics  
        $cacheOpsTotal = TenantUsageLog::where('resource_type', 'cache')
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->sum('usage_count') ?? 0;
        
        // AI operations statistics
        $aiOpsTotal = TenantUsageLog::where('resource_type', 'ai')
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->sum('usage_count') ?? 0;

        // Calculate percentages and trends based on previous period
        $previousApiCalls = TenantUsageLog::where('resource_type', 'api')
            ->where('recorded_at', '>=', Carbon::now()->subHours(48))
            ->where('recorded_at', '<', Carbon::now()->subHours(24))
            ->sum('api_requests') ?? 0;
        
        $apiTrend = $previousApiCalls > 0 ? ($apiCallsTotal > $previousApiCalls ? 'up' : ($apiCallsTotal < $previousApiCalls ? 'down' : 'stable')) : 'stable';
        $apiPercentage = $activeTenants > 0 ? round(($apiCallsTotal / ($activeTenants * 100)) * 100, 2) : 0; // Assume 100 calls per tenant as base
        
        $this->systemSummary = [
            'system_wide_stats' => [
                'total_tenants' => ['total' => $totalTenants, 'active' => $activeTenants, 'inactive' => $inactiveTenants],
                'api_calls' => ['total' => $apiCallsTotal, 'percentage' => min(100, $apiPercentage), 'trend' => $apiTrend],
                'database_queries' => ['total' => $dbQueriesTotal, 'percentage' => min(100, round(($dbQueriesTotal / max(1, $apiCallsTotal * 2)) * 100, 2)), 'trend' => 'stable'],
                'cache_operations' => ['total' => $cacheOpsTotal, 'percentage' => min(100, round(($cacheOpsTotal / max(1, $apiCallsTotal)) * 100, 2)), 'trend' => 'stable'],
                'ai_operations' => ['total' => $aiOpsTotal, 'percentage' => min(100, round(($aiOpsTotal / max(1, $apiCallsTotal * 0.3)) * 100, 2)), 'trend' => 'stable']
            ]
        ];
    }

    private function loadRealTenantUsage()
    {
        if (!$this->selectedTenantId) {
            $this->tenantUsage = [];
            return;
        }

        $tenant = Tenant::find($this->selectedTenantId);
        if (!$tenant) {
            $this->tenantUsage = [];
            return;
        }

        // Real tenant usage data from logs
        $hours = intval($this->timeRange);
        $usageLogs = TenantUsageLog::where('tenant_id', $this->selectedTenantId)
            ->where('recorded_at', '>=', Carbon::now()->subHours($hours))
            ->get();

        // Calculate current usage metrics
        $currentApiCalls = $usageLogs->where('resource_type', 'api')->sum('api_requests');
        $currentDbQueries = $usageLogs->where('resource_type', 'database')->sum('db_queries');
        $currentCacheOps = $usageLogs->where('resource_type', 'cache')->sum('usage_count');
        $currentAiOps = $usageLogs->where('resource_type', 'ai')->sum('usage_count');
        
        // Average resource usage
        $avgCpuUsage = $usageLogs->avg('cpu_usage_percent') ?? 0;
        $avgMemoryUsage = $usageLogs->avg('memory_usage_mb') ?? 0;
        $avgResponseTime = $usageLogs->avg('response_time_ms') ?? 0;
        $maxConnections = $usageLogs->max('active_connections') ?? 0;
        
        $this->tenantUsage = [
            'tenant_info' => [
                'id' => $tenant->id,
                'title' => $tenant->title,
                'is_active' => $tenant->is_active,
                'created_at' => $tenant->created_at->toDateString()
            ],
            'current_usage' => [
                'api_calls' => $currentApiCalls,
                'database_queries' => $currentDbQueries, 
                'cache_operations' => $currentCacheOps,
                'ai_operations' => $currentAiOps
            ],
            'resource_metrics' => [
                'cpu_usage_percent' => round($avgCpuUsage, 2),
                'memory_usage_mb' => round($avgMemoryUsage, 2),
                'response_time_ms' => round($avgResponseTime, 2),
                'active_connections' => $maxConnections
            ],
            'period' => [
                'hours' => $hours,
                'from' => Carbon::now()->subHours($hours)->toDateTimeString(),
                'to' => Carbon::now()->toDateTimeString()
            ]
        ];
    }

    public function render()
    {
        // System summary'yi view için uygun formata çevir
        $resourceStats = [];
        if ($this->systemSummary && is_array($this->systemSummary)) {
            // Eğer system_wide_stats varsa onu kullan, yoksa boş array
            $stats = $this->systemSummary['system_wide_stats'] ?? [];
            
            if (is_array($stats)) {
                foreach ($stats as $type => $data) {
                    $resourceStats[] = [
                        'type' => $type,
                        'current_usage' => $data['total'] ?? 0,
                        'usage_percentage' => $data['percentage'] ?? 0,
                        'trend' => $data['trend'] ?? 'stable',
                    ];
                }
            }
        }

        // Son log kayıtları için basit veri sağla
        $recentLogs = TenantUsageLog::with('tenant')
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('recorded_at', 'desc')
            ->take(20)
            ->get();

        return view('tenantmanagement::livewire.tenantmonitoring', [
            'tenants' => $this->tenantList,
            'usageLogs' => $this->filteredUsageLogs,
            'resourceStats' => $resourceStats,
            'recentLogs' => $recentLogs,
            'resourceTypes' => TenantResourceLimit::getResourceTypes(),
            'statusTypes' => TenantUsageLog::getStatusTypes(),
        ]);
    }
}