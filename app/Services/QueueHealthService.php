<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class QueueHealthService
{
    /**
     * 🚀 OTOMATIK QUEUE HEALTH CHECK & RESTART SYSTEM
     * Manuel müdahale olmadan queue'ları otomatik yönetir
     */
    public static function checkAndFixQueueHealth(): array
    {
        $results = [
            'queue_workers_status' => 'unknown',
            'failed_jobs_cleared' => 0,
            'actions_taken' => [],
            'health_score' => 0
        ];

        try {
            // 1. Failed jobs kontrolü ve otomatik temizleme
            $failedJobsCount = DB::table('failed_jobs')->count();
            if ($failedJobsCount > 0) {
                Artisan::call('queue:flush');
                $results['failed_jobs_cleared'] = $failedJobsCount;
                $results['actions_taken'][] = "Cleared {$failedJobsCount} failed jobs automatically";
                Log::info('🧹 Otomatik failed jobs temizliği', ['cleared_count' => $failedJobsCount]);
            }

            // 2. Syntax error kontrolü
            $syntaxCheck = self::checkSyntaxErrors();
            if (!$syntaxCheck['clean']) {
                $results['actions_taken'][] = 'Syntax errors detected: ' . implode(', ', $syntaxCheck['errors']);
                $results['health_score'] = 30; // Düşük skor
                return $results;
            }

            // 3. Queue restart signal (worker'lar kendi kendine restart eder)
            Artisan::call('queue:restart');
            $results['actions_taken'][] = 'Queue restart signal sent (workers will auto-restart)';

            // 🔧 HORIZON AUTO-START DISABLED - ORPHAN PROCESS SORUNU!
            // ⚠️ Bu kod her çağrıda yeni Horizon spawn ediyordu → 234 process patlaması!
            // Horizon yönetimi Supervisor/Systemd'ye bırakıldı
            //
            // $horizonStatus = self::checkHorizonStatus();
            // if (!$horizonStatus['active']) {
            //     try {
            //         $command = 'nohup php ' . base_path('artisan') . ' horizon > /dev/null 2>&1 &';
            //         shell_exec($command);
            //         $results['actions_taken'][] = 'Horizon monitoring started automatically';
            //         Log::info('🚀 Horizon otomatik başlatıldı');
            //     } catch (\Exception $e) {
            //         $results['actions_taken'][] = 'Horizon start failed: ' . $e->getMessage();
            //     }
            // }

            // 4. Health score hesaplama
            $results['health_score'] = 100;
            $results['queue_workers_status'] = 'healthy';

            Log::info('✅ Queue health check completed successfully', $results);

        } catch (\Exception $e) {
            Log::error('❌ Queue health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $results['actions_taken'][] = 'Health check failed: ' . $e->getMessage();
            $results['health_score'] = 0;
        }

        return $results;
    }

    /**
     * 🔍 Syntax error kontrolü - critical files
     */
    private static function checkSyntaxErrors(): array
    {
        $criticalFiles = [
            base_path('Modules/AI/app/Services/AIService.php'),
            base_path('Modules/Page/app/Jobs/TranslatePageJob.php'),
            base_path('Modules/Page/app/Http/Livewire/Admin/PageComponent.php'),
        ];

        $errors = [];
        foreach ($criticalFiles as $file) {
            if (!file_exists($file)) {
                $errors[] = "Missing file: {$file}";
                continue;
            }

            $output = shell_exec("php -l {$file} 2>&1");
            if (strpos($output, 'No syntax errors') === false) {
                $errors[] = "Syntax error in: {$file}";
            }
        }

        return [
            'clean' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * 🔍 Horizon status kontrolü
     */
    private static function checkHorizonStatus(): array
    {
        // Horizon process kontrolü
        $output = shell_exec('ps aux | grep horizon | grep -v grep');
        $isActive = !empty(trim($output));
        
        return [
            'active' => $isActive,
            'process_count' => $isActive ? substr_count($output, 'horizon') : 0
        ];
    }

    /**
     * 📊 Queue sistem durumu raporu
     */
    public static function getQueueSystemReport(): array
    {
        $horizonStatus = self::checkHorizonStatus();
        
        return [
            'pending_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'horizon_active' => $horizonStatus['active'],
            'horizon_processes' => $horizonStatus['process_count'],
            'last_health_check' => now()->toDateTimeString(),
            'system_status' => $horizonStatus['active'] ? 'operational' : 'horizon_inactive'
        ];
    }
}