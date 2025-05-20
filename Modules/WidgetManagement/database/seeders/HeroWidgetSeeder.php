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

class HeroWidgetSeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'hero_widget_seeder_executed';
    
    public function run()
    {
        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . config('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('HeroWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }

        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            try {
                $this->createTenantHero();
                
                // Bu tenant için çalıştırıldığını işaretle
                $tenantId = tenant('id');
                Cache::put(self::$runKey . '_tenant_' . $tenantId, true, 600);
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
            
            // Hero widget'ı oluştur
            $widget = $this->createHeroWidget();
            
            if ($widget) {
                // Central için örnek bir hero oluştur (tenant değil)
                $this->createCentralHeroExample($widget);
                
                // SADECE gerçek tenant'lar için hero'ları oluştur
                $this->createHeroForTenants($widget);
            }
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
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
                // Widget item'larını da silelim
                WidgetItem::where('tenant_widget_id', $tenantWidget->id)->delete();
                
                // Widget'ı silelim
                $tenantWidget->delete();
            }
        }
    }
    
    private function createTenantHero()
    {
        // Tenant için daha önce çalıştırılmış mı kontrol et
        $tenantId = tenant('id');
        $tenantCacheKey = self::$runKey . '_tenant_' . $tenantId;
        
        if (Cache::has($tenantCacheKey)) {
            Log::info('Tenant içinde hero widget zaten oluşturulmuş, atlanıyor...');
            return;
        }
        
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
                    // Widget item'larını da silelim
                    WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                    
                    // Widget'ı silelim
                    $existingWidget->delete();
                }
            }
            
            // Zaten bir tane var, yenisini oluşturmaya gerek yok
            if ($existingWidgets->count() >= 1) {
                Log::info('Tenant içinde hero widget zaten var, atlanıyor...');
                return;
            }
        }
        
        // Tenant için widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $centralWidget->id,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Full Width Hero',
                'bg_color' => '#f8f9fa',
                'text_color' => '#212529'
            ],
            'order' => 0,
            'is_active' => true
        ]);
        
        // Hero için item oluştur
        WidgetItem::create([
            'tenant_widget_id' => $tenantWidget->id,
            'content' => [
                'title' => 'Full Width Hero',
                'subtitle' => 'Modern ve Etkileyici',
                'description' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın.',
                'button_text' => 'Daha Fazla',
                'button_url' => '#',
                'show_secondary_button' => true,
                'secondary_button_text' => 'İletişim',
                'secondary_button_url' => '/iletisim'
            ],
            'order' => 1
        ]);
        
        Log::info('Tenant içinde hero widget başarıyla oluşturuldu. Tenant ID: ' . $tenantId);
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
            // Hero widget'ı oluştur - dynamic tip olarak
            $widget = Widget::create([
                'widget_category_id' => $heroCategory->widget_category_id,
                'name' => 'Full Width Hero',
                'slug' => 'full-width-hero',
                'description' => 'Sayfanın üst kısmında kullanılabilecek tam genişlikte hero bileşeni',
                'type' => 'dynamic',
                'content_html' => '<div class="py-5 text-center" style="background-color: {{bg_color}}; color: {{text_color}};">
    <div class="container mx-auto px-4">
        <div class="py-8 lg:py-12">
            <div class="max-w-3xl mx-auto">
                {{#each items}}
                <h1 class="text-3xl font-light mb-4">{{title}}</h1>
                <h3 class="text-xl font-light mb-3">{{subtitle}}</h3>
                <p class="text-lg mb-6">{{description}}</p>
                <div>
                    {{#if button_text}}
                    <a href="{{button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{button_text}}</a>
                    {{/if}}
                    {{#if show_secondary_button}}
                    <a href="{{secondary_button_url}}" class="inline-block px-4 py-2 mb-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">{{secondary_button_text}}</a>
                    {{/if}}
                </div>
                {{/each}}
            </div>
        </div>
    </div>
</div>',
                'content_css' => '',
                'content_js' => '',
                'has_items' => true,
                'is_active' => true,
                'is_core' => true,
                'item_schema' => [
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
                        'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın.'
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
                    ]
                ]
            ]);
            
            return $widget;
        } else {
            // Widget varsa ama tipi "static" veya "file" ise "dynamic" olarak güncelle
            if ($existingWidget->type !== 'dynamic') {
                $existingWidget->update([
                    'type' => 'dynamic',
                    'file_path' => null,
                    'content_html' => '<div class="py-5 text-center" style="background-color: {{bg_color}}; color: {{text_color}};">
    <div class="container mx-auto px-4">
        <div class="py-8 lg:py-12">
            <div class="max-w-3xl mx-auto">
                {{#each items}}
                <h1 class="text-3xl font-light mb-4">{{title}}</h1>
                <h3 class="text-xl font-light mb-3">{{subtitle}}</h3>
                <p class="text-lg mb-6">{{description}}</p>
                <div>
                    {{#if button_text}}
                    <a href="{{button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{button_text}}</a>
                    {{/if}}
                    {{#if show_secondary_button}}
                    <a href="{{secondary_button_url}}" class="inline-block px-4 py-2 mb-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">{{secondary_button_text}}</a>
                    {{/if}}
                </div>
                {{/each}}
            </div>
        </div>
    </div>
</div>',
                    'has_items' => true,
                    'item_schema' => [
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
                            'default' => 'Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın.'
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
        
        // Tenant için widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widget->id,
            'settings' => [
                'unique_id' => (string) Str::uuid(),
                'title' => 'Central Hero Demo',
                'bg_color' => '#f8f9fa',
                'text_color' => '#212529'
            ],
            'order' => 0,
            'is_active' => true
        ]);
        
        // Hero için item oluştur
        WidgetItem::create([
            'tenant_widget_id' => $tenantWidget->id,
            'content' => [
                'title' => 'Central Hero Demo',
                'subtitle' => 'Demo Amaçlı',
                'description' => 'Bu hero widget sadece central veritabanı için örnektir.',
                'button_text' => 'Demo',
                'button_url' => '#',
                'show_secondary_button' => true,
                'secondary_button_text' => 'Örnek',
                'secondary_button_url' => '#'
            ],
            'order' => 1
        ]);
        
        Log::info('Central veritabanında demo hero oluşturuldu.');
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
                // Tenant için daha önce çalıştırılmış mı kontrol et
                $tenantCacheKey = self::$runKey . '_tenant_' . $tenant->id;
                
                if (Cache::has($tenantCacheKey)) {
                    Log::info("Tenant {$tenant->id} için hero zaten oluşturulmuş, atlanıyor...");
                    continue;
                }
                
                // Her tenant için ayrı ayrı çalıştır
                $tenant->run(function () use ($widget, $tenant, $tenantCacheKey) {
                    
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
                            }
                        }
                    }
                    
                    // Zaten bir tane var, yenisini oluşturmaya gerek yok
                    if ($existingWidgets->count() >= 1) {
                        Log::info("Tenant {$tenant->id} için hero widget zaten var, atlanıyor...");
                        Cache::put($tenantCacheKey, true, 600);
                        return;
                    }
                    
                    // Tenant için widget oluştur
                    $tenantWidget = TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'unique_id' => (string) Str::uuid(),
                            'title' => $tenant->title . ' Hero',
                            'bg_color' => '#f8f9fa',
                            'text_color' => '#212529'
                        ],
                        'order' => 0,
                        'is_active' => true
                    ]);
                    
                    // Hero için item oluştur
                    WidgetItem::create([
                        'tenant_widget_id' => $tenantWidget->id,
                        'content' => [
                            'title' => $tenant->title . ' Hero',
                            'subtitle' => 'Hoş Geldiniz',
                            'description' => $tenant->title . ' web sitesine hoş geldiniz. Modern ve özelleştirilebilir tasarımımızla hizmetinizdeyiz.',
                            'button_text' => 'Keşfet',
                            'button_url' => '/hakkimizda',
                            'show_secondary_button' => true,
                            'secondary_button_text' => 'İletişim',
                            'secondary_button_url' => '/iletisim'
                        ],
                        'order' => 1
                    ]);
                    
                    Log::info("Tenant {$tenant->id} için hero başarıyla oluşturuldu.");
                    
                    // Bu tenant için çalıştırıldığını işaretle
                    Cache::put($tenantCacheKey, true, 600);
                });
            } catch (\Exception $e) {
                Log::error("Tenant {$tenant->id} için hero oluşturma hatası: " . $e->getMessage());
            }
        }
    }
}