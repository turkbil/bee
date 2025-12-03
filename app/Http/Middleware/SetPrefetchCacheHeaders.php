<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPrefetchCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Sadece frontend GET request'leri için
        if ($request->isMethod('GET') && !$request->is('admin/*') && !$request->ajax()) {
            // Successful response'lar için aggressive cache
            if ($response->isSuccessful() && !$response->isRedirection()) {
                // Prefetch için cache header'larını zorla set et
                $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');
                $response->headers->remove('Pragma');

                // Expires header'ı ekle (1 saat sonra)
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            }
        }

        return $response;
    }
}
