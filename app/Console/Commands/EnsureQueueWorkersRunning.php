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
        
        $requiredContainers = [
            'laravel-queue-general',
            'laravel-queue-tenant',
            'laravel-queue-ai',
            'laravel-horizon'
        ];

        $restartedContainers = [];
        
        foreach ($requiredContainers as $containerName) {
            $status = $this->checkContainerStatus($containerName);
            
            if (!$status['running']) {
                $this->warn("❌ {$containerName} çalışmıyor. Yeniden başlatılıyor...");
                
                if ($this->restartContainer($containerName)) {
                    $restartedContainers[] = $containerName;
                    $this->info("✅ {$containerName} başarıyla yeniden başlatıldı");
                } else {
                    $this->error("🚨 {$containerName} yeniden başlatılamadı!");
                }
            } else {
                $this->info("✅ {$containerName} çalışıyor");
            }
        }

        if (count($restartedContainers) > 0) {
            Log::info('Queue workers restarted: ' . implode(', ', $restartedContainers));
            
            // Docker compose ile tüm worker'ları yeniden başlat
            $this->startMissingWorkers();
        }

        $this->info('🎯 Queue Worker Kontrolü Tamamlandı');
        
        return Command::SUCCESS;
    }

    private function checkContainerStatus($containerName)
    {
        try {
            // Docker container durumu kontrol et
            $command = "docker inspect --format='{{.State.Status}}' {$containerName} 2>/dev/null";
            $status = trim(shell_exec($command));
            
            return [
                'running' => $status === 'running',
                'status' => $status ?: 'not_found'
            ];
        } catch (\Exception $e) {
            return ['running' => false, 'status' => 'error'];
        }
    }

    private function restartContainer($containerName)
    {
        try {
            // Container'ı yeniden başlat
            $restartCommand = "docker restart {$containerName} 2>/dev/null";
            $result = shell_exec($restartCommand);
            
            // 3 saniye bekle ve kontrol et
            sleep(3);
            $status = $this->checkContainerStatus($containerName);
            
            return $status['running'];
        } catch (\Exception $e) {
            Log::error("Failed to restart container {$containerName}: " . $e->getMessage());
            return false;
        }
    }

    private function startMissingWorkers()
    {
        try {
            $this->info('📦 Eksik worker container\'ları başlatılıyor...');
            
            // Docker compose ile queue worker'ları başlat
            $composeCommand = "cd " . base_path() . " && docker-compose up -d horizon queue-general queue-tenant queue-ai";
            
            $output = shell_exec($composeCommand . ' 2>&1');
            
            if ($output) {
                $this->line($output);
            }
            
            // Başlatılan container'ları kontrol et
            sleep(5);
            $this->info('🔍 Başlatılan container\'lar kontrol ediliyor...');
            
            $requiredContainers = [
                'laravel-queue-general',
                'laravel-queue-tenant', 
                'laravel-queue-ai',
                'laravel-horizon'
            ];
            
            foreach ($requiredContainers as $container) {
                $status = $this->checkContainerStatus($container);
                if ($status['running']) {
                    $this->info("✅ {$container} başarıyla çalışıyor");
                } else {
                    $this->error("❌ {$container} başlatılamadı - Status: {$status['status']}");
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to start missing workers: ' . $e->getMessage());
            $this->error('Worker başlatma hatası: ' . $e->getMessage());
        }
    }
}