<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\ResponseCache\Facades\ResponseCache;

class CacheManager 
{
    /**
     * Dil değişikliği ve kullanıcı girişi sonrası tüm ilgili cache'leri temizle
     */
    public static function clearAllLanguageRelatedCaches(): void 
    {
        Log::info('CacheManager: Starting comprehensive cache clear');
        
        try {
            // 1. Laravel Framework Cache'leri
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            // 2. Custom Service Cache'leri
            if (class_exists('\App\Services\ModuleSlugService')) {
                \App\Services\ModuleSlugService::clearCache();
            }
            
            if (class_exists('\App\Services\DynamicRouteResolver')) {
                app(\App\Contracts\DynamicRouteResolverInterface::class)->clearRouteCache();
            }
            
            if (class_exists('\Modules\LanguageManagement\app\Services\UrlPrefixService')) {
                \Modules\LanguageManagement\app\Services\UrlPrefixService::clearCache();
            }
            
            // 3. Language Helper Cache'leri
            if (function_exists('clearLanguageRegexCache')) {
                clearLanguageRegexCache();
            }
            
            // Language switcher cache'leri
            Cache::forget('admin_languages_switcher');
            Cache::forget('tenant_languages_switcher');
            Cache::forget('supported_language_regex');
            
            // 4. Module slug cache'leri
            Cache::forget('module_slug_service_global_settings');
            Cache::forget('module_tenant_settings_table_empty');
            Cache::forget('module_config_Page');
            Cache::forget('module_config_Portfolio');
            Cache::forget('module_config_Announcement');
            
            // 5. Dynamic route cache'leri
            Cache::forget('dynamic_route_module_map');
            Cache::tags(['dynamic_routes'])->flush();
            
            // Tenant specific cache tags
            if (tenant()) {
                $tenantId = tenant()->id;
                Cache::tags(["tenant_{$tenantId}:dynamic_routes"])->flush();
                Cache::forget("tenant_{$tenantId}_response_cache");
            } else {
                Cache::forget('central_response_cache');
            }
            
            // 6. Response Cache (Spatie)
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                ResponseCache::clear();
            }
            
            // 7. SEO Cache'leri
            if (class_exists('\App\Services\SeoCacheService') && method_exists('\App\Services\SeoCacheService', 'clearAllCache')) {
                \App\Services\SeoCacheService::clearAllCache();
            } elseif (class_exists('\App\Services\SeoCacheService') && method_exists('\App\Services\SeoCacheService', 'clearAll')) {
                \App\Services\SeoCacheService::clearAll();
            }
            
            // 8. Widget Cache'leri  
            if (class_exists('\Modules\WidgetManagement\app\Services\Widget\WidgetCacheService')) {
                if (method_exists('\Modules\WidgetManagement\app\Services\Widget\WidgetCacheService', 'clearAll')) {
                    \Modules\WidgetManagement\app\Services\Widget\WidgetCacheService::clearAll();
                } elseif (method_exists('\Modules\WidgetManagement\app\Services\Widget\WidgetCacheService', 'clear')) {
                    \Modules\WidgetManagement\app\Services\Widget\WidgetCacheService::clear();
                }
            }
            
            // 9. OPcache Reset (if available)
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // 10. Session Flash Message
            session()->flash('cache_cleared', true);
            session()->flash('cache_clear_time', now()->toDateTimeString());
            
            Log::info('CacheManager: All caches cleared successfully');
            
        } catch (\Exception $e) {
            Log::error('CacheManager: Error clearing caches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sadece tenant-specific cache'leri temizle
     */
    public static function clearTenantCaches(): void 
    {
        if (!tenant()) {
            return;
        }
        
        $tenantId = tenant()->id;
        
        try {
            // Tenant specific tags
            Cache::tags(["tenant_{$tenantId}"])->flush();
            Cache::tags(["tenant_{$tenantId}:dynamic_routes"])->flush();
            Cache::forget("tenant_{$tenantId}_response_cache");
            
            // Module slug cache for this tenant
            Cache::forget("module_slug_service_tenant_{$tenantId}");
            
            Log::info("CacheManager: Tenant {$tenantId} caches cleared");
            
        } catch (\Exception $e) {
            Log::error("CacheManager: Error clearing tenant caches", [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Sadece dil ile ilgili cache'leri temizle
     */
    public static function clearLanguageCaches(): void 
    {
        try {
            // Language specific caches
            Cache::forget('admin_languages_switcher');
            Cache::forget('tenant_languages_switcher');
            Cache::forget('supported_language_regex');
            
            // URL prefix cache
            if (class_exists('\Modules\LanguageManagement\app\Services\UrlPrefixService')) {
                \Modules\LanguageManagement\app\Services\UrlPrefixService::clearCache();
            }
            
            // View cache (language files)
            Artisan::call('view:clear');
            
            Log::info('CacheManager: Language caches cleared');
            
        } catch (\Exception $e) {
            Log::error('CacheManager: Error clearing language caches', [
                'error' => $e->getMessage()
            ]);
        }
    }
}