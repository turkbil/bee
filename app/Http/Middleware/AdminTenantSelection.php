<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminTenantSelection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Admin panel'de tenant seçimi yapıldı mı kontrol et
        $selectedTenantId = $request->get('tenant_id') ?? Session::get('admin_selected_tenant_id');
        
        if ($selectedTenantId) {
            // Session'a kaydet
            Session::put('admin_selected_tenant_id', $selectedTenantId);
            
            \Log::info('AdminTenantSelection: Tenant selected', [
                'tenant_id' => $selectedTenantId,
                'source' => $request->get('tenant_id') ? 'request_param' : 'session'
            ]);
        } else {
            // Auto-detect tenant by domain
            $host = $request->getHost();
            $tenantId = null;
            
            // Domain'e göre tenant_id tespit et
            if ($host === 'laravel.test') {
                $tenantId = 1; // Central/main tenant
            } elseif ($host === 'a.test') {
                $tenantId = 2;
            } elseif ($host === 'b.test') {
                $tenantId = 3;
            } elseif ($host === 'c.test') {
                $tenantId = 4;
            } else {
                // Fallback: database'den domain ara
                $tenant = \App\Models\Tenant::whereHas('domains', function($query) use ($host) {
                    $query->where('domain', $host);
                })->first();
                
                $tenantId = $tenant ? $tenant->id : 1; // Default tenant 1
            }
            
            Session::put('admin_selected_tenant_id', $tenantId);
            
            \Log::info('AdminTenantSelection: Tenant auto-detected', [
                'tenant_id' => $tenantId,
                'host' => $host,
                'source' => 'domain_detection'
            ]);
        }
        
        return $next($request);
    }
}