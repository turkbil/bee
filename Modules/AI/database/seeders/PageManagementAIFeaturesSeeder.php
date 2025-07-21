<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureCategory;

class PageManagementAIFeaturesSeeder extends Seeder
{
    public function run()
    {
        // Sayfa Yönetimi kategorisini al
        $category = AIFeatureCategory::where('slug', 'sayfa-yoenetimi-10')->first();
        
        if (!$category) {
            $this->command->error('Sayfa Yönetimi kategorisi bulunamadı!');
            return;
        }
        
        $this->command->info('Kategori bulundu: ' . $category->title . ' (ID: ' . $category->ai_feature_category_id . ')');

        $features = [
            // SEO & ANALİZ ARAÇLARI
            [
                'name' => 'SEO Puanı Analizi',
                'slug' => 'seo-puan-analizi',
                'description' => 'Sayfa içeriğinin SEO performansını analiz eder ve puan verir',
                'emoji' => '📊',
                'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen sayfa içeriğini analiz et ve SEO puanını hesapla.',
                'helper_function' => 'ai_seo_score_analysis',
                'button_text' => 'SEO Puanla',
                'token_cost' => json_encode(['estimated' => 150, 'min' => 120, 'max' => 200]),
                'response_template' => json_encode([
                    'format' => 'seo_score',
                    'layout' => 'two_column',
                    'sections' => [
                        'hero_score' => ['type' => 'circular_score', 'position' => 'left', 'icon' => 'fas fa-chart-line'],
                        'analysis' => ['type' => 'analysis_items', 'position' => 'right'],
                        'recommendations' => ['type' => 'recommendation_cards', 'position' => 'full_width'],
                        'technical_details' => ['type' => 'collapsible', 'position' => 'full_width']
                    ],
                    'styling' => ['theme' => 'gradient', 'animations' => true, 'icons' => true, 'badges' => true],
                    'interactive' => ['expandable_sections' => true, 'copy_buttons' => true, 'action_buttons' => true]
                ])
            ],
            [
                'name' => 'Hızlı SEO Analizi',
                'slug' => 'hizli-seo-analizi',
                'description' => 'Sayfa başlığı, meta açıklaması ve içeriğin SEO analizini yapar',
                'emoji' => '🚀',
                'quick_prompt' => 'Sen bir SEO analiz uzmanısın. Sayfa içeriğinin hızlı SEO analizini yap.',
                'helper_function' => 'ai_quick_seo_analysis',
                'button_text' => 'Hızlı Analiz',
                'token_cost' => json_encode(['estimated' => 120, 'min' => 100, 'max' => 150]),
                'response_template' => json_encode([
                    'format' => 'quick_analysis',
                    'layout' => 'single_column',
                    'sections' => [
                        'summary' => ['type' => 'summary_card', 'position' => 'top'],
                        'key_points' => ['type' => 'highlight_list', 'position' => 'main'],
                        'quick_fixes' => ['type' => 'action_list', 'position' => 'bottom']
                    ],
                    'styling' => ['theme' => 'modern', 'compact' => true, 'icons' => true],
                    'interactive' => ['quick_actions' => true]
                ])
            ],
            [
                'name' => 'Anahtar Kelime Analizi',
                'slug' => 'anahtar-kelime-analizi',
                'description' => 'Sayfa içeriğindeki anahtar kelimeleri analiz eder ve önerilerde bulunur',
                'emoji' => '🔑',
                'quick_prompt' => 'Sen bir anahtar kelime uzmanısın. İçerikteki anahtar kelimeleri analiz et.',
                'helper_function' => 'ai_keyword_analysis',
                'button_text' => 'Anahtar Kelime',
                'token_cost' => json_encode(['estimated' => 130, 'min' => 110, 'max' => 160])
            ],

            // İÇERİK DÜZENLEME ARAÇLARI
            [
                'name' => 'İçerik Optimizasyonu',
                'slug' => 'icerik-optimizasyonu',
                'description' => 'Mevcut içeriği SEO ve okunabilirlik açısından optimize eder',
                'emoji' => '⚡',
                'quick_prompt' => 'Sen bir içerik editörüsün. Verilen içeriği optimize et ve iyileştir.',
                'helper_function' => 'ai_content_optimization',
                'button_text' => 'İçerik Optimize Et',
                'token_cost' => json_encode(['estimated' => 200, 'min' => 180, 'max' => 250])
            ],
            [
                'name' => 'İçerik Genişletme',
                'slug' => 'icerik-genisletme',
                'description' => 'Mevcut içeriği genişletir ve daha detaylandırır',
                'emoji' => '📝',
                'quick_prompt' => 'Sen bir içerik yazarısın. Verilen içeriği genişlet ve detaylandır.',
                'helper_function' => 'ai_content_expansion',
                'button_text' => 'İçeriği Genişlet',
                'token_cost' => json_encode(['estimated' => 250, 'min' => 220, 'max' => 300])
            ],
            [
                'name' => 'İçerik Özetleme',
                'slug' => 'icerik-ozetleme',
                'description' => 'Uzun içerikleri özetler ve ana noktaları çıkarır',
                'emoji' => '📄',
                'quick_prompt' => 'Sen bir özet uzmanısın. Verilen içeriği özetle ve ana noktaları çıkar.',
                'helper_function' => 'ai_content_summarize',
                'button_text' => 'İçerik Özetle',
                'token_cost' => json_encode(['estimated' => 180, 'min' => 160, 'max' => 220])
            ],

            // BAŞLIK & META ARAÇLARI
            [
                'name' => 'Başlık Üretici',
                'slug' => 'baslik-uretici',
                'description' => 'İçerik için SEO uyumlu ve çekici başlıklar üretir',
                'emoji' => '💡',
                'quick_prompt' => 'Sen bir başlık uzmanısın. Verilen içerik için SEO uyumlu başlıklar üret.',
                'helper_function' => 'ai_title_generator',
                'button_text' => 'Başlık Üret',
                'token_cost' => json_encode(['estimated' => 100, 'min' => 80, 'max' => 130])
            ],
            [
                'name' => 'Meta Açıklama Üretici',
                'slug' => 'meta-aciklama-uretici',
                'description' => 'Sayfa için SEO optimized meta açıklamaları oluşturur',
                'emoji' => '🏷️',
                'quick_prompt' => 'Sen bir meta açıklama uzmanısın. Verilen içerik için meta açıklama oluştur.',
                'helper_function' => 'ai_meta_description_generator',
                'button_text' => 'Meta Açıklama',
                'token_cost' => json_encode(['estimated' => 80, 'min' => 60, 'max' => 100])
            ],
            [
                'name' => 'Alt Başlık Önerileri',
                'slug' => 'alt-baslik-onerileri',
                'description' => 'İçerik için H2, H3 alt başlık önerileri sunar',
                'emoji' => '📋',
                'quick_prompt' => 'Sen bir içerik yapılandırma uzmanısın. İçerik için alt başlık önerileri sun.',
                'helper_function' => 'ai_subheading_suggestions',
                'button_text' => 'Alt Başlık',
                'token_cost' => json_encode(['estimated' => 120, 'min' => 100, 'max' => 150])
            ],

            // SAYFA GELİŞTİRME ARAÇLARI
            [
                'name' => 'Sayfa Geliştirme Önerileri',
                'slug' => 'sayfa-gelistirme-onerileri',
                'description' => 'Sayfanın genel performansı için iyileştirme önerileri sunar',
                'emoji' => '🔧',
                'quick_prompt' => 'Sen bir web geliştirme uzmanısın. Sayfa için iyileştirme önerileri sun.',
                'helper_function' => 'ai_page_improvement_suggestions',
                'button_text' => 'Sayfa Geliştir',
                'token_cost' => json_encode(['estimated' => 180, 'min' => 160, 'max' => 220])
            ],
            [
                'name' => 'Kullanıcı Deneyimi Analizi',
                'slug' => 'kullanici-deneyimi-analizi',
                'description' => 'Sayfa içeriğinin kullanıcı deneyimi açısından analizini yapar',
                'emoji' => '👥',
                'quick_prompt' => 'Sen bir UX uzmanısın. Sayfa içeriğinin kullanıcı deneyimini analiz et.',
                'helper_function' => 'ai_ux_analysis',
                'button_text' => 'UX Analiz',
                'token_cost' => json_encode(['estimated' => 160, 'min' => 140, 'max' => 200])
            ],
            [
                'name' => 'İçerik Kalite Skoru',
                'slug' => 'icerik-kalite-skoru',
                'description' => 'İçeriğin kalitesini değerlendirir ve puan verir',
                'emoji' => '⭐',
                'quick_prompt' => 'Sen bir içerik kalite uzmanısın. Verilen içeriğin kalitesini değerlendir.',
                'helper_function' => 'ai_content_quality_score',
                'button_text' => 'Kalite Skoru',
                'token_cost' => json_encode(['estimated' => 140, 'min' => 120, 'max' => 170])
            ],

            // ÇEVİRİ & DİL ARAÇLARI
            [
                'name' => 'Çoklu Dil Çevirisi',
                'slug' => 'coklu-dil-cevirisi',
                'description' => 'Sayfa içeriğini farklı dillere çevirir',
                'emoji' => '🌍',
                'quick_prompt' => 'Sen bir çeviri uzmanısın. Verilen içeriği hedef dile çevir.',
                'helper_function' => 'ai_multi_language_translation',
                'button_text' => 'Çevir',
                'token_cost' => json_encode(['estimated' => 200, 'min' => 180, 'max' => 250])
            ],
            [
                'name' => 'Dil Kalitesi Kontrolü',
                'slug' => 'dil-kalitesi-kontrolu',
                'description' => 'İçerikteki dil bilgisi hatalarını kontrol eder',
                'emoji' => '✏️',
                'quick_prompt' => 'Sen bir dil uzmanısın. İçerikteki dil bilgisi hatalarını kontrol et.',
                'helper_function' => 'ai_language_quality_check',
                'button_text' => 'Dil Kontrolü',
                'token_cost' => json_encode(['estimated' => 120, 'min' => 100, 'max' => 150])
            ],

            // REKABET & KARŞILAŞTIRMA
            [
                'name' => 'Rekabet Analizi',
                'slug' => 'rekabet-analizi',
                'description' => 'Sayfa içeriğinin rekabet durumunu analiz eder',
                'emoji' => '📊',
                'quick_prompt' => 'Sen bir rekabet analiz uzmanısın. İçeriğin rekabet gücünü analiz et.',
                'helper_function' => 'ai_competition_analysis',
                'button_text' => 'Rekabet Analizi',
                'token_cost' => json_encode(['estimated' => 170, 'min' => 150, 'max' => 200])
            ],
            [
                'name' => 'Trending Konu Önerileri',
                'slug' => 'trending-konu-onerileri',
                'description' => 'Güncel trend konularına göre içerik önerileri sunar',
                'emoji' => '📈',
                'quick_prompt' => 'Sen bir trend analiz uzmanısın. Güncel trend konularına göre önerilerde bulun.',
                'helper_function' => 'ai_trending_topics',
                'button_text' => 'Trend Konular',
                'token_cost' => json_encode(['estimated' => 150, 'min' => 120, 'max' => 200])
            ],

            // TEKNIK SEO ARAÇLARI
            [
                'name' => 'Schema Markup Önerileri',
                'slug' => 'schema-markup-onerileri',
                'description' => 'Sayfa için uygun schema markup önerileri sunar',
                'emoji' => '🔗',
                'quick_prompt' => 'Sen bir teknik SEO uzmanısın. Sayfa için schema markup önerileri sun.',
                'helper_function' => 'ai_schema_markup_suggestions',
                'button_text' => 'Schema Markup',
                'token_cost' => json_encode(['estimated' => 130, 'min' => 110, 'max' => 160])
            ],
            [
                'name' => 'Link Önerileri',
                'slug' => 'link-onerileri',
                'description' => 'İç bağlantı ve dış bağlantı önerileri sunar',
                'emoji' => '🔗',
                'quick_prompt' => 'Sen bir link stratejisi uzmanısın. İçerik için bağlantı önerileri sun.',
                'helper_function' => 'ai_link_suggestions',
                'button_text' => 'Link Önerileri',
                'token_cost' => json_encode(['estimated' => 110, 'min' => 90, 'max' => 140])
            ]
        ];

        foreach ($features as $index => $featureData) {
            $feature = AIFeature::firstOrCreate([
                'slug' => $featureData['slug']
            ], array_merge($featureData, [
                'ai_feature_category_id' => $category->ai_feature_category_id,
                'status' => 'active',
                'is_system' => false,
                'is_featured' => $index < 6, // İlk 6 tanesi featured
                'sort_order' => $index + 1,
                'complexity_level' => 'intermediate',
                'response_template' => json_encode([
                    'format' => 'modern_card',
                    'layout' => 'two_column',
                    'sections' => [
                        'hero_score' => ['type' => 'score_card', 'position' => 'left', 'icon' => true],
                        'analysis' => ['type' => 'expandable_list', 'position' => 'right'],
                        'recommendations' => ['type' => 'action_cards', 'position' => 'full_width'],
                        'technical_details' => ['type' => 'collapsible', 'position' => 'full_width']
                    ],
                    'styling' => [
                        'theme' => 'gradient',
                        'animations' => true,
                        'icons' => true,
                        'badges' => true
                    ],
                    'interactive' => [
                        'expandable_sections' => true,
                        'copy_buttons' => true,
                        'action_buttons' => true
                    ]
                ])
            ]));
            $this->command->info('Feature eklendi/bulundu: ' . $feature->name . ' (ID: ' . $feature->id . ')');
        }

        $this->command->info('✅ Sayfa Yönetimi için ' . count($features) . ' AI feature eklendi!');
    }
}