<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SliderWidgetSeeder extends Seeder
{
    private static $runKey = 'slider_widget_seeder_executed';
    
    public function run()
    {
        $cacheKey = self::$runKey . '_' . config('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('SliderWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }

        if (function_exists('tenant') && tenant()) {
            try {
                $this->createTenantSlider();
                
                $tenantId = tenant('id');
                Cache::put(self::$runKey . '_tenant_' . $tenantId, true, 600);
                return;
            } catch (\Exception $e) {
                Log::error('Tenant SliderWidgetSeeder hatası: ' . $e->getMessage());
                return;
            }
        }

        try {
            $moduleCategory = WidgetCategory::where('slug', 'moduller')->orWhere('slug', 'moduller')->first();
            
            if (!$moduleCategory) {
                Log::info('Moduller kategorisi bulunamadı, oluşturuluyor...');
                
                try {
                    $moduleCategory = new WidgetCategory([
                        'title' => 'Moduller',
                        'slug' => 'moduller',
                        'description' => 'Sistem modüllerine ait bileşenler',
                        'icon' => 'fa-cubes',
                        'order' => 1,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => true
                    ]);
                    
                    $moduleCategory->save();
                    
                    Log::info("Moduller kategorisi oluşturuldu (ID: {$moduleCategory->widget_category_id})");
                } catch (\Exception $e) {
                    Log::error("Moduller kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                }
            }
            
            $this->cleanupExtraSliders();
            
            $widget = $this->createSliderWidget();

            if ($widget) {
                $this->createDemoTenantSlider($widget);
                $this->createSliderForAllTenants($widget);
            }

            Log::info('Slider bileşeni başarıyla oluşturuldu.');
            
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('SliderWidgetSeeder central hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    private function cleanupExtraSliders()
    {
        $widget = Widget::where('slug', 'swiper-slider')->first();
        
        if (!$widget) {
            return;
        }
        
        $tenantWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($tenantWidgets->count() <= 1) {
            return;
        }
        
        $firstWidgetId = $tenantWidgets->first()->id;
        
        foreach ($tenantWidgets as $tenantWidget) {
            if ($tenantWidget->id != $firstWidgetId) {
                WidgetItem::where('tenant_widget_id', $tenantWidget->id)->delete();
                $tenantWidget->delete();
                Log::info("Fazla slider widget silindi: ID {$tenantWidget->id}");
            }
        }
        
        Log::info("Central veritabanında fazla slider widget'ları temizlendi");
    }

    private function createTenantSlider()
    {
        $tenantId = tenant('id');
        $tenantCacheKey = self::$runKey . '_tenant_' . $tenantId;
        
        if (Cache::has($tenantCacheKey)) {
            Log::info('Tenant içinde slider widget zaten oluşturulmuş, atlanıyor...');
            return;
        }
        
        $centralWidget = null;
        
        try {
            $connection = config('database.default');
            config(['database.default' => config('tenancy.database.central_connection')]);
            
            $centralWidget = Widget::where('slug', 'swiper-slider')->first();
            
            config(['database.default' => $connection]);
        } catch (\Exception $e) {
            Log::error('Merkezi widget erişim hatası: ' . $e->getMessage());
            return;
        }
        
        if (!$centralWidget) {
            Log::error('Merkezi slider widget bulunamadı');
            return;
        }
        
        $existingWidgets = TenantWidget::where('widget_id', $centralWidget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            $firstWidgetId = $existingWidgets->first()->id;
            
            foreach ($existingWidgets as $existingWidget) {
                if ($existingWidget->id != $firstWidgetId) {
                    WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                    $existingWidget->delete();
                    Log::info("Tenant'ta fazla slider widget silindi: ID {$existingWidget->id}");
                }
            }
            
            if ($existingWidgets->count() >= 1) {
                Log::info('Tenant içinde slider widget zaten var, atlanıyor...');
                return;
            }
        }
        
        $tenantWidget = TenantWidget::create([
            'widget_id' => $centralWidget->id,
            'settings' => [
                'widget_unique_id' => (string) Str::uuid(),
                'widget_title' => 'Ana Sayfa Slider',
                'widget_height' => 500,
                'widget_autoplay' => true,
                'widget_autoplay_delay' => 5000
            ],
            'display_title' => 'Ana Sayfa Slider',
            'order' => 0,
            'is_active' => true
        ]);
        
        $items = [
            [
                'title' => 'Web Sitesi Çözümleri',
                'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                'image' => 'https://placehold.co/1200x600',
                'button_text' => 'Detaylı Bilgi',
                'button_url' => '/web-cozumleri',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'E-Ticaret Platformları',
                'description' => 'Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın',
                'image' => 'https://placehold.co/1200x600',
                'button_text' => 'Hemen Başlayın',
                'button_url' => '/e-ticaret',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ]
        ];
        
        foreach ($items as $index => $item) {
            WidgetItem::create([
                'tenant_widget_id' => $tenantWidget->id,
                'content' => $item,
                'order' => $index + 1
            ]);
        }
        
        Log::info('Tenant içinde slider widget başarıyla oluşturuldu. Tenant ID: ' . $tenantId);
    }

    private function createSliderWidget()
    {
        $sliderCategory = WidgetCategory::where('slug', 'sliderlar')->first();
        
        if (!$sliderCategory) {
            $mediaCategory = WidgetCategory::where('slug', 'media')->first();
            
            if ($mediaCategory) {
                Log::info('Media kategorisi bulundu, slider bu kategori altına eklenecek.');
                
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
                    return null;
                }
                
                if (!$sliderCategory) {
                    Log::error("Slider kategorisi oluşturulamadı.");
                    return null;
                }
            }
        }
        
        $existingWidget = Widget::where('slug', 'swiper-slider')->first();
        
        if (!$existingWidget) {
            $widget = Widget::create([
                'widget_category_id' => $sliderCategory->widget_category_id,
                'name' => 'Swiper Slider',
                'slug' => 'swiper-slider',
                'description' => 'Dinamik slaytlar ekleyebileceğiniz Swiper slider',
                'type' => 'dynamic',
                'content_html' => '
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

                <div class="swiper mySwiper-{{widget_unique_id}}">
                    <div class="swiper-wrapper">
                        {{#each items}}
                        <div class="swiper-slide">
                            <img src="{{image}}" alt="{{title}}">
                            <div class="swiper-caption">
                                <h3>{{title}}</h3>
                                <p>{{description}}</p>
                                {{#if button_text}}
                                <a href="{{button_url}}" class="swiper-button">{{button_text}}</a>
                                {{/if}}
                            </div>
                        </div>
                        {{/each}}
                    </div>
                    
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
                ',
                'content_css' => '
                .swiper {
                    width: 100%;
                    height: {{widget_height}}px;
                    margin-bottom: 30px;
                }

                .swiper-slide {
                    text-align: center;
                    font-size: 18px;
                    background: #fff;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: relative;
                }

                .swiper-slide img {
                    display: block;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .swiper-caption {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background-color: rgba(0,0,0,0.5);
                    color: #ffffff;
                    padding: 20px;
                    text-align: center;
                }

                .swiper-button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 10px;
                }

                .swiper-button:hover {
                    background-color: #0056b3;
                }

                .swiper-button-next, 
                .swiper-button-prev {
                    color: #fff !important;
                    background-color: rgba(0,0,0,0.5);
                    padding: 20px;
                    border-radius: 50%;
                    width: 30px !important;
                    height: 30px !important;
                }

                .swiper-pagination-bullet {
                    width: 12px;
                    height: 12px;
                    background: #fff;
                    opacity: 0.7;
                }

                .swiper-pagination-bullet-active {
                    opacity: 1;
                    background: #007bff;
                }
                ',
                'content_js' => '
                var mySwiper = new Swiper(".mySwiper-{{widget_unique_id}}", {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: {{widget_autoplay_delay}},
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    effect: "slide",
                    speed: 800,
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
                        'required' => true,
                        'system' => true,
                        'protected' => true
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
                        'label' => 'Buton Bağlantısı',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'is_active',
                        'label' => 'Durum',
                        'type' => 'switch',
                        'required' => false,
                        'system' => true,
                        'default' => true,
                        'protected' => true,
                        'properties' => [
                            'active_label' => 'Aktif',
                            'inactive_label' => 'Aktif Değil'
                        ]
                    ]
                ],
                'settings_schema' => [
                    [
                        'name' => 'widget_title',
                        'label' => 'Widget Başlığı',
                        'type' => 'text',
                        'required' => true,
                        'system' => true,
                        'protected' => true,
                        'properties' => [
                            'default_value' => 'Swiper Slider',
                            'width' => 12,
                            'placeholder' => 'Widget başlığını giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_height',
                        'label' => 'Yükseklik (px)',
                        'type' => 'number',
                        'required' => true,
                        'default' => 500,
                        'properties' => [
                            'default_value' => 500,
                            'width' => 6
                        ]
                    ],
                    [
                        'name' => 'widget_autoplay_delay',
                        'label' => 'Geçiş Hızı (ms)',
                        'type' => 'number',
                        'required' => false,
                        'default' => 5000,
                        'properties' => [
                            'default_value' => 5000,
                            'width' => 6
                        ]
                    ],
                    [
                        'name' => 'widget_autoplay',
                        'label' => 'Otomatik Oynat',
                        'type' => 'checkbox',
                        'required' => false,
                        'default' => true,
                        'properties' => [
                            'default_value' => true,
                            'width' => 12
                        ]
                    ],
                    [
                        'name' => 'widget_unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true,
                        'protected' => true,
                        'properties' => [
                            'width' => 12
                        ]
                    ]
                ]
            ]);
            
            return $widget;
        } else {
            Log::info('Swiper Slider bileşeni zaten mevcut, atlanıyor...');
            return $existingWidget;
        }
    }
    
    private function createDemoTenantSlider($widget)
    {
        $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            Log::info('Central veritabanında Demo Slider widget zaten var, atlanıyor...');
            return;
        }
        
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widget->id,
            'settings' => [
                'widget_unique_id' => (string) Str::uuid(),
                'widget_title' => 'Ana Sayfa Slider',
                'widget_height' => 500,
                'widget_autoplay' => true,
                'widget_autoplay_delay' => 5000
            ],
            'display_title' => 'Ana Sayfa Slider',
            'order' => 0,
            'is_active' => true
        ]);
        
        $items = [
            [
                'title' => 'Web Sitesi Çözümleri',
                'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                'image' => 'https://placehold.co/1200x600',
                'button_text' => 'Detaylı Bilgi',
                'button_url' => '/web-cozumleri',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ],
            [
                'title' => 'E-Ticaret Platformları',
                'description' => 'Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın',
                'image' => 'https://placehold.co/1200x600',
                'button_text' => 'Hemen Başlayın',
                'button_url' => '/e-ticaret',
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ]
        ];
        
        foreach ($items as $index => $item) {
            WidgetItem::create([
                'tenant_widget_id' => $tenantWidget->id,
                'content' => $item,
                'order' => $index + 1
            ]);
        }
        
        Log::info('Central veritabanında demo tenant slider oluşturuldu.');
    }
    
    private function createSliderForAllTenants($widget)
    {
        $tenants = Tenant::where('central', false)->get();
        
        if ($tenants->isEmpty()) {
            Log::info('Tenant bulunamadı, tenant slider oluşturulamıyor.');
            return;
        }
        
        foreach ($tenants as $tenant) {
            $tenantCacheKey = self::$runKey . '_tenant_' . $tenant->id;
            
            if (Cache::has($tenantCacheKey)) {
                Log::info("Tenant {$tenant->id} için slider zaten oluşturulmuş, atlanıyor...");
                continue;
            }
            
            try {
                $tenant->run(function () use ($widget, $tenant) {
                    
                    $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
                    
                    if ($existingWidgets->count() > 1) {
                        $firstWidgetId = $existingWidgets->first()->id;
                        
                        foreach ($existingWidgets as $existingWidget) {
                            if ($existingWidget->id != $firstWidgetId) {
                                WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                                $existingWidget->delete();
                                Log::info("Tenant {$tenant->id} için fazla slider widget silindi: ID {$existingWidget->id}");
                            }
                        }
                    }
                    
                    if ($existingWidgets->count() >= 1) {
                        Log::info("Tenant {$tenant->id} için slider widget zaten var, atlanıyor...");
                        return;
                    }
                    
                    $tenantWidget = TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'widget_unique_id' => (string) Str::uuid(),
                            'widget_title' => $tenant->title . ' Slider',
                            'widget_height' => 500,
                            'widget_autoplay' => true,
                            'widget_autoplay_delay' => 5000
                        ],
                        'display_title' => $tenant->title . ' Ana Slider',
                        'order' => 0,
                        'is_active' => true
                    ]);
                    
                    $items = [
                        [
                            'title' => $tenant->title . ' Web Sitesi',
                            'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                            'image' => 'https://placehold.co/1200x600',
                            'button_text' => 'Detaylı Bilgi',
                            'button_url' => '/web-cozumleri',
                            'is_active' => true,
                            'unique_id' => (string) Str::uuid()
                        ],
                        [
                            'title' => $tenant->title . ' E-Ticaret',
                            'description' => 'Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın',
                            'image' => 'https://placehold.co/1200x600',
                            'button_text' => 'Hemen Başlayın',
                            'button_url' => '/e-ticaret',
                            'is_active' => true,
                            'unique_id' => (string) Str::uuid()
                        ]
                    ];
                    
                    foreach ($items as $index => $item) {
                        WidgetItem::create([
                            'tenant_widget_id' => $tenantWidget->id,
                            'content' => $item,
                            'order' => $index + 1
                        ]);
                    }
                    
                    Log::info("Tenant {$tenant->id} için slider başarıyla oluşturuldu.");
                    
                    Cache::put($tenantCacheKey, true, 600);
                });
            } catch (\Exception $e) {
                Log::error("Tenant {$tenant->id} için slider oluşturma hatası: " . $e->getMessage());
            }
        }
    }
}