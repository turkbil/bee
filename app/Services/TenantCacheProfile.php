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
        return config('responsecache.enabled', true);
    }

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
        
        // Debug log sadece local/staging'de
        if (app()->environment(['local', 'staging'])) {
            \Log::debug('Cache profile check', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'is_ajax' => $request->ajax(),
                'is_auth' => auth()->check(),
                'path' => $path
            ]);
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
            'livewire/*'
        ]);
        
        foreach ($excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Cache bypass parametreleri
        $bypassParams = ['_', 'lang_changed', 'cb', 'no_cache'];
        if ($request->hasAny($bypassParams)) {
            if (app()->environment(['local', 'staging'])) {
                \Log::debug('Cache bypass params detected', [
                    'params' => $request->only($bypassParams)
                ]);
            }
            return false;
        }
        
        return true;
    }

    public function shouldCacheResponse(Response $response): bool
    {
        if ($response->isSuccessful()) {
            // Cache header'larını düzelt
            $response->setPublic();
            $response->setMaxAge(3600); // 1 saat
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