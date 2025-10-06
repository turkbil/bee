<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class EnsureTenantStorage extends Command
{
    protected $signature = 'tenants:ensure-storage';
    protected $description = 'Tüm tenant\'lar için storage klasörleri ve symlink\'leri oluştur';

    public function handle()
    {
        $tenants = Tenant::all();

        $this->info("🔍 Toplam {$tenants->count()} tenant bulundu");

        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $this->info("📦 Tenant {$tenantId} kontrol ediliyor...");

            // Storage klasörlerini oluştur
            $this->ensureStorageDirectories($tenantId);

            // Symlink oluştur
            $this->ensurePublicSymlink($tenantId);

            $this->line("✅ Tenant {$tenantId} hazır");
        }

        $this->info("🎉 Tamamlandı!");

        return 0;
    }

    private function ensureStorageDirectories($tenantId): void
    {
        $storagePath = storage_path("tenant{$tenantId}");

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
            $this->line("  ✅ Storage klasörü oluşturuldu");
        }

        $directories = [
            'framework/cache',
            'framework/sessions',
            'framework/views',
            'app/public',
            'logs',
        ];

        foreach ($directories as $dir) {
            $fullPath = "{$storagePath}/{$dir}";
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0775, true);
                $this->line("  ✅ {$dir} oluşturuldu");
            }
        }
    }

    private function ensurePublicSymlink($tenantId): void
    {
        $targetPath = storage_path("tenant{$tenantId}/app/public");
        $linkPath = public_path("storage/tenant{$tenantId}");

        if (!is_link($linkPath) || !file_exists($linkPath)) {
            if (is_link($linkPath)) {
                unlink($linkPath);
            }

            if (is_dir($targetPath)) {
                symlink($targetPath, $linkPath);
                $this->line("  ✅ Symlink oluşturuldu");
            }
        }
    }
}
