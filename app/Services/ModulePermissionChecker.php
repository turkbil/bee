<?php

namespace App\Services;

use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModulePermissionChecker
{
    /**
     * Kullanıcının modül erişim yetkisini kontrol eder
     */
    public function checkUserPermission(object $user, object $module, string $permissionType): bool
    {
        // ROOT her zaman erişebilir
        if ($user->isRoot()) {
            return true;
        }
        
        $isTenant = TenantHelpers::isTenant();
        
        // ADMIN rolü kontrolü
        if ($user->isAdmin()) {
            return $this->checkAdminPermission($user, $module, $isTenant);
        }
        
        // EDITOR rolü kontrolü
        if ($user->isEditor()) {
            return $this->checkEditorPermission($user, $module, $permissionType);
        }
        
        return false;
    }
    
    /**
     * Admin kullanıcının modül yetkisini kontrol eder
     */
    protected function checkAdminPermission(object $user, object $module, bool $isTenant): bool
    {
        if ($isTenant) {
            $moduleAccessService = app(\App\Contracts\ModuleAccessServiceInterface::class);
            $isAssigned = $moduleAccessService->isModuleAssignedToTenant($module->module_id, tenant()->id);
            
            if (!$isAssigned) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Editor kullanıcının modül yetkisini kontrol eder
     */
    protected function checkEditorPermission(object $user, object $module, string $permissionType): bool
    {
        // Spatie Permission sistemi ile kontrol
        $permission = $module->name . '.' . $permissionType;
        
        $hasPermission = $user->can($permission);
        
        return $hasPermission;
    }
}