<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Queue Health Check - Her 5 dakikada bir
        $schedule->command('queue:ensure-workers')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/queue-health.log'));

        // Laravel Queue Health Check - Her dakika
        $schedule->command('queue:health-check')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Failed Jobs Retry - Her saat
        $schedule->command('queue:retry --all')
                 ->hourly()
                 ->withoutOverlapping()
                 ->when(function () {
                     // Sadece başarısız job varsa çalıştır
                     return \DB::table('failed_jobs')->count() > 0;
                 });

        // Queue Statistics Collection - Her 10 dakika
        $schedule->call(function () {
            try {
                // Queue istatistiklerini cache'le
                $service = app(\App\Services\EnterpriseQueueHealthService::class);
                $stats = $service->getComprehensiveQueueStatus();
                
                \Cache::put('queue_statistics_snapshot', $stats, now()->addMinutes(15));
            } catch (\Exception $e) {
                \Log::error('Queue statistics collection failed: ' . $e->getMessage());
            }
        })->everyTenMinutes()->name('queue-stats-collection');

        // Clean old logs - Günlük
        $schedule->command('app:clear-all')
                 ->daily()
                 ->at('02:00')
                 ->withoutOverlapping();

        // Soft Delete Cleanup - 30 gün+ eski kayıtları kalıcı sil
        // Global Standart: GDPR uyumlu + SaaS best practices (Stripe, Google, Shopify)
        $schedule->command('model:prune')
                 ->daily()
                 ->at('03:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/pruning.log'));

        // Plesk Orphan Database Cleanup - Günlük (03:30)
        $schedule->command('plesk:clean-orphan-databases')
                 ->daily()
                 ->at('03:30')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/plesk-cleanup.log'));

        // TEST: Her 5 dakikada bir çalıştır (production'da kapat)
        // $schedule->command('plesk:clean-orphan-databases')
        //          ->everyFiveMinutes()
        //          ->withoutOverlapping()
        //          ->appendOutputTo(storage_path('logs/plesk-cleanup.log'));

        // Horizon Snapshot - Her 15 dakika (Horizon varsa)
        if (class_exists(\Laravel\Horizon\Console\SnapshotCommand::class)) {
            $schedule->command('horizon:snapshot')
                     ->everyFifteenMinutes()
                     ->withoutOverlapping();
        }

        // ENTERPRISE HORIZON MONITORING - Production Ready
        $schedule->call(function () {
            try {
                // Horizon durumunu kontrol et
                $output = null;
                $return_var = null;
                exec('php artisan horizon:status 2>/dev/null', $output, $return_var);

                if ($return_var !== 0 || empty($output)) {
                    // Horizon durmuş - Otomatik restart
                    \Log::warning('🚨 HORIZON DOWN DETECTED - Auto restarting...');

                    // Stuck processes'leri temizle
                    exec('pkill -f "horizon" 2>/dev/null');
                    sleep(2);

                    // Horizon'u background'da başlat
                    exec('cd ' . base_path() . ' && php artisan horizon > /dev/null 2>&1 &');

                    \Log::info('✅ HORIZON AUTO-RESTARTED successfully');
                } else {
                    \Log::debug('✅ Horizon health check passed');
                }
            } catch (\Exception $e) {
                \Log::error('❌ Horizon monitoring failed: ' . $e->getMessage());
            }
        })->everyFiveMinutes()->name('horizon-auto-restart');

        // CURRENCY RATES AUTO UPDATE - TCMB Daily Update (Every day at 15:30 after TCMB publishes)
        $schedule->command('currency:update-rates')
                 ->dailyAt('15:30')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/currency-updates.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}