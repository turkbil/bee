<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RootOnlyDebugbar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debugbar kontrolü - sadece root kullanıcılarına göster
        if (!class_exists(\Barryvdh\Debugbar\Facade::class) || !config('debugbar.admin_only', false)) {
            return $next($request);
        }

        // Varsayılan: debugbar kapalı
        $showDebugbar = false;

        // Sadece giriş yapmış kullanıcılar için kontrol yap
        if (auth()->check()) {
            $userId = auth()->id();

            // Session'da root flag kontrolü (DB sorgusu yapmadan)
            $sessionKey = "is_root_user_{$userId}";

            if (session()->has($sessionKey)) {
                // Session'dan al (0 query)
                $showDebugbar = session($sessionKey);
            } else {
                // İlk kez: DB'den kontrol et ve session'a kaydet
                $isRoot = Cache::remember("user_is_root_{$userId}", 300, function () {
                    return auth()->user()?->hasRole('root') ?? false;
                });
                session([$sessionKey => $isRoot]);
                $showDebugbar = $isRoot;
            }
        }

        if (!$showDebugbar) {
            \Barryvdh\Debugbar\Facade::disable();
        }

        return $next($request);
    }
}
