<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Contracts\DynamicRouteResolverInterface;

class DynamicRouteRegistrar
{
    protected DynamicRouteResolverInterface $resolver;
    
    public function __construct(DynamicRouteResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    
    /**
     * Tüm modüller için dynamic route'ları register et
     */
    public function registerDynamicRoutes(): void
    {
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Registering dynamic routes');
        }
        
        // Ana dynamic route pattern'leri register et
        $this->registerMainRoutes();
    }
    
    /**
     * Ana route pattern'lerini register et
     */
    protected function registerMainRoutes(): void
    {
        // Tenant context'inde dil prefix'li route'lar
        Route::middleware(['web', 'tenant', 'locale.site', 'page.tracker'])
            ->group(function () {
                // Single slug route (e.g., /tr/pages)
                Route::get('/{lang}/{slug1}', function ($lang, $slug1) {
                    return $this->handleDynamicRoute($slug1);
                })->where('lang', $this->getSupportedLanguageRegex())
                  ->where('slug1', $this->getSlugPattern());
                
                // Double slug route (e.g., /tr/page/about)
                Route::get('/{lang}/{slug1}/{slug2}', function ($lang, $slug1, $slug2) {
                    return $this->handleDynamicRoute($slug1, $slug2);
                })->where('lang', $this->getSupportedLanguageRegex())
                  ->where('slug1', $this->getSlugPattern())
                  ->where('slug2', $this->getSlugPattern());
            });
    }
    
    /**
     * Dynamic route'u handle et
     */
    protected function handleDynamicRoute(string $slug1, ?string $slug2 = null)
    {
        $routeInfo = $this->resolver->resolve($slug1, $slug2);
        
        if (!$routeInfo) {
            abort(404, 'Route not found');
        }
        
        $controller = app($routeInfo['controller']);
        $method = $routeInfo['method'];
        $params = $routeInfo['params'] ?? [];
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Dynamic route executed', [
                'module' => $routeInfo['module'],
                'action' => $routeInfo['action'],
                'controller' => $routeInfo['controller'],
                'method' => $method,
                'params' => $params
            ]);
        }
        
        return $controller->$method(...$params);
    }
    
    /**
     * Desteklenen dil regex'ini al
     */
    protected function getSupportedLanguageRegex(): string
    {
        // Bu fonksiyon helper'dan gelir, cache'li
        if (function_exists('getSupportedLanguageRegex')) {
            return getSupportedLanguageRegex();
        }
        
        return 'tr|en|ar'; // Fallback
    }
    
    /**
     * Slug pattern'i al
     */
    protected function getSlugPattern(): string
    {
        // Admin, API, auth route'larını exclude et
        return '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard)[^/]+$';
    }
    
    /**
     * Route cache uyumluluğu için route'ları pre-register et
     */
    public function preRegisterForCache(): void
    {
        if (!app()->routesAreCached()) {
            return;
        }
        
        // Production'da route cache kullanılıyorsa
        // Tüm route'lar önceden register edilmeli
        $this->registerMainRoutes();
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Routes pre-registered for cache compatibility');
        }
    }
}