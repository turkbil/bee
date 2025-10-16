<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Settings Group kontrol et veya oluştur
        $group = DB::table('settings_groups')->where('slug', 'notifications')->first();

        if (!$group) {
            $groupId = DB::table('settings_groups')->insertGetId([
                'name' => 'Bildirim Ayarları',
                'slug' => 'notifications',
                'prefix' => 'notification',
                'description' => 'Telegram ve WhatsApp bildirim ayarlarını yönetin',
                'icon' => 'bell',
                'parent_id' => 1, // Genel Sistem altında
                'order' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $groupId = $group->id;
        }

        // Settings ekle veya güncelle
        $telegramEnabled = DB::table('settings')->updateOrInsert(
            ['key' => 'telegram_enabled'],
            [
                'group_id' => $groupId,
                'label' => 'Telegram Aktif',
                'key' => 'telegram_enabled',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 1,
                'is_active' => true,
                'help_text' => 'Telegram üzerinden müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $telegramEnabled = DB::table('settings')->where('key', 'telegram_enabled')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'telegram_bot_token'],
            [
                'group_id' => $groupId,
                'label' => 'Telegram Bot Token',
                'key' => 'telegram_bot_token',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 2,
                'is_active' => true,
                'help_text' => 'Telegram BotFather\'dan (@BotFather) alacağınız benzersiz bot token\'ı. Örnek: 1234567890:ABCdefGHIjklMNOpqrsTUVwxyz',
                'updated_at' => now(),
            ]
        );
        $telegramBotToken = DB::table('settings')->where('key', 'telegram_bot_token')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'telegram_chat_id'],
            [
                'group_id' => $groupId,
                'label' => 'Telegram Chat ID',
                'key' => 'telegram_chat_id',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 3,
                'is_active' => true,
                'help_text' => 'Bildirimlerin gönderileceği Telegram grup veya kullanıcı ID\'si. @userinfobot ile öğrenebilirsiniz. Grup için - ile başlar (örn: -1002943373765)',
                'updated_at' => now(),
            ]
        );
        $telegramChatId = DB::table('settings')->where('key', 'telegram_chat_id')->value('id');

        $whatsappEnabled = DB::table('settings')->updateOrInsert(
            ['key' => 'whatsapp_enabled'],
            [
                'group_id' => $groupId,
                'label' => 'WhatsApp Aktif',
                'key' => 'whatsapp_enabled',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 4,
                'is_active' => true,
                'help_text' => 'WhatsApp Business API (Twilio) üzerinden müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $whatsappEnabled = DB::table('settings')->where('key', 'whatsapp_enabled')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'twilio_account_sid'],
            [
                'group_id' => $groupId,
                'label' => 'Twilio Account SID',
                'key' => 'twilio_account_sid',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 5,
                'is_active' => true,
                'help_text' => 'Twilio hesabınızdan alacağınız Account SID. Twilio Console\'da bulabilirsiniz. Format: ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx (32 karakter)',
                'updated_at' => now(),
            ]
        );
        $twilioAccountSid = DB::table('settings')->where('key', 'twilio_account_sid')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'twilio_auth_token'],
            [
                'group_id' => $groupId,
                'label' => 'Twilio Auth Token',
                'key' => 'twilio_auth_token',
                'type' => 'password',
                'default_value' => '',
                'sort_order' => 6,
                'is_active' => true,
                'help_text' => 'Twilio hesabınızdan alacağınız Auth Token. Twilio Console\'da "Show" butonuna tıklayarak görebilirsiniz.',
                'updated_at' => now(),
            ]
        );
        $twilioAuthToken = DB::table('settings')->where('key', 'twilio_auth_token')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'twilio_whatsapp_from'],
            [
                'group_id' => $groupId,
                'label' => 'WhatsApp Gönderen Numara',
                'key' => 'twilio_whatsapp_from',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 7,
                'is_active' => true,
                'help_text' => 'Twilio WhatsApp Sandbox numarası (Test için) veya onaylı WhatsApp Business numaranız. Format: whatsapp:+14155238886',
                'updated_at' => now(),
            ]
        );
        $twilioWhatsappFrom = DB::table('settings')->where('key', 'twilio_whatsapp_from')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'twilio_whatsapp_to'],
            [
                'group_id' => $groupId,
                'label' => 'WhatsApp Alıcı Numara',
                'key' => 'twilio_whatsapp_to',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 8,
                'is_active' => true,
                'help_text' => 'Bildirimlerin gönderileceği WhatsApp numaranız. Format: whatsapp:+905321234567',
                'updated_at' => now(),
            ]
        );
        $twilioWhatsappTo = DB::table('settings')->where('key', 'twilio_whatsapp_to')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'email_enabled'],
            [
                'group_id' => $groupId,
                'label' => 'Email Aktif',
                'key' => 'email_enabled',
                'type' => 'checkbox',
                'default_value' => '1',
                'sort_order' => 9,
                'is_active' => true,
                'help_text' => 'Email yoluyla müşteri talepleri ve bildirimleri almak için bu seçeneği aktif edin.',
                'updated_at' => now(),
            ]
        );
        $emailEnabled = DB::table('settings')->where('key', 'email_enabled')->value('id');

        DB::table('settings')->updateOrInsert(
            ['key' => 'email'],
            [
                'group_id' => $groupId,
                'label' => 'Bildirim Email Adresi',
                'key' => 'email',
                'type' => 'email',
                'default_value' => '',
                'sort_order' => 10,
                'is_active' => true,
                'help_text' => 'Bildirimlerin gönderileceği email adresi. Genellikle info@sirketiniz.com gibi bir adres kullanılır.',
                'updated_at' => now(),
            ]
        );
        $email = DB::table('settings')->where('key', 'email')->value('id');

        // Layout oluştur
        $layout = [
            'title' => 'Bildirim Ayarları',
            'description' => 'Telegram, WhatsApp ve Email bildirim ayarlarını yönetin. Sadece doldurduğunuz kanallar aktif olacaktır.',
            'elements' => [
                // Telegram Bölümü
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'width' => 12,
                        'content' => '📱 Telegram Bildirimleri'
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $telegramEnabled,
                                        'default_value' => '1'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $telegramBotToken,
                                        'placeholder' => '1234567890:ABCdefGHIjklMNOpqrsTUVwxyz',
                                        'default_value' => ''
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
                                        'name' => 'telegram_chat_id',
                                        'label' => 'Telegram Chat ID',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği Telegram grup veya kullanıcı ID\'si.',
                                        'setting_id' => $telegramChatId,
                                        'placeholder' => '-1002943373765',
                                        'default_value' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // WhatsApp Bölümü
                [
                    'type' => 'divider'
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'width' => 12,
                        'content' => '💬 WhatsApp Bildirimleri (Twilio)'
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $whatsappEnabled,
                                        'default_value' => '1'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $twilioAccountSid,
                                        'placeholder' => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                                        'default_value' => ''
                                    ]
                                ]
                            ]
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
                                        'setting_id' => $twilioAuthToken,
                                        'placeholder' => '••••••••••••••••••••',
                                        'default_value' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $twilioWhatsappFrom,
                                        'placeholder' => 'whatsapp:+14155238886',
                                        'default_value' => ''
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
                                        'name' => 'twilio_whatsapp_to',
                                        'label' => 'WhatsApp Alıcı Numara',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği WhatsApp numaranız.',
                                        'setting_id' => $twilioWhatsappTo,
                                        'placeholder' => 'whatsapp:+905321234567',
                                        'default_value' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                // Email Bölümü
                [
                    'type' => 'divider'
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'size' => 'h3',
                        'width' => 12,
                        'content' => '📧 Email Bildirimleri'
                    ]
                ],
                [
                    'type' => 'row',
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
                                        'setting_id' => $emailEnabled,
                                        'default_value' => '1'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'row',
                    'columns' => [
                        [
                            'width' => 12,
                            'elements' => [
                                [
                                    'type' => 'email',
                                    'properties' => [
                                        'name' => 'email',
                                        'label' => 'Bildirim Email Adresi',
                                        'width' => 12,
                                        'required' => false,
                                        'help_text' => 'Bildirimlerin gönderileceği email adresi.',
                                        'setting_id' => $email,
                                        'placeholder' => 'info@sirketiniz.com',
                                        'default_value' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('settings_groups')
            ->where('id', $groupId)
            ->update(['layout' => json_encode($layout)]);
    }
}
