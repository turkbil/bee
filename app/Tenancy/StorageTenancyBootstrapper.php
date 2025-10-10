<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Config;
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

        // Disk yapılandırmasını tenant köküne göre ayarla
        $this->configureFilesystemDisks($tenantId);
    }

    public function revert()
    {
        // Tenant ayrıldığında disk yapılandırmasını varsayılan değerlere döndür
        $this->resetFilesystemDisks();
    }

    /**
     * Tenant storage klasörlerini oluştur (yoksa)
     */
    private function ensureStorageDirectories($tenantId): void
    {
        $storagePath = storage_path("tenant{$tenantId}");

        // Ana storage klasörünün owner:group bilgisini al
        $parentStorage = storage_path();
        $stat = @stat($parentStorage);
        $owner = $stat ? posix_getpwuid($stat['uid'])['name'] : null;
        $group = $stat ? posix_getgrgid($stat['gid'])['name'] : null;

        // Ana klasör yoksa oluştur
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);

            // Ownership ve permission düzelt
            if ($owner && $group) {
                @chown($storagePath, $owner);
                @chgrp($storagePath, $group);
                @chmod($storagePath, 0775);
            }

            Log::info("✅ Created storage for tenant{$tenantId}");
        }

        // Alt klasörleri oluştur
        $directories = [
            'framework/cache',
            'framework/cache/data',         // Laravel cache data
            'framework/sessions',
            'framework/views',
            'app/public',
            'app/public/widgets',            // Widget dosyaları
            'app/public/settings/files',     // Setting dosyaları (public)
            'app/public/settings/images',    // Logo vb. public görüntüler
            'app/livewire-tmp',              // Livewire dosya upload için gerekli
            'media-library/temp',            // Media upload geçici dosyalar
            'settings/files',                // Legacy path, backward compatibility
            'logs',
        ];

        foreach ($directories as $dir) {
            $fullPath = "{$storagePath}/{$dir}";
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0775, true);

                // Ownership ve permission düzelt
                if ($owner && $group) {
                    @chown($fullPath, $owner);
                    @chgrp($fullPath, $group);
                    @chmod($fullPath, 0775);
                }
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

                $stat = @lstat(public_path('storage'));
                $owner = $stat ? posix_getpwuid($stat['uid'])['name'] : null;
                $group = $stat ? posix_getgrgid($stat['gid'])['name'] : null;

                if ($owner) {
                    @lchown($linkPath, $owner);
                }

                if ($group) {
                    @lchgrp($linkPath, $group);
                }

                Log::debug("✅ Created symlink for tenant{$tenantId}");
            }
        }
    }

    /**
     * Tenant için filesystem disk yapılandırmasını günceller
     */
    private function configureFilesystemDisks($tenantId): void
    {
        $rootPath = storage_path("tenant{$tenantId}/app/public");
        $url = config('app.url') . "/storage/tenant{$tenantId}";

        Config::set('filesystems.disks.public.root', $rootPath);
        Config::set('filesystems.disks.public.url', $url);

        Config::set('filesystems.disks.tenant.root', $rootPath);
        Config::set('filesystems.disks.tenant.url', $url);
    }

    private function resetFilesystemDisks(): void
    {
        $defaultRoot = storage_path('app/public');
        $defaultUrl = config('app.url') . '/storage';

        Config::set('filesystems.disks.public.root', $defaultRoot);
        Config::set('filesystems.disks.public.url', $defaultUrl);

        Config::set('filesystems.disks.tenant.root', $defaultRoot);
        Config::set('filesystems.disks.tenant.url', $defaultUrl);
    }
}
