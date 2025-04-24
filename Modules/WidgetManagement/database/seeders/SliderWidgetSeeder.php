<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\WidgetCategory;
use Modules\WidgetManagement\App\Models\TenantWidget;
use Modules\WidgetManagement\App\Models\WidgetItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SliderWidgetSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, SliderWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, SliderWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }
        
        Log::info('SliderWidgetSeeder merkezi veritabanında çalışıyor...');
        
        try {
            if (!Widget::where('slug', 'slider')->exists()) {
                // Slider kategorisini bul
                $category = WidgetCategory::where('slug', 'slider-bilesenleri')->first();
                
                if (!$category) {
                    Log::warning('Slider kategorisi bulunamadı, önce WidgetCategorySeeder çalıştırılmalı.');
                    if ($this->command) {
                        $this->command->info('Slider kategorisi bulunamadı, önce WidgetCategorySeeder çalıştırılmalı.');
                    }
                    return;
                }
                
                $widget = Widget::create([
                    'widget_category_id' => $category->widget_category_id,
                    'name' => 'Slider',
                    'slug' => 'slider',
                    'description' => 'Varsayılan yapıdaki temel slayt bileşeni',
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

                        <div class="carousel-inner" style="height:{{height}}px;">
                            {{#each items}}
                            {{#if is_active}}
                            <div class="carousel-item {{#if @first}}active{{/if}}">
                                <img src="{{image}}" class="d-block w-100" alt="{{title}}" style="object-fit:cover; height:100%;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>{{title}}</h5>
                                    <p>{{description}}</p>
                                    {{#if button_text}}
                                    <a href="{{button_url}}" class="btn btn-primary">{{button_text}}</a>
                                    {{/if}}
                                </div>
                            </div>
                            {{/if}}
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
                    'content_css' => '',
                    'content_js' => '
                    document.addEventListener("DOMContentLoaded", function() {
                        new bootstrap.Carousel(document.getElementById("slider-{{unique_id}}"), {
                            interval: {{interval}},
                            wrap: true
                        });
                    });
                    ',
                    'has_items' => true,
                    'item_schema' => [
                        ['name' => 'title', 'label' => 'Slayt Başlığı', 'type' => 'text', 'required' => true],
                        ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea'],
                        ['name' => 'image', 'label' => 'Resim', 'type' => 'image', 'required' => true],
                        ['name' => 'button_text', 'label' => 'Buton Metni', 'type' => 'text'],
                        ['name' => 'button_url', 'label' => 'Buton URL', 'type' => 'url'],
                        ['name' => 'is_active', 'label' => 'Aktif mi?', 'type' => 'checkbox'],
                        ['name' => 'unique_id', 'label' => 'Benzersiz ID', 'type' => 'text', 'system' => true],
                    ],
                    'settings_schema' => [
                        ['name' => 'unique_id', 'label' => 'Benzersiz ID', 'type' => 'text', 'system' => true],
                        ['name' => 'height', 'label' => 'Yükseklik (px)', 'type' => 'number', 'required' => true],
                        ['name' => 'interval', 'label' => 'Geçiş Süresi (ms)', 'type' => 'number', 'required' => true],
                        ['name' => 'show_indicators', 'label' => 'Göstergeleri Göster', 'type' => 'checkbox'],
                        ['name' => 'show_controls', 'label' => 'Kontrolleri Göster', 'type' => 'checkbox'],
                    ],
                    'is_active' => true,
                    'is_core' => true
                ]);
                
                Log::info('Slider widget başarıyla oluşturuldu. ID: ' . $widget->id);
                if ($this->command) {
                    $this->command->info('Slider widget başarıyla oluşturuldu.');
                }
            } else {
                Log::info('Slider widget zaten mevcut, işlem atlanıyor...');
                if ($this->command) {
                    $this->command->info('Slider widget zaten mevcut, işlem atlanıyor...');
                }
            }
        } catch (\Exception $e) {
            Log::error('SliderWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('SliderWidgetSeeder hatası: ' . $e->getMessage());
            }
        }
    }
}