<?php

namespace App\Contracts;

interface DynamicRouteResolverInterface
{
    /**
     * Slug'ları çözümle ve controller/action döndür
     */
    public function resolve(string $slug1, ?string $slug2 = null, ?string $slug3 = null): ?array;
    
    /**
     * Modül için route mapping'i al
     */
    public function getModuleRouteMapping(string $moduleName): array;
    
    /**
     * Route cache'ini temizle
     */
    public function clearRouteCache(): void;
}