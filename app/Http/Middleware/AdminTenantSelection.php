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
            // Manuel tenant seçimi - Sen tenant 1 kullan
            $defaultTenantId = 1; // Nurullah'ın tenant'ı
            Session::put('admin_selected_tenant_id', $defaultTenantId);
            
            \Log::info('AdminTenantSelection: Default tenant selected', [
                'tenant_id' => $defaultTenantId,
                'source' => 'manual_default'
            ]);
        }
        
        return $next($request);
    }
}