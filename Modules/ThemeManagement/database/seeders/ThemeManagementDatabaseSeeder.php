<?php

namespace Modules\ThemeManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ThemeManagement\App\Models\Theme;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class ThemeManagementDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Eğer themes tablosu yoksa, bu seeder'ı çalıştırma
        if (!Schema::hasTable('themes')) {
            return;
        }
        $faker = Faker::create('tr_TR');

        // Varsayılan Tema - Eğer varsa güncelle, yoksa oluştur
        $defaultTheme = [
            'name' => 'default',
            'title' => 'Varsayılan Tema',
            'folder_name' => 'default',
            'description' => 'Sistem varsayılan teması. Temel tasarım ve renkler içerir.',
            'is_active' => true,
            'is_default' => true,
            'settings' => json_encode([
                'theme_mode' => 'light',
                'color_scheme' => 'primary',
                'default_header' => 'themes.default.headers.standard',
                'default_footer' => 'themes.default.footers.standard'
            ])
        ];
        
        Theme::updateOrCreate(
            ['name' => 'default'],
            $defaultTheme
        );

        // Diğer örnek temalar
        $temalar = [
            [
                'name' => 'dark',
                'title' => 'Koyu Tema',
                'folder_name' => 'dark',
                'description' => 'Koyu arka plan ve kontrastlı renkler içeren tema.',
                'settings' => json_encode([
                    'theme_mode' => 'dark',
                    'color_scheme' => 'dark',
                    'default_header' => 'themes.dark.headers.standard',
                    'default_footer' => 'themes.dark.footers.standard'
                ])
            ],
            [
                'name' => 'blue',
                'title' => 'Mavi Tema',
                'folder_name' => 'blue',
                'description' => 'Mavi tonları ağırlıklı kurumsal görünüm sunan tema.',
                'settings' => json_encode([
                    'theme_mode' => 'light',
                    'color_scheme' => 'blue',
                    'default_header' => 'themes.blue.headers.standard',
                    'default_footer' => 'themes.blue.footers.standard'
                ])
            ],
            [
                'name' => 'modern',
                'title' => 'Modern Tema',
                'folder_name' => 'modern',
                'description' => 'Flat tasarım ve modern UI elementleri içeren tema.',
                'settings' => json_encode([
                    'theme_mode' => 'light',
                    'color_scheme' => 'indigo',
                    'default_header' => 'themes.modern.headers.standard',
                    'default_footer' => 'themes.modern.footers.standard'
                ])
            ]
        ];

        foreach ($temalar as $tema) {
            Theme::updateOrCreate(
                ['name' => $tema['name']],
                array_merge($tema, [
                    'is_active' => $faker->boolean(80),
                    'is_default' => false,
                ])
            );
        }
    }
}