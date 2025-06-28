<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

if (!class_exists('CacheHelper')) {
    class CacheHelper
    {
        // Standart TTL sabitleri
        public const TTL_SHORT = 300;      // 5 dakika
        public const TTL_MEDIUM = 1800;    // 30 dakika
        public const TTL_LONG = 3600;      // 1 saat  
        public const TTL_DAILY = 86400;    // 24 saat
        public const TTL_WEEKLY = 604800;  // 7 gün
        
        /**
         * Tenant-aware cache key oluştur
         */
        public static function key(string $key, string $type = 'general'): string
        {
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->id : 'central';
            
            return "{$tenantId}:{$type}:{$key}";
        }
        
        /**
         * Cache tag'leri oluştur
         */
        public static function tags(array $additionalTags = []): array
        {
            $tenant = tenant();
            $baseTags = ['tenant:' . ($tenant?->id ?? 'central')];
            
            return array_merge($baseTags, $additionalTags);
        }
        
        /**
         * Dil bazlı cache key
         */
        public static function languageKey(string $key, string $locale = null): string
        {
            $locale = $locale ?? app()->getLocale();
            return self::key("{$locale}:{$key}", 'language');
        }
        
        /**
         * Modül bazlı cache key
         */
        public static function moduleKey(string $module, string $key): string
        {
            return self::key("{$module}:{$key}", 'module');
        }
        
        /**
         * Route bazlı cache key
         */
        public static function routeKey(string $route, array $parameters = []): string
        {
            $paramString = !empty($parameters) ? ':' . implode(':', $parameters) : '';
            return self::key("{$route}{$paramString}", 'route');
        }
        
        /**
         * User bazlı cache key
         */
        public static function userKey(string $key, $userId = null): string
        {
            $userId = $userId ?? (auth()->check() ? auth()->id() : 'guest');
            return self::key("user:{$userId}:{$key}", 'user');
        }
        
        /**
         * Remember with tenant awareness
         */
        public static function remember(string $key, $ttl, callable $callback, string $type = 'general')
        {
            $cacheKey = self::key($key, $type);
            
            return Cache::remember($cacheKey, $ttl, function() use ($callback, $key, $type) {
                Log::info("Cache miss: {$key} (type: {$type})");
                return $callback();
            });
        }
        
        /**
         * Forget tenant-aware cache
         */
        public static function forget(string $key, string $type = 'general'): bool
        {
            $cacheKey = self::key($key, $type);
            return Cache::forget($cacheKey);
        }
        
        /**
         * Flush all cache for current tenant
         */
        public static function flushTenantCache(): void
        {
            $tenant = tenant();
            if ($tenant) {
                $pattern = "{$tenant->id}:*";
                
                // Pattern-based cache clearing (Redis için)
                try {
                    if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                        $redis = Cache::getStore()->getRedis();
                        $keys = $redis->keys($pattern);
                        
                        if (!empty($keys)) {
                            $redis->del($keys);
                            Log::info("Tenant cache temizlendi", [
                                'tenant_id' => $tenant->id,
                                'keys_deleted' => count($keys)
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Tenant cache temizleme hatası: " . $e->getMessage());
                }
            }
        }
        
        /**
         * Cache statistics
         */
        public static function getStats(): array
        {
            $tenant = tenant();
            $tenantId = $tenant?->id ?? 'central';
            
            return [
                'tenant_id' => $tenantId,
                'cache_prefix' => "{$tenantId}:",
                'available_types' => ['general', 'language', 'module', 'route', 'user'],
                'cache_driver' => config('cache.default')
            ];
        }
    }
}

// Global helper functions
if (!function_exists('cache_key')) {
    /**
     * Generate tenant-aware cache key
     */
    function cache_key(string $key, string $type = 'general'): string
    {
        return CacheHelper::key($key, $type);
    }
}

if (!function_exists('cache_remember_tenant')) {
    /**
     * Remember with tenant awareness
     */
    function cache_remember_tenant(string $key, $ttl, callable $callback, string $type = 'general')
    {
        return CacheHelper::remember($key, $ttl, $callback, $type);
    }
}

if (!function_exists('cache_forget_tenant')) {
    /**
     * Forget tenant-aware cache
     */
    function cache_forget_tenant(string $key, string $type = 'general'): bool
    {
        return CacheHelper::forget($key, $type);
    }
}