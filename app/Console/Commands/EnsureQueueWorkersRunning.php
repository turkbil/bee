<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnsureQueueWorkersRunning extends Command
{
    protected $signature = 'queue:ensure-workers';
    protected $description = 'Ensure all queue workers are running and restart if necessary';

    public function handle()
    {
        $this->info('🔧 Queue Worker Durumu Kontrol Ediliyor...');

        // Horizon durumunu kontrol et
        if ($this->isHorizonRunning()) {
            $this->info('✅ Laravel Horizon çalışıyor');
            $processCount = $this->getHorizonProcessCount();
            $this->info("📊 Aktif Horizon Worker: {$processCount}");

            Log::info('Queue health check: Horizon running with ' . $processCount . ' workers');

            $this->info('🎯 Queue Worker Kontrolü Tamamlandı');
            return Command::SUCCESS;
        }

        // Horizon çalışmıyorsa restart dene
        $this->warn('❌ Laravel Horizon çalışmıyor. Yeniden başlatılıyor...');

        try {
            $output = shell_exec('php ' . base_path('artisan') . ' horizon:terminate 2>&1');
            sleep(2);

            // Supervisor ile yeniden başlat
            if ($this->restartHorizonViaSupervisor()) {
                $this->info('✅ Horizon başarıyla yeniden başlatıldı');
                Log::info('Horizon restarted successfully');
            } else {
                $this->error('🚨 Horizon yeniden başlatılamadı!');
                Log::error('Failed to restart Horizon');
            }
        } catch (\Exception $e) {
            $this->error('🚨 Horizon restart hatası: ' . $e->getMessage());
            Log::error('Horizon restart error: ' . $e->getMessage());
        }

        $this->info('🎯 Queue Worker Kontrolü Tamamlandı');
        return Command::SUCCESS;
    }

    private function isHorizonRunning()
    {
        try {
            // Horizon status komutu ile kontrol et
            $output = shell_exec('php ' . base_path('artisan') . ' horizon:status 2>&1');
            return stripos($output, 'running') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getHorizonProcessCount()
    {
        try {
            $output = shell_exec('ps aux | grep -c "[h]orizon:work"');
            return (int) trim($output);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function restartHorizonViaSupervisor()
    {
        try {
            // Supervisor varsa kullan
            $supervisorStatus = shell_exec('supervisorctl status 2>&1');

            if (stripos($supervisorStatus, 'laravel-worker') !== false) {
                shell_exec('supervisorctl restart laravel-worker:* 2>&1');
                sleep(3);
                return $this->isHorizonRunning();
            }

            // Supervisor yoksa direkt başlat
            shell_exec('nohup php ' . base_path('artisan') . ' horizon > /dev/null 2>&1 &');
            sleep(3);
            return $this->isHorizonRunning();
        } catch (\Exception $e) {
            Log::error('Failed to restart Horizon: ' . $e->getMessage());
            return false;
        }
    }
}