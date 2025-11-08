<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ✅ TEMEL GÜVENLİK HEADER'LARI (Sorunsuz)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade'); // Daha gevşek

        // ✅ HSTS - Force HTTPS (1 year)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // ❌ CROSS-ORIGIN POLICIES KALDIRILDI
        // GoogleBot ve tracking script'leri için sorun yaratıyordu
        // Cross-Origin-Embedder-Policy, Cross-Origin-Opener-Policy tamamen kaldırıldı
        $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin'); // Sadece bu kalsın

        // ❌ CONTENT SECURITY POLICY KALDIRILDI
        // AdBlock bypass ve tracking için CSP tamamen devre dışı
        // Gerekirse daha sonra gevşek bir CSP eklenebilir

        // ✅ CACHE CONTROL - Sadece admin için
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        // ✅ REMOVE SERVER SIGNATURE
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
