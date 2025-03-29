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
            
<<<<<<< HEAD
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
=======
            // 30 dakika boyunca cache'te tut
            return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($tenantId) {
                // Bu işlem her zaman central veritabanında yapılmalı
                return TenantHelpers::central(function() use ($tenantId) {
                    // Açıkça central veritabanındaki modules tablosunu belirt
                    $modulesTable = 'modules';
                    $moduleTenantsTable = 'module_tenants';
                    
                    // Tüm aktif modülleri al - sadece gerekli sütunları seç
                    $modules = DB::table($modulesTable)
                        ->where('is_active', 1)
                        ->select('module_id', 'name', 'display_name', 'type', 'is_active')
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
>>>>>>> 3a36f960d3cf4bc9e128a5027bf7de6fa612a1cd
                            ->where('id', $tenantId)
                            ->select('central')
                            ->first();
                        
<<<<<<< HEAD
                        if (!$tenant) {
                            return collect();
                        }
                        
                        // Central tenant ise tüm modülleri döndür
                        if ($tenant->central) {
                            $modules = $query->get();
=======
                        // Eğer tenant bulunamazsa boş koleksiyon döndür
                        if (!$tenantData) {
                            return collect();
                        }
                        
                        // Eğer tenant central=1 (true) ise tüm modülleri göster
                        if ($tenantData->central) {
>>>>>>> 3a36f960d3cf4bc9e128a5027bf7de6fa612a1cd
                            return $modules->map(function($module) {
                                $module->origin = 'central';
                                $module->is_tenant_enabled = true;
                                return $module;
                            });
                        }
                        
<<<<<<< HEAD
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
=======
                        // Tenant modüllerini tek sorguda al
                        $tenantModuleIds = DB::table($moduleTenantsTable)
                            ->where('tenant_id', $tenantId)
                            ->where('is_active', 1)
                            ->select('module_id')
                            ->pluck('module_id')
                            ->toArray();
                        
                        // Eğer tenant için modül ilişkisi yoksa, tüm modülleri kullanılabilir yap
                        if (empty($tenantModuleIds)) {
                            return $modules->map(function($module) {
                                $module->origin = 'central';
                                $module->is_tenant_enabled = true;
                                return $module;
                            });
                        }
                        
                        // Her bir modüle tenant bilgisi ekle
                        $result = $modules->map(function($module) use ($tenantModuleIds) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = in_array($module->module_id, $tenantModuleIds);
                            return $module;
                        });
                        
                        // Sadece tenant için aktif olan modülleri filtrele
>>>>>>> 3a36f960d3cf4bc9e128a5027bf7de6fa612a1cd
                        return $result->filter(function($module) {
                            return $module->is_tenant_enabled === true;
                        });
                    }
                    
                    // Central için tüm modülleri göster
<<<<<<< HEAD
                    $modules = $query->get();
=======
>>>>>>> 3a36f960d3cf4bc9e128a5027bf7de6fa612a1cd
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