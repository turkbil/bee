<?php

namespace App\Services;

use Spatie\ResponseCache\CacheProfiles\CacheProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class TenantCacheProfile implements CacheProfile
{
    public function enabled(Request $request): bool
    {
        // Global config kontrolü
        if (!config('responsecache.enabled', true)) {
            return false;
        }

        // Tenant bazlı kontrol (Settings'den)
        return setting('response_cache_enabled', true);
    }

    /**
     * Dinamik sayfalar - kullanıcıya özel içerik, asla cache'lenmemeli
     */
    protected array $dynamicPaths = [
        'favorites',
        'favorites/*',
        'my-playlists',
        'my-playlists/*',
        'playlist/*/edit',
        'dashboard',
        'dashboard/*',
        'listening-history',
        'listening-history/*',
        'corporate/*',
        'api/*',
        'cart',
        'cart/*',
        'checkout',
        'checkout/*',
    ];

    public function shouldCacheRequest(Request $request): bool
    {
        // MUTLAK ADMIN CACHE ENGELLEMESİ - İLK KONTROL
        $path = $request->path();
        if (str_starts_with($path, 'admin') || str_contains($path, '/admin')) {
            return false;
        }

        // Admin No-Cache Header kontrolü
        if ($request->header('X-Cache-Bypass') === 'admin' || $request->header('X-Admin-No-Cache')) {
            return false;
        }

        // 🔴 DİNAMİK SAYFALAR - AUTH KULLANICILARI İÇİN CACHE YOK!
        // Favoriler, playlist'ler, dashboard vb. kullanıcıya özel sayfalar
        if (auth()->check()) {
            foreach ($this->dynamicPaths as $pattern) {
                if ($request->is($pattern)) {
                    return false;
                }
            }
        }

        // Temel kontroller
        if ($request->ajax() || $request->isMethod('get') === false) {
            return false;
        }

        // Config'den excluded paths al
        $excludedPaths = config('responsecache.excluded_paths', [
            'admin/*',
            'language/*',
            'debug-lang/*',
            'debug/*',
            'livewire/*',
            '*/login',          // Tüm dillerde login
            '*/register',       // Tüm dillerde register
            '*/logout',         // Tüm dillerde logout
            '*/forgot-password',
            '*/reset-password/*',
            '*/password/*',
            '*/verify-email/*'
        ]);

        foreach ($excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Cache bypass parametreleri
        $bypassParams = ['_', 'lang_changed', 'cb', 'no_cache'];
        if ($request->hasAny($bypassParams)) {
            return false;
        }

        return true;
    }

    public function shouldCacheResponse(Response $response): bool
    {
        // Redirect response'ları cache'leme
        if ($response->isRedirection()) {
            return false;
        }

        if ($response->isSuccessful()) {
            // PREFETCH İÇİN AGRESIF CACHE HEADER'LARI
            $response->setPublic();
            $response->setMaxAge(3600); // 1 saat
            $response->setSharedMaxAge(3600);

            // Eski no-cache header'larını kaldır
            $response->headers->remove('Pragma');
            $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');

            return true;
        }

        return false;
    }

    public function cacheRequestUntil(Request $request): DateTime
    {
        $seconds = config('responsecache.cache_lifetime_in_seconds', 60 * 60 * 24 * 7);
        return now()->addSeconds($seconds);
    }

    public function useCacheNameSuffix(Request $request): string
    {
        $tenant = tenant();
        $tenantId = $tenant ? $tenant->id : 'central';

        // URL-BASED CACHE KEY (URL zaten locale içeriyor: /en/page, /ar/announcements)
        // Locale session'dan değil URL'den alınır - middleware sıralamasından bağımsız
        $url = $request->fullUrl();

        // AUTH AWARE CACHE KEY - GUEST için tek cache (performans!)
        $authSuffix = auth()->check() ? 'auth_' . auth()->id() : 'guest';

        return "tenant_{$tenantId}_{$authSuffix}_" . md5($url);
    }
}