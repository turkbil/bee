<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // URL'den locale'i al (ilk segment)
        $segments = $request->segments();
        $locale = $segments[0] ?? null;
        
        // Geçerli locale'ler
        $validLocales = array_column(available_tenant_languages(), 'code');
        $defaultLocale = get_tenant_default_locale();
        
        // Eğer ilk segment geçerli bir locale ise
        if ($locale && in_array($locale, $validLocales) && $locale !== $defaultLocale) {
            // Session ve app locale'i güncelle
            app()->setLocale($locale);
            session(['tenant_locale' => $locale]);
            
            // Cookie'ye kaydet (365 gün)
            \Cookie::queue('tenant_locale_preference', $locale, 525600);
            
            // Cache temizle
            \App\Services\CacheManager::clearAllLanguageRelatedCaches();
        }
        
        return $next($request);
    }
}