<?php

namespace App\Services;

use App\Contracts\DynamicRouteResolverInterface;
use App\Services\ModuleAccessService;
use Illuminate\Support\Facades\Log;

class DynamicRouteService
{
    protected DynamicRouteResolverInterface $resolver;
    protected ModuleAccessService $moduleAccessService;
    
    public function __construct(
        DynamicRouteResolverInterface $resolver,
        ModuleAccessService $moduleAccessService
    ) {
        $this->resolver = $resolver;
        $this->moduleAccessService = $moduleAccessService;
    }
    
    /**
     * Dynamic route'u handle et - Locale aware
     * 
     * @deprecated Use DynamicRouteRegistrar instead
     */
    public function handleDynamicRoute(string $slug1, ?string $slug2 = null, ?string $slug3 = null, ?string $locale = null)
    {
        // Locale'i resolver'a geç
        $locale = $locale ?? app()->getLocale();
        
        $routeInfo = $this->resolver->resolve($slug1, $slug2, $slug3, $locale);
        
        if (!$routeInfo) {
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Dynamic route not found', [
                    'slug1' => $slug1,
                    'slug2' => $slug2,
                    'slug3' => $slug3,
                    'locale' => $locale,
                    'tenant' => tenant()?->id ?? 'central'
                ]);
            }
            abort(404, 'Page not found');
        }
        
        // Route info'ya locale ekle
        $routeInfo['locale'] = $locale;
        
        return $this->executeRoute($routeInfo);
    }
    
    /**
     * Route'u execute et - Modül-Tenant kontrol sistemi ile
     */
    protected function executeRoute(array $routeInfo)
    {
        try {
            $moduleName = $routeInfo['module'] ?? 'unknown';
            
            // 🔒 MODÜL-TENANT KONTROLü
            if (!$this->isModuleAccessible($moduleName)) {
                if (app()->environment(['local', 'staging'])) {
                    Log::warning('Module access denied', [
                        'module' => $moduleName,
                        'tenant_id' => tenant()?->id ?? 'central',
                        'url' => request()->fullUrl()
                    ]);
                }
                abort(404, 'Page not found');
            }
            
            $controller = app($routeInfo['controller']);
            $method = $routeInfo['method'];
            $params = $routeInfo['params'] ?? [];
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Executing dynamic route', [
                    'controller' => $routeInfo['controller'],
                    'method' => $method,
                    'module' => $moduleName,
                    'params' => $params,
                    'locale' => $routeInfo['locale'] ?? app()->getLocale(),
                    'app_locale' => app()->getLocale()
                ]);
            }
            
            return $controller->$method(...$params);
            
        } catch (\Exception $e) {
            Log::error('Dynamic route execution error', [
                'route_info' => $routeInfo,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'Internal server error');
        }
    }
    
    /**
     * Modülün frontend'te erişilebilir olup olmadığını kontrol et
     */
    protected function isModuleAccessible(string $moduleName): bool
    {
        try {
            // Modül tenant'a atanmış mı kontrol et
            $module = $this->moduleAccessService->getModuleByName($moduleName);
            
            if (!$module || !$module->is_active) {
                return false;
            }
            
            // Tenant'a atanmış mı kontrol et
            $tenantId = tenant()?->id ?? 1;
            return $this->moduleAccessService->isModuleAssignedToTenant($module->module_id, $tenantId);
            
        } catch (\Exception $e) {
            Log::error('Module accessibility check failed', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Route cache'ini temizle
     */
    public function clearCache(): void
    {
        $this->resolver->clearRouteCache();
    }
    
    /**
     * Slug'a göre modül bilgisini bul
     */
    public function findModuleBySlug(string $slug): ?array
    {
        // Resolver üzerinden modül bilgisini al
        return $this->resolver->findModuleBySlug($slug);
    }
    
    /**
     * İçeriğin farklı dildeki URL'ini bul
     * 
     * @param string $module Modül adı (Page, Portfolio, etc.)
     * @param string $currentSlug Mevcut dildeki slug
     * @param string $currentLocale Mevcut dil
     * @param string $targetLocale Hedef dil
     * @return string|null Hedef URL veya null
     */
    public function findLocalizedUrl(string $module, string $currentSlug, string $currentLocale, string $targetLocale): ?string
    {
        try {
            // Model namespace'ini oluştur
            $modelClass = "\\Modules\\{$module}\\App\\Models\\{$module}";
            
            if (!class_exists($modelClass)) {
                return null;
            }
            
            // Mevcut slug ile içeriği bul
            $model = $modelClass::whereJsonContains("slug->{$currentLocale}", $currentSlug)
                ->first();
            
            if (!$model) {
                return null;
            }
            
            // Hedef dildeki slug'ı al
            $targetSlug = $model->getTranslated('slug', $targetLocale);
            
            if (!$targetSlug) {
                return null;
            }
            
            // Modül slug'ını al
            $moduleSlugService = app(ModuleSlugService::class);
            $moduleSlug = $moduleSlugService->getSlug($module, 'show');
            
            // URL oluştur
            $defaultLocale = get_tenant_default_locale();
            
            if ($targetLocale === $defaultLocale) {
                return url("{$moduleSlug}/{$targetSlug}");
            } else {
                return url("{$targetLocale}/{$moduleSlug}/{$targetSlug}");
            }
            
        } catch (\Exception $e) {
            Log::warning('Failed to find localized URL', [
                'module' => $module,
                'current_slug' => $currentSlug,
                'current_locale' => $currentLocale,
                'target_locale' => $targetLocale,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}