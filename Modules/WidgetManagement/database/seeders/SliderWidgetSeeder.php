<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SliderWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'slider')->exists()) {
            // Slider Widget'ı oluştur (widgets tablosuna ekle)
            $widget = Widget::create([
                'name' => 'Slider',
                'slug' => 'slider',
                'description' => 'Dinamik slaytlar ekleyebileceğiniz carousel slider',
                'type' => 'dynamic',
                'content_html' => '
                <div id="slider-{{unique_id}}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        {{#each items}}
                        <button type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide-to="{{@index}}" {{#if @first}}class="active"{{/if}}></button>
                        {{/each}}
                    </div>
                    <div class="carousel-inner">
                        {{#each items}}
                        <div class="carousel-item {{#if @first}}active{{/if}}">
                            <img src="{{image}}" class="d-block w-100" alt="{{title}}">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>{{title}}</h5>
                                <p>{{description}}</p>
                                {{#if button_text}}
                                <a href="{{button_url}}" class="btn btn-primary">{{button_text}}</a>
                                {{/if}}
                            </div>
                        </div>
                        {{/each}}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sonraki</span>
                    </button>
                </div>
                ',
                'content_css' => '
                .carousel-item {
                    height: {{height}}px;
                }
                .carousel-item img {
                    object-fit: cover;
                    height: 100%;
                    width: 100%;
                }
                .carousel-caption {
                    background-color: {{caption_bg_color}};
                    padding: 20px;
                    border-radius: 5px;
                    color: {{caption_text_color}};
                }
                ',
                'content_js' => '
                document.addEventListener("DOMContentLoaded", function() {
                    new bootstrap.Carousel(document.getElementById("slider-{{unique_id}}"), {
                        interval: {{interval}},
                        wrap: true
                    });
                });
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
                        'required' => true,
                        'system' => true
                    ],
                    [
                        'name' => 'button_text',
                        'label' => 'Buton Metni',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'button_url',
                        'label' => 'Buton URL',
                        'type' => 'text',
                        'required' => false
                    ]
                ],
                'settings_schema' => [
                    [
                        'name' => 'height',
                        'label' => 'Yükseklik (px)',
                        'type' => 'number',
                        'required' => true,
                        'default' => 500
                    ],
                    [
                        'name' => 'interval',
                        'label' => 'Slayt Geçiş Süresi (ms)',
                        'type' => 'number',
                        'required' => true,
                        'default' => 5000
                    ],
                    [
                        'name' => 'show_indicators',
                        'label' => 'Göstergeleri Göster',
                        'type' => 'checkbox',
                        'required' => false,
                        'default' => true
                    ],
                    [
                        'name' => 'show_controls',
                        'label' => 'Kontrolleri Göster',
                        'type' => 'checkbox',
                        'required' => false,
                        'default' => true
                    ],
                    [
                        'name' => 'caption_bg_color',
                        'label' => 'Başlık Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => 'rgba(0,0,0,0.5)'
                    ],
                    [
                        'name' => 'caption_text_color',
                        'label' => 'Başlık Metin Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#ffffff'
                    ]
                ],
                'is_active' => true,
                'is_core' => true
            ]);

            // Tenant için widget oluştur (tenant_widgets tablosuna ekle)
            $tenantWidget = TenantWidget::create([
                'widget_id' => $widget->id,
                'settings' => [
                    'unique_id' => (string) Str::uuid(),
                    'title' => 'Ana Sayfa Slider',
                    'height' => 500,
                    'interval' => 5000,
                    'show_indicators' => true,
                    'show_controls' => true,
                    'caption_bg_color' => 'rgba(0,0,0,0.5)',
                    'caption_text_color' => '#ffffff'
                ],
                'order' => 0,
                'is_active' => true
            ]);

            // Slider için 2 adet örnek item oluştur (widget_items tablosuna ekle)
            $items = [
                [
                    'title' => 'Web Sitesi Çözümleri',
                    'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                    'image' => asset('storage/images/widgets/slider/slider-1.jpg'),
                    'button_text' => 'Detaylı Bilgi',
                    'button_url' => '/web-cozumleri',
                    'unique_id' => (string) Str::uuid()
                ],
                [
                    'title' => 'E-Ticaret Platformları',
                    'description' => 'Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın',
                    'image' => asset('storage/images/widgets/slider/slider-2.jpg'),
                    'button_text' => 'Hemen Başlayın',
                    'button_url' => '/e-ticaret',
                    'unique_id' => (string) Str::uuid()
                ]
            ];

            // Widget item'larını oluştur - sadece 2 item
            foreach ($items as $index => $item) {
                WidgetItem::create([
                    'tenant_widget_id' => $tenantWidget->id,
                    'content' => $item,
                    'order' => $index + 1,
                    'is_active' => true
                ]);
            }
        }
    }
}