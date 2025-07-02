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
        
        
        // URL prefix desteği kontrol et (mevcut sistem korunuyor)
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            try {
                $urlData = UrlPrefixService::parseUrl($request);
            } catch (\Exception $e) {
                \Log::error('🔍 UrlPrefixService hatası', ['error' => $e->getMessage()]);
                $urlData = ['language' => null];
            }
            
            if ($urlData['language']) {
                $locale = $this->extractLocaleFromUrlData($urlData);
                
                if ($locale && $this->isValidTenantLocale($locale)) {
                    // URL'den gelen dil aktif seçim olarak işle
                    $this->setActiveTenantLocale($locale);
                    
                    // Request'e temiz path'i yeniden ata
                    if ($urlData['has_prefix']) {
                        $request->merge(['clean_path' => $urlData['clean_path']]);
                    }
                    
                    return $next($request);
                }
            }
        }
        
        // Route parametresi kontrolü
        $langFromRoute = $request->route('locale') ?? $request->route('lang');
        if ($langFromRoute && $this->isValidTenantLocale($langFromRoute)) {
            $this->setActiveTenantLocale($langFromRoute);
            return $next($request);
        }
        
        // Normal durum: 3 aşamalı sistem devreye girer
        $locale = $this->resolveTenantLocale();
        
        // Laravel app locale'i ayarla (admin locale ile karışmasın!)
        app()->setLocale($locale);
        
        // Session'da da sakla (consistency için)
        if (!session()->has('tenant_locale') || session('tenant_locale') !== $locale) {
            session(['tenant_locale' => $locale]);
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
}