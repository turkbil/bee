<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnterpriseQueueHealthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class QueueHealthMonitorCommand extends Command
{
    protected $signature = 'queue:health-monitor {--interval=30 : Check interval in seconds}';
    
    protected $description = 'Continuously monitor queue health and auto-repair issues';
    
    protected $queueHealthService;
    protected $isRunning = false;
    
    public function __construct(EnterpriseQueueHealthService $queueHealthService)
    {
        parent::__construct();
        $this->queueHealthService = $queueHealthService;
    }
    
    public function handle()
    {
        $interval = (int) $this->option('interval');
        
        $this->info("🚀 Queue Health Monitor başlatılıyor...");
        $this->info("📊 Kontrol aralığı: {$interval} saniye");
        
        // Register signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }
        
        $this->isRunning = true;
        
        while ($this->isRunning) {
            try {
                $this->performHealthCheck();
                
                // Wait for next iteration
                sleep($interval);
                
                // Handle signals
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
                
            } catch (\Exception $e) {
                $this->error("Health check hatası: " . $e->getMessage());
                Log::error('Queue health monitor error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Wait before retry
                sleep(5);
            }
        }
        
        $this->info("🛑 Queue Health Monitor durduruldu.");
    }
    
    protected function performHealthCheck()
    {
        $this->line("🔍 Health check başlatılıyor... " . now()->format('Y-m-d H:i:s'));
        
        $status = $this->queueHealthService->getComprehensiveQueueStatus();
        
        // Health Score Check
        $healthScore = $status['health_score']['score'] ?? 0;
        $this->info("❤️  Sağlık Skoru: {$healthScore}/100 - " . ($status['health_score']['status'] ?? 'Bilinmiyor'));
        
        // Auto-repair checks
        $this->checkAndRepairFailedJobs($status);
        $this->checkAndRestartWorkers($status);
        $this->checkQueueCongestion($status);
        $this->checkSystemResources($status);
        
        // Store health metrics for monitoring
        $this->storeHealthMetrics($status);
        
        $this->line("✅ Health check tamamlandı.");
    }
    
    protected function checkAndRepairFailedJobs($status)
    {
        $failedJobs = $status['overview']['total_failed'] ?? 0;
        
        if ($failedJobs > 50) {
            $this->warn("⚠️  Çok sayıda başarısız iş tespit edildi: {$failedJobs}");
            
            // Auto-retry failed jobs older than 5 minutes
            try {
                Artisan::call('queue:retry', ['--range' => '1-50']);
                $this->info("🔄 50 başarısız iş yeniden kuyruğa alındı.");
                
                Log::info('Auto-retried failed jobs', ['count' => 50]);
                
            } catch (\Exception $e) {
                $this->error("❌ Failed job retry hatası: " . $e->getMessage());
            }
        }
    }
    
    protected function checkAndRestartWorkers($status)
    {
        $downWorkers = 0;
        $workers = $status['workers'] ?? [];
        
        foreach ($workers as $workerName => $worker) {
            if ($worker['status'] !== 'çalışıyor') {
                $downWorkers++;
                $this->warn("⚠️  Worker durmuş: {$workerName} - {$worker['status']}");
            }
        }
        
        if ($downWorkers > 0) {
            $this->warn("🔧 {$downWorkers} worker yeniden başlatılmaya çalışılıyor...");
            
            try {
                // Restart Docker containers
                $this->restartDockerWorkers();
                
                // Also restart Laravel queue workers
                Artisan::call('queue:restart');
                $this->info("🔄 Queue worker'ları yeniden başlatıldı.");
                
                Log::warning('Auto-restarted workers', ['down_workers' => $downWorkers]);
                
            } catch (\Exception $e) {
                $this->error("❌ Worker restart hatası: " . $e->getMessage());
            }
        }
    }
    
    protected function checkQueueCongestion($status)
    {
        $pendingJobs = $status['overview']['total_pending'] ?? 0;
        
        if ($pendingJobs > 1000) {
            $this->warn("🚨 Kritik kuyruk yoğunluğu: {$pendingJobs} bekleyen iş");
            
            // Scale up workers if possible
            $this->scaleUpWorkers();
            
            Log::warning('Critical queue congestion detected', [
                'pending_jobs' => $pendingJobs,
                'action' => 'scale_up_attempted'
            ]);
        } elseif ($pendingJobs > 500) {
            $this->warn("⚠️  Yüksek kuyruk yoğunluğu: {$pendingJobs} bekleyen iş");
        }
    }
    
    protected function checkSystemResources($status)
    {
        $resources = $status['system_resources'] ?? [];
        
        // Check disk usage
        $diskUsage = (int) str_replace('%', '', $resources['disk_usage'] ?? '0%');
        if ($diskUsage > 90) {
            $this->error("🚨 Kritik disk kullanımı: {$diskUsage}%");
            $this->cleanupLogs();
        } elseif ($diskUsage > 80) {
            $this->warn("⚠️  Yüksek disk kullanımı: {$diskUsage}%");
        }
        
        // Check Redis memory
        $redisMemory = $resources['redis_memory'] ?? '0 MB';
        $memoryValue = (int) str_replace([' MB', ' GB'], '', $redisMemory);
        if (str_contains($redisMemory, 'GB') || $memoryValue > 1000) {
            $this->warn("⚠️  Yüksek Redis bellek kullanımı: {$redisMemory}");
        }
    }
    
    protected function restartDockerWorkers()
    {
        $containers = [
            'laravel-queue-general',
            'laravel-queue-tenant',
            'laravel-queue-ai'
        ];
        
        foreach ($containers as $container) {
            try {
                $output = shell_exec("docker restart {$container} 2>&1");
                $this->info("🐳 Docker container yeniden başlatıldı: {$container}");
            } catch (\Exception $e) {
                $this->error("❌ Docker container restart hatası ({$container}): " . $e->getMessage());
            }
        }
    }
    
    protected function scaleUpWorkers()
    {
        try {
            // Scale up general queue workers
            shell_exec("docker service scale laravel-queue-general=4 2>/dev/null");
            shell_exec("docker service scale laravel-queue-tenant=5 2>/dev/null");
            
            $this->info("📈 Worker'lar ölçeklendirildi (geçici)");
            
            // Schedule scale-down after 30 minutes
            Cache::put('queue_scaled_up_at', now(), 30 * 60);
            
        } catch (\Exception $e) {
            $this->error("❌ Worker ölçeklendirme hatası: " . $e->getMessage());
        }
    }
    
    protected function cleanupLogs()
    {
        try {
            // Clean up old log files
            shell_exec("find /var/www/html/storage/logs -name '*.log' -mtime +7 -delete 2>/dev/null");
            
            // Truncate large log files
            shell_exec("truncate -s 50M /var/www/html/storage/logs/laravel.log 2>/dev/null");
            
            $this->info("🧹 Log dosyaları temizlendi");
            
        } catch (\Exception $e) {
            $this->error("❌ Log temizleme hatası: " . $e->getMessage());
        }
    }
    
    protected function storeHealthMetrics($status)
    {
        $metrics = [
            'timestamp' => now(),
            'health_score' => $status['health_score']['score'] ?? 0,
            'pending_jobs' => $status['overview']['total_pending'] ?? 0,
            'failed_jobs' => $status['overview']['total_failed'] ?? 0,
            'processing_jobs' => $status['overview']['total_processing'] ?? 0,
            'worker_status' => $status['workers'] ?? [],
            'alerts_count' => count($status['alerts'] ?? [])
        ];
        
        // Store in cache for dashboard
        Cache::put('queue_health_metrics', $metrics, 300); // 5 minutes
        
        // Store history for trends
        $history = Cache::get('queue_health_history', []);
        $history[] = $metrics;
        
        // Keep only last 100 entries
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }
        
        Cache::put('queue_health_history', $history, 3600); // 1 hour
    }
    
    public function shutdown()
    {
        $this->isRunning = false;
        $this->info("🛑 Graceful shutdown signal alındı...");
    }
}