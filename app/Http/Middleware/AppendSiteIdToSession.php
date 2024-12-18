<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\Tenant;

class AppendSiteIdToSession
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $tenant = tenancy()->tenant;

        if ($tenant) {
            $sessionId = session()->getId();
            session(['tenant_id' => $tenant->id]);

            DB::table('sessions')->where('id', $sessionId)->update([
                'tenant_id' => $tenant->id,
            ]);
        }

        return $response;
    }

}
