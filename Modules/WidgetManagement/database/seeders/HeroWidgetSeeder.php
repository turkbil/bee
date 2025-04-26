<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class HeroWidgetSeeder extends Seeder
{
    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            try {
                $this->createTenantHero();
                return;
            } catch (\Exception $e) {
                Log::error('Tenant HeroWidgetSeeder hatası: ' . $e->getMessage());
                return;
            }
        }

        // Central işlemleri
        try {
            // Modül Bileşenleri kategorisini kontrol et
            $moduleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')
                ->orWhere('title', 'Modül Bileşenleri')
                ->first();
            
            if (!$moduleCategory) {
                try {
                    $moduleCategory = new WidgetCategory([
                        'title' => 'Modül Bileşenleri',
                        'slug' => 'modul-bilesenleri',
                        'description' => 'Sistem modüllerine ait bileşenler',
                        'icon' => 'fa-cubes',
                        'order' => 1,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => true
                    ]);
                    
                    $moduleCategory->save();
                    
                } catch (\Exception $e) {
                    Log::error("Modül Bileşenleri kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                }
            } else {
            }
            
            $this->cleanupExtraHeroes();
            
            // Statik hero widget'ı oluştur
            $widget = $this->createHeroWidget();
            
            if ($widget) {
                // Central veritabanı için sadece bir tane hero oluşturalım
                $this->createDemoTenantHero($widget);
                
                // Tüm mevcut tenant'lar için hero'ları oluştur
                $this->createHeroForAllTenants($widget);
            }
        } catch (\Exception $e) {
            Log::error('HeroWidgetSeeder central hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    private function cleanupExtraHeroes()
    {
        // Hero widget'ını al
        $widget = Widget::where('slug', 'full-width-hero')->first();
        
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
                // Widget'ı silelim
                $tenantWidget->delete();
            }
        }
    }
    
    private function createTenantHero()
    {
        // Merkezi veritabanından hero widget'ı al
        $centralWidget = null;
        
        try {
            // Geçici olarak central bağlantısına geç
            $connection = config('database.default');
            config(['database.default' => config('tenancy.database.central_connection')]);
            
            $centralWidget = Widget::where('slug', 'full-width-hero')->first();
            
            // Bağlantıyı geri al
            config(['database.default' => $connection]);
        } catch (\Exception $e) {
            Log::error('Merkezi widget erişim hatası: ' . $e->getMessage());
            return;
        }
        
        if (!$centralWidget) {
            Log::error('Merkezi hero widget bulunamadı');
            return;
        }
        
        // Önce tenant'ta fazla widget'ları temizleyelim
        $existingWidgets = TenantWidget::where('widget_id', $centralWidget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            // İlk kaydı koru, diğerlerini sil
            $firstWidgetId = $existingWidgets->first()->id;
            
            foreach ($existingWidgets as $existingWidget) {
                if ($existingWidget->id != $firstWidgetId) {
                    // Widget'ı silelim
                    $existingWidget->delete();
                }
            }
            
            // Zaten bir tane var, yenisini oluşturmaya gerek yok
            if ($existingWidgets->count() >= 1) {
                return;
            }
        }
        
        // Tenant için widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $centralWidget->id,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Full Width Hero',
                'subtitle' => 'Modern ve Etkileyici',
                'description' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın.',
                'button_text' => 'Daha Fazla',
                'button_url' => '#',
                'show_secondary_button' => true,
                'secondary_button_text' => 'İletişim',
                'secondary_button_url' => '/iletisim',
                'bg_color' => '#f8f9fa',
                'text_color' => '#212529'
            ],
            'order' => 0,
            'is_active' => true
        ]);
    }

    private function createHeroWidget()
    {
        // Hero kategorisini bul, yoksa oluştur
        $heroCategory = WidgetCategory::where('slug', 'herolar')->first();
        
        // Eğer hero kategorisi yoksa, önce 'Content' kategorisini kontrol et
        if (!$heroCategory) {
            $contentCategory = WidgetCategory::where('slug', 'content')->first();
            
            if ($contentCategory) {
                // Hero alt kategorisini oluştur
                $heroCategory = WidgetCategory::create([
                    'title' => 'Herolar',
                    'slug' => 'herolar',
                    'description' => 'Sayfa üst kısmında kullanılabilecek hero bileşenleri',
                    'icon' => 'fa-heading',
                    'order' => 5,
                    'is_active' => true,
                    'parent_id' => $contentCategory->widget_category_id,
                    'has_subcategories' => false
                ]);
            } else {
                // Content kategorisi yoksa, ana kategori olarak oluştur
                try {
                    $heroCategory = WidgetCategory::create([
                        'title' => 'Herolar',
                        'slug' => 'herolar',
                        'description' => 'Sayfa üst kısmında kullanılabilecek hero bileşenleri',
                        'icon' => 'fa-heading',
                        'order' => 5,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => false
                    ]);
                } catch (\Exception $e) {
                    Log::error("Hero kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return null;
                }
                
                if (!$heroCategory) {
                    Log::error("Hero kategorisi oluşturulamadı.");
                    return null;
                }
            }
        }
        
        // Hero widget'ı zaten var mı kontrolü
        $existingWidget = Widget::where('slug', 'full-width-hero')->first();
        
        if (!$existingWidget) {
            // Hero widget'ı oluştur - static tip olarak
            $widget = Widget::create([
                'widget_category_id' => $heroCategory->widget_category_id,
                'name' => 'Full Width Hero',
                'slug' => 'full-width-hero',
                'description' => 'Sayfanın üst kısmında kullanılabilecek tam genişlikte hero bileşeni',
                'type' => 'static',
                'content_html' => '<div class="py-5 text-center" style="background-color: {{bg_color}}; color: {{text_color}};">
    <div class="container">
        <div class="row py-lg-5">
            <div class="col-lg-8 col-md-10 mx-auto">
                <h1 class="fw-light">{{title}}</h1>
                <h3 class="fw-light">{{subtitle}}</h3>
                <p class="lead">{{description}}</p>
                <p>
                    {{#if button_text}}
                    <a href="{{button_url}}" class="btn btn-primary my-2 me-2">{{button_text}}</a>
                    {{/if}}
                    {{#if show_secondary_button}}
                    <a href="{{secondary_button_url}}" class="btn btn-secondary my-2">{{secondary_button_text}}</a>
                    {{/if}}
                </p>
            </div>
        </div>
    </div>
</div>',
                'content_css' => '',
                'content_js' => '',
                'has_items' => false,
                'is_active' => true,
                'is_core' => true,
                'settings_schema' => [
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'default' => 'Full Width Hero'
                    ],
                    [
                        'name' => 'subtitle',
                        'label' => 'Alt Başlık',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'Modern ve Etkileyici'
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Açıklama',
                        'type' => 'textarea',
                        'required' => false,
                        'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.'
                    ],
                    [
                        'name' => 'button_text',
                        'label' => 'Buton Metni',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'Daha Fazla'
                    ],
                    [
                        'name' => 'button_url',
                        'label' => 'Buton URL',
                        'type' => 'text',
                        'required' => false,
                        'default' => '#'
                    ],
                    [
                        'name' => 'show_secondary_button',
                        'label' => 'İkinci Butonu Göster',
                        'type' => 'checkbox',
                        'required' => false,
                        'default' => true
                    ],
                    [
                        'name' => 'secondary_button_text',
                        'label' => 'İkinci Buton Metni',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'İletişim'
                    ],
                    [
                        'name' => 'secondary_button_url',
                        'label' => 'İkinci Buton URL',
                        'type' => 'text',
                        'required' => false,
                        'default' => '/iletisim'
                    ],
                    [
                        'name' => 'bg_color',
                        'label' => 'Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#f8f9fa'
                    ],
                    [
                        'name' => 'text_color',
                        'label' => 'Metin Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#212529'
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ]
                ]
            ]);
            
            return $widget;
        } else {
            // Widget varsa ama tipi "file" ise "static" olarak güncelle
            if ($existingWidget->type === 'file') {
                $existingWidget->update([
                    'type' => 'static',
                    'file_path' => null,
                    'content_html' => '<div class="py-5 text-center" style="background-color: {{bg_color}}; color: {{text_color}};">
    <div class="container">
        <div class="row py-lg-5">
            <div class="col-lg-8 col-md-10 mx-auto">
                <h1 class="fw-light">{{title}}</h1>
                <h3 class="fw-light">{{subtitle}}</h3>
                <p class="lead">{{description}}</p>
                <p>
                    {{#if button_text}}
                    <a href="{{button_url}}" class="btn btn-primary my-2 me-2">{{button_text}}</a>
                    {{/if}}
                    {{#if show_secondary_button}}
                    <a href="{{secondary_button_url}}" class="btn btn-secondary my-2">{{secondary_button_text}}</a>
                    {{/if}}
                </p>
            </div>
        </div>
    </div>
</div>',
                    'settings_schema' => [
                        [
                            'name' => 'title',
                            'label' => 'Başlık',
                            'type' => 'text',
                            'required' => true,
                            'default' => 'Full Width Hero'
                        ],
                        [
                            'name' => 'subtitle',
                            'label' => 'Alt Başlık',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'Modern ve Etkileyici'
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Açıklama',
                            'type' => 'textarea',
                            'required' => false,
                            'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.'
                        ],
                        [
                            'name' => 'button_text',
                            'label' => 'Buton Metni',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'Daha Fazla'
                        ],
                        [
                            'name' => 'button_url',
                            'label' => 'Buton URL',
                            'type' => 'text',
                            'required' => false,
                            'default' => '#'
                        ],
                        [
                            'name' => 'show_secondary_button',
                            'label' => 'İkinci Butonu Göster',
                            'type' => 'checkbox',
                            'required' => false,
                            'default' => true
                        ],
                        [
                            'name' => 'secondary_button_text',
                            'label' => 'İkinci Buton Metni',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'İletişim'
                        ],
                        [
                            'name' => 'secondary_button_url',
                            'label' => 'İkinci Buton URL',
                            'type' => 'text',
                            'required' => false,
                            'default' => '/iletisim'
                        ],
                        [
                            'name' => 'bg_color',
                            'label' => 'Arkaplan Rengi',
                            'type' => 'color',
                            'required' => false,
                            'default' => '#f8f9fa'
                        ],
                        [
                            'name' => 'text_color',
                            'label' => 'Metin Rengi',
                            'type' => 'color',
                            'required' => false,
                            'default' => '#212529'
                        ],
                        [
                            'name' => 'unique_id',
                            'label' => 'Benzersiz ID',
                            'type' => 'text',
                            'required' => false,
                            'system' => true,
                            'hidden' => true
                        ]
                    ]
                ]);
            }
            return $existingWidget;
        }
    }
    
    private function createDemoTenantHero($widget)
    {
        // Önce mevcut widget'ları kontrol edelim ve temizleyelim
        $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            // Zaten bir tane var, yenisini oluşturmaya gerek yok
            return;
        }
        
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widget->id,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Full Width Hero',
                'subtitle' => 'Modern ve Etkileyici',
                'description' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.',
                'button_text' => 'Daha Fazla',
                'button_url' => '#',
                'show_secondary_button' => true,
                'secondary_button_text' => 'İletişim',
                'secondary_button_url' => '/iletisim',
                'bg_color' => '#f8f9fa',
                'text_color' => '#212529'
            ],
            'order' => 0,
            'is_active' => true
        ]);
    }
    
    private function createHeroForAllTenants($widget)
    {
        // Tüm tenant'ları al
        $tenants = Tenant::where('central', false)->get();
        
        if ($tenants->isEmpty()) {
            return;
        }
        
        foreach ($tenants as $tenant) {
            try {
                $tenant->run(function () use ($widget, $tenant) {
                    // Önce tenant'ta fazla widget'ları temizleyelim
                    $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
                    
                    if ($existingWidgets->count() > 1) {
                        // İlk kaydı koru, diğerlerini sil
                        $firstWidgetId = $existingWidgets->first()->id;
                        
                        foreach ($existingWidgets as $existingWidget) {
                            if ($existingWidget->id != $firstWidgetId) {
                                // Widget'ı silelim
                                $existingWidget->delete();
                            }
                        }
                    }
                    
                    // Zaten bir tane var, yenisini oluşturmaya gerek yok
                    if ($existingWidgets->count() >= 1) {
                        return;
                    }
                    
                    // Tenant için widget oluştur
                    $tenantWidget = TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'unique_id' => (string) Str::uuid(),
                            'title' => $tenant->title . ' Hero',
                            'subtitle' => 'Hoş Geldiniz',
                            'description' => $tenant->title . ' web sitesine hoş geldiniz. Modern ve özelleştirilebilir tasarımımızla hizmetinizdeyiz.',
                            'button_text' => 'Keşfet',
                            'button_url' => '/hakkimizda',
                            'show_secondary_button' => true,
                            'secondary_button_text' => 'İletişim',
                            'secondary_button_url' => '/iletisim',
                            'bg_color' => '#f8f9fa',
                            'text_color' => '#212529'
                        ],
                        'order' => 0,
                        'is_active' => true
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("Tenant {$tenant->id} için hero oluşturma hatası: " . $e->getMessage());
            }
        }
    }
}