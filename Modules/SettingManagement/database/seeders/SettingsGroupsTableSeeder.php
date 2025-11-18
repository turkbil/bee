<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsGroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['id' => 1, 'name' => 'Genel Sistem', 'parent_id' => null, 'icon' => 'fas fa-cogs'],
            ['id' => 2, 'name' => 'Tenant', 'parent_id' => null, 'icon' => 'fas fa-building'],
            ['id' => 3, 'name' => 'Kullanıcı', 'parent_id' => null, 'icon' => 'fas fa-users'],
            ['id' => 4, 'name' => 'Modül', 'parent_id' => null, 'icon' => 'fas fa-puzzle-piece'],
            ['id' => 5, 'name' => 'Site', 'parent_id' => null, 'icon' => 'fas fa-globe'],
            ['id' => 6, 'name' => 'Site Ayarları', 'parent_id' => 1, 'icon' => 'fas fa-sliders-h', 'prefix' => 'site', 'layout' => $this->getSiteAyarlariLayout()],
            ['id' => 7, 'name' => 'Tema', 'parent_id' => 5, 'icon' => 'fas fa-palette', 'prefix' => 'theme'],
            ['id' => 8, 'name' => 'SEO Ayarları', 'parent_id' => 1, 'icon' => 'fas fa-search', 'prefix' => 'seo', 'layout' => $this->getSeoAyarlariLayout()]
        ];
        
        foreach ($groups as $group) {
            $layout = $group['layout'] ?? null;
            unset($group['layout']);

            $existing = DB::table('settings_groups')->where('id', $group['id'])->first();

            DB::table('settings_groups')->updateOrInsert(
                ['id' => $group['id']],
                [
                    'name' => $group['name'],
                    'slug' => Str::slug($group['name']),
                    'parent_id' => $group['parent_id'],
                    'icon' => $group['icon'],
                    'prefix' => $group['prefix'] ?? null,
                    'is_active' => true,
                    'layout' => $layout,
                    'created_at' => $existing->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
    
    private function getSiteAyarlariLayout(): string
    {
        $layout = [
            'title' => 'Site Ayarları Formu',
            'elements' => [
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Site Adı',
                                        'name' => 'site_title',
                                        'placeholder' => 'Sitenizin adını giriniz',
                                        'help_text' => 'Sitenizin adı (örn: iXtif, TechStore, vb.)',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '',
                                        'setting_id' => 1
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Kurum Adı',
                                        'name' => 'company_name',
                                        'placeholder' => 'Firma veya kurum adınızı giriniz',
                                        'help_text' => 'Firma veya kurum adınız (örn: ABC Ltd. Şti., XYZ A.Ş.)',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 90
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Site Sloganı',
                                        'name' => 'site_slogan',
                                        'placeholder' => 'Sitenizin sloganını giriniz',
                                        'help_text' => 'Sitenizin sloganı veya açıklaması (örn: Türkiye\'nin İstif Pazarı)',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 91
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'file',
                                    'properties' => [
                                        'label' => 'Site Logo',
                                        'name' => 'site_logo',
                                        'help_text' => 'Önerilen boyut: 200x60 piksel',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 2
                                    ]
                                ],
                                [
                                    'type' => 'favicon',
                                    'properties' => [
                                        'label' => 'Favicon',
                                        'name' => 'site_favicon',
                                        'help_text' => 'Önerilen boyut: 32x32 piksel, Sadece ICO ve PNG formatları desteklenir',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 3
                                    ]
                                ],
                                [
                                    'type' => 'image',
                                    'properties' => [
                                        'label' => 'Site Logo Kontrast (Beyaz Tonlar)',
                                        'name' => 'site_logo_2',
                                        'help_text' => 'Koyu arka planlarda kullanılacak beyaz/açık tonlu logo',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 55
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        return json_encode($layout);
    }
    
    private function getSeoAyarlariLayout(): string
    {
        $layout = [
            'title' => 'SEO Ayarları Formu',
            'elements' => [
                // ========================================
                // Meta Tag Ayarları
                // ========================================
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Meta Tag Ayarları',
                        'size' => 'h3',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        // Sol Kolon
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar',
                                        'name' => 'seo_default_author',
                                        'placeholder' => 'Yazar adı',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'url',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar Web Sitesi',
                                        'name' => 'seo_default_author_url',
                                        'placeholder' => 'https://example.com/author',
                                        'help_text' => 'Yazarın web sitesi veya profil sayfası',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar Ünvanı',
                                        'name' => 'seo_default_author_title',
                                        'placeholder' => 'Örn: CEO, Endüstriyel Ekipman Uzmanı',
                                        'help_text' => 'Yazarın ünvanı veya mesleği (Google E-E-A-T için)',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'textarea',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar Biyografisi',
                                        'name' => 'seo_default_author_bio',
                                        'placeholder' => 'Örn: 15 yıldır forklift sektöründe uzman...',
                                        'help_text' => 'Yazarın kısa özgeçmişi (Google E-E-A-T için)',
                                        'rows' => 3,
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        // Sağ Kolon
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Twitter Site Hesabı',
                                        'name' => 'seo_default_twitter_site',
                                        'placeholder' => '@site',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Twitter Creator Hesabı',
                                        'name' => 'seo_default_twitter_creator',
                                        'placeholder' => '@creator',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'image',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar Görseli',
                                        'name' => 'seo_default_author_image',
                                        'help_text' => 'Yazarın profil fotoğrafı',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // ========================================
                // Analitik Ayarları
                // ========================================
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Analitik Ayarları',
                        'size' => 'h3',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Analytics Kodu',
                                        'name' => 'seo_site_google_analytics_code',
                                        'placeholder' => 'G-XXXXXXXXXX',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'textarea',
                                    'properties' => [
                                        'label' => 'Yandex Metrica',
                                        'name' => 'seo_site_yandex_metrica',
                                        'placeholder' => 'Yandex Metrica kodu',
                                        'rows' => 3,
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // ========================================
                // Divider
                // ========================================
                [
                    'type' => 'divider',
                    'properties' => [
                        'width' => 12
                    ]
                ],

                // ========================================
                // Dijital Pazarlama Platformları
                // ========================================
                [
                    'type' => 'section',
                    'properties' => [
                        'content' => '🎯 Dijital Pazarlama Platformları',
                        'width' => 12
                    ]
                ],

                // Google Tag Manager
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Google Tag Manager (Ana Yönetim)',
                        'size' => 'h4',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 12]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Tag Manager Container ID',
                                        'name' => 'seo_google_tag_manager_id',
                                        'placeholder' => 'GTM-XXXXXXX',
                                        'help_text' => 'GTM Container ID (örn: GTM-XXXXXXX). Tüm tracking kodlarını GTM üzerinden yönetin.',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // Google Ads
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Google Ads - Dönüşüm Takibi',
                        'size' => 'h4',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Ads Conversion ID',
                                        'name' => 'seo_google_ads_conversion_id',
                                        'placeholder' => '17679808859',
                                        'help_text' => 'Google Ads Conversion Tracking ID (örn: AW-XXXXXXXXXX)',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Ads Conversion Label',
                                        'name' => 'seo_google_ads_conversion_label',
                                        'placeholder' => 'JgaPCLyV8LMbENvyse5B',
                                        'help_text' => 'Google Ads Conversion Label (örn: JgaPCLyV8LMbENvyse5B)',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Ads - Form Gönderme Conversion Label',
                                        'name' => 'seo_google_ads_form_conversion_label',
                                        'placeholder' => 'Form Label (opsiyonel)',
                                        'help_text' => 'Form gönderme conversion label (örn: AbC-123xyz)',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Google Ads - Telefon Tıklama Conversion Label',
                                        'name' => 'seo_google_ads_phone_conversion_label',
                                        'placeholder' => 'Phone Label (opsiyonel)',
                                        'help_text' => 'Telefon tıklama conversion label',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // Sosyal Medya & Diğer Platformlar
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Sosyal Medya & Diğer Platformlar',
                        'size' => 'h4',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 4],
                            ['index' => 2, 'width' => 4],
                            ['index' => 3, 'width' => 4]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Facebook Pixel ID',
                                        'name' => 'seo_facebook_pixel_id',
                                        'placeholder' => '123456789012345',
                                        'help_text' => 'Facebook (Meta) Pixel ID (örn: 123456789012345). Facebook/Instagram reklamları için gerekli.',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'LinkedIn Partner ID',
                                        'name' => 'seo_linkedin_partner_id',
                                        'placeholder' => '123456',
                                        'help_text' => 'LinkedIn Insight Tag Partner ID (örn: 123456). B2B endüstriyel ürünler için önemli!',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Microsoft Clarity Project ID',
                                        'name' => 'seo_microsoft_clarity_id',
                                        'placeholder' => 'abcd1234',
                                        'help_text' => 'Microsoft Clarity Project ID (örn: abcd1234). ÜCRETSIZ heatmap ve session replay!',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Twitter (X) Pixel ID',
                                        'name' => 'seo_twitter_pixel_id',
                                        'placeholder' => 'o1234 (opsiyonel)',
                                        'help_text' => 'Twitter (X) Pixel ID. Twitter reklamları için (opsiyonel).',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'TikTok Pixel ID',
                                        'name' => 'seo_tiktok_pixel_id',
                                        'placeholder' => 'C1234567890ABCDEF (opsiyonel)',
                                        'help_text' => 'TikTok Pixel ID. TikTok reklamları için (opsiyonel).',
                                        'width' => 12,
                                        'required' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($layout);
    }
}
