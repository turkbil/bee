<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class RedisTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $tenantKey = $tenant->getTenantKey();
        
        // Redis Cluster kullanıyorsak
        if (config('redis_cluster.clustering.enabled')) {
            $this->setupClusterRedis($tenantKey);
        } else {
            $this->setupSingleRedis($tenantKey);
        }
    }
    
    /**
     * Single Redis instance setup
     */
    protected function setupSingleRedis($tenantKey)
    {
        // Standart prefix formatı: tenant:{tenant_id}
        $prefix = 'tenant:' . $tenantKey;
        
        // Redis prefix'lerini tenant bazında ayarla
        Config::set([
            'database.redis.options.prefix' => $prefix . ':',
            'cache.prefix' => $prefix,
        ]);
        
        // Farklı Redis connection'ları için prefix
        $connections = ['default', 'cache', 'session', 'queue'];
        
        foreach ($connections as $connection) {
            if (config("database.redis.{$connection}")) {
                Config::set([
                    "database.redis.{$connection}.options.prefix" => $prefix . ':' . $connection . ':',
                ]);
            }
        }
        
        // Cache store'ları için prefix
        $cacheStores = ['redis', 'tenant', 'file'];
        
        foreach ($cacheStores as $store) {
            if (config("cache.stores.{$store}")) {
                if ($store === 'redis' || $store === 'tenant') {
                    Config::set([
                        "cache.stores.{$store}.prefix" => $prefix . ':cache',
                    ]);
                } elseif ($store === 'file') {
                    Config::set([
                        "cache.stores.file.path" => storage_path('framework/cache/data/' . $prefix),
                    ]);
                }
            }
        }
        
        // Session Redis connection prefix
        if (config('session.driver') === 'redis') {
            Config::set([
                'session.connection' => 'default',
                'session.cookie' => 'laravel_session_' . $tenantKey,
                'database.redis.default.options.prefix' => $prefix . ':session:',
            ]);
        }
        
        // Broadcast Redis prefix
        if (config('broadcasting.default') === 'redis') {
            Config::set([
                'broadcasting.connections.redis.options.prefix' => $prefix . ':broadcast:',
            ]);
        }
        
        // Queue Redis prefix
        if (config('queue.default') === 'redis') {
            Config::set([
                'queue.connections.redis.queue' => $prefix . ':queue',
            ]);
        }
    }
    
    /**
     * Redis Cluster setup
     */
    protected function setupClusterRedis($tenantKey)
    {
        // Standart prefix formatı: tenant:{tenant_id}
        $prefix = 'tenant:' . $tenantKey;
        
        // Cluster service'i kullan
        if (app()->has(\App\Services\RedisClusterService::class)) {
            $clusterService = app(\App\Services\RedisClusterService::class);
            
            // Tenant için doğru cluster'ı belirle
            $cluster = $clusterService->getClusterForTenant($tenantKey);
            
            // Cluster connection'ını Laravel Redis config'ine ekle
            $clusterConfig = [
                'cluster' => true,
                'options' => [
                    'cluster' => 'redis',
                    'prefix' => $prefix . ':',
                ],
            ];
            
            Config::set([
                'database.redis.tenant_cluster' => $clusterConfig,
                'cache.stores.redis.connection' => 'tenant_cluster',
            ]);
        }
        
        // Fallback to single Redis setup
        $this->setupSingleRedis($tenantKey);
    }

    public function revert()
    {
        // Orijinal Redis ayarlarına geri dön
        Config::set([
            'database.redis.options.prefix' => env('REDIS_PREFIX', ''),
            'cache.prefix' => env('CACHE_PREFIX', ''),
        ]);
        
        // Connection'ları temizle
        $connections = ['default', 'cache', 'session', 'queue'];
        
        foreach ($connections as $connection) {
            if (config("database.redis.{$connection}")) {
                Config::set([
                    "database.redis.{$connection}.options.prefix" => env('REDIS_PREFIX', ''),
                ]);
            }
        }
        
        // Cache store prefix'lerini temizle
        Config::set([
            'cache.stores.redis.prefix' => env('CACHE_PREFIX', ''),
            'cache.stores.file.path' => storage_path('framework/cache/data'),
        ]);
        
        // Session ve broadcast prefix'lerini temizle
        if (config('session.driver') === 'redis') {
            Config::set([
                'database.redis.default.options.prefix' => env('REDIS_PREFIX', ''),
            ]);
        }
        
        if (config('broadcasting.default') === 'redis') {
            Config::set([
                'broadcasting.connections.redis.options.prefix' => env('REDIS_PREFIX', ''),
            ]);
        }
    }
}