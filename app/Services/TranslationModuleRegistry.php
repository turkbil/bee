<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Enterprise Translation Module Registry
 * 
 * Yüzlerce modülü otomatik keşfeden ve organize eden sistem
 * Zero-config, convention-based approach
 */
class TranslationModuleRegistry
{
    private static $cache = null;
    private static $cacheKey = 'translation_modules_registry';
    private static $cacheTimeout = 3600; // 1 saat
    
    /**
     * Tüm çeviri destekli modülleri getir
     */
    public static function getAvailableModules(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        
        return self::$cache = Cache::remember(self::$cacheKey, self::$cacheTimeout, function() {
            return self::discoverModules();
        });
    }
    
    /**
     * Entity mapping'i getir (JavaScript için)
     */
    public static function getEntityMapping(): array
    {
        $modules = self::getAvailableModules();
        $mapping = [];
        
        foreach ($modules as $moduleName => $moduleConfig) {
            if (isset($moduleConfig['entities']) && is_array($moduleConfig['entities'])) {
                foreach ($moduleConfig['entities'] as $entityKey => $entityConfig) {
                    $mapping[$entityKey] = $entityConfig['route_prefix'] ?? Str::kebab($entityKey);
                }
            }
        }
        
        return $mapping;
    }
    
    /**
     * Belirli bir entity için config getir
     */
    public static function getEntityConfig(string $entityType): ?array
    {
        $modules = self::getAvailableModules();
        
        foreach ($modules as $moduleConfig) {
            if (isset($moduleConfig['entities'][$entityType])) {
                return $moduleConfig['entities'][$entityType];
            }
        }
        
        return null;
    }
    
    /**
     * Cache'i temizle
     */
    public static function clearCache(): void
    {
        Cache::forget(self::$cacheKey);
        self::$cache = null;
    }
    
    /**
     * Cache'i yenile
     */
    public static function refreshCache(): array
    {
        self::clearCache();
        return self::getAvailableModules();
    }
    
    /**
     * Modülleri otomatik keşfet
     */
    private static function discoverModules(): array
    {
        $modules = [];
        $modulePaths = glob(base_path('Modules/*/'));
        
        if (!$modulePaths) {
            return [];
        }
        
        foreach ($modulePaths as $path) {
            $moduleName = basename($path);
            
            try {
                $config = self::loadModuleConfig($moduleName);
                
                if ($config && ($config['translation_enabled'] ?? false)) {
                    $modules[$moduleName] = $config;
                }
            } catch (\Exception $e) {
                // Module config yüklenemedi, atla
                \Log::warning("Translation module config failed for {$moduleName}: " . $e->getMessage());
                continue;
            }
        }
        
        return $modules;
    }
    
