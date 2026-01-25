<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Domain;

class RedirectToPrimaryDomain
{
    /**
     * www.domain.com → domain.com redirect (veya tersi)
     * Primary domain'e yönlendirir
     */
    public function handle(Request $request, Closure $next)
    {
        // Sadece tenant context'te çalış
        if (!tenant()) {
            return $next($request);
        }

        $currentHost = $request->getHost();
        $tenantId = tenant('id');

        // Primary domain'i al
        $primaryDomain = Domain::where('tenant_id', $tenantId)
            ->where('is_primary', true)
            ->first();

        if (!$primaryDomain) {
            return $next($request);
        }

        // Eğer current host primary domain'den farklıysa redirect yap
        if ($currentHost !== $primaryDomain->domain) {
            $url = $request->getScheme() . '://' . $primaryDomain->domain . $request->getRequestUri();
            return redirect()->away($url, 301);
        }

        return $next($request);
    }
}
