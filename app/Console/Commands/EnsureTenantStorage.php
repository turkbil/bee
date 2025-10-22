<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

/**
 * ⚠️ NOT: Bu command CONSOLE context'te çalışır
 * Tenant initialize EDİLMEZ, storage_path() suffix eklemez
 * Bu yüzden storage_path("tenant{$id}") kullanımı DOĞRUDUR
 */
class EnsureTenantStorage extends Command
{
    protected $signature = 'tenants:ensure-storage';
    protected $description = 'Tüm tenant\'lar için storage klasörleri ve symlink\'leri oluştur';

    public function handle()
    {
        $this->ensureCentralStorage();

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

    private function ensureCentralStorage(): void
    {
        $basePath = storage_path('app/public');
        $stat = @stat($basePath);
        $owner = $stat ? posix_getpwuid($stat['uid'])['name'] : null;
        $group = $stat ? posix_getgrgid($stat['gid'])['name'] : null;

        $directories = [
            $basePath . '/settings',
            $basePath . '/settings/files',
            $basePath . '/settings/images',
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);

                if ($owner && $group) {
                    @chown($dir, $owner);
                    @chgrp($dir, $group);
                    @chmod($dir, 0775);
                }
            }
        }
    }

    private function ensureStorageDirectories($tenantId): void
    {
        $storagePath = storage_path("tenant{$tenantId}");

        // Ana storage klasörünün owner:group bilgisini al
        $parentStorage = storage_path();
        $stat = @stat($parentStorage);
        $owner = $stat ? posix_getpwuid($stat['uid'])['name'] : null;
        $group = $stat ? posix_getgrgid($stat['gid'])['name'] : null;

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);

            // Ownership ve permission düzelt
            if ($owner && $group) {
                @chown($storagePath, $owner);
                @chgrp($storagePath, $group);
                @chmod($storagePath, 0775);
            }

            $this->line("  ✅ Storage klasörü oluşturuldu ({$owner}:{$group})");
        }

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

                $stat = @lstat(public_path('storage'));
                $owner = $stat ? posix_getpwuid($stat['uid'])['name'] : null;
                $group = $stat ? posix_getgrgid($stat['gid'])['name'] : null;

                if ($owner) {
                    @lchown($linkPath, $owner);
                }

                if ($group) {
                    @lchgrp($linkPath, $group);
                }

                $this->line("  ✅ Symlink oluşturuldu");
            }
        }
    }
}
