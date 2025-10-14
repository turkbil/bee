<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixLegacyTenantUrls
{
    /**
     * Handle an incoming request - eski tenant URL'lerini düzelt
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Sadece HTML response'ları işle
        if ($response->headers->get('Content-Type') && 
            str_contains($response->headers->get('Content-Type'), 'text/html')) {
            
            $content = $response->getContent();
            
            // /storage/tenant{id}/ → /storage/ değişimi
            $content = preg_replace('#/storage/tenant\d+/#', '/storage/', $content);
            
            $response->setContent($content);
        }

        return $response;
    }
}
