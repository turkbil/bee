<?php

namespace App\Services;

use App\Contracts\DynamicRouteResolverInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\ModuleSlugService;

class DynamicRouteResolver implements DynamicRouteResolverInterface
{
    /**
     * Cache TTL dakika cinsinden
     */
    protected const CACHE_TTL = 30;
    
    /**
     * Dinamik modül route cache'i
     */
    protected static array $moduleRouteCache = [];
    
    /**
     * Slug'ları çözümle ve controller/action döndür - locale aware
     */
    public function resolve(string $slug1, ?string $slug2 = null, ?string $slug3 = null, ?string $locale = null): ?array
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = $this->generateCacheKey($slug1, $slug2, $slug3, $locale);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($slug1, $slug2, $slug3, $locale) {
            return $this->resolveSlugMapping($slug1, $slug2, $slug3, $locale);
        });
    }
    
    /**
     * Tüm modüllerin route mapping'ini dinamik al
     */
    protected function getModuleRouteMap(): array
    {
        if (!empty(self::$moduleRouteCache)) {
            return self::$moduleRouteCache;
        }

        $cacheKey = 'dynamic_route_module_map';

        self::$moduleRouteCache = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () {
            return $this->loadModuleRoutes();
        });

        return self::$moduleRouteCache;
    }
    
    /**
     * Modül route'larını dosyalardan yükle
     */
    protected function loadModuleRoutes(): array
    {
        $moduleRoutes = [];
        $modulesPath = base_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return $moduleRoutes;
        }
        
        $modules = array_filter(glob($modulesPath . '/*'), 'is_dir');
        
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $configPath = $modulePath . '/config/config.php';
            
            if (file_exists($configPath)) {
                try {
                    $config = include $configPath;
                    if (is_array($config) && isset($config['routes'])) {
                        $moduleRoutes[$moduleName] = $config['routes'];
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to load route config for module: {$moduleName}", [
                        'error' => $e->getMessage(),
                        'path' => $configPath
                    ]);
                }
            }
        }
        
        return $moduleRoutes;
    }
    
    /**
     * Modül için route mapping'i al
     */
    public function getModuleRouteMapping(string $moduleName): array
    {
        $moduleRouteMap = $this->getModuleRouteMap();
        return $moduleRouteMap[$moduleName] ?? [];
    }
    
    /**
     * Route cache'ini temizle
     */
    public function clearRouteCache(): void
    {
        self::$moduleRouteCache = []; // Memory cache temizle

        // Cache::forget ile specific key'leri temizle
        Cache::forget('dynamic_route_module_map');

        // Redis pattern matching ile tüm dynamic route cache'lerini temizle
        try {
            // Explicit redis store kullan (FileStore'da getRedis() metodu yok)
            $redis = Cache::store('redis')->getRedis();
            $prefix = config('database.redis.options.prefix', '');
            $pattern = $prefix . ':dynamic_route:*';
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        } catch (\Exception $e) {
            // Redis kullanılmıyorsa veya hata varsa sessizce devam et
        }
    }
    
    /**
     * Slug mapping'ini çözümle - locale aware
     */
    protected function resolveSlugMapping(string $slug1, ?string $slug2, ?string $slug3, string $locale): ?array
    {
        try {
            $moduleRouteMap = $this->getModuleRouteMap();
            
            foreach ($moduleRouteMap as $moduleName => $routes) {
                // Tüm modül action'larının slug'larını al (DİNAMİK + LOCALE AWARE!)
                $moduleSlugMap = [];
                foreach ($routes as $action => $config) {
                    // Önce locale-specific slug'ı dene
                    $actionSlug = ModuleSlugService::getMultiLangSlug($moduleName, $action, $locale);
                    if (!$actionSlug) {
                        // Yoksa default slug'ı kullan  
                        $actionSlug = ModuleSlugService::getMultiLangSlug($moduleName, $action, $locale);
                    }
                    $moduleSlugMap[$action] = $actionSlug;
                }

                // Single slug pattern - index action için
                if (!$slug2 && !$slug3) {
                    foreach ($moduleSlugMap as $action => $actionSlug) {
                        if ($slug1 === $actionSlug && $action === 'index' && isset($routes[$action])) {
                            return [
                                'controller' => $routes[$action]['controller'],
                                'method' => $routes[$action]['method'],
                                'module' => $moduleName,
                                'action' => $action,
                                'params' => []
                            ];
                        }
                    }
                }
                
                // Two slug pattern - show action veya diğer action'lar için
                if ($slug2 && !$slug3) {
                    // 1. Önce show slug + content slug pattern'ini kontrol et (/haberler/content-slug)
                    // Bu, kullanıcının show için özel slug tanımlamasına olanak sağlar
                    if (isset($moduleSlugMap['show']) && $slug1 === $moduleSlugMap['show'] && isset($routes['show'])) {
                        return [
                            'controller' => $routes['show']['controller'],
                            'method' => $routes['show']['method'],
                            'module' => $moduleName,
                            'action' => 'show',
                            'params' => [$slug2]
                        ];
                    }

                    // 2. Backward compatibility: index slug + content slug pattern'ini de kontrol et
                    // Eğer show slug tanımlı değilse veya index slug ile aynıysa bu pattern çalışır
                    if ($slug1 === $moduleSlugMap['index'] && isset($routes['show']) &&
                        (!isset($moduleSlugMap['show']) || $moduleSlugMap['show'] === $moduleSlugMap['index'])) {
                        return [
                            'controller' => $routes['show']['controller'],
                            'method' => $routes['show']['method'],
                            'module' => $moduleName,
                            'action' => 'show',
                            'params' => [$slug2]
                        ];
                    }
                    
                    // 2. Sonra diğer action'ları kontrol et
                    foreach ($moduleSlugMap as $action => $actionSlug) {
                        // Eğer slug1 bu action'ın slug'ı ise
                        if ($slug1 === $actionSlug && $action !== 'index' && isset($routes[$action])) {
                            return [
                                'controller' => $routes[$action]['controller'],
                                'method' => $routes[$action]['method'],
                                'module' => $moduleName,
                                'action' => $action,
                                'params' => [$slug2]
                            ];
                        }
                    }
                }
                
                // Three slug pattern - üç parametre gerekli action'lar için
                if ($slug2 && $slug3) {
                    foreach ($moduleSlugMap as $action => $actionSlug) {
                        // DİNAMİK pattern - index/[herhangi-action]/[slug] şeklinde
                        // category, tag, etiket, label ne olursa olsun çalışır
                        if ($slug1 === $moduleSlugMap['index'] && $slug2 === $actionSlug && isset($routes[$action])) {
                            return [
                                'controller' => $routes[$action]['controller'],
                                'method' => $routes[$action]['method'],
                                'module' => $moduleName,
                                'action' => $action,
                                'params' => [$slug3] // içerik slug'ını geç
                            ];
                        }
                    }
                }
            }

            // 🔥 DİNAMİK FALLBACK: Single slug için content kontrolü
            // Hiçbir modül index slug'ı match etmediyse, tüm modüllerde content slug ara
            if (!$slug2 && !$slug3) {
                foreach ($moduleRouteMap as $moduleName => $routes) {
                    // Show action yoksa atla
                    if (!isset($routes['show'])) {
                        continue;
                    }

                    // Model class'ını oluştur
                    $modelClass = "\\Modules\\{$moduleName}\\App\\Models\\{$moduleName}";

                    if (!class_exists($modelClass)) {
                        continue;
                    }

                    try {
                        // Bu slug'a sahip aktif content var mı?
                        $model = $modelClass::whereJsonContains("slug->{$locale}", $slug1)
                            ->where('is_active', true)
                            ->first();

                        if ($model) {
                            return [
                                'controller' => $routes['show']['controller'],
                                'method' => $routes['show']['method'],
                                'module' => $moduleName,
                                'action' => 'show',
                                'params' => [$slug1]
                            ];
                        }
                    } catch (\Exception $e) {
                        // Hata varsa devam et (örn: tablo yoksa)
                        continue;
                    }
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Dynamic route resolution error', [
                'slug1' => $slug1,
                'slug2' => $slug2,
                'slug3' => $slug3,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Cache key oluştur
     */
    protected function generateCacheKey(string $slug1, ?string $slug2, ?string $slug3, ?string $locale = null): string
    {
        $tenant = tenant();
        $tenantPart = $tenant ? "tenant_{$tenant->id}" : 'central';
        $locale = $locale ?? app()->getLocale();
        
        $slugParts = [$slug1];
        if ($slug2) $slugParts[] = $slug2;
        if ($slug3) $slugParts[] = $slug3;
        $slugPart = implode('_', $slugParts);
        
        return "dynamic_route:{$tenantPart}:{$locale}:{$slugPart}";
    }
    
    /**
     * Cache tag'leri al
     */
    protected function getCacheTags(): array
    {
        $tenant = tenant();
        $tags = ['dynamic_routes'];
        
        if ($tenant) {
            $tags[] = "tenant_{$tenant->id}:dynamic_routes";
        }
        
        return $tags;
    }
    
    /**
     * Slug'a göre modül bilgisini bul
     */
    public function findModuleBySlug(string $slug): ?array
    {
        $moduleRouteMap = $this->getModuleRouteMap();
        
        foreach ($moduleRouteMap as $moduleName => $routes) {
            // Tüm action'ların slug'larını kontrol et
            foreach ($routes as $action => $config) {
                $actionSlug = ModuleSlugService::getSlug($moduleName, $action);
                if ($slug === $actionSlug) {
                    return [
                        'module' => $moduleName,
                        'action' => $action,
                        'config' => $config
                    ];
                }
            }
        }
        
        return null;
    }
}