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
        // 🔐 Subscription Verification - Her gün sabah 06:00 (Tenant 1001 - Muzibu)
        $schedule->command('subscriptions:verify')
                 ->dailyAt('06:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/subscription-verification.log'));

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

        // Telescope Prune - 1 gün üzeri kayıtları sil (Günlük 04:00)
        $schedule->command('telescope:prune --hours=24')
                 ->daily()
                 ->at('04:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/telescope-prune.log'));

        // Horizon Snapshot - Her 15 dakika (Horizon varsa)
        if (class_exists(\Laravel\Horizon\Console\SnapshotCommand::class)) {
            $schedule->command('horizon:snapshot')
                     ->everyFifteenMinutes()
                     ->withoutOverlapping();
        }

        // 🔧 HORIZON MONITORING - DISABLED (Supervisor handles restart)
        // ⚠️ BU AUTO-RESTART SORUNLUYDU!
        // - Her 5 dakikada pkill yapıyordu → Mevcut Horizon'ları öldürüyordu
        // - Background'da başlatıyordu (&) → Orphan process oluşuyordu
        // - Supervisor zaten Horizon'u yönetiyor → Çift başlatma problemi
        //
        // ÇÖZÜM: Supervisor config kullan (/etc/supervisor/conf.d/)
        // Eğer Supervisor yoksa, manuel systemd service oluştur
        //
        // $schedule->call(function () {
        //     // DISABLED - See comment above
        // })->everyFiveMinutes()->name('horizon-auto-restart');

        // CURRENCY RATES AUTO UPDATE - Artık ShopServiceProvider'da tanımlı
        // Bu satırlar ShopServiceProvider::registerCommandSchedules() metoduna taşındı

        // 📝 BLOG AUTO GENERATION - TEST MODE (Her 5 dakika)
        // Production'da hourly() kullan, test için everyFiveMinutes()

        $schedule->call(function () {
            try {
                $currentHour = (int) now()->format('H');
                $currentMinute = (int) now()->format('i');

                // Her tenant için kontrol et
                $tenants = \App\Models\Tenant::all();

                foreach ($tenants as $tenant) {
                    try {
                        tenancy()->initialize($tenant);

                        // Blog AI enabled mi?
                        $enabled = getTenantSetting('blog_ai_enabled', '0');
                        $enabled = ($enabled === '1' || $enabled === 1 || $enabled === true || $enabled === 'true');

                        if (!$enabled) {
                            tenancy()->end();
                            continue;
                        }

                        // 🔧 TEST MODE: Her 5 dakikada bir blog üret (saatlik sınırlama YOK!)
                        // Production'da: activeHours kontrolü ekle

                        // Command dispatch et
                        \Illuminate\Support\Facades\Artisan::call('generate:tenant-blogs', [
                            '--tenant-id' => $tenant->id
                        ]);

                        \Log::channel('daily')->info('🤖 Blog Cron Triggered (TEST MODE - 5min)', [
                            'tenant_id' => $tenant->id,
                            'time' => now()->format('H:i'),
                        ]);

                        tenancy()->end();

                    } catch (\Exception $e) {
                        \Log::error('Blog cron tenant error', [
                            'tenant_id' => $tenant->id ?? 'N/A',
                            'error' => $e->getMessage(),
                        ]);
                        tenancy()->end();
                    }
                }

            } catch (\Exception $e) {
                \Log::error('Blog cron scheduler error: ' . $e->getMessage());
            }
        })
        ->hourly() // Production: Saatlik çalışma
        ->name('blog-ai-dynamic-scheduler')
        ->withoutOverlapping(10)
        ->appendOutputTo(storage_path('logs/blog-cron.log'));

        // 📝 BLOG DRAFT REGENERATION - Haftalık (Her Pazar 02:00)
        $schedule->call(function () {
            try {
                $tenants = \App\Models\Tenant::all();
                foreach ($tenants as $tenant) {
                    tenancy()->initialize($tenant);

                    // Blog AI enabled mi?
                    $enabled = getTenantSetting('blog_ai_enabled', '0');
                    $enabled = ($enabled === '1' || $enabled === 1 || $enabled === true || $enabled === 'true');

                    if ($enabled) {
                        // Her Pazar 200 yeni draft üret (haftalık buffer)
                        \Modules\Blog\App\Jobs\GenerateDraftsJob::dispatch(200)
                            ->onQueue('blog-ai');

                        \Log::channel('daily')->info('🤖 Weekly Draft Generation', [
                            'tenant_id' => $tenant->id,
                            'draft_count' => 200,
                            'day' => 'Sunday',
                        ]);
                    }

                    tenancy()->end();
                }
            } catch (\Exception $e) {
                \Log::error('Weekly draft generation error: ' . $e->getMessage());
            }
        })
        ->weeklyOn(0, '02:00') // Pazar 02:00
        ->name('blog-draft-weekly-regeneration')
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/blog-draft-weekly.log'));

        // 🔐 SUBSCRIPTION MANAGEMENT CRONS

        // 🔗 Subscription Chain Transitions - Saatlik (active→expired, pending→active)
        $schedule->command('subscription:process-transitions')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/subscription-transitions.log'));

        // Check Expired Subscriptions - Günlük 06:00 (yeni unified command)
        $schedule->command('subscription:check-expired')
                 ->dailyAt('06:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/subscription-check.log'));

        // Trial Expiry Check - Günlük 09:00 (eski komut - backward compat)
        $schedule->command('subscription:check-trial')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/subscription-trial.log'));

        // Renewal Reminders - Günlük 10:00
        $schedule->command('subscription:send-reminders')
                 ->dailyAt('10:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/subscription-reminders.log'));

        // Process Recurring Payments - Günlük 06:00
        $schedule->command('subscription:process-recurring')
                 ->dailyAt('06:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/subscription-payments.log'));

        // Session Cleanup - Haftalık Pazar 03:00
        $schedule->command('auth:cleanup-sessions')
                 ->weeklyOn(0, '03:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/session-cleanup.log'));
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