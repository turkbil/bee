<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Str;

class ContentCategoriesSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, ContentCategoriesSeeder atlanıyor.');
            }
            return;
        }
        
        // Ana "İçerikler" kategorisini oluştur
        $contentCategory = WidgetCategory::firstOrCreate(
            ['slug' => 'icerikler'],
            [
                'title' => 'İçerikler',
                'description' => 'İçerik modüllerine ait bileşenler',
                'order' => 1,
                'is_active' => true,
                'icon' => 'fa-file-alt',
                'parent_id' => null
            ]
        );
        
        // Alt kategori olarak "Page Modülü" ekle
        WidgetCategory::firstOrCreate(
            ['slug' => 'sayfa-modulu'],
            [
                'title' => 'Sayfa Modülü',
                'description' => 'Sayfa içeriklerine ait bileşenler',
                'order' => 1,
                'is_active' => true,
                'icon' => 'fa-file',
                'parent_id' => $contentCategory->widget_category_id
            ]
        );
        
        // Alt kategori olarak "Portfolio Modülü" ekle
        WidgetCategory::firstOrCreate(
            ['slug' => 'portfolio-modulu'],
            [
                'title' => 'Portfolio Modülü',
                'description' => 'Portfolio içeriklerine ait bileşenler',
                'order' => 2,
                'is_active' => true,
                'icon' => 'fa-images',
                'parent_id' => $contentCategory->widget_category_id
            ]
        );
        
        $this->command->info('İçerik kategorileri başarıyla oluşturuldu.');
    }
}