<?php

namespace App\Services;

use App\Contracts\ModuleAccessServiceInterface;
use App\Services\ModulePermissionChecker;
use App\Services\ModuleAccessCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\ModuleManagement\App\Models\Module;
use App\Helpers\TenantHelpers;

class ModuleAccessService implements ModuleAccessServiceInterface
{
    protected ModulePermissionChecker $permissionChecker;
    protected ModuleAccessCache $cache;
    
    public function __construct(
        ModulePermissionChecker $permissionChecker,
        ModuleAccessCache $cache
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->cache = $cache;
    }
    
    /**
     * Kullanıcının belirtilen modül ve izin tipine erişimi olup olmadığını kontrol eder
     */
    public function canAccess(string $moduleName, string $permissionType = 'view'): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->is_active) {
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Kullanıcı yok veya aktif değil", ['module' => $moduleName]);
            }
            return false;
        }
        
        // Cache'den kontrol et
        $cached = $this->cache->getAccessCache($user->id, $moduleName, $permissionType);
        if ($cached !== null) {
            return $cached;
        }
        
        // Modül aktif mi?
        $module = $this->getModuleByName($moduleName);
        if (!$module || !$module->is_active) {
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Modül bulunamadı veya aktif değil", ['module' => $moduleName]);
            }
            $this->cache->setAccessCache($user->id, $moduleName, $permissionType, false);
            return false;
        }
        
        // Yetki kontrolü
        $hasAccess = $this->permissionChecker->checkUserPermission($user, $module, $permissionType);
        
        // Cache'e kaydet
        $this->cache->setAccessCache($user->id, $moduleName, $permissionType, $hasAccess);
        
        return $hasAccess;
    }
    
    /**
     * Modül tenant'a atanmış mı kontrol eder
     */
    public function isModuleAssignedToTenant(string $moduleId, string $tenantId): bool
    {
        // Central tenant (ID: 1) tüm modüllere erişebilir
        if ($tenantId === '1' || $tenantId === 1) {
            return true;
        }
        
        // Cache'den kontrol et
        $cached = $this->cache->getTenantAssignmentCache($moduleId, $tenantId);
        if ($cached !== null) {
            return $cached;
        }
        
        // Database'den kontrol et
        $isAssigned = TenantHelpers::central(function () use ($moduleId, $tenantId) {
            $assigned = DB::table('module_tenants')
                ->where('module_id', $moduleId)
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->exists();
            
            if ($assigned) {
                return true;
            }
            
            // Central tenant kontrolü
            $tenant = DB::table('tenants')
                ->where('id', $tenantId)
                ->where('central', true)
                ->where('is_active', true)
                ->exists();
                
            return $tenant;
        });
        
        // Cache'e kaydet
        $this->cache->setTenantAssignmentCache($moduleId, $tenantId, $isAssigned);
        
        return $isAssigned;
    }
    
    /**
     * Modül adına göre modül modelini getirir
     * Cache'li version - duplicate query'leri önler
     */
    public function getModuleByName(string $moduleName): ?object
    {
        static $moduleCache = [];
        
        if (isset($moduleCache[$moduleName])) {
            return $moduleCache[$moduleName];
        }
        
        // Redis cache'i de kullan (15 dakika)
        $cacheKey = "module_by_name:{$moduleName}";
        $module = cache()->remember($cacheKey, 900, function () use ($moduleName) {
            return TenantHelpers::central(function () use ($moduleName) {
                return Module::where('name', $moduleName)->first();
            });
        });
        
        $moduleCache[$moduleName] = $module;
        
        return $module;
    }
    
    /**
     * Kullanıcının erişebileceği modülleri listeler
     */
    public function getAccessibleModules(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }
        
        $modules = Module::where('is_active', true)->get();
        $accessibleModules = [];
        
        foreach ($modules as $module) {
            if ($this->canAccess($module->name)) {
                $accessibleModules[] = $module->toArray();
            }
        }
        
        return $accessibleModules;
    }
    
    /**
     * Modül erişim cache'ini temizler
     */
    public function clearModuleAccessCache(?string $userId = null): void
    {
        if ($userId) {
            $this->cache->clearUserCache($userId);
        } else {
            $this->cache->clearAllCache();
        }
    }
}