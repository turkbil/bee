<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\TenantHelpers;

class RootAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Root yetkisi kontrolü
        if (!auth()->user() || !auth()->user()->isRoot()) {
            abort(403, 'ERİŞİM ENGELENDİ.');
        }
        
        // Tenant kontrolü - sadece central'da erişime izin ver
        if (TenantHelpers::isTenant()) {
            abort(403, 'ERİŞİM ENGELENDİ.');
        }
        
        return $next($request);
    }
}