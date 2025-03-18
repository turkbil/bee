<?php namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantConnection;
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
            // Cache anahtarı oluştur
            $cacheKey = "modules_tenant_" . ($tenantId ?? 'central');
            
            // Cache'den sonuçları kontrol et - önbellek kullanımı
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Bu işlem her zaman central veritabanında yapılmalı
            // TenantConnection::central() metodunu kullanarak central veritabanına bağlan
            return TenantConnection::central(function() use ($tenantId, $cacheKey) {
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
                    $emptyResult = collect();
                    Cache::put($cacheKey, $emptyResult, now()->addMinutes(60));
                    return $emptyResult;
                }
                
                // Tenant ID varsa, module_tenants tablosundaki ilişkileri kontrol et
                if ($tenantId) {
                    // Tenant'a ait central değerini kontrol et
                    $tenantCentral = DB::table('tenants')
                        ->where('id', $tenantId)
                        ->value('central');
                    
                    // Eğer tenant central=1 (true) ise tüm modülleri göster
                    if ($tenantCentral) {
                        $result = $modules->map(function($module) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = true;
                            return $module;
                        });
                        
                        // Sonuçları önbelleğe al
                        Cache::put($cacheKey, $result, now()->addMinutes(60));
                        return $result;
                    }
                    
                    // Central değilse, tenant için tanımlı modüllere bak
                    $tenantModuleIds = DB::table($moduleTenantsTable)
                        ->where('tenant_id', $tenantId)
                        ->where('is_active', 1)
                        ->pluck('module_id')
                        ->toArray();
                    
                    // Eğer tenant için modül ilişkisi yoksa, tüm modülleri kullanılabilir yap
                    // Bu, tenant için henüz özel ayar yapılmadığını gösterir
                    if (empty($tenantModuleIds)) {
                        // Tüm modülleri tenant için etkinleştir
                        $result = $modules->map(function($module) {
                            $module->origin = 'central';
                            $module->is_tenant_enabled = true; // Varsayılan olarak etkin
                            return $module;
                        });
                        
                        // Sonuçları önbelleğe al
                        Cache::put($cacheKey, $result, now()->addMinutes(60));
                        return $result;
                    }
                    
                    // Her bir modüle tenant bilgisi ekle
                    $result = $modules->map(function($module) use ($tenantModuleIds) {
                        $module->origin = 'central';
                        // module_id kullan, id değil
                        $module->is_tenant_enabled = in_array($module->module_id, $tenantModuleIds);
                        return $module;
                    });
                    
                    // Sadece tenant için aktif olan modülleri filtrele
                    $filteredResult = $result->filter(function($module) {
                        return $module->is_tenant_enabled === true;
                    });
                    
                    // Sonuçları önbelleğe al
                    Cache::put($cacheKey, $filteredResult, now()->addMinutes(60));
                    return $filteredResult;
                }
                
                // Central için tüm modülleri göster
                $result = $modules->map(function($module) {
                    $module->origin = 'central';
                    $module->is_tenant_enabled = true;
                    return $module;
                });
                
                // Sonuçları önbelleğe al
                Cache::put($cacheKey, $result, now()->addMinutes(60));
                return $result;
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