<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\SiteLanguage;

class SiteLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Her tenant için varsayılan Türkçe dili oluştur
        $defaultLanguage = [
            'code' => 'tr',
            'name' => 'Turkish',
            'native_name' => 'Türkçe',
            'direction' => 'ltr',
            'flag_icon' => '🇹🇷',
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1,
        ];

        SiteLanguage::updateOrCreate(
            ['code' => $defaultLanguage['code']],
            $defaultLanguage
        );
    }
}