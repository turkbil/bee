<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AISettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GRUP OLUŞTUR: "Yapay Zeka" (Genel Sistem altında)
        $groupId = 9; // Yeni ID

        $existing = DB::table('settings_groups')->where('id', $groupId)->first();

        DB::table('settings_groups')->updateOrInsert(
            ['id' => $groupId],
            [
                'name' => 'Yapay Zeka',
                'slug' => 'yapay-zeka',
                'parent_id' => 1, // Genel Sistem altında
                'icon' => 'fas fa-robot',
                'prefix' => 'ai',
                'is_active' => true,
                'layout' => $this->getAISettingsLayout(),
                'created_at' => $existing->created_at ?? now(),
                'updated_at' => now(),
            ]
        );

        // 2. AYARLARI OLUŞTUR
        $settings = [
            // ==================== KİŞİLİK VE KİMLİK ====================
            [
                'group_id' => $groupId,
                'label' => 'AI Asistan İsmi',
                'key' => 'ai_assistant_name',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => false,
                'is_required' => true,
                'help_text' => 'AI asistanınızın adı. Kullanıcılara kendini bu isimle tanıtacak. (Örn: "Merhaba, ben iXtif Yapay Zeka Asistanı!")',
            ],
            [
                'group_id' => $groupId,
                'label' => 'AI Kişiliği / Rol',
                'key' => 'ai_personality_role',
                'type' => 'select',
                'options' => json_encode([
                    '' => '-- Seçim Yapın (Opsiyonel) --',
                    'sales_expert' => 'Satış Uzmanı (İkna edici, hevesli, pazarlamacı)',
                    'technical_consultant' => 'Teknik Danışman (Detaylı, analitik, uzman)',
                    'friendly_assistant' => 'Samimi Asistan (Arkadaş canlısı, yardımsever)',
                    'professional_consultant' => 'Profesyonel Danışman (Resmi, ciddi, güvenilir)',
                    'hybrid' => 'Karma (Satış + Teknik bilgi) ⭐ Önerilen',
                ]),
                'default_value' => '',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI asistanınızın kişiliği. Müşterilere nasıl yaklaşacağını belirler. "Karma" hem satış hem teknik bilgi verir, en dengeli seçenektir.',
            ],

            // ==================== FİRMA BİLGİLERİ ====================
            [
                'group_id' => $groupId,
                'label' => 'Firma Sektörü',
                'key' => 'ai_company_sector',
                'type' => 'text',
                'default_value' => '',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => false,
                'is_required' => true,
                'help_text' => 'Firmanızın faaliyet gösterdiği sektör. (Örn: "Forklift ve İstif Makineleri", "E-ticaret ve Lojistik")',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Firma Kuruluş Yılı',
                'key' => 'ai_company_founded_year',
                'type' => 'text',
                'default_value' => '2025 (2010\'dan beri tecrübeli)',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Firmanızın kuruluş yılı ve tecrübe bilgisi. Örn: "2025 (2010\'dan beri tecrübeli)" - AI bu bilgiyi müşterilere aktarır.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Firma Ana Hizmetleri',
                'key' => 'ai_company_main_services',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => false,
                'is_required' => true,
                'help_text' => 'Firmanızın sunduğu ana hizmetler (her satıra bir hizmet). AI bu bilgileri kullanarak müşterilere bilgi verir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Firma Uzmanlaştığı Alanlar',
                'key' => 'ai_company_expertise',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' => 6,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Firmanızın uzmanlaştığı alanlar. AI bu konularda daha detaylı bilgi verecek.',
            ],

            // ==================== HEDEF KİTLE ====================
            [
                'group_id' => $groupId,
                'label' => 'Hedef Müşteri Profili',
                'key' => 'ai_target_customer_profile',
                'type' => 'select',
                'options' => json_encode([
                    '' => '-- Seçim Yapın (Opsiyonel) --',
                    'b2b' => 'B2B (İşletmeler, Firmalar)',
                    'b2c' => 'B2C (Bireysel Müşteriler)',
                    'both' => 'Hem B2B hem B2C ⭐ Önerilen',
                ]),
                'default_value' => '',
                'sort_order' => 7,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Hedef müşteri kitleniz. AI buna göre dil ve tarz kullanır. "Both" seçeneği her iki müşteri tipine de uygun yaklaşır.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Hedef Sektörler',
                'key' => 'ai_target_industries',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' => 8,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Ürünlerinizi kullanan sektörler. AI konuşmadan müşterinin sektörünü anlar ve ona özel çözümler önerir. Örn: Müşteri "depo" derse → Lojistik çözümü öner.',
            ],

            // ==================== SOSYAL MEDYA VE İLETİŞİM ====================
            // NOT: Telefon, email, adres bilgileri artık "İletişim Bilgileri" grubunda (Grup 10)
            // AI otomatik olarak contact_phone_1, contact_whatsapp_1, social_* ayarlarını kullanır
            [
                'group_id' => $groupId,
                'label' => 'Telegram (AI Bildirimleri için)',
                'key' => 'ai_social_telegram',
                'type' => 'text',
                'default_value' => '',
                'sort_order' =>  9,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Telegram kullanıcı adınız veya grup linkiniz (örn: @firmaismi veya https://t.me/firmaismi). AI bildirim göndermek için kullanır. Müşteri iletişimi için değil!',
            ],

            // ==================== YANITLAMA TARZI ====================
            [
                'group_id' => $groupId,
                'label' => 'Yanıt Tarzı',
                'key' => 'ai_response_tone',
                'type' => 'select',
                'options' => json_encode([
                    'very_formal' => 'Çok Resmi (Sayın, Efendim, vb.)',
                    'formal' => 'Resmi (Siz, Sizler)',
                    'friendly' => 'Samimi (Sen, arkadaş canlısı) ⭐ Önerilen',
                    'casual' => 'Rahat (Günlük konuşma dili)',
                ]),
                'default_value' => '',
                'sort_order' =>  19,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI müşterilere nasıl hitap edecek? Pazarlama için "Samimi" önerilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Emoji Kullanımı',
                'key' => 'ai_use_emojis',
                'type' => 'select',
                'options' => json_encode([
                    'none' => 'Hiç Kullanma',
                    'minimal' => 'Minimal (1-2 emoji per mesaj)',
                    'moderate' => 'Orta (2-3 emoji per mesaj) ⭐ Önerilen',
                    'frequent' => 'Sık (3-5 emoji per mesaj)',
                ]),
                'default_value' => '',
                'sort_order' =>  20,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI emoji kullanacak mı? Samimi ve yardımsever bir ton için "Orta" önerilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Yanıt Uzunluğu',
                'key' => 'ai_response_length',
                'type' => 'select',
                'options' => json_encode([
                    'very_short' => 'Çok Kısa (1-2 cümle)',
                    'short' => 'Kısa (3-5 cümle)',
                    'medium' => 'Orta (5-10 cümle) ⭐ Önerilen',
                    'long' => 'Uzun (10+ cümle, detaylı)',
                ]),
                'default_value' => '',
                'sort_order' => 21,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI ne kadar detaylı yanıt versin? Pazarlama için "Orta" önerilir.',
            ],

            // ==================== PAZARLAMA TAKTİKLERİ ====================
            [
                'group_id' => $groupId,
                'label' => 'Satış Yaklaşımı',
                'key' => 'ai_sales_approach',
                'type' => 'select',
                'options' => json_encode([
                    'aggressive' => 'Agresif (Sürekli satışa teşvik)',
                    'moderate' => 'Orta (Dengeli bilgi + satış) ⭐ Önerilen',
                    'consultative' => 'Danışmanlık (Önce bilgi, sonra satış)',
                    'passive' => 'Pasif (Sadece bilgi, satış baskısı yok)',
                ]),
                'default_value' => '',
                'sort_order' =>  22,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI ne kadar satış odaklı olsun? Pazarlama için "Orta" dengeli yaklaşım önerilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'CTA (Call-to-Action) Sıklığı',
                'key' => 'ai_cta_frequency',
                'type' => 'select',
                'options' => json_encode([
                    'every_message' => 'Her Mesajda (📞 Arayın, WhatsApp yazın)',
                    'occasional' => 'Ara Sıra (İhtiyaç olduğunda) ⭐ Önerilen',
                    'rare' => 'Nadiren (Sadece önemli konularda)',
                    'never' => 'Hiç (CTA yok)',
                ]),
                'default_value' => '',
                'sort_order' =>  23,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI ne sıklıkla "Bizi arayın", "WhatsApp yazın" gibi CTA kullanacak? "Ara Sıra" dengeli ve etkilidir.',
            ],

            // ==================== FİYAT POLİTİKASI ====================
            [
                'group_id' => $groupId,
                'label' => 'Fiyat Gösterme Politikası',
                'key' => 'ai_price_policy',
                'type' => 'select',
                'options' => json_encode([
                    'show_all' => 'Tüm Fiyatları Göster (Varsa)',
                    'show_range' => 'Fiyat Aralığı Göster (45.000-55.000 TL)',
                    'contact_only' => 'Fiyat Verme, Sadece Yönlendir',
                ]),
                'default_value' => '',
                'sort_order' =>  24,
                'is_active' => true,
                'is_system' => false,
                'is_required' => true,
                'help_text' => 'AI fiyatları nasıl göstersin? B2B için genelde "contact_only" tercih edilir.',
            ],

            // ==================== ÖZEL TALİMATLAR ====================
            [
                'group_id' => $groupId,
                'label' => 'Özel Talimatlar (Custom Prompt)',
                'key' => 'ai_custom_instructions',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' =>  25,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI\'ya özel talimatlar. (Örn: "Rakip markaları asla kötüleme", "Her yanıtta şirket referanslarını vurgula"). Boş bırakılabilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Yasaklı Konular',
                'key' => 'ai_forbidden_topics',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' =>  26,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI\'nın kesinlikle konuşmayacağı konular (her satıra bir konu).',
            ],

            // ==================== ÖZEL BİLGİLER ====================
            [
                'group_id' => $groupId,
                'label' => 'Firma Sertifikaları',
                'key' => 'ai_company_certifications',
                'type' => 'textarea',
                'default_value' => '',
                'sort_order' =>  32,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Firma sertifikalarınız. AI müşterilere güven vermek için bu bilgileri kullanır.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Firma Referans Sayısı',
                'key' => 'ai_company_reference_count',
                'type' => 'text',
                'default_value' => '',
                'sort_order' =>  33,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'Mutlu müşteri sayısı. (Örn: "500+", "1000+"). AI bunu vurgular.',
            ],

            // ==================== DİNAMİK BİLGİ BANKASI (FAQ/SORULAR) ====================
            [
                'group_id' => $groupId,
                'label' => 'AI Bilgi Bankası (Sık Sorulan Sorular)',
                'key' => 'ai_knowledge_base',
                'type' => 'json',
                'default_value' => json_encode([]),
                'sort_order' => 38,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI\'nın bilgi bankası (maksimum 2-3 soru önerilir, token limiti için). Admin panelinden AI Knowledge Base Manager ile detaylı soru-cevap ekleyebilirsiniz.',
            ],

            // ==================== MODÜL ENTEGRASYONLARI ====================
            [
                'group_id' => $groupId,
                'label' => 'Shop Modülü Entegrasyonu',
                'key' => 'ai_module_shop_enabled',
                'type' => 'select',
                'options' => json_encode([
                    'enabled' => 'Etkin ⭐ (Ürünler hakkında bilgi ver)',
                    'disabled' => 'Devre Dışı',
                ]),
                'default_value' => '',
                'sort_order' =>  35,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI ürünler hakkında bilgi versin mi? Pazarlama için önerilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Page Modülü Entegrasyonu',
                'key' => 'ai_module_page_enabled',
                'type' => 'select',
                'options' => json_encode([
                    'enabled' => 'Etkin ⭐ (Şirket bilgileri ver)',
                    'disabled' => 'Devre Dışı',
                ]),
                'default_value' => '',
                'sort_order' =>  36,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI şirket hakkında bilgi versin mi? Güven oluşturmak için önerilir.',
            ],
            [
                'group_id' => $groupId,
                'label' => 'Blog Modülü Entegrasyonu',
                'key' => 'ai_module_blog_enabled',
                'type' => 'select',
                'options' => json_encode([
                    'enabled' => 'Etkin ⭐ (Blog makaleleri öner)',
                    'disabled' => 'Devre Dışı',
                ]),
                'default_value' => '',
                'sort_order' =>  37,
                'is_active' => true,
                'is_system' => false,
                'is_required' => false,
                'help_text' => 'AI blog makaleleri önersin mi? Etkinleştirilirse AI ilgili blog yazılarını müşterilere önerir (SEO ve güven için faydalıdır).',
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
                    'help_text' => $setting['help_text'] ?? null,
                    'created_at' => $existing->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function getAISettingsLayout(): string
    {
        $layout = [
            'title' => 'Yapay Zeka Asistan Ayarları',
            'description' => 'AI asistanınızın kişiliğini, tarzını ve davranışlarını bu ayarlarla özelleştirin.',
            'elements' => [
                // KİŞİLİK VE KİMLİK
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => '🤖 Kişilik ve Kimlik',
                        'size' => 'h3',
                        'width' => 12,
                    ],
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
                                        'label' => 'AI Asistan İsmi',
                                        'name' => 'ai_assistant_name',
                                        'setting_id' => 1,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'AI Kişiliği / Rol',
                                        'name' => 'ai_personality_role',
                                        'setting_id' => 2,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // FİRMA BİLGİLERİ
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => '🏢 Firma Bilgileri',
                        'size' => 'h3',
                        'width' => 12,
                    ],
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
                                        'label' => 'Firma Sektörü',
                                        'name' => 'ai_company_sector',
                                        'setting_id' => 3,
                                    ],
                                ],
                                [
                                    'type' => 'textarea',
                                    'properties' => [
                                        'label' => 'Firma Ana Hizmetleri',
                                        'name' => 'ai_company_main_services',
                                        'setting_id' => 5,
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
                                        'label' => 'Firma Kuruluş Yılı',
                                        'name' => 'ai_company_founded_year',
                                        'setting_id' => 4,
                                    ],
                                ],
                                [
                                    'type' => 'textarea',
                                    'properties' => [
                                        'label' => 'Firma Uzmanlaştığı Alanlar',
                                        'name' => 'ai_company_expertise',
                                        'setting_id' => 6,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // HEDEF KİTLE
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => '🎯 Hedef Kitle',
                        'size' => 'h3',
                        'width' => 12,
                    ],
                ],
                [
                    'type' => 'row',
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Hedef Müşteri Profili',
                                        'name' => 'ai_target_customer_profile',
                                        'setting_id' => 7,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'textarea',
                                    'properties' => [
                                        'label' => 'Hedef Sektörler',
                                        'name' => 'ai_target_industries',
                                        'setting_id' => 8,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // YANITLAMA TARZI
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => '💬 Yanıtlama Tarzı',
                        'size' => 'h3',
                        'width' => 12,
                    ],
                ],
                [
                    'type' => 'row',
                    'columns' => [
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Yanıt Tarzı',
                                        'name' => 'ai_response_tone',
                                        'setting_id' => 12,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Emoji Kullanımı',
                                        'name' => 'ai_use_emojis',
                                        'setting_id' => 13,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Yanıt Uzunluğu',
                                        'name' => 'ai_response_length',
                                        'setting_id' => 14,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // MODÜL ENTEGRASYONLARI
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => '🔌 Modül Entegrasyonları',
                        'size' => 'h3',
                        'width' => 12,
                    ],
                ],
                [
                    'type' => 'row',
                    'columns' => [
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Shop Modülü',
                                        'name' => 'ai_module_shop_enabled',
                                        'setting_id' => 23,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Page Modülü',
                                        'name' => 'ai_module_page_enabled',
                                        'setting_id' => 24,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'width' => 4,
                            'elements' => [
                                [
                                    'type' => 'select',
                                    'properties' => [
                                        'label' => 'Blog Modülü',
                                        'name' => 'ai_module_blog_enabled',
                                        'setting_id' => 25,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return json_encode($layout);
    }
}
