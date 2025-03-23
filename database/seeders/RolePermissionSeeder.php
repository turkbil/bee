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
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }
            
            // YENİ: Role yapısı - Root, Admin, Editor
            $roles = [
                [
                    'name' => 'root',
                    'role_type' => 'root',
                    'guard_name' => 'web',
                    'is_protected' => true,
                    'description' => 'Tam yetkili sistem yöneticisi'
                ],
                [
                    'name' => 'admin',
                    'role_type' => 'admin',
                    'guard_name' => 'web',
                    'is_protected' => true,
                    'description' => 'Tenant yöneticisi'
                ],
                [
                    'name' => 'editor',
                    'role_type' => 'editor',
                    'guard_name' => 'web',
                    'is_protected' => true,
                    'description' => 'Modül bazlı yetkilendirilebilir editör'
                ]
            ];
            
            foreach ($roles as $roleData) {
                $role = Role::firstOrCreate([
                    'name' => $roleData['name'],
                    'guard_name' => $roleData['guard_name']
                ]);
                
                // Diğer alanları güncelle
                $role->role_type = $roleData['role_type'];
                $role->is_protected = $roleData['is_protected'];
                
                // Description sütunu varsa güncelle
                if (Schema::hasColumn('roles', 'description')) {
                    $role->description = $roleData['description'];
                }
                
                $role->save();
                
                // Root tüm izinlere sahip olsun
                if ($roleData['role_type'] === 'root') {
                    $role->syncPermissions(Permission::all());
                }
                
                // Admin tenant.delete hariç tüm izinlere sahip olsun
                if ($roleData['role_type'] === 'admin') {
                    $role->syncPermissions(Permission::whereNotIn('name', ['tenant.delete'])->get());
                }
                
                // Editor için şu aşamada özel izin ataması yapmıyoruz
                // Bu modül bazlı olarak yapılacak
            }
            
            // Mevcut kullanıcılara roller ata
            $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
            if ($nurullah) {
                $nurullah->syncRoles(['root']);
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
                    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }
                
                // YENİ: Role yapısı - Root, Admin, Editor (tenant seviyesinde)
                $roles = [
                    [
                        'name' => 'root',
                        'role_type' => 'root',
                        'guard_name' => 'web',
                        'is_protected' => true,
                        'description' => 'Tam yetkili tenant yöneticisi'
                    ],
                    [
                        'name' => 'admin',
                        'role_type' => 'admin',
                        'guard_name' => 'web',
                        'is_protected' => true,
                        'description' => 'Tenant yöneticisi'
                    ],
                    [
                        'name' => 'editor',
                        'role_type' => 'editor',
                        'guard_name' => 'web',
                        'is_protected' => true,
                        'description' => 'Modül bazlı yetkilendirilebilir editör'
                    ]
                ];
                
                foreach ($roles as $roleData) {
                    $role = Role::firstOrCreate([
                        'name' => $roleData['name'],
                        'guard_name' => $roleData['guard_name']
                    ]);
                    
                    // Diğer alanları güncelle
                    $role->role_type = $roleData['role_type'];
                    $role->is_protected = $roleData['is_protected'];
                    
                    // Description sütunu varsa güncelle
                    if (Schema::hasColumn('roles', 'description')) {
                        $role->description = $roleData['description'];
                    }
                    
                    $role->save();
                    
                    // Root tüm izinlere sahip olsun
                    if ($roleData['role_type'] === 'root') {
                        $role->syncPermissions(Permission::all());
                    }
                    
                    // Admin içerik.delete hariç tüm izinlere sahip olsun
                    if ($roleData['role_type'] === 'admin') {
                        $role->syncPermissions(Permission::all());
                    }
                }
                
                // Mevcut kullanıcılara roller ata
                $tenantAdmin = User::where('email', $tenant->id . '@test')->first();
                if ($tenantAdmin) {
                    $tenantAdmin->syncRoles(['root']);
                }
                
                $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
                if ($nurullah) {
                    $nurullah->syncRoles(['root']);
                }
                
                // Diğer kullanıcılara rastgele editor veya admin atayalım
                User::whereNotIn('email', [$tenant->id . '@test', 'nurullah@nurullah.net'])
                    ->get()
                    ->each(function ($user) {
                        $roles = ['admin', 'editor'];
                        $user->syncRoles([$roles[array_rand($roles)]]);
                    });
            });
        }
    }
}