<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminNoCacheMiddleware
{
    /**
     * Admin panelinde cache'i mutlak engelle
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cache bypass header'ları ekle
        $request->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $request->headers->set('Pragma', 'no-cache');
        $request->headers->set('Expires', '0');
        
        // Response cache bypass için özel header
        $request->headers->set('X-Cache-Bypass', 'admin');
        
        $response = $next($request);
        
        // Response header'larını da ekle - Güçlü cache engelleme
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', '');
        $response->headers->set('X-Admin-No-Cache', 'true');
        
        return $response;
    }
}