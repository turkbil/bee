<?php

namespace Modules\LanguageManagement\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CentralDomainOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Root kullanıcısı her zaman erişebilir
        if (auth()->check() && auth()->user()->hasRole('root')) {
            return $next($request);
        }

        // 2. Central tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            $tenant = tenant();
            if ($tenant->central) {
                return $next($request);
            }
        }

        // 3. Tenant context'i yoksa (central domain'deyiz)
        if (!function_exists('tenant') || !tenant()) {
            return $next($request);
        }

        abort(403, 'Bu sayfaya sadece merkezi domain veya root kullanıcısı erişebilir.');
    }
}