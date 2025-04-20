<?php
namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Facades\Schema;

class WidgetCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Tablo var mı kontrol et
        if (!Schema::hasTable('widget_categories')) {
            $this->command->info('widget_categories tablosu bulunamadı, işlem atlanıyor...');
            return;
        }

        $categories = [
            [
                'title' => 'Temel Bileşenler',
                'description' => 'Temel web site bileşenleri için şablonlar',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'İçerik Bileşenleri',
                'description' => 'Metin, resim ve diğer içerik türleri için şablonlar',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Etkileşimli Bileşenler',
                'description' => 'Formlar, butonlar ve diğer etkileşimli unsurlar',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Modül Bileşenleri',
                'description' => 'Modüller için özel bileşenler',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Medya Bileşenleri',
                'description' => 'Galeri, video ve diğer medya elemanları',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Blok Bileşenleri',
                'description' => 'Sayfa bölümleri ve blokları için şablonlar',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            WidgetCategory::create($category);
        }

        $this->command->info('Widget kategorileri başarıyla eklendi.');
    }
}