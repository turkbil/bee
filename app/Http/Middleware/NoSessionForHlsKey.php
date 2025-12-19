<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HLS Encryption Key endpoint için session'ı bypass et
 * 
 * Session middleware cache header'larını override ediyor:
 * Cache-Control: private, must-revalidate (YANLIŞ!)
 * 
 * Bu middleware, encryption key endpoint'i için:
 * Cache-Control: public, max-age=86400 (DOĞRU!)
 */
class NoSessionForHlsKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // Disable session for this request
        config(['session.driver' => 'array']); // Temporary in-memory session (won't persist)
        
        $response = $next($request);

        // Force cache headers AFTER middleware pipeline
        // 🔧 FIX: Support both old (/api/) and new (/hls-key/) paths
        if ($request->is('hls-key/muzibu/songs/*') || $request->is('*/hls-key/muzibu/songs/*') ||
            $request->is('api/muzibu/songs/*/key') || $request->is('*/api/muzibu/songs/*/key')) {
            $response->headers->set('Cache-Control', 'public, max-age=86400, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
            $response->headers->remove('Pragma');
            $response->headers->remove('Set-Cookie'); // Remove ALL cookies
        }

        return $response;
    }
}
