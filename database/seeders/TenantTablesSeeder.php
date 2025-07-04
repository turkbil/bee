<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Helpers\TenantHelpers;

class TenantTablesSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder hem central hem tenant'ta çalışabilir
        if (TenantHelpers::isCentral()) {
            $this->command->info('TenantTablesSeeder central veritabanında çalışıyor...');
        } else {
            $this->command->info('TenantTablesSeeder tenant veritabanında çalışıyor...');
        }
        
        try {
            $this->command->info('TenantTablesSeeder başlıyor...');
            
            if (TenantHelpers::isCentral()) {
                // Central'da demo tenant tablolar oluştur
                if (!Schema::hasTable('tenants')) {
                    $this->command->info('Central\'da tenants tablosu zaten var, demo işlem tamamlandı.');
                } else {
                    $this->command->info('Central\'da tenant table seeding işlemi tamamlandı.');
                }
            } else {
                // Tenant context'te çalışıyorsa
                $currentTenant = tenant();
                if (!$currentTenant) {
                    $this->command->warning('Geçerli tenant bulunamadı.');
                    return;
                }
                
                $this->command->info("Tenant {$currentTenant->id} için tablolar oluşturuluyor...");
                
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
                        'id' => $currentTenant->id,
                        'title' => $currentTenant->title,
                        'tenancy_db_name' => $currentTenant->tenancy_db_name,
                        'is_active' => $currentTenant->is_active,
                        'central' => $currentTenant->central,
                        'data' => json_encode($currentTenant->data),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->command->info("Tenant {$currentTenant->id}: tenants tablosu oluşturuldu");
                }
            }
            
            $this->command->info('TenantTablesSeeder tamamlandı');
        } catch (\Exception $e) {
            $message = 'TenantTablesSeeder hatası: ' . $e->getMessage();
            $this->command->error($message);
            Log::error($message);
        }
    }
}