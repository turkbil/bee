<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearAll extends Command
{
    protected $signature = 'app:clear-all';
    protected $description = 'Tüm önbellekleri ve storage içindeki yüklenen dosyaları, fotoğrafları temizler';

    public function handle()
    {
        // Önbellekleri temizle
        $this->info('Önbellekler temizleniyor...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('event:clear');
        Artisan::call('optimize:clear');
        
        // bootstrap/cache içeriğini temizle
        $this->info('bootstrap/cache içeriği temizleniyor...');
        $bootstrapCache = base_path('bootstrap/cache');
        if (File::exists($bootstrapCache)) {
            $files = File::allFiles($bootstrapCache);
            foreach ($files as $file) {
                if (!str_contains($file->getFilename(), '.gitignore')) {
                    File::delete($file->getPathname());
                }
            }
        }
        
        // Ana storage içindeki klasörleri temizle
        $this->cleanStorageDirectories([
            'app',
            'debugbar',
            'framework/cache',
            'framework/sessions', 
            'framework/testing',
            'framework/views',
            'logs'
        ]);
        
        // Detaylı log temizliği
        $logsPath = storage_path('logs');
        if (File::exists($logsPath)) {
            $this->info('Log dosyaları temizleniyor...');
            $logFiles = File::allFiles($logsPath);
            foreach ($logFiles as $file) {
                if (!str_contains($file->getFilename(), '.gitignore')) {
                    if (str_contains($file->getFilename(), '.log')) {
                        // Log dosyalarını boşalt ama silme
                        File::put($file->getPathname(), '');
                    } else {
                        // Diğer dosyaları sil
                        File::delete($file->getPathname());
                    }
                }
            }
        }
        
        // Tenant klasörlerini temizle
        $tenantDirs = ['tenant1', 'tenant2', 'tenant3', 'tenant4'];
        foreach ($tenantDirs as $tenantDir) {
            $this->cleanStorageDirectories([
                $tenantDir . '/app',
                $tenantDir . '/debugbar',
                $tenantDir . '/framework/cache',
                $tenantDir . '/framework/sessions',
                $tenantDir . '/framework/testing',
                $tenantDir . '/framework/views',
                $tenantDir . '/logs',
                $tenantDir . '/sessions'
            ]);
            
            // Tenant log dosyalarını temizle
            $tenantLogsPath = storage_path($tenantDir . '/logs');
            if (File::exists($tenantLogsPath)) {
                $this->info("Tenant $tenantDir log dosyaları temizleniyor...");
                $logFiles = File::allFiles($tenantLogsPath);
                foreach ($logFiles as $file) {
                    if (!str_contains($file->getFilename(), '.gitignore')) {
                        if (str_contains($file->getFilename(), '.log')) {
                            // Log dosyalarını boşalt ama silme
                            File::put($file->getPathname(), '');
                        } else {
                            // Diğer dosyaları sil
                            File::delete($file->getPathname());
                        }
                    }
                }
            }
        }
        
        // Public storage içeriklerini temizle
        if (File::exists(public_path('storage'))) {
            $this->info('Public storage içeriği temizleniyor...');
            $directories = File::directories(public_path('storage'));
            
            foreach ($directories as $directory) {
                File::deleteDirectory($directory);
                // Klasörü yeniden oluştur
                File::makeDirectory($directory, 0755, true, true);
            }
            
            // Doğrudan storage altındaki dosyaları temizle (.gitignore hariç)
            $files = File::files(public_path('storage'));
            foreach ($files as $file) {
                if (!str_contains($file->getFilename(), '.gitignore')) {
                    File::delete($file->getPathname());
                }
            }
        }
        
        // Sonradan eklenen diğer storage klasörlerini temizle
        $extraStorageDirs = [
            'app/public',
            'app/livewire-tmp',
            'framework/cache/data',
            'framework/cache/laravel-excel',
            'framework/testing/disks'
        ];
        
        $this->cleanStorageDirectories($extraStorageDirs);
        
        $this->info('Tüm önbellekler, fotoğraflar ve yüklenen dosyalar başarıyla temizlendi!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Belirtilen storage dizinlerini temizle
     * 
     * @param array $directories
     * @return void
     */
    private function cleanStorageDirectories($directories)
    {
        foreach ($directories as $directory) {
            $path = storage_path($directory);
            
            if (File::exists($path)) {
                $this->info("$directory içeriği temizleniyor...");
                
                // Klasör içindeki dosyaları temizle (.gitignore hariç)
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if (!str_contains($file->getFilename(), '.gitignore')) {
                        File::delete($file->getPathname());
                    }
                }
                
                // Alt klasörleri temizle
                $subDirs = File::directories($path);
                foreach ($subDirs as $subDir) {
                    // Alt klasörü içindeki her şeyi sil
                    File::deleteDirectory($subDir);
                    // Klasörü yeniden oluştur
                    File::makeDirectory($subDir, 0755, true, true);
                }
            }
        }
    }
}