    /**
     * Modül config'ini yükle (manuel veya otomatik)
     */
    private static function loadModuleConfig(string $moduleName): ?array
    {
        // Önce manuel config dosyasını kontrol et
        $configPath = base_path("Modules/{$moduleName}/Config/translation.php");
        
        if (file_exists($configPath)) {
            try {
                $config = require $configPath;
                if (is_array($config)) {
                    return $config;
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to load translation config for {$moduleName}: " . $e->getMessage());
            }
        }
        
        // Manuel config yoksa otomatik generate et
        return self::generateModuleConfig($moduleName);
    }
    
    /**
     * Modül için otomatik config generate et
     */
    private static function generateModuleConfig(string $moduleName): array
    {
        $entities = [];
        $modelsPath = base_path("Modules/{$moduleName}/app/Models/");
        
        if (!is_dir($modelsPath)) {
            return ['translation_enabled' => false, 'entities' => []];
        }
        
        $modelFiles = glob($modelsPath . '*.php');
        
        if (!$modelFiles) {
            return ['translation_enabled' => false, 'entities' => []];
        }
        
        foreach ($modelFiles as $file) {
            $modelName = basename($file, '.php');
            
            try {
                $entity = self::analyzeModel($moduleName, $modelName);
                if ($entity) {
                    $entityKey = strtolower(Str::snake($modelName));
                    $entities[$entityKey] = $entity;
                }
            } catch (\Exception $e) {
                // Model analizi başarısız, atla
                continue;
            }
        }
        
        return [
            'translation_enabled' => !empty($entities),
            'module_name' => $moduleName,
            'auto_generated' => true,
            'generated_at' => now()->toISOString(),
            'entities' => $entities
        ];
    }
    
    /**
     * Model'i analiz et ve çeviri config'i oluştur
     */
    private static function analyzeModel(string $moduleName, string $modelName): ?array
    {
        $modelClass = "Modules\\{$moduleName}\\App\\Models\\{$modelName}";
        
        if (!class_exists($modelClass)) {
            return null;
        }
        
        try {
            $model = new $modelClass;
            
            // Fillable alanları al
            $fillable = method_exists($model, 'getFillable') ? $model->getFillable() : [];
            
            // Cast'leri al
            $casts = method_exists($model, 'getCasts') ? $model->getCasts() : [];
            
            // Çeviri yapılabilir alanları tespit et
            $translatableFields = self::detectTranslatableFields($fillable, $casts);
            
            if (empty($translatableFields)) {
                return null; // Çeviri yapılabilir alan yok
            }
            
            // SEO trait kontrolü
            $seoEnabled = self::hasSeoTrait($model);
            
            // Component adını tahmin et
            $componentName = self::guessComponentName($modelName);
            
            return [
                'model' => $modelName,
                'model_class' => $modelClass,
                'module_name' => $moduleName,
                'route_prefix' => Str::kebab($modelName),
                'translatable_fields' => array_values($translatableFields),
                'has_seo' => $seoEnabled,
                'component' => $componentName,
                'primary_key' => $model->getKeyName() ?? 'id'
            ];
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Çeviri yapılabilir alanları tespit et
     */
    private static function detectTranslatableFields(array $fillable, array $casts): array
    {
        $commonTranslatableFields = [
            'title', 'name', 'content', 'body', 'description', 
            'excerpt', 'summary', 'slug', 'meta_title', 
            'meta_description', 'short_description'
        ];
        
        $translatableFields = [];
        
        foreach ($fillable as $field) {
            // Yaygın çeviri alanları
            if (in_array($field, $commonTranslatableFields)) {
                $translatableFields[] = $field;
                continue;
            }
            
            // JSON cast edilmiş alanlar (çoklu dil için)
            if (isset($casts[$field]) && in_array($casts[$field], ['json', 'array', 'object'])) {
                $translatableFields[] = $field;
                continue;
            }
            
            // '_tr', '_en' gibi suffix'li alanlar
            if (preg_match('/_(tr|en|ar|de|fr|es|it)$/', $field)) {
                $baseField = preg_replace('/_(tr|en|ar|de|fr|es|it)$/', '', $field);
                if (!in_array($baseField, $translatableFields)) {
                    $translatableFields[] = $baseField;
                }
            }
        }
        
        return array_unique($translatableFields);
    }
    
    /**
     * Model'in SEO trait'i var mı kontrol et
     */
    private static function hasSeoTrait($model): bool
    {
        $traits = class_uses_recursive($model);
        
        return in_array('Modules\\SeoManagement\\App\\Traits\\HasSeo', $traits) ||
               in_array('App\\Traits\\HasSeo', $traits) ||
               method_exists($model, 'seoSettings');
    }
    
    /**
     * Component adını tahmin et
     */
    private static function guessComponentName(string $modelName): string
    {
        $baseName = $modelName;
        
        // Yaygın component naming patterns
        $patterns = [
            $baseName . 'Component',
            $baseName . 'ManageComponent', 
            $baseName . 'AdminComponent',
            Str::kebab($baseName) . '-component'
        ];
        
        return $patterns[0]; // İlk pattern'i default olarak döndür
    }
    
    /**
     * İstatistikler
     */
    public static function getStats(): array
    {
        $modules = self::getAvailableModules();
        $totalEntities = 0;
        $enabledModules = 0;
        
        foreach ($modules as $moduleConfig) {
            if ($moduleConfig['translation_enabled'] ?? false) {
                $enabledModules++;
                $totalEntities += count($moduleConfig['entities'] ?? []);
            }
        }
        
        return [
            'total_modules' => count($modules),
            'enabled_modules' => $enabledModules,
            'total_entities' => $totalEntities,
            'cache_status' => Cache::has(self::$cacheKey) ? 'cached' : 'fresh'
        ];
    }
}