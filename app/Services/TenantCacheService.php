<?php

namespace App\Services;

use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class TenantCacheService
{
    /**
     * Tenant ID'ye göre önbellek anahtarı oluştur
     *
     * @param string $key
     * @param int|null $tenantId
     * @return string
     */
    public function getTenantCacheKey(string $key, ?int $tenantId = null): string
    {
        if ($tenantId === null && TenantHelpers::isTenant()) {
            $tenantId = tenant_id();
        }
        
        if ($tenantId) {
            return "tenant_{$tenantId}_" . $key;
        }
        
        return $key;
    }
    
    /**
     * Tenant önbelleğine veri ekle
     *
     * @param string $key
     * @param mixed $value
     * @param int|\DateTimeInterface|\DateInterval|null $ttl
     * @param int|null $tenantId
     * @return bool
     */
    public function put(string $key, $value, $ttl = null, ?int $tenantId = null): bool
    {
        $cacheKey = $this->getTenantCacheKey($key, $tenantId);
        return Cache::put($cacheKey, $value, $ttl);
    }
    
    /**
     * Tenant önbelleğinden veri al
     *
     * @param string $key
     * @param mixed $default
     * @param int|null $tenantId
     * @return mixed
     */
    public function get(string $key, $default = null, ?int $tenantId = null)
    {
        $cacheKey = $this->getTenantCacheKey($key, $tenantId);
        return Cache::get($cacheKey, $default);
    }
    
    /**
     * Tenant önbelleğinde anahtar var mı kontrol et
     *
     * @param string $key
     * @param int|null $tenantId
     * @return bool
     */
    public function has(string $key, ?int $tenantId = null): bool
    {
        $cacheKey = $this->getTenantCacheKey($key, $tenantId);
        return Cache::has($cacheKey);
    }
    
    /**
     * Tenant önbelleğinden veri sil
     *
     * @param string $key
     * @param int|null $tenantId
     * @return bool
     */
    public function forget(string $key, ?int $tenantId = null): bool
    {
        $cacheKey = $this->getTenantCacheKey($key, $tenantId);
        return Cache::forget($cacheKey);
    }
    
    /**
     * Tenant önbelleğini temizle
     *
     * @param int|null $tenantId
     * @return bool
     */
    public function flush(?int $tenantId = null): bool
    {
        if ($tenantId === null && TenantHelpers::isTenant()) {
            $tenantId = tenant_id();
        }
        
        if ($tenantId) {
            // Belirli bir tenant'ın önbelleğini temizle
            $prefix = "tenant_{$tenantId}_*";
            $keys = Redis::keys($prefix);
            
            if (!empty($keys)) {
                return Redis::del($keys) > 0;
            }
            
            return true;
        }
        
        // Tüm önbelleği temizle
        return Cache::flush();
    }
    
    /**
     * Önbelleği anımsa (remember) metodu
     *
     * @param string $key
     * @param \DateTimeInterface|\DateInterval|int $ttl
     * @param \Closure $callback
     * @param int|null $tenantId
     * @return mixed
     */
    public function remember(string $key, $ttl, \Closure $callback, ?int $tenantId = null)
    {
        $cacheKey = $this->getTenantCacheKey($key, $tenantId);
        return Cache::remember($cacheKey, $ttl, $callback);
    }
    
    /**
     * Tenant önbelleğini etiketleme (tag)
     *
     * @param string|array $tags
     * @param int|null $tenantId
     * @return \Illuminate\Cache\TaggedCache
     */
    public function tag($tags, ?int $tenantId = null)
    {
        if ($tenantId === null && TenantHelpers::isTenant()) {
            $tenantId = tenant_id();
        }
        
        if (is_string($tags)) {
            $tags = [$tags];
        }
        
        if ($tenantId) {
            // Etiketlere tenant ID'sini ekle
            $tenantTags = array_map(function ($tag) use ($tenantId) {
                return "tenant_{$tenantId}_{$tag}";
            }, $tags);
            
            return Cache::tags($tenantTags);
        }
        
        return Cache::tags($tags);
    }
}