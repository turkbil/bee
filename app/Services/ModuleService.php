<?php namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

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
            // Bu işlem her zaman central veritabanında yapılmalı
            return TenantHelpers::central(function() use ($tenantId) {
                // Açıkça central veritabanındaki modules tablosunu belirt
                $modulesTable = 'modules';
                $moduleTenantsTable = 'module_tenants';
                
                // Tüm aktif modülleri al
                $modules = DB::table($modulesTable)
                    ->where('is_active', 1)
                    ->orderBy('display_name')
                    ->get();
                
                // Eğer modül yoksa boş bir koleksiyon döndür
                if ($modules->isEmpty()) {
                    return collect();
                }
                
                // Tenant ID varsa, module_tenants tablosundaki ilişkileri kontrol et
                if ($tenantId) {
                    // Tenant'a ait modül ve central değerini tek sorguda al
                    $tenantData = DB::table('tenants')
                        ->where('id', $tenantId)
                        ->select('central')
                        ->first();
                    
                    // Eğer tenant bulunamazsa boş koleksiyon döndür
                    if (!$tenantData) {
                        return collect();
                    }
                    
                    // Eğer tenant central=1 (true) ise tüm modülleri göster
                    if ($tenantData->central) {
                        return $modules->map(function($module) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = true;
                            return $module;
                        });
                    }
                    
                    // Aktif modülleri tek sorguda al
                    $tenantModules = DB::table($moduleTenantsTable)
                        ->where('tenant_id', $tenantId)
                        ->where('is_active', 1)
                        ->pluck('module_id')
                        ->toArray();
                    
                    // Eğer tenant için modül ilişkisi yoksa, tüm modülleri kullanılabilir yap
                    if (empty($tenantModules)) {
                        return $modules->map(function($module) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = true;
                            return $module;
                        });
                    }
                    
                    // Her bir modüle tenant bilgisi ekle
                    $result = $modules->map(function($module) use ($tenantModules) {
                        $module->origin = 'central';
                        $module->is_tenant_enabled = in_array($module->module_id, $tenantModules);
                        return $module;
                    });
                    
                    // Sadece tenant için aktif olan modülleri filtrele
                    return $result->filter(function($module) {
                        return $module->is_tenant_enabled === true;
                    });
                }
                
                // Central için tüm modülleri göster
                return $modules->map(function($module) {
                    $module->origin = 'central';
                    $module->is_tenant_enabled = true;
                    return $module;
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