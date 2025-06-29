<?php

namespace App\Listeners;

use App\Events\ModuleEnabled;
use App\Events\ModuleDisabled;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class ClearModuleRouteCache
{
    /**
     * Handle module enabled event
     */
    public function handleModuleEnabled(ModuleEnabled $event): void
    {
        $this->clearRouteCaches($event->moduleName, 'enabled');
    }
    
    /**
     * Handle module disabled event
     */
    public function handleModuleDisabled(ModuleDisabled $event): void
    {
        $this->clearRouteCaches($event->moduleName, 'disabled');
    }
    
    /**
     * Clear module-related route caches
     */
    protected function clearRouteCaches(string $moduleName, string $action): void
    {
        try {
            // 1. Laravel route cache temizle
            if (app()->routesAreCached()) {
                Artisan::call('route:clear');
                
                if (app()->environment(['local', 'staging'])) {
                    Log::debug("Route cache cleared for module {$action}", [
                        'module' => $moduleName
                    ]);
                }
            }
            
            // 2. Module slug cache'ini temizle
            $this->clearModuleSlugCache($moduleName);
            
            // 3. Tenant-aware cache'leri temizle
            $this->clearTenantModuleCaches($moduleName);
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Module route caches cleared", [
                    'module' => $moduleName,
                    'action' => $action
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Module route cache clear error", [
                'module' => $moduleName,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear module slug cache
     */
    protected function clearModuleSlugCache(string $moduleName): void
    {
        // Redis pattern-based cache clearing
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            
            // Module slug pattern'leri
            $patterns = [
                "module_slug:*:{$moduleName}:*",
                "tenant_*:module_route:*:{$moduleName}:*",
                "*:dynamic_route:*:{$moduleName}:*"
            ];
            
            foreach ($patterns as $pattern) {
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } else {
            // Tag-based cache clearing
            $tags = [
                'module_routes',
                "module_routes:{$moduleName}",
                'dynamic_routes'
            ];
            
            foreach ($tags as $tag) {
                Cache::tags($tag)->flush();
            }
        }
    }
    
    /**
     * Clear tenant-specific module caches
     */
    protected function clearTenantModuleCaches(string $moduleName): void
    {
        $tenant = tenant();
        if (!$tenant) {
            return;
        }
        
        $tenantTags = [
            "tenant_{$tenant->id}:module_routes",
            "tenant_{$tenant->id}:module_routes:{$moduleName}"
        ];
        
        foreach ($tenantTags as $tag) {
            Cache::tags($tag)->flush();
        }
    }
}