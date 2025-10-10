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

            // TenantSeeder - sadece local/testing ortamında çalıştır
            // Production'da CREATE DATABASE izni olmadığı için skip edilir
            if (app()->environment(['local', 'testing'])) {
                $this->command->info('🏠 Local/Testing environment - TenantSeeder çalıştırılıyor...');
                $this->call(TenantSeeder::class);

                // TenantSeeder'dan sonra context'i AGRESIVE şekilde central'a geri döndür
                tenancy()->end();
            } else {
                $this->command->info('🚀 Production environment - Central tenant oluşturuluyor...');
                $this->call(ProductionTenantSeeder::class);
            }
            
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

            // AI Provider'lar ve modelleri (central'da tutulur)
            $this->call(\Modules\AI\Database\Seeders\AIProviderSeeder::class);

            // Shop kategorileri (central'da tutulur)
            $this->call(ShopCategorySeeder::class);

            // ModuleManagement seeder'ı (modules tablosuna kayıt ekler - EN ÖNEMLİ!)
            $this->call(\Modules\ModuleManagement\Database\Seeders\ModuleManagementSeeder::class);

            // ModuleSeeder'dan önce context'i tekrar garanti altına al
            tenancy()->end();
            if (!TenantHelpers::isCentral()) {
                app()->forgetInstance('tenant.current');
                config(['database.default' => 'mysql']);
                $this->command->info('⚠️ ModuleSeeder öncesi ZORLA CENTRAL CONTEXT!');
            }
            $this->command->info('🔄 ModuleSeeder öncesi context kontrolü: ' . (TenantHelpers::isCentral() ? 'CENTRAL ✅' : 'TENANT ❌'));

            // Modül seeder'ları (central context'te) - diğer modüllerin içerik seeder'ları
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