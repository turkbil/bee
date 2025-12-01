<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearAll extends Command
{
    protected $signature = 'app:clear-all';
    protected $description = 'Önbellekleri ve log dosyalarını temizler. STORAGE/MEDYA DOSYALARINA DOKUNMAZ!';

    public function handle()
    {
        $this->info('');
        $this->info('🧹 Güvenli Cache Temizleme Başlatıldı');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('');

        // 1. Laravel önbelleklerini temizle
        $this->info('📦 Laravel önbellekleri temizleniyor...');

        Artisan::call('cache:clear');
        $this->line('   ✓ cache:clear');

        Artisan::call('config:clear');
        $this->line('   ✓ config:clear');

        Artisan::call('route:clear');
        $this->line('   ✓ route:clear');

        Artisan::call('view:clear');
        $this->line('   ✓ view:clear');

        Artisan::call('event:clear');
        $this->line('   ✓ event:clear');

        Artisan::call('optimize:clear');
        $this->line('   ✓ optimize:clear');

        // 2. Response cache temizle (varsa)
        try {
            Artisan::call('responsecache:clear');
            $this->line('   ✓ responsecache:clear');
        } catch (\Exception $e) {
            // Paket yüklü değilse atla
        }

        $this->info('');

        // 3. Ana log dosyalarını BOŞALT (silme, truncate)
        $this->info('📄 Log dosyaları temizleniyor...');
        $this->truncateLogFiles(storage_path('logs'));

        // 4. Tenant log dosyalarını BOŞALT
        $storagePath = storage_path();
        $allDirs = File::directories($storagePath);

        foreach ($allDirs as $dir) {
            $dirName = basename($dir);
            if (str_starts_with($dirName, 'tenant')) {
                $tenantLogsPath = $dir . '/logs';
                if (File::exists($tenantLogsPath)) {
                    $this->truncateLogFiles($tenantLogsPath, $dirName);
                }
            }
        }

        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Temizlik tamamlandı!');
        $this->info('');
        $this->warn('⚠️  NOT: Storage ve medya dosyalarına DOKUNULMADI.');
        $this->info('');

        return Command::SUCCESS;
    }

    /**
     * Log dosyalarını truncate et (sil değil, içeriğini boşalt)
     */
    private function truncateLogFiles(string $path, string $label = 'ana')
    {
        if (!File::exists($path)) {
            return;
        }

        $logFiles = File::files($path);

        foreach ($logFiles as $file) {
            $filename = $file->getFilename();

            // Sadece .log dosyalarını işle
            if (!str_ends_with($filename, '.log')) {
                continue;
            }

            // .gitignore ve benzeri dosyaları atla
            if (str_starts_with($filename, '.')) {
                continue;
            }

            try {
                $filepath = $file->getPathname();

                // Dosya içeriğini boşalt (truncate)
                if (is_writable($filepath)) {
                    file_put_contents($filepath, '');
                    $this->line("   ✓ [{$label}] {$filename} temizlendi");
                } else {
                    $this->warn("   ⚠ [{$label}] {$filename} yazılamadı (izin yok)");
                }
            } catch (\Exception $e) {
                $this->warn("   ⚠ [{$label}] {$filename} hata: " . $e->getMessage());
            }
        }
    }
}
