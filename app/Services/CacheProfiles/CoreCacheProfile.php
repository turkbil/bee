<?php

namespace App\Services\CacheProfiles;

/**
 * Core sistem cache profile
 * Tüm tenant'larda ortak olan path'ler (cart, checkout, api, auth vb.)
 */
class CoreCacheProfile implements ModuleCacheProfileInterface
{
    public function getDynamicPaths(): array
    {
        return [
            // API endpoint'leri
            'api/*',

            // E-ticaret dinamik sayfalar
            'cart',
            'cart/*',
            'checkout',
            'checkout/*',

            // Kullanıcı dashboard'ları
            'dashboard',
            'dashboard/*',

            // Kurumsal üyelik
            'corporate/*',

            // Favoriler (genel)
            'favorites',
            'favorites/*',

            // Çalma listeleri (genel)
            'my-playlists',
            'my-playlists/*',

            // Dinleme geçmişi
            'listening-history',
            'listening-history/*',
        ];
    }

    public function getExcludedPaths(): array
    {
        return [
            // Admin paneli
            'admin/*',

            // Dil değişimi
            'language/*',
            'debug-lang/*',

            // Debug
            'debug/*',

            // Livewire
            'livewire/*',

            // Auth sayfaları (tüm dillerde)
            '*/login',
            '*/register',
            '*/logout',
            '*/forgot-password',
            '*/reset-password/*',
            '*/password/*',
            '*/verify-email/*',
        ];
    }

    public function getTenantIds(): array
    {
        // Tüm tenant'larda aktif
        return [];
    }

    public function getModuleName(): string
    {
        return 'Core';
    }
}
