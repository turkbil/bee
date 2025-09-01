<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\TenantHelpers;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (TenantHelpers::isCentral()) {
            $this->command->info('=== CENTRAL DATABASE SEEDING ===');
            
            // Central-only seeder'lar
            $this->call(ThemesSeeder::class);
            $this->call(\Modules\LanguageManagement\Database\Seeders\AdminLanguagesSeeder::class);
            $this->call(TenantSeeder::class);
            
            // TenantSeeder'dan sonra context'i AGRESIVE şekilde central'a geri döndür
            tenancy()->end();
            
            // Context durumunu kontrol et ve zorla central'a al
            if (!TenantHelpers::isCentral()) {
                // Eğer hala tenant context'indeyse, zorla central'a dön
                app()->forgetInstance('tenant.current');
                config(['database.default' => 'mysql']);
                $this->command->info('⚠️ ZORLA CENTRAL CONTEXT\'E DÖNDÜRÜLDÜ!');
            }
            
            $this->command->info('✅ Context reset to central after TenantSeeder - Current: ' . (TenantHelpers::isCentral() ? 'CENTRAL' : 'TENANT'));
            
            // Central rol ve izinleri
            $this->call(RolePermissionSeeder::class);
            $this->call(ModulePermissionSeeder::class);
            $this->call(FixModelHasRolesSeeder::class);
            
            // AI Credit Packages (central'da tutulur)
            $this->call(AICreditPackageSeeder::class);
            
            // AI Provider'lar ve modelleri (ModuleSeeder'da çalışır)
            
            // ModuleSeeder'dan önce context'i tekrar garanti altına al
            tenancy()->end();
            if (!TenantHelpers::isCentral()) {
                app()->forgetInstance('tenant.current');
                config(['database.default' => 'mysql']);
                $this->command->info('⚠️ ModuleSeeder öncesi ZORLA CENTRAL CONTEXT!');
            }
            $this->command->info('🔄 ModuleSeeder öncesi context kontrolü: ' . (TenantHelpers::isCentral() ? 'CENTRAL ✅' : 'TENANT ❌'));
            
            // Modül seeder'ları (central context'te)
            $this->call(ModuleSeeder::class);
            
        } else {
            $this->command->info('=== TENANT DATABASE SEEDING ===');
            
            // Tenant-only seeder'lar
            $this->call(\Modules\LanguageManagement\Database\Seeders\TenantLanguagesSeeder::class);
            $this->call(RolePermissionSeeder::class); // Tenant rolleri için
            $this->call(TenantTablesSeeder::class);
            
            // MenuManagement seeder (tenant context'te çalışmalı)
            $this->call(\Modules\MenuManagement\Database\Seeders\MenuManagementSeeder::class);
            
            // Modül seeder'ları (tenant context'te)
            $this->call(ModuleSeeder::class);
        }
    }
}