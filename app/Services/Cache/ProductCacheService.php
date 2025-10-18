<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Modules\Shop\App\Models\ShopProduct;

/**
 * Product Data Cache Service
 *
 * Provides Redis caching for product data to improve AI chat performance
 * TTL: 1 hour (3600 seconds)
 * Invalidation: On product update/delete
 */
class ProductCacheService
{
    /**
     * Cache key prefix
     */
    const PREFIX = 'product_cache';

    /**
     * Cache TTL (1 hour)
     */
    const TTL = 3600;

    /**
     * Get product by ID with caching
     */
    public static function getProduct(int $productId, ?int $tenantId = null): ?array
    {
        $key = self::buildKey('product', $productId, $tenantId);

        return Cache::remember($key, self::TTL, function () use ($productId) {
            $product = ShopProduct::find($productId);

            if (!$product) {
                return null;
            }

            return self::formatProduct($product);
        });
    }

    /**
     * Get multiple products by IDs with caching
     */
    public static function getProducts(array $productIds, ?int $tenantId = null): array
    {
        $products = [];

        foreach ($productIds as $productId) {
            $product = self::getProduct($productId, $tenantId);
            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Get products by category with caching
     */
    public static function getProductsByCategory(int $categoryId, ?int $tenantId = null, int $limit = 10): array
    {
        $key = self::buildKey('category', $categoryId, $tenantId, $limit);

        return Cache::remember($key, self::TTL, function () use ($categoryId, $limit) {
            $products = ShopProduct::where('category_id', $categoryId)
                ->where('is_active', true)
                ->limit($limit)
                ->get();

            return $products->map(fn($p) => self::formatProduct($p))->toArray();
        });
    }

    /**
     * Get products for AI context (optimized for prompt)
     */
    public static function getProductsForAI(array $productIds, ?int $tenantId = null): array
    {
        $key = self::buildKey('ai_context', implode('_', $productIds), $tenantId);

        return Cache::remember($key, self::TTL, function () use ($productIds) {
            $products = ShopProduct::whereIn('product_id', $productIds)
                ->where('is_active', true)
                ->get();

            return $products->map(fn($p) => self::formatProductForAI($p))->toArray();
        });
    }

    /**
     * Invalidate product cache
     */
    public static function invalidateProduct(int $productId, ?int $tenantId = null): void
    {
        $key = self::buildKey('product', $productId, $tenantId);
        Cache::forget($key);

        // Also invalidate AI context caches that might contain this product
        self::invalidateProductAICaches($productId, $tenantId);
    }

    /**
     * Invalidate category cache
     */
    public static function invalidateCategory(int $categoryId, ?int $tenantId = null): void
    {
        // Clear all limits for this category
        for ($limit = 1; $limit <= 100; $limit++) {
            $key = self::buildKey('category', $categoryId, $tenantId, $limit);
            Cache::forget($key);
        }
    }

    /**
     * Invalidate all product caches for tenant
     */
    public static function invalidateAll(?int $tenantId = null): void
    {
        $pattern = self::buildKey('*', null, $tenantId);

        // Get all keys matching pattern
        $keys = [];
        $cursor = null;

        do {
            [$cursor, $foundKeys] = Redis::scan($cursor ?? 0, 'MATCH', $pattern, 'COUNT', 100);
            if (is_array($foundKeys)) { $keys = array_merge($keys, $foundKeys); }
        } while ($cursor !== 0 && $cursor !== null);

        // Delete all found keys
        if (!empty($keys)) {
            Cache::deleteMultiple($keys);
        }
    }

    /**
     * Format product for cache storage
     */
    protected static function formatProduct(ShopProduct $product): array
    {
        return [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $product->title,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'body' => $product->body,
            'base_price' => $product->base_price,
            'price_on_request' => $product->price_on_request,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'technical_specs' => $product->technical_specs,
            'features' => $product->features,
            'tags' => $product->tags,
            'is_active' => $product->is_active,
            'url' => url('/shop/' . (is_array($product->slug) ? ($product->slug['tr'] ?? $product->slug['en'] ?? '') : $product->slug)),
        ];
    }

    /**
     * Format product specifically for AI prompts (compact)
     */
    protected static function formatProductForAI(ShopProduct $product): array
    {
        $title = is_array($product->title) ? ($product->title['tr'] ?? $product->title['en'] ?? '') : $product->title;
        $slug = is_array($product->slug) ? ($product->slug['tr'] ?? $product->slug['en'] ?? '') : $product->slug;
        $shortDesc = is_array($product->short_description) ? ($product->short_description['tr'] ?? '') : $product->short_description;
        $body = is_array($product->body) ? ($product->body['tr'] ?? '') : $product->body;

        // Get first 300 chars of short desc, 500 chars of body
        $shortDescTrimmed = $shortDesc ? mb_substr(strip_tags($shortDesc), 0, 300) : '';
        $bodyTrimmed = $body ? mb_substr(strip_tags($body), 0, 500) : '';

        return [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $title,
            'slug' => $slug,
            'short_description' => $shortDescTrimmed,
            'description' => $bodyTrimmed,
            'technical_specs' => $product->technical_specs ?? [],
            'features' => $product->features ?? [],
            'tags' => $product->tags ?? [],
            'base_price' => $product->base_price,
            'price_on_request' => $product->price_on_request,
        ];
    }

    /**
     * Build cache key
     */
    protected static function buildKey(string $type, mixed $identifier = null, ?int $tenantId = null, mixed $extra = null): string
    {
        $tenantId = $tenantId ?? tenant('id') ?? 'global';

        $parts = [self::PREFIX, $tenantId, $type];

        if ($identifier !== null) {
            $parts[] = $identifier;
        }

        if ($extra !== null) {
            $parts[] = $extra;
        }

        return implode(':', $parts);
    }

    /**
     * Invalidate AI context caches containing this product
     */
    protected static function invalidateProductAICaches(int $productId, ?int $tenantId = null): void
    {
        $pattern = self::buildKey('ai_context', '*', $tenantId);

        $keys = [];
        $cursor = null;

        do {
            [$cursor, $foundKeys] = Redis::scan($cursor ?? 0, 'MATCH', $pattern, 'COUNT', 100);

            // Filter keys that contain this product ID
            if (is_array($foundKeys)) {
                foreach ($foundKeys as $key) {
                    if (str_contains($key, "_{$productId}_") || str_contains($key, "_{$productId}:")) {
                        $keys[] = $key;
                    }
                }
            }
        } while ($cursor !== 0 && $cursor !== null);

        if (!empty($keys)) {
            Cache::deleteMultiple($keys);
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(?int $tenantId = null): array
    {
        $pattern = self::buildKey('*', null, $tenantId);

        $keys = [];
        $cursor = null;

        do {
            [$cursor, $foundKeys] = Redis::scan($cursor ?? 0, 'MATCH', $pattern, 'COUNT', 100);
            if (is_array($foundKeys)) { $keys = array_merge($keys, $foundKeys); }
        } while ($cursor !== 0 && $cursor !== null);

        $stats = [
            'total_keys' => count($keys),
            'types' => [],
            'memory_usage' => 0,
        ];

        foreach ($keys as $key) {
            // Extract type from key
            $parts = explode(':', $key);
            $type = $parts[2] ?? 'unknown';

            if (!isset($stats['types'][$type])) {
                $stats['types'][$type] = 0;
            }

            $stats['types'][$type]++;

            // Estimate memory (rough)
            $stats['memory_usage'] += Redis::strlen($key);
        }

        return $stats;
    }

    /**
     * Warm up cache for popular products
     */
    public static function warmUp(?int $tenantId = null, int $limit = 100): int
    {
        $products = ShopProduct::where('is_active', true)
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();

        $warmed = 0;

        foreach ($products as $product) {
            self::getProduct($product->product_id, $tenantId);
            $warmed++;
        }

        return $warmed;
    }
}
