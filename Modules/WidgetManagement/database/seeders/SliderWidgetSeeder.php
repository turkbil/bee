<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SliderWidgetSeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'slider_widget_seeder_executed';

    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, SliderWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, SliderWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }

        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('SliderWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }
        
        Log::info('SliderWidgetSeeder merkezi veritabanında çalışıyor...');

        try {
            
            // Slider bileşeni oluştur
            $this->createSliderWidget();

            Log::info('Slider bileşeni başarıyla oluşturuldu.');

            if ($this->command) {
                $this->command->info('Slider bileşeni başarıyla oluşturuldu.');
            }
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            $cacheKey = self::$runKey . '_' . Config::get('database.default');
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('SliderWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($this->command) {
                $this->command->error('SliderWidgetSeeder hatası: ' . $e->getMessage());
            }
        }
    }

    private function createSliderWidget()
    {
        // Slider kategorisini bul, yoksa oluştur
        $sliderCategory = WidgetCategory::where('slug', 'sliderlar')->first();
        
        // Eğer slider kategorisi yoksa, önce 'Media' kategorisini kontrol et
        if (!$sliderCategory) {
            $mediaCategory = WidgetCategory::where('slug', 'media')->first();
            
            if ($mediaCategory) {
                Log::info('Media kategorisi bulundu, slider bu kategori altına eklenecek.');
                
                // Slider alt kategorisini oluştur
                $sliderCategory = WidgetCategory::create([
                    'title' => 'Sliderlar',
                    'slug' => 'sliderlar',
                    'description' => 'Slider ve carousel bileşenleri',
                    'icon' => 'fa-images',
                    'order' => 10,
                    'is_active' => true,
                    'parent_id' => $mediaCategory->widget_category_id,
                    'has_subcategories' => false
                ]);
                
                Log::info("Slider alt kategorisi oluşturuldu: Sliderlar (slug: sliderlar)");
            } else {
                // Media kategorisi yoksa, ana kategori olarak oluştur
                Log::warning('Slider kategorisi bulunamadı, oluşturuluyor...');
                
                try {
                    $sliderCategory = WidgetCategory::create([
                        'title' => 'Sliderlar',
                        'slug' => 'sliderlar',
                        'description' => 'Slider ve carousel bileşenleri',
                        'icon' => 'fa-images',
                        'order' => 10,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => false
                    ]);
                    
                    Log::info("Slider kategorisi oluşturuldu: Sliderlar (slug: sliderlar)");
                } catch (\Exception $e) {
                    Log::error("Slider kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return;
                }
                
                if (!$sliderCategory) {
                    Log::error("Slider kategorisi oluşturulamadı.");
                    return;
                }
            }
        }
        
        // Slider widget'ı zaten var mı kontrolü
        $existingWidget = Widget::where('slug', 'dinamik-slider')->first();
        
        if (!$existingWidget) {
            // Slider widget'ı oluştur
            $widget = Widget::create([
                'widget_category_id' => $sliderCategory->widget_category_id,
                'name' => 'Dinamik Slider',
                'slug' => 'dinamik-slider',
                'description' => 'Dinamik slaytlar ekleyebileceğiniz carousel slider',
                'type' => 'dynamic',
                'content_html' => '
                <div id="slider-{{unique_id}}" class="carousel slide" data-bs-ride="carousel">
                    {{#if show_indicators}}
                    <div class="carousel-indicators">
                        {{#each items}}
                        <button type="button" data-bs-target="#slider-{{../unique_id}}" data-bs-slide-to="{{@index}}" {{#if @first}}class="active"{{/if}}></button>
                        {{/each}}
                    </div>
                    {{/if}}
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
                    {{#if show_controls}}
                    <button class="carousel-control-prev" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sonraki</span>
                    </button>
                    {{/if}}
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
                'is_active' => true,
                'is_core' => true,
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
                        'label' => 'Görsel',
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
                        'type' => 'text',
                        'required' => false
                    ]
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
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ],
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
                ]
            ]);
            
            Log::info('Dinamik Slider bileşeni oluşturuldu.');
            
            // Demo olarak bir tenant widget ve öğeler oluştur
            $this->createDemoTenantSlider($widget);
        } else {
            Log::info('Dinamik Slider bileşeni zaten mevcut, atlanıyor...');
        }
    }
    
    private function createDemoTenantSlider($widget)
    {
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
        
        // Slider için 2 adet örnek item oluştur
        $items = [
            [
                'title' => 'Web Sitesi Çözümleri',
                'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                'image' => 'https://via.placeholder.com/1200x600',
                'button_text' => 'Detaylı Bilgi',
                'button_url' => '/web-cozumleri'
            ],
            [
                'title' => 'E-Ticaret Platformları',
                'description' => 'Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın',
                'image' => 'https://via.placeholder.com/1200x600',
                'button_text' => 'Hemen Başlayın',
                'button_url' => '/e-ticaret'
            ]
        ];
        
        // Widget item'larını oluştur
        foreach ($items as $index => $item) {
            WidgetItem::create([
                'tenant_widget_id' => $tenantWidget->id,
                'content' => $item,
                'order' => $index + 1
            ]);
        }
        
        Log::info('Demo tenant slider oluşturuldu.');
    }
}