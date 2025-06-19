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
        // GET request ve başarılı response
        if ($request->ajax()) {
            return false;
        }

        if ($request->isMethod('get') === false) {
            return false;
        }

        // Admin sayfalarını cache'leme
        if ($request->is('admin/*')) {
            return false;
        }

        // Auth gerektiren sayfalarını cache'leme - Auth olsa da cache et
        // Not: Production'da auth kullanıcıları için cache kapatmak istersen bu satırı aç:
        // if (auth()->check()) {
        //     return false;
        // }
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
        
        return "tenant_{$tenantId}_" . md5($request->fullUrl());
    }
}