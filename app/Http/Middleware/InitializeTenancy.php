<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

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
                $tenant = $this->resolver->resolve($host);
    
                if ($tenant) {
                    $this->tenancy->initialize($tenant);
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