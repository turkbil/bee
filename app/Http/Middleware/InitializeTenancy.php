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
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains');
        
        // Eğer merkezi domain ise, herhangi bir tenant başlatma
        if (in_array($host, $centralDomains)) {
            return $next($request);
        }
        
        // Tenant domain'se tenant'ı başlat
        try {
            if (!$this->tenancy->initialized) {
                // Domain için tenant bilgisini önbellekten oku - 1 hafta sakla
                $cacheKey = 'domain_tenant_' . $host;
                $tenant = Cache::remember($cacheKey, now()->addDays(7), function () use ($host) {
                    return $this->resolver->resolve($host);
                });
                
                if ($tenant) {
                    // Tenant aktif değilse özel hata sayfasını göster
                    if (!$tenant->is_active) {
                        return response()->view('errors.offline', [], 503);
                    }
                    
                    $this->tenancy->initialize($tenant);
                    
                    // Tenant başlatıldıktan sonra bağlantı ayarını mysql olarak düzelt
                    Config::set('database.connections.tenant.driver', 'mysql');
                    DB::purge('tenant');
                    
                    // Redis önbellek için tenant prefix'i ayarla
                    $redisPrefix = 'tenant_' . $tenant->id . ':';
                    Config::set('database.redis.options.prefix', $redisPrefix);
                    
                    // Session'a tenant bilgisi kaydet - Tek sorguda domain bilgisini de getir
                    if (!isset($tenant->domains)) {
                        // Domain bilgisi cache'de sakla - 7 gün
                        $domainCacheKey = 'tenant_domains_' . $tenant->id;
                        $domains = Cache::remember($domainCacheKey, now()->addDays(7), function () use ($tenant) {
                            return $tenant->domains()->get();
                        });
                        $tenant->setRelation('domains', $domains);
                    }
                    
                    // Tenant şemasını kontrolü optimize etmek için önbelleğe alalım
                    $schemaCheckKey = 'tenant_schema_' . $tenant->id . '_exists';
                    $schemaExists = Cache::remember($schemaCheckKey, now()->addDays(7), function () use ($tenant) {
                        return Schema::hasTable('migrations');
                    });
                    
                    session(['current_tenant' => $tenant]);
                } else {
                    abort(404, 'Tenant bulunamadı');
                }
            }
            
            return $next($request);
        } catch (\Exception $e) {
            logger()->error('Tenant başlatma hatası: ' . $e->getMessage());
            abort(500, 'Tenant başlatılamadı');
        }
    }
}