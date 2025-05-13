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
            // Moduller kategorisini kontrol et
            $moduleCategory = WidgetCategory::where('slug', 'moduller')
                ->orWhere('title', 'Moduller')
                ->first();
            
            if (!$moduleCategory) {
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
                    
                } catch (\Exception $e) {
                    Log::error("Moduller kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                }
            }
            
            $this->cleanupExtraHeroes();
            
            // Statik hero widget'ı oluştur
            $widget = $this->createHeroWidget();
            
            if ($widget) {
                // Central için örnek bir hero oluştur (tenant değil)
                $this->createCentralHeroExample($widget);
                
                // SADECE gerçek tenant'lar için hero'ları oluştur
                $this->createHeroForTenants($widget);
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
                'widget.unique_id' => (string) Str::uuid(),
                'widget.title' => 'Full Width Hero',
                'widget.subtitle' => 'Modern ve Etkileyici',
                'widget.description' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın.',
                'widget.button_text' => 'Daha Fazla',
                'widget.button_url' => '#',
                'widget.show_secondary_button' => true,
                'widget.secondary_button_text' => 'İletişim',
                'widget.secondary_button_url' => '/iletisim',
                'widget.bg_color' => '#f8f9fa',
                'widget.text_color' => '#212529'
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
                'content_html' => '<div class="py-5 text-center" style="background-color: {{widget.bg_color}}; color: {{widget.text_color}};">
    <div class="container mx-auto px-4">
        <div class="py-8 lg:py-12">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-3xl font-light mb-4">{{widget.title}}</h1>
                <h3 class="text-xl font-light mb-3">{{widget.subtitle}}</h3>
                <p class="text-lg mb-6">{{widget.description}}</p>
                <div>
                    {{#if widget.button_text}}
                    <a href="{{widget.button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{widget.button_text}}</a>
                    {{/if}}
                    {{#if widget.show_secondary_button}}
                    <a href="{{widget.secondary_button_url}}" class="inline-block px-4 py-2 mb-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">{{widget.secondary_button_text}}</a>
                    {{/if}}
                </div>
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
                        'name' => 'widget.title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'default' => 'Full Width Hero'
                    ],
                    [
                        'name' => 'widget.subtitle',
                        'label' => 'Alt Başlık',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'Modern ve Etkileyici'
                    ],
                    [
                        'name' => 'widget.description',
                        'label' => 'Açıklama',
                        'type' => 'textarea',
                        'required' => false,
                        'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.'
                    ],
                    [
                        'name' => 'widget.button_text',
                        'label' => 'Buton Metni',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'Daha Fazla'
                    ],
                    [
                        'name' => 'widget.button_url',
                        'label' => 'Buton URL',
                        'type' => 'text',
                        'required' => false,
                        'default' => '#'
                    ],
                    [
                        'name' => 'widget.show_secondary_button',
                        'label' => 'İkinci Butonu Göster',
                        'type' => 'checkbox',
                        'required' => false,
                        'default' => true
                    ],
                    [
                        'name' => 'widget.secondary_button_text',
                        'label' => 'İkinci Buton Metni',
                        'type' => 'text',
                        'required' => false,
                        'default' => 'İletişim'
                    ],
                    [
                        'name' => 'widget.secondary_button_url',
                        'label' => 'İkinci Buton URL',
                        'type' => 'text',
                        'required' => false,
                        'default' => '/iletisim'
                    ],
                    [
                        'name' => 'widget.bg_color',
                        'label' => 'Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#f8f9fa'
                    ],
                    [
                        'name' => 'widget.text_color',
                        'label' => 'Metin Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#212529'
                    ],
                    [
                        'name' => 'widget.unique_id',
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
                    'content_html' => '<div class="py-5 text-center" style="background-color: {{widget.bg_color}}; color: {{widget.text_color}};">
    <div class="container mx-auto px-4">
        <div class="py-8 lg:py-12">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-3xl font-light mb-4">{{widget.title}}</h1>
                <h3 class="text-xl font-light mb-3">{{widget.subtitle}}</h3>
                <p class="text-lg mb-6">{{widget.description}}</p>
                <div>
                    {{#if widget.button_text}}
                    <a href="{{widget.button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{widget.button_text}}</a>
                    {{/if}}
                    {{#if widget.show_secondary_button}}
                    <a href="{{widget.secondary_button_url}}" class="inline-block px-4 py-2 mb-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">{{widget.secondary_button_text}}</a>
                    {{/if}}
                </div>
            </div>
        </div>
    </div>
</div>',
                    'settings_schema' => [
                        [
                            'name' => 'widget.title',
                            'label' => 'Başlık',
                            'type' => 'text',
                            'required' => true,
                            'default' => 'Full Width Hero'
                        ],
                        [
                            'name' => 'widget.subtitle',
                            'label' => 'Alt Başlık',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'Modern ve Etkileyici'
                        ],
                        [
                            'name' => 'widget.description',
                            'label' => 'Açıklama',
                            'type' => 'textarea',
                            'required' => false,
                            'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.'
                        ],
                        [
                            'name' => 'widget.button_text',
                            'label' => 'Buton Metni',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'Daha Fazla'
                        ],
                        [
                            'name' => 'widget.button_url',
                            'label' => 'Buton URL',
                            'type' => 'text',
                            'required' => false,
                            'default' => '#'
                        ],
                        [
                            'name' => 'widget.show_secondary_button',
                            'label' => 'İkinci Butonu Göster',
                            'type' => 'checkbox',
                            'required' => false,
                            'default' => true
                        ],
                        [
                            'name' => 'widget.secondary_button_text',
                            'label' => 'İkinci Buton Metni',
                            'type' => 'text',
                            'required' => false,
                            'default' => 'İletişim'
                        ],
                        [
                            'name' => 'widget.secondary_button_url',
                            'label' => 'İkinci Buton URL',
                            'type' => 'text',
                            'required' => false,
                            'default' => '/iletisim'
                        ],
                        [
                            'name' => 'widget.bg_color',
                            'label' => 'Arkaplan Rengi',
                            'type' => 'color',
                            'required' => false,
                            'default' => '#f8f9fa'
                        ],
                        [
                            'name' => 'widget.text_color',
                            'label' => 'Metin Rengi',
                            'type' => 'color',
                            'required' => false,
                            'default' => '#212529'
                        ],
                        [
                            'name' => 'widget.unique_id',
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
    
    // Central veritabanı için örnek hero - sadece central veritabanında çalışır, tenant değildir
    private function createCentralHeroExample($widget)
    {
        // Bu fonksiyon sadece bir kez çalışır ve central için bir örnek oluşturur
        
        // Önce central veritabanını kontrol et
        $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            // Zaten var, yeni oluşturmaya gerek yok
            return;
        }
        
        TenantWidget::create([
            'widget_id' => $widget->id,
            'settings' => [
                'widget.unique_id' => (string) Str::uuid(),
                'widget.title' => 'Central Hero Demo',
                'widget.subtitle' => 'Demo Amaçlı',
                'widget.description' => 'Bu hero widget sadece central veritabanı için örnektir.',
                'widget.button_text' => 'Demo',
                'widget.button_url' => '#',
                'widget.show_secondary_button' => true,
                'widget.secondary_button_text' => 'Örnek',
                'widget.secondary_button_url' => '#',
                'widget.bg_color' => '#f8f9fa',
                'widget.text_color' => '#212529'
            ],
            'order' => 0,
            'is_active' => true
        ]);
    }
    
    // Sadece gerçek tenant'lar için hero oluşturur - central tenant için çalışmaz
    private function createHeroForTenants($widget)
    {
        // SADECE central=false olan gerçek tenant'ları al
        $tenants = Tenant::where('central', false)->get();
        
        if ($tenants->isEmpty()) {
            Log::info("Hiç gerçek tenant bulunamadı.");
            return;
        }
        
        foreach ($tenants as $tenant) {
            try {
                // Her tenant için ayrı ayrı çalıştır
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
                    TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'widget.unique_id' => (string) Str::uuid(),
                            'widget.title' => $tenant->title . ' Hero',
                            'widget.subtitle' => 'Hoş Geldiniz',
                            'widget.description' => $tenant->title . ' web sitesine hoş geldiniz. Modern ve özelleştirilebilir tasarımımızla hizmetinizdeyiz.',
                            'widget.button_text' => 'Keşfet',
                            'widget.button_url' => '/hakkimizda',
                            'widget.show_secondary_button' => true,
                            'widget.secondary_button_text' => 'İletişim',
                            'widget.secondary_button_url' => '/iletisim',
                            'widget.bg_color' => '#f8f9fa',
                            'widget.text_color' => '#212529'
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