<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Önce tenant'ları oluştur
        $this->call(TenantSeeder::class);
        
        // Rol ve izinleri oluştur
        $this->call(RolePermissionSeeder::class);
        
        // Modül tenant tabloları oluştur
        $this->call(TenantTablesSeeder::class);
        
        // Modüllere özel izinleri oluştur
        $this->call(ModulePermissionSeeder::class);
        
        // Hatalı rol atamalarını düzelt
        $this->call(FixModelHasRolesSeeder::class);
        
        // En son modül seeder'larını çalıştır
        $this->call(ModuleSeeder::class);
    }
}