<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // En azından bir rol kontrolü yapalım
        if (!auth()->check() || 
            (!auth()->user()->isRoot() && 
             !auth()->user()->isAdmin() && 
             !auth()->user()->isEditor())) {
            abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }
        return $next($request);
    }
}