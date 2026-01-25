<?php

namespace App\Services;

use Spatie\ResponseCache\CacheProfiles\CacheProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Services\CacheProfiles\ModuleCacheProfileInterface;

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
     * Modül cache profile'larını yükle (otomatik discover)
     * CacheProfiles klasöründeki tüm profilleri otomatik bulur ve yükler
     */
    protected function loadModuleProfiles(): array
    {
        $profiles = [];
        $profilePath = app_path('Services/CacheProfiles');

        if (!is_dir($profilePath)) {
            return $profiles;
        }

        // CacheProfiles klasöründeki tüm PHP dosyalarını bul
        $files = glob($profilePath . '/*CacheProfile.php');

        foreach ($files as $file) {
            $className = 'App\\Services\\CacheProfiles\\' . basename($file, '.php');

            // Class var mı ve interface implement ediyor mu kontrol et
            if (class_exists($className)) {
                $instance = new $className();

                if ($instance instanceof ModuleCacheProfileInterface) {
                    $profiles[] = $instance;
                }
            }
        }

        return $profiles;
    }

    /**
     * Tenant-aware dinamik sayfalar
     * Modül cache profile'larından otomatik toplar
     */
    protected function getDynamicPaths(): array
    {
        $tenant = tenant();
        $tenantId = $tenant ? $tenant->id : null;
        $allPaths = [];

        // Tüm modül profile'larını yükle
        $profiles = $this->loadModuleProfiles();

        foreach ($profiles as $profile) {
            $moduleTenantIds = $profile->getTenantIds();

            // Tenant kontrolü:
            // - Boş array = tüm tenant'larda aktif
            // - Dolu array = sadece belirtilen tenant'larda aktif
            if (empty($moduleTenantIds) || in_array($tenantId, $moduleTenantIds)) {
                $allPaths = array_merge($allPaths, $profile->getDynamicPaths());
            }
        }

        return array_unique($allPaths);
    }

    /**
     * Config excluded paths
     * Modül cache profile'larından otomatik toplar
     */
    protected function getModuleExcludedPaths(): array
    {
        $tenant = tenant();
        $tenantId = $tenant ? $tenant->id : null;
        $allPaths = [];

        // Tüm modül profile'larını yükle
        $profiles = $this->loadModuleProfiles();

        foreach ($profiles as $profile) {
            $moduleTenantIds = $profile->getTenantIds();

            // Tenant kontrolü
            if (empty($moduleTenantIds) || in_array($tenantId, $moduleTenantIds)) {
                $allPaths = array_merge($allPaths, $profile->getExcludedPaths());
            }
        }

        return array_unique($allPaths);
    }

    public function shouldCacheRequest(Request $request): bool
    {
        // MUTLAK ADMIN CACHE ENGELLEMESİ - İLK KONTROL
        $path = $request->path();
        if (str_starts_with($path, 'admin') || str_contains($path, '/admin')) {
            return false;
        }

        // 🔴 LIVEWIRE ENDPOINT'LERİ - KESİNLİKLE CACHE'LEME!
        // Livewire update istekleri dinamik ve CSRF token gerektirir
        if (str_starts_with($path, 'livewire')) {
            return false;
        }

        // Admin No-Cache Header kontrolü
        if ($request->header('X-Cache-Bypass') === 'admin' || $request->header('X-Admin-No-Cache')) {
            return false;
        }

        // 🔴 DİNAMİK SAYFALAR - AUTH KULLANICILARI İÇİN CACHE YOK!
        // Favoriler, playlist'ler, dashboard vb. kullanıcıya özel sayfalar
        // TENANT-AWARE: Her tenant kendi modüllerine göre dinamik path'lere sahip
        if (auth()->check()) {
            foreach ($this->getDynamicPaths() as $pattern) {
                if ($request->is($pattern)) {
                    return false;
                }
            }
        }

        // Temel kontroller
        if ($request->ajax() || $request->isMethod('get') === false) {
            return false;
        }

        // Modül cache profile'larından excluded paths al (otomatik, tenant-aware)
        $excludedPaths = $this->getModuleExcludedPaths();

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