<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Dynamic Module Manager Service
 * 
 * Eliminates ALL hardcoded module structures across the system.
 * Provides tenant-specific, database-driven module management.
 */
readonly class DynamicModuleManager
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'dynamic_modules_';

    /**
     * Get all available modules dynamically
     */
    public static function getAvailableModules(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'available_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $modulesPath = base_path('Modules');
            
            if (!File::exists($modulesPath)) {
                return collect([]);
            }
            
            $modules = collect(File::directories($modulesPath))
                ->map(function ($path) {
                    return basename($path);
                })
                ->filter(function ($moduleName) {
                    // Check if module has proper structure
                    return self::isValidModule($moduleName);
                });
                
            return $modules;
        });
    }

    /**
     * Get module translations for specific module
     */
    public static function getModuleTranslations(string $moduleName): array
    {
        $cacheKey = self::CACHE_PREFIX . "translations_{$moduleName}_" . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($moduleName) {
            $translations = [];
            
            // Get active languages
            $activeLanguages = TenantLanguageProvider::getActiveLanguageCodes();
            
            foreach ($activeLanguages as $langCode) {
                // Try to get translation from module's lang files
                $translation = self::getModuleTranslationFromFile($moduleName, $langCode);
                
                if ($translation) {
                    $translations[$langCode] = $translation;
                } else {
                    // Fallback to module name
                    $translations[$langCode] = $moduleName;
                }
            }
            
            return $translations;
        });
    }

    /**
     * Get module slug translations for specific module and action
     */
    public static function getModuleSlugs(string $moduleName, string $action = 'index'): array
    {
        $cacheKey = self::CACHE_PREFIX . "slugs_{$moduleName}_{$action}_" . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($moduleName, $action) {
            $slugs = [];
            
            // Get active languages
            $activeLanguages = TenantLanguageProvider::getActiveLanguageCodes();
            
            foreach ($activeLanguages as $langCode) {
                // Try to get slug from module config or generate from name
                $slug = self::generateModuleSlug($moduleName, $action, $langCode);
                $slugs[$langCode] = $slug;
            }
            
            return $slugs;
        });
    }

    /**
     * Get module actions (index, show, category, etc.)
     */
    public static function getModuleActions(string $moduleName): array
    {
        $cacheKey = self::CACHE_PREFIX . "actions_{$moduleName}_" . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($moduleName) {
            $actions = ['index', 'show']; // Default actions
            
            // Check if module has categories
            if (self::moduleHasCategories($moduleName)) {
                $actions[] = 'category';
            }
            
            // Check if module has tags
            if (self::moduleHasTags($moduleName)) {
                $actions[] = 'tag';
            }
            
            return $actions;
        });
    }

    /**
     * Check if module is valid (has proper structure)
     */
    private static function isValidModule(string $moduleName): bool
    {
        $modulePath = base_path("Modules/{$moduleName}");
        
        // Check if module.json exists
        if (!File::exists($modulePath . '/module.json')) {
            return false;
        }
        
        // Check if main model exists
        $modelPath = $modulePath . "/app/Models/{$moduleName}.php";
        if (!File::exists($modelPath)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get module translation from language files
     */
    private static function getModuleTranslationFromFile(string $moduleName, string $langCode): ?string
    {
        // Try admin.php first
        $adminLangPath = base_path("Modules/{$moduleName}/lang/{$langCode}/admin.php");
        
        if (File::exists($adminLangPath)) {
            $translations = include $adminLangPath;
            
            // Look for common keys - PRIORITY ORDER
            $possibleKeys = [
                'module_name',           // En öncelikli - modül spesifik
                strtolower($moduleName), // portfolio => 'Portfolyo'
                $moduleName,             // Portfolio => 'Portfolyo'  
                'title'                  // En son - generic field (form field'ları için)
            ];
            
            foreach ($possibleKeys as $key) {
                if (isset($translations[$key])) {
                    return $translations[$key];
                }
            }
        }
        
        // Try global lang files
        $globalLangPath = base_path("lang/{$langCode}/admin.php");
        if (File::exists($globalLangPath)) {
            $translations = include $globalLangPath;
            
            $moduleKey = strtolower($moduleName);
            if (isset($translations[$moduleKey])) {
                return $translations[$moduleKey];
            }
        }
        
        return null;
    }

    /**
     * Generate module slug for specific language
     * Gets module name from CENTRAL DATABASE, not lang files!
     */
    private static function generateModuleSlug(string $moduleName, string $action, string $langCode): string
    {
        // Get module translation from CENTRAL DATABASE - direkt bu fonksiyonu kullan
        $moduleTranslation = self::getModuleDisplayName($moduleName, $langCode);
        
        // Convert to slug format
        $baseSlug = self::convertToSlug($moduleTranslation, $langCode);
        
        // Add action suffix if not index
        if ($action !== 'index') {
            $actionSlug = self::getActionSlug($action, $langCode);
            return $baseSlug; // For now, just return base slug
        }
        
        return $baseSlug;
    }

    /**
     * Convert text to slug format
     */
    private static function convertToSlug(string $text, string $langCode): string
    {
        // Language-specific slug conversion
        $slug = strtolower($text);
        
        // Turkish character conversion
        if ($langCode === 'tr') {
            $slug = str_replace(
                ['ğ', 'ü', 'ş', 'ı', 'ö', 'ç', 'Ğ', 'Ü', 'Ş', 'İ', 'Ö', 'Ç'],
                ['g', 'u', 's', 'i', 'o', 'c', 'g', 'u', 's', 'i', 'o', 'c'],
                $slug
            );
        }
        
        // Arabic character conversion
        if ($langCode === 'ar') {
            // Remove Arabic diacritics and convert to basic Latin
            $slug = transliterator_transliterate('Any-Latin; Latin-ASCII', $slug);
        }
        
        // Replace spaces and special characters with dashes
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Get action slug for specific language
     */
    private static function getActionSlug(string $action, string $langCode): string
    {
        $actionTranslations = [
            'tr' => [
                'category' => 'kategori',
                'tag' => 'etiket',
                'show' => 'detay',
                'index' => '',
            ],
            'en' => [
                'category' => 'category',
                'tag' => 'tag',
                'show' => 'show',
                'index' => '',
            ],
            'ar' => [
                'category' => 'تصنيف',
                'tag' => 'علامة',
                'show' => 'عرض',
                'index' => '',
            ],
        ];
        
        return $actionTranslations[$langCode][$action] ?? $action;
    }

    /**
     * Check if module has categories
     */
    private static function moduleHasCategories(string $moduleName): bool
    {
        $categoryModelPath = base_path("Modules/{$moduleName}/app/Models/{$moduleName}Category.php");
        return File::exists($categoryModelPath);
    }

    /**
     * Check if module has tags
     */
    private static function moduleHasTags(string $moduleName): bool
    {
        $tagModelPath = base_path("Modules/{$moduleName}/app/Models/{$moduleName}Tag.php");
        return File::exists($tagModelPath);
    }

    /**
     * Clear all module caches
     */
    public static function clearCache(): void
    {
        $tenantKey = self::getTenantCacheKey();
        $patterns = [
            self::CACHE_PREFIX . 'available_' . $tenantKey,
        ];

        // Clear translation caches for all modules
        $modules = self::getAvailableModules();
        foreach ($modules as $moduleName) {
            $patterns[] = self::CACHE_PREFIX . "translations_{$moduleName}_" . $tenantKey;
            $patterns[] = self::CACHE_PREFIX . "slugs_{$moduleName}_index_" . $tenantKey;
            $patterns[] = self::CACHE_PREFIX . "slugs_{$moduleName}_show_" . $tenantKey;
            $patterns[] = self::CACHE_PREFIX . "slugs_{$moduleName}_category_" . $tenantKey;
            $patterns[] = self::CACHE_PREFIX . "actions_{$moduleName}_" . $tenantKey;
        }

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get current tenant cache key
     */
    private static function getTenantCacheKey(): string
    {
        // Try to get tenant from current context
        if (function_exists('tenant') && tenant()) {
            return (string) tenant()->getTenantKey();
        }
        
        return 'central';
    }

    /**
     * Get module display name for specific language
     * ALWAYS gets from central database modules table - NO LANG FILES!
     */
    public static function getModuleDisplayName(string $moduleName, string $langCode): string
    {
        \Log::debug("DynamicModuleManager::getModuleDisplayName called", ['module' => $moduleName, 'langCode' => $langCode]);
        // DIREKT central database'den al - recursive call önleme
        try {
            if (\Schema::hasTable('modules')) {
                $module = \DB::table('modules')
                    ->where('name', strtolower($moduleName))
                    ->first();
                
                if ($module && $module->display_name) {
                    // display_name JSON ise locale'e göre al
                    if (is_string($module->display_name) && str_starts_with($module->display_name, '{')) {
                        $displayNames = json_decode($module->display_name, true);
                        if (is_array($displayNames) && isset($displayNames[$langCode])) {
                            return $displayNames[$langCode];
                        }
                    }
                    
                    // display_name string ise direkt döndür
                    if (is_string($module->display_name)) {
                        return $module->display_name;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('DynamicModuleManager: Could not fetch from modules table', [
                'module' => $moduleName,
                'locale' => $langCode,
                'error' => $e->getMessage()
            ]);
        }
        
        return $moduleName;
    }

    /**
     * Get all module slugs for all languages
     */
    public static function getAllModuleSlugs(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'all_slugs_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $allSlugs = [];
            $modules = self::getAvailableModules();
            $languages = TenantLanguageProvider::getActiveLanguageCodes();
            
            foreach ($modules as $moduleName) {
                $allSlugs[$moduleName] = [];
                $actions = self::getModuleActions($moduleName);
                
                foreach ($actions as $action) {
                    $allSlugs[$moduleName][$action] = [];
                    foreach ($languages as $langCode) {
                        $slug = self::generateModuleSlug($moduleName, $action, $langCode);
                        $allSlugs[$moduleName][$action][$langCode] = $slug;
                    }
                }
            }
            
            return $allSlugs;
        });
    }
}