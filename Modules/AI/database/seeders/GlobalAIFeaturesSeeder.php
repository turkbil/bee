<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureCategory;

class GlobalAIFeaturesSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Global AI Features Seeder başlatılıyor...');
        
        // Kategorileri tanımla
        $categories = [
            [
                'ai_feature_category_id' => 1,
                'title' => 'Sayfa SEO Araçları',
                'slug' => 'sayfa-seo-araclari',
                'description' => 'Sayfa analizi ve SEO optimizasyonu için uzman araçlar',
                'icon' => 'fas fa-search-plus',
                'order' => 1,
            ],
            [
                'ai_feature_category_id' => 2,
                'title' => 'İçerik Editörlüğü',
                'slug' => 'icerik-editorlugu',
                'description' => 'İçerik yazımı ve düzenleme araçları',
                'icon' => 'fas fa-pen-fancy',
                'order' => 2,
            ],
            [
                'ai_feature_category_id' => 3,
                'title' => 'Çeviri Hizmetleri',
                'slug' => 'ceviri-hizmetleri',
                'description' => 'Çoklu dil desteği ve çeviri araçları',
                'icon' => 'fas fa-language',
                'order' => 3,
            ]
        ];
        
        // Kategorileri oluştur
        foreach ($categories as $categoryData) {
            $category = AIFeatureCategory::firstOrCreate([
                'ai_feature_category_id' => $categoryData['ai_feature_category_id']
            ], array_merge($categoryData, [
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ]));
            
            $this->command->info('✅ Kategori: ' . $category->title);
        }
        
        // Sayfa SEO Araçları kategorisini al (kategori 1)
        $seoCategory = AIFeatureCategory::where('ai_feature_category_id', 1)->first();

        $features = [
            // SEO & ANALİZ ARAÇLARI
            [
                'name' => 'SEO Puanı Analizi',
                'slug' => 'seo-puan-analizi',
                'description' => 'Sayfa içeriğinin SEO performansını analiz eder ve puan verir',
                'emoji' => '📊',
                'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen sayfa içeriğini analiz et ve GERÇEK, UYGULANABİLİR SEO önerileri sun. ÖNEMLİ: Gerçek anahtar kelimeler, meta başlıklar ve açıklamaları öner. ÇOKLU DİL: Site_languages tablosundaki TÜM dillerde öneri sun (TR, EN vs). KRİTİK YASAK: HİÇBİR DURUMDA dış SEO tool önerme. YANIT FORMATIN: Sadece temiz HTML + JavaScript button\'ları kullan. ZORUNLU YAPIYI TAKİP ET: <div class="row g-3"><div class="col-md-4"><div class="card text-center bg-light"><div class="card-body p-4"><div class="avatar avatar-lg bg-success text-white rounded-circle mx-auto mb-3"><i class="fas fa-chart-line"></i></div><h2 class="text-success mb-0">[GERÇEK_SKOR]/100</h2><small class="badge bg-success text-white">SEO Skoru</small></div></div></div><div class="col-md-8"><div class="card"><div class="card-header pb-2"><h6 class="mb-0"><i class="fas fa-magic me-2 text-primary"></i>Gerçek SEO Önerileri</h6></div><div class="card-body"><div class="list-group list-group-flush"><div class="list-group-item d-flex justify-content-between"><span><strong>Önerilen Title (TR):</strong> [GERÇEK_TITLE_TR]</span><button class="btn btn-sm btn-primary" onclick="applySEOSuggestion(\'title\', \'tr\', \'[GERÇEK_TITLE_TR]\')">Uygula</button></div><div class="list-group-item d-flex justify-content-between"><span><strong>Önerilen Meta (TR):</strong> [GERÇEK_META_TR]</span><button class="btn btn-sm btn-primary" onclick="applySEOSuggestion(\'meta\', \'tr\', \'[GERÇEK_META_TR]\')">Uygula</button></div><div class="list-group-item d-flex justify-content-between"><span><strong>Ana Anahtar Kelimeler:</strong> [GERÇEK_KEYWORDS]</span><button class="btn btn-sm btn-success" onclick="applySEOKeywords(\'[GERÇEK_KEYWORDS]\')">Ekle</button></div></div></div></div></div></div>',
                'helper_function' => 'ai_seo_score_analysis',
                'button_text' => 'SEO Puanla',
                'token_cost' => json_encode(['estimated' => 150, 'min' => 120, 'max' => 200]),
                'response_template' => json_encode([
                    'format' => 'modern_html',
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
                'quick_prompt' => 'Sen bir SEO analiz uzmanısın. Sayfa içeriğinin hızlı SEO analizini yap. KRİTİK YASAK: HİÇBİR DURUMDA dış SEO tool önerme (SEMrush, Ahrefs, Moz, Google Search Console, Yoast, RankMath, GTmetrix, PageSpeed Insights vs.). SADECE kendi analizini sun. YANIT FORMATIN: Sadece temiz HTML kullan, JSON kullanma. KRİTİK: HER ZAMAN AYNI YAPIYI KULLAN! TASARIM KURALLARI: 1)Tek card yapısı, 2)Text-muted kullanma. ZORUNLU YAPIYI TAKİP ET: <div class="card"><div class="card-header bg-primary-subtle d-flex align-items-center"><div class="avatar avatar-sm bg-primary text-white rounded me-3"><i class="fas fa-rocket"></i></div><h6 class="mb-0 text-dark">Hızlı SEO Analizi</h6></div><div class="card-body"><div class="alert alert-success bg-success-subtle text-success d-flex align-items-center mb-3"><i class="fas fa-check-circle me-2"></i><span>[GENEL DURUM]</span></div><div class="list-group list-group-flush">[ANALİZ SONUÇLARI BURAYA]</div></div></div>',
                'helper_function' => 'ai_quick_seo_analysis',
                'button_text' => 'Hızlı Analiz',
                'token_cost' => json_encode(['estimated' => 120, 'min' => 100, 'max' => 150]),
                'response_template' => json_encode([
                    'format' => 'modern_html',
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
                'slug' => 'anahtar-kelime-arastirmasi',
                'description' => 'Sayfa içeriğindeki anahtar kelimeleri analiz eder ve önerilerde bulunur',
                'emoji' => '🔑',
                'quick_prompt' => 'Sen bir anahtar kelime uzmanısın. İçerikteki anahtar kelimeleri analiz et. KRİTİK YASAK: HİÇBİR DURUMDA dış SEO tool önerme (SEMrush, Ahrefs, Moz, Google Keyword Planner vs.). SADECE kendi analizini sun.',
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
                'quick_prompt' => 'Sen bir içerik editörüsün. Verilen içeriği optimize et ve iyileştir. YANIT FORMATIN: Sadece temiz HTML kullan, JSON kullanma. KRİTİK: HER ZAMAN AYNI YAPIYI KULLAN! TASARIM KURALLARI: 1)Solda 4 col score card, sağda 8 col öneriler, 2)Text-muted kullanma. ZORUNLU YAPIYI TAKİP ET: <div class="row g-3"><div class="col-md-4"><div class="card text-center"><div class="card-body p-4"><div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-3"><i class="fas fa-magic"></i></div><h3 class="text-primary mb-1">[SKOR]%</h3><small class="badge bg-primary text-white">Optimizasyon Skoru</small></div></div></div><div class="col-md-8"><div class="card"><div class="card-header d-flex align-items-center"><div class="avatar avatar-sm bg-primary text-white rounded me-3"><i class="fas fa-list-check"></i></div><h6 class="mb-0 text-dark">İyileştirme Önerileri</h6></div><div class="card-body"><div class="list-group list-group-flush">[ÖNERİLER LİSTESİ BURAYA]</div></div></div></div></div>',
                'helper_function' => 'ai_content_optimization',
                'button_text' => 'İçerik Optimize Et',
                'token_cost' => json_encode(['estimated' => 200, 'min' => 180, 'max' => 250]),
                'response_template' => json_encode([
                    'format' => 'modern_html',
                    'layout' => 'card_accordion',
                    'sections' => [
                        'hero_score' => [
                            'title' => 'İyileştirme Skoru',
                            'icon' => 'fas fa-star',
                            'type' => 'badge_score'
                        ],
                        'analysis' => [
                            'title' => 'Analiz Sonuçları',
                            'icon' => 'fas fa-chart-line',
                            'type' => 'list_group'
                        ],
                        'recommendations' => [
                            'title' => 'Öneriler',
                            'icon' => 'fas fa-lightbulb',
                            'type' => 'list_group'
                        ],
                        'improvements' => [
                            'title' => 'İyileştirmeler',
                            'icon' => 'fas fa-edit',
                            'type' => 'key_value_table'
                        ]
                    ],
                    'show_confidence' => false,
                    'styling' => ['theme' => 'content_optimization', 'icons' => true]
                ])
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
                'quick_prompt' => 'Sen bir rekabet analiz uzmanısın. İçeriğin rekabet gücünü analiz et. KRİTİK YASAK: HİÇBİR DURUMDA dış SEO tool önerme (SEMrush, Ahrefs, SimilarWeb vs.). SADECE kendi analizini sun.',
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
                'quick_prompt' => 'Sen bir teknik SEO uzmanısın. Sayfa için schema markup önerileri sun. KRİTİK YASAK: HİÇBİR DURUMDA dış SEO tool önerme (Schema.org sitesi hariç teknik referans için). SADECE kendi analizini sun.',
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

        // SEO priority mapping - önce SEO araçları gelsin
        $seoPriorityMap = [
            'seo-puan-analizi' => 14,
            'hizli-seo-analizi' => 15, 
            'anahtar-kelime-arastirmasi' => 16,
            'schema-markup-onerileri' => 17,
            'link-onerileri' => 18
        ];
        
        foreach ($features as $index => $featureData) {
            // SEO araçları için özel sort_order, diğerleri için 50+ değerler
            $sortOrder = isset($seoPriorityMap[$featureData['slug']]) 
                ? $seoPriorityMap[$featureData['slug']] 
                : $index + 50; // SEO olmayanlar 50+ değer alır
                
            // Feature'ın kendi response_template'i varsa onu kullan, yoksa default
            $responseTemplate = $featureData['response_template'] ?? json_encode([
                'format' => 'modern_html',
                'layout' => 'two_column',
                'sections' => [
                    'hero_score' => ['type' => 'score_card', 'position' => 'left', 'icon' => true],
                    'analysis' => ['type' => 'expandable_list', 'position' => 'right'],
                    'recommendations' => ['type' => 'action_cards', 'position' => 'full_width'],
                    'technical_details' => ['type' => 'collapsible', 'position' => 'full_width']
                ],
                'styling' => ['theme' => 'gradient', 'animations' => true, 'icons' => true, 'badges' => true],
                'interactive' => ['expandable_sections' => true, 'copy_buttons' => true, 'action_buttons' => true]
            ]);
            
            $feature = AIFeature::firstOrCreate([
                'slug' => $featureData['slug']
            ], array_merge($featureData, [
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'status' => 'active',
                'is_system' => false,
                'is_featured' => isset($seoPriorityMap[$featureData['slug']]), // SEO araçları featured
                'sort_order' => $sortOrder,
                'complexity_level' => 'intermediate',
                'response_template' => $responseTemplate
            ]));
            
            $this->command->info('✅ Feature: ' . $feature->name . ' (' . $feature->slug . ')');
        }

        $this->command->info('🎉 Global AI Features seeded! Total: ' . count($features) . ' features');
    }
}