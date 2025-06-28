<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCacheBypass
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Eğer kullanıcı giriş yapmışsa AGRESİF cache temizleme
        if (auth()->check()) {
            // Response cache'i temizle (request'ten ÖNCE)
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            }
            
            // Laravel cache'i de temizle
            try {
                \Artisan::call('cache:clear');
                \Artisan::call('view:clear');
                \Artisan::call('config:clear');
                \Artisan::call('route:clear');
            } catch (\Exception $e) {
                \Log::warning('AuthCacheBypass middleware cache clear error: ' . $e->getMessage());
            }
            
            // OPCache'i de temizle (eğer varsa)
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
        }
        
        $response = $next($request);
        
        // Eğer kullanıcı giriş yapmışsa ULTRA AGRESİF cache bypass header'ları
        if (auth()->check()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, s-maxage=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Mon, 01 Jan 1990 00:00:00 GMT');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            $response->headers->set('Vary', 'Authorization, Cookie, Accept-Encoding');
            $response->headers->set('X-Auth-Cache-Bypass', 'true');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, nocache');
            $response->headers->set('X-Accel-Expires', '0');
            $response->headers->set('Surrogate-Control', 'no-store');
            $response->headers->set('X-Force-Refresh', time());
            
            // ETag'i kaldır
            $response->headers->remove('ETag');
            $response->headers->remove('If-None-Match');
        }
        
        return $response;
    }
}