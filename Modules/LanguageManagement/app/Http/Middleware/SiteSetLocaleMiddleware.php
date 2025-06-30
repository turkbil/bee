<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LanguageManagement\app\Services\LanguageService;
use Modules\LanguageManagement\app\Services\UrlPrefixService;
use Symfony\Component\HttpFoundation\Response;

class SiteSetLocaleMiddleware
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Handle an incoming request for SITE context only
     * Uses tenant_languages table and site_locale session
     */
    public function handle(Request $request, Closure $next): Response
    {

        // URL prefix desteği - sadece site context için
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            $urlData = UrlPrefixService::parseUrl($request);
            
            if ($urlData['language']) {
                // UrlPrefixService'ten gelen language object'inden locale'i çıkar
                $locale = null;
                if (is_object($urlData['language'])) {
                    $locale = $urlData['language']->code ?? $urlData['language']->prefix ?? null;
                } else {
                    $locale = $urlData['language'];
                }
                
                if ($locale) {
                    // 🎯 CENTRAL TENANT KONTROLÜ - UrlPrefixService locale'i override edebilir
                    if (!app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                        // Central tenant'ın varsayılan dilini kontrol et
                        $centralTenant = \App\Helpers\TenantHelpers::central(function() {
                            return \App\Models\Tenant::where('central', true)->first();
                        });
                        
                        if ($centralTenant && $centralTenant->tenant_default_locale && 
                            $centralTenant->tenant_default_locale !== $locale) {
                            $locale = $centralTenant->tenant_default_locale;
                            
                        }
                    }
                    
                    // Laravel locale'i ayarla
                    app()->setLocale($locale);
                    
                    // Session'a kaydet
                    session(['site_locale' => $locale]);
                
                    // Request'e temiz path'i yeniden ata
                    if ($urlData['has_prefix']) {
                        $request->merge(['clean_path' => $urlData['clean_path']]);
                    }
                    
                    return $next($request);
                }
            }
        }
        
        // Route parametresinden dil prefix'ini kontrol et
        $langFromRoute = $request->route('locale') ?? $request->route('lang');
        
        if ($langFromRoute) {
            // Route'dan gelen dil geçerli mi kontrol et
            if ($this->languageService->isValidLanguageForContext($langFromRoute, 'site')) {
                $this->languageService->setLocale($langFromRoute, 'site');
                
                // Eğer kullanıcı giriş yapmışsa tercihini kaydet
                if (auth()->check()) {
                    $this->languageService->setUserLanguagePreference($langFromRoute, 'site');
                }
                
                
                return $next($request);
            }
        }
        
        // URL'den dil parametresi var mı kontrol et (query parameter)
        $languageFromUrl = $request->route('language') ?? $request->get('lang');
        
        
        if ($languageFromUrl) {
            // URL'den gelen dil geçerli mi kontrol et
            if ($this->languageService->isValidLanguageForContext($languageFromUrl, 'site')) {
                $this->languageService->setLocale($languageFromUrl, 'site');
                
                // Eğer kullanıcı giriş yapmışsa tercihini kaydet
                if (auth()->check()) {
                    $this->languageService->setUserLanguagePreference($languageFromUrl, 'site');
                }
                
            }
        } else {
            // 🎯 CENTRAL TENANT İÇİN ÖZEL KONTROL - Site context
            // Central tenant kontrolü - Tenancy başlatılmamışsa central'dayız  
            if (!app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                // Central tenant bilgisini al
                $centralTenant = \App\Helpers\TenantHelpers::central(function() {
                    return \App\Models\Tenant::where('central', true)->first();
                });
                
                if ($centralTenant && $centralTenant->tenant_default_locale) {
                    // Session'daki dil central tenant'ın varsayılanından farklıysa güncelle
                    $sessionLocale = session('site_locale');
                    if ($sessionLocale !== $centralTenant->tenant_default_locale) {
                        session(['site_locale' => $centralTenant->tenant_default_locale]);
                        app()->setLocale($centralTenant->tenant_default_locale);
                        
                        
                        return $next($request);
                    }
                }
            }
            
            // URL'de dil yok, site session/user tercihi/varsayılan sırasıyla kontrol et
            $currentLanguage = $this->languageService->getCurrentLocale('site');
            
            // Sadece mevcut locale farklıysa güncelle
            if (app()->getLocale() !== $currentLanguage) {
                $this->languageService->setLocale($currentLanguage, 'site');
            }
        }


        return $next($request);
    }
}