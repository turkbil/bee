<?php
namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Önce "Tema Ayarları" grubu oluşturalım
        $themeSettingsGroupId = DB::table('settings_groups')->insertGetId([
            'name' => 'Tema Ayarları',
            'slug' => 'tema-ayarlari',
            'parent_id' => 5, // Site grubunun altına ekleyelim
            'icon' => 'fas fa-palette',
            'is_active' => true,
            'layout' => $this->getThemeSettingsLayout(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tema Ayarları - Temel Renkler
        $settings = [
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Ana Renk',
                'key' => 'theme_primary_color',
                'type' => 'color',
                'default_value' => '#0ea5e9',
                'sort_order' => 1
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'İkincil Renk',
                'key' => 'theme_secondary_color',
                'type' => 'color',
                'default_value' => '#64748b',
                'sort_order' => 2
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Vurgu Rengi',
                'key' => 'theme_accent_color',
                'type' => 'color',
                'default_value' => '#8b5cf6',
                'sort_order' => 3
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Arkaplan Rengi',
                'key' => 'theme_background_color',
                'type' => 'color',
                'default_value' => '#ffffff',
                'sort_order' => 4
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Metin Rengi',
                'key' => 'theme_text_color',
                'type' => 'color',
                'default_value' => '#333333',
                'sort_order' => 5
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Başarı Rengi',
                'key' => 'theme_success_color',
                'type' => 'color',
                'default_value' => '#10b981',
                'sort_order' => 6
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Uyarı Rengi',
                'key' => 'theme_warning_color',
                'type' => 'color',
                'default_value' => '#f59e0b',
                'sort_order' => 7
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Hata Rengi',
                'key' => 'theme_danger_color',
                'type' => 'color',
                'default_value' => '#ef4444',
                'sort_order' => 8
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Bilgi Rengi',
                'key' => 'theme_info_color',
                'type' => 'color',
                'default_value' => '#3b82f6',
                'sort_order' => 9
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Kart Arkaplan Rengi',
                'key' => 'theme_card_background_color',
                'type' => 'color',
                'default_value' => '#ffffff',
                'sort_order' => 10
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Gölge Rengi',
                'key' => 'theme_shadow_color',
                'type' => 'color',
                'default_value' => 'rgba(0, 0, 0, 0.1)',
                'sort_order' => 11
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Kenar Rengi',
                'key' => 'theme_border_color',
                'type' => 'color',
                'default_value' => '#e5e7eb',
                'sort_order' => 12
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'group_id' => $setting['group_id'],
                'label' => $setting['label'],
                'key' => $setting['key'],
                'type' => $setting['type'],
                'options' => $setting['options'] ?? null,
                'default_value' => $setting['default_value'],
                'sort_order' => $setting['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getThemeSettingsLayout(): string
    {
        $layout = [
            'title' => 'Tema Ayarları Formu',
            'elements' => [
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Temel Renkler',
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
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Ana Renk',
                                        'name' => 'theme_primary_color',
                                        'help_text' => 'Sitenin ana rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#0ea5e9',
                                        'setting_id' => 6 // Bu ID'ler dinamik olarak atanacak
                                    ]
                                ],
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Vurgu Rengi',
                                        'name' => 'theme_accent_color',
                                        'help_text' => 'Vurgu rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#8b5cf6',
                                        'setting_id' => 8
                                    ]
                                ],
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Metin Rengi',
                                        'name' => 'theme_text_color',
                                        'help_text' => 'Ana metin rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#333333',
                                        'setting_id' => 10
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'İkincil Renk',
                                        'name' => 'theme_secondary_color',
                                        'help_text' => 'İkincil renk',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#64748b',
                                        'setting_id' => 7
                                    ]
                                ],
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Arkaplan Rengi',
                                        'name' => 'theme_background_color',
                                        'help_text' => 'Sayfa arkaplan rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#ffffff',
                                        'setting_id' => 9
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Durum Renkleri',
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
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Başarı Rengi',
                                        'name' => 'theme_success_color',
                                        'help_text' => 'Başarı durumu rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#10b981',
                                        'setting_id' => 11
                                    ]
                                ],
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Hata Rengi',
                                        'name' => 'theme_danger_color',
                                        'help_text' => 'Hata durumu rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#ef4444',
                                        'setting_id' => 13
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Uyarı Rengi',
                                        'name' => 'theme_warning_color',
                                        'help_text' => 'Uyarı durumu rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#f59e0b',
                                        'setting_id' => 12
                                    ]
                                ],
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Bilgi Rengi',
                                        'name' => 'theme_info_color',
                                        'help_text' => 'Bilgi durumu rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#3b82f6',
                                        'setting_id' => 14
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Diğer Arayüz Renkleri',
                        'size' => 'h3',
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
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Kart Arkaplan Rengi',
                                        'name' => 'theme_card_background_color',
                                        'help_text' => 'Kartların arkaplan rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#ffffff',
                                        'setting_id' => 15
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Gölge Rengi',
                                        'name' => 'theme_shadow_color',
                                        'help_text' => 'Gölge rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => 'rgba(0, 0, 0, 0.1)',
                                        'setting_id' => 16
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'color',
                                    'properties' => [
                                        'label' => 'Kenar Rengi',
                                        'name' => 'theme_border_color',
                                        'help_text' => 'Kenar çizgisi rengi',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '#e5e7eb',
                                        'setting_id' => 17
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