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
use Illuminate\Support\Facades\Cache;
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
        
        // Central domainleri config'den kontrol et
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($host, $centralDomains)) {
            // Config'de tanımlı central domain ise tenancy başlatma
            return $next($request);
        }
        
        try {
            // Cache key oluştur - 15 dakika cache
            $cacheKey = "tenant_domain_data:{$host}";
            
            $tenantData = Cache::remember($cacheKey, 60 * 15, function() use ($host) {
                // Domain ve tenant bilgilerini tek sorguda al
                $data = DB::table('domains')
                    ->join('tenants', 'domains.tenant_id', '=', 'tenants.id')
                    ->where('domains.domain', $host)
                    ->select([
                        'domains.tenant_id',
                        'tenants.id',
                        'tenants.central',
                        'tenants.is_active'
                    ])
                    ->first();
                    
                return $data ? (array) $data : null;
            });
            
            if (!$tenantData) {
                abort(404, 'Domain bulunamadı');
            }
            
            // Tenant pasif ise admin ve login hariç offline sayfasına yönlendir
            if (!$tenantData['is_active']) {
                // Sadece admin ve login rotalarına izin ver
                if ($request->is('admin') || $request->is('admin/*') || $request->is('login')) {
                    // Admin sayfalarına devam et
                } else {
                    return response()->view('errors.offline', ['domain' => $host], 503);
                }
            }
            
            // Tenant central ise tenancy başlatma
            if ($tenantData['central']) {
                return $next($request);
            }
            
            // Tenant modelini cache'den al
            $tenantCacheKey = "tenant_model:{$tenantData['tenant_id']}";
            $tenantModel = Cache::remember($tenantCacheKey, 60 * 30, function() use ($tenantData) {
                return Tenant::find($tenantData['tenant_id']);
            });
            
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