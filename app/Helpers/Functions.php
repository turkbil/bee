<?php

use App\Helpers\TenantHelpers;

// Global helper fonksiyonları

if (!function_exists('cdn')) {
    /**
     * Central domain üzerinden asset URL'i oluşturur
     * 
     * @param string $path
     * @return string
     */
    function cdn($path)
    {
        if (empty($path)) {
            return '';
        }
        
        // URL'de http:// veya https:// varsa, bunları temizle
        $path = preg_replace('#^https?://[^/]+/#', '', $path);
        
        // URL'in başındaki slash'leri temizle
        $path = ltrim($path, '/');
        
        // Tekrar eden domain kontrolü
        $centralDomains = config('tenancy.central_domains', []);
        foreach ($centralDomains as $domain) {
            // Domain adı path içinde varsa temizle
            if (strpos($path, $domain . '/') !== false) {
                $parts = explode($domain . '/', $path);
                $path = end($parts);
                break;
            }
        }
        
        // APP_URL'i kullanarak central domain üzerinden URL oluştur
        $base = rtrim(env('APP_URL'), '/');
        
        // Direk path'i ekle (storage/ öneki ile)
        return $base . '/' . $path;
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Aktif tenant ID'sini döndürür, tenant yoksa null döner
     * 
     * @return int|null
     */
    function tenant_id()
    {
        return TenantHelpers::getCurrentTenantId();
    }
}

if (!function_exists('is_tenant')) {
    /**
     * İşlemin tenant veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_tenant()
    {
        return TenantHelpers::isTenant();
    }
}

if (!function_exists('is_central')) {
    /**
     * İşlemin central veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_central()
    {
        return TenantHelpers::isCentral();
    }
}

if (!function_exists('tenant_name')) {
    /**
     * Aktif tenant'ın adını döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_name()
    {
        if (is_tenant()) {
            return tenant()->name ?? tenant()->id;
        }
        
        return null;
    }
}

if (!function_exists('tenant_domain')) {
    /**
     * Aktif tenant'ın domain adresini döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_domain()
    {
        if (is_tenant()) {
            return tenant()->domains->first()->domain ?? null;
        }
        
        return null;
    }
}

if (!function_exists('tenant_disk')) {
    /**
     * Aktif tenant için disk adını döndürür
     * Central için 'public', tenant için 'tenant' döndürür ve yapılandırır
     *
     * @return string
     */
    function tenant_disk()
    {
        return TenantHelpers::getTenantDiskConfig();
    }
}

if (!function_exists('tenant_storage_path')) {
    /**
     * Tenant için depolama yolunu oluşturur
     * Tenant1 için normal storage yolu, diğer tenant'lar için tenant{id} yolu kullanır
     *
     * @param int|null $tenantId
     * @param string $path
     * @return string
     */
    function tenant_storage_path($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Central için normal storage yolu
        if ($tenantId == 1) {
            return storage_path('app/public/' . ltrim($path, '/'));
        }
        
        // Tenant için tenant{id} yolu
        return storage_path('tenant' . $tenantId . '/app/public/' . ltrim($path, '/'));
    }
}

if (!function_exists('tenant_storage_url')) {
    /**
     * Tenant için depolama URL'ini oluşturur
     * tenant{id} formatında URL döndürür
     *
     * @param string $path
     * @param int|null $tenantId
     * @return string
     */
    function tenant_storage_url($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Tenant ID'li URL formatı
        return url('/storage/tenant' . $tenantId . '/' . ltrim($path, '/'));
    }
}

if (!function_exists('widget')) {
    /**
     * Widget render helper
     * 
     * @param string $position Widget pozisyonu
     * @param int|null $pageId Sayfa ID
     * @param string|null $module Modül adı
     * @return string
     */
    function widget($position, $pageId = null, $module = null)
    {
        $service = app('widget.service');
        return $service->renderWidgetsInPosition($position, $pageId, $module);
    }
}
