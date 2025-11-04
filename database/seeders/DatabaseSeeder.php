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
            // AdminLanguagesSeeder temporarily disabled - will be handled in ModuleSeeder
            // $this->call(\Modules\LanguageManagement\Database\Seeders\AdminLanguagesSeeder::class);

            // TenantSeeder
            if (app()->environment(['local', 'testing'])) {
                $this->command->info('🏠 Local/Testing environment - TenantSeeder çalıştırılıyor...');
                $this->call(TenantSeeder::class);
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

            // AI Workflow Nodes (central'da tutulur)
            $this->call(AIWorkflowNodesSeeder::class);

            // Shop markası (tenant'larda tutulur - ModuleSeeder'da çalışır ama sıralama için burada not)
            // Shop kategorileri (central'da tutulur)
            $this->call(\Modules\Shop\Database\Seeders\ShopCategorySeeder::class);

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

            // Basit ve çalışan tenant seeder
            $this->call(TenantDatabaseSeeder::class);
        }

        // Central tenant için sadece AI Knowledge Base seed et
        // (Diğer veriler ModuleSeeder'da zaten ekleniyor)
        if (TenantHelpers::isCentral()) {
            $this->command->info('🏠 Running AI Knowledge Base seeder for central tenant (laravel)...');

            // Central tenant'ı bul
            $centralTenant = \App\Models\Tenant::where('central', true)->first();

            if ($centralTenant) {
                // Tenant context'e gir
                tenancy()->initialize($centralTenant);

                // Sadece AI Knowledge Base seeder'ını çalıştır
                $this->call(\Modules\SettingManagement\Database\Seeders\AIKnowledgeBaseSeeder::class);

                // Context'den çık
                tenancy()->end();

                $this->command->info('✅ Central tenant AI Knowledge Base seeding completed!');
            }
        }
    }
}