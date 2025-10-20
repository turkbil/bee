<?php

namespace App\Services;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        // Tenant ID'sini al
        $tenantId = $this->getTenantId();

        // getPath() sadece media_id/filename döndürüyor
        $mediaPath = $this->getPath();

        // Tenant-aware base URL
        $tenantBase = $this->getTenantAwareUrl();
        $tenantBase = rtrim($tenantBase, '/');

        // URL: https://domain.com/storage/tenant{id}/{media_id}/{filename}
        // Symlink: public/storage/tenant{id} → storage/tenant{id}/app/public
        return $tenantBase . '/storage/tenant' . $tenantId . '/' . $mediaPath;
    }

    /**
     * Tenant ID'sini al
     */
    protected function getTenantId(): int
    {
        try {
            // Tenant context'inden ID al
            if (app()->bound(\Stancl\Tenancy\Tenancy::class)) {
                $tenancy = app(\Stancl\Tenancy\Tenancy::class);

                if ($tenancy->initialized) {
                    return tenant('id');
                }
            }

            // Central context - default tenant 1
            return 1;
        } catch (\Exception $e) {
            // Fallback
            return 1;
        }
    }

    /**
     * Tenant-aware URL döndürür
     * Tenant context'inde tenant domain'ini, central'de app.url'i kullanır
     */
    protected function getTenantAwareUrl(): string
    {
        try {
            // Tenant context var mı kontrol et
            if (app()->bound('currentTenant') && $tenant = app('currentTenant')) {
                // Tenant'ın ilk domain'ini al
                if ($tenant->domains()->exists()) {
                    $domain = $tenant->domains()->first()->domain;
                    return 'https://' . $domain;
                }
            }

            // Alternatif: tenancy()->initialized kontrolü
            if (function_exists('tenant') && tenant()) {
                $tenant = tenant();
                if (isset($tenant->domains) && $tenant->domains->count() > 0) {
                    $domain = $tenant->domains->first()->domain;
                    return 'https://' . $domain;
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda config'den al
        }

        // Fallback: config'den al
        return config('app.url');
    }

    public function getPath(): string
    {
        // Medya ID'sini al
        $mediaDirectory = $this->media->id;

        // Dosya adını al
        $fileName = $this->media->file_name;

        // Path: {media_id}/{file_name}
        // NOT: TenantPathGenerator fiziksel path'e tenant{id}/ prefix'i ekliyor
        // Ama URL için sadece media_id/filename döndürüyoruz
        // StorageController tenant prefix'ini kendisi ekleyecek
        return $mediaDirectory . '/' . $fileName;
    }

    public function getPathFromUrl(string $url): string
    {
        // URL'den path'i çıkar ve tenant prefix'ini temizle
        // URL format: https://domain.com/storage/tenant{id}/{media_id}/{filename}
        // Return: {media_id}/{filename}

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        // /storage/ prefix'ini kaldır
        $path = preg_replace('#^/storage/#', '', $path);

        // tenant{id}/ prefix'ini kaldır
        $path = preg_replace('#^tenant\d+/#', '', $path);

        return $path;
    }
}