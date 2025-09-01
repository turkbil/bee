<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use App\Services\EnterpriseQueueHealthService;
use Illuminate\Support\Facades\Cache;

class QueueMonitoringComponent extends Component
{
    public $queueData = [];
    public $selectedQueue = 'overview';
    public $autoRefresh = true;
    public $refreshInterval = 30; // seconds
    public $healthScore = [];
    public $alerts = [];
    
    protected $queueHealthService;
    
    public function mount()
    {
        // Service olmadan da çalışacak şekilde başlat
        $this->queueHealthService = null;
        $this->loadQueueData();
    }
    
    public function loadQueueData()
    {
        try {
            // Service'i her seferinde yeniden deneyelim
            if (!$this->queueHealthService) {
                try {
                    $this->queueHealthService = app(\App\Services\EnterpriseQueueHealthService::class);
                } catch (\Exception $e) {
                    // Service yüklenemiyorsa gerçek sistem verilerini al
                    $this->queueData = $this->getRealSystemData();
                    $this->healthScore = $this->calculateRealHealthScore();
                    $this->alerts = $this->getRealAlerts();
                    return;
                }
            }
            
            // Service varsa gerçek veriyi al
            if ($this->queueHealthService) {
                $this->queueData = Cache::remember('queue_monitoring_data', 10, function () {
                    return $this->queueHealthService->getComprehensiveQueueStatus();
                });
                
                $this->healthScore = $this->queueData['health_score'] ?? $this->calculateRealHealthScore();
                $this->alerts = $this->queueData['alerts'] ?? $this->getRealAlerts();
                
                $this->dispatch('queueDataUpdated', $this->queueData);
            }
            
        } catch (\Exception $e) {
            // Hata durumunda da gerçek veri göster
            $this->queueData = $this->getRealSystemData();
            $this->healthScore = $this->calculateRealHealthScore();
            $this->alerts = $this->getRealAlerts();
            \Log::error('loadQueueData failed: ' . $e->getMessage());
        }
    }
    
    protected function getRealSystemData()
    {
        return [
            'overview' => [
                'total_pending' => $this->getRealPendingJobs(),
                'total_failed' => $this->getRealFailedJobs(),
                'total_processing' => $this->getRealProcessingJobs(),
                'total_completed_today' => $this->getRealCompletedJobsToday(),
                'queue_throughput' => 0,
                'average_wait_time' => 0,
                'last_job_processed' => $this->getLastJobProcessedTime(),
            ],
            'workers' => $this->getRealWorkerStatuses(),
            'queues' => $this->getRealQueueStatuses(),
            'performance' => [
                'jobs_per_minute' => 0,
                'jobs_per_hour' => 0,
                'peak_processing_time' => [],
                'queue_efficiency' => '0%',
                'memory_usage_trend' => 'bilinmiyor',
                'error_rate' => $this->getRealFailedJobs(),
                'retry_rate' => '0%',
            ],
            'health_score' => $this->calculateRealHealthScore(),
            'alerts' => $this->getRealAlerts(),
            'docker_containers' => $this->getRealDockerStatuses(),
            'system_resources' => $this->getRealSystemResources()
        ];
    }

