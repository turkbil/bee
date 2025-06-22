<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dil seçim mantığı: 0. Session 1. User admin dili 2. Tenant admin dili 3. tr
        $locale = 'tr'; // Varsayılan
        
        // 0. Session'dan kontrol et (en hızlı)
        if (session()->has('locale')) {
            $locale = session('locale');
        }
        // 1. Kullanıcının seçtiği admin dili
        elseif (auth()->check() && auth()->user()->language) {
            $locale = auth()->user()->language;
        }
        // 2. Tenant'ın seçtiği admin dili (varsa)
        elseif (function_exists('tenant') && tenant() && isset(tenant()->admin_language)) {
            $locale = tenant()->admin_language;
        }
        
        // Desteklenen dil kontrolü
        if (!$this->isLanguageSupported($locale)) {
            $locale = 'tr';
        }
        
        // Dili ayarla
        app()->setLocale($locale);
        
        return $next($request);
    }
    
    /**
     * Varsayılan dili getir
     */
    private function getDefaultLanguage(): string
    {
        // Sistem dillerinden varsayılanı al
        if (class_exists('Modules\LanguageManagement\App\Models\SystemLanguage')) {
            $defaultLanguage = \Modules\LanguageManagement\App\Models\SystemLanguage::where('is_default', true)
                ->where('is_active', true)
                ->first();
                
            if ($defaultLanguage) {
                return $defaultLanguage->code;
            }
        }
        
        return config('app.locale', 'tr');
    }
    
    /**
     * Dil desteklenip desteklenmediğini kontrol et
     */
    private function isLanguageSupported(string $locale): bool
    {
        // Sistem dillerinden kontrol et
        if (class_exists('Modules\LanguageManagement\App\Models\SystemLanguage')) {
            return \Modules\LanguageManagement\App\Models\SystemLanguage::where('code', $locale)
                ->where('is_active', true)
                ->exists();
        }
        
        // Fallback olarak config'den desteklenen dilleri kontrol et
        $supportedLanguages = ['tr', 'en'];
        return in_array($locale, $supportedLanguages);
    }
    
    /**
     * Browser dilinden desteklenen dili bul
     */
    private function getSupportedLanguage(string $browserLanguage): ?string
    {
        if ($this->isLanguageSupported($browserLanguage)) {
            return $browserLanguage;
        }
        
        return null;
    }
}