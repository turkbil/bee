<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Site Ayarları 
            [
                'group_id' => 6,
                'label' => 'Site Başlığı',
                'key' => 'site_title',
                'type' => 'text',
                'default_value' => 'My Site',
                'sort_order' => 1,
            ],
            [
                'group_id' => 6,
                'label' => 'Site Açıklaması',
                'key' => 'site_description', 
                'type' => 'textarea',
                'default_value' => 'Site açıklaması buraya gelecek',
                'sort_order' => 2,
            ],
            [
                'group_id' => 6,
                'label' => 'Site Logo',
                'key' => 'site_logo',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 3,
            ],
            [
                'group_id' => 6,
                'label' => 'Favicon',
                'key' => 'site_favicon',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 4,
            ],

            // İletişim Bilgileri
            [
                'group_id' => 7,
                'label' => 'Telefon',
                'key' => 'contact_phone',
                'type' => 'text',
                'default_value' => '+90 850 XXX XX XX',
                'sort_order' => 1,
            ],
            [
                'group_id' => 7,
                'label' => 'E-posta',
                'key' => 'contact_email',
                'type' => 'text',
                'default_value' => 'info@example.com',
                'sort_order' => 2,
            ],
            [
                'group_id' => 7,
                'label' => 'Adres',
                'key' => 'contact_address',
                'type' => 'textarea',
                'default_value' => 'İstanbul, Türkiye',
                'sort_order' => 3,
            ],
            [
                'group_id' => 7,
                'label' => 'Google Maps Embed',
                'key' => 'contact_map',
                'type' => 'textarea',
                'default_value' => null,
                'sort_order' => 4,
            ],

            // Sosyal Medya
            [
                'group_id' => 8,
                'label' => 'Facebook',
                'key' => 'social_facebook',
                'type' => 'text',
                'default_value' => 'https://facebook.com/',
                'sort_order' => 1,
            ],
            [
                'group_id' => 8,
                'label' => 'Twitter',
                'key' => 'social_twitter',
                'type' => 'text', 
                'default_value' => 'https://twitter.com/',
                'sort_order' => 2,
            ],
            [
                'group_id' => 8,
                'label' => 'Instagram',
                'key' => 'social_instagram',
                'type' => 'text',
                'default_value' => 'https://instagram.com/',
                'sort_order' => 3,
            ],
            [
                'group_id' => 8,
                'label' => 'LinkedIn',
                'key' => 'social_linkedin',
                'type' => 'text',
                'default_value' => 'https://linkedin.com/',
                'sort_order' => 4,
            ],

            // Default Tema
            [
                'group_id' => 11,
                'label' => 'Tema Rengi',
                'key' => 'theme_color',
                'type' => 'select',
                'options' => json_encode([
                    'light' => 'Açık Tema',
                    'dark' => 'Koyu Tema'
                ]),
                'default_value' => 'light',
                'sort_order' => 1,
            ],

            // Yönetici Ayarları
            [
                'group_id' => 12,
                'label' => 'Panel Teması',
                'key' => 'admin_theme',
                'type' => 'select',
                'options' => json_encode([
                    'light' => 'Aydınlık Mod',
                    'dark' => 'Karanlık Mod'
                ]),
                'default_value' => 'light',
                'sort_order' => 1,
            ],
            [
                'group_id' => 12,
                'label' => 'Panel Genişliği',
                'key' => 'admin_width',
                'type' => 'select',
                'options' => json_encode([
                    'boxed' => 'Dar Tema',
                    'fluid' => 'Geniş Tema'
                ]),
                'default_value' => 'fluid',
                'sort_order' => 2,
            ],
            [
                'group_id' => 12,
                'label' => 'Menü Konumu',
                'key' => 'admin_menu_position',
                'type' => 'select',
                'options' => json_encode([
                    'horizontal' => 'Yatay Menü',
                    'vertical' => 'Dikey Menü'
                ]),
                'default_value' => 'vertical',
                'sort_order' => 3,
            ],
            [
                'group_id' => 12,
                'label' => 'Menü Fixed',
                'key' => 'admin_menu_fixed',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 4,
            ],
            
            // Kullanıcı Ayarları
            [
                'group_id' => 13,
                'label' => 'Kayıt Aktif',
                'key' => 'user_registration_enabled',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 1,
            ],
            [
                'group_id' => 13,
                'label' => 'E-posta Doğrulama',
                'key' => 'user_email_verification',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 2,
            ],
            
            // YENİ EKLENEN ÖRNEKLER GRUBU ALTINDA TÜM INPUT TÜRLERİ
            [
                'group_id' => 14,
                'label' => 'Metin Girişi',
                'key' => 'ornekler_text',
                'type' => 'text',
                'default_value' => 'Örnek metin içeriği',
                'sort_order' => 1,
            ],
            [
                'group_id' => 14,
                'label' => 'Numara Girişi',
                'key' => 'ornekler_number',
                'type' => 'number',
                'default_value' => '42',
                'sort_order' => 2,
            ],
            [
                'group_id' => 14,
                'label' => 'E-posta Girişi',
                'key' => 'ornekler_email',
                'type' => 'email',
                'default_value' => 'ornek@example.com',
                'sort_order' => 3,
            ],
            [
                'group_id' => 14,
                'label' => 'Şifre Girişi',
                'key' => 'ornekler_password',
                'type' => 'password',
                'default_value' => 'gizli123',
                'sort_order' => 4,
            ],
            [
                'group_id' => 14,
                'label' => 'Telefon Numarası',
                'key' => 'ornekler_tel',
                'type' => 'tel',
                'default_value' => '+90 555 123 45 67',
                'sort_order' => 5,
            ],
            [
                'group_id' => 14,
                'label' => 'URL Girişi',
                'key' => 'ornekler_url',
                'type' => 'url',
                'default_value' => 'https://example.com',
                'sort_order' => 6,
            ],
            [
                'group_id' => 14,
                'label' => 'Tarih Seçimi',
                'key' => 'ornekler_date',
                'type' => 'date',
                'default_value' => '2025-01-01',
                'sort_order' => 7,
            ],
            [
                'group_id' => 14,
                'label' => 'Saat Seçimi',
                'key' => 'ornekler_time',
                'type' => 'time',
                'default_value' => '09:00',
                'sort_order' => 8,
            ],
            [
                'group_id' => 14,
                'label' => 'Renk Seçimi',
                'key' => 'ornekler_color',
                'type' => 'color',
                'default_value' => '#3498db',
                'sort_order' => 9,
            ],
            [
                'group_id' => 14,
                'label' => 'Uzun Metin Alanı',
                'key' => 'ornekler_textarea',
                'type' => 'textarea',
                'default_value' => 'Burası çok uzun bir metin alanı örneğidir. Birden fazla satır içerebilir ve kullanıcının daha detaylı içerik girmesine olanak tanır.',
                'sort_order' => 10,
            ],
            [
                'group_id' => 14,
                'label' => 'Onay Kutusu',
                'key' => 'ornekler_checkbox',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 11,
            ],
            [
                'group_id' => 14,
                'label' => 'Seçim Kutusu',
                'key' => 'ornekler_select',
                'type' => 'select',
                'options' => json_encode([
                    'option1' => 'Seçenek 1',
                    'option2' => 'Seçenek 2',
                    'option3' => 'Seçenek 3'
                ]),
                'default_value' => 'option1',
                'sort_order' => 12,
            ],
            [
                'group_id' => 14,
                'label' => 'Dosya Yükleme',
                'key' => 'ornekler_file',
                'type' => 'file',
                'default_value' => null,
                'sort_order' => 13,
            ],
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