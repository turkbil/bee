<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;

class ContactSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // İletişim Bilgileri Grubu Oluştur
        $contactGroup = SettingGroup::updateOrCreate(
            ['slug' => 'iletisim-bilgileri'],
            [
                'parent_id' => 1, // Genel Sistem
                'name' => 'İletişim Bilgileri',
                'description' => 'Telefon, WhatsApp, email, sosyal medya ve adres bilgileri',
                'icon' => 'fa-solid fa-address-book',
                'is_active' => true,
                'layout' => $this->getLayout(),
            ]
        );

        $settings = [
            // 📞 TELEFONLAR
            ['key' => 'contact_phone_1', 'label' => 'Ana Telefon', 'type' => 'text', 'default_value' => '', 'sort_order' => 1],
            ['key' => 'contact_phone_2', 'label' => 'Alternatif Telefon 1', 'type' => 'text', 'default_value' => '', 'sort_order' => 2],
            ['key' => 'contact_phone_3', 'label' => 'Alternatif Telefon 2', 'type' => 'text', 'default_value' => '', 'sort_order' => 3],

            // 💬 WHATSAPP
            ['key' => 'contact_whatsapp_1', 'label' => 'Ana WhatsApp', 'type' => 'text', 'default_value' => '', 'sort_order' => 10],
            ['key' => 'contact_whatsapp_2', 'label' => 'Destek WhatsApp', 'type' => 'text', 'default_value' => '', 'sort_order' => 11],
            ['key' => 'contact_whatsapp_3', 'label' => 'Satış WhatsApp', 'type' => 'text', 'default_value' => '', 'sort_order' => 12],

            // 📧 E-POSTALAR
            ['key' => 'contact_email_1', 'label' => 'Genel E-posta', 'type' => 'email', 'default_value' => '', 'sort_order' => 20],
            ['key' => 'contact_email_2', 'label' => 'Destek E-posta', 'type' => 'email', 'default_value' => '', 'sort_order' => 21],
            ['key' => 'contact_email_3', 'label' => 'Satış E-posta', 'type' => 'email', 'default_value' => '', 'sort_order' => 22],

            // 🌐 SOSYAL MEDYA
            ['key' => 'social_instagram', 'label' => 'Instagram', 'type' => 'url', 'default_value' => '', 'sort_order' => 30],
            ['key' => 'social_facebook', 'label' => 'Facebook', 'type' => 'url', 'default_value' => '', 'sort_order' => 31],
            ['key' => 'social_twitter', 'label' => 'Twitter / X', 'type' => 'url', 'default_value' => '', 'sort_order' => 32],
            ['key' => 'social_linkedin', 'label' => 'LinkedIn', 'type' => 'url', 'default_value' => '', 'sort_order' => 33],
            ['key' => 'social_tiktok', 'label' => 'TikTok', 'type' => 'url', 'default_value' => '', 'sort_order' => 34],
            ['key' => 'social_youtube', 'label' => 'YouTube', 'type' => 'url', 'default_value' => '', 'sort_order' => 35],
            ['key' => 'social_pinterest', 'label' => 'Pinterest', 'type' => 'url', 'default_value' => '', 'sort_order' => 36],

            // 📍 ADRES BİLGİLERİ
            ['key' => 'contact_address_line_1', 'label' => 'Adres Satır 1', 'type' => 'text', 'default_value' => '', 'sort_order' => 40],
            ['key' => 'contact_address_line_2', 'label' => 'Adres Satır 2', 'type' => 'text', 'default_value' => '', 'sort_order' => 41],
            ['key' => 'contact_city', 'label' => 'Şehir', 'type' => 'text', 'default_value' => '', 'sort_order' => 42],
            ['key' => 'contact_state', 'label' => 'İlçe / Bölge', 'type' => 'text', 'default_value' => '', 'sort_order' => 43],
            ['key' => 'contact_postal_code', 'label' => 'Posta Kodu', 'type' => 'text', 'default_value' => '', 'sort_order' => 44],
            ['key' => 'contact_country', 'label' => 'Ülke', 'type' => 'text', 'default_value' => 'Türkiye', 'sort_order' => 45],

            // ⏰ ÇALIŞMA SAATLERİ
            ['key' => 'contact_working_hours', 'label' => 'Çalışma Saatleri', 'type' => 'text', 'default_value' => '09:00 - 18:00', 'sort_order' => 50],
            ['key' => 'contact_working_days', 'label' => 'Çalışma Günleri', 'type' => 'text', 'default_value' => 'Pazartesi - Cuma', 'sort_order' => 51],
        ];

        // Tüm ayarları oluştur
        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                [
                    'group_id' => $contactGroup->id,
                    'label' => $settingData['label'],
                    'type' => $settingData['type'],
                    'default_value' => $settingData['default_value'],
                    'sort_order' => $settingData['sort_order'],
                    'is_active' => true,
                    'is_system' => false,
                    'is_required' => false,
                ]
            );
        }

        echo "✅ İletişim Bilgileri grubu ve " . count($settings) . " ayar oluşturuldu!\n";
    }

    /**
     * Form Builder Layout
     */
    private function getLayout(): array
    {
        return [
            'title' => 'İletişim Bilgileri',
            'elements' => [
                // 📞 TELEFONLAR
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '📞 Telefon Numaraları',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 4], ['index' => 2, 'width' => 4], ['index' => 3, 'width' => 4]]],
                    'columns' => [
                        [
                            'width' => 4,
                            'elements' => [[
                                'type' => 'text',
                                'properties' => [
                                    'name' => 'contact_phone_1',
                                    'label' => 'Ana Telefon',
                                    'width' => 12,
                                    'placeholder' => '0216 XXX XX XX',
                                    'help_text' => 'Şirketin ana iletişim telefon numarası',
                                ],
                            ]],
                        ],
                        [
                            'width' => 4,
                            'elements' => [[
                                'type' => 'text',
                                'properties' => [
                                    'name' => 'contact_phone_2',
                                    'label' => 'Alternatif Telefon 1',
                                    'width' => 12,
                                    'placeholder' => '0216 XXX XX XX',
                                    'help_text' => 'İkinci telefon hattı (opsiyonel)',
                                ],
                            ]],
                        ],
                        [
                            'width' => 4,
                            'elements' => [[
                                'type' => 'text',
                                'properties' => [
                                    'name' => 'contact_phone_3',
                                    'label' => 'Alternatif Telefon 2',
                                    'width' => 12,
                                    'placeholder' => '0216 XXX XX XX',
                                    'help_text' => 'Üçüncü telefon hattı (opsiyonel)',
                                ],
                            ]],
                        ],
                    ],
                ],

                // 💬 WHATSAPP
                ['type' => 'divider', 'properties' => ['width' => 12]],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '💬 WhatsApp Numaraları',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 4], ['index' => 2, 'width' => 4], ['index' => 3, 'width' => 4]]],
                    'columns' => [
                        ['width' => 4, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_whatsapp_1', 'label' => 'Ana WhatsApp', 'width' => 12, 'placeholder' => '0501 XXX XX XX', 'help_text' => 'Ana WhatsApp iletişim numarası']]]],
                        ['width' => 4, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_whatsapp_2', 'label' => 'Destek WhatsApp', 'width' => 12, 'placeholder' => '0501 XXX XX XX', 'help_text' => 'Müşteri destek WhatsApp hattı']]]],
                        ['width' => 4, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_whatsapp_3', 'label' => 'Satış WhatsApp', 'width' => 12, 'placeholder' => '0501 XXX XX XX', 'help_text' => 'Satış ekibi WhatsApp hattı']]]],
                    ],
                ],

                // 📧 E-POSTALAR
                ['type' => 'divider', 'properties' => ['width' => 12]],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '📧 E-posta Adresleri',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 4], ['index' => 2, 'width' => 4], ['index' => 3, 'width' => 4]]],
                    'columns' => [
                        ['width' => 4, 'elements' => [['type' => 'email', 'properties' => ['name' => 'contact_email_1', 'label' => 'Genel E-posta', 'width' => 12, 'placeholder' => 'info@sirketiniz.com', 'help_text' => 'Genel iletişim e-posta adresi']]]],
                        ['width' => 4, 'elements' => [['type' => 'email', 'properties' => ['name' => 'contact_email_2', 'label' => 'Destek E-posta', 'width' => 12, 'placeholder' => 'destek@sirketiniz.com', 'help_text' => 'Müşteri destek e-posta adresi']]]],
                        ['width' => 4, 'elements' => [['type' => 'email', 'properties' => ['name' => 'contact_email_3', 'label' => 'Satış E-posta', 'width' => 12, 'placeholder' => 'satis@sirketiniz.com', 'help_text' => 'Satış ekibi e-posta adresi']]]],
                    ],
                ],

                // 🌐 SOSYAL MEDYA
                ['type' => 'divider', 'properties' => ['width' => 12]],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '🌐 Sosyal Medya Hesapları',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 6], ['index' => 2, 'width' => 6]]],
                    'columns' => [
                        ['width' => 6, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_instagram', 'label' => 'Instagram', 'width' => 12, 'placeholder' => 'https://instagram.com/hesabiniz', 'help_text' => 'Instagram profil linki']]]],
                        ['width' => 6, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_facebook', 'label' => 'Facebook', 'width' => 12, 'placeholder' => 'https://facebook.com/hesabiniz', 'help_text' => 'Facebook sayfası linki']]]],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 6], ['index' => 2, 'width' => 6]]],
                    'columns' => [
                        ['width' => 6, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_twitter', 'label' => 'Twitter / X', 'width' => 12, 'placeholder' => 'https://twitter.com/hesabiniz']]]],
                        ['width' => 6, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_linkedin', 'label' => 'LinkedIn', 'width' => 12, 'placeholder' => 'https://linkedin.com/company/hesabiniz']]]],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 4], ['index' => 2, 'width' => 4], ['index' => 3, 'width' => 4]]],
                    'columns' => [
                        ['width' => 4, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_tiktok', 'label' => 'TikTok', 'width' => 12, 'placeholder' => 'https://tiktok.com/@hesabiniz']]]],
                        ['width' => 4, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_youtube', 'label' => 'YouTube', 'width' => 12, 'placeholder' => 'https://youtube.com/@hesabiniz']]]],
                        ['width' => 4, 'elements' => [['type' => 'url', 'properties' => ['name' => 'social_pinterest', 'label' => 'Pinterest', 'width' => 12, 'placeholder' => 'https://pinterest.com/hesabiniz']]]],
                    ],
                ],

                // 📍 ADRES BİLGİLERİ
                ['type' => 'divider', 'properties' => ['width' => 12]],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '📍 Adres Bilgileri',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 6], ['index' => 2, 'width' => 6]]],
                    'columns' => [
                        ['width' => 6, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_address_line_1', 'label' => 'Adres Satır 1', 'width' => 12, 'placeholder' => 'Mahalle, Cadde, No']]]],
                        ['width' => 6, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_address_line_2', 'label' => 'Adres Satır 2', 'width' => 12, 'placeholder' => 'Daire, Kat, vb.']]]],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 3], ['index' => 2, 'width' => 3], ['index' => 3, 'width' => 3], ['index' => 4, 'width' => 3]]],
                    'columns' => [
                        ['width' => 3, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_city', 'label' => 'Şehir', 'width' => 12, 'placeholder' => 'İstanbul']]]],
                        ['width' => 3, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_state', 'label' => 'İlçe / Bölge', 'width' => 12, 'placeholder' => 'Kadıköy']]]],
                        ['width' => 3, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_postal_code', 'label' => 'Posta Kodu', 'width' => 12, 'placeholder' => '34000']]]],
                        ['width' => 3, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_country', 'label' => 'Ülke', 'width' => 12, 'placeholder' => 'Türkiye']]]],
                    ],
                ],

                // ⏰ ÇALIŞMA SAATLERİ
                ['type' => 'divider', 'properties' => ['width' => 12]],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '⏰ Çalışma Saatleri',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => ['columns' => [['index' => 1, 'width' => 6], ['index' => 2, 'width' => 6]]],
                    'columns' => [
                        ['width' => 6, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_working_hours', 'label' => 'Çalışma Saatleri', 'width' => 12, 'placeholder' => '09:00 - 18:00']]]],
                        ['width' => 6, 'elements' => [['type' => 'text', 'properties' => ['name' => 'contact_working_days', 'label' => 'Çalışma Günleri', 'width' => 12, 'placeholder' => 'Pazartesi - Cuma']]]],
                    ],
                ],
            ],
        ];
    }
}
