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

        // Varsayılan Tema
        Theme::create([
            'name' => 'default',
            'title' => 'Varsayılan Tema',
            'folder_name' => 'default',
            'description' => 'Sistem varsayılan teması. Temel tasarım ve renkler içerir.',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Diğer örnek temalar
        $temalar = [
            [
                'name' => 'dark',
                'title' => 'Koyu Tema',
                'folder_name' => 'dark',
                'description' => 'Koyu arka plan ve kontrastlı renkler içeren tema.',
            ],
            [
                'name' => 'blue',
                'title' => 'Mavi Tema',
                'folder_name' => 'blue',
                'description' => 'Mavi tonları ağırlıklı kurumsal görünüm sunan tema.',
            ],
            [
                'name' => 'modern',
                'title' => 'Modern Tema',
                'folder_name' => 'modern',
                'description' => 'Flat tasarım ve modern UI elementleri içeren tema.',
            ]
        ];

        foreach ($temalar as $tema) {
            Theme::create(array_merge($tema, [
                'is_active' => $faker->boolean(80),
                'is_default' => false,
            ]));
        }
    }
}