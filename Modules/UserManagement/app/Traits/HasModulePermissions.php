<?php

namespace Modules\UserManagement\App\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\UserManagement\App\Models\UserModulePermission;
use Illuminate\Support\Facades\Log;

trait HasModulePermissions
{
    /**
     * Kullanıcının belirli bir modül ve izin tipine erişimi olup olmadığını kontrol eder
     */
    public function hasModulePermission(string $moduleName, string $permissionType): bool
    {
        // Root kontrolü
        if ($this->hasRole('root')) {
            return true;
        }

        // Admin kontrolü
        if ($this->hasRole('admin')) {
            // Tenant kontrolü
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                // Tenant'ta ise, modülün tenant'a atanmış olup olmadığını kontrol et
                $moduleService = app(\App\Services\ModuleAccessService::class);
                $module = $moduleService->getModuleByName($moduleName);
                
                if (!$module) {
                    \Log::warning("Module not found: {$moduleName}");
                    return false;
                }
                
                return $moduleService->isModuleAssignedToTenant($module->module_id, tenant()->id);
            }
            
            // Central'da ise kısıtlı modülleri kontrol et
            if (in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                \Log::warning("Admin tried to access restricted module: {$moduleName}");
                return false;
            }
            
            return true;
        }

        // Rol çakışması kontrolü (güvenlik için)
        if ($this->isEditor() && ($this->hasRole('admin') || $this->hasRole('root'))) {
            \Log::warning("Kullanıcı {$this->id} hem Editor hem de admin/root rollerine sahip! Bu durum kontrol edilmeli.");
            return false;
        }

        // Editor ve diğer roller için modül bazlı izin kontrolü
        $cacheKey = "user_{$this->id}_module_{$moduleName}_permission_{$permissionType}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($moduleName, $permissionType) {
            // Kullanıcın direkt olarak moduleName.permissionType izni var mı kontrol et
            $permissionName = "{$moduleName}.{$permissionType}";
            if ($this->hasPermissionTo($permissionName)) {
                return true;
            }
            
            // Model has permissions tablosundan kontrol et
            $hasPermissionInDB = \DB::table('model_has_permissions')
                ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                ->where('model_id', $this->id)
                ->where('model_type', get_class($this))
                ->where('permissions.name', $permissionName)
                ->exists();
                
            if ($hasPermissionInDB) {
                return true;
            }
            
            // UserModulePermission tablosu üzerinden kontrol
            $hasDirectModulePermission = $this->userModulePermissions()
                ->where('module_name', $moduleName)
                ->where('permission_type', $permissionType)
                ->where('is_active', true)
                ->exists();
                
            \Log::debug("UserModulePermission check - User: {$this->id}, Module: {$moduleName}, Permission: {$permissionType}, Result: " . ($hasDirectModulePermission ? 'true' : 'false'));
                
