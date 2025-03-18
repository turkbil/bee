<?php

namespace Modules\ModuleManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ModuleManagement\App\Models\Module;
use Stancl\Tenancy\Tenancy;

class ModuleTenantsSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Eğer tenant kontekstinde çalışıyorsa, seederi çalıştırma
        if (app()->has('tenancy') && app(Tenancy::class)->initialized) {
            return;
        }

        try {
            // Önce tüm mevcut tenant ve modülleri alalım
            $tenants = DB::table('tenants')->pluck('id')->toArray();
            $modules = Module::all();

            // Foreign key constraint hatası için DISABLE FOREIGN_KEY_CHECKS
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Temizlik yapalım önce
            DB::table('module_tenants')->truncate();
            
            // Foreign key constraint'leri tekrar aktif et
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Her tenant ve modül kombinasyonu için rastgele atamalar yapalım
            // Ama bazı tenant'larda bazı modüller eksik olacak şekilde
            foreach ($tenants as $tenant) {
                // Her tenant için farklı bir seed kullanarak
                $tenantSeed = crc32($tenant);
                srand($tenantSeed);
                
                foreach ($modules as $module) {
                    // %70 ihtimalle modülü tenant'a ekleyelim (%30 ihtimalle eksik olacak)
                    if (rand(1, 100) <= 50) {
                        // %80 ihtimalle aktif olsun
                        $isActive = rand(1, 100) <= 90;
                        
                        DB::table('module_tenants')->insert([
                            'tenant_id' => $tenant,
                            'module_id' => $module->module_id,
                            'is_active' => $isActive,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            
            $this->command->info('Modules-Tenant assignments seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Modules-Tenant seeding failed: ' . $e->getMessage());
            $this->command->error('Modules-Tenant seeding error: ' . $e->getMessage());
        }
    }
}