<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom Guest Middleware
 * 
 * Device limit exceeded durumunda authenticated user'a login sayfasına izin verir
 */
class RedirectIfAuthenticatedExceptDeviceLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // 🔥 POST request'lerde middleware'i atla (login işlemi sırasında)
        // Login işlemi tamamlandıktan sonra controller'da redirect yapılır
        if ($request->isMethod('POST')) {
            return $next($request);
        }

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // 🔐 Device limit exceeded ise login sayfasına izin ver (modal göstermek için)
                if (session('device_limit_exceeded')) {
                    \Log::info('🔐 GUEST MIDDLEWARE: Device limit exceeded, allowing access to login page', [
                        'user_id' => Auth::id(),
                        'route' => $request->route()->getName(),
                    ]);
                    return $next($request);
                }

                // Normal durum: authenticated user guest sayfalarına giremez
                return redirect('/');
            }
        }

        return $next($request);
    }
}
