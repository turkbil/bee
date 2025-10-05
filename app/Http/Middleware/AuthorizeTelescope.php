<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizeTelescope
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!auth()->check()) {
            return redirect('/admin/login');
        }

        // Root veya Admin rolü yoksa 403
        if (!$request->user()->hasAnyRole(['root', 'admin'])) {
            abort(403, 'Telescope\'a erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
