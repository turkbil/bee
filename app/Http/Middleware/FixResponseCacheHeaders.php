<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixResponseCacheHeaders
{
    /**
     * Dinamik sayfalar - asla cache'lenmemeli
     * (Kullanıcıya özel içerik, favori/playlist değişiklikleri vb.)
     */
    protected array $noCachePaths = [
        'favorites',
        'favorites/*',
        'my-playlists',
        'my-playlists/*',
        'playlist/*/edit',
        'dashboard',
        'dashboard/*',
        'listening-history',
        'listening-history/*',
        'corporate/*',
        'api/*',
        'cart',
        'cart/*',
        'checkout',
        'checkout/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip admin paths
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            return $response;
        }

        // AUTH GEREKTİREN DİNAMİK SAYFALAR - CACHE YOK!
        // Kullanıcıya özel içerik, favoriler, playlistler vb.
        if (auth()->check()) {
            foreach ($this->noCachePaths as $pattern) {
                if ($request->is($pattern)) {
                    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
                    $response->headers->set('Pragma', 'no-cache');
                    $response->headers->set('Expires', '0');
                    $response->setPrivate();
                    return $response;
                }
            }
        }

        // SADECE STATİK PUBLIC SAYFALAR İÇİN CACHE
        // (Anasayfa, liste sayfaları, detay sayfaları vb.)
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
