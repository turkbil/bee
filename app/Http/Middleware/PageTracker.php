<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PageTracker
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // İstek öncesi bilgileri topla
        $preRequestData = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'auth_status' => auth()->check() ? 'authenticated' : 'guest',
            'user_id' => auth()->check() ? auth()->id() : 'guest',
            'session_id' => session()->getId(),
            'app_locale_before' => app()->getLocale(),
            'session_data' => [
                'admin_locale' => session('admin_locale'),
                'site_locale' => session('site_locale'),
                'site_locale_laravel_test' => session('site_locale_laravel_test'),
            ],
            'request_headers' => [
                'cache_control' => $request->header('cache-control'),
                'pragma' => $request->header('pragma'),
                'if_none_match' => $request->header('if-none-match'),
                'if_modified_since' => $request->header('if-modified-since'),
            ]
        ];
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        // Yanıt sonrası bilgileri topla
        $postRequestData = [
            'status_code' => $response->getStatusCode(),
            'app_locale_after' => app()->getLocale(),
            'response_headers' => [
                'cache_control' => $response->headers->get('cache-control'),
                'pragma' => $response->headers->get('pragma'),
                'expires' => $response->headers->get('expires'),
                'x_auth_cache_bypass' => $response->headers->get('x-auth-cache-bypass'),
            ],
            'duration_ms' => $duration
        ];
        
        // Log seviyesi: sadece local/staging'de info, production'da devre dışı
        if (app()->environment(['local', 'staging'])) {
            \Log::info('🎯 PAGE TRACKER', array_merge($preRequestData, $postRequestData));
        }
        
        return $response;
    }
}