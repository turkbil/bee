<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use App\Models\Tenant;

class ModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mevcut modülleri bul
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        $moduleNames = array_map('basename', $modules);
        
        // Tüm tenant'lar için modül izinlerini oluştur
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($moduleNames, $tenant) {
                foreach ($moduleNames as $moduleName) {
                    $this->createModulePermissions($moduleName, $tenant->central);
                }
                
                // Süper admin rolüne tüm izinleri ver
                $this->assignPermissionsToSuperAdmin($tenant->central);
            });
        }
    }
    
    /**
     * Bir modül için gerekli izinleri oluşturur
     */
    private function createModulePermissions(string $moduleName, bool $isCentral): void
    {
        // Modül adını küçük harflerle ve slug formatında tutalım
        $moduleSlug = strtolower($moduleName);
        
        // Temel izinleri oluştur
        $permissions = [
            // Görüntüleme izni
            "{$moduleSlug}.view" => "{$moduleName} modülünü görüntüleme",
            
            // Ekleme izni
            "{$moduleSlug}.create" => "{$moduleName} modülüne yeni kayıt ekleme",
            
            // Düzenleme izni
            "{$moduleSlug}.edit" => "{$moduleName} modülündeki kayıtları düzenleme",
            
            // Silme izni
            "{$moduleSlug}.delete" => "{$moduleName} modülündeki kayıtları silme",
            
            // Ayarlar izni
            "{$moduleSlug}.settings" => "{$moduleName} modülü ayarlarını düzenleme",
        ];
        
        // Central tenant için ek izinler
        if ($isCentral) {
            $permissions["{$moduleSlug}.install"] = "{$moduleName} modülünü kurma/kaldırma";
            $permissions["{$moduleSlug}.export"] = "{$moduleName} modülü verilerini dışa aktarma";
            $permissions["{$moduleSlug}.import"] = "{$moduleName} modülü verilerini içe aktarma";
        }
        
        // İzinleri veritabanına kaydet
        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
            
            // Description sütunu varsa güncelle
            if (Schema::hasColumn('permissions', 'description')) {
                $permission->description = $description;
                $permission->save();
            }
        }
        
        // Rollere modül izinlerini ata
        $this->assignModulePermissionsToRoles($moduleSlug, $isCentral);
    }
    
    /**
     * Bir modül için oluşturulan izinleri ilgili rollere atar
     */
    private function assignModulePermissionsToRoles(string $moduleSlug, bool $isCentral): void
    {
        if ($isCentral) {
            // Central için roller
            $this->assignCentralModulePermissions($moduleSlug);
        } else {
            // Tenant için roller
            $this->assignTenantModulePermissions($moduleSlug);
        }
    }
    
    /**
     * Central domain için modül izinlerini rollere atar
     */
    private function assignCentralModulePermissions(string $moduleSlug): void
    {
        // Admin rolü
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                "{$moduleSlug}.view",
                "{$moduleSlug}.create",
                "{$moduleSlug}.edit",
                "{$moduleSlug}.delete",
                "{$moduleSlug}.settings",
                "{$moduleSlug}.export",
                "{$moduleSlug}.import"
            ]);
        }
        
        // Tenant-manager rolü
        $tenantManagerRole = Role::where('name', 'tenant-manager')->first();
        if ($tenantManagerRole) {
            $tenantManagerRole->givePermissionTo([
                "{$moduleSlug}.view"
            ]);
        }
        
        // Viewer rolü
        $viewerRole = Role::where('name', 'viewer')->first();
        if ($viewerRole) {
            $viewerRole->givePermissionTo([
                "{$moduleSlug}.view"
            ]);
        }
    }
    
    /**
     * Tenant domain için modül izinlerini rollere atar
     */
    private function assignTenantModulePermissions(string $moduleSlug): void
    {
        // Tenant-admin rolü
        $tenantAdminRole = Role::where('name', 'tenant-admin')->first();
        if ($tenantAdminRole) {
            $tenantAdminRole->givePermissionTo([
                "{$moduleSlug}.view",
                "{$moduleSlug}.create",
                "{$moduleSlug}.edit",
                "{$moduleSlug}.delete",
                "{$moduleSlug}.settings"
            ]);
        }
        
        // Manager rolü
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                "{$moduleSlug}.view",
                "{$moduleSlug}.create",
                "{$moduleSlug}.edit",
                "{$moduleSlug}.delete"
            ]);
        }
        
        // Editor rolü
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole) {
            $editorRole->givePermissionTo([
                "{$moduleSlug}.view",
                "{$moduleSlug}.create",
                "{$moduleSlug}.edit"
            ]);
        }
        
        // User rolü
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo([
                "{$moduleSlug}.view"
            ]);
        }
    }
    
    /**
     * Süper admin rolüne tüm izinleri atar
     */
    private function assignPermissionsToSuperAdmin(bool $isCentral): void
    {
        if ($isCentral) {
            $superAdminRole = Role::where('name', 'super-admin')->first();
        } else {
            $superAdminRole = Role::where('name', 'tenant-admin')->first();
        }
        
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::all());
        }
    }
}