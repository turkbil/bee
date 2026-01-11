<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Modules\LanguageManagement\app\Services\UrlPrefixService;
use Symfony\Component\HttpFoundation\Response;

class SiteSetLocaleMiddleware
{
    /**
     * 3 Aşamalı Hibrit Tenant Dil Sistemi (ORJİNAL HALİNE DÖNDÜRÜLDİ)
     * 1. ACTIVE CHOICE (Session - bu oturumda değiştirildi mi?)
     * 2. STORED PREFERENCE (User DB / Cookie - kalıcı tercih)
     * 3. SMART DEFAULT (Tenant varsayılan + fallback)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ⚠️ AUTH ROUTE BYPASS - Login/register/logout locale detection yapmasın
        $excludedPaths = [
            'login',
            'register',
            'logout',
            'password',
            'forgot-password',
            'reset-password',
            'verify-email',
            'email/verification-notification',
            'confirm-password'
        ];

        foreach ($excludedPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                // Auth route'ları için locale detection yok, sadece varsayılan dil
                $defaultLocale = $this->getTenantDefaultLocale();
                app()->setLocale($defaultLocale);
                session(['tenant_locale' => $defaultLocale]);

                \Log::debug('AUTH ROUTE BYPASS - Locale forced to default', [
                    'path' => $request->path(),
                    'locale' => $defaultLocale
                ]);

                return $next($request);
            }
        }

        // 1. URL path'ten locale tespiti - EN BASİT VE NET YAKLAŞIM
        $segments = $request->segments();
        $defaultLocale = $this->getTenantDefaultLocale();
        $detectedLocale = null;
        
        // URL analizi
        if (count($segments) > 0 && strlen($segments[0]) == 2) {
            // İlk segment 2 karakterli ise locale olabilir
            if ($this->isValidTenantLocale($segments[0])) {
                // Geçerli bir locale: /en/pages, /ar/announcements
                $detectedLocale = $segments[0];
            }
        }
        
        // Locale yoksa varsayılan dili kullan
        if (!$detectedLocale) {
            // Prefix yok: /, /pages, /announcements = varsayılan dil
            $detectedLocale = $defaultLocale;
        }
        
        // Önceki locale'i session'dan al (app locale değil!)
        $previousLocale = session('tenant_locale', $this->getTenantDefaultLocale());

        // 2. Locale'i set et
        app()->setLocale($detectedLocale);
        session(['tenant_locale' => $detectedLocale]);

        // Kullanıcı ve cookie güncelle (sadece gerçek değişimde)
        if ($previousLocale !== $detectedLocale) {
            if (auth()->check() && auth()->user()->tenant_locale !== $detectedLocale) {
                auth()->user()->update(['tenant_locale' => $detectedLocale]);
            }
            \Cookie::queue('tenant_locale_preference', $detectedLocale, 525600);

            // Dil değişmişse cache'leri temizle (THROTTLED)
            $this->clearLanguageRelatedCachesThrottled($previousLocale, $detectedLocale);
        }

        return $next($request);
    }

    /**
     * URL prefix data'sından locale çıkar
     */
    private function extractLocaleFromUrlData(array $urlData): ?string
    {
        if (!$urlData['language']) {
            return null;
        }
        
        if (is_object($urlData['language'])) {
            return $urlData['language']->code ?? $urlData['language']->prefix ?? null;
        }
        
        return $urlData['language'];
    }

    /**
     * Aktif tenant locale ayarla (URL'den geldiğinde)
     */
    private function setActiveTenantLocale(string $locale): void
    {
        app()->setLocale($locale);
        session(['tenant_locale' => $locale]);
        
        // Kullanıcı tercihi olarak da kaydet
        if (auth()->check()) {
            auth()->user()->update(['tenant_locale' => $locale]);
        }
        
        // Cookie'ye de kaydet
        Cookie::queue('tenant_locale_preference', $locale, 525600);
    }

