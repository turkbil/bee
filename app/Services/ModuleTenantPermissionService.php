<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class ModuleTenantPermissionService
{
    /**
     * Tenant'a modül eklendiğinde veya aktifleştirildiğinde izinleri oluştur
     * 
     * @param int $moduleId Modül ID 
     * @param string $tenantId Tenant ID
     * @return void
     */
    public function handleModuleAddedToTenant($moduleId, $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                Log::error("Module not found: {$moduleId}");
                return;
            }

            $this->createTenantModulePermissions($module, $tenantId);
        } catch (\Exception $e) {
            Log::error("Error creating tenant module permissions: " . $e->getMessage());
        }
    }

    /**
     * Tenant'tan modül kaldırıldığında izinleri de kaldır
     * 
     * @param int $moduleId Modül ID
     * @param string $tenantId Tenant ID
     * @return void 
     */
    public function handleModuleRemovedFromTenant($moduleId, $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                Log::error("Module not found: {$moduleId}");
                return;
            }

            $this->removeTenantModulePermissions($module, $tenantId);
        } catch (\Exception $e) {
            Log::error("Error removing tenant module permissions: " . $e->getMessage());
        }
    }

    /**
     * Tenant için modül izinlerini oluştur
     * 
     * @param \Modules\ModuleManagement\App\Models\Module $module
     * @param string $tenantId
     * @return void
     */
    protected function createTenantModulePermissions($module, $tenantId): void
    {
        try {
            // Tenant'a geçiş yapalım
            tenancy()->initialize($tenantId);

            // Modül adı ve slug'ını hazırla
            $moduleSlug = Str::slug(Str::snake($module->name), '');
            $displayName = $module->display_name;

            // SettingManagement modülü kontrolü 
            $isSettingManagement = (
                $moduleSlug === 'settingmanagement' || 
                strtolower($module->name) === 'settingmanagement'
            );

            // Temel izin tipleri 
            if ($isSettingManagement) {
                // Setting Management için sadece görüntüleme ve güncelleme izinleri
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'update' => 'Güncelleme',
                ];
            } else {
                // Diğer modüller için tüm izinler
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'create' => 'Oluşturma',
                    'update' => 'Güncelleme',
                    'delete' => 'Silme',
                ];
            }

            // Her izin tipi için oluştur
            $createdPermissions = [];
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$moduleSlug}.{$type}";
                $description = "{$displayName} - {$label}";
                
                // İzin yoksa oluştur
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['description' => $description]
                );
                
                $createdPermissions[] = $permission;
            }
            
            // Admin rolüne bu izinleri ata
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $adminRole->givePermissionTo($createdPermissions);
            }

            // Tenant'dan çıkış yapalım
            tenancy()->end();
            
            Log::info("Permissions created for module {$module->name} in tenant {$tenantId}");
        } catch (\Exception $e) {
            Log::error("Permission creation failed for tenant {$tenantId}, module {$module->name}: " . $e->getMessage());
            
            // Tenant'dan çıkışı garantiye al
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }

    /**
     * Tenant'tan modül izinlerini kaldır
     * 
     * @param \Modules\ModuleManagement\App\Models\Module $module
     * @param string $tenantId
     * @return void
     */
    protected function removeTenantModulePermissions($module, $tenantId): void
    {
        try {
            // Tenant'a geçiş yapalım
            tenancy()->initialize($tenantId);

            // Modül adı ve slug'ını hazırla
            $moduleSlug = Str::slug(Str::snake($module->name), '');
            
            // SettingManagement modülü kontrolü 
            $isSettingManagement = (
                $moduleSlug === 'settingmanagement' || 
                strtolower($module->name) === 'settingmanagement'
            );

            // Temel izin tipleri
            if ($isSettingManagement) {
                $permissionTypes = ['view', 'update'];
            } else {
                $permissionTypes = ['view', 'create', 'update', 'delete'];
            }

            // Modüle ait izinleri bul ve sil
            foreach ($permissionTypes as $type) {
                $permissionName = "{$moduleSlug}.{$type}";
                
                // İzni bul
                $permission = Permission::where('name', $permissionName)->first();
                
                if ($permission) {
                    // Roller ile ilişkisini kaldır
                    $permission->roles()->detach();
                    
                    // Kullanıcı izinlerini kaldır
                    $permission->users()->detach();
                    
                    // İzni sil
                    $permission->delete();
                }
            }
            
            // Kullanıcı-modül izinleri tablosundan ilgili izinleri temizle
            if (Schema::hasTable('user_module_permissions')) {
                DB::table('user_module_permissions')
                    ->where('module_name', $module->name)
                    ->delete();
                
                Log::info("User module permissions for module {$module->name} removed in tenant {$tenantId}");
            }

            // Tenant'dan çıkış yapalım
            tenancy()->end();
            
            Log::info("Permissions removed for module {$module->name} in tenant {$tenantId}");
        } catch (\Exception $e) {
            Log::error("Permission removal failed for tenant {$tenantId}, module {$module->name}: " . $e->getMessage());
            
            // Tenant'dan çıkışı garantiye al
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}