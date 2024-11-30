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

                                     // Tenant bilgisi alınıyor
        $tenant = tenancy()->tenant; // Stancl Tenancy ile tenant bilgisi

        // Tenant varsa
        if ($tenant) {
            // Tenant ID'yi session'a kaydediyoruz
            session(['tenant_id' => $tenant->id]);

            // Eğer 'sessions' tablosu varsa, tenant ID'sini burada da güncelleyebiliriz
            $sessionId = session()->getId();
            DB::table('sessions')->where('id', $sessionId)->update([
                'tenant_id' => $tenant->id,
            ]);
        }

        return $response;
    }

}
