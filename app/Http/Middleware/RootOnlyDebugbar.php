<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (class_exists(\Barryvdh\Debugbar\Facade::class) && config('debugbar.admin_only', false)) {

            // Giriş yapmış kullanıcı kontrolü
            if (auth()->check()) {
                $user = auth()->user();

                // Root rolü yoksa debugbar'ı devre dışı bırak
                if (!$user->hasRole('root')) {
                    \Barryvdh\Debugbar\Facade::disable();
                }
            } else {
                // Giriş yapmamışsa debugbar'ı devre dışı bırak
                \Barryvdh\Debugbar\Facade::disable();
            }
        }

        return $next($request);
    }
}
