<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Events\ModuleAddedToTenant;
use App\Events\ModuleRemovedFromTenant;

class ModuleTenantPermissionService
{
    /**
     * Tenant'a modül eklendiğinde izinleri oluştur (Event-driven)
     */
    public function handleModuleAddedToTenant(int $moduleId, string $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                Log::error("Module not found for permission creation", [
                    'module_id' => $moduleId,
                    'tenant_id' => $tenantId
                ]);
                return;
            }
            
            // Module data hazırla
            $moduleData = [
                'id' => $module->id,
                'name' => $module->name,
                'display_name' => $module->display_name,
                'is_active' => $module->is_active
            ];
            
            // Event dispatch et - Queue job çalışacak
            ModuleAddedToTenant::dispatch($moduleId, $tenantId, $moduleData);
            
            // Cache temizleme
            $this->clearModuleCache($moduleId, $tenantId);
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Module added to tenant event dispatched', [
                    'module_id' => $moduleId,
                    'tenant_id' => $tenantId,
                    'module_name' => $module->name
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error handling module added to tenant', [
                'module_id' => $moduleId,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tenant'tan modül kaldırıldığında izinleri kaldır (Event-driven)
     */
    public function handleModuleRemovedFromTenant(int $moduleId, string $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                Log::warning("Module not found for permission removal", [
                    'module_id' => $moduleId,
                    'tenant_id' => $tenantId
                ]);
                // Modül bulunamasa bile permission temizleme devam etsin
            }
            
            // Module data hazırla
            $moduleData = [
                'id' => $moduleId,
                'name' => $module ? $module->name : "module_{$moduleId}",
                'display_name' => $module ? $module->display_name : "Module {$moduleId}",
                'is_active' => false
            ];
            
            // Event dispatch et - Queue job çalışacak
            ModuleRemovedFromTenant::dispatch($moduleId, $tenantId, $moduleData);
            
            // Cache temizleme
            $this->clearModuleCache($moduleId, $tenantId);
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Module removed from tenant event dispatched', [
                    'module_id' => $moduleId,
                    'tenant_id' => $tenantId,
                    'module_name' => $moduleData['name']
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error handling module removed from tenant', [
                'module_id' => $moduleId,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Modül permission'larını sync et (bulk operation)
     */
    public function syncModulePermissions(array $moduleIds, string $tenantId): void
    {
        try {
            foreach ($moduleIds as $moduleId) {
                $this->handleModuleAddedToTenant($moduleId, $tenantId);
            }
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Module permissions sync completed', [
                    'module_count' => count($moduleIds),
                    'tenant_id' => $tenantId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error syncing module permissions', [
                'module_ids' => $moduleIds,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tenant için tüm modül permission'larını yeniden oluştur
     */
    public function refreshTenantPermissions(string $tenantId): void
    {
        try {
            // Tenant'a atanmış aktif modülleri al
            $moduleIds = \DB::table('module_tenants')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->pluck('module_id')
                ->toArray();
            
            if (empty($moduleIds)) {
                if (app()->environment(['local', 'staging'])) {
                    Log::debug('No active modules found for tenant', ['tenant_id' => $tenantId]);
                }
                return;
            }
            
            $this->syncModulePermissions($moduleIds, $tenantId);
            
        } catch (\Exception $e) {
            Log::error('Error refreshing tenant permissions', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * İlgili cache'leri temizle
     */
    protected function clearModuleCache(int $moduleId, string $tenantId): void
    {
        $cacheKeys = [
            "module_{$moduleId}_tenant_{$tenantId}",
            "modules_tenant_{$tenantId}"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Tag-based cache clearing
        Cache::tags(["tenant_{$tenantId}:module_access"])->flush();
        
        // ModuleAccessService cache'ini de temizle
        try {
            $moduleAccessService = app(\App\Contracts\ModuleAccessServiceInterface::class);
            $moduleAccessService->clearModuleAccessCache();
        } catch (\Exception $e) {
            Log::warning('ModuleAccessService cache clear failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Queue status kontrolü
     */
    public function getQueueStatus(): array
    {
        try {
            // Queue driver'ının durumunu kontrol et
            $queueDriver = config('queue.default');
            $queueConnection = config("queue.connections.{$queueDriver}");
            
            return [
                'driver' => $queueDriver,
                'connection' => $queueConnection,
                'is_sync' => $queueDriver === 'sync',
                'queue_name' => 'module_permissions'
            ];
            
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}