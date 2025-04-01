<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;

class HeroBannerWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'hero-banner')->exists()) {
            // Hero Banner Widget
            Widget::create([
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
                'has_items' => false,
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
        }
    }
}