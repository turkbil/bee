<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Tenant;

class FixModelHasRolesSeeder extends Seeder
{
    /**
     * Hatalı rolleri düzeltecek seeder
     */
    public function run(): void
    {
        // Önce Central için düzeltmeleri yap
        $this->fixCentralRoles();
        
        // Ardından tüm tenant'lar için düzeltmeleri yap
        $this->fixTenantRoles();
    }
    
    /**
     * Central veritabanındaki rol atamalarını düzelt
     */
    private function fixCentralRoles(): void
    {
        try {
            $this->command->info('Central rol atamalarını düzeltme başladı...');
            
            // Gerekli rol ID'lerini al
            $rootRoleId = DB::table('roles')->where('name', 'root')->value('id');
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            
            if (!$rootRoleId || !$adminRoleId) {
                $this->command->error('Roller bulunamadı!');
                return;
            }
            
            // Root kullanıcıları
            $rootUsers = [
                'nurullah@nurullah.net',
                'info@turkbilisim.com.tr'
            ];
            
            // Admin kullanıcıları
            $adminUsers = [
                'laravel@test'
            ];
            
            // Root kullanıcıları için rolleri düzelt
            foreach ($rootUsers as $email) {
                $user = DB::table('users')->where('email', $email)->first();
                
                if ($user) {
                    // Önce mevcut rol atamalarını temizle
                    DB::table('model_has_roles')
                        ->where('model_id', $user->id)
                        ->where('model_type', 'App\\Models\\User')
                        ->delete();
                    
                    // Root rolünü ata
                    DB::table('model_has_roles')->insert([
                        'role_id' => $rootRoleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id
                    ]);
                    
                    $this->command->info("Central kullanıcı {$email} için root rolü atandı");
                }
            }
            
            // Admin kullanıcıları için rolleri düzelt
            foreach ($adminUsers as $email) {
                $user = DB::table('users')->where('email', $email)->first();
                
                if ($user) {
                    // Önce mevcut rol atamalarını temizle
                    DB::table('model_has_roles')
                        ->where('model_id', $user->id)
                        ->where('model_type', 'App\\Models\\User')
                        ->delete();
                    
                    // Admin rolünü ata
                    DB::table('model_has_roles')->insert([
                        'role_id' => $adminRoleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id
                    ]);
                    
                    $this->command->info("Central kullanıcı {$email} için admin rolü atandı");
                }
            }
            
            $this->command->info('Central rol atamaları başarıyla düzeltildi');
        } catch (\Exception $e) {
            $this->command->error('Central rol atamaları düzeltilirken hata: ' . $e->getMessage());
            Log::error('Central rol atamaları düzeltilirken hata: ' . $e->getMessage());
        }
    }
    
    /**
     * Tenant veritabanlarındaki rol atamalarını düzelt
     */
    private function fixTenantRoles(): void
    {
        $tenants = Tenant::where('central', false)->get();
        
        foreach ($tenants as $tenant) {
            try {
                $tenant->run(function () use ($tenant) {
                    $this->command->info("Tenant {$tenant->id} ({$tenant->domain}) için rol atamaları düzeltiliyor...");
                    
                    // Gerekli rol ID'lerini al
                    $rootRoleId = DB::table('roles')->where('name', 'root')->value('id');
                    $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
                    
                    if (!$rootRoleId || !$adminRoleId) {
                        $this->command->error("Tenant {$tenant->id}: Roller bulunamadı!");
                        return;
                    }
                    
                    // Root kullanıcıları
                    $rootUsers = [
                        'nurullah@nurullah.net',
                        'info@turkbilisim.com.tr'
                    ];
                    
                    // Tenant'a özel admin kullanıcısı
                    $tenantPrefix = substr($tenant->domain, 0, 1); // a.test -> a, b.test -> b
                    $adminEmail = "{$tenantPrefix}@test";
                    
                    // Root kullanıcıları için rolleri düzelt
                    foreach ($rootUsers as $email) {
                        $user = DB::table('users')->where('email', $email)->first();
                        
                        if ($user) {
                            // Önce mevcut rol atamalarını temizle
                            DB::table('model_has_roles')
                                ->where('model_id', $user->id)
                                ->where('model_type', 'App\\Models\\User')
                                ->delete();
                            
                            // Root rolünü ata
                            DB::table('model_has_roles')->insert([
                                'role_id' => $rootRoleId,
                                'model_type' => 'App\\Models\\User',
                                'model_id' => $user->id
                            ]);
                            
                            $this->command->info("Tenant {$tenant->id}: Kullanıcı {$email} için root rolü atandı");
                        }
                    }
                    
                    // Admin kullanıcısı için rolleri düzelt
                    $adminUser = DB::table('users')->where('email', $adminEmail)->first();
                    
                    if ($adminUser) {
                        // Önce mevcut rol atamalarını temizle
                        DB::table('model_has_roles')
                            ->where('model_id', $adminUser->id)
                            ->where('model_type', 'App\\Models\\User')
                            ->delete();
                        
                        // Admin rolünü ata
                        DB::table('model_has_roles')->insert([
                            'role_id' => $adminRoleId,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $adminUser->id
                        ]);
                        
                        $this->command->info("Tenant {$tenant->id}: Kullanıcı {$adminEmail} için admin rolü atandı");
                    }
                    
                    $this->command->info("Tenant {$tenant->id} için rol atamaları başarıyla düzeltildi");
                });
            } catch (\Exception $e) {
                $this->command->error("Tenant {$tenant->id} rol atamaları düzeltilirken hata: " . $e->getMessage());
                Log::error("Tenant {$tenant->id} rol atamaları düzeltilirken hata: " . $e->getMessage());
            }
        }
    }
}