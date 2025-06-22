<?php

use Modules\LanguageManagement\app\Services\SystemLanguageService;
use Modules\LanguageManagement\app\Services\SiteLanguageService;

if (!function_exists('current_admin_language')) {
    /**
     * Mevcut admin dil kodunu al
     */
    function current_admin_language(): string
    {
        return app(SystemLanguageService::class)->getCurrentAdminLocale();
    }
}

if (!function_exists('current_site_language')) {
    /**
     * Mevcut site dil kodunu al
     */
    function current_site_language(): string
    {
        return app(SiteLanguageService::class)->getCurrentSiteLocale();
    }
}

if (!function_exists('available_admin_languages')) {
    /**
     * Kullanılabilir admin dillerini al
     */
    function available_admin_languages(): array
    {
        return app(SystemLanguageService::class)->getActiveSystemLanguages();
    }
}

if (!function_exists('available_site_languages')) {
    /**
     * Kullanılabilir site dillerini al
     */
    function available_site_languages(): array
    {
        return app(SiteLanguageService::class)->getActiveSiteLanguages();
    }
}

if (!function_exists('default_admin_language')) {
    /**
     * Varsayılan admin dilini al
     */
    function default_admin_language(): string
    {
        return app(SystemLanguageService::class)->getDefaultAdminLanguage();
    }
}

if (!function_exists('default_site_language')) {
    /**
     * Varsayılan site dilini al
     */
    function default_site_language(): string
    {
        return app(SiteLanguageService::class)->getDefaultSiteLanguage();
    }
}

if (!function_exists('system_language_name')) {
    /**
     * Sistem dil kodundan dil adını al
     */
    function system_language_name(string $code): string
    {
        $language = app(SystemLanguageService::class)->getSystemLanguageByCode($code);
        return $language ? $language->native_name : $code;
    }
}

if (!function_exists('site_language_name')) {
    /**
     * Site dil kodundan dil adını al
     */
    function site_language_name(string $code): string
    {
        $language = app(SiteLanguageService::class)->getSiteLanguageByCode($code);
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
            $language = app(SystemLanguageService::class)->getSystemLanguageByCode($code);
        } else {
            $language = app(SiteLanguageService::class)->getSiteLanguageByCode($code);
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
        return app(SystemLanguageService::class)->setUserAdminLanguagePreference($languageCode);
    }
}

if (!function_exists('set_user_site_language')) {
    /**
     * Kullanıcı site dil tercihini ayarla
     */
    function set_user_site_language(string $languageCode): bool
    {
        return app(SiteLanguageService::class)->setUserSiteLanguagePreference($languageCode);
    }
}