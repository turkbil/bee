<?php
if (!function_exists('cdn')) {
    /**
     * Central domain üzerinden asset URL'i oluşturur
     * 
     * @param string $path
     * @return string
     */
    function cdn($path)
    {
        // URL'in başındaki ve sonundaki slash'leri düzelt
        $path = ltrim($path, '/');
        
        // APP_URL'i kullanarak central domain üzerinden URL oluştur
        return rtrim(env('APP_URL'), '/') . '/' . $path;
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
        return \App\Helpers\TenantConnection::getCurrentTenantId();
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
        return \App\Helpers\TenantConnection::isTenant();
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
        return \App\Helpers\TenantConnection::isCentral();
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