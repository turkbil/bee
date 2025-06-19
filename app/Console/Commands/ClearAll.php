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
                    try {
                        File::delete($file->getPathname());
                    } catch (\Exception $e) {
                        $this->warn("İzin hatası: {$file->getPathname()} dosyası silinemedi.");
                    }
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
                        // Log dosyalarını güvenli şekilde temizle
                        try {
                            // Dosyanın izinlerini kontrol et
                            if (is_writable($file->getPathname())) {
                                // Dosya yazılabilir ise içeriğini boşalt
                                file_put_contents($file->getPathname(), '', LOCK_EX);
                                $this->info("{$file->getFilename()} dosyası temizlendi.");
                            } else {
                                // Dosya yazılabilir değilse, silinmeye çalış
                                if (File::delete($file->getPathname())) {
                                    // Silinirse yeni boş dosya oluştur doğru izinlerle
                                    touch($file->getPathname());
                                    chmod($file->getPathname(), 0664);
                                    $this->info("{$file->getFilename()} dosyası silindi ve yeniden oluşturuldu.");
                                } else {
                                    $this->warn("İzin hatası: {$file->getFilename()} dosyası temizlenemedi.");
                                }
                            }
                        } catch (\Exception $e) {
                            $this->warn("Hata: {$file->getFilename()} - " . $e->getMessage());
                        }
                    } else {
                        // Diğer dosyaları sil
                        try {
                            File::delete($file->getPathname());
                        } catch (\Exception $e) {
                            $this->warn("İzin hatası: {$file->getPathname()} dosyası silinemedi.");
                        }
                    }
                }
            }
        }
        
        // Tenant klasörlerini otomatik olarak tespit et ve temizle
        $tenantDirs = [];
        $storagePath = storage_path();
        $allDirs = File::directories($storagePath);
        
        // Tenant ile başlayan klasörleri bul
        foreach ($allDirs as $dir) {
            $dirName = basename($dir);
            if (str_starts_with($dirName, 'tenant')) {
                $tenantDirs[] = $dirName;
            }
        }
        
        if (empty($tenantDirs)) {
            $this->info('Tenant klasörü bulunamadı.');
        } else {
            $this->info(count($tenantDirs) . ' adet tenant klasörü bulundu: ' . implode(', ', $tenantDirs));
        }
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
                            // Log dosyalarını güvenli şekilde temizle
                            try {
                                // Dosyanın izinlerini kontrol et
                                if (is_writable($file->getPathname())) {
                                    // Dosya yazılabilir ise içeriğini boşalt
                                    file_put_contents($file->getPathname(), '', LOCK_EX);
                                    $this->info("Tenant {$file->getFilename()} dosyası temizlendi.");
                                } else {
                                    // Dosya yazılabilir değilse, silinmeye çalış
                                    if (File::delete($file->getPathname())) {
                                        // Silinirse yeni boş dosya oluştur doğru izinlerle
                                        touch($file->getPathname());
                                        chmod($file->getPathname(), 0664);
                                        $this->info("Tenant {$file->getFilename()} dosyası silindi ve yeniden oluşturuldu.");
                                    } else {
                                        $this->warn("İzin hatası: Tenant {$file->getFilename()} dosyası temizlenemedi.");
                                    }
                                }
                            } catch (\Exception $e) {
                                $this->warn("Hata: Tenant {$file->getFilename()} - " . $e->getMessage());
                            }
                        } else {
                            // Diğer dosyaları sil
                            try {
                                File::delete($file->getPathname());
                            } catch (\Exception $e) {
                                $this->warn("İzin hatası: {$file->getPathname()} dosyası silinemedi.");
                            }
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
                try {
                    File::deleteDirectory($directory);
                    // Klasörü yeniden oluştur
                    File::makeDirectory($directory, 0755, true, true);
                } catch (\Exception $e) {
                    $this->warn("İzin hatası: {$directory} klasörü işlenemedi.");
                }
            }
            
            // Doğrudan storage altındaki dosyaları temizle (.gitignore hariç)
            $files = File::files(public_path('storage'));
            foreach ($files as $file) {
                if (!str_contains($file->getFilename(), '.gitignore')) {
                    try {
                        File::delete($file->getPathname());
                    } catch (\Exception $e) {
                        $this->warn("İzin hatası: {$file->getPathname()} dosyası silinemedi.");
                    }
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
                        try {
                            File::delete($file->getPathname());
                        } catch (\Exception $e) {
                            $this->warn("İzin hatası: {$file->getPathname()} dosyası silinemedi.");
                        }
                    }
                }
                
                // Alt klasörleri temizle
                $subDirs = File::directories($path);
                foreach ($subDirs as $subDir) {
                    // Alt klasörü içindeki her şeyi sil
                    try {
                        File::deleteDirectory($subDir);
                        // Klasörü yeniden oluştur
                        File::makeDirectory($subDir, 0755, true, true);
                    } catch (\Exception $e) {
                        $this->warn("İzin hatası: {$subDir} klasörü işlenemedi.");
                    }
                }
            }
        }
    }
}