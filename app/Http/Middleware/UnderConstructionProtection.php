<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnderConstructionProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔴 DEVRE DIŞI - JS tabanlı şifre koruması kullanılıyor
        // Layout'taki JavaScript ile kontrol ediliyor (resources/views/themes/muzibu/layouts/app.blade.php)
        return $next($request);
    }
}
