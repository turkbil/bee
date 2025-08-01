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
        $urlLocale = $segments[0] ?? null;
        
        // Geçerli locale'ler
        $validLocales = array_column(available_tenant_languages(), 'code');
        $defaultLocale = get_tenant_default_locale();
        
        // Öncelik sırası:
        // 1. URL'deki locale (eğer varsa ve geçerliyse)
        // 2. Cookie'deki tercih
        // 3. Session'daki tercih
        // 4. Browser Accept-Language header
        // 5. Default locale
        
        $locale = null;
        
        // 1. URL'de belirtilmiş locale varsa
        if ($urlLocale && in_array($urlLocale, $validLocales)) {
            $locale = $urlLocale;
        }
        
        // 2. URL'de yoksa, kullanıcının daha önce seçtiği tercih var mı?
        if (!$locale && !$urlLocale) {
            // Cookie'den kontrol et
            $cookieLocale = $request->cookie('tenant_locale_preference');
            if ($cookieLocale && in_array($cookieLocale, $validLocales)) {
                $locale = $cookieLocale;
            }
            
            // 3. Session'dan kontrol et
            if (!$locale) {
                $sessionLocale = session('tenant_locale');
                if ($sessionLocale && in_array($sessionLocale, $validLocales)) {
                    $locale = $sessionLocale;
                }
            }
            
            // 4. Browser tercihini kontrol et
            if (!$locale) {
                $locale = $this->detectBrowserLocale($request, $validLocales);
            }
            
            // 5. Hala yoksa default locale
            if (!$locale) {
                $locale = $defaultLocale;
            }
            
            // Ana sayfadaysa ve default locale değilse, uygun dile yönlendir
            if ($locale !== $defaultLocale && !$request->is('*/') && count($segments) === 0) {
                return redirect()->to('/' . $locale);
            }
        }
        
        // Locale'i ayarla
        if ($locale) {
            app()->setLocale($locale);
            session(['tenant_locale' => $locale]);
            
            // URL'den geliyorsa cookie'ye kaydet (kullanıcı tercihi olarak)
            if ($urlLocale && $locale === $urlLocale) {
                \Cookie::queue('tenant_locale_preference', $locale, 525600); // 365 gün
            }
        }
        
        return $next($request);
    }
    
    /**
     * Browser'ın Accept-Language header'ından dil tespit et
     * Hem masaüstü hem mobil cihazlar için çalışır
     */
    private function detectBrowserLocale(Request $request, array $validLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }
        
        // Accept-Language header'ını parse et
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $lang = trim($part);
            $quality = 1.0;
            
            // Quality değerini kontrol et
            // Örnekler:
            // - tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7 (Desktop)
            // - tr-TR,tr;q=0.9 (Mobile Safari)
            // - tr-TR,en-US;q=0.9,en;q=0.8 (Android Chrome)
            if (preg_match('/^([a-z]{2})(?:-[A-Z]{2})?(?:;q=([0-9.]+))?/i', $lang, $matches)) {
                $langCode = strtolower($matches[1]);
                if (isset($matches[2])) {
                    $quality = (float) $matches[2];
                }
                
                // Aynı dil kodu varsa, en yüksek quality'yi al
                if (!isset($languages[$langCode]) || $languages[$langCode] < $quality) {
                    $languages[$langCode] = $quality;
                }
            }
        }
        
        // Quality'e göre sırala (yüksekten düşüğe)
        arsort($languages);
        
        // İlk eşleşen valid locale'i bul
        foreach ($languages as $langCode => $quality) {
            if (in_array($langCode, $validLocales)) {
                return $langCode;
            }
        }
        
        // Mobil cihazlarda bazen sadece ülke kodu gelebilir
        // Sistem dili ayarlarından gelen değeri de kontrol et
        $userAgent = $request->header('User-Agent');
        if ($userAgent && $this->isMobileDevice($userAgent)) {
            // Mobil cihaz algılandı, ekstra kontroller yapılabilir
            // Şimdilik standart Accept-Language yeterli
        }
        
        return null;
    }
    
    /**
     * Mobil cihaz kontrolü
     */
    private function isMobileDevice(string $userAgent): bool
    {
        return preg_match('/(android|iphone|ipad|mobile|phone)/i', $userAgent) !== false;
    }
}