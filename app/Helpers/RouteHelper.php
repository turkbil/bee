<?php

use Modules\LanguageManagement\app\Services\UrlPrefixService;

// Route helper fonksiyonları modül helper'ında tanımlı - çakışma önlendi

if (!function_exists('needs_locale_prefix')) {
    /**
     * Check if locale needs URL prefix
     */
    function needs_locale_prefix(string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();
        
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            return UrlPrefixService::needsPrefix($locale);
        }
        
        return false;
    }
}

if (!function_exists('get_default_locale')) {
    /**
     * Get default locale
     */
    function get_default_locale(): string
    {
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            return UrlPrefixService::getDefaultLanguage();
        }
        
        return config('app.locale', 'tr');
    }
}

if (!function_exists('route_with_locale')) {
    /**
     * Dil prefix'i ile route oluştur
     */
    function route_with_locale($name, $parameters = [], $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $tenant = tenant();
        
        if (!$tenant) {
            return route($name, $parameters);
        }
        
        // Varsayılan dil mi kontrolü
        $isDefault = \Modules\LanguageManagement\app\Models\TenantLanguage::where('prefix', $locale)
            ->where('is_default', 1)
            ->exists();
        
        if ($isDefault) {
            // Prefix'siz route
            return route($name . '.default', $parameters);
        } else {
            // Prefix'li route
            $parameters = array_merge(['locale' => $locale], $parameters);
            return route($name . '.prefixed', $parameters);
        }
    }
}

if (!function_exists('current_url_with_locale')) {
    /**
     * Mevcut URL'i başka dile çevir
     */
    function current_url_with_locale($locale)
    {
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            return UrlPrefixService::switchLanguage($locale);
        }
        
        return url()->current();
    }
}

if (!function_exists('generate_localized_url')) {
    /**
     * Yeni gelişmiş URL oluşturucu
     */
    function generate_localized_url($path, $locale = null)
    {
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            return UrlPrefixService::generateUrl($path, $locale);
        }
        
        return url($path);
    }
}