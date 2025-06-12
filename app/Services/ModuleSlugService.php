<?php

namespace App\Services;

use App\Models\ModuleTenantSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModuleSlugService
{
    // Global cache key'ler
    private static $globalCacheKey = 'module_slug_service_global_settings';
    private static $tableEmptyCacheKey = 'module_tenant_settings_table_empty';
    
    // Memory cache - tek request içinde performans için
    private static $memoryCache = [];
    private static $globalSettingsLoaded = false;
    private static $tableIsEmpty = null;
    private static $configCache = []; // Config dosyaları için memory cache
    
    public static function getSlug(string $moduleName, string $slugKey): string
    {
        // Memory cache kontrolü
        $memoryCacheKey = $moduleName . '.' . $slugKey;
        if (isset(self::$memoryCache[$memoryCacheKey])) {
            return self::$memoryCache[$memoryCacheKey];
        }
        
        // Global ayarları yükle (eğer yüklenmediyse)
        if (!self::$globalSettingsLoaded) {
            self::loadGlobalSettings();
        }
        
        // Config'den slug'ı al ve memory cache'e kaydet
        $result = self::getConfigSlug($moduleName, $slugKey);
        self::$memoryCache[$memoryCacheKey] = $result;
        
        return $result;
    }
    
    /**
     * Global ayarları tek seferde yükle - performans için optimized
     */
    private static function loadGlobalSettings(): void
    {
        try {
            // Önce tablonun boş olup olmadığını kontrol et (24 saat cache)
            self::$tableIsEmpty = Cache::remember(self::$tableEmptyCacheKey, 60 * 24, function() {
                return ModuleTenantSetting::count() === 0;
            });
            
            if (self::$tableIsEmpty) {
                // Tablo boşsa hiç sorgu yapma, doğrudan config kullan
                Log::debug('ModuleSlugService: Table is empty, using config only');
                self::$globalSettingsLoaded = true;
                return;
            }
            
            // Tablo doluysa tüm ayarları tek seferde al (1 saat cache)
            $allSettings = Cache::remember(self::$globalCacheKey, 60, function() {
                return ModuleTenantSetting::all()->keyBy('module_name');
            });
            
            // Tüm config'leri de memory'ye yükle
            $modules = ['Page', 'Portfolio', 'Announcement'];
            foreach ($modules as $moduleName) {
                $configSlugs = self::getAllConfigSlugs($moduleName);
                
                if (isset($allSettings[$moduleName]) && isset($allSettings[$moduleName]->settings['slugs'])) {
                    // Custom ayarlar varsa onları kullan
                    $customSlugs = $allSettings[$moduleName]->settings['slugs'];
                    foreach ($configSlugs as $key => $defaultSlug) {
                        $finalSlug = $customSlugs[$key] ?? $defaultSlug;
                        self::$memoryCache[$moduleName . '.' . $key] = $finalSlug;
                    }
                } else {
                    // Custom ayar yoksa config kullan
                    foreach ($configSlugs as $key => $slug) {
                        self::$memoryCache[$moduleName . '.' . $key] = $slug;
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('ModuleSlugService: Failed to load global settings', [
                'error' => $e->getMessage()
            ]);
        }
        
        self::$globalSettingsLoaded = true;
    }
    
    /**
     * Config dosyasından slug al (optimized)
     */
    private static function getConfigSlug(string $moduleName, string $slugKey): string
    {
        // Memory cache'den kontrol et
        if (isset(self::$configCache[$moduleName])) {
            return self::$configCache[$moduleName][$slugKey] ?? $slugKey;
        }
        
        // Config'i yükle ve memory cache'e kaydet
        self::$configCache[$moduleName] = self::getAllConfigSlugs($moduleName);
        return self::$configCache[$moduleName][$slugKey] ?? $slugKey;
    }
    
    /**
     * Config dosyasını yükle ve return et
     */
    private static function loadConfigSlugs(string $moduleName): array
    {
        $configPath = "Modules/{$moduleName}/config/config.php";
        $fullPath = base_path($configPath);
        
        if (file_exists($fullPath)) {
            $config = include $fullPath;
            return $config['slugs'] ?? [];
        } else {
            return [];
        }
    }
    
    /**
     * Belirli bir slug'ın diğer modüllerde kullanılıp kullanılmadığını kontrol et
     */
    public static function isSlugConflict(string $newSlug, string $currentModule, string $currentKey): bool
    {
        // Global ayarları yükle
        if (!self::$globalSettingsLoaded) {
            self::loadGlobalSettings();
        }
        
        // Memory cache'deki tüm slug'ları kontrol et
        foreach (self::$memoryCache as $cacheKey => $slug) {
            [$moduleName, $keyName] = explode('.', $cacheKey, 2);
            
            // Aynı modül + aynı key ise skip et
            if ($moduleName === $currentModule && $keyName === $currentKey) {
                continue;
            }
            
            if ($slug === $newSlug) {
                return true; // Çakışma var
            }
        }
        
        return false; // Çakışma yok
    }
    
    /**
     * Modülün tüm config slug'larını al (24 saat cache)
     */
    private static function getAllConfigSlugs(string $moduleName): array
    {
        $configCacheKey = 'module_config_' . $moduleName;
        return Cache::remember($configCacheKey, 60 * 24, function() use ($moduleName) {
            return self::loadConfigSlugs($moduleName);
        });
    }
    
    /**
     * Cache'i temizle (slug değişikliklerinde)
     */
    public static function clearCache(): void
    {
        // Memory cache'i temizle
        self::$memoryCache = [];
        self::$configCache = [];
        self::$globalSettingsLoaded = false;
        self::$tableIsEmpty = null;
        
        // Laravel cache'i temizle
        Cache::forget(self::$globalCacheKey);
        Cache::forget(self::$tableEmptyCacheKey);
        Cache::forget('module_config_Page');
        Cache::forget('module_config_Portfolio');
        Cache::forget('module_config_Announcement');
    }
    
    /**
     * Tablo boş cache'ini temizle (yeni veri eklendiğinde)
     */
    public static function markTableAsNotEmpty(): void
    {
        self::$tableIsEmpty = false;
        Cache::forget(self::$tableEmptyCacheKey);
        Cache::forget(self::$globalCacheKey);
    }
}