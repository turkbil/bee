<?php

namespace App\Traits;

use App\Services\ModuleAccessService;
use Illuminate\Support\Facades\Log;

trait HasModuleAccessControl
{
    /**
     * Modül erişim kontrolü yap - frontend için
     */
    protected function checkModuleAccess(string $moduleName): void
    {
        try {
            $moduleAccessService = app(ModuleAccessService::class);
            
            // Modül var mı ve aktif mi kontrol et
            $module = $moduleAccessService->getModuleByName($moduleName);
            
            if (!$module || !$module->is_active) {
                Log::info("Module not found or inactive", [
                    'module' => $moduleName,
                    'found' => !!$module,
                    'active' => $module->is_active ?? false
                ]);
                abort(404, 'Page not found');
            }
            
            // Tenant'a atanmış mı kontrol et
            $tenantId = tenant()?->id ?? 1;
            if (!$moduleAccessService->isModuleAssignedToTenant($module->module_id, $tenantId)) {
                Log::info("Module not assigned to tenant", [
                    'module' => $moduleName,
                    'module_id' => $module->module_id,
                    'tenant_id' => $tenantId
                ]);
                abort(404, 'Page not found');
            }
            
        } catch (\Exception $e) {
            Log::error('Module access check failed', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Page not found');
        }
    }
}