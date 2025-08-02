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
    
    public static function getSlug(string $moduleName, string $slugKey, ?string $locale = null): string
    {
        // Her zaman multiLang slug kullan
        $locale = $locale ?? app()->getLocale();
        return self::getMultiLangSlug($moduleName, $slugKey, $locale);
    }
    
    /**
     * Multi-language modül adı getir
     * 
     * @param string $moduleName
     * @param string $locale
     * @return string
     */
    public static function getModuleName(string $moduleName, string $locale): string
    {
        // Memory cache kontrolü
        $memoryCacheKey = $moduleName . '.name.' . $locale;
        if (isset(self::$memoryCache[$memoryCacheKey])) {
            return self::$memoryCache[$memoryCacheKey];
        }
        
        try {
            $cacheKey = "module_name_{$moduleName}_{$locale}";
            
            $name = Cache::remember($cacheKey, 60, function() use ($moduleName, $locale) {
                // ModuleTenantSetting'den multiLangNames'i al
                $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();
                
                if ($setting && isset($setting->settings['multiLangNames'][$locale])) {
                    return $setting->settings['multiLangNames'][$locale];
                }
                
                // Default modül adı
                return self::getDefaultModuleName($moduleName, $locale);
            });
            
            self::$memoryCache[$memoryCacheKey] = $name;
            return $name;
            
        } catch (\Exception $e) {
            Log::warning('ModuleSlugService: Failed to get module name', [
                'module' => $moduleName,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
        }
        
        return self::getDefaultModuleName($moduleName, $locale);
    }
    
    /**
     * Default modül adını getir
     */
    public static function getDefaultModuleName(string $moduleName, string $locale): string
    {
        $defaultNames = [
            'Page' => [
                'tr' => 'Sayfalar',
                'en' => 'Pages',
                'ar' => 'الصفحات'
            ],
            'Portfolio' => [
                'tr' => 'Portfolyo',
                'en' => 'Portfolio',
                'ar' => 'المحفظة'
            ],
            'Announcement' => [
                'tr' => 'Duyurular',
                'en' => 'Announcements',
                'ar' => 'الإعلانات'
            ]
        ];
        
        return $defaultNames[$moduleName][$locale] ?? $moduleName;
    }
    
    /**
     * Multi-language slug getir - YENİ YAPІ
     * 
     * @param string $moduleName
     * @param string $slugKey
     * @param string $locale
     * @return string
     */
    public static function getMultiLangSlug(string $moduleName, string $slugKey, string $locale): string
    {
        // Memory cache kontrolü - locale specific
        $memoryCacheKey = $moduleName . '.' . $slugKey . '.' . $locale;
        if (isset(self::$memoryCache[$memoryCacheKey])) {
            return self::$memoryCache[$memoryCacheKey];
        }
        
        try {
            // MultiLang ayarlarından al
            $cacheKey = "module_multilang_slug_{$moduleName}_{$slugKey}_{$locale}";
            
            $slug = Cache::remember($cacheKey, 60, function() use ($moduleName, $slugKey, $locale) {
                // ModuleTenantSetting'den multiLangSlugs'ı al
                $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();
                
                if ($setting && isset($setting->settings['multiLangSlugs'][$locale][$slugKey])) {
                    return $setting->settings['multiLangSlugs'][$locale][$slugKey];
                }
                
                
                // Config'den default al
                return self::getConfigSlug($moduleName, $slugKey);
            });
            
            self::$memoryCache[$memoryCacheKey] = $slug;
            return $slug;
            
        } catch (\Exception $e) {
            Log::warning('ModuleSlugService: Failed to get multiLang slug', [
                'module' => $moduleName,
                'key' => $slugKey,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
        }
        
        // Hata durumunda config slug'ını döndür
        return self::getConfigSlug($moduleName, $slugKey);
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
                
                // Sadece multiLangSlugs kullan artık
                $dbSetting = $allSettings[$moduleName] ?? $allSettings[strtolower($moduleName)] ?? null;
                if ($dbSetting && isset($dbSetting->settings['multiLangSlugs'])) {
                    // MultiLang slug ayarları varsa memory'ye yükle
                    foreach ($dbSetting->settings['multiLangSlugs'] as $locale => $slugs) {
                        foreach ($slugs as $key => $slug) {
                            self::$memoryCache[$moduleName . '.' . $key . '.' . $locale] = $slug;
                        }
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
    public static function isSlugConflict(string $newSlug, string $currentModule, string $currentKey, ?string $locale = null): bool
    {
        // MultiLang slug çakışma kontrolü
        if ($locale) {
            return self::isMultiLangSlugConflict($newSlug, $currentModule, $currentKey, $locale);
        }
        
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
     * MultiLang slug çakışma kontrolü
     */
    public static function isMultiLangSlugConflict(string $newSlug, string $currentModule, string $currentKey, string $locale): bool
    {
        try {
            $modules = ['Page', 'Portfolio', 'Announcement'];
            
            foreach ($modules as $moduleName) {
                // Aynı modülün aynı key'i ise skip et
                if ($moduleName === $currentModule) {
                    continue;
                }
                
                $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();
                
                if ($setting && isset($setting->settings['multiLangSlugs'][$locale])) {
                    $slugs = $setting->settings['multiLangSlugs'][$locale];
                    
                    foreach ($slugs as $key => $slug) {
                        if ($slug === $newSlug) {
                            return true; // Çakışma var
                        }
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('ModuleSlugService: Failed to check multiLang conflict', [
                'slug' => $newSlug,
                'module' => $currentModule,
                'key' => $currentKey,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
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
        
        // MultiLang cache'lerini temizle
        $modules = ['Page', 'Portfolio', 'Announcement'];
        $locales = ['tr', 'en', 'ar'];
        $keys = ['index', 'show', 'category'];
        
        foreach ($modules as $module) {
            foreach ($locales as $locale) {
                // Slug cache'leri
                foreach ($keys as $key) {
                    Cache::forget("module_multilang_slug_{$module}_{$key}_{$locale}");
                }
                // Modül adı cache'leri
                Cache::forget("module_name_{$module}_{$locale}");
            }
        }
        
        // Tüm cache tag'lerini temizle
        try {
            Cache::flush();
        } catch (\Exception $e) {
            // Cache driver flush desteklemiyorsa sessizce devam et
        }
        
        Log::info('ModuleSlugService: All caches cleared (including multiLang)');
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
    
    /**
     * Belirli bir locale için slug al
     * UnifiedUrlBuilderService tarafından kullanılıyor
     */
    public static function getSlugForLocale(string $moduleName, string $slugKey, string $locale): string
    {
        return self::getMultiLangSlug($moduleName, $slugKey, $locale);
    }
    
    /**
     * Tüm modüllerin listesini al
     */
    public static function getAllModules(): array
    {
        $modules = [];
        
        // ModuleManagement service'i varsa ondan al
        if (class_exists('\Modules\ModuleManagement\App\Services\ModuleManagementService')) {
            try {
                $moduleService = app('\Modules\ModuleManagement\App\Services\ModuleManagementService');
                if (method_exists($moduleService, 'getActiveModules')) {
                    return $moduleService->getActiveModules();
                }
            } catch (\Exception $e) {
                Log::warning('ModuleManagementService kullanılamadı: ' . $e->getMessage());
            }
        }
        
        // Fallback: Laravel Modules'dan al
        if (class_exists('\Nwidart\Modules\Facades\Module')) {
            try {
                $moduleNames = \Nwidart\Modules\Facades\Module::allEnabled();
                return array_keys($moduleNames);
            } catch (\Exception $e) {
                Log::warning('Laravel Modules kullanılamadı: ' . $e->getMessage());
            }
        }
        
        // Hardcode fallback
        return ['Page', 'Portfolio', 'Announcement', 'UserManagement', 'ModuleManagement'];
    }
}