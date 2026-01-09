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
     *
     * Authenticated kullanıcıları guest sayfalarından (login/register) yönlendirir
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // POST request'lerde middleware'i atla (login işlemi sırasında)
        if ($request->isMethod('POST')) {
            return $next($request);
        }

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Authenticated user guest sayfalarına giremez
                return redirect('/');
            }
        }

        return $next($request);
    }
}
