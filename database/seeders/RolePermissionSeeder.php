<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Bu seeder hem central hem tenant'ta çalışmalı
            if (\App\Helpers\TenantHelpers::isCentral()) {
                $this->command->info('Central veritabanında roller oluşturuluyor...');
                $this->seedCentralRoles();
            } else {
                $this->command->info('Tenant veritabanında roller oluşturuluyor...');
                $this->seedCurrentTenantRoles();
            }
        } catch (\Exception $e) {
            Log::error('RolePermissionSeeder hatası: ' . $e->getMessage());
            $this->command->error('RolePermissionSeeder hatası: ' . $e->getMessage());
        }
    }
    
    /**
     * Central domain için rolleri oluştur
     */
    private function seedCentralRoles(): void
    {
        try {
            $this->command->info('Central roller oluşturuluyor...');
            
            // Rolü güvenli bir şekilde oluştur veya güncelle
            $this->createOrUpdateRole('root', 'Tam yetkili sistem yöneticisi', 'root', true);
            $this->createOrUpdateRole('admin', 'Tenant yöneticisi', 'admin', true);
            $this->createOrUpdateRole('editor', 'Modül bazlı yetkilendirilebilir editör', 'editor', true);
            
            // Root kullanıcıları
            $rootUsers = [
                'nurullah@nurullah.net' => 'Nurullah Okatan',
                'info@turkbilisim.com.tr' => 'Türk Bilişim'
            ];
            
            foreach ($rootUsers as $email => $name) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make($email == 'nurullah@nurullah.net' ? 'nurullah' : 'turkbilisim'),
                        'email_verified_at' => now()
                    ]
                );
                
                // Root rolünü ata
                $user->assignRole('root');
                $this->command->info("Central: {$email} kullanıcısına root rolü atandı");
            }
            
            // SADECE laravel@test kullanıcısını Central'a ekle
            $user = User::firstOrCreate(
                ['email' => 'laravel@test'],
                [
                    'name' => 'Laravel Admin',
                    'password' => Hash::make('test'),
                    'email_verified_at' => now()
                ]
            );
            
            // Admin rolünü ekle
            $user->assignRole('admin');
            $this->command->info("Central: laravel@test kullanıcısına admin rolü atandı");
            
        } catch (\Exception $e) {
            Log::error('Central rol atama hatası: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Role'ü oluştur veya varsa güncelle
     */
    private function createOrUpdateRole($name, $description, $roleType, $isProtected)
    {
        $role = Role::where('name', $name)->first();
        
        if (!$role) {
            Role::create([
                'name' => $name,
                'guard_name' => 'web',
                'role_type' => $roleType,
                'is_protected' => $isProtected,
                'description' => $description
            ]);
        } else {
            $role->update([
                'role_type' => $roleType,
                'is_protected' => $isProtected,
                'description' => $description
            ]);
        }
    }
    
    /**
     * Geçerli tenant için rolleri oluştur (tenant context'inde çalışırken)
     */
    private function seedCurrentTenantRoles(): void
    {
        // Role yapısı
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
        
        // Rol oluştur/güncelle
        foreach ($roles as $roleData) {
            $role = Role::where('name', $roleData['name'])->first();
            
            if (!$role) {
                Role::create([
                    'name' => $roleData['name'],
                    'guard_name' => $roleData['guard_name'],
                    'role_type' => $roleData['role_type'],
                    'is_protected' => $roleData['is_protected'],
                    'description' => $roleData['description']
                ]);
            } else {
                $role->update([
                    'role_type' => $roleData['role_type'],
                    'is_protected' => $roleData['is_protected'],
                    'description' => $roleData['description']
                ]);
            }
        }
        
        // ROOT KULLANICILARI - Her tenant'a ekle
        $rootEmails = ['nurullah@nurullah.net', 'info@turkbilisim.com.tr'];
        foreach ($rootEmails as $email) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $email == 'nurullah@nurullah.net' ? 'Nurullah Okatan' : 'Türk Bilişim',
                        'password' => Hash::make($email == 'nurullah@nurullah.net' ? 'nurullah' : 'turkbilisim'),
                        'email_verified_at' => now()
                    ]
                );
                
                // Root rolünü ata - varsa ilişkiyi kaldır ve yeniden ekle
                $user->syncRoles(['root']);
                $this->command->info("Tenant: {$email} kullanıcısına root rolü atandı");
            } catch (\Exception $e) {
                Log::error("Tenant - root kullanıcı atama hatası: " . $e->getMessage());
                throw $e;
            }
        }
    }
}