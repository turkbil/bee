<?php
// Modules/WidgetManagement/database/seeders/PageWidgetSeeder.php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Str;

class PageWidgetSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, PageWidgetSeeder atlanıyor.');
            }
            return;
        }
        
        // "İçerikler" ana kategorisini bul
        $mainCategory = WidgetCategory::where('slug', 'icerikler')->first();
        
        if (!$mainCategory) {
            $this->command->info('İçerikler kategorisi bulunamadı, ContentCategoriesSeeder\'ı önce çalıştırın.');
            return;
        }
        
        // "Sayfa Modülü" alt kategorisini bul
        $category = WidgetCategory::where('slug', 'sayfa-modulu')
                                  ->where('parent_id', $mainCategory->widget_category_id)
                                  ->first();
        
        if (!$category) {
            $this->command->info('Sayfa Modülü kategorisi bulunamadı, ContentCategoriesSeeder\'ı önce çalıştırın.');
            return;
        }
        
        // Son Eklenen Sayfalar Widget'ı
        if (!Widget::where('slug', 'son-eklenen-sayfalar')->exists()) {
            Widget::create([
                'widget_category_id' => $category->widget_category_id,
                'name' => 'Son Eklenen Sayfalar',
                'slug' => 'son-eklenen-sayfalar',
                'description' => 'Son eklenen sayfaları listeler',
                'type' => 'file',
                'file_path' => 'modules/page/recent/view',
                'has_items' => false,
                'is_active' => true,
                'is_core' => true,
                'settings_schema' => [
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ],
                    [
                        'name' => 'show_dates',
                        'label' => 'Tarihleri Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'limit',
                        'label' => 'Gösterilecek Sayfa Sayısı',
                        'type' => 'number',
                        'required' => false
                    ]
                ]
            ]);
            
            if ($this->command) {
                $this->command->info('Son Eklenen Sayfalar widget\'ı başarıyla oluşturuldu.');
            }
        }
        
        // Ana Sayfa İçeriği Widget'ı
        if (!Widget::where('slug', 'anasayfa-icerik')->exists()) {
            Widget::create([
                'widget_category_id' => $category->widget_category_id,
                'name' => 'Ana Sayfa İçeriği',
                'slug' => 'anasayfa-icerik',
                'description' => 'Ana sayfa olarak işaretlenmiş sayfanın içeriğini gösterir',
                'type' => 'file',
                'file_path' => 'modules/page/home/view',
                'has_items' => false,
                'is_active' => true,
                'is_core' => true,
                'settings_schema' => [
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ],
                    [
                        'name' => 'show_title',
                        'label' => 'Sayfa Başlığını Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ]
                ]
            ]);
            
            if ($this->command) {
                $this->command->info('Ana Sayfa İçeriği widget\'ı başarıyla oluşturuldu.');
            }
        }
    }
}