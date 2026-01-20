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

            $name = Cache::remember($cacheKey, 1440, function() use ($moduleName, $locale) {
                // Tenant context check - ModuleTenantSetting sadece tenant'larda var
                if (tenancy()->initialized && \Schema::hasTable('module_tenant_settings')) {
                    // ModuleTenantSetting'den title kolonunu al
                    $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();

                    // Önce title kolonuna bak (yeni sistem)
                    if ($setting && $setting->title && isset($setting->title[$locale]) && !empty(trim($setting->title[$locale]))) {
                        return $setting->title[$locale];
                    }

                    // Backward compatibility: settings['multiLangNames']
                    if ($setting && isset($setting->settings['multiLangNames'][$locale])) {
                        return $setting->settings['multiLangNames'][$locale];
                    }
                }

                // Default modül adı - central'dan
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
     * Default modül adını getir - modules tablosundan display_name kolonunu kullanır
     */
    public static function getDefaultModuleName(string $moduleName, string $locale): string
    {
        try {
            // Önce modules tablosundan display_name'i al (CENTRAL DB!)
            $centralConnection = config('tenancy.central_connection', 'mysql');
            if (\Schema::connection($centralConnection)->hasTable('modules')) {
                $module = \DB::connection($centralConnection)->table('modules')
                    ->where('name', strtolower($moduleName))
                    ->first();
                
                if ($module && $module->display_name) {
                    // display_name JSON ise locale'e göre al
                    if (is_string($module->display_name) && str_starts_with($module->display_name, '{')) {
                        $displayNames = json_decode($module->display_name, true);
                        if (is_array($displayNames) && isset($displayNames[$locale])) {
                            return $displayNames[$locale];
                        }
                    }
                    
                    // display_name string ise direkt döndür
                    if (is_string($module->display_name)) {
                        return $module->display_name;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('ModuleSlugService: Could not fetch from modules table', [
                'module' => $moduleName,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
        }
        
        // RECURSIVE CALL ÖNLEME: DynamicModuleManager artık bize geri çağırıyor!
        // Bu fallback kaldırıldı çünkü infinite loop oluşturuyordu.
        
        return $moduleName;
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
        // Tenant ID al - cache key'lerin tenant-specific olması için
        $tenantId = tenant()?->id ?? 'central';

        // Memory cache kontrolü - tenant + locale specific
        $memoryCacheKey = $tenantId . '.' . $moduleName . '.' . $slugKey . '.' . $locale;
        if (isset(self::$memoryCache[$memoryCacheKey])) {
            return self::$memoryCache[$memoryCacheKey];
        }

        try {
            // MultiLang ayarlarından al - TENANT-SPECIFIC cache key
            $cacheKey = "module_multilang_slug_{$tenantId}_{$moduleName}_{$slugKey}_{$locale}";

            $slug = Cache::remember($cacheKey, 1440, function() use ($moduleName, $slugKey, $locale) {
                // Tenant context check - ModuleTenantSetting sadece tenant'larda var
                if (tenancy()->initialized && \Schema::hasTable('module_tenant_settings')) {
                    // ModuleTenantSetting'den multiLangSlugs'ı al
                    $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();

                    if ($setting && isset($setting->settings['multiLangSlugs'][$locale][$slugKey])) {
                        return $setting->settings['multiLangSlugs'][$locale][$slugKey];
                    }
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
            // Tenant context check - ModuleTenantSetting sadece tenant'larda var
            if (!tenancy()->initialized || !\Schema::hasTable('module_tenant_settings')) {
                Log::debug('ModuleSlugService: Not in tenant context, using config only');
                self::$globalSettingsLoaded = true;
                return;
            }

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
        // Modül klasörünü case-insensitive bul (Linux case-sensitive!)
        $actualModuleName = self::findActualModuleName($moduleName);

        if (!$actualModuleName) {
            return [];
        }

        $configPath = "Modules/{$actualModuleName}/config/config.php";
        $fullPath = base_path($configPath);

        if (file_exists($fullPath)) {
            $config = include $fullPath;
            return $config['slugs'] ?? [];
        }

        return [];
    }

    /**
     * Modül klasörünü case-insensitive bul
     */
    private static function findActualModuleName(string $moduleName): ?string
    {
        static $moduleNameCache = [];

        $cacheKey = strtolower($moduleName);
        if (isset($moduleNameCache[$cacheKey])) {
            return $moduleNameCache[$cacheKey];
        }

        $modulesPath = base_path('Modules');
        if (!is_dir($modulesPath)) {
            return null;
        }

        foreach (scandir($modulesPath) as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            if (strtolower($dir) === $cacheKey) {
                $moduleNameCache[$cacheKey] = $dir;
                return $dir;
            }
        }

        return null;
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
     * MultiLang slug çakışma kontrolü - NURULLAH'IN DOĞRU MANTIĞI
     * 
     * Çakışma kuralları:
     * 1. Slug, central domain'deki modules tablosundaki herhangi bir modül ismi ile aynı olamaz
     * 2. Slug, aynı tenant'taki başka bir modülün herhangi bir key'inde kullanılamaz
     * 3. Aynı modülün farklı key'leri aynı slug'ı kullanabilir (OK)
     * 4. Farklı dillerde aynı slug kullanılabilir (prefix var)
     */
    public static function isMultiLangSlugConflict(string $newSlug, string $currentModule, string $currentKey, string $locale): bool
    {
        try {
            // 1. MODÜL İSMİ ÇAKIŞMASI KONTROLÜ (Central Domain)
            // NOT: Bu kontrol sadece central domain için yapılır, modules tablosu tenant'larda yok
            if (config('tenancy.database.prefix') === null || config('tenancy.database.prefix') === '') {
                // Central domain'de modules tablosu var mı kontrol et
                if (\Schema::hasTable('modules')) {
                    $moduleNameExists = \DB::table('modules')
                        ->where('name', $newSlug)
                        ->exists();
                        
                    if ($moduleNameExists) {
                        Log::info("Module name conflict", [
                            'slug' => $newSlug,
                            'conflict_type' => 'module_name',
                            'message' => "Slug '{$newSlug}' matches an existing module name in central database"
                        ]);
                        
                        return true; // Çakışma var: Slug bir modül ismi ile aynı
                    }
                }
            }
            
            // 2. AYNI TENANT'TAKİ BAŞKA MODÜL KONTROLÜ
            // Tenant context check - ModuleTenantSetting sadece tenant'larda var
            if (tenancy()->initialized && \Schema::hasTable('module_tenant_settings')) {
                $modules = ['Page', 'Portfolio', 'Announcement'];

                foreach ($modules as $moduleName) {
                    // Aynı modül ise skip et (aynı modülde çakışma olmaz) - Case insensitive kontrol
                    if (strtolower($moduleName) === strtolower($currentModule)) {
                        continue;
                    }

                    $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();

                    if ($setting && isset($setting->settings['multiLangSlugs'][$locale])) {
                        $slugs = $setting->settings['multiLangSlugs'][$locale];

                        // Bu modülün herhangi bir key'inde aynı slug var mı?
                        foreach ($slugs as $key => $slug) {
                            if ($slug === $newSlug) {
                                Log::info("Tenant slug conflict", [
                                    'conflicting_module' => $moduleName,
                                    'conflicting_key' => $key,
                                    'conflicting_slug' => $slug,
                                    'new_module' => $currentModule,
                                    'new_key' => $currentKey,
                                    'new_slug' => $newSlug,
                                    'locale' => $locale,
                                    'conflict_type' => 'tenant_slug',
                                    'message' => "Slug '{$newSlug}' is already used by {$moduleName}.{$key} in {$locale}"
                                ]);

                                return true; // Çakışma var: Başka modül bu slug'ı kullanıyor
                            }
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
        
        // MultiLang cache'lerini temizle - DİNAMİK + TENANT-SPECIFIC
        $modules = \App\Services\DynamicModuleManager::getAvailableModules()->toArray();
        $locales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
        $keys = ['index', 'show', 'category', 'tag'];
        $tenantId = tenant()?->id ?? 'central';

        foreach ($modules as $module) {
            foreach ($locales as $locale) {
                // Slug cache'leri - YENİ tenant-specific format
                foreach ($keys as $key) {
                    Cache::forget("module_multilang_slug_{$tenantId}_{$module}_{$key}_{$locale}");
                    // Eski format için de temizle (backward compatibility)
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
        
        // DynamicModuleManager'dan al
        try {
            return \App\Services\DynamicModuleManager::getAvailableModules()->toArray();
        } catch (\Exception $e) {
            Log::warning('DynamicModuleManager kullanılamadı: ' . $e->getMessage());
        }
        
        // Son fallback - boş array
        return [];
    }
}