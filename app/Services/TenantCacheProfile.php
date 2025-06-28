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
        \Log::info('🔧 CACHE PROFILE: shouldCacheRequest çağrıldı', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_auth' => auth()->check(),
            'query_params' => $request->query()
        ]);
        
        // GET request ve başarılı response
        if ($request->ajax()) {
            \Log::info('🚫 CACHE: Ajax request - cache yok');
            return false;
        }

        if ($request->isMethod('get') === false) {
            \Log::info('🚫 CACHE: Non-GET request - cache yok');
            return false;
        }

        // Admin sayfalarını cache'leme
        if ($request->is('admin/*')) {
            return false;
        }

        // DİL DEĞİŞTİRME ROUTE'LARINI CACHE'LEME!
        if ($request->is('language/*')) {
            return false;
        }

        // CACHE BYPASS QUERY PARAMETRELERİ KONTROLÜ
        if ($request->has(['_', 'lang_changed']) || $request->has('cb')) {
            \Log::info('🚫 CACHE BYPASS: Dil değişikliği parametreleri mevcut', [
                'lang_changed' => $request->get('lang_changed'),
                'timestamp' => $request->get('_'),
                'cache_buster' => $request->get('cb')
            ]);
            return false;
        }

        // Debug sayfalarını cache'leme
        if ($request->is('debug-lang/*') || $request->is('debug/*')) {
            return false;
        }
        
        // Cache temizlendi, normal cache devam etsin

        // AUTH USER'LAR İÇİN DE CACHE AKTİF - PC bazlı cache silme ile çözüldü
        if (auth()->check()) {
            \Log::info('✅ CACHE ENABLED for auth user', [
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            // Auth cache artık açık, login/logout'ta temizlik yapılıyor
        }
        
        \Log::info('✅ CACHE: Request cache\'lenecek', [
            'url' => $request->fullUrl(),
            'auth_status' => auth()->check() ? 'auth_' . auth()->id() : 'guest'
        ]);
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
        
        // LOCALE AWARE CACHE KEY
        $locale = app()->getLocale();
        
        // AUTH AWARE CACHE KEY - CRITICAL!
        $authSuffix = auth()->check() ? 'auth_' . auth()->id() : 'guest';
        
        return "tenant_{$tenantId}_{$authSuffix}_locale_{$locale}_" . md5($request->fullUrl());
    }
}