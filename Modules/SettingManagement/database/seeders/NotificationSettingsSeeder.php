<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bildirim Ayarları Grubu Oluştur
        $notificationGroup = SettingGroup::updateOrCreate(
            ['slug' => 'bildirim-ayarlari'],
            [
                'parent_id' => 1, // Genel Sistem
                'name' => 'Bildirim Ayarları',
                'description' => 'Telegram, WhatsApp (Twilio) ve Email bildirim ayarları',
                'icon' => 'fa-solid fa-bell',
                'is_active' => true,
                'layout' => $this->getLayout(),
            ]
        );

        $settings = [
            // 📱 TELEGRAM BİLDİRİMLERİ
            [
                'key' => 'telegram_enabled',
                'label' => 'Telegram Aktif',
                'type' => 'checkbox',
                'default_value' => '0',
                'sort_order' => 1,
            ],
            [
                'key' => 'telegram_bot_token',
                'label' => 'Telegram Bot Token',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 2,
            ],
            [
                'key' => 'telegram_chat_id',
                'label' => 'Telegram Chat ID',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 3,
            ],

            // 💬 WHATSAPP BİLDİRİMLERİ (TWILIO)
            [
                'key' => 'whatsapp_enabled',
                'label' => 'WhatsApp Aktif',
                'type' => 'checkbox',
                'default_value' => '0',
                'sort_order' => 10,
            ],
            [
                'key' => 'twilio_account_sid',
                'label' => 'Twilio Account SID',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 11,
            ],
            [
                'key' => 'twilio_auth_token',
                'label' => 'Twilio Auth Token',
                'type' => 'password',
                'default_value' => '',
                'sort_order' => 12,
            ],
            [
                'key' => 'twilio_whatsapp_from',
                'label' => 'WhatsApp Gönderen Numara',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 13,
            ],
            [
                'key' => 'twilio_whatsapp_to',
                'label' => 'WhatsApp Alıcı Numara',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 14,
            ],

            // 📧 EMAIL BİLDİRİMLERİ
            [
                'key' => 'email_enabled',
                'label' => 'Email Aktif',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 20,
            ],
            [
                'key' => 'notification_email',
                'label' => 'Bildirim Email Adresi',
                'type' => 'email',
                'default_value' => '',
                'sort_order' => 21,
            ],
        ];

        // Tüm ayarları oluştur
        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                [
                    'group_id' => $notificationGroup->id,
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

        echo "✅ Bildirim Ayarları grubu ve " . count($settings) . " ayar oluşturuldu!\n";
    }

    /**
     * Form Builder Layout
     */
    private function getLayout(): array
    {
        return [
            'title' => 'Bildirim Ayarları',
            'elements' => [
                // 📱 TELEGRAM BİLDİRİMLERİ
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '📱 Telegram Bildirimleri',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 12],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'checkbox',
                                    'properties' => [
                                        'name' => 'telegram_enabled',
                                        'label' => 'Telegram Aktif',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Telegram üzerinden müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                                        'default_value' => '0',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'name' => 'telegram_bot_token',
                                        'label' => 'Telegram Bot Token',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Telegram BotFather\'dan (@BotFather) alacağınız benzersiz bot token\'ı.',
                                        'placeholder' => '1234567890:ABCdefGHIjklMNOpqrsTUVwxyz',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'name' => 'telegram_chat_id',
                                        'label' => 'Telegram Chat ID',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği Telegram grup veya kullanıcı ID\'si.',
                                        'placeholder' => '-1002943373765',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // 💬 WHATSAPP BİLDİRİMLERİ
                [
                    'type' => 'divider',
                    'properties' => [
                        'width' => 12,
                    ],
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '💬 WhatsApp Bildirimleri (Twilio)',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 12],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'checkbox',
                                    'properties' => [
                                        'name' => 'whatsapp_enabled',
                                        'label' => 'WhatsApp Aktif',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'WhatsApp Business API (Twilio) üzerinden müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                                        'default_value' => '0',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'name' => 'twilio_account_sid',
                                        'label' => 'Twilio Account SID',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Twilio hesabınızdan alacağınız Account SID.',
                                        'placeholder' => 'AC1b50075754770609cb4a69be42112e3f',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'password',
                                    'properties' => [
                                        'name' => 'twilio_auth_token',
                                        'label' => 'Twilio Auth Token',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Twilio hesabınızdan alacağınız Auth Token.',
                                        'placeholder' => '••••••••••••••••••••',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'name' => 'twilio_whatsapp_from',
                                        'label' => 'WhatsApp Gönderen Numara',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Twilio WhatsApp Sandbox numarası veya onaylı numara.',
                                        'placeholder' => 'whatsapp:+14155238886',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'name' => 'twilio_whatsapp_to',
                                        'label' => 'WhatsApp Alıcı Numara',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği WhatsApp numaranız.',
                                        'placeholder' => 'whatsapp:+905321234567',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // 📧 EMAIL BİLDİRİMLERİ
                [
                    'type' => 'divider',
                    'properties' => [
                        'width' => 12,
                    ],
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'align' => 'left',
                        'width' => 12,
                        'content' => '📧 Email Bildirimleri',
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 12],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'checkbox',
                                    'properties' => [
                                        'name' => 'email_enabled',
                                        'label' => 'Email Aktif',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Email yoluyla müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                                        'default_value' => '1',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 12],
                        ],
                    ],
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'email',
                                    'properties' => [
                                        'name' => 'notification_email',
                                        'label' => 'Bildirim Email Adresi',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği email adresi.',
                                        'placeholder' => 'info@sirketiniz.com',
                                        'default_value' => '',
                                        'is_active' => true,
                                        'is_system' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
