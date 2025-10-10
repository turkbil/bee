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
                'label' => 'Site - Firma - Kurum Adı',
                'key' => 'site_title',
                'type' => 'text',
                'default_value' => 'Türk Bilişim',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'is_required' => true,
                'help_text' => 'Sitenizin genel başlığı, tarayıcı başlıklarında ve meta etiketlerinde kullanılır.'
            ],
            [
                'group_id' => 6,
                'label' => 'Site Logo',
                'key' => 'site_logo',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Site logonuz, tercihen PNG veya SVG formatında şeffaf arka planlı bir dosya.'
            ],
            [
                'group_id' => 6,
                'label' => 'Favicon',
                'key' => 'site_favicon',
                'type' => 'file',
                'default_value' => 'favicon.ico',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'is_required' => false,
                'help_text' => 'Favicon, tarayıcı sekmesinde görünen küçük simgedir. Tercihen 32x32 veya 16x16 boyutlarında PNG, ICO formatında olmalıdır.'
            ],
            [
                'group_id' => 6,
                'label' => 'Ana E-posta Adresi',
                'key' => 'site_email',
                'type' => 'text',
                'default_value' => 'info@turkbilisim.com.tr',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'is_required' => true,
                'help_text' => 'İletişim formlarından ve sistem bildirimlerinden gelen e-postaların gönderileceği adres.'
            ],
            [
                'group_id' => 6,
                'label' => 'Google Analytics Kodu',
                'key' => 'site_google_analytics_code',
                'type' => 'text',
                'default_value' => null,
                'sort_order' => 5,
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
