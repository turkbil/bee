<?php

use Modules\ModuleManagement\App\Services\TenantModuleSettingsService;

if (!function_exists('href')) {
    /**
     * Modül için özelleştirilmiş route slug'ını döndürür
     * 
     * @param string $moduleName
     * @param string $routeType (index_slug, show_slug, category_slug, tag_slug)
     * @param array $parameters
     * @return string
     */
    function href(string $moduleName, string $routeType = 'index_slug', array $parameters = []): string
    {
        $service = app(TenantModuleSettingsService::class);
        $slug = $service->getRouteSlug($moduleName, $routeType);
        
        $url = '/' . $slug;
        
        // Parametreleri URL'ye ekle
        if (!empty($parameters)) {
            foreach ($parameters as $param) {
                $url .= '/' . $param;
            }
        }
        
        return $url;
    }
}

if (!function_exists('module_url')) {
    /**
     * Modül için tam URL oluşturur
     * 
     * @param string $moduleName
     * @param string $routeType
     * @param array $parameters
     * @return string
     */
    function module_url(string $moduleName, string $routeType = 'index_slug', array $parameters = []): string
    {
        $path = href($moduleName, $routeType, $parameters);
        return url($path);
    }
}

if (!function_exists('module_setting')) {
    /**
     * Modül ayarını getirir
     * 
     * @param string $moduleName
     * @param string $settingKey
     * @param mixed $default
     * @return mixed
     */
    function module_setting(string $moduleName, string $settingKey, $default = null)
    {
        $service = app(TenantModuleSettingsService::class);
        $config = $service->getModuleConfig($moduleName);
        
        $keys = explode('.', $settingKey);
        $value = $config;
        
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
}

if (!function_exists('is_content_module')) {
    /**
     * Modülün content modülü olup olmadığını kontrol eder
     * 
     * @param string $moduleName
     * @return bool
     */
    function is_content_module(string $moduleName): bool
    {
        $service = app(TenantModuleSettingsService::class);
        return $service->isContentModule($moduleName);
    }
}