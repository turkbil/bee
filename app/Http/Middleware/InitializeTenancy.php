<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class InitializeTenancy extends BaseMiddleware
{
    protected $tenancy;
    protected $resolver;
    
    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }
    
    public function handle($request, Closure $next)
    {
        // Tenancy zaten başlatılmışsa tekrar başlatma
        if ($this->tenancy->initialized) {
            return $next($request);
        }
        
        $host = $request->getHost();
        
        try {
            // Domain bilgisini al
            $domain = DB::table('domains')->where('domain', $host)->first();
            
            if (!$domain) {
                abort(404, 'Domain bulunamadı');
            }
            
            // Tenant bilgilerini al
            $tenant = DB::table('tenants')
                ->where('id', $domain->tenant_id)
                ->first(['id', 'is_active']);
            
            if (!$tenant) {
                abort(404, 'Tenant bulunamadı');
            }
            
            // Tenant pasif ise offline sayfasına yönlendir
            if (!$tenant->is_active) {
                return response()->view('errors.offline', ['domain' => $host], 503);
            }
            
            // Tenant modelini al ve başlat - HER ZAMAN!
            $tenantModel = Tenant::find($domain->tenant_id);
            
            if (!$tenantModel) {
                abort(404, 'Tenant modeli bulunamadı');
            }
            
            // Tenant'ı başlat
            $this->tenancy->initialize($tenantModel);
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Tenant başlatma hatası: ' . $e->getMessage());
            abort(500, 'Tenant başlatılamadı');
        }
    }
}