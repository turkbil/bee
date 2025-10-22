<?php
namespace Modules\SettingManagement\App\Helpers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantStorageHelper
{
    /**
     * Dosyayı tenant için doğru bir şekilde yükler
     *
     * @param mixed $file Yüklenecek dosya
     * @param string $relativePath Relative path (örn: "settings/1" veya "widgets")
     * @param string $fileName Dosya adı
     * @param int|null $tenantId Tenant ID
     * @return string Public URL path
     *
     * @throws \Exception
     */
    public static function storeTenantFile($file, string $relativePath, string $fileName, ?int $tenantId = null): string
    {
        [$tenantId, $disk, $diskName] = self::resolveTenantFilesystem($tenantId);

        // ✅ FIX: Double prefix bug - tenant disk root zaten tenant{id}/app/public olduğu için
        // relativePath'e tekrar tenant prefix eklemeye gerek yok!
        $relativePath = trim($relativePath, '/');

        // ❌ REMOVED: Eski kod double prefix oluşturuyordu
        // if ($diskName !== 'tenant' && $tenantId) {
        //     $relativePath = trim("tenant{$tenantId}/{$relativePath}", '/');
        // }

        $fullPath = trim(($relativePath !== '' ? $relativePath . '/' : '') . $fileName, '/');

        try {
            if ($relativePath !== '') {
                self::ensureDirectoryExists($disk, $relativePath);
            }

            $fileContent = file_get_contents($file->getRealPath());
            if (! $disk->put($fullPath, $fileContent)) {
                throw new \RuntimeException("Dosya yüklenemedi: {$fullPath}");
            }

            if ($relativePath !== '') {
                self::applyPermissions($disk, $relativePath, 0775);
            }
            self::applyPermissions($disk, $fullPath, 0664);

            // ✅ Return path tenant prefix'li olmalı (URL için)
            // Çünkü symlink: public/storage/tenant{id} → storage/tenant{id}/app/public
            if ($diskName === 'tenant' && $tenantId) {
                return "storage/tenant{$tenantId}/{$fullPath}";
            }

            return "storage/{$fullPath}";
        } catch (\Throwable $e) {
            $errorMsg = "Dosya yükleme hatası (Tenant {$tenantId}): {$e->getMessage()}";
            $errorMsg .= " | Path: {$relativePath}/{$fileName}";

            try {
                $errorMsg .= " | Disk root: " . $disk->path('/');
            } catch (\Throwable $ex) {
                $errorMsg .= " | Disk path alınamadı";
            }

            throw new \Exception($errorMsg, previous: $e);
        }
    }

