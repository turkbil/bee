<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;

class SliderWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'slider')->exists()) {
            // Slider Widget
            $widget = Widget::create([
                'name' => 'Slider',
                'slug' => 'slider',
                'description' => 'Dinamik slaytlar ekleyebileceğiniz carousel slider',
                'type' => 'dynamic',
                'content_html' => '
                <div id="slider-{{id}}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        {{#each items}}
                        <button type="button" data-bs-target="#slider-{{id}}" data-bs-slide-to="{{@index}}" {{#if @first}}class="active"{{/if}}></button>
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
                    <button class="carousel-control-prev" type="button" data-bs-target="#slider-{{id}}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#slider-{{id}}" data-bs-slide="next">
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
                    background-color: rgba(0,0,0,0.5);
                    padding: 20px;
                    border-radius: 5px;
                }
                ',
                'content_js' => '
                document.addEventListener("DOMContentLoaded", function() {
                    new bootstrap.Carousel(document.getElementById("slider-{{id}}"), {
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
                        'required' => true
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Açıklama',
                        'type' => 'textarea',
                        'required' => false
                    ],
                    [
                        'name' => 'image',
                        'label' => 'Resim',
                        'type' => 'image',
                        'required' => true
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
                        'type' => 'url',
                        'required' => false
                    ]
                ],
                'settings_schema' => [
                    [
                        'name' => 'id',
                        'label' => 'Slider ID',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'height',
                        'label' => 'Yükseklik (px)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'interval',
                        'label' => 'Slayt Geçiş Süresi (ms)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'show_indicators',
                        'label' => 'Göstergeleri Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'show_controls',
                        'label' => 'Kontrolleri Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ]
                ],
                'is_active' => true,
                'is_core' => true
            ]);

            // Örnek TenantWidget ve ayarları
            $tenantWidget = TenantWidget::create([
                'widget_id' => $widget->id,
                'settings' => [
                    'id' => 'ana-slider',
                    'height' => 500,
                    'interval' => 5000,
                    'show_indicators' => true,
                    'show_controls' => true
                ],
                'position' => 'top',
                'page_id' => null,
                'module' => null
            ]);

            // Slider item'ları
            $items = [
                [
                    'title' => 'Dijital Dönüşüm Çözümleri',
                    'description' => 'Kurumsal dijital altyapınızı güçlendirin',
                    'image' => asset('storage/images/slider-1.jpg'),
                    'button_text' => 'Detayları İncele',
                    'button_url' => '/dijital-donusum'
                ],
                [
                    'title' => 'Yapay Zeka Danışmanlığı',
                    'description' => 'Yapay zeka teknolojileriyle iş süreçlerinizi optimize edin',
                    'image' => asset('storage/images/slider-2.jpg'),
                    'button_text' => 'Çözümleri Keşfet',
                    'button_url' => '/yapay-zeka'
                ],
                [
                    'title' => 'Bulut Bilişim Hizmetleri',
                    'description' => 'Esnek ve güvenli bulut çözümleri',
                    'image' => asset('storage/images/slider-3.jpg'),
                    'button_text' => 'Hemen Başlayın',
                    'button_url' => '/bulut-hizmetler'
                ]
            ];

            // Item'ları oluştur
            foreach ($items as $index => $item) {
                WidgetItem::create([
                    'tenant_widget_id' => $tenantWidget->id,
                    'content' => $item,
                    'order' => $index + 1
                ]);
            }
        }
    }
}