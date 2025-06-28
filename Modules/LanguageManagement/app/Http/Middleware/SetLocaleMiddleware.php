<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LanguageManagement\app\Services\LanguageService;
use Modules\LanguageManagement\app\Services\UrlPrefixService;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $context = 'site'): Response
    {
        \Log::info('🔧 NEW SetLocaleMiddleware BAŞLADI', [
            'context' => $context,
            'url' => $request->fullUrl(),
            'route_lang' => $request->route('lang'),
            'current_app_locale' => app()->getLocale(),
            'session_site_locale' => session('site_locale'),
            'session_admin_locale' => session('admin_locale')
        ]);
        
        // Site context için URL prefix desteği
        if ($context === 'site') {
            // YENİ: UrlPrefixService ile gelişmiş URL parsing
            if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
                $urlData = UrlPrefixService::parseUrl($request);
                
                // Debug bilgilerini log'la (gerektiğinde aktif edilebilir)
                // if (isset($urlData['debug_info'])) {
                //     \Log::info('🔍 SetLocaleMiddleware UrlPrefixService DEBUG', $urlData['debug_info']);
                // }
                
                if ($urlData['language']) {
                    // UrlPrefixService'ten gelen language object'inden locale'i çıkar
                    $locale = null;
                    if (is_object($urlData['language'])) {
                        $locale = $urlData['language']->code ?? $urlData['language']->prefix ?? null;
                    } else {
                        $locale = $urlData['language'];
                    }
                    
                    if ($locale) {
                        // Laravel locale'i ayarla
                        app()->setLocale($locale);
                        
                        // Session'a kaydet
                        session(['site_locale' => $locale]);
                    
                        // Request'e temiz path'i yeniden ata (opsiyonel)
                        if ($urlData['has_prefix']) {
                            $request->merge(['clean_path' => $urlData['clean_path']]);
                        }
                        
                        // Log
                        \Log::info('✅ URL prefix ile dil ayarlandı', [
                            'locale' => $locale,
                            'has_prefix' => $urlData['has_prefix'],
                            'clean_path' => $urlData['clean_path']
                        ]);
                        
                        // UrlPrefixService başarılı oldu, diğer kontrolları ATLAMA!
                        // return $next($request); // Bu satırı kaldırıyoruz
                    }
                }
            }
            
            // FALLBACK: Eski yöntemler
            // Route parametresinden dil prefix'ini kontrol et
            $langFromRoute = $request->route('locale') ?? $request->route('lang');
            
            if ($langFromRoute) {
                // Route'dan gelen dil geçerli mi kontrol et
                if ($this->languageService->isValidLanguageForContext($langFromRoute, $context)) {
                    $this->languageService->setLocale($langFromRoute, $context);
                    
                    // Eğer kullanıcı giriş yapmışsa tercihini kaydet
                    if (auth()->check()) {
                        $this->languageService->setUserLanguagePreference($langFromRoute, $context);
                    }
                    
                    return $next($request);
                }
            }
        }
        
        // URL'den dil parametresi var mı kontrol et (eski yöntem - query parameter)
        $languageFromUrl = $request->route('language') ?? $request->get('lang');
        
        if ($languageFromUrl) {
            // URL'den gelen dil geçerli mi kontrol et
            if ($this->languageService->isValidLanguageForContext($languageFromUrl, $context)) {
                $this->languageService->setLocale($languageFromUrl, $context);
                
                // Eğer kullanıcı giriş yapmışsa tercihini kaydet
                if (auth()->check()) {
                    $this->languageService->setUserLanguagePreference($languageFromUrl, $context);
                }
            }
        } else {
            // URL'de dil yok, session/user tercihi/varsayılan sırasıyla kontrol et
            $currentLanguage = $this->languageService->getCurrentLocale($context);
            
            // Sadece mevcut locale farklıysa güncelle
            if (app()->getLocale() !== $currentLanguage) {
                $this->languageService->setLocale($currentLanguage, $context);
                \Log::info('🔄 LanguageService ile locale güncellendi', [
                    'from' => app()->getLocale(),
                    'to' => $currentLanguage,
                    'context' => $context
                ]);
            }
        }

        \Log::info('🎯 NEW SetLocaleMiddleware TAMAMLANDI', [
            'context' => $context,
            'final_app_locale' => app()->getLocale(),
            'final_session_site_locale' => session('site_locale'),
            'final_session_admin_locale' => session('admin_locale')
        ]);

        return $next($request);
    }
}