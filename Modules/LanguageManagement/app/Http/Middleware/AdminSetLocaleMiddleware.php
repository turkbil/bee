<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AdminSetLocaleMiddleware
{
    /**
     * 3 Aşamalı Hibrit Admin Dil Sistemi
     * 1. ACTIVE CHOICE (Session - bu oturumda değiştirildi mi?)
     * 2. STORED PREFERENCE (User DB - kalıcı tercih)
     * 3. SMART DEFAULT (Config varsayılan + fallback)
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('🎯 AdminSetLocaleMiddleware BAŞLADI', [
            'url' => $request->url(),
            'path' => $request->path()
        ]);
        
        $locale = $this->resolveAdminLocale();
        
        // Laravel app locale'i admin dili ile ayarla (admin çevirileri için)
        app()->setLocale($locale);
        
        \Log::info('🔧 AdminSetLocaleMiddleware - Admin locale ayarlandı', [
            'admin_locale' => $locale,
            'session_admin_locale' => session('admin_locale'),
            'app_locale' => app()->getLocale(),
            'url' => $request->url()
        ]);
        
        // Session'da da sakla (consistency için)
        if (!session()->has('admin_locale') || session('admin_locale') !== $locale) {
            session(['admin_locale' => $locale]);
        }

        return $next($request);
    }

    /**
     * 3 Aşamalı Admin Dil Tespiti
     */
    private function resolveAdminLocale(): string
    {
        \Log::info('🔧 resolveAdminLocale BAŞLADI', [
            'session_admin_locale' => session('admin_locale'),
            'auth_check' => auth()->check(),
            'user_admin_locale' => auth()->check() ? auth()->user()->admin_locale : 'NOT_AUTH'
        ]);
        
        // 1. ACTIVE CHOICE - Session'da bu oturum için ayarlanmış dil var mı?
        if (session()->has('admin_locale') && $this->isValidAdminLocale(session('admin_locale'))) {
            \Log::info('🔧 resolveAdminLocale - Session değeri döndürülüyor', [
                'admin_locale' => session('admin_locale')
            ]);
            return session('admin_locale');
        }
        
        // 2. STORED PREFERENCE - Login kullanıcının kalıcı tercihi
        if (auth()->check() && auth()->user()->admin_locale && $this->isValidAdminLocale(auth()->user()->admin_locale)) {
            // Session'a da kaydet ki bir sonraki requestte 1. aşamadan gelsin
            session(['admin_locale' => auth()->user()->admin_locale]);
            
            // Cookie'ye de kaydet (logout sonrası hatırlama için)
            Cookie::queue('admin_locale_preference', auth()->user()->admin_locale, 525600);
            
            \Log::info('🔧 resolveAdminLocale - User DB değeri döndürülüyor', [
                'user_admin_locale' => auth()->user()->admin_locale
            ]);
            
            return auth()->user()->admin_locale;
        }
        
        // 2b. STORED PREFERENCE - Guest için cookie tercihi
        if (!auth()->check()) {
            $cookieLocale = Cookie::get('admin_locale_preference');
            if ($cookieLocale && $this->isValidAdminLocale($cookieLocale)) {
                session(['admin_locale' => $cookieLocale]);
                return $cookieLocale;
            }
        }
        
        // 3. SMART DEFAULT - Sistem varsayılanı + fallback
        $defaultLocale = config('app.admin_default_locale', 'tr');
        
        \Log::info('🔧 resolveAdminLocale - Default değer kontrolü', [
            'default_locale' => $defaultLocale,
            'is_valid' => $this->isValidAdminLocale($defaultLocale)
        ]);
        
        if ($this->isValidAdminLocale($defaultLocale)) {
            session(['admin_locale' => $defaultLocale]);
            return $defaultLocale;
        }
        
        // Final fallback
        session(['admin_locale' => 'tr']);
        \Log::info('🔧 resolveAdminLocale - Final fallback: tr');
        return 'tr';
    }

    /**
     * Admin için geçerli dil kontrolü
     * Not: admin_languages tablosu central DB'de, kontrol et
     */
    private function isValidAdminLocale(string $locale): bool
    {
        if (empty($locale) || strlen($locale) > 10) {
            return false;
        }
        
        try {
            // admin_languages tablosundan kontrol et (central DB'de)
            $exists = \DB::table('admin_languages')
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