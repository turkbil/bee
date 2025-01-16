<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppendSiteIdToSession
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $tenant = tenancy()->tenant;

        if ($tenant) {
            session(['tenant_id' => $tenant->id]);
            cache()->put('tenant_id_' . session()->getId(), $tenant->id, now()->addMinutes(120));
        }

        return $response;
    }
}
