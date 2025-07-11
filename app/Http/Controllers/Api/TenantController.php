<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stancl\Tenancy\Facades\Tenancy;

class TenantController extends Controller
{
    /**
     * Get current tenant info
     */
    public function getCurrentTenant(Request $request)
    {
        try {
            // Tenant zaten domain ile belirleniyor
            $tenant = tenant();
            
            if (!$tenant) {
                return response()->json([
                    'message' => 'No tenant context found',
                    'tenant' => null
                ]);
            }

            return response()->json([
                'message' => 'Current tenant retrieved successfully',
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'is_active' => $tenant->is_active,
                    'plan' => $tenant->plan,
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve current tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tenant details with domain info
     */
    public function getTenantDetails(Request $request)
    {
        try {
            $tenant = tenant();
            
            if (!$tenant) {
                return response()->json([
                    'message' => 'No tenant context found',
                ], 404);
            }

            // Domain bilgilerini al
            $domains = $tenant->domains()->get(['domain', 'created_at']);

            return response()->json([
                'message' => 'Tenant details retrieved successfully',
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'is_active' => $tenant->is_active,
                    'plan' => $tenant->plan,
                    'domains' => $domains->map(function ($domain) {
                        return [
                            'domain' => $domain->domain,
                            'created_at' => $domain->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $tenant->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve tenant details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}