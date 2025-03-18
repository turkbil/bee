<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Tenant;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Central domain için yetkilendirmeler
        $this->seedCentralPermissions();
        
        // Tenant'lar için yetkilendirmeler
        $this->seedTenantPermissions();
    }
    
    /**
     * Central domain için temel yetkilendirmeleri oluştur
     */
    private function seedCentralPermissions(): void
    {
        // Central domaininde çalıştır
        $centralTenant = Tenant::where('central', true)->first();
        
        if (!$centralTenant) {
            $this->command->warn('Central tenant bulunamadı!');
            return;
        }
        
        $centralTenant->run(function () {
            // Temel izinleri oluştur
            $permissions = [
                // Tenant yönetimi
                'tenant.view', 'tenant.create', 'tenant.edit', 'tenant.delete',
                
                // Kullanıcı yönetimi
                'user.view', 'user.create', 'user.edit', 'user.delete',
                
                // Rol yönetimi
                'role.view', 'role.create', 'role.edit', 'role.delete',
                
                // İzin yönetimi
                'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
                
                // Modül yönetimi
                'module.view', 'module.create', 'module.edit', 'module.delete', 'module.install',
                
                // Log görüntüleme
                'log.view',
                
                // Sistem ayarları
                'settings.view', 'settings.edit'
            ];
            
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
            
            // Rolleri oluştur
            $roles = [
                'super-admin' => 'Tüm yetkilere sahip süper yönetici',
                'admin' => 'Sistem yöneticisi',
                'tenant-manager' => 'Tenant yöneticisi',
                'user-manager' => 'Kullanıcı yöneticisi',
                'viewer' => 'Salt görüntüleme yetkisi olan kullanıcı'
            ];
            
            foreach ($roles as $roleName => $description) {
                $role = Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);
                
                // Description sütunu varsa güncelle
                if (Schema::hasColumn('roles', 'description')) {
                    $role->description = $description;
                    $role->save();
                }
                
                // Super-admin tüm izinlere sahip olsun
                if ($roleName === 'super-admin') {
                    $role->givePermissionTo(Permission::all());
                }
                
                // Admin rolü tenant.delete hariç tüm izinlere sahip olsun
                if ($roleName === 'admin') {
                    $role->givePermissionTo(Permission::whereNotIn('name', ['tenant.delete'])->get());
                }
                
                // Tenant-manager tenant ve kullanıcı yönetimi yapabilsin
                if ($roleName === 'tenant-manager') {
                    $role->givePermissionTo([
                        'tenant.view', 'tenant.create', 'tenant.edit',
                        'user.view', 'user.create', 'user.edit',
                        'log.view'
                    ]);
                }
                
                // User-manager sadece kullanıcı yönetimi yapabilsin
                if ($roleName === 'user-manager') {
                    $role->givePermissionTo([
                        'user.view', 'user.create', 'user.edit', 'user.delete'
                    ]);
                }
                
                // Viewer sadece görüntüleme yetkilerine sahip olsun
                if ($roleName === 'viewer') {
                    $role->givePermissionTo([
                        'tenant.view', 'user.view', 'role.view', 'permission.view', 
                        'module.view', 'log.view', 'settings.view'
                    ]);
                }
            }
            
            // Mevcut kullanıcılara roller ata
            $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
            if ($nurullah) {
                $nurullah->assignRole('super-admin');
            }
        });
    }
    
    /**
     * Tenant'lar için temel yetkilendirmeleri oluştur
     */
    private function seedTenantPermissions(): void
    {
        $tenants = Tenant::where('central', false)->get();
        
        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant) {
                // Temel izinleri oluştur
                $permissions = [
                    // Kullanıcı yönetimi
                    'user.view', 'user.create', 'user.edit', 'user.delete',
                    
                    // Rol yönetimi
                    'role.view', 'role.create', 'role.edit', 'role.delete',
                    
                    // İzin yönetimi
                    'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
                    
                    // İçerik yönetimi
                    'content.view', 'content.create', 'content.edit', 'content.delete',
                    
                    // Dosya yönetimi
                    'media.view', 'media.upload', 'media.edit', 'media.delete',
                    
                    // Log görüntüleme
                    'log.view',
                    
                    // Tenant ayarları
                    'settings.view', 'settings.edit'
                ];
                
                foreach ($permissions as $permission) {
                    Permission::create(['name' => $permission, 'guard_name' => 'web']);
                }
                
                // Rolleri oluştur
                $roles = [
                    'tenant-admin' => 'Tenant yöneticisi',
                    'manager' => 'Bölüm yöneticisi',
                    'editor' => 'İçerik editörü',
                    'user' => 'Normal kullanıcı',
                    'guest' => 'Misafir kullanıcı'
                ];
                
                foreach ($roles as $roleName => $description) {
                    $role = Role::create([
                        'name' => $roleName,
                        'guard_name' => 'web',
                        'description' => $description
                    ]);
                    
                    // Tenant-admin rolü tüm izinlere sahip olsun
                    if ($roleName === 'tenant-admin') {
                        $role->givePermissionTo(Permission::all());
                    }
                    
                    // Manager rolü kullanıcı ve içerik yönetimi yapabilsin
                    if ($roleName === 'manager') {
                        $role->givePermissionTo([
                            'user.view', 'user.create', 'user.edit',
                            'content.view', 'content.create', 'content.edit', 'content.delete',
                            'media.view', 'media.upload', 'media.edit', 'media.delete',
                            'log.view'
                        ]);
                    }
                    
                    // Editor rolü sadece içerik düzenleyebilsin
                    if ($roleName === 'editor') {
                        $role->givePermissionTo([
                            'content.view', 'content.create', 'content.edit',
                            'media.view', 'media.upload', 'media.edit'
                        ]);
                    }
                    
                    // User rolü sadece görüntüleme yetkilerine sahip olsun
                    if ($roleName === 'user') {
                        $role->givePermissionTo([
                            'content.view',
                            'media.view'
                        ]);
                    }
                    
                    // Guest rolü minimum izinlere sahip olsun
                    if ($roleName === 'guest') {
                        $role->givePermissionTo([
                            'content.view'
                        ]);
                    }
                }
                
                // Mevcut kullanıcılara roller ata
                $tenantAdmin = User::where('email', $tenant->id . '@test')->first();
                if ($tenantAdmin) {
                    $tenantAdmin->assignRole('tenant-admin');
                }
                
                $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
                if ($nurullah) {
                    $nurullah->assignRole('tenant-admin');
                }
                
                // Diğer kullanıcılara rastgele roller ata
                User::whereNotIn('email', [$tenant->id . '@test', 'nurullah@nurullah.net'])
                    ->get()
                    ->each(function ($user) {
                        $roles = ['manager', 'editor', 'user', 'guest'];
                        $user->assignRole($roles[array_rand($roles)]);
                    });
            });
        }
    }
}