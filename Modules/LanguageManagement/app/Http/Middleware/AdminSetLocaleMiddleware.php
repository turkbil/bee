<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LanguageManagement\app\Services\LanguageService;
use Symfony\Component\HttpFoundation\Response;

class AdminSetLocaleMiddleware
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Handle an incoming request for ADMIN context only
     * Uses system_languages table and admin_locale session
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('🔧 AdminSetLocaleMiddleware BAŞLADI', [
            'url' => $request->fullUrl(),
            'current_app_locale' => app()->getLocale(),
            'session_admin_locale' => session('admin_locale'),
            'user_admin_preference' => auth()->check() ? auth()->user()->admin_language_preference : null
        ]);

        // URL'den dil parametresi kontrol et (route parameter veya query string)
        $languageFromUrl = $request->route('locale') ?? $request->get('lang');
        
        if ($languageFromUrl) {
            // Admin context için system_languages tablosundan geçerlilik kontrol et
            if ($this->languageService->isValidLanguageForContext($languageFromUrl, 'admin')) {
                $this->languageService->setLocale($languageFromUrl, 'admin');
                
                // Kullanıcı admin dil tercihini kaydet
                if (auth()->check()) {
                    $this->languageService->setUserLanguagePreference($languageFromUrl, 'admin');
                }
                
                \Log::info('✅ Admin URL parametresinden dil ayarlandı', [
                    'locale' => $languageFromUrl,
                    'source' => 'url_parameter'
                ]);
            }
        } else {
            // URL'de dil yok, admin session/user tercihi/varsayılan sırasıyla kontrol et
            $currentLanguage = $this->languageService->getCurrentLocale('admin');
            
            // Sadece mevcut locale farklıysa güncelle
            if (app()->getLocale() !== $currentLanguage) {
                $this->languageService->setLocale($currentLanguage, 'admin');
                
                \Log::info('🔄 Admin LanguageService ile locale güncellendi', [
                    'from' => app()->getLocale(),
                    'to' => $currentLanguage,
                    'source' => 'session_or_preference'
                ]);
            }
        }

        \Log::info('🎯 AdminSetLocaleMiddleware TAMAMLANDI', [
            'final_app_locale' => app()->getLocale(),
            'final_session_admin_locale' => session('admin_locale')
        ]);

        return $next($request);
    }
}