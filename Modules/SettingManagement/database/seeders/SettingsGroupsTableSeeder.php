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
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Varsayılan Yazar',
                                        'name' => 'seo_default_author',
                                        'placeholder' => 'Varsayılan yazar adını giriniz',
                                        'help_text' => 'Sayfalarda author meta tag için kullanılacak varsayılan yazar adı',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => 'Nurullah Okatan',
                                        'setting_id' => 1
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
                                        'label' => 'Twitter Site Hesabı',
                                        'name' => 'seo_default_twitter_site',
                                        'placeholder' => '@sitenizinhesabi',
                                        'help_text' => 'Twitter Cards için site hesabı (@ işareti ile başlamalıdır)',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '@turkbilisim',
                                        'setting_id' => 2
                                    ]
                                ],
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Twitter Creator Hesabı',
                                        'name' => 'seo_default_twitter_creator',
                                        'placeholder' => '@creatorhesabi',
                                        'help_text' => 'Twitter Cards için creator hesabı (@ işareti ile başlamalıdır)',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '@nurullahokatan',
                                        'setting_id' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
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
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Google Analytics Kodu',
                        'name' => 'site_google_analytics_code',
                        'placeholder' => 'G-XXXXXXXXXX',
                        'help_text' => 'Google Analytics izleme kodu (Örnek: G-XXXXXXXXXX)',
                        'width' => 12,
                        'required' => false,
                        'default_value' => '',
                        'setting_id' => 5
                    ]
                ]
            ]
        ];

        return json_encode($layout);
    }
}
