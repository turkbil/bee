<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class MegaMenuCacheService
{
    /**
     * Cache key prefix
     */
    protected const CACHE_PREFIX = 'megamenu_html';

    /**
     * Cache duration in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get cached megamenu HTML or generate and cache it
     */
    public static function getHtml(string $type = 'products'): string
    {
        // Admin sayfalarında cache kullanma
        if (request()->is('admin/*')) {
            return '';
        }

        // Tenant context yoksa boş döner
        if (!function_exists('tenant') || !tenant()) {
            return '';
        }

        $cacheKey = self::getCacheKey($type);

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($type) {
                return self::renderHtml($type);
            });
        } catch (\Exception $e) {
            Log::warning('MegaMenuCache: Cache error', ['error' => $e->getMessage()]);
            // Fallback: render without cache
            return self::renderHtml($type);
        }
    }

    /**
     * Generate cache key with tenant and locale
     */
    protected static function getCacheKey(string $type): string
    {
        $tenantId = tenant()->id ?? 'default';
        $locale = app()->getLocale();

        return self::CACHE_PREFIX . "_{$tenantId}_{$locale}_{$type}";
    }

    /**
     * Render megamenu HTML from blade
     */
    protected static function renderHtml(string $type): string
    {
        $tenantId = tenant()->id ?? 2;
        $viewPath = "themes.ixtif.partials.mega-menu.{$tenantId}.{$type}";

        if (!View::exists($viewPath)) {
            return '';
        }

        try {
            return view($viewPath)->render();
        } catch (\Exception $e) {
            Log::error('MegaMenuCache: Render error', [
                'view' => $viewPath,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Invalidate all megamenu caches for current tenant
     */
    public static function invalidate(): void
    {
        if (!function_exists('tenant') || !tenant()) {
            return;
        }

        $tenantId = tenant()->id;
        $locales = ['tr', 'en']; // Supported locales
        $types = ['products', 'hakkimizda'];

        // HTML cache'leri temizle
        foreach ($locales as $locale) {
            foreach ($types as $type) {
                $key = self::CACHE_PREFIX . "_{$tenantId}_{$locale}_{$type}";
                Cache::forget($key);
            }
        }

        // Kategori cache'ini de temizle (mobile menu için)
        Cache::forget("megamenu_categories_{$tenantId}");

        Log::info('MegaMenuCache: Cache invalidated', ['tenant_id' => $tenantId]);
    }

    /**
     * Invalidate and regenerate cache (warm up)
     */
    public static function warmUp(): void
    {
        self::invalidate();

        // Regenerate main megamenu
        self::getHtml('products');

        Log::info('MegaMenuCache: Cache warmed up', ['tenant_id' => tenant()->id ?? 'unknown']);
    }

    /**
     * Check if cache exists
     */
    public static function exists(string $type = 'products'): bool
    {
        return Cache::has(self::getCacheKey($type));
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $tenantId = tenant()->id ?? 'unknown';
        $locale = app()->getLocale();

        return [
            'tenant_id' => $tenantId,
            'locale' => $locale,
            'products_cached' => self::exists('products'),
            'cache_key' => self::getCacheKey('products'),
            'ttl' => self::CACHE_TTL,
        ];
    }

    /**
     * Get cached menu categories (for mobile menu etc)
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getCategories(): \Illuminate\Support\Collection
    {
        if (!function_exists('tenant') || !tenant()) {
            return collect();
        }

        $tenantId = tenant()->id;
        $cacheKey = "megamenu_categories_{$tenantId}";

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () {
                return \Modules\Shop\App\Models\ShopCategory::where('is_active', 1)
                    ->where('show_in_menu', 1)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order', 'asc')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::warning('MegaMenuCache: Categories cache error', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}
