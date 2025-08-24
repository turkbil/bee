<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisClusterService;

class TenantCacheManager
{
    protected string $tenantPrefix;
    protected int $defaultTtl = 3600; // 1 saat
    protected $clusterService;

    public function __construct()
    {
        $this->tenantPrefix = $this->getTenantPrefix();
        $this->clusterService = app(RedisClusterService::class);
    }

    /**
     * Cache key oluştur
     */
    public function generateKey(string $prefix, ...$segments): string
    {
        $segments = array_filter($segments, function ($segment) {
            return $segment !== null && $segment !== '';
        });
        
        $key = $this->tenantPrefix . ':' . $prefix;
        
        if (!empty($segments)) {
            $key .= ':' . implode(':', array_map(function ($segment) {
                if (is_array($segment)) {
                    return md5(json_encode($segment));
                }
                return (string) $segment;
            }, $segments));
        }
        
        return $key;
    }

    /**
     * Cache'e veri kaydet
     */
    public function remember(string $key, \Closure $callback, int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $tags = $this->getCacheTags();
        
        if ($this->supportsTagging()) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Cache'den veri al
     */
    public function get(string $key, $default = null): mixed
    {
        $tags = $this->getCacheTags();
        
        if ($this->supportsTagging()) {
            return Cache::tags($tags)->get($key, $default);
        }
        
        return Cache::get($key, $default);
    }

    /**
     * Cache'e veri koy
     */
    public function put(string $key, mixed $value, int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $tags = $this->getCacheTags();
        
        if ($this->supportsTagging()) {
            return Cache::tags($tags)->put($key, $value, $ttl);
        }
        
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Cache'den sil
     */
    public function forget(string $key): bool
    {
        $tags = $this->getCacheTags();
        
        if ($this->supportsTagging()) {
            return Cache::tags($tags)->forget($key);
        }
        
        return Cache::forget($key);
    }

    /**
     * Pattern ile cache temizle
     */
    public function forgetPattern(string $pattern): void
    {
        if ($this->supportsTagging()) {
            // Redis ile pattern matching
            $fullPattern = $this->tenantPrefix . ':' . $pattern;
            $this->forgetByPattern($fullPattern);
        } else {
            // Fallback: tüm cache'i temizle
            Cache::flush();
        }
    }

    /**
     * Belirli tag'leri temizle
     */
    public function flushTags(array $tags = null): void
    {
        $tags = $tags ?? $this->getCacheTags();
        
        if ($this->supportsTagging()) {
            Cache::tags($tags)->flush();
        } else {
            Cache::flush();
        }
    }

    /**
     * Tenant prefix oluştur
     */
    protected function getTenantPrefix(): string
    {
        if (function_exists('tenant') && tenant()) {
            return 'tenant_' . tenant('id');
        }
        
        return 'default';
    }

    /**
     * Cache tag'leri getir
     */
    protected function getCacheTags(): array
    {
        return [
            $this->tenantPrefix,
            $this->tenantPrefix . '_data'
        ];
    }

    /**
     * Cache driver tag desteği var mı?
     */
    protected function supportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached']);
    }

    /**
     * Redis ile pattern matching (Cluster desteği ile)
     */
    protected function forgetByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                // Redis clustering aktif mi?
                if (config('redis_cluster.clustering.enabled')) {
                    $tenantKey = $this->extractTenantKey();
                    $this->clusterService->clearTenantCache($tenantKey);
                } else {
                    // Single Redis
                    $redis = Redis::connection(config('cache.stores.redis.connection'));
                    $keys = $redis->keys($pattern);
                    
                    if (!empty($keys)) {
                        $redis->del($keys);
                    }
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda log'la ama işlemi durdurma
            logger()->warning('Cache pattern delete failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tenant key'ini prefix'ten çıkar
     */
    protected function extractTenantKey(): string
    {
        return str_replace('tenant_', '', $this->tenantPrefix);
    }

    /**
     * Cache istatistikleri
     */
    public function getStats(): array
    {
        return [
            'tenant_prefix' => $this->tenantPrefix,
            'default_ttl' => $this->defaultTtl,
            'cache_driver' => config('cache.default'),
            'supports_tagging' => $this->supportsTagging(),
            'cache_tags' => $this->getCacheTags()
        ];
    }

    /**
     * Cache'i temizle
     */
    public function clear(): void
    {
        $this->flushTags();
    }
}