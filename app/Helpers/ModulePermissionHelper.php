<?php

use Modules\UserManagement\App\Models\ModulePermission;
use Modules\UserManagement\App\Models\UserModulePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\ModuleManagement\App\Models\Module;

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
        
        // Kullanıcı izinleri kontrol eder
        $cacheKey = "user_{$user->id}_module_{$moduleName}_permission_{$permissionType}";
        return Cache::remember($cacheKey, now()->addHours(24), function() use ($user, $moduleName, $permissionType) {
            return $user->hasModulePermission($moduleName, $permissionType);
        });
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
        
        $cacheKey = "user_{$user->id}_module_{$moduleName}_permissions";
        return Cache::remember($cacheKey, now()->addHours(24), function() use ($user, $moduleName) {
            return $user->getModulePermissions($moduleName);
        });
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
        $cacheKey = "module_permission_types";
        return Cache::remember($cacheKey, now()->addWeek(), function() {
            return ModulePermission::getPermissionTypes();
        });
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
        // Önbellekten kontrol et - süreyi artırdık
        $cacheKey = "module_{$moduleName}_permission_{$permissionType}_active";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($moduleName, $permissionType) {
            // Tek bir sorgu ile kontrol et
            return Module::where('name', $moduleName)
                ->where('is_active', true)
                ->whereHas('permissions', function($query) use ($permissionType) {
                    $query->where('permission_type', $permissionType)
                          ->where('is_active', true);
                })
                ->exists();
        });
    }
}