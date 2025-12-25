<?php

namespace App\Services\CacheProfiles;

/**
 * Shop modülü cache profile
 * E-ticaret modülü olan tenant'lar için
 */
class ShopCacheProfile implements ModuleCacheProfileInterface
{
    public function getDynamicPaths(): array
    {
        return [
            // Sipariş sayfaları
            'orders',
            'orders/*',

            // Ödeme işlemleri
            'payment/*',

            // Fatura/invoice
            'invoices',
            'invoices/*',
        ];
    }

    public function getExcludedPaths(): array
    {
        return [
            // Ödeme callback'leri
            'payment/callback/*',
            'payment/webhook/*',
        ];
    }

    public function getTenantIds(): array
    {
        // Tüm tenant'larda aktif (e-ticaret ortak)
        return [];
    }

    public function getModuleName(): string
    {
        return 'Shop';
    }
}
