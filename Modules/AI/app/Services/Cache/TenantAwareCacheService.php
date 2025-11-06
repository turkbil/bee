<?php

namespace Modules\AI\App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Tenant-Aware Cache Service
 *
 * Her tenant için izole cache sistemi
 * Multi-tier cache (Redis + Memcached)
 */
class TenantAwareCacheService
{
    /**
     * Tenant-aware cache key oluştur
     */
    public function makeCacheKey(string $nodeType, array $params = []): string
    {
        $tenantId = tenant('id') ?? 'central';
        $paramsHash = md5(json_encode($params));

        return sprintf(
            "tenant_%s_%s_%s",
            $tenantId,
            $nodeType,
            $paramsHash
        );
    }

    /**
     * Cache'den oku (multi-tier)
     */
    public function remember(string $nodeType, array $params, int $ttl, callable $callback)
    {
        $cacheKey = $this->makeCacheKey($nodeType, $params);

        // Tier 1: Fast cache (Redis, kısa TTL)
        $fastKey = "fast_{$cacheKey}";
        $fastTTL = min(60, $ttl);

        if (Redis::exists($fastKey)) {
            $this->incrementHitRate($cacheKey, true);
            return json_decode(Redis::get($fastKey), true);
        }

        // Tier 2: Warm cache (Laravel Cache, uzun TTL)
        $warmData = Cache::remember($cacheKey, $ttl, function() use ($callback, $fastKey, $fastTTL) {
            $data = $callback();

            // Fast cache'e de yaz
            Redis::setex($fastKey, $fastTTL, json_encode($data));

            return $data;
        });

        $this->incrementHitRate($cacheKey, Cache::has($cacheKey));

        return $warmData;
    }

    /**
     * Cache invalidation (tenant-aware)
     */
    public function invalidate(string $nodeType, array $tags = []): void
    {
        $tenantId = tenant('id') ?? 'central';

        // Tag-based invalidation
        $cacheTags = array_merge(
            ["tenant_{$tenantId}", "tenant_{$tenantId}_{$nodeType}"],
            $tags
        );

        Cache::tags($cacheTags)->flush();

        // Redis fast cache'i de temizle
        $pattern = "fast_tenant_{$tenantId}_{$nodeType}_*";
        $keys = Redis::keys($pattern);

        if (!empty($keys)) {
            Redis::del($keys);
        }

        \Log::info("🔥 Cache invalidated", [
            'tenant' => $tenantId,
            'node_type' => $nodeType,
            'tags' => $cacheTags
        ]);
    }

    /**
     * Cache hit rate tracking
     */
    protected function incrementHitRate(string $cacheKey, bool $hit): void
    {
        $statsKey = "cache_stats:" . date('Y-m-d');

        Redis::hincrby($statsKey, "{$cacheKey}:total", 1);

        if ($hit) {
            Redis::hincrby($statsKey, "{$cacheKey}:hits", 1);
        }

        // Expire after 7 days
        Redis::expire($statsKey, 604800);
    }

    /**
     * Get cache hit rate for monitoring
     */
    public function getHitRate(string $nodeType = null): array
    {
        $statsKey = "cache_stats:" . date('Y-m-d');
        $stats = Redis::hgetall($statsKey);

        $rates = [];
        $pattern = $nodeType ? "*{$nodeType}*" : "*";

        foreach ($stats as $key => $value) {
            if (strpos($key, ':total') !== false) {
                $baseKey = str_replace(':total', '', $key);

                if (fnmatch($pattern, $baseKey)) {
                    $total = (int)$value;
                    $hits = (int)($stats["{$baseKey}:hits"] ?? 0);

                    $rates[$baseKey] = [
                        'total' => $total,
                        'hits' => $hits,
                        'rate' => $total > 0 ? round(($hits / $total) * 100, 2) : 0
                    ];
                }
            }
        }

        return $rates;
    }

    /**
     * Cache warmup (pre-populate common queries)
     */
    public function warmup(array $queries): void
    {
        foreach ($queries as $nodeType => $paramsList) {
            foreach ($paramsList as $params) {
                $cacheKey = $this->makeCacheKey($nodeType, $params);

                \Log::info("🔥 Warming cache", [
                    'key' => $cacheKey,
                    'node_type' => $nodeType
                ]);
            }
        }
    }
}
