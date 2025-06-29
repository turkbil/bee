<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Database\Models\Domain;
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
            
            $domainModel = Cache::remember($cacheKey, 60 * 15, function() use ($host) {
                // Stancl API ile domain ve tenant bilgilerini al
                return Domain::with('tenant')->where('domain', $host)->first();
            });
            
            if (!$domainModel || !$domainModel->tenant) {
                // Tenant bulunamadı - özel error sayfası göster
                return response()->view('errors.tenant-not-found', [
                    'domain' => $host,
                    'message' => 'Bu domain için aktif bir site bulunamadı.'
                ], 404);
            }
            
            $tenant = $domainModel->tenant;
            
            // Tenant pasif ise admin ve login hariç offline sayfasına yönlendir
            if (!$tenant->is_active) {
                // Sadece admin ve login rotalarına izin ver
                if ($request->is('admin') || $request->is('admin/*') || $request->is('login')) {
                    // Admin sayfalarına devam et
                } else {
                    return response()->view('errors.offline', ['domain' => $host], 503);
                }
            }
            
            // Tenant central ise tenancy başlatma
            if ($tenant->central) {
                return $next($request);
            }
            
            // Tenant'ı başlat
            $this->tenancy->initialize($tenant);
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Tenant başlatma hatası: ' . $e->getMessage(), [
                'host' => $host,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Genel hata sayfası göster
            return response()->view('errors.tenant-not-found', [
                'domain' => $host,
                'message' => 'Site yüklenirken bir hata oluştu.'
            ], 500);
        }
    }
}