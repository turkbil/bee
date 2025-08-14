<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogWriterUniversalInputSeeder extends Seeder
{
    /**
     * Blog Writer için Universal Input System V3 Implementation
     * UNIVERSAL-INPUT-SYSTEM-V3-PROFESSIONAL-ROADMAP.md tam uyumlu
     */
    public function run(): void
    {
        // 1. AI INPUT GROUPS - Blog Writer için input grupları
        $inputGroups = [
            [
                'id' => 1,
                'name' => 'Temel Blog Ayarları',
                'slug' => 'blog_basic_inputs',
                'feature_id' => 201, // Blog Writer Feature ID
                'sort_order' => 1,
                'is_collapsible' => false,
                'is_expanded' => true,
                'description' => 'Blog yazısı oluşturma için temel gerekli alanlar'
            ],
            [
                'id' => 2,
                'name' => 'İleri Düzey Ayarlar',
                'slug' => 'blog_advanced_settings',
                'feature_id' => 201,
                'sort_order' => 2,
                'is_collapsible' => true,
                'is_expanded' => false,
                'description' => 'Blog yazısını özelleştirmek için gelişmiş seçenekler'
            ]
        ];

        foreach ($inputGroups as $group) {
            DB::table('ai_input_groups')->updateOrInsert(
                ['id' => $group['id']],
                array_merge($group, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // 2. AI FEATURE INPUTS - Blog Writer için özel inputlar
        $featureInputs = [
            // Blog Konusu (Ana Input)
            [
                'id' => 1,
                'name' => 'Blog Konusu',
                'slug' => 'blog_topic',
                'feature_id' => 201,
                'group_id' => 1,
                'type' => 'textarea',
                'placeholder' => 'Hangi konu hakkında blog yazısı yazmak istiyorsunuz?',
                'help_text' => 'Yapay zeka ile yazılacak konuyu belirtin. Açık ve detaylı konu tanımlaması daha iyi sonuç verir.',
                'is_primary' => true,
                'is_required' => true,
                'validation_rules' => json_encode(['required', 'string', 'min:10', 'max:1000']),
                'sort_order' => 1,
                'config' => json_encode([
                    'rows' => 4,
                    'character_limit' => 1000,
                    'show_counter' => true
                ]),
            ],

            // Yazım Tonu
            [
                'id' => 2,
                'name' => 'Yazım Tonu',
                'slug' => 'writing_tone',
                'feature_id' => 201,
                'group_id' => 2,
                'type' => 'select',
                'placeholder' => 'Yazım tonunu seçin',
                'help_text' => 'İçeriğinizin hangi tonla yazılmasını istiyorsunuz?',
                'is_primary' => false,
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'string']),
                'sort_order' => 2, // Hedef kitleden sonra gelecek
                'config' => json_encode([
                    'data_source' => 'ai_prompts',
                    'data_filter' => ['prompt_type' => 'writing_tone', 'is_active' => true],
                    'value_field' => 'prompt_id',
                    'label_field' => 'name',
                    'default_value' => null // İlk seçenek otomatik seçilecek
                ]),
            ],

            // İçerik Uzunluğu
            [
                'id' => 3,
                'name' => 'İçerik Uzunluğu',
                'slug' => 'content_length',
                'feature_id' => 201,
                'group_id' => 1, // Temel ayarlar grubuna taşındı
                'type' => 'range',
                'placeholder' => null,
                'help_text' => 'Blog yazısının ne kadar detaylı olmasını istiyorsunuz?',
                'is_primary' => true, // Primary yapıldı
                'is_required' => true, // Zorunlu hale getirildi
                'validation_rules' => json_encode(['required', 'integer', 'min:1', 'max:5']),
                'sort_order' => 3, // Hedef kitleden sonra gelecek
                'config' => json_encode([
                    'data_source' => 'ai_prompts',
                    'data_filter' => ['prompt_type' => 'content_length', 'is_active' => true],
                    'value_field' => 'prompt_id',
                    'label_field' => 'name',
                    'min_value' => 1,
                    'max_value' => 5,
                    'default_value' => 3,
                    'step' => 1,
                    'show_labels' => true,
                    'show_badge' => true
                ]),
            ],

            // Hedef Kitle
            [
                'id' => 4,
                'name' => 'Hedef Kitle',
                'slug' => 'target_audience',
                'feature_id' => 201,
                'group_id' => 2, // İleri Düzey Ayarlar grubunda kalacak
                'type' => 'text',
                'placeholder' => null, // Placeholder kaldırıldı
                'help_text' => 'Yaş grubu, meslek, deneyim seviyesi, ilgi alanları gibi detayları ekleyebilirsiniz.',
                'is_primary' => true, // Primary kalacak (yüksek öncelik)
                'is_required' => false, // Zorunlu değil - rahat bırakıldı
                'validation_rules' => json_encode(['nullable', 'string', 'min:3', 'max:500']),
                'sort_order' => 1, // İleri düzey ayarlar içinde en üst sırada
                'config' => json_encode([
                    'character_limit' => 500,
                    'show_counter' => true,
                    'autocomplete_suggestions' => [
                        '18-25 yaş gençler',
                        '25-35 yaş profesyoneller',
                        '35-45 yaş yöneticiler', 
                        '45+ yaş deneyimliler',
                        'Teknoloji meraklıları',
                        'İşletme sahipleri',
                        'Freelancer\'lar',
                        'Öğrenciler',
                        'Akademisyenler'
                    ]
                ]),
            ],

            // Şirket Profili Kullanımı
            [
                'id' => 5,
                'name' => 'Şirket Profilimi Kullan',
                'slug' => 'use_company_profile',
                'feature_id' => 201,
                'group_id' => 2,
                'type' => 'checkbox',
                'placeholder' => null,
                'help_text' => 'AI, şirket bilgilerinizi kullanarak daha kişiselleştirilmiş içerik üretir',
                'is_primary' => false,
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'boolean']),
                'sort_order' => 4,
                'config' => json_encode([
                    'style' => 'switch',
                    'size' => 'default',
                    'color' => 'success',
                    'icon' => 'ti ti-building-store',
                    'api_check' => '/admin/ai/api/profiles/company-info',
                    'show_status' => true
                ]),
            ]
        ];

        foreach ($featureInputs as $input) {
            DB::table('ai_feature_inputs')->updateOrInsert(
                ['id' => $input['id']],
                array_merge($input, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // 3. INPUT OPTIONS - Dropdown ve çoktan seçmeli alanlar için sabit seçenekler (eğer gerekiyorsa)
        $inputOptions = [
            // Eğer gelecekte sabit seçenekler gerekirse buraya eklenecek
            // Şu anda tüm veriler dinamik olarak ai_prompts'tan çekiliyor
        ];

        if (!empty($inputOptions)) {
            foreach ($inputOptions as $option) {
                DB::table('ai_input_options')->updateOrInsert(
                    ['id' => $option['id']],
                    array_merge($option, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }

        $this->command->info('✅ Blog Writer Universal Input System seeder başarıyla tamamlandı.');
        $this->command->info('📊 Input Groups: ' . count($inputGroups));
        $this->command->info('📊 Feature Inputs: ' . count($featureInputs));
        $this->command->info('📊 Input Options: ' . count($inputOptions));
    }
}