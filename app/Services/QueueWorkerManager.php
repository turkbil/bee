<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueWorkerManager
{
    private const WORKER_STATUS_KEY = 'queue_worker_status';
    private const WORKER_PID_KEY = 'queue_worker_pid';
    
    public function isWorkerRunning(): bool
    {
        $pid = Cache::get(self::WORKER_PID_KEY);
        
        if (!$pid) {
            return false;
        }
        
        // Check if process is actually running
        $result = Process::run("ps -p {$pid}");
        return $result->successful();
    }
    
    public function startWorker(): bool
    {
        try {
            // Kill existing worker if any
            $this->stopWorker();
            
            // Start new worker in background - TÜM QUEUE'LARI DİNLE
            $command = 'nohup php ' . base_path() . '/artisan queue:work --queue=default,module_permissions,translation,high,low --sleep=3 --tries=3 --timeout=120 > /dev/null 2>&1 & echo $!';
            $result = Process::run($command);
            
            if ($result->successful()) {
                $pid = trim($result->output());
                Cache::put(self::WORKER_PID_KEY, $pid, now()->addHours(24));
                Cache::put(self::WORKER_STATUS_KEY, 'running', now()->addHours(24));
                
                Log::info("Queue worker started with PID: {$pid}");
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to start queue worker: ' . $e->getMessage());
            return false;
        }
    }
    
    public function stopWorker(): bool
    {
        $pid = Cache::get(self::WORKER_PID_KEY);
        
        if ($pid) {
            Process::run("kill -TERM {$pid}");
            Cache::forget(self::WORKER_PID_KEY);
            Cache::forget(self::WORKER_STATUS_KEY);
            Log::info("Queue worker stopped (PID: {$pid})");
        }
        
        return true;
    }
    
    public function restartWorker(): bool
    {
        $this->stopWorker();
        sleep(2); // Wait for graceful shutdown
        return $this->startWorker();
    }
    
    public function ensureWorkerRunning(): bool
    {
        if (!$this->isWorkerRunning()) {
            Log::warning('Queue worker not running, starting automatically...');
            return $this->startWorker();
        }
        
        return true;
    }
    
    public function getWorkerStatus(): array
    {
        return [
            'running' => $this->isWorkerRunning(),
            'pid' => Cache::get(self::WORKER_PID_KEY),
            'status' => Cache::get(self::WORKER_STATUS_KEY, 'stopped'),
            'last_check' => now()->toDateTimeString()
        ];
    }
}