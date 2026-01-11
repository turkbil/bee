<?php

namespace Modules\Muzibu\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

/**
 * Muzibu Admin Middleware
 *
 * Muzibu admin sayfalarına central domain'den (tuufi.com) erişildiğinde
 * Muzibu tenant context'ini başlatır.
 *
 * Bu middleware olmadan MuzibuCorporateAccount ve diğer Muzibu modelleri
 * central database'e sorgu atar (boş sonuç döner).
 */
class InitializeMuzibuAdmin
{
    /**
     * Muzibu Tenant ID (sabit)
     */
    protected const MUZIBU_TENANT_ID = 1001;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Tenancy zaten başlatılmışsa ve Muzibu tenant değilse, Muzibu'yu başlat
        if (function_exists('tenancy')) {
            $tenancy = tenancy();

            // Eğer hiç tenant başlatılmamışsa veya central tenant ise
            if (!$tenancy->initialized || (tenant() && tenant()->central)) {
                $this->initializeMuzibuTenant();
            }
        }

        return $next($request);
    }

    /**
     * Muzibu tenant'ını başlat
     */
    protected function initializeMuzibuTenant(): void
    {
        try {
            $tenant = Tenant::find(self::MUZIBU_TENANT_ID);

            if ($tenant) {
                tenancy()->initialize($tenant);
            } else {
                Log::warning('Muzibu Admin: Tenant not found', [
                    'tenant_id' => self::MUZIBU_TENANT_ID
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Muzibu Admin: Failed to initialize tenant', [
                'tenant_id' => self::MUZIBU_TENANT_ID,
                'error' => $e->getMessage()
            ]);
        }
    }
}
