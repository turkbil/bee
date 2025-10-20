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
                'label' => 'Site Adı',
                'key' => 'site_title',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'is_required' => true,
                'help_text' => 'Sitenizin adı (örn: iXtif, TechStore, vb.)'
            ],
            [
                'group_id' => 6,
                'label' => 'Kurum Adı',
                'key' => 'company_name',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Firma veya kurum adınız (örn: ABC Ltd. Şti., XYZ A.Ş.)'
            ],
            [
                'group_id' => 6,
                'label' => 'Site Sloganı',
                'key' => 'site_slogan',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Sitenizin sloganı veya açıklaması (örn: Türkiye\'nin İstif Pazarı)'
            ],
            [
                'group_id' => 6,
                'label' => 'Site Logo',
                'key' => 'site_logo',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Site logonuz, tercihen PNG veya SVG formatında şeffaf arka planlı bir dosya.'
            ],
            [
                'group_id' => 6,
                'label' => 'Site Logo Kontrast (Beyaz Tonlar)',
                'key' => 'site_logo_2',
                'type' => 'image',
                'default_value' => null,
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Dark mode için beyaz/açık tonlu logo. Boş bırakılırsa site_logo kullanılır.'
            ],
            [
                'group_id' => 6,
                'label' => 'Favicon',
                'key' => 'site_favicon',
                'type' => 'favicon',
                'default_value' => 'favicon.ico',
                'sort_order' => 6,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Favicon, tarayıcı sekmesinde görünen küçük simgedir. Sadece ICO ve PNG formatlarında 32x32 veya 16x16 boyutlarında olmalıdır.'
            ],
            [
                'group_id' => 8,
                'label' => 'Google Analytics Kodu',
                'key' => 'site_google_analytics_code',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Google Analytics takip kodunuz (örn: G-XXXXXXXXXX). Boş bırakılırsa analitik takibi devre dışı kalır.'
            ],
        ];

        foreach ($settings as $setting) {
            $existing = DB::table('settings')->where('key', $setting['key'])->first();

            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'group_id' => $setting['group_id'],
                    'label' => $setting['label'],
                    'type' => $setting['type'],
                    'options' => $setting['options'] ?? null,
                    'default_value' => $setting['default_value'],
                    'sort_order' => $setting['sort_order'],
                    'is_active' => $setting['is_active'],
                    'is_system' => $setting['is_system'],
                    'is_required' => $setting['is_required'],
                    'help_text' => $setting['help_text'],
                    'created_at' => $existing->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
