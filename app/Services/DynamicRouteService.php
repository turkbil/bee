<?php

namespace App\Services;

use App\Contracts\DynamicRouteResolverInterface;
use Illuminate\Support\Facades\Log;

class DynamicRouteService
{
    protected DynamicRouteResolverInterface $resolver;
    
    public function __construct(DynamicRouteResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    
    /**
     * Dynamic route'u handle et
     * 
     * @deprecated Use DynamicRouteRegistrar instead
     */
    public function handleDynamicRoute(string $slug1, ?string $slug2 = null, ?string $slug3 = null)
    {
        $routeInfo = $this->resolver->resolve($slug1, $slug2, $slug3);
        
        if (!$routeInfo) {
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Dynamic route not found', [
                    'slug1' => $slug1,
                    'slug2' => $slug2,
                    'slug3' => $slug3
                ]);
            }
            abort(404, 'Page not found');
        }
        
        return $this->executeRoute($routeInfo);
    }
    
    /**
     * Route'u execute et
     */
    protected function executeRoute(array $routeInfo)
    {
        try {
            $controller = app($routeInfo['controller']);
            $method = $routeInfo['method'];
            $params = $routeInfo['params'] ?? [];
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Executing dynamic route', [
                    'controller' => $routeInfo['controller'],
                    'method' => $method,
                    'module' => $routeInfo['module'] ?? 'unknown',
                    'params' => $params
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
     * Route cache'ini temizle
     */
    public function clearCache(): void
    {
        $this->resolver->clearRouteCache();
    }
}