    /**
     * 3 Aşamalı Tenant Dil Tespiti
     */
    private function resolveTenantLocale(): string
    {
        
        // 1. ACTIVE CHOICE - Session'da bu oturum için ayarlanmış dil var mı?
        if (session()->has('tenant_locale') && $this->isValidTenantLocale(session('tenant_locale'))) {
            return session('tenant_locale');
        }
        
        // 2. STORED PREFERENCE - Login kullanıcının kalıcı tercihi
        if (auth()->check() && auth()->user()->tenant_locale && $this->isValidTenantLocale(auth()->user()->tenant_locale)) {
            // Session'a da kaydet ki bir sonraki requestte 1. aşamadan gelsin
            session(['tenant_locale' => auth()->user()->tenant_locale]);
            
            // Cookie'ye de kaydet (logout sonrası hatırlama için)
            Cookie::queue('tenant_locale_preference', auth()->user()->tenant_locale, 525600);
            
            return auth()->user()->tenant_locale;
        }
        
        // 2b. STORED PREFERENCE - Guest için cookie tercihi
        if (!auth()->check()) {
            $cookieLocale = Cookie::get('tenant_locale_preference');
            if ($cookieLocale && $this->isValidTenantLocale($cookieLocale)) {
                session(['tenant_locale' => $cookieLocale]);
                return $cookieLocale;
            }
        }
        
        // 3. SMART DEFAULT - Tenant varsayılanı + fallback
        // NOT: TenancyProvider zaten session'a tenant_default_locale yazmış olabilir
        $defaultLocale = $this->getTenantDefaultLocale();
        
        if ($this->isValidTenantLocale($defaultLocale)) {
            session(['tenant_locale' => $defaultLocale]);
            Cookie::queue('tenant_locale_preference', $defaultLocale, 525600);
            return $defaultLocale;
        }
        
        // Final fallback
        session(['tenant_locale' => 'tr']);
        return 'tr';
    }

    /**
     * Tenant varsayılan dilini al (central tenant için özel kontrol)
     */
    private function getTenantDefaultLocale(): string
    {
        try {
            // Önce tenant() helper'ını dene
            $tenant = tenant();
            
            if ($tenant && $tenant->tenant_default_locale) {
                return $tenant->tenant_default_locale;
            }
            
            // Central tenant için dinamik kontrol (central = 1)
            $centralTenant = \App\Models\Tenant::where('central', 1)->first();
            
            if ($centralTenant && $centralTenant->tenant_default_locale) {
                return $centralTenant->tenant_default_locale;
            }
            
            return 'tr'; // Final fallback
        } catch (\Exception $e) {
            return 'tr';
        }
    }

    /**
     * Tenant için geçerli dil kontrolü (unified tenant system)
     */
    private function isValidTenantLocale(string $locale): bool
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
            // DB hatası durumunda basic locale kontrolü
            return in_array($locale, ['tr', 'en']);
        }
    }
    
    /**
     * Throttled cache clearing - sonsuz döngü önleme
     */
    private function clearLanguageRelatedCachesThrottled(string $previousLocale, string $newLocale): void
    {
        // Throttling key - aynı dil değişimi 5 saniye içinde sadece 1 kez
        $throttleKey = "language_cache_clear_{$previousLocale}_to_{$newLocale}";

        // Redis cache kullan (file cache yerine)
        $cache = \Cache::store('redis');

        if ($cache->has($throttleKey)) {
            \Log::debug('Language cache clear skipped (throttled)', [
                'from' => $previousLocale,
                'to' => $newLocale
            ]);
            return;
        }

        // 5 saniye throttle
        $cache->put($throttleKey, true, 5);
        
        $this->clearLanguageRelatedCaches();
    }

    /**
     * Dil değişiminde ilgili cache'leri temizle
     */
    private function clearLanguageRelatedCaches(): void
    {
        try {
            // AGRESIF MENU CACHE TEMİZLİĞİ
            if (function_exists('clearMenuCaches')) {
                // Tüm menu cache'lerini sil
                clearMenuCaches();
            }
            
            // Tüm diller için menü cache'lerini temizle
            $languages = \get_tenant_languages();
            foreach ($languages as $lang) {
                \Cache::forget("menu.default.{$lang}");
                \Cache::forget("menu.header.{$lang}");
                \Cache::forget("menu.footer.{$lang}");
                \Cache::forget("menu.items.{$lang}");
                \Cache::forget("menu.location.header.{$lang}");
                \Cache::forget("menu.location.footer.{$lang}");
                \Cache::forget("menu.location.sidebar.{$lang}");
                
                // Tüm menu ID'leri için cache temizle
                for ($i = 1; $i <= 100; $i++) {
                    \Cache::forget("menu.id.{$i}.{$lang}");
                }
            }
            
            // Module slug cache'lerini temizle
            if (class_exists('\App\Services\ModuleSlugService')) {
                \App\Services\ModuleSlugService::clearCache();
            }
            
            // Route cache'lerini temizle
            if (class_exists('\App\Services\DynamicRouteResolver')) {
                app(\App\Services\DynamicRouteResolver::class)->clearRouteCache();
            }

            // Widget cache'lerini temizle (pattern-based)
            try {
                $redis = \Cache::getRedis();
                $prefix = config('database.redis.options.prefix', '');
                $pattern = $prefix . ':widget_*';
                $keys = $redis->keys($pattern);

                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } catch (\Exception $e) {
                \Log::debug('Widget cache clear failed: ' . $e->getMessage());
            }

            // View cache'lerini temizle
            \Artisan::call('view:clear');

            \Log::debug('Language related caches cleared due to locale change');
            
        } catch (\Exception $e) {
            \Log::warning('Failed to clear language caches', [
                'error' => $e->getMessage()
            ]);
        }
    }
}