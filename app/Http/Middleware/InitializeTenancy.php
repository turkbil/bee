<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
        // Eğer tenancy zaten başlatılmışsa tekrar başlatmayı engelle
        if ($this->tenancy->initialized) {
            return $next($request);
        }
        
        $host = $request->getHost();
        
        // Domain tipini önbellekten kontrol et (central mi tenant mi?)
        $domainTypeKey = 'domain_type_' . $host;
        $domainType = Cache::remember($domainTypeKey, now()->addHours(24), function() use ($host) {
            // Önce config dosyasından kontrol et
            $centralDomains = config('tenancy.central_domains', []);
            if (in_array($host, $centralDomains)) {
                return 'central';
            }
            
            // Eğer configde yoksa veritabanında kontrol et
            $domain = DB::table('domains')->where('domain', $host)->first(['tenant_id']);
            if ($domain) {
                // Tenant'ın central olup olmadığını kontrol et
                $isCentral = DB::table('tenants')
                    ->where('id', $domain->tenant_id)
                    ->value('central') ?? false;
                    
                if ($isCentral) {
                    return 'central';
                }
            }
            
            return 'tenant';
        });
        
        // Central domain ise tenant başlatma
        if ($domainType === 'central') {
            return $next($request);
        }
        
        // Tenant domain için işlemler
        try {
            // Domain için tenant ID'sini önbellekten al (daha uzun süre sakla)
            $tenantIdKey = 'domain_tenant_id_' . $host;
            $tenantId = Cache::remember($tenantIdKey, now()->addDay(), function() use ($host) {
                $domain = DB::table('domains')->where('domain', $host)->first(['tenant_id']);
                return $domain ? $domain->tenant_id : null;
            });
            
            if (!$tenantId) {
                Log::error("Tenant bulunamadı: {$host}");
                abort(404, 'Tenant bulunamadı');
            }
            
            // Tenant aktiflik kontrolünü önbellekten kontrol et
            $activeKey = 'tenant_active_' . $tenantId;
            $isActive = Cache::remember($activeKey, now()->addMinutes(15), function() use ($tenantId) {
                return DB::table('tenants')->where('id', $tenantId)->value('is_active') ?? false;
            });
            
            if (!$isActive) {
                Log::info("Tenant {$tenantId} pasif durumda. Offline sayfası gösteriliyor.");
                return response()->view('errors.offline', [], 503);
            }
            
            // Tenant modelini önbellekten getir
            $tenantKey = 'tenant_' . $tenantId;
            $tenant = Cache::remember($tenantKey, now()->addMinutes(30), function() use ($tenantId) {
                return $this->resolver->resolve($tenantId);
            });
            
            if (!$tenant) {
                Log::error("Tenant model bulunamadı: {$tenantId}");
                abort(404, 'Tenant model bulunamadı');
            }
            
            // Tenant'ı başlat
            $this->tenancy->initialize($tenant);
            
            // Redis önbellek prefix'i tenant bazlı olarak ayarla
            $redisPrefix = 'tenant_' . $tenant->id . ':';
            Config::set('database.redis.options.prefix', $redisPrefix);
            
            // Tenant'ı oturuma kaydet
            session(['current_tenant' => $tenant]);
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Tenant başlatma hatası: ' . $e->getMessage(), [
                'exception' => $e,
                'host' => $host
            ]);
            abort(500, 'Tenant başlatılamadı');
        }
    }
}