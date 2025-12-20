<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixResponseCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip admin paths
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            return $response;
        }

        // AGGRESSIVE CACHE HEADER REPLACEMENT (Works for all GET responses!)
        $response->headers->remove('Cache-Control');
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');

        $response->headers->set('Cache-Control', 'public, max-age=3600, immutable');
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
