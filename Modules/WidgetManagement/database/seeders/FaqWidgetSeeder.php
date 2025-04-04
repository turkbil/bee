<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Str;

class FaqWidgetSeeder extends Seeder
{
    public function run()
    {
        // Önce kontrol et, eğer bu slug ile widget varsa oluşturma
        if (!Widget::where('slug', 'faq')->exists()) {
            // FAQ Widget
            $widget = Widget::create([
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
                                                {{title}}
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
                        'name' => 'title',
                        'label' => 'Soru',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ],
                    [
                        'name' => 'answer',
                        'label' => 'Cevap',
                        'type' => 'textarea',
                        'required' => true
                    ],
                    [
                        'name' => 'is_active',
                        'label' => 'Aktif',
                        'type' => 'checkbox',
                        'required' => false,
                        'system' => true
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true
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
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true
                    ]
                ],
                'is_active' => true,
                'is_core' => true
            ]);

            // Örnek veriler için TenantWidget oluştur
            $tenantWidget = TenantWidget::create([
                'widget_id' => $widget->id,
                'settings' => [
                    'unique_id' => (string) Str::uuid(),
                    'title' => 'Sıkça Sorulan Sorular',
                    'subtitle' => 'Size yardımcı olabilecek bilgileri derledik',
                    'show_title' => true,
                    'background_color' => '#f8f9fa',
                    'title_color' => '#212529',
                    'subtitle_color' => '#6c757d',
                    'button_bg' => '#007bff',
                    'button_color' => '#ffffff',
                    'button_active_bg' => '#0056b3',
                    'button_active_color' => '#ffffff'
                ],
                'position' => 'bottom',
                'page_id' => null,
                'module' => null
            ]);

            // Örnek FAQ item'ları
            $items = [
                [
                    'title' => 'Hizmetleriniz hangi illerde sunulmaktadır?',
                    'answer' => 'Şu anda İstanbul, Ankara, İzmir ve Bursa olmak üzere 4 büyük ilimizde hizmet vermekteyiz. Yakın zamanda diğer illere de yayılmayı planlıyoruz.',
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ],
                [
                    'title' => 'Fiyatlandırma nasıl yapılmaktadır?',
                    'answer' => 'Fiyatlandırmamız proje büyüklüğüne, kullanım alanına ve müşteri ihtiyaçlarına göre değişkenlik göstermektedir. Detaylı bilgi için müşteri temsilcilerimizle iletişime geçebilirsiniz.',
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ],
                [
                    'title' => 'Teslimat süreciniz ne kadar sürüyor?',
                    'answer' => 'Standart teslimat süresi 5-7 iş günüdür. Özel projeler ve büyük ölçekli siparişlerde bu süre biraz daha uzayabilir. Acil durumlarda hızlandırılmış teslimat seçeneğimiz de mevcuttur.',
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ],
                [
                    'title' => 'Ürünlerinize garanti veriyor musunuz?',
                    'answer' => 'Evet, tüm ürünlerimiz 2 yıl üretici garantisi kapsamındadır. Herhangi bir üründe garanti kapsamında arıza olması durumunda ücretsiz olarak değişim veya tamir yapılmaktadır.',
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ],
                [
                    'title' => 'Müşteri desteği nasıl sağlanmaktadır?',
                    'answer' => 'Müşteri destek hattımız hafta içi 09:00-18:00 saatleri arasında hizmet vermektedir. Ayrıca web sitemiz üzerinden 7/24 destek talebi oluşturabilir, e-posta yoluyla da iletişime geçebilirsiniz.',
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ]
            ];

            // Item'ları oluştur
            foreach ($items as $index => $item) {
                WidgetItem::create([
                    'tenant_widget_id' => $tenantWidget->id,
                    'content' => $item,
                    'order' => $index + 1
                ]);
            }
        }
    }
}