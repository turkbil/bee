<?php

namespace App\Services\CacheProfiles;

/**
 * Muzibu modülü cache profile
 * Sadece Tenant 1001 (muzibu.com.tr) için aktif
 */
class MuzibuCacheProfile implements ModuleCacheProfileInterface
{
    public function getDynamicPaths(): array
    {
        return [
            // Favoriler
            'muzibu/favorites',
            'muzibu/favorites/*',

            // Çalma listeleri
            'muzibu/my-playlists',
            'muzibu/my-playlists/*',
            'muzibu/playlist/*/edit',

            // Dashboard
            'muzibu/dashboard',
            'muzibu/dashboard/*',

            // Dinleme geçmişi
            'muzibu/listening-history',
            'muzibu/listening-history/*',

            // Kurumsal müzik
            'muzibu/corporate/*',
        ];
    }

    public function getExcludedPaths(): array
    {
        // Muzibu'ya özel excluded path yok (şimdilik)
        return [];
    }

    public function getTenantIds(): array
    {
        // Sadece Tenant 1001 (Muzibu)
        return [1001];
    }

    public function getModuleName(): string
    {
        return 'Muzibu';
    }
}
