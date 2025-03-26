<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RootAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->isRoot()) {
            abort(403, 'Bu alana sadece Root kullanıcılar erişebilir.');
        }
        
        return $next($request);
    }
}