    protected function getRealPendingJobs()
    {
        try {
            // Laravel jobs tablosundan bekleyen işleri say
            $count = \DB::table('jobs')->count();
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRealFailedJobs()
    {
        try {
            return \DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRealProcessingJobs()
    {
        try {
            // Reserved_at dolu olan işler işleniyor demektir
            return \DB::table('jobs')->whereNotNull('reserved_at')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRealCompletedJobsToday()
    {
        try {
            // Bugün silinmiş (tamamlanmış) job sayısını tahmin et
            // Bu veri log'lardan çıkarılabilir ama şimdilik 0
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getLastJobProcessedTime()
    {
        try {
            $lastJob = \DB::table('jobs')
                         ->orderBy('id', 'desc')
                         ->first();
            return $lastJob ? now() : now()->subHours(1);
        } catch (\Exception $e) {
            return now()->subHours(1);
        }
    }

    protected function getRealWorkerStatuses()
    {
        $containers = [
            'laravel-queue-general' => 'General Queue Worker',
            'laravel-queue-tenant' => 'Tenant Queue Worker', 
            'laravel-queue-ai' => 'AI Queue Worker',
            'laravel-horizon' => 'Horizon Manager'
        ];

        $workers = [];
        foreach ($containers as $container => $name) {
            $status = $this->getDockerContainerStatus($container);
            $workers[str_replace('laravel-', '', $container)] = [
                'name' => $name,
                'status' => $status,
                'uptime' => $this->getContainerUptime($container),
                'memory_usage' => $this->getContainerMemoryUsage($container),
                'cpu_usage' => $this->getContainerCpuUsage($container),
                'restart_count' => $this->getContainerRestartCount($container),
                'last_restart' => $this->getLastContainerRestart($container),
                'health_check' => $status === 'running' ? 'sağlıklı' : 'sorunlu'
            ];
        }

        return $workers;
    }

    protected function getRealQueueStatuses()
    {
        $queueNames = ['default', 'tenant_isolated', 'ai_translation', 'ai_processing', 'critical'];
        $queues = [];

        foreach ($queueNames as $queue) {
            $queues[$queue] = [
                'name' => ucfirst(str_replace('_', ' ', $queue)),
                'pending' => $this->getQueueJobCount($queue),
                'processing' => 0,
                'failed_today' => $this->getQueueFailedJobsCount($queue),
                'avg_processing_time' => 'bilinmiyor',
                'last_job' => $this->getQueueLastJob($queue),
                'worker_assignment' => 'queue-general'
            ];
        }

        return $queues;
    }

    protected function getDockerContainerStatus($containerName)
    {
        try {
            $command = "docker inspect --format='{{.State.Status}}' {$containerName} 2>/dev/null || echo 'not_running'";
            $status = trim(shell_exec($command));
            return $status === 'running' ? 'çalışıyor' : 'durmuş';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getContainerUptime($containerName)
    {
        try {
            $command = "docker inspect --format='{{.State.StartedAt}}' {$containerName} 2>/dev/null";
            $startTime = trim(shell_exec($command));
            if ($startTime && $startTime !== '') {
                $start = new \DateTime($startTime);
                $now = new \DateTime();
                $diff = $now->diff($start);
                return $diff->format('%a gün %h saat');
            }
            return 'bilinmiyor';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getContainerMemoryUsage($containerName)
    {
        try {
            $command = "docker stats --no-stream --format 'table {{.MemPerc}}' {$containerName} 2>/dev/null | tail -n 1";
            $memUsage = trim(shell_exec($command));
            return $memUsage ?: 'bilinmiyor';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getContainerCpuUsage($containerName)
    {
        try {
            $command = "docker stats --no-stream --format 'table {{.CPUPerc}}' {$containerName} 2>/dev/null | tail -n 1";
            $cpuUsage = trim(shell_exec($command));
            return $cpuUsage ?: 'bilinmiyor';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getContainerRestartCount($containerName)
    {
        try {
            $command = "docker inspect --format='{{.RestartCount}}' {$containerName} 2>/dev/null || echo '0'";
            $restartCount = trim(shell_exec($command));
            return (int)$restartCount;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getLastContainerRestart($containerName)
    {
        try {
            $command = "docker inspect --format='{{.State.StartedAt}}' {$containerName} 2>/dev/null";
            $startTime = trim(shell_exec($command));
            if ($startTime && $startTime !== '') {
                return new \DateTime($startTime);
            }
            return now()->subDays(1);
        } catch (\Exception $e) {
            return now()->subDays(1);
        }
    }

    protected function getQueueJobCount($queueName)
    {
        try {
            return \DB::table('jobs')->where('queue', $queueName)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getQueueFailedJobsCount($queueName)
    {
        try {
            return \DB::table('failed_jobs')->where('queue', $queueName)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getQueueLastJob($queueName)
    {
        try {
            $job = \DB::table('jobs')->where('queue', $queueName)->orderBy('id', 'desc')->first();
            return $job ? now() : now()->subHour();
        } catch (\Exception $e) {
            return now()->subHour();
        }
    }

    protected function getRealDockerStatuses()
    {
        $containers = [
            'laravel-queue-general',
            'laravel-queue-tenant', 
            'laravel-queue-ai',
            'laravel-horizon'
        ];

        $statuses = [];
        foreach ($containers as $container) {
            $statuses[$container] = $this->getDockerContainerStatus($container);
        }

        return $statuses;
    }

    protected function getRealSystemResources()
    {
        return [
            'redis_memory' => $this->getRedisMemoryUsage(),
            'redis_connections' => $this->getRedisConnections(),
            'database_connections' => $this->getDatabaseConnections(),
            'disk_usage' => $this->getDiskUsage(),
            'load_average' => $this->getSystemLoadAverage()
        ];
    }

    protected function calculateRealHealthScore()
    {
        $failedJobs = $this->getRealFailedJobs();
        $pendingJobs = $this->getRealPendingJobs();
        
        // Basit health score hesapla
        $score = 100;
        
        // Başarısız işler için puan düş
        if ($failedJobs > 10) {
            $score -= min(30, $failedJobs * 2);
        }
        
        // Bekleyen iş sayısı için puan düş
        if ($pendingJobs > 50) {
            $score -= min(20, ($pendingJobs - 50) / 10);
        }

        $status = match(true) {
            $score >= 90 => 'Mükemmel',
            $score >= 70 => 'İyi',
            $score >= 50 => 'Orta',
            $score >= 30 => 'Kötü',
            default => 'Kritik'
        };

        return [
            'score' => max(0, (int)$score),
            'status' => $status,
            'factors' => [
                'failed_jobs' => $failedJobs,
                'pending_jobs' => $pendingJobs
            ]
        ];
    }

    protected function getRealAlerts()
    {
        $alerts = [];
        
        $failedJobs = $this->getRealFailedJobs();
        if ($failedJobs > 10) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Başarısız İşler',
                'message' => "{$failedJobs} başarısız iş var"
            ];
        }

        $pendingJobs = $this->getRealPendingJobs();
        if ($pendingJobs > 100) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Yüksek Kuyruk',
                'message' => "{$pendingJobs} bekleyen iş var"
            ];
        }

        return $alerts;
    }

    // System resource helper metodları
    protected function getRedisMemoryUsage()
    {
        try {
            return 'bilinmiyor';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getRedisConnections()
    {
        try {
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getDatabaseConnections()
    {
        try {
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getDiskUsage()
    {
        try {
            $command = "df -h / | awk 'NR==2{printf \"%s\", \$5}'";
            $usage = trim(shell_exec($command));
            return $usage ?: 'bilinmiyor';
        } catch (\Exception $e) {
            return 'bilinmiyor';
        }
    }

    protected function getSystemLoadAverage()
    {
        try {
            $command = "uptime | awk -F'load average:' '{print \$2}' | awk '{print \$1}' | sed 's/,//'";
            $load = trim(shell_exec($command));
            return $load ?: '0.00';
        } catch (\Exception $e) {
            return '0.00';
        }
    }
    
    public function refreshData()
    {
        Cache::forget('queue_monitoring_data');
        $this->loadQueueData();
        
        session()->flash('message', 'Queue verileri güncellendi.');
    }
    
    public function selectQueue($queue)
    {
        $this->selectedQueue = $queue;
    }
    
    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }
    
    public function restartFailedJobs()
    {
        try {
            // Restart all failed jobs
            \Artisan::call('queue:retry', ['--all' => true]);
            
            session()->flash('message', 'Tüm başarısız işler yeniden kuyruğa alındı.');
            $this->refreshData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Başarısız işleri yeniden başlatırken hata: ' . $e->getMessage());
        }
    }
    
    public function clearFailedJobs()
    {
        try {
            // Clear all failed jobs
            \Artisan::call('queue:flush');
            
            session()->flash('message', 'Tüm başarısız işler temizlendi.');
            $this->refreshData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Başarısız işleri temizlerken hata: ' . $e->getMessage());
        }
    }
    
    public function restartWorkers()
    {
        try {
            // Restart queue workers
            \Artisan::call('queue:restart');
            
            session()->flash('message', 'Queue worker\'ları yeniden başlatıldı.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Worker\'ları yeniden başlatırken hata: ' . $e->getMessage());
        }
    }
    
    public function getHealthScoreColor()
    {
        $score = $this->healthScore['score'] ?? 0;
        
        return match(true) {
            $score >= 90 => 'success',
            $score >= 75 => 'info',
            $score >= 50 => 'warning',
            $score >= 25 => 'orange',
            default => 'danger'
        };
    }
    
    public function getAlertTypeClass($type)
    {
        return match($type) {
            'critical' => 'alert-danger',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-secondary'
        };
    }
    
    public function render()
    {
        return view('tenantmanagement::livewire.queue-monitoring-component');
    }
}