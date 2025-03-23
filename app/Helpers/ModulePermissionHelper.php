<?php

use Modules\UserManagement\App\Models\ModulePermission;
use Modules\UserManagement\App\Models\UserModulePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

if (!function_exists('can_module')) {
    /**
     * Kullanıcının belirli bir modül izni olup olmadığını kontrol eder
     *
     * @param string $moduleName Modül adı
     * @param string $permissionType İzin tipi (view, create, update, delete)
     * @return bool Erişim izni varsa true, yoksa false
     */
    function can_module(string $moduleName, string $permissionType = 'view'): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Süper admin veya tenant admin ise her zaman erişim izni ver
        if ($user->hasRole('root') || $user->hasRole('admin')) {
            return true;
        }
        
        return $user->hasModulePermission($moduleName, $permissionType);
    }
}

if (!function_exists('get_module_permissions')) {
    /**
     * Kullanıcının bir modüle ait tüm izinlerini getirir
     *
     * @param string $moduleName Modül adı
     * @return array İzin tipleri dizisi
     */
    function get_module_permissions(string $moduleName): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }
        
        return $user->getModulePermissions($moduleName);
    }
}

if (!function_exists('get_module_permission_types')) {
    /**
     * Mevcut tüm modül izin tiplerini döndürür
     * 
     * @return array İzin tipleri dizisi
     */
    function get_module_permission_types(): array
    {
        return ModulePermission::getPermissionTypes();
    }
}

if (!function_exists('is_module_active')) {
    /**
     * Belirli bir modül ve izin tipinin aktif olup olmadığını kontrol eder
     * 
     * @param string $moduleName Modül adı
     * @param string $permissionType İzin tipi
     * @return bool Aktif ise true, değilse false
     */
    function is_module_active(string $moduleName, string $permissionType = 'view'): bool
    {
        // Önbellekten kontrol et
        $cacheKey = "module_{$moduleName}_permission_{$permissionType}_active";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName, $permissionType) {
            $module = \Modules\ModuleManagement\App\Models\Module::where('name', $moduleName)
                ->where('is_active', true)
                ->first();
                
            if (!$module) {
                return false;
            }
            
            return ModulePermission::where('module_id', $module->module_id)
                ->where('permission_type', $permissionType)
                ->where('is_active', true)
                ->exists();
        });
    }
}