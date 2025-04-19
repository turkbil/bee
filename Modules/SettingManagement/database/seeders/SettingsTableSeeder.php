<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'group_id' => 6,
                'label' => 'Site Başlığı',
                'key' => 'site_title',
                'type' => 'text',
                'default_value' => 'Türk Bilişim',
                'sort_order' => 1
            ],
            [
                'group_id' => 6,
                'label' => 'Site Logo',
                'key' => 'site_logo',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 2
            ],
            [
                'group_id' => 6,
                'label' => 'Favicon',
                'key' => 'site_favicon',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 3
            ],
            [
                'group_id' => 6,
                'label' => 'Ana E-posta Adresi',
                'key' => 'site_email',
                'type' => 'text',
                'default_value' => 'info@turkbilisim.com.tr',
                'sort_order' => 4
            ],
            [
                'group_id' => 6,
                'label' => 'Google Analytics Kodu',
                'key' => 'site_google_analytics_code',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 5
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
