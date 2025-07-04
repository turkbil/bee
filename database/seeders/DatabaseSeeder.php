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
            
            // Central rol ve izinleri
            $this->call(RolePermissionSeeder::class);
            $this->call(ModulePermissionSeeder::class);
            $this->call(FixModelHasRolesSeeder::class);
            
            // Modül seeder'ları (central context'te)
            $this->call(ModuleSeeder::class);
            
        } else {
            $this->command->info('=== TENANT DATABASE SEEDING ===');
            
            // Tenant-only seeder'lar
            $this->call(\Modules\LanguageManagement\Database\Seeders\TenantLanguagesSeeder::class);
            $this->call(RolePermissionSeeder::class); // Tenant rolleri için
            $this->call(TenantTablesSeeder::class);
            
            // Modül seeder'ları (tenant context'te)
            $this->call(ModuleSeeder::class);
        }
    }
}