            return $hasDirectModulePermission;
        });
    }

    /**
     * Kullanıcının bir modüle ait tüm izinlerini getirir
     *
     * @param string $moduleName Modül adı
     * @return array İzin tipleri dizisi
     */
    public function getModulePermissions(string $moduleName): array
    {
        // Önbellekten kontrol et
        $cacheKey = "user_{$this->id}_module_{$moduleName}_permissions";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName) {
            // Root her izne sahiptir
            if ($this->hasRole('root')) {
                return array_keys(UserModulePermission::getPermissionTypes());
            }
            
            // Admin tenant'a atanmış modüllerin tüm izinlerine sahiptir
            if ($this->hasRole('admin')) {
                // Tenant'ta ise modülün atanmış olması gerekir
                if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                    $moduleService = app(\App\Services\ModuleAccessService::class);
                    $module = $moduleService->getModuleByName($moduleName);
                    
                    if (!$module || !$moduleService->isModuleAssignedToTenant($module->module_id, tenant()->id)) {
                        return [];
                    }
                }
                
                // Central'da admin kısıtlı modüllere erişemez
                if (!app(\Stancl\Tenancy\Tenancy::class)->initialized && 
                    in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                    return [];
                }
                
                return array_keys(UserModulePermission::getPermissionTypes());
            }
            
            // Editor ve diğer roller için özel izinleri getir
            return $this->userModulePermissions()
                ->where('module_name', $moduleName)
                ->where('is_active', true)
                ->pluck('permission_type')
                ->toArray();
        });
    }

    /**
     * Kullanıcının bir modüle ait belirli izinleri var mı kontrol eder
     *
     * @param string $moduleName Modül adı
     * @param array $permissionTypes İzin tipleri dizisi
     * @return bool Tüm izinlere sahipse true, değilse false
     */
    public function hasModulePermissions(string $moduleName, array $permissionTypes): bool
    {
        // Süper admin kontrolü
        if ($this->hasRole('root')) {
            return true;
        }
        
        // Admin kontrolü - tenant'ta atanmış modüller için tüm izinlere sahip
        if ($this->hasRole('admin')) {
            // Tenant'ta ise modülün atanmış olması gerekir
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $moduleService = app(\App\Services\ModuleAccessService::class);
                $module = $moduleService->getModuleByName($moduleName);
                
                if (!$module || !$moduleService->isModuleAssignedToTenant($module->module_id, tenant()->id)) {
                    return false;
                }
            }
            
            // Central'da admin kısıtlı modüllere erişemez
            if (!app(\Stancl\Tenancy\Tenancy::class)->initialized && 
                in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                return false;
            }
            
            return true;
        }
        
        $userPermissions = $this->getModulePermissions($moduleName);
        
        foreach ($permissionTypes as $permissionType) {
            if (!in_array($permissionType, $userPermissions)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Kullanıcının tüm modül izinleri ilişkisi
     */
    public function userModulePermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }
    
    /**
     * Kullanıcıya modül izni ekler
     *
     * @param string $moduleName Modül adı
     * @param string|array $permissionTypes İzin tipi veya tipleri
     * @return void
     */
    public function giveModulePermissionTo(string $moduleName, $permissionTypes): void
    {
        if (!is_array($permissionTypes)) {
            $permissionTypes = [$permissionTypes];
        }
        
        foreach ($permissionTypes as $permissionType) {
            $this->userModulePermissions()->updateOrCreate(
                [
                    'module_name' => $moduleName,
                    'permission_type' => $permissionType,
                ],
                [
                    'is_active' => true
                ]
            );
        }
        
        // Önbelleği temizle
        $this->clearModulePermissionCache($moduleName);
    }
    
    /**
     * Kullanıcıdan modül izni kaldırır
     *
     * @param string $moduleName Modül adı
     * @param string|array $permissionTypes İzin tipi veya tipleri
     * @return void
     */
    public function revokeModulePermissionTo(string $moduleName, $permissionTypes): void
    {
        if (!is_array($permissionTypes)) {
            $permissionTypes = [$permissionTypes];
        }
        
        $this->userModulePermissions()
            ->where('module_name', $moduleName)
            ->whereIn('permission_type', $permissionTypes)
            ->delete();
        
        // Önbelleği temizle
        $this->clearModulePermissionCache($moduleName);
    }
    
    /**
     * Kullanıcının modül izinleri önbelleğini temizler
     *
     * @param string $moduleName Modül adı
     * @return void
     */
    private function clearModulePermissionCache(string $moduleName): void
    {
        $permissionTypes = UserModulePermission::getPermissionTypes();
        
        foreach ($permissionTypes as $type => $name) {
            Cache::forget("user_{$this->id}_module_{$moduleName}_permission_{$type}");
        }
        
        Cache::forget("user_{$this->id}_module_{$moduleName}_permissions");
    }
    
    /**
     * Kullanıcının root (tam yetkili) rolü olup olmadığını kontrol eder
     */
    public function isRoot(): bool
    {
        return $this->hasRole('root');
    }
    
    /**
     * Kullanıcının admin rolü olup olmadığını kontrol eder
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Kullanıcının editor rolü olup olmadığını kontrol eder
     */
    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }
}