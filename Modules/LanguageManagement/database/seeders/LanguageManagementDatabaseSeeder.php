<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\SystemLanguage;
use Modules\LanguageManagement\app\Models\SiteLanguage;

class LanguageManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedSystemLanguages();
        $this->seedSiteLanguages();
    }

    private function seedSystemLanguages(): void
    {
        $systemLanguages = [
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

        foreach ($systemLanguages as $language) {
            SystemLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('System languages seeded successfully.');
    }

    private function seedSiteLanguages(): void
    {
        // Her tenant kendi site dillerini yönetecek
        // Sadece TR'yi default olarak ekleyelim
        $siteLanguages = [
            [
                'code' => 'tr',
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'direction' => 'ltr',
                'flag_icon' => '🇹🇷',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($siteLanguages as $language) {
            SiteLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Site languages seeded successfully.');
    }
}
