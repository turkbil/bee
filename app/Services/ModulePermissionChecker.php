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
            if (app()->environment(['local', 'staging'])) {
                Log::debug("ROOT user modül erişimi onaylandı", [
                    'user_id' => $user->id,
                    'module' => $module->name,
                    'permission' => $permissionType
                ]);
            }
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
                if (app()->environment(['local', 'staging'])) {
                    Log::debug("Admin kullanıcı - modül tenant'a atanmamış", [
                        'user_id' => $user->id,
                        'module' => $module->name,
                        'tenant_id' => tenant()->id
                    ]);
                }
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
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug("Editor yetki kontrolü", [
                'user_id' => $user->id,
                'permission' => $permission,
                'result' => $hasPermission
            ]);
        }
        
        return $hasPermission;
    }
}