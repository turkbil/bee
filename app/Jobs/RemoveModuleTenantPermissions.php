<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;

class RemoveModuleTenantPermissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public int $moduleId;
    public string $tenantId;
    public array $moduleData;
    
    /**
     * Job timeout (seconds)
     */
    public int $timeout = 120;
    
    public function __construct(int $moduleId, string $tenantId, array $moduleData)
    {
        $this->moduleId = $moduleId;
        $this->tenantId = $tenantId;
        $this->moduleData = $moduleData;
        
        // Queue configuration
        $this->onQueue('module_permissions');
    }
    
    public function handle(): void
    {
        try {
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Removing module tenant permissions', [
                    'module_id' => $this->moduleId,
                    'tenant_id' => $this->tenantId,
                    'module_name' => $this->moduleData['name'] ?? 'unknown'
                ]);
            }
            
            // Tenant context'ini initialize et
            $tenant = Tenant::find($this->tenantId);
            if (!$tenant) {
                throw new \Exception("Tenant not found: {$this->tenantId}");
            }
            
            app(Tenancy::class)->initialize($tenant);
            
            try {
                $this->removePermissions();
                $this->clearRelatedCaches();
                
                if (app()->environment(['local', 'staging'])) {
                    Log::debug('Module tenant permissions removed successfully', [
                        'module_id' => $this->moduleId,
                        'tenant_id' => $this->tenantId
                    ]);
                }
                
            } finally {
                // Tenant context'ini temizle
                app(Tenancy::class)->end();
            }
            
        } catch (\Exception $e) {
            Log::error('Remove module tenant permissions job failed', [
                'module_id' => $this->moduleId,
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Permission'ları kaldır
     */
    protected function removePermissions(): void
    {
        $moduleName = $this->moduleData['name'];
        $permissions = $this->getModulePermissions($moduleName);
        
        foreach ($permissions as $permissionName) {
            $this->removePermission($permissionName);
        }
    }
    
    /**
     * Modül permission'larını al
     */
    protected function getModulePermissions(string $moduleName): array
    {
        return [
            "{$moduleName}.view",
            "{$moduleName}.create", 
            "{$moduleName}.edit",
            "{$moduleName}.delete",
            "{$moduleName}.manage"
        ];
    }
    
    /**
     * Permission'ı kaldır
     */
    protected function removePermission(string $permissionName): void
    {
        try {
            $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
            
            if ($permission) {
                // Önce tüm rol ve kullanıcılardan permission'ı kaldır
                $permission->roles()->detach();
                $permission->users()->detach();
                
                // Permission'ı soft delete (eğer destekleniyorsa) veya force delete
                if (config('permission.soft_delete', false)) {
                    $permission->delete();
                } else {
                    $permission->forceDelete();
                }
                
                if (app()->environment(['local', 'staging'])) {
                    Log::debug('Permission removed', [
                        'permission' => $permissionName,
                        'tenant_id' => $this->tenantId
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Permission removal failed', [
                'permission' => $permissionName,
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * İlgili cache'leri temizle
     */
    protected function clearRelatedCaches(): void
    {
        $cacheKeys = [
            "module_{$this->moduleId}_tenant_{$this->tenantId}",
            "modules_tenant_{$this->tenantId}",
            "tenant_{$this->tenantId}:module_access"
        ];
        
        foreach ($cacheKeys as $key) {
            \Cache::forget($key);
        }
        
        // Tag-based cache clearing
        \Cache::tags(["tenant_{$this->tenantId}:module_access"])->flush();
        
        // Permission cache clearing (Spatie package)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
    
    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('RemoveModuleTenantPermissions job failed', [
            'module_id' => $this->moduleId,
            'tenant_id' => $this->tenantId,
            'exception' => $exception->getMessage()
        ]);
    }
}