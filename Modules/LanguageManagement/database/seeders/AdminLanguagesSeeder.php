<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\AdminLanguage;

class AdminLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
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

        foreach ($languages as $language) {
            AdminLanguage::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}