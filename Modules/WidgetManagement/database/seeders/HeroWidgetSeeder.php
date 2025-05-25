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
    private static $runKey = 'hero_widget_seeder_executed';
    
    public function run()
    {
        $cacheKey = self::$runKey . '_' . config('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('HeroWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }

        if (function_exists('tenant') && tenant()) {
            try {
                $this->createTenantHero();
                
                $tenantId = tenant('id');
                Cache::put(self::$runKey . '_tenant_' . $tenantId, true, 600);
                return;
            } catch (\Exception $e) {
                Log::error('Tenant HeroWidgetSeeder hatası: ' . $e->getMessage());
                return;
            }
        }

        try {
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
            
            $widget = $this->createHeroWidget();
            
            if ($widget) {
                $this->createCentralHeroExample($widget);
                $this->createHeroForTenants($widget);
            }
            
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('HeroWidgetSeeder central hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    private function cleanupExtraHeroes()
    {
        $widget = Widget::where('slug', 'full-width-hero')->first();
        
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
            }
        }
    }
    
    private function createTenantHero()
    {
        $tenantId = tenant('id');
        $tenantCacheKey = self::$runKey . '_tenant_' . $tenantId;
        
        if (Cache::has($tenantCacheKey)) {
            Log::info('Tenant içinde hero widget zaten oluşturulmuş, atlanıyor...');
            return;
        }
        
        $centralWidget = null;
        
        try {
            $connection = config('database.default');
            config(['database.default' => config('tenancy.database.central_connection')]);
            
            $centralWidget = Widget::where('slug', 'full-width-hero')->first();
            
            config(['database.default' => $connection]);
        } catch (\Exception $e) {
            Log::error('Merkezi widget erişim hatası: ' . $e->getMessage());
            return;
        }
        
        if (!$centralWidget) {
            Log::error('Merkezi hero widget bulunamadı');
            return;
        }
        
        $existingWidgets = TenantWidget::where('widget_id', $centralWidget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            $firstWidgetId = $existingWidgets->first()->id;
            
            foreach ($existingWidgets as $existingWidget) {
                if ($existingWidget->id != $firstWidgetId) {
                    WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                    $existingWidget->delete();
                }
            }
            
            if ($existingWidgets->count() >= 1) {
                Log::info('Tenant içinde hero widget zaten var, atlanıyor...');
                return;
            }
        }
        
        $tenantWidget = TenantWidget::create([
            'widget_id' => $centralWidget->id,
            'settings' => [
                'widget_unique_id' => (string) Str::uuid(),
                'widget_title' => 'Full Width Hero',
                'widget_hero_title' => 'Tenant Özel Hero Başlığı',
                'widget_hero_subtitle' => 'Bu tenant için özel olarak hazırlanmış alt başlık.',
                'widget_hero_description' => 'Tenant kullanıcılarına özel, dinamik olarak yönetilebilen hero alanı.',
                'widget_button_text' => 'Keşfet',
                'widget_button_url' => '/urunler'
            ],
            'order' => 0,
            'is_active' => true
        ]);
        
        Log::info('Tenant içinde hero widget başarıyla oluşturuldu. Tenant ID: ' . $tenantId);
    }

    private function createHeroWidget()
    {
        $heroCategory = WidgetCategory::where('slug', 'herolar')->first();
        
        if (!$heroCategory) {
            $contentCategory = WidgetCategory::where('slug', 'content')->first();
            
            if ($contentCategory) {
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
        
        $existingWidget = Widget::where('slug', 'full-width-hero')->first();
        
        if (!$existingWidget) {
            $widget = Widget::create([
                'widget_category_id' => $heroCategory->widget_category_id,
                'name' => 'Full Width Hero',
                'slug' => 'full-width-hero',
                'description' => 'Sayfanın üst kısmında kullanılabilecek tam genişlikte hero bileşeni',
                'type' => 'dynamic',
                'content_html' => '<div class="py-5 text-center">
                    <div class="container mx-auto px-4">
                        <div class="py-8 lg:py-12">
                            <div class="max-w-3xl mx-auto">
                                <h1 class="text-3xl font-light mb-4">{{widget_hero_title}}</h1>
                                <h3 class="text-xl font-light mb-3">{{widget_hero_subtitle}}</h3>
                                <p class="text-lg mb-6">{{widget_hero_description}}</p>
                                <div>
                                    {{#if widget_button_text}}
                                    <a href="{{widget_button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{widget_button_text}}</a>
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
                'item_schema' => [],
                'settings_schema' => [
                    [
                        'name' => 'widget_title',
                        'label' => 'Widget Başlığı (Yönetim)',
                        'type' => 'text',
                        'required' => true,
                        'properties' => [
                            'default_value' => 'Full Width Hero',
                            'width' => 12,
                            'placeholder' => 'Widget başlığını giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_hero_title',
                        'label' => 'Ana Başlık',
                        'type' => 'text',
                        'required' => true,
                        'properties' => [
                            'default_value' => 'Etkileyici Bir Başlık',
                            'width' => 12,
                            'placeholder' => 'Ana başlığı giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_hero_subtitle',
                        'label' => 'Alt Başlık',
                        'type' => 'text',
                        'required' => false,
                        'properties' => [
                            'default_value' => 'Harika bir alt başlık ile devam edin.',
                            'width' => 12,
                            'placeholder' => 'Alt başlığı giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_hero_description',
                        'label' => 'Açıklama Metni',
                        'type' => 'textarea',
                        'required' => false,
                        'properties' => [
                            'default_value' => 'Buraya projenizi veya hizmetinizi anlatan kısa ve etkili bir açıklama yazabilirsiniz.',
                            'width' => 12,
                            'placeholder' => 'Açıklama metnini giriniz',
                            'rows' => 4
                        ]
                    ],
                    [
                        'name' => 'widget_button_text',
                        'label' => 'Buton Metni',
                        'type' => 'text',
                        'required' => false,
                        'properties' => [
                            'default_value' => 'Daha Fazla Bilgi',
                            'width' => 6,
                            'placeholder' => 'Buton metnini giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_button_url',
                        'label' => 'Buton URL',
                        'type' => 'text',
                        'required' => false,
                        'properties' => [
                            'default_value' => '#',
                            'width' => 6,
                            'placeholder' => 'Buton URL\'sini giriniz'
                        ]
                    ],
                    [
                        'name' => 'widget_unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true,
                        'properties' => [
                            'width' => 12
                        ]
                    ]
                ]
            ]);
            
            return $widget;
        } else {
            if ($existingWidget->type !== 'dynamic' || $existingWidget->has_items === true) { 
                $existingWidget->update([
                    'type' => 'dynamic',
                    'file_path' => null,
                    'content_html' => '<div class="py-5 text-center">
                        <div class="container mx-auto px-4">
                            <div class="py-8 lg:py-12">
                                <div class="max-w-3xl mx-auto">
                                    <h1 class="text-3xl font-light mb-4">{{widget_hero_title}}</h1>
                                    <h3 class="text-xl font-light mb-3">{{widget_hero_subtitle}}</h3>
                                    <p class="text-lg mb-6">{{widget_hero_description}}</p>
                                    <div>
                                        {{#if widget_button_text}}
                                        <a href="{{widget_button_url}}" class="inline-block px-4 py-2 mr-2 mb-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">{{widget_button_text}}</a>
                                        {{/if}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>',
                    'has_items' => false,
                    'item_schema' => [],
                    'settings_schema' => [
                        [
                            'name' => 'widget_title',
                            'label' => 'Widget Başlığı (Yönetim)',
                            'type' => 'text',
                            'required' => true,
                            'properties' => [
                                'default_value' => 'Full Width Hero',
                                'width' => 12,
                                'placeholder' => 'Widget başlığını giriniz'
                            ]
                        ],
                        [
                            'name' => 'widget_hero_title',
                            'label' => 'Ana Başlık',
                            'type' => 'text',
                            'required' => true,
                            'properties' => [
                                'default_value' => 'Etkileyici Bir Başlık',
                                'width' => 12,
                                'placeholder' => 'Ana başlığı giriniz'
                            ]
                        ],
                        [
                            'name' => 'widget_hero_subtitle',
                            'label' => 'Alt Başlık',
                            'type' => 'text',
                            'required' => false,
                            'properties' => [
                                'default_value' => 'Harika bir alt başlık ile devam edin.',
                                'width' => 12,
                                'placeholder' => 'Alt başlığı giriniz'
                            ]
                        ],
                        [
                            'name' => 'widget_hero_description',
                            'label' => 'Açıklama Metni',
                            'type' => 'textarea',
                            'required' => false,
                            'properties' => [
                                'default_value' => 'Buraya projenizi veya hizmetinizi anlatan kısa ve etkili bir açıklama yazabilirsiniz.',
                                'width' => 12,
                                'placeholder' => 'Açıklama metnini giriniz',
                                'rows' => 4
                            ]
                        ],
                        [
                            'name' => 'widget_button_text',
                            'label' => 'Buton Metni',
                            'type' => 'text',
                            'required' => false,
                            'properties' => [
                                'default_value' => 'Daha Fazla Bilgi',
                                'width' => 6,
                                'placeholder' => 'Buton metnini giriniz'
                            ]
                        ],
                        [
                            'name' => 'widget_button_url',
                            'label' => 'Buton URL',
                            'type' => 'text',
                            'required' => false,
                            'properties' => [
                                'default_value' => '#',
                                'width' => 6,
                                'placeholder' => 'Buton URL\'sini giriniz'
                            ]
                        ],
                        [
                            'name' => 'widget_unique_id',
                            'label' => 'Benzersiz ID',
                            'type' => 'text',
                            'required' => false,
                            'system' => true,
                            'hidden' => true,
                            'properties' => [
                                'width' => 12
                            ]
                        ]
                    ]
                ]);
            }
            return $existingWidget;
        }
    }
    
    private function createCentralHeroExample($widget)
    {
        $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
        
        if ($existingWidgets->count() >= 1) {
            return;
        }
        
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widget->id,
            'settings' => [
                'widget_unique_id' => (string) Str::uuid(),
                'widget_title' => 'Merkezi Demo Hero',
                'widget_hero_title' => 'Merkezi Sistem Hero Başlığı',
                'widget_hero_subtitle' => 'Bu merkezi sistem için bir demo alt başlığıdır.',
                'widget_hero_description' => 'Merkezi sistemdeki tüm tenantlar için örnek bir hero açıklaması.',
                'widget_button_text' => 'Başla',
                'widget_button_url' => '/demo-start'
            ],
            'order' => 0,
            'is_active' => true
        ]);
        
        Log::info('Central veritabanında demo hero oluşturuldu.');
    }
    
    private function createHeroForTenants($widget)
    {
        $tenants = Tenant::where('central', false)->get();
        
        if ($tenants->isEmpty()) {
            Log::info("Hiç gerçek tenant bulunamadı.");
            return;
        }
        
        foreach ($tenants as $tenant) {
            try {
                $tenantCacheKey = self::$runKey . '_tenant_' . $tenant->id;
                
                if (Cache::has($tenantCacheKey)) {
                    Log::info("Tenant {$tenant->id} için hero zaten oluşturulmuş, atlanıyor...");
                    continue;
                }
                
                $tenant->run(function () use ($widget, $tenant, $tenantCacheKey) {
                    
                    $existingWidgets = TenantWidget::where('widget_id', $widget->id)->get();
                    
                    if ($existingWidgets->count() > 1) {
                        $firstWidgetId = $existingWidgets->first()->id;
                        
                        foreach ($existingWidgets as $existingWidget) {
                            if ($existingWidget->id != $firstWidgetId) {
                                WidgetItem::where('tenant_widget_id', $existingWidget->id)->delete();
                                $existingWidget->delete();
                            }
                        }
                    }
                    
                    if ($existingWidgets->count() >= 1) {
                        Log::info("Tenant {$tenant->id} için hero widget zaten var, atlanıyor...");
                        Cache::put($tenantCacheKey, true, 600);
                        return;
                    }
                    
                    $tenantWidget = TenantWidget::create([
                        'widget_id' => $widget->id,
                        'settings' => [
                            'widget_unique_id' => (string) Str::uuid(),
                            'widget_title' => $tenant->name . ' Hero Alanı',
                            'widget_hero_title' => $tenant->name . ' Hoş Geldiniz!',
                            'widget_hero_subtitle' => 'Size özel içeriklerimizle tanışın.',
                            'widget_hero_description' => $tenant->name . ' için özel olarak hazırlanmış bu alanda en yeni duyurularımızı ve hizmetlerimizi bulabilirsiniz.',
                            'widget_button_text' => 'Hizmetlerimiz',
                            'widget_button_url' => '/hizmetler'
                        ],
                        'order' => 0,
                        'is_active' => true
                    ]);
                    
                    Log::info("Tenant {$tenant->id} için hero başarıyla oluşturuldu.");
                    
                    Cache::put($tenantCacheKey, true, 600);
                });
            } catch (\Exception $e) {
                Log::error("Tenant {$tenant->id} için hero oluşturma hatası: " . $e->getMessage());
            }
        }
    }
}