<?php namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Cache;

class ModuleService {
    /**
     * Tüm modülleri getirir, central veya tenant origin bilgisi ekler
     *
     * @param int|null $tenantId
     * @return Collection
     */
    public function getModulesByTenant(?int $tenantId = null): Collection
    {
        try {
            // Cache key oluştur
            $cacheKey = "modules_tenant_" . ($tenantId ?? 'central');
            
            // 6 saat boyunca cache'te tut (süreyi artırdık)
            return Cache::remember($cacheKey, now()->addHours(6), function() use ($tenantId) {
                // Bu işlem her zaman central veritabanında yapılmalı
                return TenantHelpers::central(function() use ($tenantId) {
                    // SQL sorgusunu optimize et - JOIN kullanarak tek seferde veri çek
                    $query = DB::table('modules')
                        ->where('modules.is_active', 1)
                        ->select(
                            'modules.module_id', 
                            'modules.name', 
                            'modules.display_name', 
                            'modules.type', 
                            'modules.is_active'
                        )
                        ->orderBy('modules.display_name');
                    
                    // Tenant ID varsa JOIN ile tenant ilişkisini kontrol et
                    if ($tenantId) {
                        // Önce tenant centralse kontrol et
                        $tenant = DB::table('tenants')
                            ->where('id', $tenantId)
                            ->select('central')
                            ->first();
                        
                        if (!$tenant) {
                            return collect();
                        }
                        
                        // Central tenant ise tüm modülleri döndür
                        if ($tenant->central) {
                            $modules = $query->get();
                            return $modules->map(function($module) {
                                $module->origin = 'central';
                                $module->is_tenant_enabled = true;
                                return $module;
                            });
                        }
                        
                        // Normal tenant ise JOIN ile ilişkili modülleri tek sorguda getir
                        $modules = $query
                            ->leftJoin('module_tenants', function($join) use ($tenantId) {
                                $join->on('modules.module_id', '=', 'module_tenants.module_id')
                                    ->where('module_tenants.tenant_id', '=', $tenantId)
                                    ->where('module_tenants.is_active', '=', 1);
                            })
                            ->select(
                                'modules.module_id', 
                                'modules.name', 
                                'modules.display_name', 
                                'modules.type', 
                                'modules.is_active',
                                'module_tenants.is_active as tenant_module_active'
                            )
                            ->get();
                        
                        // Modülleri işle
                        $result = $modules->map(function($module) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = !is_null($module->tenant_module_active);
                            unset($module->tenant_module_active); // Geçici alanı kaldır
                            return $module;
                        });
                        
                        // Sadece tenant için aktif modülleri filtrele
                        return $result->filter(function($module) {
                            return $module->is_tenant_enabled === true;
                        });
                    }
                    
                    // Central için tüm modülleri göster
                    $modules = $query->get();
                    return $modules->map(function($module) {
                        $module->origin = 'central';
                        $module->is_tenant_enabled = true;
                        return $module;
                    });
                });
            });
        } catch (\Exception $e) {
            Log::error('Module sorgusu hatası', [
                'error' => $e->getMessage(), 
                'tenant_id' => $tenantId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return collect();
        }
    }
    
    /**
     * Modülleri türlerine göre gruplar
     *
     * @param Collection $modules
     * @return Collection
     */
    public function groupModulesByType(Collection $modules): Collection
    {
        return $modules->groupBy('type');
    }
}