<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationUniversalInputSeeder extends Seeder
{
    /**
     * Translation Feature için Universal Input System V3 Implementation
     * Page modülü ile tam entegre çalışacak çeviri sistemi
     */
    public function run(): void
    {
        // 1. AI INPUT GROUPS - Translation için input grupları
        $inputGroups = [
            [
                'id' => 3,
                'name' => 'Kaynak Dil ve İçerik',
                'slug' => 'translation_source',
                'feature_id' => 301, // Translation Feature ID
                'sort_order' => 1,
                'is_collapsible' => false,
                'is_expanded' => true,
                'description' => 'Çevrilecek içerik ve kaynak dil bilgileri'
            ],
            [
                'id' => 4,
                'name' => 'Hedef Diller ve Seçenekler',
                'slug' => 'translation_targets',
                'feature_id' => 301,
                'sort_order' => 2,
                'is_collapsible' => false,
                'is_expanded' => true,
                'description' => 'Hangi dillere çeviri yapılacağı ve çeviri seçenekleri'
            ],
            [
                'id' => 5,
                'name' => 'Gelişmiş Çeviri Ayarları',
                'slug' => 'translation_advanced',
                'feature_id' => 301,
                'sort_order' => 3,
                'is_collapsible' => true,
                'is_expanded' => false,
                'description' => 'SEO uyumluluğu ve özel çeviri kuralları'
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

        // 2. AI FEATURE INPUTS - Translation için özel inputlar
        $featureInputs = [
            // Kaynak Dil Seçimi
            [
                'id' => 6,
                'name' => 'Kaynak Dil',
                'slug' => 'source_language',
                'feature_id' => 301,
                'group_id' => 3,
                'type' => 'select',
                'placeholder' => 'İçeriğin mevcut dilini seçin',
                'help_text' => 'Çevrilecek içeriğin şu anki dili hangisi?',
                'is_required' => true,
                'validation_rules' => json_encode(['required', 'string']),
                'sort_order' => 1,
                'config' => json_encode([
                    'data_source' => 'tenant_languages',
                    'data_filter' => ['is_active' => true],
                    'value_field' => 'code',
                    'label_field' => 'name',
                    'show_flags' => true,
                    'auto_detect' => true
                ]),
            ],

            // Çevrilecek İçerik Türü
            [
                'id' => 7,
                'name' => 'İçerik Türü',
                'slug' => 'content_type',
                'feature_id' => 301,
                'group_id' => 3,
                'type' => 'radio',
                'placeholder' => null,
                'help_text' => 'Ne tür içerik çevirmek istiyorsunuz?',
                'is_required' => true,
                'validation_rules' => json_encode(['required', 'in:page,text,seo,bulk']),
                'sort_order' => 2,
                'config' => json_encode([
                    'options' => [
                        ['value' => 'page', 'label' => 'Sayfa İçeriği', 'description' => 'Mevcut sayfalarınızı çevirin'],
                        ['value' => 'text', 'label' => 'Metin Çevirisi', 'description' => 'Tek seferde metin çevirin'],
                        ['value' => 'seo', 'label' => 'SEO Çevirisi', 'description' => 'SEO ayarlarıyla birlikte çeviri'],
                        ['value' => 'bulk', 'label' => 'Toplu Çeviri', 'description' => 'Birden fazla içeriği çevirin']
                    ],
                    'display' => 'cards'
                ]),
            ],

            // Hedef Diller (Çoklu Seçim)
            [
                'id' => 8,
                'name' => 'Hedef Diller',
                'slug' => 'target_languages',
                'feature_id' => 301,
                'group_id' => 4,
                'type' => 'select', // multiselect yok, select kullanıyoruz
                'placeholder' => 'Çeviri yapılacak dilleri seçin',
                'help_text' => 'İçeriğin çevrileceği dilleri seçebilirsiniz (birden fazla seçim yapabilirsiniz)',
                'is_required' => true,
                'validation_rules' => json_encode(['required', 'array', 'min:1']),
                'sort_order' => 1,
                'config' => json_encode([
                    'data_source' => 'tenant_languages',
                    'data_filter' => ['is_active' => true],
                    'value_field' => 'code',
                    'label_field' => 'name',
                    'show_flags' => true,
                    'multiple' => true,
                    'max_selections' => 10,
                    'exclude_source' => true // Kaynak dili hariç tut
                ]),
            ],

            // Çeviri Kalitesi
            [
                'id' => 9,
                'name' => 'Çeviri Kalitesi',
                'slug' => 'translation_quality',
                'feature_id' => 301,
                'group_id' => 4,
                'type' => 'select',
                'placeholder' => 'Çeviri kalitesi seviyesini seçin',
                'help_text' => 'Daha yüksek kalite daha fazla token kullanır',
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'string']),
                'sort_order' => 2,
                'config' => json_encode([
                    'options' => [
                        ['value' => 'fast', 'label' => 'Hızlı Çeviri', 'description' => 'Temel çeviri, düşük token'],
                        ['value' => 'balanced', 'label' => 'Dengeli Çeviri', 'description' => 'Kalite ve hız dengesi'],
                        ['value' => 'premium', 'label' => 'Premium Çeviri', 'description' => 'En yüksek kalite, yüksek token']
                    ],
                    'default_value' => 'balanced'
                ]),
            ],

            // SEO Ayarlarını Koru
            [
                'id' => 10,
                'name' => 'SEO Ayarlarını Çevir',
                'slug' => 'preserve_seo',
                'feature_id' => 301,
                'group_id' => 5,
                'type' => 'checkbox', // switch yok, checkbox kullanıyoruz
                'placeholder' => null,
                'help_text' => 'Meta başlıklar, açıklamalar ve SEO alanları da çevrilsin mi?',
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'boolean']),
                'sort_order' => 1,
                'config' => json_encode([
                    'style' => 'switch',
                    'size' => 'default',
                    'color' => 'success',
                    'default_value' => true,
                    'icon' => 'fas fa-search'
                ]),
            ],

            // HTML/Markdown Formatını Koru
            [
                'id' => 11,
                'name' => 'Formatı Koru',
                'slug' => 'preserve_formatting',
                'feature_id' => 301,
                'group_id' => 5,
                'type' => 'checkbox',
                'placeholder' => null,
                'help_text' => 'HTML etiketleri, Markdown formatı ve linkleri koruyarak çeviri yap',
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'boolean']),
                'sort_order' => 2,
                'config' => json_encode([
                    'style' => 'switch',
                    'size' => 'default',
                    'color' => 'info',
                    'default_value' => true,
                    'icon' => 'fas fa-code'
                ]),
            ],

            // Kültürel Uyarlama
            [
                'id' => 12,
                'name' => 'Kültürel Uyarlama',
                'slug' => 'cultural_adaptation',
                'feature_id' => 301,
                'group_id' => 5,
                'type' => 'checkbox',
                'placeholder' => null,
                'help_text' => 'Sadece çeviri değil, hedef kültüre uygun uyarlama yap (daha uzun sürer)',
                'is_required' => false,
                'validation_rules' => json_encode(['nullable', 'boolean']),
                'sort_order' => 3,
                'config' => json_encode([
                    'style' => 'switch',
                    'size' => 'default',
                    'color' => 'warning',
                    'default_value' => false,
                    'icon' => 'fas fa-globe-americas',
                    'premium_feature' => true
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

        $this->command->info('✅ Translation Universal Input System seeder başarıyla tamamlandı.');
        $this->command->info('📊 Input Groups: ' . count($inputGroups));
        $this->command->info('📊 Feature Inputs: ' . count($featureInputs));
    }
}