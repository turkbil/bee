<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminNoCacheMiddleware
{
    /**
     * Admin panel'de TÜM cache ve Redis işlemlerini devre dışı bırak
     */
    public function handle(Request $request, Closure $next)
    {
        // Admin panel kontrolü
        if ($request->is('admin/*') || str_contains($request->path(), 'admin')) {
            
            Log::info('🚫 ADMIN CACHE KILLER AKTIF', [
                'path' => $request->path(),
                'url' => $request->url()
            ]);
            
            // Cache'i devre dışı bırakmak için config'i değiştir
            config(['cache.default' => 'array']); // Array driver cache'i RAM'de tutar, request sonunda kaybolur
            
            // Redis connection'ını da devre dışı bırak
            config(['database.redis.default.host' => '127.0.0.1']);
            config(['database.redis.options.prefix' => 'admin_disabled_']);
            
            Log::info('✅ Admin panel cache/Redis tamamen bypass edildi');
        }

        return $next($request);
    }
}