<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TenantCacheMiddleware
{
    /**
     * Tenant bazlı önbellek yapılandırmasını işle.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant()) {
            $tenantId = tenant('id');
            
            // Tenant bazlı önbellek yapılandırmasını ayarla
            config([
                'cache.prefix' => 'tenant_' . $tenantId . '_cache_',
                'session.cookie' => 'tenant_' . $tenantId . '_session',
            ]);
            
            // Redis için tenant bazlı önbellek anahtarı prefixini ayarla
            config([
                'database.redis.options.prefix' => 'tenant_' . $tenantId . '_',
            ]);
        }
        
        return $next($request);
    }
}