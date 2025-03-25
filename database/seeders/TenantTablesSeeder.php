<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class TenantTablesSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $this->command->info('TenantTablesSeeder başlıyor...');
            
            $tenants = Tenant::where('central', false)->get();
            
            foreach ($tenants as $tenant) {
                $tenant->run(function () use ($tenant) {
                    $this->command->info("Tenant {$tenant->id} için tablolar oluşturuluyor...");
                    
                    // tenants tablosu - module_tenants ilişkisi için gerekli
                    if (!Schema::hasTable('tenants')) {
                        Schema::create('tenants', function ($table) {
                            $table->id();
                            $table->string('title', 100);
                            $table->string('tenancy_db_name', 50)->nullable();
                            $table->boolean('is_active')->default(true);
                            $table->boolean('central')->default(false);
                            $table->json('data')->nullable();
                            $table->timestamps();
                        });
                        
                        // Bu tenant'ı kendi tenant tablosuna ekle
                        DB::table('tenants')->insert([
                            'id' => $tenant->id,
                            'title' => $tenant->title,
                            'tenancy_db_name' => $tenant->tenancy_db_name,
                            'is_active' => $tenant->is_active,
                            'central' => $tenant->central,
                            'data' => json_encode($tenant->data),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $this->command->info("Tenant {$tenant->id}: tenants tablosu oluşturuldu");
                    }
                    
                    // module_tenants tablosunu tenant veritabanlarında OLUŞTURMA
                    // Bu tablo sadece central veritabanında olacak
                });
            }
            
            $this->command->info('TenantTablesSeeder tamamlandı');
        } catch (\Exception $e) {
            $message = 'TenantTablesSeeder hatası: ' . $e->getMessage();
            $this->command->error($message);
            Log::error($message);
        }
    }
}