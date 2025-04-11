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
                        // Log dosyalarını boşalt ama silme
                        try {
                            // İlk yöntem: doğrudan yazma
                            File::put($file->getPathname(), '');
                        } catch (\Exception $e) {
                            // İlk yöntem başarısız olursa, ikinci yöntemi dene
                            try {
                                // Dosyayı sil ve yeniden oluştur
                                if (File::exists($file->getPathname())) {
                                    File::delete($file->getPathname());
                                }
                                File::put($file->getPathname(), '');
                                $this->info("{$file->getPathname()} dosyası silindi ve yeniden oluşturuldu.");
                            } catch (\Exception $e2) {
                                // Üçüncü yöntem: fopen ile boşaltmayı dene
                                try {
                                    $handle = @fopen($file->getPathname(), 'w');
                                    if ($handle) {
                                        ftruncate($handle, 0);
                                        fclose($handle);
                                        $this->info("{$file->getPathname()} dosyası fopen ile temizlendi.");
                                    } else {
                                        throw new \Exception("Dosya açılamadı");
                                    }
                                } catch (\Exception $e3) {
                                    $this->warn("İzin hatası: {$file->getPathname()} dosyası hiçbir yöntemle temizlenemedi. Lütfen uygulamayı durdurup tekrar deneyin.");
                                }
                            }
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
                            // Log dosyalarını boşalt ama silme
                            try {
                                // İlk yöntem: doğrudan yazma
                                File::put($file->getPathname(), '');
                            } catch (\Exception $e) {
                                // İlk yöntem başarısız olursa, ikinci yöntemi dene
                                try {
                                    // Dosyayı sil ve yeniden oluştur
                                    if (File::exists($file->getPathname())) {
                                        File::delete($file->getPathname());
                                    }
                                    File::put($file->getPathname(), '');
                                    $this->info("{$file->getPathname()} dosyası silindi ve yeniden oluşturuldu.");
                                } catch (\Exception $e2) {
                                    // Üçüncü yöntem: fopen ile boşaltmayı dene
                                    try {
                                        $handle = @fopen($file->getPathname(), 'w');
                                        if ($handle) {
                                            ftruncate($handle, 0);
                                            fclose($handle);
                                            $this->info("{$file->getPathname()} dosyası fopen ile temizlendi.");
                                        } else {
                                            throw new \Exception("Dosya açılamadı");
                                        }
                                    } catch (\Exception $e3) {
                                        $this->warn("İzin hatası: {$file->getPathname()} dosyası hiçbir yöntemle temizlenemedi. Lütfen uygulamayı durdurup tekrar deneyin.");
                                    }
                                }
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