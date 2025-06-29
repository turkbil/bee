<?php

namespace Modules\LanguageManagement\app\Services;

use Modules\LanguageManagement\app\Models\TenantLanguage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class UrlPrefixService
{
    protected static $defaultLanguage = null;
    protected static $urlPrefixMode = null;
    protected static $availableLocales = null;
    protected static $defaultLanguageObject = null;
    
    /**
     * Get default language object (tek sorgu - optimize edilmiş)
     */
    protected static function getDefaultLanguageObject()
    {
        if (self::$defaultLanguageObject === null) {
            self::$defaultLanguageObject = Cache::remember('site_default_language_object', 3600, function () {
                try {
                    $defaultLang = TenantLanguage::where('is_default', true)->first();
                    return $defaultLang ? [
                        'code' => $defaultLang->code,
                        'url_prefix_mode' => $defaultLang->url_prefix_mode ?? 'except_default'
                    ] : [
                        'code' => 'tr',
                        'url_prefix_mode' => 'except_default'
                    ];
                } catch (\Exception $e) {
                    return [
                        'code' => 'tr',
                        'url_prefix_mode' => 'except_default'
                    ];
                }
            });
        }
        
        return self::$defaultLanguageObject;
    }
    
    /**
     * Get default language code
     */
    public static function getDefaultLanguage(): string
    {
        if (self::$defaultLanguage === null) {
            $defaultObj = self::getDefaultLanguageObject();
            self::$defaultLanguage = $defaultObj['code'];
        }
        
        return self::$defaultLanguage;
    }
    
    /**
     * Get URL prefix mode
     */
    public static function getUrlPrefixMode(): string
    {
        if (self::$urlPrefixMode === null) {
            $defaultObj = self::getDefaultLanguageObject();
            self::$urlPrefixMode = $defaultObj['url_prefix_mode'];
        }
        
        return self::$urlPrefixMode;
    }
    
    /**
     * Get available locales
     */
    public static function getAvailableLocales(): array
    {
        if (self::$availableLocales === null) {
            self::$availableLocales = Cache::remember('site_available_locales', 3600, function () {
                try {
                    return TenantLanguage::where('is_active', true)
                        ->orderBy('sort_order')
                        ->pluck('code')
                        ->toArray();
                } catch (\Exception $e) {
                    return ['tr'];
                }
            });
        }
        
        return self::$availableLocales;
    }
    
    /**
     * Check if locale needs prefix
     */
    public static function needsPrefix(string $locale): bool
    {
        $mode = self::getUrlPrefixMode();
        $defaultLang = self::getDefaultLanguage();
        
        switch ($mode) {
            case 'none':
                return false;
                
            case 'except_default':
                return $locale !== $defaultLang;
                
            case 'all':
                return true;
                
            default:
                return $locale !== $defaultLang;
        }
    }
    
    /**
     * URL'den dil prefix'ini ayıkla ve temiz path döndür
     */
    public static function parseUrl($request)
    {
        $path = $request->path();
        $tenant = tenant();
        
        // URL pattern: /tr/sayfa/hakkimizda veya /en/portfolio/website
        if (preg_match('/^([a-z]{2})\/(.*)$/', $path, $matches)) {
            $prefix = $matches[1];
            $cleanPath = $matches[2];
            
            // Bu prefix bu tenant'ta geçerli mi?
            if ($tenant) {
                $language = $tenant->siteLanguages()
                    ->where('prefix', $prefix)
                    ->where('is_active', 1)
                    ->first();
                
                if ($language) {
                    return [
                        'language' => $language,
                        'prefix' => $prefix,
                        'clean_path' => $cleanPath,
                        'has_prefix' => true,
                        'is_default' => $language->is_default ?? false
                    ];
                }
            } else {
                // Tenant yoksa genel available locales'dan kontrol et
                if (in_array($prefix, self::getAvailableLocales())) {
                    return [
                        'language' => (object) ['prefix' => $prefix, 'is_default' => false],
                        'prefix' => $prefix,
                        'clean_path' => $cleanPath,
                        'has_prefix' => true,
                        'is_default' => $prefix === self::getDefaultLanguage()
                    ];
                }
            }
        }
        
        // Prefix yok - locale öncelik sırası: User Preference → Session → Default
        $domain = request()->getHost();
        $sessionKey = 'site_locale_' . str_replace('.', '_', $domain);
        
        // Hızlı user locale kontrolü
        $userLocale = null;
        if (auth()->check()) {
            $user = auth()->user();
            $isAdminContext = str_contains(request()->url(), '/admin/');
            $userLocale = $isAdminContext ? $user->admin_locale : $user->tenant_locale;
        }
        
        // Öncelik sırası: User preference → Session → Fallback
        $sessionLocale = $userLocale ?: (session($sessionKey) ?: session('site_locale'));
        $tenant = tenant();
        $isCentralTenant = $tenant ? $tenant->central : false;
        
        // Minimal processing - debug bilgileri kaldırıldı
        
        // Session'da locale varsa onu kullan
        if ($sessionLocale) {
            $sessionLanguage = null;
            
            if ($tenant) {
                // Tenant var - central mi normal mi kontrol et (Tenant model'deki siteLanguages() metodu zaten bunu hallediyor)
                // \Log::info('🔍 UrlPrefixService TENANT MODE', [
                //     'tenant_id' => $tenant->id,
                //     'is_central' => $tenant->central,
                //     'session_locale' => $sessionLocale
                // ]);
                
                $sessionLanguage = \Modules\LanguageManagement\app\Models\TenantLanguage::where('code', $sessionLocale)
                    ->where('is_active', 1)
                    ->first();
            } else {
                // Tenant yoksa fallback (teorik olarak olmamalı)
                // \Log::info('🔍 UrlPrefixService NO TENANT FALLBACK', [
                //     'session_locale' => $sessionLocale,
                //     'domain' => request()->getHost()
                // ]);
                
                $sessionLanguage = \Modules\LanguageManagement\app\Models\TenantLanguage::on('mysql')
                    ->where('code', $sessionLocale)
                    ->where('is_active', 1)
                    ->first();
            }
            
            // \Log::info('🔍 UrlPrefixService session language found', [
            //     'session_locale' => $sessionLocale,
            //     'session_language_found' => $sessionLanguage ? 'YES' : 'NO',
            //     'session_language_code' => $sessionLanguage->code ?? 'N/A'
            // ]);
            
            if ($sessionLanguage) {
                return [
                    'language' => $sessionLanguage,
                    'prefix' => null,
                    'clean_path' => $path,
                    'has_prefix' => false,
                    'is_default' => $sessionLanguage->is_default ?? false
                        ];
            }
        }
        
        // Session yoksa veya geçersizse varsayılan dil - CACHE'DEN AL
        $defaultLanguageObj = self::getDefaultLanguageObject();
        $defaultLanguage = (object) [
            'code' => $defaultLanguageObj['code'],
            'is_default' => true,
            'url_prefix_mode' => $defaultLanguageObj['url_prefix_mode']
        ];
            
        return [
            'language' => $defaultLanguage,
            'prefix' => null,
            'clean_path' => $path,
            'has_prefix' => false,
            'is_default' => true
        ];
    }
    
    /**
     * Get locale from URL (backward compatibility)
     */
    public static function getLocaleFromUrl(): ?string
    {
        $segment = request()->segment(1);
        
        if ($segment && in_array($segment, self::getAvailableLocales())) {
            return $segment;
        }
        
        return null;
    }
    
    /**
     * URL oluştur (dil prefix'i ile)
     */
    public static function generateUrl($path, $locale = null)
    {
        $tenant = tenant();
        $locale = $locale ?? app()->getLocale();
        
        // Varsayılan dil mi?
        $isDefault = false;
        if ($tenant) {
            $isDefault = $tenant->siteLanguages()
                ->where('prefix', $locale)
                ->where('is_default', 1)
                ->exists();
        } else {
            $isDefault = $locale === self::getDefaultLanguage();
        }
            
        if ($isDefault) {
            // Varsayılan dil = prefix yok
            return url(ltrim($path, '/'));
        }
        
        // Varsayılan değil = prefix ekle
        return url("/{$locale}/" . ltrim($path, '/'));
    }
    
    /**
     * Mevcut URL'i başka dile çevir
     */
    public static function switchLanguage($newLocale)
    {
        $request = request();
        $parsed = self::parseUrl($request);
        
        // Aynı path'i yeni dil ile oluştur
        return self::generateUrl($parsed['clean_path'], $newLocale);
    }
    
    /**
     * Generate URL with proper locale prefix (backward compatibility)
     */
    public static function route(string $name, array $parameters = [], string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        
        // Check if we need prefix for this locale
        if (self::needsPrefix($locale)) {
            $parameters = array_merge(['locale' => $locale], $parameters);
        }
        
        return route($name, $parameters);
    }
    
    /**
     * Get current URL for different locale
     */
    public static function getCurrentUrlForLocale(string $locale): string
    {
        $currentRoute = Route::current();
        
        if (!$currentRoute) {
            return url('/');
        }
        
        $routeName = $currentRoute->getName();
        $parameters = $currentRoute->parameters();
        
        // Remove current locale from parameters if exists
        unset($parameters['locale']);
        
        // Add new locale if needed
        if (self::needsPrefix($locale)) {
            $parameters = array_merge(['locale' => $locale], $parameters);
        }
        
        try {
            return $routeName ? route($routeName, $parameters) : url('/');
        } catch (\Exception $e) {
            return url('/');
        }
    }
    
    /**
     * Clear all caches
     */
    public static function clearCache(): void
    {
        Cache::forget('site_default_language');
        Cache::forget('site_url_prefix_mode');
        Cache::forget('site_available_locales');
        
        self::$defaultLanguage = null;
        self::$urlPrefixMode = null;
        self::$availableLocales = null;
    }
    
    /**
     * Register locale routes
     */
    public static function registerLocaleRoutes($callback): void
    {
        $availableLocales = self::getAvailableLocales();
        
        // Routes without prefix (for default language or 'none' mode)
        Route::middleware('web')->group($callback);
        
        // Routes with prefix (for non-default languages)
        if (self::getUrlPrefixMode() !== 'none') {
            foreach ($availableLocales as $locale) {
                if (self::needsPrefix($locale)) {
                    Route::middleware('web')
                        ->prefix($locale)
                        ->group($callback);
                }
            }
        }
    }
}