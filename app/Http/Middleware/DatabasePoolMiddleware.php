<?php

namespace App\Http\Middleware;

use App\Services\DatabaseConnectionPoolService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class DatabasePoolMiddleware
{
    protected $poolService;

    public function __construct(DatabaseConnectionPoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Tenancy middleware'den sonra çalışması için tenant bilgisini al
        $tenant = tenant();
        
        if ($tenant) {
            $tenantKey = $tenant->getTenantKey();
            
            try {
                // Tenant için connection pool'dan connection al
                $connectionName = $this->poolService->getTenantConnection($tenantKey);
                
                // Request'e connection bilgisini ekle (opsiyonel debugging için)
                $request->attributes->set('pool_connection', $connectionName);
                
                // Log::debug('DatabasePool middleware: Connection assigned', [
                //     'tenant' => $tenantKey,
                //     'connection' => $connectionName,
                //     'route' => $request->route()?->getName(),
                // ]);
                
            } catch (\Exception $e) {
                Log::error('DatabasePool middleware: Connection failed', [
                    'tenant' => $tenantKey,
                    'error' => $e->getMessage(),
                    'route' => $request->route()?->getName(),
                ]);
                
                // Hata durumunda varsayılan connection kullan
                throw $e;
            }
        }

        $response = $next($request);

        // Response sonrası cleanup (opsiyonel)
        if ($tenant && isset($connectionName)) {
            // Connection'ı release et (idle pool'a gönder)
            $this->poolService->releaseConnection($connectionName);
            
            // Log::debug('DatabasePool middleware: Connection released', [
            //     'tenant' => $tenantKey,
            //     'connection' => $connectionName,
            // ]);
        }

        return $response;
    }
}