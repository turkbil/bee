<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\AdminLanguage;
use Modules\LanguageManagement\app\Models\TenantLanguage;
use App\Helpers\TenantHelpers;

class LanguageManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Context-aware çalışma
        if (TenantHelpers::isCentral()) {
            $this->command->info('Central context: Admin languages oluşturuluyor...');
            $this->seedAdminLanguages();
        } else {
            $this->command->info('Tenant context: Tenant languages oluşturuluyor...');
            $this->seedTenantLanguages();
        }
    }

    private function seedAdminLanguages(): void
    {
        $adminLanguages = [
            [
                'code' => 'tr',
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'direction' => 'ltr',
                'flag_icon' => '🇹🇷',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'flag_icon' => '🇺🇸',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($adminLanguages as $language) {
            AdminLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Admin languages seeded successfully.');
    }

    private function seedTenantLanguages(): void
    {
        // Her tenant kendi site dillerini yönetecek
        // Sadece TR'yi default olarak ekleyelim
        $tenantLanguages = [
            [
                'code' => 'tr',
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'direction' => 'ltr',
                'flag_icon' => '🇹🇷',
                'is_active' => true,
                // is_default kaldırıldı - artık tenants.tenant_default_locale'de
                'sort_order' => 1,
            ],
        ];

        foreach ($tenantLanguages as $language) {
            TenantLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Tenant languages seeded successfully.');
    }
}
