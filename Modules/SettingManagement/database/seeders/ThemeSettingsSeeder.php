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
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Tema Kimliği',
                'key' => 'theme_id',
                'type' => 'select',
                'options' => json_encode([
                    'options' => [
                        ['value' => '1', 'label' => 'Blank Tema'],
                    ]
                ]),
                'default_value' => '1',
                'sort_order' => 13
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Koyu Mod Aktif',
                'key' => 'theme_dark_mode_enabled',
                'type' => 'boolean',
                'default_value' => 'false',
                'sort_order' => 14
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Köşe Yuvarlaklığı',
                'key' => 'theme_border_radius',
                'type' => 'text',
                'default_value' => '0.375rem',
                'sort_order' => 15
            ],
            [
                'group_id' => $themeSettingsGroupId,
                'label' => 'Font Ailesi',
                'key' => 'theme_font_family',
                'type' => 'text',
                'default_value' => 'Inter, system-ui, sans-serif',
                'sort_order' => 16
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
}