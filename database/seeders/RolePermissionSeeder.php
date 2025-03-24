<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
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
        $this->seedCentralRoles();
        
        // Tenant'lar için yetkilendirmeler
        $this->seedTenantRoles();
    }
    
    /**
     * Central domain için rolleri oluştur
     */
    private function seedCentralRoles(): void
    {
        // Central domaininde çalıştır
        $centralTenant = Tenant::where('central', true)->first();
        
        if (!$centralTenant) {
            $this->command->warn('Central tenant bulunamadı!');
            return;
        }
        
        $centralTenant->run(function () {
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
            }
            
            // Kullanıcılara roller ata
            $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
            if ($nurullah) {
                $nurullah->syncRoles(['root']);
            }
            
            $turkbilisim = User::where('email', 'info@turkbilisim.com.tr')->first();
            if ($turkbilisim) {
                $turkbilisim->syncRoles(['root']);
            }
            
            $admin = User::where('email', 'laravel@test')->first();
            if ($admin) {
                $admin->syncRoles(['admin']);
            }
            
            // Diğer kullanıcılara rastgele editor veya admin atayalım
            User::whereNotIn('email', ['nurullah@nurullah.net', 'info@turkbilisim.com.tr', 'laravel@test'])
                ->get()
                ->each(function ($user) {
                    $roles = ['admin', 'editor'];
                    $user->syncRoles([$roles[array_rand($roles)]]);
                });
        });
    }
    
    /**
     * Tenant'lar için rolleri oluştur
     */
    private function seedTenantRoles(): void
    {
        $tenants = Tenant::where('central', false)->get();
        
        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant) {
                // YENİ: Role yapısı - Root, Admin, Editor (tenant seviyesinde)
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
                        'description' => 'Site sorumlusu ve yöneticisi'
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
                }
                
                // Mevcut kullanıcılara roller ata
                // Tenant email'lerini değiştir (@test formatını kullan)
                $tenantAdmin = null;
                if ($tenant->id == 'a') {
                    $tenantAdmin = User::where('email', 'a@test')->first();
                } else if ($tenant->id == 'b') {
                    $tenantAdmin = User::where('email', 'b@test')->first();
                } else if ($tenant->id == 'c') {
                    $tenantAdmin = User::where('email', 'c@test')->first();
                }
                
                if ($tenantAdmin) {
                    $tenantAdmin->syncRoles(['admin']);
                }
                
                // Ana kullanıcılara root rolü ata
                $nurullah = User::where('email', 'nurullah@nurullah.net')->first();
                if ($nurullah) {
                    $nurullah->syncRoles(['root']);
                }
                
                $turkbilisim = User::where('email', 'info@turkbilisim.com.tr')->first();
                if ($turkbilisim) {
                    $turkbilisim->syncRoles(['root']);
                }
                
                // Diğer kullanıcılara rastgele editor veya admin atayalım
                User::whereNotIn('email', ['a@test', 'b@test', 'c@test', 'nurullah@nurullah.net', 'info@turkbilisim.com.tr'])
                    ->get()
                    ->each(function ($user) {
                        $roles = ['admin', 'editor'];
                        $user->syncRoles([$roles[array_rand($roles)]]);
                    });
            });
        }
    }
}