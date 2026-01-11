<?php

namespace Modules\TenantManagement\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CacheOperationTracker
{
    private static $instance;
    private $operations = [];
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Track cache operation
     */
    public function trackOperation($operation, $key, $tenant_id = null)
    {
        $tenant_id = $tenant_id ?: $this->getCurrentTenantId();
        
        $this->operations[] = [
            'tenant_id' => $tenant_id,
            'operation' => $operation, // get, put, forget, flush, etc.
            'key' => $key,
            'timestamp' => microtime(true),
            'hour' => Carbon::now()->format('Y-m-d-H')
        ];
        
        // Update counters immediately
        $this->updateCounters($tenant_id, $operation);
    }
    
    /**
     * Update cache operation counters
     */
    private function updateCounters($tenant_id, $operation)
    {
        $hour = Carbon::now()->format('Y-m-d-H');
        $counterKey = "cache_ops_{$tenant_id}_{$hour}";
        
        $current = Cache::get($counterKey, 0);
        Cache::put($counterKey, $current + 1, 3600);
    }
    
    /**
     * Get real cache statistics
     */
    public function getRealCacheStats($tenant_id = null)
    {
        try {
            $redis = Redis::connection();
            
            // Get Redis info
            $info = $redis->info();
            
            return [
                'total_keys' => $redis->dbsize(),
                'memory_usage' => $info['used_memory'] ?? 0,
                'memory_usage_human' => $info['used_memory_human'] ?? '0B',
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
                'connected_clients' => $info['connected_clients'] ?? 0,
                'expired_keys' => $info['expired_keys'] ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'total_keys' => 0,
                'memory_usage' => 0,
                'memory_usage_human' => '0B',
                'hits' => 0,
                'misses' => 0,
                'hit_rate' => 0,
                'connected_clients' => 0,
                'expired_keys' => 0
            ];
        }
    }
    
    /**
     * Get tenant-specific cache keys
     */
    public function getTenantCacheKeys($tenant_id, $pattern = '*')
    {
        try {
            $redis = Redis::connection();
            $tenantPattern = "*tenant_{$tenant_id}_*";
            
            if ($pattern !== '*') {
                $tenantPattern = "*tenant_{$tenant_id}_{$pattern}*";
            }
            
            $keys = $redis->keys($tenantPattern);
            $result = [];
            
            foreach ($keys as $key) {
                try {
                    $ttl = $redis->ttl($key);
                    $type = $redis->type($key);
                    $size = strlen($redis->get($key) ?: '');
                    
                    $result[] = [
                        'key' => str_replace("tenant_{$tenant_id}_", '', $key),
                        'full_key' => $key,
                        'type' => $type,
                        'ttl' => $ttl,
                        'size' => $size,
                        'expires_at' => $ttl > 0 ? Carbon::now()->addSeconds($ttl)->toDateTimeString() : 'Never'
                    ];
                } catch (\Exception $e) {
                    // Key might have expired, skip
                    continue;
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Clear tenant cache
     */
    public function clearTenantCache($tenant_id, $pattern = '*')
    {
        try {
            $redis = Redis::connection();
            $tenantPattern = "*tenant_{$tenant_id}_{$pattern}*";
            
            if ($pattern === '*') {
                $tenantPattern = "*tenant_{$tenant_id}_*";
            }
            
            $keys = $redis->keys($tenantPattern);
            $cleared = 0;
            
            foreach ($keys as $key) {
                if ($redis->del($key)) {
                    $cleared++;
                }
            }
            
            $this->trackOperation('clear_bulk', $pattern, $tenant_id);
            
            return $cleared;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get cache operations for tenant
     */
    public function getTenantOperations($tenant_id, $hours = 24)
    {
        $operations = [];
        $startHour = Carbon::now()->subHours($hours);
        
        for ($i = 0; $i < $hours; $i++) {
            $hour = $startHour->copy()->addHours($i)->format('Y-m-d-H');
            $counterKey = "cache_ops_{$tenant_id}_{$hour}";
            $count = Cache::get($counterKey, 0);
            
            $operations[] = [
                'hour' => $hour,
                'operations' => $count
            ];
        }
        
        return $operations;
    }
    
    /**
     * Calculate hit rate percentage
     */
    private function calculateHitRate($hits, $misses)
    {
        $total = $hits + $misses;
        if ($total === 0) {
            return 0;
        }
        
        return round(($hits / $total) * 100, 2);
    }
    
    /**
     * Get current tenant ID
     */
    private function getCurrentTenantId()
    {
        if (function_exists('tenant') && tenant()) {
            return tenant()->id;
        }
        
        // Fallback
        return \App\Models\Tenant::where('is_active', true)->first()?->id ?? 1;
    }
}

// Global cache operation tracker
if (!function_exists('trackCacheOperation')) {
    function trackCacheOperation($operation, $key, $tenant_id = null)
    {
        try {
            \Modules\TenantManagement\App\Services\CacheOperationTracker::getInstance()
                ->trackOperation($operation, $key, $tenant_id);
        } catch (\Exception $e) {
            // Silent fail - don't break cache operations
        }
    }
}