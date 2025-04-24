<?php

namespace Modules\WidgetManagement\app\Services\Widget;

use Illuminate\Support\Facades\Cache;

class WidgetCacheService
{
    protected $cachePrefix;
    
    public function __construct($cachePrefix = 'widget_')
    {
        $this->cachePrefix = $cachePrefix;
    }
    
    public function clearCache($tenantId = null, $widgetId = null): void
    {
        if ($widgetId) {
            if ($tenantId) {
                Cache::forget($this->cachePrefix . $tenantId . "_widget_{$widgetId}");
            } else {
                Cache::forget($this->cachePrefix . "central_widget_{$widgetId}");
            }
        } else {
            if ($tenantId) {
                $keys = Cache::get($this->cachePrefix . "keys_{$tenantId}", []);
                
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                
                Cache::forget($this->cachePrefix . "keys_{$tenantId}");
            } else {
                $globalKeys = Cache::get($this->cachePrefix . "global_keys", []);
                
                foreach ($globalKeys as $key) {
                    Cache::forget($key);
                }
                
                Cache::forget($this->cachePrefix . "global_keys");
            }
        }
    }
    
    public function cacheWidgetData($key, $data, $duration = null)
    {
        $duration = $duration ?? config('cache.ttl', 60);
        
        Cache::put($key, $data, $duration);
        
        return $data;
    }
    
    public function getCachedWidgetData($key, $callback = null, $duration = null)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        
        if ($callback) {
            $data = $callback();
            return $this->cacheWidgetData($key, $data, $duration);
        }
        
        return null;
    }
    
    public function registerCacheKey($tenantId, $key)
    {
        $cacheKey = $this->cachePrefix . "keys_{$tenantId}";
        $keys = Cache::get($cacheKey, []);
        $keys[] = $key;
        
        Cache::put($cacheKey, array_unique($keys));
    }
}