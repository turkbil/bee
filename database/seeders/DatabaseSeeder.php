<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Önce tenant'ları oluştur
        $this->call(TenantSeeder::class);

        // Rol ve izinleri oluştur
        $this->call(RolePermissionSeeder::class);
        
        // Modüllere özel izinleri oluştur
        $this->call(ModulePermissionSeeder::class);
        
        // En son modül seeder'larını çalıştır
        $this->call(ModuleSeeder::class);
    }
}