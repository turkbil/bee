<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ThemeManagement\App\Models\Theme;
use Illuminate\Support\Facades\Schema;

class ThemesSeeder extends Seeder
{
    public function run(): void
    {
        // Eğer themes tablosu yoksa, bu seeder'ı çalıştırma
        if (!Schema::hasTable('themes')) {
            return;
        }

        // Varsayılan Tema (blank)
        Theme::create([
            'name' => 'blank',
            'title' => 'Blank Tema',
            'folder_name' => 'blank',
            'description' => 'Boş tema (blank), temel dizayn için test.',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Diğer örnek temalar (production-ready, hard-coded)
        $temalar = [
            [
                'name' => 'dark',
                'title' => 'Koyu Tema',
                'folder_name' => 'dark',
                'description' => 'Koyu arka plan ve kontrastlı renkler içeren tema.',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'blue',
                'title' => 'Mavi Tema',
                'folder_name' => 'blue',
                'description' => 'Mavi tonları ağırlıklı kurumsal görünüm sunan tema.',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'modern',
                'title' => 'Modern Tema',
                'folder_name' => 'modern',
                'description' => 'Flat tasarım ve modern UI elementleri içeren tema.',
                'is_active' => false,
                'is_default' => false,
            ]
        ];

        foreach ($temalar as $tema) {
            Theme::create($tema);
        }
    }
}