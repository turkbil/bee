<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LanguageManagement\app\Services\LanguageService;
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
    public function handle(Request $request, Closure $next, string $context = 'admin'): Response
    {
        // URL'den dil parametresi var mı kontrol et
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
            // URL'de dil yok, varsayılan dili kullan
            $defaultLanguage = $this->languageService->getDefaultLanguage($context);
            $this->languageService->setLocale($defaultLanguage, $context);
        }

        return $next($request);
    }
}