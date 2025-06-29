<?php

use Modules\LanguageManagement\app\Services\AdminLanguageService;
use Modules\LanguageManagement\app\Services\TenantLanguageService;

if (!function_exists('current_admin_language')) {
    /**
     * Mevcut admin dil kodunu al
     */
    function current_admin_language(): string
    {
        return app(AdminLanguageService::class)->getCurrentAdminLocale();
    }
}

if (!function_exists('current_site_language')) {
    /**
     * Mevcut site dil kodunu al
     */
    function current_site_language(): string
    {
        return app(TenantLanguageService::class)->getCurrentTenantLocale();
    }
}

if (!function_exists('available_admin_languages')) {
    /**
     * Kullanılabilir admin dillerini al
     */
    function available_admin_languages(): array
    {
        return app(AdminLanguageService::class)->getActiveAdminLanguages();
    }
}

if (!function_exists('available_site_languages')) {
    /**
     * Kullanılabilir site dillerini al
     */
    function available_site_languages(): array
    {
        return app(TenantLanguageService::class)->getActiveTenantLanguages();
    }
}

if (!function_exists('default_admin_language')) {
    /**
     * Varsayılan admin dilini al
     */
    function default_admin_language(): string
    {
        return app(AdminLanguageService::class)->getDefaultAdminLanguage();
    }
}

if (!function_exists('default_site_language')) {
    /**
     * Varsayılan site dilini al
     */
    function default_site_language(): string
    {
        return app(TenantLanguageService::class)->getDefaultTenantLanguage();
    }
}

if (!function_exists('system_language_name')) {
    /**
     * Sistem dil kodundan dil adını al
     */
    function system_language_name(string $code): string
    {
        $language = app(AdminLanguageService::class)->getAdminLanguageByCode($code);
        return $language ? $language->native_name : $code;
    }
}

if (!function_exists('site_language_name')) {
    /**
     * Site dil kodundan dil adını al
     */
    function site_language_name(string $code): string
    {
        $language = app(TenantLanguageService::class)->getTenantLanguageByCode($code);
        return $language ? $language->native_name : $code;
    }
}

if (!function_exists('language_flag')) {
    /**
     * Dil kodundan bayrak emojisini al (sistem veya site)
     */
    function language_flag(string $code, string $context = 'admin'): string
    {
        if ($context === 'admin') {
            $language = app(AdminLanguageService::class)->getAdminLanguageByCode($code);
        } else {
            $language = app(TenantLanguageService::class)->getTenantLanguageByCode($code);
        }
        
        return $language ? ($language->flag_icon ?? '🌐') : '🌐';
    }
}

if (!function_exists('set_user_admin_language')) {
    /**
     * Kullanıcı admin dil tercihini ayarla
     */
    function set_user_admin_language(string $languageCode): bool
    {
        return app(AdminLanguageService::class)->setUserAdminLanguagePreference($languageCode);
    }
}

if (!function_exists('set_user_site_language')) {
    /**
     * Kullanıcı site dil tercihini ayarla
     */
    function set_user_site_language(string $languageCode): bool
    {
        return app(TenantLanguageService::class)->setUserTenantLanguagePreference($languageCode);
    }
}

if (!function_exists('needs_locale_prefix')) {
    /**
     * Bu dil için URL prefix gerekli mi?
     */
    function needs_locale_prefix(string $locale): bool
    {
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            return \Modules\LanguageManagement\app\Services\UrlPrefixService::needsPrefix($locale);
        }
        
        $defaultLanguage = default_site_language();
        return $locale !== $defaultLanguage;
    }
}

if (!function_exists('locale_route')) {
    /**
     * Locale-aware route oluştur
     */
    function locale_route(string $name, array $parameters = [], string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (needs_locale_prefix($locale)) {
            $parameters = array_merge(['lang' => $locale], $parameters);
            return route($name, $parameters);
        }
        
        return route($name, $parameters);
    }
}

if (!function_exists('current_url_for_locale')) {
    /**
     * Mevcut URL'i başka dil için oluştur
     */
    function current_url_for_locale(string $locale): string
    {
        try {
            $request = request();
            $currentLocale = app()->getLocale();
            $path = $request->path();
            
            // Ana sayfa kontrolü
            if ($path === '/') {
                return needs_locale_prefix($locale) ? url('/' . $locale) : url('/');
            }
            
            // Mevcut URL'den locale prefix'ini temizle
            if (needs_locale_prefix($currentLocale)) {
                $path = preg_replace('/^' . preg_quote($currentLocale, '/') . '\//', '', $path);
            }
            
            // Yeni locale için prefix ekle (gerekirse)
            if (needs_locale_prefix($locale)) {
                $path = $locale . '/' . $path;
            }
            
            return url('/' . ltrim($path, '/'));
            
        } catch (\Exception $e) {
            // Fallback to language switch route
            return url('/language/' . $locale);
        }
    }
}