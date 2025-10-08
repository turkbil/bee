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

class CreateModuleTenantPermissions implements ShouldQueue
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

        // Queue configuration - default queue kullan çünkü Horizon tarafından dinleniyor
        $this->onQueue('default');
    }
    
    public function handle(): void
    {
        try {
            if (app()->environment(['local', 'staging'])) {
                Log::debug('Creating module tenant permissions', [
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
                $this->createPermissions();
                $this->clearRelatedCaches();
                
                if (app()->environment(['local', 'staging'])) {
                    Log::debug('Module tenant permissions created successfully', [
                        'module_id' => $this->moduleId,
                        'tenant_id' => $this->tenantId
                    ]);
                }
                
            } finally {
                // Tenant context'ini temizle
                app(Tenancy::class)->end();
            }
            
        } catch (\Exception $e) {
            Log::error('Create module tenant permissions job failed', [
                'module_id' => $this->moduleId,
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Permission'ları oluştur
     */
    protected function createPermissions(): void
    {
        $moduleName = $this->moduleData['name'];
        $permissions = $this->getModulePermissions($moduleName);
        
        foreach ($permissions as $permission) {
            $this->createOrUpdatePermission($permission);
        }
        
        // Editor rolüne basic permission'ları ata
        $this->assignBasicPermissionsToEditor($moduleName);
    }
    
    /**
     * Modül permission'larını al (Central database'den)
     */
    protected function getModulePermissions(string $moduleName): array
    {
        try {
            // Tenant context'inden çık
            app(Tenancy::class)->end();

            // Central database'den modülün tüm permission'larını al
            $permissions = \Modules\UserManagement\App\Models\Permission::where('name', 'like', "{$moduleName}.%")
                ->pluck('name')
                ->toArray();

            // Tenant context'ini tekrar başlat
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                app(Tenancy::class)->initialize($tenant);
            }

            // Eğer central'da permission yoksa, standart permission'ları kullan
            if (empty($permissions)) {
                return [
                    "{$moduleName}.view",
                    "{$moduleName}.create",
                    "{$moduleName}.edit",
                    "{$moduleName}.delete",
                    "{$moduleName}.manage"
                ];
            }

            return $permissions;

        } catch (\Exception $e) {
            Log::warning('Failed to get permissions from central database, using defaults', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);

            // Hata durumunda standart permission'ları döndür
            return [
                "{$moduleName}.view",
                "{$moduleName}.create",
                "{$moduleName}.edit",
                "{$moduleName}.delete",
                "{$moduleName}.manage"
            ];
        }
    }
    
    /**
     * Permission oluştur veya güncelle
     */
    protected function createOrUpdatePermission(string $permissionName): void
    {
        $description = $this->generatePermissionDescription($permissionName);
        
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
            ['name' => $permissionName],
            [
                'guard_name' => 'web',
                'description' => $description
            ]
        );
        
        // Eğer permission zaten varsa ama description NULL ise güncelle
        if ($permission->description === null && $description !== null) {
            $permission->update(['description' => $description]);
        }
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Permission created/updated', [
                'permission' => $permissionName,
                'description' => $description,
                'tenant_id' => $this->tenantId
            ]);
        }
    }
    
    /**
     * Permission description'ını otomatik oluştur
     */
    protected function generatePermissionDescription(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        $moduleName = $parts[0] ?? '';
        $action = $parts[1] ?? '';
        
        // Modül adını Türkçe'ye çevir
        $moduleDisplayNames = [
            'ai' => 'AI',
            'widgetmanagement' => 'Widgetmanagement',
            'modulemanagement' => 'Modulemanagement',
            'tenantmanagement' => 'Tenantmanagement',
            'usermanagement' => 'Usermanagement',
            'settingmanagement' => 'Settingmanagement',
            'thememanagement' => 'Thememanagement',
            'mediamanagement' => 'Mediamanagement',
            'menumanagement' => 'Menumanagement',
            'seomanagement' => 'Seomanagement',
            'studio' => 'Studio',
            'announcement' => 'Announcement',
            'page' => 'Page',
            'portfolio' => 'Portfolio',
            'blog' => 'Blog',
            'languagemanagement' => 'Languagemanagement'
        ];
        
        // Action'ı Türkçe'ye çevir
        $actionDisplayNames = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'edit' => 'Güncelleme',
            'delete' => 'Silme',
            'manage' => 'Yönetme',
            'update' => 'Güncelleme'
        ];
        
        $moduleDisplay = $moduleDisplayNames[$moduleName] ?? ucfirst($moduleName);
        $actionDisplay = $actionDisplayNames[$action] ?? ucfirst($action);
        
        return "{$moduleDisplay} - {$actionDisplay}";
    }
    
    /**
     * Editor rolüne basic permission'ları ata
     */
    protected function assignBasicPermissionsToEditor(string $moduleName): void
    {
        try {
            $editorRole = \Spatie\Permission\Models\Role::where('name', 'editor')->first();
            if (!$editorRole) {
                return;
            }
            
            $basicPermissions = [
                "{$moduleName}.view",
                "{$moduleName}.create",
                "{$moduleName}.edit"
            ];
            
            foreach ($basicPermissions as $permissionName) {
                $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
                if ($permission && !$editorRole->hasPermissionTo($permission)) {
                    $editorRole->givePermissionTo($permission);
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Editor permission assignment failed', [
                'module' => $moduleName,
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
    }
    
    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CreateModuleTenantPermissions job failed', [
            'module_id' => $this->moduleId,
            'tenant_id' => $this->tenantId,
            'exception' => $exception->getMessage()
        ]);
    }
}