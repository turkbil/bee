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

            // Kurumsal üyelik (ana sayfa + alt sayfalar)
            'corporate',
            'corporate/*',

            // Abonelik (Subscription) - Ödeme içerir
            'subscription',
            'subscription/*',
            'subscriptions',
            'subscriptions/*',
            'pricing',
            'pricing/*',
            'plans',
            'plans/*',

            // Favoriler (genel)
            'favorites',
            'favorites/*',

            // Çalma listeleri (genel)
            'my-playlists',
            'my-playlists/*',

            // Dinleme geçmişi
            'listening-history',
            'listening-history/*',

            // Profil ve hesap ayarları
            'profile',
            'profile/*',
            'account',
            'account/*',
            'settings',
            'settings/*',

            // Siparişler ve faturalar
            'orders',
            'orders/*',
            'invoices',
            'invoices/*',

            // Bildirimler
            'notifications',
            'notifications/*',

            // İstek listesi
            'wishlist',
            'wishlist/*',
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

            // Payment callback'leri (ASLA cache'lenmemeli!)
            'payment/*',
            'payment/callback/*',
            'webhook/*',
            'webhooks/*',
            'ipn/*',

            // Download linkleri (kullanıcıya özel)
            'download/*',
            'downloads/*',
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
