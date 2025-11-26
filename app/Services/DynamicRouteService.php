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
            // 404 durumunda alternatif slug kontrolü
            $alternativeUrl = $this->checkAlternativeSlugs($slug1, $slug2, $slug3, $locale);
            
            if ($alternativeUrl) {
                if (app()->environment(['local', 'staging'])) {
                    Log::debug('Redirecting to alternative slug', [
                        'from' => request()->fullUrl(),
                        'to' => $alternativeUrl
                    ]);
                }
                return redirect($alternativeUrl, 301);
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
            
            
            return $controller->$method(...$params);
            
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // HttpException'ları (abort() çağrıları) yeniden fırlat
            throw $e;
        } catch (\Exception $e) {
            Log::error('Dynamic route execution error', [
                'route_info' => $routeInfo,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
            
            // Modül slug'ını al - MultiLang destekli
            $moduleSlugService = app(ModuleSlugService::class);
            $moduleSlug = $moduleSlugService->getMultiLangSlug($module, 'show', $targetLocale);
            
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
    
    /**
     * Alternatif slug'ları kontrol et ve doğru URL'e yönlendir
     * 
     * @param string $slug1 İlk slug
     * @param string|null $slug2 İkinci slug
     * @param string|null $slug3 Üçüncü slug
     * @param string $locale Mevcut dil
     * @return string|null Yönlendirilecek URL veya null
     */
    protected function checkAlternativeSlugs(string $slug1, ?string $slug2, ?string $slug3, string $locale): ?string
    {
        try {
            $moduleSlugService = app(ModuleSlugService::class);
            
            // TENANT-AWARE: Tenant'ın aktif dillerini al
            $tenantId = tenant()?->id ?? 1;
            
            // Central tenant (ID: 1) için de normal tenant gibi davran
            // Çünkü site_languages tenant database'inde
            if (true) { // Her zaman bu blok çalışsın
                // Diğer tenant'lar için tenant_languages tablosu (tenant DB'de)
                $availableLocales = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)
                    ->pluck('code')
                    ->toArray();
            }
            
            // TENANT-AWARE: Sadece tenant'a atanmış modülleri kontrol et
            $tenantModules = $this->moduleAccessService->getTenantModules($tenantId);
            
            foreach ($tenantModules as $module) {
                $moduleName = $module->name;
                
                // Her dil için modül slug'larını kontrol et
                foreach ($availableLocales as $checkLocale) {
                    // Index slug'ını kontrol et - MultiLang destekli
                    $indexSlug = $moduleSlugService->getMultiLangSlug($moduleName, 'index', $checkLocale);
                    
                    // Single slug pattern - modül index sayfası
                    if (!$slug2 && !$slug3 && $slug1 === $indexSlug) {
                        // Doğru dil için slug'ı al - MultiLang destekli
                        $correctSlug = $moduleSlugService->getMultiLangSlug($moduleName, 'index', $locale);
                        
                        if ($correctSlug !== $slug1) {
                            $defaultLocale = get_tenant_default_locale();
                            $prefix = ($locale !== $defaultLocale) ? '/' . $locale : '';
                            return url($prefix . '/' . $correctSlug);
                        }
                    }
                    
                    // Two slug pattern - detay sayfası
                    if ($slug2 && !$slug3 && $slug1 === $indexSlug) {
                        // Model'i slug'dan bul
                        $modelClass = "\\Modules\\{$moduleName}\\App\\Models\\{$moduleName}";
                        if (class_exists($modelClass)) {
                            // Herhangi bir dilde bu slug var mı kontrol et
                            $query = $modelClass::query();
                            foreach ($availableLocales as $searchLocale) {
                                $query->orWhereJsonContains("slug->{$searchLocale}", $slug2);
                            }
                            
                            $model = $query->first();
                            if ($model) {
                                // Doğru dildeki slug'ı al
                                $correctSlug = $model->getTranslated('slug', $locale);
                                $correctModuleSlug = $moduleSlugService->getMultiLangSlug($moduleName, 'index', $locale);
                                
                                $defaultLocale = get_tenant_default_locale();
                                $prefix = ($locale !== $defaultLocale) ? '/' . $locale : '';
                                return url($prefix . '/' . $correctModuleSlug . '/' . $correctSlug);
                            }
                        }
                    }
                    
                    // Three slug pattern - kategori/tag sayfası
                    if ($slug2 && $slug3 && $slug1 === $indexSlug) {
                        // Category, tag vb. action slug'larını kontrol et
                        $modulePath = base_path('Modules/' . $moduleName);
                        $configPath = $modulePath . '/config/config.php';
                        if (file_exists($configPath)) {
                            $config = include $configPath;
                            if (isset($config['routes'])) {
                                foreach ($config['routes'] as $action => $routeConfig) {
                                    if ($action === 'index') continue;
                                    
                                    $actionSlug = $moduleSlugService->getMultiLangSlug($moduleName, $action, $checkLocale);
                                    
                                    if ($slug2 === $actionSlug) {
                                        // Action model'ini bul (PortfolioCategory, PortfolioTag vb.)
                                        $actionModelClass = "\\Modules\\{$moduleName}\\App\\Models\\{$moduleName}" . ucfirst($action);
                                        if (class_exists($actionModelClass)) {
                                            // Slug'ı ara
                                            $query = $actionModelClass::query();
                                            foreach ($availableLocales as $searchLocale) {
                                                $query->orWhereJsonContains("slug->{$searchLocale}", $slug3);
                                            }
                                            
                                            $model = $query->first();
                                            if ($model) {
                                                // Doğru URL'i oluştur - MultiLang destekli
                                                $correctModuleSlug = $moduleSlugService->getMultiLangSlug($moduleName, 'index', $locale);
                                                $correctActionSlug = $moduleSlugService->getMultiLangSlug($moduleName, $action, $locale);
                                                
                                                $correctContentSlug = $model->getTranslated('slug', $locale);
                                                
                                                $defaultLocale = get_tenant_default_locale();
                                                $prefix = ($locale !== $defaultLocale) ? '/' . $locale : '';
                                                return url($prefix . '/' . $correctModuleSlug . '/' . $correctActionSlug . '/' . $correctContentSlug);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('Alternative slug check failed', [
                'slug1' => $slug1,
                'slug2' => $slug2,
                'slug3' => $slug3,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}