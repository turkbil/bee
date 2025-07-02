<?php

use Illuminate\Support\Facades\Cookie;

if (!function_exists('current_admin_language')) {
    /**
     * 3 Aşamalı Hibrit: Mevcut admin dil kodunu al
     * 1. Session → 2. User DB → 3. Default
     */
    function current_admin_language(): string
    {
        // 1. ACTIVE CHOICE - Session'dan
        if (session()->has('admin_locale') && is_valid_admin_locale(session('admin_locale'))) {
            \Log::info('🔍 current_admin_language - Session değeri kullanıldı', [
                'admin_locale' => session('admin_locale')
            ]);
            return session('admin_locale');
        }
        
        // 2. STORED PREFERENCE - User DB'den
        if (auth()->check() && auth()->user()->admin_locale && is_valid_admin_locale(auth()->user()->admin_locale)) {
            // Session'a da kaydet (1. aşama için)
            session(['admin_locale' => auth()->user()->admin_locale]);
            \Log::info('🔍 current_admin_language - User DB değeri kullanıldı', [
                'user_admin_locale' => auth()->user()->admin_locale,
                'session_updated' => true
            ]);
            return auth()->user()->admin_locale;
        }
        
        // 2b. Guest için cookie
        if (!auth()->check()) {
            $cookieLocale = Cookie::get('admin_locale_preference');
            if ($cookieLocale && is_valid_admin_locale($cookieLocale)) {
                session(['admin_locale' => $cookieLocale]);
                \Log::info('🔍 current_admin_language - Cookie değeri kullanıldı', [
                    'cookie_locale' => $cookieLocale
                ]);
                return $cookieLocale;
            }
        }
        
        // 3. SMART DEFAULT
        $default = config('app.admin_default_locale', 'tr');
        session(['admin_locale' => $default]);
        \Log::info('🔍 current_admin_language - Default değer kullanıldı', [
            'default' => $default,
            'auth_check' => auth()->check(),
            'user_admin_locale' => auth()->check() ? auth()->user()->admin_locale : 'NOT_AUTH'
        ]);
        return $default;
    }
}

if (!function_exists('current_tenant_language')) {
    /**
     * 3 Aşamalı Hibrit: Mevcut tenant dil kodunu al
     * 1. Session → 2. User DB/Cookie → 3. Tenant Default
     */
    function current_tenant_language(): string
    {
        // 1. ACTIVE CHOICE - Session'dan
        if (session()->has('tenant_locale') && is_valid_tenant_locale(session('tenant_locale'))) {
            return session('tenant_locale');
        }
        
        // 2. STORED PREFERENCE - User DB'den (login) veya Cookie'den (guest)
        if (auth()->check() && auth()->user()->tenant_locale && is_valid_tenant_locale(auth()->user()->tenant_locale)) {
            session(['tenant_locale' => auth()->user()->tenant_locale]);
            return auth()->user()->tenant_locale;
        }
        
        // 2b. Guest için cookie
        if (!auth()->check()) {
            $cookieLocale = Cookie::get('tenant_locale_preference');
            if ($cookieLocale && is_valid_tenant_locale($cookieLocale)) {
                session(['tenant_locale' => $cookieLocale]);
                return $cookieLocale;
            }
        }
        
        // 3. SMART DEFAULT - Tenant varsayılanı
        $default = get_tenant_default_locale();
        session(['tenant_locale' => $default]);
        return $default;
    }
}

// 3 Aşamalı Sistem için Yardımcı Fonksiyonlar

if (!function_exists('is_valid_admin_locale')) {
    /**
     * Admin için geçerli dil kontrolü
     */
    function is_valid_admin_locale(string $locale): bool
    {
        if (empty($locale) || strlen($locale) > 10) {
            return false;
        }
        
        try {
            $exists = \DB::table('admin_languages')
                ->where('code', $locale)
                ->where('is_active', true)
                ->exists();
            return $exists;
        } catch (\Exception $e) {
            return in_array($locale, ['tr', 'en']);
        }
    }
}

