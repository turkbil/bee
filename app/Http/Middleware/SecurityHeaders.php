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

        // Security Headers - 2025 Best Practices
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS - Force HTTPS (1 year)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Cross-Origin Policies
        // COEP: credentialless allows cross-origin resources without CORS/CORP headers
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');
        // COOP: same-origin-allow-popups yerine - login/session sorunlarını önler
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin');

        // Content Security Policy - Strict but CDN-compatible
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://www.googletagmanager.com https://www.google-analytics.com https://mc.yandex.ru https://mc.yandex.com",
            "worker-src 'self' blob:",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://rsms.re https://fonts.googleapis.com",
            "img-src 'self' data: https: blob:",
            "font-src 'self' data: https: https://fonts.gstatic.com",
            "connect-src 'self' https://cdn.jsdelivr.net https://www.google-analytics.com https://stats.g.doubleclick.net https://mc.yandex.ru https://mc.yandex.com wss://mc.yandex.com",
            "frame-src 'self' https://mc.yandex.ru https://mc.yandex.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Cache Control - Sadece admin için set et, ResponseCache'i ezme
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }
        // Frontend sayfalar için Cache-Control'u EZME - ResponseCache middleware'i yönetir

        // Remove server signature
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
