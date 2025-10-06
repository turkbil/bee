<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Log;

/**
 * Storage Tenancy Bootstrapper
 * Tenant başlatıldığında storage klasörleri ve symlink'leri otomatik oluşturur
 */
class StorageTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $tenantId = $tenant->id;

        // Storage klasörlerini oluştur (yoksa)
        $this->ensureStorageDirectories($tenantId);

        // Public symlink oluştur (yoksa)
        $this->ensurePublicSymlink($tenantId);
    }

    public function revert()
    {
        // Storage revert işlemi yapmıyoruz (güvenlik)
    }

    /**
     * Tenant storage klasörlerini oluştur (yoksa)
     */
    private function ensureStorageDirectories($tenantId): void
    {
        $storagePath = storage_path("tenant{$tenantId}");

        // Ana klasör yoksa oluştur
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
            Log::info("✅ Created storage for tenant{$tenantId}");
        }

        // Alt klasörleri oluştur
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
            }
        }
    }

    /**
     * Public symlink oluştur (yoksa)
     */
    private function ensurePublicSymlink($tenantId): void
    {
        $targetPath = storage_path("tenant{$tenantId}/app/public");
        $linkPath = public_path("storage/tenant{$tenantId}");

        // Symlink yoksa veya kırıksa yeniden oluştur
        if (!is_link($linkPath) || !file_exists($linkPath)) {
            // Eski kırık link varsa sil
            if (is_link($linkPath)) {
                unlink($linkPath);
            }

            // Hedef klasör varsa symlink oluştur
            if (is_dir($targetPath)) {
                symlink($targetPath, $linkPath);
                Log::debug("✅ Created symlink for tenant{$tenantId}");
            }
        }
    }
}
