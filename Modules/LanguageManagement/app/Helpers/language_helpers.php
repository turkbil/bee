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
            return session('admin_locale');
        }
        
        // 2. STORED PREFERENCE - User DB'den
        if (auth()->check() && auth()->user()->admin_locale && is_valid_admin_locale(auth()->user()->admin_locale)) {
            // Session'a da kaydet (1. aşama için)
            session(['admin_locale' => auth()->user()->admin_locale]);
            return auth()->user()->admin_locale;
        }
        
        // 2b. Guest için cookie
        if (!auth()->check()) {
            $cookieLocale = Cookie::get('admin_locale_preference');
            if ($cookieLocale && is_valid_admin_locale($cookieLocale)) {
                session(['admin_locale' => $cookieLocale]);
                return $cookieLocale;
            }
        }
        
        // 3. SMART DEFAULT
        $default = config('app.admin_default_locale', 'tr');
        session(['admin_locale' => $default]);
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
        
        // PERFORMANCE: Extract codes from cached admin languages to prevent duplicate queries
        static $cachedValidLocales = null;
        if ($cachedValidLocales === null) {
            try {
                $adminLanguages = available_admin_languages();
                $cachedValidLocales = array_column($adminLanguages, 'code');
            } catch (\Exception $e) {
                $cachedValidLocales = ['tr', 'en'];
            }
        }
        
        return in_array($locale, $cachedValidLocales);
    }
}

if (!function_exists('is_valid_tenant_locale')) {
    /**
     * Tenant için geçerli dil kontrolü (unified tenant system)
     */
    function is_valid_tenant_locale(string $locale): bool
    {
        // LocaleValidationService varsa onu kullan
        if (class_exists('\App\Services\LocaleValidationService')) {
            try {
                return app(\App\Services\LocaleValidationService::class)->isValidTenantLocale($locale);
            } catch (\Exception $e) {
                // Fallback to old system
            }
        }
        
        // Eski sistem fallback
        if (empty($locale) || strlen($locale) > 5) {
            return false;
        }
        
        // PERFORMANCE: Extract codes from cached tenant languages to prevent duplicate queries
        static $cachedValidTenantLocales = null;
        if ($cachedValidTenantLocales === null) {
            try {
                $tenantLanguages = available_tenant_languages();
                $cachedValidTenantLocales = array_column($tenantLanguages, 'code');
            } catch (\Exception $e) {
                $cachedValidTenantLocales = ['tr', 'en'];
            }
        }
        
        return in_array($locale, $cachedValidTenantLocales);
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
        // PERFORMANCE: Use same cache key as AdminLanguageSwitcher to prevent duplicates
        $cached = \Cache::remember('admin_languages_switcher', 600, function() {
            try {
                return \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            } catch (\Exception $e) {
                return collect([
                    ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe'],
                    ['code' => 'en', 'name' => 'English', 'native_name' => 'English']
                ]);
            }
        });
        
        // Convert to array if it's a Collection
        return is_array($cached) ? $cached : $cached->toArray();
    }
}

if (!function_exists('get_tenant_languages')) {
    /**
     * Tenant dillerinin kod listesini al
     */
    function get_tenant_languages(): array
    {
        $languages = available_tenant_languages();
        return array_column($languages, 'code');
    }
}

if (!function_exists('available_tenant_languages')) {
    /**
     * Kullanılabilir tenant dillerini al (unified tenant system)
     */
    function available_tenant_languages(): array
    {
        // PERFORMANCE: Use same cache key as AdminLanguageSwitcher to prevent duplicates
        $cached = \Cache::remember('tenant_languages_switcher', 600, function() {
            try {
                // Unified tenant DB kontrolü - tüm tenantlar aynı şekilde
                return \DB::table('tenant_languages')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            } catch (\Exception $e) {
                return collect([
                    ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe'],
                    ['code' => 'en', 'name' => 'English', 'native_name' => 'English']
                ]);
            }
        });
        
        // Convert to array if it's a Collection
        return is_array($cached) ? $cached : $cached->toArray();
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
            // UnifiedUrlService kullan - modül slug değişikliklerini de handle eder
            $unifiedUrlService = app(\App\Services\UnifiedUrlService::class);
            
            $request = request();
            $currentLocale = app()->getLocale();
            $currentUrl = $request->url();
            $path = $request->path();
            
            // Ana sayfa kontrolü
            if ($path === '/' || $path === '') {
                return needs_locale_prefix($locale) ? url('/' . $locale) : url('/');
            }
            
            // URL'i çözümle
            $routeInfo = $unifiedUrlService->resolveUrl($currentUrl, $currentLocale);
            
            if ($routeInfo) {
                // Modül içeriği ise
                if (isset($routeInfo['module']) && isset($routeInfo['model'])) {
                    $model = $routeInfo['model'];
                    return $unifiedUrlService->buildUrlForModel($model, $locale);
                }
                
                // Modül action (category, tag) ise
                if (isset($routeInfo['type']) && $routeInfo['type'] === 'module_action') {
                    $params = [];
                    if (isset($routeInfo['model'])) {
                        $params[] = $routeInfo['model'];
                    }
                    return $unifiedUrlService->buildUrlForModule(
                        $routeInfo['module'],
                        $routeInfo['action'],
                        $params,
                        $locale
                    );
                }
                
                // Modül index/list sayfası ise
                if (isset($routeInfo['module']) && isset($routeInfo['action'])) {
                    return $unifiedUrlService->buildUrlForModule(
                        $routeInfo['module'],
                        $routeInfo['action'] ?? 'index',
                        $routeInfo['params'] ?? [],
                        $locale
                    );
                }
            }
            
            // Fallback: Basit path değişimi
            // Mevcut URL'den locale prefix'ini temizle
            $pathParts = explode('/', trim($path, '/'));
            $firstPart = $pathParts[0] ?? '';
            
            // İlk kısım bir dil kodu mu kontrol et
            $validLocales = get_tenant_languages();
            if (in_array($firstPart, $validLocales)) {
                array_shift($pathParts);
            }
            
            $cleanPath = implode('/', $pathParts);
            
            // Yeni locale için URL oluştur
            if (needs_locale_prefix($locale)) {
                return url('/' . $locale . '/' . $cleanPath);
            }
            
            return url('/' . $cleanPath);
            
        } catch (\Exception $e) {
            \Log::error('current_url_for_locale failed', [
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to language switch route
            return url('/language/' . $locale);
        }
    }
}