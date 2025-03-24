<?php

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
            
            // Permission var mı?
            $permissionName = "{$moduleName}.{$permissionType}";
            return \Spatie\Permission\Models\Permission::where('name', $permissionName)
                ->exists();
        });
    }
}