    /**
     * Dosyayı siler
     */
    public static function deleteFile(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        try {
            [$tenantId, $relativePath] = self::extractTenantPath($path);

            if ($relativePath === null) {
                return false;
            }

            [$resolvedTenantId, $disk, $diskName] = self::resolveTenantFilesystem($tenantId);

            // ✅ FIX: Direkt relativePath ile sil, tenant disk root zaten doğru
            // Double prefix oluşturmaya gerek yok
            if (self::deleteFromDisk($disk, $relativePath)) {
                return true;
            }

            // ❌ REMOVED: Gereksiz prefix ekleme ve fallback
            // Çünkü tenant disk doğru yapılandırıldıysa zaten başarılı olur
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Silme işlemini verilen disk üzerinde gerçekleştirir
     */
    protected static function deleteFromDisk(FilesystemAdapter $disk, string $path): bool
    {
        $path = ltrim($path, '/');

        if ($path === '') {
            return false;
        }

        if ($disk->exists($path)) {
            return (bool) $disk->delete($path);
        }

        return false;
    }

    /**
     * Verilen dizinin varlığını garanti eder.
     */
    protected static function ensureDirectoryExists(FilesystemAdapter $disk, string $relativePath): void
    {
        $relativePath = trim($relativePath, '/');

        if ($relativePath === '') {
            return;
        }

        $directoryExists = method_exists($disk, 'directoryExists')
            ? $disk->directoryExists($relativePath)
            : $disk->exists($relativePath);

        if (! $directoryExists) {
            $disk->makeDirectory($relativePath);
        }
    }

    /**
     * Dosya veya dizin izinlerini ayarlar.
     */
    protected static function applyPermissions(FilesystemAdapter $disk, string $path, int $permission): void
    {
        $path = trim($path, '/');

        if ($path === '') {
            return;
        }

        try {
            $absolutePath = $disk->path($path);
        } catch (\Throwable $e) {
            return;
        }

        if ($absolutePath && file_exists($absolutePath)) {
            // 🔒 KRİTİK: Permission ve owner ayarla (psacln sorununu önler)
            @chmod($absolutePath, $permission);

            // 🔧 Owner'ı tuufi.com_:psaserv olarak ayarla (403 hatası önlenir)
            // posix_getpwnam varsa (Linux) kullan
            if (function_exists('posix_getpwnam') && function_exists('posix_getgrnam')) {
                $userInfo = @posix_getpwnam('tuufi.com_');
                $groupInfo = @posix_getgrnam('psaserv');

                if ($userInfo !== false && $groupInfo !== false) {
                    @chown($absolutePath, $userInfo['uid']);
                    @chgrp($absolutePath, $groupInfo['gid']);
                }
            }
        }
    }

    /**
     * Tenant ID ve relative path'i çıkartır
     *
     * @return array{0:int|null,1:string|null}
     */
    protected static function extractTenantPath(string $path): array
    {
        $path = trim($path);

        if ($path === '') {
            return [null, null];
        }

        if (preg_match('#^https?://#i', $path)) {
            $parsedPath = parse_url($path, PHP_URL_PATH);
            if (is_string($parsedPath)) {
                $path = ltrim($parsedPath, '/');
            }
        } else {
            $path = ltrim($path, '/');
        }

        if (preg_match('#^storage/tenant(\d+)/(.*)$#i', $path, $matches)) {
            return [(int) $matches[1], $matches[2]];
        }

        if (preg_match('#^tenant(\d+)/(.*)$#i', $path, $matches)) {
            return [(int) $matches[1], $matches[2]];
        }

        if (Str::startsWith($path, 'storage/')) {
            return [null, substr($path, strlen('storage/'))];
        }

        return [null, $path];
    }

    /**
     * İlgili tenant için filesystem adapter'ı döndürür.
     *
     * @return array{0:int,1:FilesystemAdapter,2:string}
     */
    protected static function resolveTenantFilesystem(?int $tenantId = null): array
    {
        $tenantId = $tenantId
            ?? (function_exists('tenant_id') ? tenant_id() : null)
            ?? 1;

        $tenantId = (int) max(1, $tenantId);

        self::configureTenantDisk($tenantId);

        try {
            $disk = Storage::disk('tenant');
            $diskName = 'tenant';
        } catch (\Throwable $e) {
            $disk = Storage::disk('public');
            $diskName = 'public';
        }

        return [$tenantId, $disk, $diskName];
    }

    /**
     * Tenant disk yapılandırmasını hazırlar.
     *
     * ⚠️ CRITICAL: Bu metod resolveTenantFilesystem() içinden çağrılıyor
     * resolveTenantFilesystem() tenant_id() helper kullanıyor
     * Yani tenant context ZATEN initialized!
     */
    protected static function configureTenantDisk(int $tenantId): void
    {
        // ⚠️ CRITICAL FIX: tenant_id() helper çalışıyorsa tenant initialized!
        // storage_path() otomatik tenant prefix ekliyor (suffix_storage_path=true)
        // Manuel "tenant{$tenantId}/" EKLEMEMELIYIZ!
        $root = storage_path("app/public");

        if (! is_dir($root)) {
            @mkdir($root, 0775, true);
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        config([
            'filesystems.disks.tenant' => [
                'driver' => 'local',
                'root' => $root,
                'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantId}" : null,
                'visibility' => 'public',
                'throw' => false,
            ],
        ]);
    }
}
