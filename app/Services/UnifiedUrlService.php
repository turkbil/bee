<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Unified URL Service
 * 
 * Tüm URL oluşturma ve çözümleme işlemlerini tek noktadan yönetir.
 * MultiLang, fallback ve cache yönetimini sağlar.
 */
class UnifiedUrlService
{
    /**
     * Model'den URL oluştur
     */
    public function buildUrlForModel(Model $model, ?string $locale = null): string
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $modelClass = get_class($model);
            $modelName = class_basename($modelClass);
            $moduleName = $this->getModuleNameFromModel($modelClass);
            
            // Page modülü için özel işlem
            if ($moduleName === 'Page') {
                return $this->buildPageUrl($model, $locale);
            }
            
            // İçerik slug'ını al
            $contentSlug = $this->getModelSlug($model, $locale);
            if (!$contentSlug) {
                return '#';
            }
            
            // Modül slug'ını al
            $moduleSlug = ModuleSlugService::getMultiLangSlug($moduleName, 'show', $locale);
            if (!$moduleSlug) {
                $moduleSlug = ModuleSlugService::getSlug($moduleName, 'show', $locale);
            }
            
            // URL oluştur - prefix kontrolü ile
            $prefix = $this->getLocalePrefix($locale);
            return url($prefix . '/' . $moduleSlug . '/' . $contentSlug);
            
        } catch (\Exception $e) {
            Log::error('UnifiedUrlService: Failed to build URL for model', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return '#';
        }
    }
    
    /**
     * Modül için URL oluştur
     */
    public function buildUrlForModule(string $module, string $action = 'index', ?array $params = null, ?string $locale = null): string
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $module = ucfirst(strtolower($module));
            
            // Modül slug'larını al
            $moduleSlug = ModuleSlugService::getMultiLangSlug($module, 'index', $locale);
            if (!$moduleSlug) {
                $moduleSlug = ModuleSlugService::getSlug($module, 'index', $locale);
            }
            
            // Action sadece index ise
            if ($action === 'index' && empty($params)) {
                $prefix = $this->getLocalePrefix($locale);
                return url($prefix . '/' . $moduleSlug);
            }
            
            // Action slug'ını al
            $actionSlug = ModuleSlugService::getMultiLangSlug($module, $action, $locale);
            if (!$actionSlug) {
                $actionSlug = ModuleSlugService::getSlug($module, $action, $locale);
            }
            
            // URL parçalarını oluştur
            $urlParts = [$moduleSlug, $actionSlug];
            
            // Parametreleri ekle
            if ($params) {
                foreach ($params as $param) {
                    // Eğer param bir model ise slug'ını al
                    if ($param instanceof Model) {
                        $paramSlug = $this->getModelSlug($param, $locale);
                        $urlParts[] = $paramSlug ?: $param->getKey();
                    } else {
                        $urlParts[] = $param;
                    }
                }
            }
            
            // URL oluştur - prefix kontrolü ile
            $prefix = $this->getLocalePrefix($locale);
            $path = implode('/', $urlParts);
            
            return url($prefix . '/' . $path);
            
        } catch (\Exception $e) {
            Log::error('UnifiedUrlService: Failed to build URL for module', [
                'module' => $module,
                'action' => $action,
                'params' => $params,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return '#';
        }
    }
    
    /**
     * URL'den route bilgisini çöz
     */
    public function resolveUrl(string $url, ?string $locale = null): ?array
    {
        try {
            $locale = $locale ?? app()->getLocale();
            
            // URL'i parse et
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '';
            
            // Path'i segmentlere ayır
            $segments = array_filter(explode('/', trim($path, '/')));
            $segments = array_values($segments); // Re-index array
            
            // Locale prefix'i varsa çıkar
            $defaultLocale = get_tenant_default_locale();
            $validLocales = get_tenant_languages();
            
            if (!empty($segments) && in_array($segments[0], $validLocales)) {
                $locale = array_shift($segments);
                $segments = array_values($segments); // Re-index
            }
            
            // Segment sayısına göre çözümle
            return match (count($segments)) {
                0 => ['type' => 'home'],
                1 => $this->resolveSingleSegment($segments[0], $locale),
                2 => $this->resolveTwoSegments($segments[0], $segments[1], $locale),
                3 => $this->resolveThreeSegments($segments[0], $segments[1], $segments[2], $locale),
                default => null
            };
            
        } catch (\Exception $e) {
            Log::error('UnifiedUrlService: Failed to resolve URL', [
                'url' => $url,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Slug'dan içeriği bul (multiLang destekli)
     */
    public function findContentBySlug(string $module, string $slug, ?string $locale = null): ?Model
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $module = ucfirst(strtolower($module));

            // Model class'ını bul
            $modelClass = "\\Modules\\{$module}\\App\\Models\\{$module}";

            if (!class_exists($modelClass)) {
                return null;
            }

            // Tablo adını al
            $tableName = (new $modelClass)->getTable();

            // Tenant context check - Tablo var mı kontrol et
            if (!\Schema::hasTable($tableName)) {
                // Tablo yok, muhtemelen central'dayız ve bu tenant-only bir tablo
                return null;
            }

            // Cache key
            $cacheKey = "content_by_slug_{$module}_{$slug}_{$locale}";

            return Cache::remember($cacheKey, 300, function() use ($modelClass, $slug, $locale, $module) {
                // Önce istenen dilde ara
                $model = $modelClass::whereJsonContains("slug->{$locale}", $slug)->first();

                if ($model) {
                    return $model;
                }

                // Bulunamazsa tüm dillerde ara (fallback)
                $availableLocales = \get_tenant_languages();
                foreach ($availableLocales as $searchLocale) {
                    if ($searchLocale === $locale) continue; // Zaten aradık

                    $model = $modelClass::whereJsonContains("slug->{$searchLocale}", $slug)->first();
                    if ($model) {
                        Log::info('UnifiedUrlService: Found content in different locale', [
                            'module' => $module,
                            'slug' => $slug,
                            'requested_locale' => $locale,
                            'found_locale' => $searchLocale
                        ]);
                        return $model;
                    }
                }

                return null;
            });

        } catch (\Exception $e) {
            Log::error('UnifiedUrlService: Failed to find content by slug', [
                'module' => $module,
                'slug' => $slug,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
    
    /**
     * Model'den slug al (multiLang destekli)
     */
    protected function getModelSlug(Model $model, string $locale): ?string
    {
        try {
            // HasTranslations trait'i kullanıyorsa
            if (method_exists($model, 'getTranslated')) {
                $slug = $model->getTranslated('slug', $locale);
                
                // Fallback: istenen dilde yoksa varsayılan dili kullan
                if (!$slug) {
                    $defaultLocale = get_tenant_default_locale();
                    $slug = $model->getTranslated('slug', $defaultLocale);
                }
                
                return $slug;
            }
            
            // Normal slug field'ı varsa
            if (isset($model->slug)) {
                return $model->slug;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('UnifiedUrlService: Failed to get model slug', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Model class'ından modül adını çıkar
     */
    protected function getModuleNameFromModel(string $modelClass): string
    {
        // Örnek: Modules\Portfolio\App\Models\Portfolio -> Portfolio
        if (preg_match('/Modules\\\\(\w+)\\\\/', $modelClass, $matches)) {
            return $matches[1];
        }
        
        // Fallback: class basename
        return class_basename($modelClass);
    }
    
    /**
     * Page URL'i oluştur (özel durum)
     */
    protected function buildPageUrl(Model $page, string $locale): string
    {
        $slug = $this->getModelSlug($page, $locale);
        
        if (!$slug) {
            return '#';
        }
        
        $prefix = $this->getLocalePrefix($locale);
        return url($prefix . '/' . ltrim($slug, '/'));
    }
    
    /**
     * Tek segment çözümle
     */
    protected function resolveSingleSegment(string $segment, string $locale): ?array
    {
        // Önce Page modülünde ara
        $page = $this->findContentBySlug('Page', $segment, $locale);
        if ($page) {
            return [
                'type' => 'page',
                'model' => $page,
                'module' => 'Page'
            ];
        }
        
        // Modül index sayfası mı kontrol et
        $modules = ['Portfolio', 'Announcement'];
        foreach ($modules as $module) {
            $indexSlug = ModuleSlugService::getMultiLangSlug($module, 'index', $locale);
            if ($segment === $indexSlug) {
                return [
                    'type' => 'module_index',
                    'module' => $module,
                    'action' => 'index'
                ];
            }
        }
        
        return null;
    }
    
    /**
     * İki segment çözümle
     */
    protected function resolveTwoSegments(string $segment1, string $segment2, string $locale): ?array
    {
        $modules = ['Portfolio', 'Announcement'];
        
        foreach ($modules as $module) {
            // Show pattern: module-show-slug/content-slug
            $showSlug = ModuleSlugService::getMultiLangSlug($module, 'show', $locale);
            if ($segment1 === $showSlug) {
                $content = $this->findContentBySlug($module, $segment2, $locale);
                if ($content) {
                    return [
                        'type' => 'module_show',
                        'module' => $module,
                        'action' => 'show',
                        'model' => $content
                    ];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Üç segment çözümle
     */
    protected function resolveThreeSegments(string $segment1, string $segment2, string $segment3, string $locale): ?array
    {
        $modules = ['Portfolio', 'Announcement'];
        
        foreach ($modules as $module) {
            $indexSlug = ModuleSlugService::getMultiLangSlug($module, 'index', $locale);
            
            if ($segment1 === $indexSlug) {
                // Action slug kontrolü (category, tag, vs.)
                $config = config("modules.{$module}.routes", []);
                
                foreach ($config as $action => $routeConfig) {
                    if ($action === 'index') continue;
                    
                    $actionSlug = ModuleSlugService::getMultiLangSlug($module, $action, $locale);
                    
                    if ($segment2 === $actionSlug) {
                        // Action model'ini bul
                        $actionModel = $this->findActionContent($module, $action, $segment3, $locale);
                        
                        if ($actionModel) {
                            return [
                                'type' => 'module_action',
                                'module' => $module,
                                'action' => $action,
                                'model' => $actionModel
                            ];
                        }
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Action içeriğini bul (category, tag vs.)
     */
    protected function findActionContent(string $module, string $action, string $slug, string $locale): ?Model
    {
        try {
            $module = ucfirst($module);
            $action = ucfirst($action);
            
            // Model class tahminleri
            $possibleClasses = [
                "\\Modules\\{$module}\\App\\Models\\{$module}{$action}",
                "\\Modules\\{$module}\\App\\Models\\{$module}" . rtrim($action, 's'),
            ];
            
            foreach ($possibleClasses as $modelClass) {
                if (class_exists($modelClass)) {
                    return $this->findContentBySlugInClass($modelClass, $slug, $locale);
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('UnifiedUrlService: Failed to find action content', [
                'module' => $module,
                'action' => $action,
                'slug' => $slug,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Belirli bir class'ta slug ile içerik bul
     */
    protected function findContentBySlugInClass(string $modelClass, string $slug, string $locale): ?Model
    {
        // Model class kontrolü
        if (!class_exists($modelClass)) {
            return null;
        }

        // Tablo adını al
        $tableName = (new $modelClass)->getTable();

        // Tenant context check - Tablo var mı kontrol et
        if (!\Schema::hasTable($tableName)) {
            // Tablo yok, muhtemelen central'dayız ve bu tenant-only bir tablo
            return null;
        }

        $cacheKey = "content_by_slug_class_" . md5($modelClass) . "_{$slug}_{$locale}";

        return Cache::remember($cacheKey, 300, function() use ($modelClass, $slug, $locale) {
            // Önce istenen dilde ara
            $model = $modelClass::whereJsonContains("slug->{$locale}", $slug)->first();

            if ($model) {
                return $model;
            }

            // Tüm dillerde ara
            $availableLocales = get_tenant_languages();
            foreach ($availableLocales as $searchLocale) {
                if ($searchLocale === $locale) continue;

                $model = $modelClass::whereJsonContains("slug->{$searchLocale}", $slug)->first();
                if ($model) {
                    return $model;
                }
            }

            return null;
        });
    }
    
    /**
     * Cache temizle
     */
    public function clearCache(): void
    {
        // Content cache'lerini temizle
        Cache::flush();
        
        // ModuleSlugService cache'ini de temizle
        ModuleSlugService::clearCache();
        
        Log::info('UnifiedUrlService: Cache cleared');
    }
    
    /**
     * Locale için prefix al
     * Varsayılan dilde bile prefix olabilir (url_prefix_mode'a göre)
     */
    protected function getLocalePrefix(string $locale): string
    {
        // UrlPrefixService varsa kullan
        if (class_exists('\Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            $needsPrefix = \Modules\LanguageManagement\app\Services\UrlPrefixService::needsPrefix($locale);
            return $needsPrefix ? '/' . $locale : '';
        }
        
        // Fallback: needs_locale_prefix helper
        if (function_exists('needs_locale_prefix')) {
            return needs_locale_prefix($locale) ? '/' . $locale : '';
        }
        
        // Son fallback: basit kontrol
        $defaultLocale = get_tenant_default_locale();
        return ($locale !== $defaultLocale) ? '/' . $locale : '';
    }
}