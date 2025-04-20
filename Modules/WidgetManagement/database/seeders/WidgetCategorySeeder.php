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

        // Önceden var olan kategorileri kontrol et
        if (WidgetCategory::count() > 0) {
            $this->command->info('Kategoriler zaten mevcut, sadece eksik kategoriler eklenecek...');
        }

        $categories = [
            [
                'title' => 'Kart',
                'description' => 'Kart tipi bileşenler için şablonlar',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'İçerik',
                'description' => 'Metin ve içerik türleri için şablonlar',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Özellik',
                'description' => 'Özellik listeleme bileşenleri',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Form',
                'description' => 'Form ve giriş elemanları',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Hero',
                'description' => 'Ana başlık ve tanıtım bileşenleri',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Yerleşim',
                'description' => 'Sayfa düzeni ve yerleşim şablonları',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Medya',
                'description' => 'Görsel, video ve diğer medya elemanları',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Referans',
                'description' => 'Müşteri yorumları ve referanslar',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Slider',
                'description' => 'Slider ve carousel bileşenleri',
                'order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            WidgetCategory::firstOrCreate(
                ['title' => $category['title']],
                $category
            );
        }

        $this->command->info('Widget kategorileri başarıyla eklendi.');
    }
}