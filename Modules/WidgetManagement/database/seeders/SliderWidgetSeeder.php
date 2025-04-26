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
    // Çalıştırma izleme anahtarı
    private static $runKey = 'slider_widget_seeder_executed';
    
    public function run()
    {

        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . config('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('SliderWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }

        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            try {
                $this->createTenantSlider();
                
                // Bu tenant için çalıştırıldığını işaretle
                $tenantId = tenant('id');
                Cache::put(self::$runKey . '_tenant_' . $tenantId, true, 600);
                return;
            } catch (\Exception $e) {
                Log::error('Tenant SliderWidgetSeeder hatası: ' . $e->getMessage());
                return;
            }
        }

        // Central işlemleri
        try {
            // Moduller kategorisini kontrol et
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
            } else {
            }
            
            // Önce central veritabanındaki fazla slider kayıtlarını temizleyelim
            $this->cleanupExtraSliders();
            
            // Slider bileşeni oluştur
            $widget = $this->createSliderWidget();

            if ($widget) {
                // Central veritabanı için sadece bir tane slider oluşturalım
                $this->createDemoTenantSlider($widget);
                
                // Tüm mevcut tenant'lar için slider'ları oluştur
                $this->createSliderForAllTenants($widget);
            }

            Log::info('Slider bileşeni başarıyla oluşturuldu.');
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('SliderWidgetSeeder central hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    private function cleanupExtraSliders()
    {
        // Slider widget'ını al
        $widget = Widget::where('slug', 'dinamik-slider')->first();
        
        if (!$widget) {
            return;
        }
        
        // Bu widget'a ait tüm tenant_widgets kayıtlarını kontrol et
        $tenantWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($tenantWidgets->count() <= 1) {
            return; // Zaten sadece bir tane varsa işlem yapmaya gerek yok
        }
        
        // İlk kaydı koru, diğerlerini sil
        $firstWidgetId = $tenantWidgets->first()->id;
        
        foreach ($tenantWidgets as $tenantWidget) {
            if ($tenantWidget->id != $firstWidgetId) {
                // Widget item'larını da silelim
                WidgetItem::where('tenant_widget_id', $tenantWidget->id)->delete();
                
                // Widget'ı silelim
                $tenantWidget->delete();
                
                Log::info("Fazla slider widget silindi: ID {$tenantWidget->id}");
            }
        }
        
        Log::info("Central veritabanında fazla slider widget'ları temizlendi");
    }

    private function createTenantSlider()
    {
        // Tenant için daha önce çalıştırılmış mı kontrol et
        $tenantId = tenant('id');
        $tenantCacheKey = self::$runKey . '_tenant_' . $tenantId;
        
        if (Cache::has($tenantCacheKey)) {
            Log::info('Tenant içinde slider widget zaten oluşturulmuş, atlanıyor...');
            return;
        }
        
        // Merkezi veritabanından slider widget'ı al
        $centralWidget = null;
        
        try {
            // Geçici olarak central bağlantısına geç
            $connection = config('database.default');
            config(['database.default' => config('tenancy.database.central_connection')]);
            
            $centralWidget = Widget::where('slug', 'dinamik-slider')->first();
            
            // Bağlantıyı geri al
            config(['database.default' => $connection]);
        } catch (\Exception $e) {
            Log::error('Merkezi widget erişim hatası: ' . $e->getMessage());
            return;
        }
        
        if (!$centralWidget) {
            Log::error('Merkezi slider widget bulunamadı');
            return;
        }
        
        // Önce tenant'ta fazla widget'ları temizleyelim
        $existingWidgets = TenantWidget::where('widget_id', $centralWidget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            // İlk kaydı koru, diğerlerini sil
            $firstWidgetId = $existingWidgets->first()->id;
            
            foreach ($existingWidgets as $existingWidget) {
                if ($existingWidget->id != $firstWidgetId) {
                    // Widget item'larını da silelim
                    WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                    
                    // Widget'ı silelim
                    $existingWidget->delete();
                    
                    Log::info("Tenant'ta fazla slider widget silindi: ID {$existingWidget->id}");
                }
            }
            
            // Zaten bir tane var, yenisini oluşturmaya gerek yok
            if ($existingWidgets->count() >= 1) {
                Log::info('Tenant içinde slider widget zaten var, atlanıyor...');
                return;
            }
        }
        
        // Tenant için widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $centralWidget->id,
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
        
        Log::info('Tenant içinde slider widget başarıyla oluşturuldu. Tenant ID: ' . $tenantId);
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
                    return null;
                }
                
                if (!$sliderCategory) {
                    Log::error("Slider kategorisi oluşturulamadı.");
                    return null;
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
            
            return $widget;
        } else {
            Log::info('Dinamik Slider bileşeni zaten mevcut, atlanıyor...');
            return $existingWidget;
        }
    }
    
    private function createDemoTenantSlider($widget)
    {
        // Önce mevcut widget'ları kontrol edelim ve temizleyelim
        $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            // Zaten bir tane var, yenisini oluşturmaya gerek yok
            Log::info('Central veritabanında Demo Slider widget zaten var, atlanıyor...');
            return;
        }
        
        // Central veritabanındaki tenant_widgets tablosuna ekleme yapılıyor
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
        
        Log::info('Central veritabanında demo tenant slider oluşturuldu.');
    }
    
    private function createSliderForAllTenants($widget)
    {
        // Tüm tenant'ları al
        $tenants = Tenant::where('central', false)->get();
        
        if ($tenants->isEmpty()) {
            Log::info('Tenant bulunamadı, tenant slider oluşturulamıyor.');
            return;
        }
        
        foreach ($tenants as $tenant) {
            // Tenant için daha önce çalıştırılmış mı kontrol et
            $tenantCacheKey = self::$runKey . '_tenant_' . $tenant->id;
            
            if (Cache::has($tenantCacheKey)) {
                Log::info("Tenant {$tenant->id} için slider zaten oluşturulmuş, atlanıyor...");
                continue;
            }
            
            try {
                $tenant->run(function () use ($widget, $tenant) {
                    
                    // Önce tenant'ta fazla widget'ları temizleyelim
                    $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
                    
                    if ($existingWidgets->count() > 1) {
                        // İlk kaydı koru, diğerlerini sil
                        $firstWidgetId = $existingWidgets->first()->id;
                        
                        foreach ($existingWidgets as $existingWidget) {
                            if ($existingWidget->id != $firstWidgetId) {
                                // Widget item'larını da silelim
                                WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                                
                                // Widget'ı silelim
                                $existingWidget->delete();
                                
                                Log::info("Tenant {$tenant->id} için fazla slider widget silindi: ID {$existingWidget->id}");
                            }
                        }
                    }
                    
                    // Zaten bir tane var, yenisini oluşturmaya gerek yok
                    if ($existingWidgets->count() >= 1) {
                        Log::info("Tenant {$tenant->id} için slider widget zaten var, atlanıyor...");
                        return;
                    }
                    
                    // Tenant için widget oluştur
                    $tenantWidget = TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'unique_id' => (string) Str::uuid(),
                            'title' => $tenant->title . ' Slider',
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
                            'title' => $tenant->title . ' Web Sitesi',
                            'description' => 'Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın',
                            'image' => 'https://via.placeholder.com/1200x600',
                            'button_text' => 'Detaylı Bilgi',
                            'button_url' => '/web-cozumleri'
                        ],
                        [
                            'title' => $tenant->title . ' E-Ticaret',
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
                    
                    Log::info("Tenant {$tenant->id} için slider başarıyla oluşturuldu.");
                    
                    // Bu tenant için çalıştırıldığını işaretle
                    Cache::put($tenantCacheKey, true, 600);
                });
            } catch (\Exception $e) {
                Log::error("Tenant {$tenant->id} için slider oluşturma hatası: " . $e->getMessage());
            }
        }
    }
}