<?php

namespace App\Services;

use Spatie\MissingPageRedirector\Redirector\Redirector;
use Symfony\Component\HttpFoundation\Request;

class TenantAwareRedirector implements Redirector
{
    public function getRedirectsFor(Request $request): array
    {
        $path = $request->getPathInfo();
        
        // Admin route'ları için yönlendirme yapma
        if (str_starts_with($path, '/admin')) {
            return [];
        }
        
        // API route'ları için yönlendirme yapma
        if (str_starts_with($path, '/api')) {
            return [];
        }
        
        // Storage ve asset dosyaları için yönlendirme yapma
        if (str_starts_with($path, '/storage') || 
            str_starts_with($path, '/assets') || 
            str_starts_with($path, '/css') || 
            str_starts_with($path, '/js')) {
            return [];
        }
        
        // Login/logout ve auth route'ları için yönlendirme yapma
        if (str_starts_with($path, '/login') || 
            str_starts_with($path, '/logout') || 
            str_starts_with($path, '/register') || 
            str_starts_with($path, '/password') ||
            str_starts_with($path, '/auth')) {
            return [];
        }
        
        // Tenant anasayfasına yönlendir (/ route'u)
        return [
            $path => '/',
        ];
    }
}