<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Str;

class HeroBannerWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'hero-banner')->exists()) {
            // Hero Banner Widget
            $widget = Widget::create([
                'name' => 'Hero Banner',
                'slug' => 'hero-banner',
                'description' => 'Ana sayfa hero bölümü için büyük banner ve metin alanı',
                'type' => 'static',
                'content_html' => '
                <div class="hero-banner" style="background-color: {{background_color}}; color: {{text_color}};">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="display-4 fw-bold">{{title}}</h1>
                                <p class="lead">{{subtitle}}</p>
                                {{#if button_text}}
                                <a href="{{button_url}}" class="btn btn-primary btn-lg">{{button_text}}</a>
                                {{/if}}
                            </div>
                            <div class="col-md-6">
                                {{#if image_url}}
                                <img src="{{image_url}}" alt="{{title}}" class="img-fluid rounded">
                                {{/if}}
                            </div>
                        </div>
                    </div>
                </div>
                ',
                'content_css' => '
                .hero-banner {
                    padding: 60px 0;
                    background-size: cover;
                    background-position: center;
                    position: relative;
                }
                ',
                'has_items' => false, // Statik olduğu için false
                'settings_schema' => [
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'subtitle',
                        'label' => 'Alt Başlık',
                        'type' => 'textarea',
                        'required' => false
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
                    ],
                    [
                        'name' => 'image_url',
                        'label' => 'Görsel URL',
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
                        'name' => 'text_color',
                        'label' => 'Metin Rengi',
                        'type' => 'color',
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
                    'unique_id' => (string) Str::uuid(),
                    'title' => 'Dijital Dönüşümün Anahtarı Burada',
                    'subtitle' => 'Teknoloji ve inovasyonla geleceğe hazırlanın',
                    'button_text' => 'Hemen Keşfedin',
                    'button_url' => '/hakkimizda',
                    'image_url' => asset('storage/images/hero-banner-sample.jpg'),
                    'background_color' => '#f0f4ff',
                    'text_color' => '#333333'
                ],
                'position' => 'top',
                'page_id' => null,
                'module' => null
            ]);
            
            // Statik bileşen için içerik öğesi oluştur
            WidgetItem::create([
                'tenant_widget_id' => $tenantWidget->id,
                'content' => [
                    'title' => $tenantWidget->settings['title'],
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid(),
                    'content_html' => $widget->content_html
                ],
                'order' => 1
            ]);
        }
    }
}