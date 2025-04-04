<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Str;

class AdvancedFeaturesWidgetSeeder extends Seeder
{
    public function run()
    {
        // Demo Widget şablonu oluştur veya güncelle
        $widget = Widget::updateOrCreate(
            ['slug' => 'advanced-features-demo'],
            [
            'name' => 'Gelişmiş Özellikler Demo',
            'slug' => 'advanced-features-demo',
            'description' => 'Eklenen tüm yeni özellikleri (renk, resim, dosya vb.) gösteren demo widget',
            'type' => 'dynamic',
            'content_html' => '
                <div class="advanced-demo-widget" style="background-color: {{background_color}}; color: {{text_color}}; padding: 20px; border-radius: 8px; margin: 0 auto; max-width: 900px;">
                    <div class="widget-header" style="border-bottom: 1px solid rgba(0,0,0,0.1); margin-bottom: 20px; padding-bottom: 10px;">
                        <h2 style="margin-bottom: 5px;">{{title}}</h2>
                        {{#if subtitle}}
                            <p style="margin-top: 0; opacity: 0.8;">{{subtitle}}</p>
                        {{/if}}
                    </div>
                    
                    <div class="widget-content">
                        <div class="items-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                            {{#each items}}
                                {{#if is_active}}
                                <div class="item" style="background-color: {{background_color}}; border-radius: 5px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                    <h3 style="color: {{title_color}}; margin-top: 0;">{{title}}</h3>
                                    {{#if image}}
                                        <div style="margin: 10px 0;">
                                            <img src="{{image}}" alt="{{title}}" style="max-width: 100%; border-radius: 5px;">
                                        </div>
                                    {{/if}}
                                    
                                    {{#if description}}
                                        <p>{{description}}</p>
                                    {{/if}}
                                    
                                    {{#if url}}
                                        <div style="margin-top: 10px;">
                                            <a href="{{url}}" style="display: inline-block; padding: 8px 15px; background-color: {{button_color}}; color: white; text-decoration: none; border-radius: 4px;">
                                                {{button_text}}
                                            </a>
                                        </div>
                                    {{/if}}
                                    
                                    {{#if custom_date}}
                                        <div style="margin-top: 10px; font-size: 0.9em; opacity: 0.7;">
                                            Tarih: {{custom_date}}
                                        </div>
                                    {{/if}}
                                </div>
                                {{/if}}
                            {{/each}}
                        </div>
                    </div>
                </div>
            ',
            'content_css' => '
                .advanced-demo-widget {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                }
                .item:hover {
                    transform: translateY(-3px);
                    transition: transform 0.3s ease;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                }
            ',
            'has_items' => true,
            'item_schema' => [
                [
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ],
                [
                    'name' => 'description',
                    'label' => 'Açıklama',
                    'type' => 'textarea',
                    'required' => false
                ],
                [
                    'name' => 'image',
                    'label' => 'Görsel',
                    'type' => 'image',
                    'required' => false
                ],
                [
                    'name' => 'background_color',
                    'label' => 'Arkaplan Rengi',
                    'type' => 'color',
                    'required' => false
                ],
                [
                    'name' => 'title_color',
                    'label' => 'Başlık Rengi',
                    'type' => 'color',
                    'required' => false
                ],
                [
                    'name' => 'button_text',
                    'label' => 'Buton Metni',
                    'type' => 'text',
                    'required' => false
                ],
                [
                    'name' => 'button_color',
                    'label' => 'Buton Rengi',
                    'type' => 'color',
                    'required' => false
                ],
                [
                    'name' => 'url',
                    'label' => 'Bağlantı URL',
                    'type' => 'url',
                    'required' => false
                ],
                [
                    'name' => 'custom_date',
                    'label' => 'Tarih',
                    'type' => 'date',
                    'required' => false
                ],
                [
                    'name' => 'is_active',
                    'label' => 'Aktif',
                    'type' => 'checkbox',
                    'required' => false,
                    'system' => true
                ],
            ],
            'settings_schema' => [
                [
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ],
                [
                    'name' => 'subtitle',
                    'label' => 'Alt Başlık',
                    'type' => 'text',
                    'required' => false
                ],
                [
                    'name' => 'background_color',
                    'label' => 'Arkaplan Rengi',
                    'type' => 'color',
                    'required' => false
                ],
                [
                    'name' => 'text_color',
                    'label' => 'Metin Rengi',
                    'type' => 'color',
                    'required' => false
                ],
                [
                    'name' => 'show_border',
                    'label' => 'Kenarlık Göster',
                    'type' => 'checkbox',
                    'required' => false
                ],
                [
                    'name' => 'item_count',
                    'label' => 'Gösterilecek Öğe Sayısı',
                    'type' => 'number',
                    'required' => false
                ],
                [
                    'name' => 'unique_id',
                    'label' => 'Benzersiz ID',
                    'type' => 'text',
                    'required' => false,
                    'system' => true
                ],
            ],
            'is_active' => true,
            'is_core' => false
            ]
        );

        // Bir örnek tenant widget oluştur veya güncelle
        $tenantWidget = TenantWidget::updateOrCreate(
            [
                'widget_id' => $widget->id,
                'position' => 'center'
            ],
            [
            'widget_id' => $widget->id,
            'position' => 'center',
            'order' => 1,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Gelişmiş Özellikler Demosu',
                'subtitle' => 'Tüm yeni alan tiplerini gösteren interaktif bir örnek',
                'background_color' => '#f8f9fa',
                'text_color' => '#343a40',
                'show_border' => true,
                'item_count' => 6
            ]
        ]);

        // Örnek içerik öğeleri oluştur
        $items = [
            [
                'title' => 'Renk Seçimi Örneği',
                'description' => 'Bu öğede özel renkler ve renk alanları kullanılmıştır.',
                'image' => asset('storage/widgets/color-palette.jpg'),
                'background_color' => '#e3f2fd',
                'title_color' => '#0d47a1',
                'button_text' => 'Renk Örnekleri',
                'button_color' => '#1976d2',
                'url' => 'https://example.com/colors',
                'custom_date' => '2025-05-15',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'Resim/Dosya Yükleme',
                'description' => 'Resim ve dosya yükleme özelliklerini test etmek için kullanılır.',
                'image' => asset('storage/widgets/upload-image.jpg'),
                'background_color' => '#f3e5f5',
                'title_color' => '#6a1b9a',
                'button_text' => 'Galeriye Git',
                'button_color' => '#8e24aa',
                'url' => 'https://example.com/gallery',
                'custom_date' => '2025-05-18',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'Tarih ve Saat Alanları',
                'description' => 'Tarih ve saat tipinde alanların nasıl çalıştığını gösterir.',
                'image' => asset('storage/widgets/calendar.jpg'),
                'background_color' => '#e8f5e9',
                'title_color' => '#2e7d32',
                'button_text' => 'Takvim',
                'button_color' => '#43a047',
                'url' => 'https://example.com/calendar',
                'custom_date' => '2025-05-20',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'URL ve Bağlantı Alanları',
                'description' => 'URL alanları ve harici bağlantılar için kullanılır.',
                'image' => asset('storage/widgets/link.jpg'),
                'background_color' => '#fff3e0',
                'title_color' => '#e65100',
                'button_text' => 'Bağlantıya Git',
                'button_color' => '#fb8c00',
                'url' => 'https://example.com/links',
                'custom_date' => '2025-05-25',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'Onay Kutuları',
                'description' => 'Onay kutusu tipindeki alanlar için örnek.',
                'image' => asset('storage/widgets/checkbox.jpg'),
                'background_color' => '#ffebee',
                'title_color' => '#b71c1c',
                'button_text' => 'Tercihleri Ayarla',
                'button_color' => '#e53935',
                'url' => 'https://example.com/preferences',
                'custom_date' => '2025-05-30',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'Pasif Öğe Örneği',
                'description' => 'Bu öğe pasif durumda, widget içinde görünmeyecek.',
                'background_color' => '#f5f5f5',
                'title_color' => '#757575',
                'is_active' => false,
                'unique_id' => (string) Str::uuid()
            ]
        ];

        // Öğeleri veritabanına ekle
        foreach ($items as $index => $item) {
            WidgetItem::create([
                'tenant_widget_id' => $tenantWidget->id,
                'content' => $item,
                'order' => $index + 1
            ]);
        }

        // Çoklu resim desteği için başka bir widget şablonu oluştur veya güncelle
        $multipleImagesWidget = Widget::updateOrCreate(
            ['slug' => 'multiple-images-gallery'],
            [
            'name' => 'Çoklu Resim Galerisi',
            'slug' => 'multiple-images-gallery',
            'description' => 'Çoklu resim yükleme özelliğini gösteren bir galeri widget\'ı',
            'type' => 'dynamic',
            'content_html' => '
                <div class="gallery-widget" style="padding: 20px;">
                    <h2 style="text-align: center; margin-bottom: 20px;">{{title}}</h2>
                    <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                        {{#each items}}
                            {{#if is_active}}
                            <div class="gallery-item" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                {{#if image}}
                                    <img src="{{image}}" alt="{{title}}" style="width: 100%; height: 200px; object-fit: cover;">
                                {{/if}}
                                <div style="padding: 10px;">
                                    <h3 style="margin: 0 0 5px 0;">{{title}}</h3>
                                    {{#if description}}
                                        <p style="margin: 0; font-size: 0.9em; opacity: 0.8;">{{description}}</p>
                                    {{/if}}
                                </div>
                            </div>
                            {{/if}}
                        {{/each}}
                    </div>
                </div>
            ',
            'has_items' => true,
            'item_schema' => [
                [
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ],
                [
                    'name' => 'description',
                    'label' => 'Açıklama',
                    'type' => 'textarea',
                    'required' => false
                ],
                [
                    'name' => 'image',
                    'label' => 'Ana Görsel',
                    'type' => 'image',
                    'required' => false
                ],
                [
                    'name' => 'gallery_images',
                    'label' => 'Galeri Görselleri',
                    'type' => 'image_multiple',
                    'required' => false
                ],
                [
                    'name' => 'is_active',
                    'label' => 'Aktif',
                    'type' => 'checkbox',
                    'required' => false,
                    'system' => true
                ],
            ],
            'settings_schema' => [
                [
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ],
                [
                    'name' => 'layout_type',
                    'label' => 'Görünüm Tipi',
                    'type' => 'select',
                    'required' => false,
                    'options' => [
                        'grid' => 'Izgara',
                        'masonry' => 'Masonry',
                        'carousel' => 'Döngü'
                    ]
                ],
                [
                    'name' => 'columns',
                    'label' => 'Sütun Sayısı',
                    'type' => 'number',
                    'required' => false
                ],
                [
                    'name' => 'unique_id',
                    'label' => 'Benzersiz ID',
                    'type' => 'text',
                    'required' => false,
                    'system' => true
                ],
            ],
            'is_active' => true,
            'is_core' => false
            ]
        );

        // Galeri için örnek tenant widget oluştur veya güncelle
        $galleryTenantWidget = TenantWidget::updateOrCreate(
            [
                'widget_id' => $multipleImagesWidget->id,
                'position' => 'center'
            ],
            [
            'widget_id' => $multipleImagesWidget->id,
            'position' => 'center',
            'order' => 2,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Çoklu Resim Galerisi Örneği',
                'layout_type' => 'grid',
                'columns' => 3
            ]
        ]);

        // Bu seeder tamamlandığında bir bilgilendirme göster
        $this->command->info('AdvancedFeaturesWidgetSeeder başarıyla tamamlandı!');
        $this->command->info('Oluşturulan demo widget\'lar:');
        $this->command->info('1. Gelişmiş Özellikler Demo (ID: ' . $widget->id . ')');
        $this->command->info('2. Çoklu Resim Galerisi (ID: ' . $multipleImagesWidget->id . ')');
        $this->command->info('-----');
        $this->command->info('Test etmek için tenant widget\'lar:');
        $this->command->info('1. Gelişmiş Özellikler Demo (ID: ' . $tenantWidget->id . ')');
        $this->command->info('2. Çoklu Resim Galerisi (ID: ' . $galleryTenantWidget->id . ')');
    }
}