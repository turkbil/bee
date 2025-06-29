<?php

namespace App\Listeners;

use App\Events\ModuleAddedToTenant;
use App\Events\ModuleRemovedFromTenant;
use App\Jobs\CreateModuleTenantPermissions;
use App\Jobs\RemoveModuleTenantPermissions;
use Illuminate\Support\Facades\Log;

class HandleModuleTenantPermissions
{
    /**
     * Handle module added to tenant event
     */
    public function handleModuleAdded(ModuleAddedToTenant $event): void
    {
        try {
            // Queue job to create permissions
            CreateModuleTenantPermissions::dispatch(
                $event->moduleId,
                $event->tenantId,
                $event->moduleData
            );
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Module tenant permission creation job dispatched', [
                    'module_id' => $event->moduleId,
                    'tenant_id' => $event->tenantId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch module permission creation job', [
                'module_id' => $event->moduleId,
                'tenant_id' => $event->tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle module removed from tenant event
     */
    public function handleModuleRemoved(ModuleRemovedFromTenant $event): void
    {
        try {
            // Queue job to remove permissions
            RemoveModuleTenantPermissions::dispatch(
                $event->moduleId,
                $event->tenantId,
                $event->moduleData
            );
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Module tenant permission removal job dispatched', [
                    'module_id' => $event->moduleId,
                    'tenant_id' => $event->tenantId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch module permission removal job', [
                'module_id' => $event->moduleId,
                'tenant_id' => $event->tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
}