<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Events\ModuleEnabled;
use App\Events\ModuleDisabled;

class ModuleRouteService
{
    /**
     * Cache TTL dakika cinsinden
     */
    protected const CACHE_TTL = 30; // 30 dakika
    
    /**
     * Tüm modüllerin dynamic route'larını otomatik yükle
     * 
     * @deprecated Use event-driven approach instead
     */
    public static function autoLoadModuleRoutes(): void
    {
        if (app()->environment(['local', 'staging'])) {
            Log::debug('ModuleRouteService: autoLoadModuleRoutes called (legacy method)');
        }
        
        try {
            $modules = \Module::allEnabled();
            
            foreach ($modules as $module) {
                $moduleName = $module->getLowerName();
                $modulePath = $module->getPath();
                
                // Event dispatch et - listener'lar route'ları yükleyecek
                ModuleEnabled::dispatch($moduleName, $modulePath);
            }
            
        } catch (\Exception $e) {
            Log::error('ModuleRouteService legacy load error', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Belirli modül için route'ları yükle
     */
    public static function loadModuleRoutes(string $moduleName): bool
    {
        try {
            $module = \Module::find($moduleName);
            if (!$module || !$module->isEnabled()) {
                return false;
            }
            
            $modulePath = $module->getPath();
            ModuleEnabled::dispatch($moduleName, $modulePath);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Module route loading error', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Modül route'larını kaldır
     */
    public static function unloadModuleRoutes(string $moduleName): bool
    {
        try {
            $module = \Module::find($moduleName);
            if (!$module) {
                return false;
            }
            
            $modulePath = $module->getPath();
            ModuleDisabled::dispatch($moduleName, $modulePath);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Module route unloading error', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Tenant-aware modül slug mapping
     */
    public static function getModuleSlug(string $moduleName, ?string $locale = null): ?string
    {
        $tenant = tenant();
        $locale = $locale ?? app()->getLocale();
        
        $cacheKey = self::generateSlugCacheKey($moduleName, $locale, $tenant?->id);
        $cacheTags = self::getSlugCacheTags($tenant?->id);
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($moduleName, $locale, $tenant) {
            return self::resolveModuleSlug($moduleName, $locale, $tenant);
        });
    }
    
    /**
     * Module route cache'ini temizle
     */
    public static function clearModuleRouteCache(?string $moduleName = null): void
    {
        if ($moduleName) {
            // Belirli modül cache'ini temizle
            $tags = [
                'module_routes',
                "module_routes:{$moduleName}"
            ];
            
            $tenant = tenant();
            if ($tenant) {
                $tags[] = "tenant_{$tenant->id}:module_routes";
                $tags[] = "tenant_{$tenant->id}:module_routes:{$moduleName}";
            }
            
            foreach ($tags as $tag) {
                Cache::tags($tag)->flush();
            }
        } else {
            // Tüm module route cache'ini temizle
            Cache::tags(['module_routes', 'dynamic_routes'])->flush();
        }
    }
    
    /**
     * Route cache ile uyumlu hale getir
     */
    public static function isRouteCacheCompatible(): bool
    {
        // Route cache aktifse ve production'daysa true
        return app()->routesAreCached() && app()->environment('production');
    }
    
    /**
     * Slug cache key oluştur
     */
    protected static function generateSlugCacheKey(string $moduleName, string $locale, ?string $tenantId): string
    {
        $tenantPart = $tenantId ? "tenant_{$tenantId}" : 'central';
        return "module_slug:{$tenantPart}:{$moduleName}:{$locale}";
    }
    
    /**
     * Slug cache tag'leri al
     */
    protected static function getSlugCacheTags(?string $tenantId): array
    {
        $tags = ['module_routes', 'dynamic_routes'];
        
        if ($tenantId) {
            $tags[] = "tenant_{$tenantId}:module_routes";
        }
        
        return $tags;
    }
    
    /**
     * Modül slug'ını çöz
     */
    protected static function resolveModuleSlug(string $moduleName, string $locale, ?object $tenant): ?string
    {
        // Bu metod ModuleSlugService'e delegate edilebilir
        // Şimdilik basit implementation
        
        try {
            // 1. Önce veritabanından slug mapping kontrol et
            if ($tenant && class_exists('\Modules\ModuleManagement\App\Models\ModuleSlug')) {
                $slug = \Modules\ModuleManagement\App\Models\ModuleSlug::where('module_name', $moduleName)
                    ->where('locale', $locale)
                    ->where('tenant_id', $tenant->id)
                    ->value('slug');
                    
                if ($slug) {
                    return $slug;
                }
            }
            
            // 2. Default slug: module name
            return strtolower($moduleName);
            
        } catch (\Exception $e) {
            Log::error('Module slug resolution error', [
                'module' => $moduleName,
                'locale' => $locale,
                'tenant_id' => $tenant?->id,
                'error' => $e->getMessage()
            ]);
            
            return strtolower($moduleName);
        }
    }
}