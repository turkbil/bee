<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;
use Modules\ModuleManagement\App\Models\Module;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ModuleAccessService
{
    /**
     * Kullanıcının belirtilen modül ve izin tipine erişimi olup olmadığını kontrol eder
     *
     * @param string $moduleName
     * @param string $permissionType
     * @return bool
     */
    public function canAccess(string $moduleName, string $permissionType = 'view'): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->is_active) {
            Log::warning("Kullanıcı yok veya aktif değil, modül erişimi reddedildi: {$moduleName}.{$permissionType}");
            return false;
        }
        
        // ROOT her zaman erişebilir
        if ($user->isRoot()) {
            return true;
        }
        
        // Modül aktif mi?
        $module = $this->getModuleByName($moduleName);
        if (!$module || !$module->is_active) {
            Log::warning("Modül bulunamadı veya aktif değil: {$moduleName}");
            return false;
        }
        
        // Tenant kontrolü
        $isTenant = TenantHelpers::isTenant();
        
        // ADMIN rolü kontrolü
        if ($user->isAdmin()) {
            // Tenant'ta ise modülün tenant'a atanmış olması gerekir
            if ($isTenant) {
                $isAssigned = $this->isModuleAssignedToTenant($module->module_id, tenant()->id);
                if (!$isAssigned) {
                    Log::info("Admin, tenant'a atanmamış modüle erişmeye çalışıyor: {$moduleName}");
                    return false;
                }
                return true;
            }
            
            // Central'da ise:
            
            // 1. Kısıtlı modüller (örn: tenantmanagement) sadece root erişebilir
            if (in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                Log::info("Admin, kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                return false;
            }
            
            // 2. Diğer tüm modüllere erişim ver
            return true;
        }
        
        // EDITOR için:
        if ($user->isEditor()) {
            // Rol çakışması kontrolü (güvenlik için)
            if ($user->hasRole('admin') || $user->hasRole('root')) {
                Log::warning("Kullanıcı {$user->id} hem Editor hem de admin/root rollerine sahip! Bu durum kontrol edilmeli.");
                return false;
            }
            
            // Tenant'ta ise önce modül atanmış mı kontrol et
            if ($isTenant && !$this->isModuleAssignedToTenant($module->module_id, tenant()->id)) {
                Log::info("Modül {$moduleName} tenant'a atanmamış. ID: " . tenant()->id);
                return false;
            }
            
            // Kullanıcının modül bazlı iznini kontrol et - HAYATİ KONTROL
            $hasPermission = $user->hasModulePermission($moduleName, $permissionType);
            
            // Sonucu log'la
            Log::info("Editor {$user->id} ({$user->email}) - Modül: {$moduleName}, İzin: {$permissionType}, Sonuç: " . ($hasPermission ? 'Erişim var' : 'Erişim yok'));
            
            if (!$hasPermission) {
                return false;
            }
            
            return true;
        }
        
        // Özel rol kontrolü
        if ($user->hasAnyRole()) {
            $permissionName = "{$moduleName}.{$permissionType}";
            return $user->hasPermissionTo($permissionName);
        }
        
        Log::info("Kullanıcı {$user->id} için rol kontrollerinden geçemedi. Roller: " . implode(', ', $user->getRoleNames()->toArray()));
        return false;
    }
    
    /**
     * Modülün tenant'a atanıp atanmadığını kontrol eder
     *
     * @param int $moduleId
     * @param int $tenantId
     * @return bool
     */
    public function isModuleAssignedToTenant(int $moduleId, int $tenantId): bool
    {
        // Tenant ID 1 ise (central tenant) tüm modüllere erişim ver
        if ($tenantId === 1) {
            return true;
        }
        
        $cacheKey = "module_{$moduleId}_tenant_{$tenantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleId, $tenantId) {
            // module_tenants tablosu CENTRAL veritabanında olduğu için TenantHelpers::central içinde sorgu yapılmalı
            return \App\Helpers\TenantHelpers::central(function () use ($moduleId, $tenantId) {
                // Önce module_tenants tablosunu kontrol et
                $assigned = DB::table('module_tenants')
                    ->where('module_id', $moduleId)
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true)
                    ->exists();
                
                if ($assigned) {
                    return true;
                }
                
                // Ayrıca tenant verisini kontrol et - central=true ise tüm modüllere erişim ver
                $tenant = DB::table('tenants')
                    ->where('id', $tenantId)
                    ->select('central')
                    ->first();
                    
                if ($tenant && $tenant->central) {
                    return true;
                }
                
                // İlişki yoksa false döndür
                return false;
            });
        });
    }

    /**
     * Tenant'a atanan tüm modülleri getirir
     *
     * @param int $tenantId
     * @return array
     */
    public function getTenantModules(int $tenantId): array
    {
        $cacheKey = "tenant_{$tenantId}_modules";
        $cacheDuration = config('module-permissions.cache_durations.module_tenant_assignment', 720);
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($tenantId) {
            return DB::table('module_tenants')
                ->join('modules', 'module_tenants.module_id', '=', 'modules.module_id')
                ->where('tenant_id', $tenantId)
                ->where('module_tenants.is_active', true)
                ->where('modules.is_active', true)
                ->pluck('modules.name')
                ->toArray();
        });
    }
    
    /**
     * Belirli bir tipe sahip tüm modülleri getir
     * 
     * @param string $type Modül tipi (content, system, management, vb.)
     * @return array
     */
    public function getModulesByType(string $type): array
    {
        $cacheKey = "modules_by_type_{$type}";
        $cacheDuration = config('module-permissions.cache_durations.module_list', 1440);
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($type) {
            return Module::where('type', $type)
                ->where('is_active', true)
                ->pluck('name')
                ->toArray();
        });
    }
    
    /**
     * Tüm modül tiplerini getir
     * 
     * @return array
     */
    public function getAllModuleTypes(): array
    {
        $cacheKey = "all_module_types";
        $cacheDuration = config('module-permissions.cache_durations.module_list', 1440);
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () {
            return Module::distinct()
                ->pluck('type')
                ->toArray();
        });
    }
    
    /**
     * Temel modülleri getir (sistem çekirdeği için gerekli olan modüller)
     * 
     * @return array
     */
    public function getCoreModules(): array
    {
        $cacheKey = "core_modules";
        $cacheDuration = config('module-permissions.cache_durations.module_list', 1440);
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () {
            // Sistem modülleri olarak 'system' tipindeki modülleri alabiliriz
            // veya management tipini de ekleyebiliriz
            return Module::whereIn('type', ['system', 'management'])
                ->where('is_active', true)
                ->pluck('name')
                ->toArray();
        });
    }
    
    /**
     * Tenant için gerekli minimum modülleri getir
     * 
     * @return array
     */
    public function getRequiredTenantModules(): array
    {
        $cacheKey = "required_tenant_modules";
        $cacheDuration = config('module-permissions.cache_durations.module_list', 1440);
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () {
            // Tenant için minimum 'system' tipindeki modüller gereklidir
            return Module::where('type', 'system')
                ->where('is_active', true)
                ->pluck('name')
                ->toArray();
        });
    }
    
    /**
     * Modül adına göre modül bilgisini getirir
     *
     * @param string $moduleName
     * @return \Modules\ModuleManagement\App\Models\Module|null
     */
    public function getModuleByName(string $moduleName)
    {
        $cacheKey = "module_by_name_{$moduleName}";
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($moduleName) {
            return Module::where('name', $moduleName)
                ->where('is_active', true)
                ->first();
        });
    }
    
    /**
     * Kullanıcının erişebileceği modül listesini döndürür
     *
     * @param \App\Models\User $user
     * @return array
     */
    public function getUserAccessibleModules($user): array
    {
        if (!$user) {
            return [];
        }
        
        $cacheKey = "user_{$user->id}_accessible_modules";
        
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($user) {
            // ROOT tüm modüllere erişebilir
            if ($user->isRoot()) {
                return Module::where('is_active', true)
                    ->pluck('name')
                    ->toArray();
            }
            
            // ADMIN kontrolü
            if ($user->isAdmin()) {
                // Tenant'ta ise sadece tenant'a atanmış modüller
                if (TenantHelpers::isTenant()) {
                    return $this->getTenantModules(tenant()->id);
                }
                
                // Central'da ise kısıtlı modüller hariç tümü
                $restrictedModules = config('module-permissions.admin_restricted_modules', []);
                return Module::where('is_active', true)
                    ->whereNotIn('name', $restrictedModules)
                    ->pluck('name')
                    ->toArray();
            }
            
            // EDITOR için kullanıcıya özel izinler
            if ($user->isEditor()) {
                $permissions = $user->userModulePermissions()
                    ->where('permission_type', 'view')
                    ->where('is_active', true)
                    ->pluck('module_name')
                    ->toArray();
                    
                // Tenant'ta ise, sadece tenant'a atanmış modülleri filtrele
                if (TenantHelpers::isTenant()) {
                    $tenantModules = $this->getTenantModules(tenant()->id);
                    return array_intersect($permissions, $tenantModules);
                }
                
                return $permissions;
            }
            
            return [];
        });
    }
    
    /**
     * Önbellekteki erişim verilerini temizler
     *
     * @param int|null $userId
     * @param int|null $tenantId
     * @param string|null $moduleName
     * @param string|null $moduleType
     * @return void
     */
    public function clearAccessCache(?int $userId = null, ?int $tenantId = null, ?string $moduleName = null, ?string $moduleType = null): void
    {
        // Belirli kullanıcı önbelleğini temizle
        if ($userId) {
            Cache::forget("user_{$userId}_accessible_modules");
        }
        
        // Belirli tenant modülleri önbelleğini temizle
        if ($tenantId) {
            Cache::forget("tenant_{$tenantId}_modules");
            
            if ($moduleName) {
                $module = $this->getModuleByName($moduleName);
                if ($module) {
                    Cache::forget("module_{$module->module_id}_tenant_{$tenantId}");
                }
            }
        }
        
        // Belirli bir modül önbelleğini temizle
        if ($moduleName) {
            Cache::forget("module_by_name_{$moduleName}");
        }
        
        // Belirli bir modül tipi önbelleğini temizle
        if ($moduleType) {
            Cache::forget("modules_by_type_{$moduleType}");
        }
        
        // Eğer hiçbir parametre belirtilmemişse, tüm modül önbelleklerini temizle
        if (!$userId && !$tenantId && !$moduleName && !$moduleType) {
            // Modül tipleri önbelleğini temizle
            Cache::forget("all_module_types");
            Cache::forget("core_modules");
            Cache::forget("required_tenant_modules");
            
            // Mevcut tüm modül tiplerini temizle
            $moduleTypes = $this->getAllModuleTypes();
            foreach ($moduleTypes as $type) {
                Cache::forget("modules_by_type_{$type}");
            }
        }
    }
    
    /**
     * Modül eklendiğinde/değiştirildiğinde çağrılacak metod
     * 
     * @return void
     */
    public function refreshModuleCache(): void
    {
        // Önemli modül önbelleklerini temizle
        Cache::forget("all_module_types");
        Cache::forget("core_modules");
        Cache::forget("required_tenant_modules");
        
        // Tüm modül tiplerini temizle (mevcut olanları al)
        $moduleTypes = DB::table('modules')
            ->distinct()
            ->pluck('type')
            ->toArray();
            
        foreach ($moduleTypes as $type) {
            Cache::forget("modules_by_type_{$type}");
        }
    }
}