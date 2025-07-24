<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureCategory;
use Modules\AI\App\Models\Prompt;

class AIPageManagementFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Page Management AI Features seeding started...');

        // Sayfa SEO Araçları kategorisini kullan (kategori ID 1)
        $pageCategory = AIFeatureCategory::firstOrCreate([
            'ai_feature_category_id' => 1
        ], [
            'ai_feature_category_id' => 1,
            'title' => 'Sayfa SEO Araçları',
            'slug' => 'sayfa-seo-araclari',
            'description' => 'Sayfa analizi ve SEO optimizasyonu için uzman araçlar',
            'icon' => 'fas fa-search-plus',
            'order' => 1,
            'is_active' => true,
            'parent_id' => null,
            'has_subcategories' => false
        ]);

        // Page Management AI Prompts oluştur
        $prompts = [
            [
                'name' => 'İçerik Optimizasyon Uzmanı',
                'content' => 'Sen profesyonel bir içerik optimizasyon uzmanısın. Web sayfası içeriklerini SEO ve kullanıcı deneyimi açısından analiz edip optimize ediyorsun. Başlıkları, açıklamaları ve içerikleri daha etkili hale getirmek için somut öneriler veriyorsun. Anahtar kelime yoğunluğu, okunabilirlik, meta etiketler ve sayfa yapısı konularında uzmansın.',
                'prompt_type' => 'feature',
                'priority' => 1,
                'prompt_category' => 'expert_knowledge',
                'is_active' => true
            ],
            [
                'name' => 'Anahtar Kelime Araştırma Uzmanı',
                'content' => 'Sen deneyimli bir anahtar kelime araştırma uzmanısın. Web sayfalarının içeriğini analiz ederek en uygun anahtar kelimeleri tespit ediyorsun. Rekabet analizi, arama hacmi, kullanıcı niyeti ve semantic SEO konularında derinlemesine bilgin var. Hedef kitleye uygun long-tail ve short-tail anahtar kelimeler öneriyorsun.',
                'prompt_type' => 'feature',
                'priority' => 2,
                'prompt_category' => 'expert_knowledge',
                'is_active' => true
            ],
            [
                'name' => 'Çeviri ve Lokalizasyon Uzmanı',
                'content' => 'Sen profesyonel bir çeviri ve lokalizasyon uzmanısın. Çoklu dil desteği olan web sitelerinde içerikleri sadece çevirmekle kalmayıp, hedef kültüre uygun hale getiriyorsun. SEO açısından uygun çeviriler yapıyor, yerel arama trendlerini dikkate alıyor ve kültürel farklılıkları gözetiyorsun.',
                'prompt_type' => 'feature',  
                'priority' => 3,
                'prompt_category' => 'expert_knowledge',
                'is_active' => true
            ],
            [
                'name' => 'AI Asistan Sohbet Uzmanı',
                'content' => 'Sen yardımsever ve bilgili bir AI asistanısın. Sayfa yönetimi konularında kullanıcılara rehberlik ediyorsun. SEO, içerik yazımı, sayfa optimizasyonu, teknik web konuları hakkında açık ve anlaşılır yanıtlar veriyorsun. Kullanıcının seviyesine uygun dilde konuşuyor ve pratik öneriler sunuyorsun.',
                'prompt_type' => 'feature',
                'priority' => 4,
                'prompt_category' => 'expert_knowledge', 
                'is_active' => true
            ]
        ];

        foreach ($prompts as $promptData) {
            Prompt::firstOrCreate(['name' => $promptData['name']], $promptData);
        }

        // AI Features oluştur
        $features = [
            [
                'name' => 'İçerik Optimizasyonu',
                'slug' => 'icerik-optimizasyonu',
                'description' => 'Sayfa başlıklarını, açıklamalarını ve içeriklerini SEO ve kullanıcı deneyimi açısından optimize eder',
                'emoji' => '✨',
                'icon' => 'fas fa-edit',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'content',
                'complexity_level' => 'intermediate',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'language' => ['type' => 'string', 'required' => false, 'default' => 'tr', 'description' => 'İçerik dili']
                ]),
                'helper_returns' => json_encode([
                    'suggestions' => [
                        'type' => 'object',
                        'properties' => [
                            'title_suggestions' => ['type' => 'array', 'description' => 'Başlık önerileri'],
                            'content_improvements' => ['type' => 'array', 'description' => 'İçerik iyileştirmeleri'],
                            'seo_recommendations' => ['type' => 'array', 'description' => 'SEO önerileri']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir içerik optimizasyon uzmanısın. Verilen sayfa içeriğini analiz et ve SEO ve kullanıcı deneyimi açısından iyileştirme önerileri sun.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen profesyonel bir içerik optimizasyon uzmanısın. Web sayfası içeriklerini SEO ve kullanıcı deneyimi açısından analiz edip optimize ediyorsun. Başlıkları, açıklamaları ve içerikleri daha etkili hale getirmek için somut öneriler veriyorsun. Anahtar kelime yoğunluğu, okunabilirlik, meta etiketler ve sayfa yapısı konularında uzmansın.',
                'response_template' => json_encode([
                    'format' => 'structured_suggestions',
                    'show_scores' => true,
                    'include_examples' => true,
                    'sections' => ['title', 'content', 'seo', 'readability']
                ]),
                'helper_function' => 'optimizePageContent',
                'button_text' => 'İçeriği Optimize Et',
                'helper_description' => 'Sayfa içeriğini SEO ve kullanıcı dostu hale getirir',
                'input_placeholder' => 'Optimize edilecek sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'medium',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Blog yazısı başlığı ve içeriği', 'output' => 'SEO optimizasyonlu öneriler']
                ])
            ],
            [
                'name' => 'Anahtar Kelime Araştırması',
                'slug' => 'anahtar-kelime-arastirmasi',
                'description' => 'Sayfa içeriğine uygun anahtar kelimeleri araştırır ve önerir',
                'emoji' => '🔑',
                'icon' => 'fas fa-key',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'seo',
                'complexity_level' => 'advanced',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'language' => ['type' => 'string', 'required' => false, 'default' => 'tr'],
                    'industry' => ['type' => 'string', 'required' => false, 'description' => 'Sektör bilgisi']
                ]),
                'helper_returns' => json_encode([
                    'keywords' => [
                        'type' => 'object',
                        'properties' => [
                            'primary_keywords' => ['type' => 'array', 'description' => 'Ana anahtar kelimeler'],
                            'secondary_keywords' => ['type' => 'array', 'description' => 'İkincil anahtar kelimeler'],
                            'long_tail_keywords' => ['type' => 'array', 'description' => 'Uzun kuyruk anahtar kelimeler']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir anahtar kelime araştırma uzmanısın. Verilen içerik için en uygun anahtar kelimeleri tespit et ve kategorize et.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen deneyimli bir anahtar kelime araştırma uzmanısın. Web sayfalarının içeriğini analiz ederek en uygun anahtar kelimeleri tespit ediyorsun. Rekabet analizi, arama hacmi, kullanıcı niyeti ve semantic SEO konularında derinlemesine bilgin var. Hedef kitleye uygun long-tail ve short-tail anahtar kelimeler öneriyorsun.',
                'response_template' => json_encode([
                    'format' => 'keyword_categories',
                    'show_competition' => true,
                    'include_suggestions' => true,
                    'sections' => ['primary', 'secondary', 'long_tail', 'semantic']
                ]),
                'helper_function' => 'researchKeywords',
                'button_text' => 'Anahtar Kelime Araştır',
                'helper_description' => 'İçeriğe uygun anahtar kelimeleri bulur',
                'input_placeholder' => 'Anahtar kelime araştırması yapılacak içerik...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'medium',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'E-ticaret sitesi için ürün açıklaması', 'output' => 'Kategorize edilmiş anahtar kelimeler']
                ])
            ],
            [
                'name' => 'Çevirmen',
                'slug' => 'cevirmen',
                'description' => 'Sayfa içeriklerini diğer dillere profesyonel şekilde çevirir',
                'emoji' => '🌍',
                'icon' => 'fas fa-language',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'translation',
                'complexity_level' => 'intermediate',
                'input_validation' => json_encode([
                    'source_text' => ['type' => 'string', 'required' => true, 'description' => 'Çevrilecek metin'],
                    'source_language' => ['type' => 'string', 'required' => true, 'description' => 'Kaynak dil'],
                    'target_language' => ['type' => 'string', 'required' => true, 'description' => 'Hedef dil'],
                    'content_type' => ['type' => 'string', 'required' => false, 'default' => 'web_page']
                ]),
                'helper_returns' => json_encode([
                    'translation' => [
                        'type' => 'object',
                        'properties' => [
                            'translated_text' => ['type' => 'string', 'description' => 'Çevrilmiş metin'],
                            'confidence_score' => ['type' => 'number', 'description' => 'Güven skoru'],
                            'notes' => ['type' => 'array', 'description' => 'Çeviri notları']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen profesyonel bir çevirmensin. Verilen metni hedef dile SEO uyumlu şekilde çevir.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen profesyonel bir çeviri ve lokalizasyon uzmanısın. Çoklu dil desteği olan web sitelerinde içerikleri sadece çevirmekle kalmayıp, hedef kültüre uygun hale getiriyorsun. SEO açısından uygun çeviriler yapıyor, yerel arama trendlerini dikkate alıyor ve kültürel farklılıkları gözetiyorsun.',
                'response_template' => json_encode([
                    'format' => 'translation_result',
                    'show_confidence' => true,
                    'include_notes' => true,
                    'preserve_formatting' => true
                ]),
                'helper_function' => 'translateContent',
                'button_text' => 'Çevir',
                'helper_description' => 'İçeriği diğer dillere çevirir',
                'input_placeholder' => 'Çevrilecek metin...',
                'status' => 'active',
                'response_format' => 'text',
                'response_length' => 'variable',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Türkçe makale TR->EN', 'output' => 'İngilizce çeviri + güven skoru']
                ])
            ],
            [
                'name' => 'Otomatik Optimize',
                'slug' => 'otomatik-optimize',
                'description' => 'Sayfa içeriğini tek tıkla otomatik olarak optimize eder',
                'emoji' => '⚡',
                'icon' => 'fas fa-magic',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'optimization',
                'complexity_level' => 'advanced',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'language' => ['type' => 'string', 'required' => false, 'default' => 'tr']
                ]),
                'helper_returns' => json_encode([
                    'optimization' => [
                        'type' => 'object',
                        'properties' => [
                            'optimized_title' => ['type' => 'string', 'description' => 'Optimize edilmiş başlık'],
                            'optimized_content' => ['type' => 'string', 'description' => 'Optimize edilmiş içerik'],
                            'improvements' => ['type' => 'array', 'description' => 'Yapılan iyileştirmeler']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir otomatik optimizasyon uzmanısın. Verilen içeriği SEO ve kullanıcı deneyimi açısından optimize et.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen otomatik optimizasyon konusunda uzman bir AI\'sın. Web sayfası içeriklerini SEO skorunu artıracak, okunabilirliği iyileştirecek ve kullanıcı deneyimini optimize edecek şekilde otomatik olarak iyileştiriyorsun. Başlık, meta açıklama, içerik yapısı ve anahtar kelime dağılımını optimize ediyorsun.',
                'response_template' => json_encode([
                    'format' => 'optimization_result',
                    'show_before_after' => true,
                    'include_scores' => true,
                    'sections' => ['title', 'content', 'improvements']
                ]),
                'helper_function' => 'autoOptimize',
                'button_text' => 'Otomatik Optimize Et',
                'helper_description' => 'Sayfa içeriğini otomatik olarak optimize eder',
                'input_placeholder' => 'Optimize edilecek sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'long',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Optimize edilmemiş sayfa içeriği', 'output' => 'SEO optimizasyonlu yeni içerik']
                ])
            ],
            [
                'name' => 'Rekabet Analizi',
                'slug' => 'rekabet-analizi',
                'description' => 'Benzer sayfa ve rakiplerin analiz ederek karşılaştırma yapar',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-bar',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'analysis',
                'complexity_level' => 'advanced',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'industry' => ['type' => 'string', 'required' => false, 'description' => 'Sektör bilgisi']
                ]),
                'helper_returns' => json_encode([
                    'analysis' => [
                        'type' => 'object',
                        'properties' => [
                            'competitive_position' => ['type' => 'string', 'description' => 'Rekabet durumu'],
                            'improvement_areas' => ['type' => 'array', 'description' => 'Gelişim alanları'],
                            'strengths' => ['type' => 'array', 'description' => 'Güçlü yönler']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir rekabet analizi uzmanısın. Verilen içeriği benzer rakiplerle karşılaştırarak analiz et.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen rekabet analizi konusunda uzman bir AI\'sın. Web sayfa içeriklerini analiz ederek benzer rakiplerle karşılaştırma yapıyorsun. İçerik kalitesi, SEO optimizasyonu, anahtar kelime kullanımı ve kullanıcı deneyimi açısından rekabet pozisyonu belirliyor ve iyileştirme önerileri sunuyorsun.',
                'response_template' => json_encode([
                    'format' => 'competitive_analysis',
                    'show_comparison' => true,
                    'include_recommendations' => true,
                    'sections' => ['position', 'strengths', 'weaknesses', 'opportunities']
                ]),
                'helper_function' => 'competitorAnalysis',
                'button_text' => 'Rekabet Analizi Yap',
                'helper_description' => 'Rakiplerle karşılaştırmalı analiz yapar',
                'input_placeholder' => 'Analiz edilecek sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'long',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'E-ticaret ürün sayfası', 'output' => 'Rakip analizi ve iyileştirme önerileri']
                ])
            ],
            [
                'name' => 'İçerik Kalite Skoru',
                'slug' => 'icerik-kalite-skoru',
                'description' => 'Sayfa içeriğinin kalitesini değerlendirir ve puanlar',
                'emoji' => '⭐',
                'icon' => 'fas fa-star',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'analysis',
                'complexity_level' => 'intermediate',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'language' => ['type' => 'string', 'required' => false, 'default' => 'tr']
                ]),
                'helper_returns' => json_encode([
                    'quality_score' => [
                        'type' => 'object',
                        'properties' => [
                            'overall_score' => ['type' => 'number', 'description' => 'Genel kalite skoru'],
                            'readability_score' => ['type' => 'number', 'description' => 'Okunabilirlik skoru'],
                            'seo_score' => ['type' => 'number', 'description' => 'SEO skoru'],
                            'detailed_feedback' => ['type' => 'array', 'description' => 'Detaylı geri bildirim']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir içerik kalite değerlendirme uzmanısın. Verilen içeriğin kalitesini puanla ve analiz et.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen içerik kalitesi değerlendirme konusunda uzman bir AI\'sın. Web sayfa içeriklerini okunabilirlik, SEO uyumluluğu, yapısal kalite, hedef kitle uygunluğu ve genel etkinlik açısından analiz edip puanlıyorsun. 100 üzerinden detaylı skorlama ve iyileştirme önerileri sunuyorsun.',
                'response_template' => json_encode([
                    'format' => 'quality_assessment',
                    'show_scores' => true,
                    'include_breakdown' => true,
                    'sections' => ['overall', 'readability', 'seo', 'structure', 'engagement']
                ]),
                'helper_function' => 'contentQualityScore',
                'button_text' => 'Kalite Skorunu Hesapla',
                'helper_description' => 'İçerik kalitesini değerlendirir ve puanlar',
                'input_placeholder' => 'Değerlendirilecek sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'medium',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Blog makalesi', 'output' => 'Kalite skoru ve iyileştirme önerileri']
                ])
            ],
            [
                'name' => 'Schema Markup Üretici',
                'slug' => 'schema-markup-uretici',
                'description' => 'Sayfa içeriği için yapılandırılmış veri önerileri oluşturur',
                'emoji' => '🔗',
                'icon' => 'fas fa-code',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'technical_seo',
                'complexity_level' => 'advanced',
                'input_validation' => json_encode([
                    'title' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa başlığı'],
                    'content' => ['type' => 'string', 'required' => true, 'description' => 'Sayfa içeriği'],
                    'page_type' => ['type' => 'string', 'required' => false, 'default' => 'WebPage', 'description' => 'Sayfa türü']
                ]),
                'helper_returns' => json_encode([
                    'schema_markup' => [
                        'type' => 'object',
                        'properties' => [
                            'json_ld' => ['type' => 'string', 'description' => 'JSON-LD formatında schema'],
                            'microdata' => ['type' => 'string', 'description' => 'Microdata formatında schema'],
                            'recommendations' => ['type' => 'array', 'description' => 'Schema önerileri']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen bir schema markup uzmanısın. Verilen sayfa için uygun yapılandırılmış veri önerileri oluştur.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen yapılandırılmış veri (schema markup) konusunda uzman bir AI\'sın. Web sayfa içeriklerini analiz ederek en uygun schema.org yapılarını belirliyor ve JSON-LD formatında kod önerileri sunuyorsun. Arama motorlarının sayfa içeriğini daha iyi anlamasını sağlayacak yapılandırılmış veri çözümleri üretiyorsun.',
                'response_template' => json_encode([
                    'format' => 'schema_suggestions',
                    'show_code' => true,
                    'include_examples' => true,
                    'sections' => ['json_ld', 'microdata', 'validation', 'benefits']
                ]),
                'helper_function' => 'generateSchemaMarkup',
                'button_text' => 'Schema Markup Oluştur',
                'helper_description' => 'Yapılandırılmış veri önerileri oluşturur',
                'input_placeholder' => 'Schema markup oluşturulacak sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'code',
                'response_length' => 'long',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Ürün sayfası içeriği', 'output' => 'JSON-LD schema markup kodu']
                ])
            ],
            [
                'name' => 'Çoklu Dil Çevirisi',
                'slug' => 'coklu-dil-cevirisi',
                'description' => 'Sayfa içeriğini birden fazla dile aynı anda çevirir',
                'emoji' => '🌍',
                'icon' => 'fas fa-globe',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'translation',
                'complexity_level' => 'advanced',
                'input_validation' => json_encode([
                    'source_text' => ['type' => 'string', 'required' => true, 'description' => 'Çevrilecek metin'],
                    'source_language' => ['type' => 'string', 'required' => false, 'default' => 'tr', 'description' => 'Kaynak dil'],
                    'target_languages' => ['type' => 'array', 'required' => true, 'description' => 'Hedef diller'],
                    'preserve_formatting' => ['type' => 'boolean', 'required' => false, 'default' => true]
                ]),
                'helper_returns' => json_encode([
                    'translations' => [
                        'type' => 'object',
                        'properties' => [
                            'results' => ['type' => 'object', 'description' => 'Dil kodları ve çevirileri'],
                            'quality_scores' => ['type' => 'object', 'description' => 'Çeviri kalite skorları'],
                            'notes' => ['type' => 'array', 'description' => 'Çeviri notları']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen çoklu dil çeviri uzmanısın. Verilen metni birden fazla dile aynı anda profesyonel şekilde çevir.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen çoklu dil çeviri konusunda uzman bir AI\'sın. Web sayfa içeriklerini birden fazla dile aynı anda çeviriyorsun. Her dil için kültürel adaptasyon, SEO uyumluluğu ve yerel arama trendlerini gözetiyorsun. Çeviri kalitesini değerlendirip güven skorları sunuyorsun.',
                'response_template' => json_encode([
                    'format' => 'multi_language_result',
                    'show_quality_scores' => true,
                    'include_notes' => true,
                    'sections' => ['translations', 'quality', 'recommendations']
                ]),
                'helper_function' => 'translateMultiLanguage',
                'button_text' => 'Çoklu Dil Çevirisi',
                'helper_description' => 'İçeriği birden fazla dile çevirir',
                'input_placeholder' => 'Çevrilecek sayfa içeriği...',
                'status' => 'active',
                'response_format' => 'structured',
                'response_length' => 'long',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Türkçe makale -> EN, DE, FR', 'output' => 'Üç dilde çeviri + kalite skorları']
                ])
            ],
            [
                'name' => 'AI Asistan Sohbet',
                'slug' => 'ai-asistan-sohbet',
                'description' => 'Sayfa yönetimi konularında genel yardım ve rehberlik sağlar',
                'emoji' => '💬',
                'icon' => 'fas fa-comments',
                'ai_feature_category_id' => $pageCategory->ai_feature_category_id,
                'category' => 'assistant',
                'complexity_level' => 'beginner',
                'input_validation' => json_encode([
                    'user_message' => ['type' => 'string', 'required' => true, 'description' => 'Kullanıcı mesajı'],
                    'page_title' => ['type' => 'string', 'required' => false, 'description' => 'Sayfa başlığı'],
                    'page_content' => ['type' => 'string', 'required' => false, 'description' => 'Sayfa içeriği'],
                    'current_language' => ['type' => 'string', 'required' => false, 'default' => 'tr'],
                    'conversation_type' => ['type' => 'string', 'required' => false, 'default' => 'general']
                ]),
                'helper_returns' => json_encode([
                    'response' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'description' => 'AI yanıtı'],
                            'suggestions' => ['type' => 'array', 'description' => 'Ek öneriler'],
                            'related_actions' => ['type' => 'array', 'description' => 'İlgili işlemler']
                        ]
                    ]
                ]),
                'quick_prompt' => 'Sen yardımsever bir AI asistanısın. Sayfa yönetimi konularında kullanıcıya yardımcı ol.',
                'has_custom_prompt' => true,
                'custom_prompt' => 'Sen yardımsever ve bilgili bir AI asistanısın. Sayfa yönetimi konularında kullanıcılara rehberlik ediyorsun. SEO, içerik yazımı, sayfa optimizasyonu, teknik web konuları hakkında açık ve anlaşılır yanıtlar veriyorsun. Kullanıcının seviyesine uygun dilde konuşuyor ve pratik öneriler sunuyorsun.',
                'response_template' => json_encode([
                    'format' => 'conversational',
                    'show_suggestions' => true,
                    'include_actions' => true,
                    'friendly_tone' => true
                ]),
                'helper_function' => 'aiAssistantChat',
                'button_text' => 'Sohbet Et',
                'helper_description' => 'AI asistanı ile sayfa yönetimi hakkında konuş',
                'input_placeholder' => 'AI asistanına bir soru sorun...',
                'status' => 'active',
                'response_format' => 'markdown',
                'response_length' => 'medium',
                'requires_input' => true,
                'usage_examples' => json_encode([
                    ['input' => 'Bu sayfanın SEO skorunu nasıl artırabilirim?', 'output' => 'Detaylı SEO önerileri ve eylem adımları']
                ])
            ]
        ];

        // SEO araçları öncelik haritası
        $seoPriorityMap = [
            'icerik-optimizasyonu' => 19,
            'anahtar-kelime-arastirmasi' => 20,
            'otomatik-optimize' => 21,
            'rekabet-analizi' => 22,
            'icerik-kalite-skoru' => 23,
            'schema-markup-uretici' => 24
        ];
        
        foreach ($features as $index => $featureData) {
            // SEO araçları için özel sort_order, diğerleri için 100+ değerler
            $sortOrder = isset($seoPriorityMap[$featureData['slug']]) 
                ? $seoPriorityMap[$featureData['slug']] 
                : $index + 100; // SEO olmayanlar 100+ değer alır
                
            $featureData['sort_order'] = $sortOrder;
            $featureData['is_featured'] = isset($seoPriorityMap[$featureData['slug']]); // SEO araçları featured
            
            AIFeature::firstOrCreate(
                ['slug' => $featureData['slug']], 
                $featureData
            );
        }

        $this->command->info('✅ Page Management AI Features created successfully!');
        $this->command->info('📊 Created features:');
        $this->command->info('   - İçerik Optimizasyonu (icerik-optimizasyonu)');
        $this->command->info('   - Anahtar Kelime Araştırması (anahtar-kelime-arastirmasi)');
        $this->command->info('   - Çevirmen (cevirmen)');
        $this->command->info('   - Otomatik Optimize (otomatik-optimize)');
        $this->command->info('   - Rekabet Analizi (rekabet-analizi)');
        $this->command->info('   - İçerik Kalite Skoru (icerik-kalite-skoru)');
        $this->command->info('   - Schema Markup Üretici (schema-markup-uretici)');
        $this->command->info('   - Çoklu Dil Çevirisi (coklu-dil-cevirisi)');
        $this->command->info('   - AI Asistan Sohbet (ai-asistan-sohbet)');
    }
}