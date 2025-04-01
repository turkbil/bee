<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;

class FaqWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'faq')->exists()) {
            // FAQ Widget
            Widget::create([
                'name' => 'SSS (FAQ)',
                'slug' => 'faq',
                'description' => 'Sıkça Sorulan Sorular bölümü',
                'type' => 'dynamic',
                'content_html' => '
                <div class="faq-widget">
                    <div class="container">
                        {{#if show_title}}
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <h2 class="section-title">{{title}}</h2>
                                {{#if subtitle}}
                                <p class="section-subtitle">{{subtitle}}</p>
                                {{/if}}
                            </div>
                        </div>
                        {{/if}}
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="accordion" id="faqAccordion-{{id}}">
                                    {{#each items}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{@index}}">
                                            <button class="accordion-button {{#if @index}}collapsed{{/if}}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{@index}}" aria-expanded="{{#if @first}}true{{else}}false{{/if}}" aria-controls="collapse{{@index}}">
                                                {{question}}
                                            </button>
                                        </h2>
                                        <div id="collapse{{@index}}" class="accordion-collapse collapse {{#if @first}}show{{/if}}" aria-labelledby="heading{{@index}}" data-bs-parent="#faqAccordion-{{id}}">
                                            <div class="accordion-body">
                                                {{answer}}
                                            </div>
                                        </div>
                                    </div>
                                    {{/each}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ',
                'content_css' => '
                .faq-widget {
                    padding: 50px 0;
                    background-color: {{background_color}};
                }
                .section-title {
                    color: {{title_color}};
                    margin-bottom: 15px;
                }
                .section-subtitle {
                    color: {{subtitle_color}};
                    margin-bottom: 30px;
                }
                .accordion-button:not(.collapsed) {
                    background-color: {{button_active_bg}};
                    color: {{button_active_color}};
                }
                .accordion-button {
                    background-color: {{button_bg}};
                    color: {{button_color}};
                }
                ',
                'has_items' => true,
                'item_schema' => [
                    [
                        'name' => 'question',
                        'label' => 'Soru',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'answer',
                        'label' => 'Cevap',
                        'type' => 'textarea',
                        'required' => true
                    ]
                ],
                'settings_schema' => [
                    [
                        'name' => 'id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'subtitle',
                        'label' => 'Alt Başlık',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'show_title',
                        'label' => 'Başlığı Göster',
                        'type' => 'checkbox',
                        'required' => false
                    ],
                    [
                        'name' => 'background_color',
                        'label' => 'Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'title_color',
                        'label' => 'Başlık Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'subtitle_color',
                        'label' => 'Alt Başlık Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'button_bg',
                        'label' => 'Buton Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'button_color',
                        'label' => 'Buton Metin Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'button_active_bg',
                        'label' => 'Aktif Buton Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false
                    ],
                    [
                        'name' => 'button_active_color',
                        'label' => 'Aktif Buton Metin Rengi',
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