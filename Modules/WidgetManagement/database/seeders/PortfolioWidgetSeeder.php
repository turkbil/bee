<?php
// Modules/WidgetManagement/database/seeders/PortfolioWidgetSeeder.php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\WidgetCategory;
use Illuminate\Support\Str;

class PortfolioWidgetSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, PortfolioWidgetSeeder atlanıyor.');
            }
            return;
        }
        
        // "İçerikler" ana kategorisini bul
        $mainCategory = WidgetCategory::where('slug', 'icerikler')->first();
        
        if (!$mainCategory) {
            $this->command->info('İçerikler kategorisi bulunamadı, ContentCategoriesSeeder\'ı önce çalıştırın.');
            return;
        }
        
        // "Portfolio Modülü" alt kategorisini bul
        $category = WidgetCategory::where('slug', 'portfolio-modulu')
                                  ->where('parent_id', $mainCategory->widget_category_id)
                                  ->first();
        
        if (!$category) {
            $this->command->info('Portfolio Modülü kategorisi bulunamadı, ContentCategoriesSeeder\'ı önce çalıştırın.');
            return;
        }
        
        // Portfolio Liste Widget'ı
        if (!Widget::where('slug', 'portfolio-liste')->exists()) {
            Widget::create([
                'widget_category_id' => $category->widget_category_id,
                'name' => 'Portfolio Liste',
                'slug' => 'portfolio-liste',
                'description' => 'Projeleri listeler',
                'type' => 'file',
                'file_path' => 'modules/portfolio/list/view',
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
                        'name' => 'show_description',
                        'label' => 'Açıklamayı Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Açıklama Metni',
                        'type' => 'textarea',
                        'required' => false
                    ],
                    [
                        'name' => 'show_all_link',
                        'label' => 'Tümünü Göster Bağlantısı',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'all_link_text',
                        'label' => 'Bağlantı Metni',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'limit',
                        'label' => 'Gösterilecek Proje Sayısı',
                        'type' => 'number',
                        'required' => false
                    ],
                    [
                        'name' => 'order_direction',
                        'label' => 'Sıralama Yönü',
                        'type' => 'select',
                        'options' => [
                            'desc' => 'Yeniden Eskiye',
                            'asc' => 'Eskiden Yeniye'
                        ],
                        'required' => false
                    ],
                    [
                        'name' => 'category_id',
                        'label' => 'Kategori Filtresi',
                        'type' => 'select',
                        'options' => [],
                        'required' => false
                    ]
                ]
            ]);
            
            if ($this->command) {
                $this->command->info('Portfolio Liste widget\'ı başarıyla oluşturuldu.');
            }
        }
        
        // Portfolio Detay Widget'ı
        if (!Widget::where('slug', 'portfolio-detay')->exists()) {
            Widget::create([
                'widget_category_id' => $category->widget_category_id,
                'name' => 'Portfolio Detay',
                'slug' => 'portfolio-detay',
                'description' => 'Seçilen bir projenin detaylarını gösterir',
                'type' => 'file',
                'file_path' => 'modules/portfolio/detail/view',
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
                        'name' => 'project_id',
                        'label' => 'Proje ID',
                        'type' => 'number',
                        'required' => false
                    ],
                    [
                        'name' => 'project_slug',
                        'label' => 'Proje Slug',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'show_date',
                        'label' => 'Tarihi Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'show_category',
                        'label' => 'Kategoriyi Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'show_cover',
                        'label' => 'Kapak Resmini Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'show_gallery',
                        'label' => 'Galeriyi Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'show_related',
                        'label' => 'Benzer Projeleri Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ]
                ]
            ]);
            
            if ($this->command) {
                $this->command->info('Portfolio Detay widget\'ı başarıyla oluşturuldu.');
            }
        }
    }
}