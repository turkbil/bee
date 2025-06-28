<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModuleRouteService
{
    /**
     * Tüm modüllerin dynamic route'larını otomatik yükle
     */
    public static function autoLoadModuleRoutes()
    {
        Log::info('🚀 ModuleRouteService: Otomatik modül route yükleme başladı');
        
        try {
            $modules = \Module::allEnabled();
            $loadedModules = [];
            
            foreach ($modules as $module) {
                $moduleName = $module->getLowerName();
                $dynamicRoutePath = $module->getPath() . '/routes/dynamic.php';
                
                if (file_exists($dynamicRoutePath)) {
                    // Modül context'ini ayarla
                    app()->instance('current.module', $module);
                    app()->instance('current.module.name', $moduleName);
                    
                    // Route dosyasını yükle
                    require $dynamicRoutePath;
                    
                    $loadedModules[] = $moduleName;
                    Log::info("✅ {$moduleName} modülü dynamic route'ları yüklendi");
                } else {
                    Log::warning("⚠️ {$moduleName} modülü için dynamic.php bulunamadı: {$dynamicRoutePath}");
                }
            }
            
            Log::info('🎯 ModuleRouteService: Tamamlandı', [
                'loaded_modules' => $loadedModules,
                'total_count' => count($loadedModules)
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ ModuleRouteService hatası:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Tenant-aware modül slug mapping
     */
    public static function getModuleSlug($module, $locale = null)
    {
        $tenant = tenant();
        $locale = $locale ?? app()->getLocale();
        
        if (!$tenant) {
            return self::getDefaultSlug($module, $locale);
        }
        
        // Cache key: tenant_123:module_slugs:page:tr
        $cacheKey = "tenant_{$tenant->id}:module_slugs:{$module}:{$locale}";
        
        return Cache::remember($cacheKey, 3600, function () use ($module, $locale, $tenant) {
            try {
                // Şimdilik basit fallback - config'den al
                $customSlug = null;
                
                if ($customSlug) {
                    Log::info("📋 Tenant slug bulundu", [
                        'tenant' => $tenant->id,
                        'module' => $module,
                        'locale' => $locale,
                        'slug' => $customSlug
                    ]);
                    return $customSlug;
                }
                
            } catch (\Exception $e) {
                Log::warning("⚠️ Tenant slug alınamadı: " . $e->getMessage());
            }
            
            // Varsayılan slug döndür
            return self::getDefaultSlug($module, $locale);
        });
    }
    
    /**
     * Varsayılan modül slug'ını al
     */
    public static function getDefaultSlug($module, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $module = strtolower($module);
        
        // Varsayılan slug mapping'leri
        $defaultSlugs = [
            'page' => [
                'tr' => 'sayfa',
                'en' => 'page',
                'ar' => 'safha'
            ],
            'portfolio' => [
                'tr' => 'referanslar',
                'en' => 'portfolio',
                'ar' => 'mahfaza'
            ],
            'announcement' => [
                'tr' => 'duyurular',
                'en' => 'announcements',
                'ar' => 'elanlar'
            ]
        ];
        
        $slug = $defaultSlugs[$module][$locale] ?? $module;
        
        Log::info("🎯 Varsayılan slug kullanıldı", [
            'module' => $module,
            'locale' => $locale,
            'slug' => $slug
        ]);
        
        return $slug;
    }
    
    /**
     * Modül route cache'ini temizle
     */
    public static function clearModuleRouteCache()
    {
        $tenant = tenant();
        if ($tenant) {
            $pattern = "tenant_{$tenant->id}:module_slugs:*";
            Cache::forget($pattern);
        }
        
        Log::info('🧹 Modül route cache temizlendi');
    }
    
    /**
     * Tüm aktif modülleri listele
     */
    public static function getActiveModules()
    {
        try {
            return \Module::allEnabled()->mapWithKeys(function ($module) {
                return [$module->getLowerName() => $module->getName()];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('❌ Aktif modüller alınamadı: ' . $e->getMessage());
            return [];
        }
    }
}