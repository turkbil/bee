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
        // Muzibu'ya özel path'ler (muzibu/ prefix'li olanlar)
        // NOT: dashboard, corporate, favorites vb. CoreCacheProfile'da zaten tanımlı
        return [
            // Favoriler (muzibu/ prefix'li)
            'muzibu/favorites',
            'muzibu/favorites/*',

            // Çalma listeleri (muzibu/ prefix'li)
            'muzibu/my-playlists',
            'muzibu/my-playlists/*',
            'muzibu/playlist/*/edit',

            // Dinleme geçmişi (muzibu/ prefix'li)
            'muzibu/listening-history',
            'muzibu/listening-history/*',

            // Abonelik ve ödeme geçmişi
            'my-subscriptions',

            // Playlist detay düzenleme
            'playlists/*/edit',
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
