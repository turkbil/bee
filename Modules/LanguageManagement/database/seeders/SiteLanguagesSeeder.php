<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\SiteLanguage;

class SiteLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temel desteklenen diller
        $languages = [
            [
                'code' => 'tr',
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'direction' => 'ltr',
                'flag_icon' => '🇹🇷',
                'is_active' => true,
                'is_default' => true,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 1,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'flag_icon' => '🇺🇸',
                'is_active' => true,
                'is_default' => false,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 2,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'flag_icon' => '🇸🇦',
                'is_active' => true,
                'is_default' => false,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 3,
            ],
        ];

        foreach ($languages as $language) {
            SiteLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}