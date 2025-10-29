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

        // Content Security Policy - Marketing & Analytics Platforms
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://www.googletagmanager.com https://www.google-analytics.com https://googleads.g.doubleclick.net https://connect.facebook.net https://www.facebook.com https://snap.licdn.com https://www.linkedin.com https://www.clarity.ms https://scripts.clarity.ms https://analytics.tiktok.com https://mc.yandex.ru https://mc.yandex.com https://instant.page",
            "worker-src 'self' blob:",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://rsms.re https://rsms.me https://fonts.googleapis.com https://www.googletagmanager.com",
            "img-src 'self' data: https: blob: https://www.googletagmanager.com https://www.google-analytics.com https://www.facebook.com https://px.ads.linkedin.com https://www.clarity.ms https://analytics.tiktok.com https://mc.yandex.ru",
            "font-src 'self' data: https: https://fonts.gstatic.com https://rsms.me https://rsms.re",
            "connect-src 'self' https://cdn.jsdelivr.net https://www.googletagmanager.com https://www.google-analytics.com https://stats.g.doubleclick.net https://www.googleadservices.com https://www.google.com https://googleads.g.doubleclick.net https://pagead2.googlesyndication.com https://www.facebook.com https://connect.facebook.net https://graph.facebook.com https://www.linkedin.com https://px.ads.linkedin.com https://www.clarity.ms https://scripts.clarity.ms https://b.clarity.ms https://analytics.tiktok.com https://mc.yandex.ru https://mc.yandex.com wss://mc.yandex.ru wss://mc.yandex.com",
            "frame-src 'self' https://www.googletagmanager.com https://www.facebook.com https://www.linkedin.com https://mc.yandex.ru https://mc.yandex.com",
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