if (!function_exists('is_valid_tenant_locale')) {
    /**
     * Tenant için geçerli dil kontrolü (unified tenant system)
     */
    function is_valid_tenant_locale(string $locale): bool
    {
        if (empty($locale) || strlen($locale) > 5) {
            return false;
        }
        
        try {
            // Unified tenant DB kontrolü - tüm tenantlar aynı şekilde
            $exists = \DB::table('tenant_languages')
                ->where('code', $locale)
                ->where('is_active', true)
                ->exists();
            return $exists;
        } catch (\Exception $e) {
            return in_array($locale, ['tr', 'en']);
        }
    }
}

if (!function_exists('get_tenant_default_locale')) {
    /**
     * Tenant varsayılan dilini al (unified tenant system)
     */
    function get_tenant_default_locale(): string
    {
        try {
            // Her durum için tenant() helper'ını kullan
            $tenant = tenant();
            return $tenant ? ($tenant->tenant_default_locale ?? 'tr') : 'tr';
        } catch (\Exception $e) {
            return 'tr';
        }
    }
}

if (!function_exists('available_admin_languages')) {
    /**
     * Kullanılabilir admin dillerini al
     */
    function available_admin_languages(): array
    {
        try {
            return \DB::table('admin_languages')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [
                ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe'],
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English']
            ];
        }
    }
}

if (!function_exists('available_tenant_languages')) {
    /**
     * Kullanılabilir tenant dillerini al (unified tenant system)
     */
    function available_tenant_languages(): array
    {
        try {
            // Unified tenant DB kontrolü - tüm tenantlar aynı şekilde
            return \DB::table('tenant_languages')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [
                ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe'],
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English']
            ];
        }
    }
}

if (!function_exists('default_admin_language')) {
    /**
     * Varsayılan admin dilini al
     */
    function default_admin_language(): string
    {
        return config('app.admin_default_locale', 'tr');
    }
}

if (!function_exists('default_tenant_language')) {
    /**
     * Varsayılan tenant dilini al
     */
    function default_tenant_language(): string
    {
        return get_tenant_default_locale();
    }
}

if (!function_exists('admin_language_name')) {
    /**
     * Admin dil kodundan dil adını al
     */
    function admin_language_name(string $code): string
    {
        $language = app(AdminLanguageService::class)->getAdminLanguageByCode($code);
        return $language ? $language->native_name : $code;
    }
}

if (!function_exists('tenant_language_name')) {
    /**
     * Tenant dil kodundan dil adını al
     */
    function tenant_language_name(string $code): string
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
     * 3 Aşamalı Sistem: Kullanıcı admin dil tercihini ayarla
     */
    function set_user_admin_language(string $languageCode): bool
    {
        if (!is_valid_admin_locale($languageCode)) {
            return false;
        }
        
        try {
            // 1. Session'a kaydet (anında etkili)
            session(['admin_locale' => $languageCode]);
            
            // 2. User DB'ye kaydet (kalıcı tercih)
            if (auth()->check()) {
                auth()->user()->update(['admin_locale' => $languageCode]);
            }
            
            // 3. Cookie'ye kaydet (logout sonrası hatırlama)
            Cookie::queue('admin_locale_preference', $languageCode, 525600);
            
            // Laravel locale'i de güncelle
            app()->setLocale($languageCode);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('set_user_tenant_language')) {
    /**
     * 3 Aşamalı Sistem: Kullanıcı tenant dil tercihini ayarla
     */
    function set_user_tenant_language(string $languageCode): bool
    {
        if (!is_valid_tenant_locale($languageCode)) {
            return false;
        }
        
        try {
            // 1. Session'a kaydet (anında etkili)
            session(['tenant_locale' => $languageCode]);
            
            // 2. User DB'ye kaydet (kalıcı tercih)
            if (auth()->check()) {
                auth()->user()->update(['tenant_locale' => $languageCode]);
            }
            
            // 3. Cookie'ye kaydet (logout sonrası hatırlama)
            Cookie::queue('tenant_locale_preference', $languageCode, 525600);
            
            // Laravel locale'i de güncelle (sadece tenant context'te)
            if (!request()->is('admin*')) {
                app()->setLocale($languageCode);
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        
        $defaultLanguage = default_tenant_language();
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