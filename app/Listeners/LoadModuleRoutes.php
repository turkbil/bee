<?php

namespace App\Listeners;

use App\Events\ModuleEnabled;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LoadModuleRoutes
{
    /**
     * Handle module enabled event
     */
    public function handle(ModuleEnabled $event): void
    {
        $this->loadModuleDynamicRoutes($event->moduleName, $event->modulePath);
    }
    
    /**
     * Load dynamic routes for specific module
     */
    protected function loadModuleDynamicRoutes(string $moduleName, string $modulePath): void
    {
        try {
            $dynamicRoutePath = $modulePath . '/routes/dynamic.php';
            
            if (file_exists($dynamicRoutePath)) {
                // Modül context'ini ayarla
                app()->instance('current.module.name', $moduleName);
                
                // Route dosyasını yükle
                require $dynamicRoutePath;
                
                // Cache module as loaded
                $this->cacheModuleAsLoaded($moduleName);
                
                if (app()->environment(['local', 'staging'])) {
                    Log::debug("Module dynamic routes loaded", [
                        'module' => $moduleName,
                        'route_file' => $dynamicRoutePath
                    ]);
                }
            } else {
                if (app()->environment(['local', 'staging'])) {
                    Log::debug("No dynamic routes file found for module", [
                        'module' => $moduleName,
                        'expected_path' => $dynamicRoutePath
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Module route loading error", [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Cache module as loaded
     */
    protected function cacheModuleAsLoaded(string $moduleName): void
    {
        $cacheKey = "module_routes_loaded:{$moduleName}";
        $cacheTags = ['module_routes', "module_routes:{$moduleName}"];
        
        Cache::tags($cacheTags)->put($cacheKey, true, now()->addHours(24));
    }
}