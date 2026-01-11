<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('is_module_active')) {
    function is_module_active(string $moduleName, string $permissionType = 'view'): bool
    {
        // Cache ile kontrol et
        $cacheKey = "module_{$moduleName}_permission_{$permissionType}_active";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName, $permissionType) {
            // Modül aktif mi?
            $module = \Modules\ModuleManagement\App\Models\Module::where('name', $moduleName)
                ->where('is_active', true)
                ->first();
                
            if (!$module) {
                return false;
            }
            
            // Permission var mı? Herhangi bir guard için kontrol et
            $permissionName = "{$moduleName}.{$permissionType}";
            return \Spatie\Permission\Models\Permission::where('name', $permissionName)->exists();
        });
    }
}

if (!function_exists('module_permissions')) {
    /**
     * Belirli bir modül için izinleri almak
     */
    function module_permissions(string $moduleName): array
    {
        $cacheKey = "module_{$moduleName}_permissions";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName) {
            $permissions = [];
            $permissionTypes = \Modules\UserManagement\App\Models\ModulePermission::getPermissionTypes();
            
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$moduleName}.{$type}";
                $exists = \Spatie\Permission\Models\Permission::where('name', $permissionName)->exists();
                $permissions[$type] = $exists;
            }
            
            return $permissions;
        });
    }
}

if (!function_exists('clear_module_permission_cache')) {
    /**
     * Modül izinleri önbelleğini temizle
     */
    function clear_module_permission_cache(string $moduleName): void
    {
        $permissionTypes = \Modules\UserManagement\App\Models\ModulePermission::getPermissionTypes();
        
        foreach ($permissionTypes as $type => $label) {
            Cache::forget("module_{$moduleName}_permission_{$type}_active");
        }
        
        Cache::forget("module_{$moduleName}_permissions");
    }
}