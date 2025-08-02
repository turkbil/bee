<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\SeoManagement\app\Models\SeoSetting;
use App\Contracts\UrlBuilderInterface;

/**
 * Unified URL Builder Service
 * 
 * Tüm URL oluşturma işlemlerini tek bir serviste toplar.
 * MenuManagement, LanguageSwitcher ve diğer sistemler bu servisi kullanır.
 */
readonly class UnifiedUrlBuilderService implements UrlBuilderInterface
{
    private const CACHE_TTL = 60; // dakika
    private const CACHE_PREFIX = 'unified_url_';

    public function __construct(
        private ModuleSlugService $moduleSlugService,
        private HomepageRouteService $homepageService,
        private LocaleValidationService $localeValidator,
        private ?PerformanceMonitoringService $performanceMonitor = null
    ) {
        // Performance monitoring is handled via dependency injection
    }

    /**
     * Model için URL oluştur
     */
    public function buildUrlForModel(Model $model, string $action = 'show', ?string $locale = null): string
    {
        // Performance monitoring başlat
        $this->performanceMonitor?->startTiming('url_model_generation');
        
        $locale = $this->normalizeLocale($locale);
        $cacheKey = $this->getCacheKey('model', [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => $action,
            'locale' => $locale
        ]);

        $url = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($model, $action, $locale) {
            try {
                // Homepage kontrolü
                if ($this->homepageService->isHomepage($model)) {
                    return $this->homepageService->getHomepageUrl($locale);
                }

                // Normal model URL'i
                return $this->generateModelUrl($model, $action, $locale);
            } catch (\Exception $e) {
                Log::error('UnifiedUrlBuilder: Model URL generation failed', [
                    'model' => get_class($model),
                    'id' => $model->getKey(),
                    'error' => $e->getMessage()
                ]);
                
                return $this->getFallbackUrl($locale);
            }
        });
        
        // Performance monitoring bitir
        if ($this->performanceMonitor) {
            $duration = $this->performanceMonitor->endTiming('url_model_generation');
            $this->performanceMonitor->trackUrlGeneration('model', $duration);
        }
        
        return $url;
    }

    /**
     * Module için URL oluştur
     */
    public function buildUrlForModule(string $module, string $action = 'index', ?array $params = null, ?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $cacheKey = $this->getCacheKey('module', [
            'module' => $module,
            'action' => $action,
            'params' => $params,
            'locale' => $locale
        ]);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($module, $action, $params, $locale) {
            try {
                // Module index slug'ını al
                $moduleIndexSlug = $this->moduleSlugService->getSlugForLocale($module, 'index', $locale);
                if (!$moduleIndexSlug) {
                    $moduleIndexSlug = $this->moduleSlugService->getSlug($module, 'index');
                }
                
                // Action index değilse, action slug'ını da al
                if ($action !== 'index') {
                    $actionSlug = $this->moduleSlugService->getSlugForLocale($module, $action, $locale);
                    if (!$actionSlug) {
                        $actionSlug = $this->moduleSlugService->getSlug($module, $action);
                    }
                    
                    // 3-segment URL için: module/action/params
                    $path = $moduleIndexSlug . '/' . $actionSlug;
                    if ($params && !empty($params)) {
                        $path .= '/' . implode('/', $params);
                    }
                    
                    return $this->buildUrlWithLocale($path, $locale);
                }
                
                // Index action için sadece module slug kullan
                return $this->buildModuleUrl($moduleIndexSlug, $params, $locale);
            } catch (\Exception $e) {
                Log::error('UnifiedUrlBuilder: Module URL generation failed', [
                    'module' => $module,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getFallbackUrl($locale);
            }
        });
    }

    /**
     * Path için URL oluştur
     */
    public function buildUrlForPath(string $path, ?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $defaultLocale = get_tenant_default_locale();

        // Path temizleme
        $path = ltrim($path, '/');

        // Locale prefix kontrolü
        if ($locale === $defaultLocale) {
            return url($path ?: '/');
        }

        return url("{$locale}/" . ($path ?: ''));
    }

    /**
     * Mevcut URL için alternate linkler oluştur
     */
    public function generateAlternateLinks(?Model $model = null, string $action = 'show'): array
    {
        $links = [];
        $availableLocales = $this->getAvailableLocales();
        $currentLocale = app()->getLocale();

        foreach ($availableLocales as $locale) {
            if ($model) {
                // Action 'show' değilse (category, tag, author, vb.) özel işlem yap
                if ($action !== 'show' && $action !== 'index' && method_exists($model, 'getTranslated')) {
                    // Bu bir action model'i (PortfolioCategory, AnnouncementTag, vb.)
                    $moduleName = $this->getModuleNameFromModel($model);
                    $slug = $model->getTranslated('slug', $locale);
                    
                    if ($slug) {
                        $url = $this->buildUrlForModule($moduleName, $action, [$slug], $locale);
                    } else {
                        // Slug yoksa ID kullan
                        $url = $this->buildUrlForModule($moduleName, $action, [$model->getKey()], $locale);
                    }
                } else {
                    // Normal model URL'i (show action)
                    $url = $this->buildUrlForModel($model, $action, $locale);
                }
            } else {
                // Model yoksa mevcut URL'in path'ini analiz et
                $url = $this->buildAlternateUrlFromCurrentPath($locale);
            }

            $links[$locale] = [
                'url' => $url,
                'hreflang' => $locale,
                'current' => $locale === $currentLocale,
                'name' => $this->getLocaleName($locale)
            ];
        }

        return $links;
    }

    /**
     * URL'den route bilgisi çıkar (reverse routing)
     */
    public function parseUrl(string $url): array
    {
        try {
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            $host = $parsedUrl['host'] ?? '';

            // Harici URL kontrolü
            if ($host && $host !== request()->getHost()) {
                return [
                    'type' => 'external',
                    'data' => ['url' => $url]
                ];
            }

            // Path segmentlerini al
            $segments = array_filter(explode('/', trim($path, '/')));

            if (empty($segments)) {
                return [
                    'type' => 'homepage',
                    'data' => []
                ];
            }

            // Locale kontrolü
            $firstSegment = reset($segments);
            $locale = null;
            
            if ($this->localeValidator->isValidLocale($firstSegment)) {
                $locale = array_shift($segments);
            }

            // Module routing analizi
            return $this->analyzeModuleRoute($segments, $locale);

        } catch (\Exception $e) {
            Log::error('UnifiedUrlBuilder: URL parsing failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return [
                'type' => 'unknown',
                'data' => ['url' => $url]
            ];
        }
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(?string $locale = null): void
    {
        if ($locale) {
            Cache::forget(self::CACHE_PREFIX . $locale . '*');
        } else {
            Cache::flush(); // Tüm URL cache'lerini temizle
        }
    }

    /**
     * Performance metrikleri
     */
    public function getPerformanceMetrics(): array
    {
        return Cache::remember('url_builder_metrics', 300, function () {
            return [
                'total_urls_generated' => Cache::get('url_builder_total', 0),
                'cache_hit_rate' => Cache::get('url_builder_cache_hits', 0) / max(Cache::get('url_builder_total', 1), 1) * 100,
                'average_generation_time' => Cache::get('url_builder_avg_time', 0),
                'errors_count' => Cache::get('url_builder_errors', 0)
            ];
        });
    }

    // Private helper methods

    private function normalizeLocale(?string $locale): string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->localeValidator->isValidLocale($locale)) {
            return get_tenant_default_locale();
        }

        return $locale;
    }

    private function generateModelUrl(Model $model, string $action, string $locale): string
    {
        $moduleName = class_basename($model);
        $slug = $model->getTranslated('slug', $locale);
        
        if (!$slug) {
            // Fallback to default locale slug
            $slug = $model->getTranslated('slug', get_tenant_default_locale());
        }

        if (!$slug) {
            throw new \Exception('No slug found for model');
        }

        // Module slug al - locale parametresi ile
        $moduleSlug = $this->moduleSlugService->getSlug($moduleName, $action, $locale);
        
        // URL oluştur
        $defaultLocale = get_tenant_default_locale();
        
        if ($locale === $defaultLocale) {
            return url("{$moduleSlug}/{$slug}");
        }

        return url("{$locale}/{$moduleSlug}/{$slug}");
    }

    private function buildModuleUrl(string $moduleSlug, ?array $params, string $locale): string
    {
        $defaultLocale = get_tenant_default_locale();
        $path = $moduleSlug;

        if ($params && !empty($params)) {
            $path .= '/' . implode('/', $params);
        }

        if ($locale === $defaultLocale) {
            return url($path);
        }

        return url("{$locale}/{$path}");
    }
    
    /**
     * Path'e locale prefix ekleyerek URL oluştur
     */
    private function buildUrlWithLocale(string $path, string $locale): string
    {
        $defaultLocale = get_tenant_default_locale();
        
        if ($locale === $defaultLocale) {
            return url($path);
        }
        
        return url("{$locale}/{$path}");
    }

    private function buildUrlForCurrentPath(string $locale): string
    {
        $currentPath = request()->path();
        $segments = array_filter(explode('/', $currentPath));
        
        // Mevcut locale prefix'ini kaldır
        $firstSegment = reset($segments);
        if ($this->localeValidator->isValidLocale($firstSegment)) {
            array_shift($segments);
        }

        $cleanPath = implode('/', $segments);
        
        return $this->buildUrlForPath($cleanPath, $locale);
    }
    
    /**
     * Mevcut URL'den alternatif dil URL'i oluştur
     * Modül/action/slug yapısını koruyarak
     */
    private function buildAlternateUrlFromCurrentPath(string $locale): string
    {
        $currentPath = request()->path();
        $segments = array_filter(explode('/', $currentPath));
        
        // Mevcut locale prefix'ini kaldır
        $firstSegment = reset($segments);
        if ($this->localeValidator->isValidLocale($firstSegment)) {
            array_shift($segments);
        }
        
        // URL yapısını analiz et
        if (count($segments) >= 3) {
            // 3 segment: module/action/slug pattern
            $moduleSlug = $segments[0];
            $actionSlug = $segments[1];
            $contentSlug = $segments[2];
            
            // Modül adını bul
            $moduleName = $this->findModuleNameBySlug($moduleSlug);
            if ($moduleName) {
                // Action'ı bul
                $action = $this->findActionBySlug($moduleName, $actionSlug);
                if ($action) {
                    // Yeni dil için slug'ları al
                    $newModuleSlug = $this->moduleSlugService->getSlug($moduleName, 'index', $locale);
                    $newActionSlug = $this->moduleSlugService->getSlug($moduleName, $action, $locale);
                    
                    // URL'i oluştur
                    return $this->buildUrlWithLocale(
                        $newModuleSlug . '/' . $newActionSlug . '/' . $contentSlug,
                        $locale
                    );
                }
            }
        } elseif (count($segments) >= 2) {
            // 2 segment: module/slug pattern
            $moduleSlug = $segments[0];
            $contentSlug = $segments[1];
            
            // Modül adını bul
            $moduleName = $this->findModuleNameBySlug($moduleSlug);
            if ($moduleName) {
                $newModuleSlug = $this->moduleSlugService->getSlug($moduleName, 'show', $locale);
                return $this->buildUrlWithLocale(
                    $newModuleSlug . '/' . $contentSlug,
                    $locale
                );
            }
        }
        
        // Fallback: basit path çevirisi
        return $this->buildUrlForPath(implode('/', $segments), $locale);
    }

    private function getAvailableLocales(): array
    {
        return Cache::remember('available_locales', 3600, function () {
            return array_column(available_tenant_languages(), 'code');
        });
    }

    private function getLocaleName(string $locale): string
    {
        $names = config('app.available_locales', [
            'tr' => 'Türkçe',
            'en' => 'English',
            'ar' => 'العربية'
        ]);

        return $names[$locale] ?? strtoupper($locale);
    }

    private function getFallbackUrl(string $locale): string
    {
        $defaultLocale = get_tenant_default_locale();
        
        if ($locale === $defaultLocale) {
            return url('/');
        }

        return url("/{$locale}");
    }

    private function getCacheKey(string $type, array $params): string
    {
        $key = self::CACHE_PREFIX . $type . '_' . md5(serialize($params));
        
        // Metrik güncelle
        Cache::increment('url_builder_total');
        
        return $key;
    }
    
    /**
     * Model'den modül adını çıkar
     */
    private function getModuleNameFromModel(Model $model): string
    {
        $className = get_class($model);
        
        // PortfolioCategory -> Portfolio
        // AnnouncementCategory -> Announcement
        if (str_contains($className, 'Category')) {
            $baseName = str_replace('Category', '', class_basename($model));
            return $baseName;
        }
        
        // Namespace'den modül adını çıkar
        // Modules\Portfolio\App\Models\Portfolio -> Portfolio
        if (preg_match('/Modules\\\\([^\\\\]+)\\\\/', $className, $matches)) {
            return $matches[1];
        }
        
        // Fallback: class basename
        return class_basename($model);
    }
    
    /**
     * Slug'dan modül adını bul
     */
    private function findModuleNameBySlug(string $slug): ?string
    {
        $modules = $this->moduleSlugService->getAllModules();
        
        foreach ($modules as $module) {
            // Her dil için index slug'ını kontrol et
            $availableLocales = $this->getAvailableLocales();
            foreach ($availableLocales as $locale) {
                $moduleSlug = $this->moduleSlugService->getSlug($module, 'index', $locale);
                if ($moduleSlug === $slug) {
                    return $module;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Modül ve slug'dan action'ı bul
     */
    private function findActionBySlug(string $moduleName, string $actionSlug): ?string
    {
        // Tüm olası action'ları kontrol et
        $possibleActions = ['category', 'tag', 'author', 'label', 'type', 'group'];
        
        foreach ($possibleActions as $action) {
            // Her dil için action slug'ını kontrol et
            $availableLocales = $this->getAvailableLocales();
            foreach ($availableLocales as $locale) {
                $slug = $this->moduleSlugService->getSlug($moduleName, $action, $locale);
                if ($slug === $actionSlug) {
                    return $action;
                }
            }
        }
        
        return null;
    }

    private function analyzeModuleRoute(array $segments, ?string $locale): array
    {
        if (empty($segments)) {
            return [
                'type' => 'homepage',
                'locale' => $locale,
                'data' => []
            ];
        }

        // Module routing analizi
        $possibleModule = $segments[0];
        
        // Module var mı kontrol et
        $modules = $this->moduleSlugService->getAllModules();
        
        foreach ($modules as $module) {
            $indexSlug = $this->moduleSlugService->getSlug($module, 'index');
            
            if ($possibleModule === $indexSlug) {
                return [
                    'type' => 'module',
                    'locale' => $locale,
                    'data' => [
                        'module' => $module,
                        'action' => count($segments) === 1 ? 'index' : 'show',
                        'params' => array_slice($segments, 1)
                    ]
                ];
            }
        }

        return [
            'type' => 'internal',
            'locale' => $locale,
            'data' => ['path' => implode('/', $segments)]
        ];
    }
}