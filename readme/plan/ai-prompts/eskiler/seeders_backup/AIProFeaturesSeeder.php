<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;

/**
 * 🚀 AI PRO FEATURES SEEDER
 * 
 * Nurullah'ın isteği doğrultusunda kapsamlı AI feature koleksiyonu
 * Orijinal ve satılabilir kalitede AI özellikler
 * 
 * Created: 21.07.2025 - Gece Nöbeti
 * Developer: Claude + Nurullah
 */
class AIProFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            // 🎯 1. ÖNCELİK - İÇERİK İŞLEME
            [
                'name' => 'Akıllı Yazı Özetleme',
                'slug' => 'akilli-yazi-ozetleme',
                'description' => 'Uzun metinleri önemli noktaları kaybetmeden özetler. Akademik, blog ve makale metinleri için optimize edilmiş.',
                'emoji' => '📝',
                'icon' => 'fas fa-compress',
                'category' => 'content_processing',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 1,
                'helper_function' => 'ai_summarize_content',
                'button_text' => 'Metni Özetle',
                'helper_description' => 'Uzun metinleri anlamlı ve öz bir şekilde özetler',
                'input_placeholder' => 'Özetlemek istediğiniz metni buraya yapıştırın...',
                'quick_prompt' => 'Sen uzman bir editör ve içerik uzmanısın. Verilen metni kapsamlı şekilde özetlemen gerekiyor.',
                'response_template' => [
                    'format' => 'summary_with_highlights',
                    'sections' => ['Özet', 'Ana Noktalar', 'Sonuç'],
                    'include_statistics' => true,
                    'word_reduction_percentage' => true
                ],
                'settings' => [
                    'max_input_length' => 10000,
                    'target_summary_ratio' => 0.3,
                    'preserve_key_quotes' => true,
                    'include_section_breakdown' => true
                ]
            ],

            [
                'name' => 'Profesyonel Yazı Uzatma',
                'slug' => 'profesyonel-yazi-uzatma',
                'description' => 'Kısa metinleri detaylandırarak kapsamlı içerikler oluşturur. SEO uyumlu ve doğal akışta uzatır.',
                'emoji' => '📈',
                'icon' => 'fas fa-expand',
                'category' => 'content_processing',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 2,
                'helper_function' => 'ai_expand_content',
                'button_text' => 'İçeriği Genişlet',
                'helper_description' => 'Kısa metinleri profesyonel ve detaylı içeriğe dönüştürür',
                'input_placeholder' => 'Uzatmak istediğiniz ana fikirleri yazın...',
                'quick_prompt' => 'Sen deneyimli bir copywriter ve içerik geliştirme uzmanısın. Verilen kısa metni kapsamlı ve değerli bir içeriğe dönüştür.',
                'response_template' => [
                    'format' => 'expanded_content',
                    'sections' => ['Genişletilmiş İçerik', 'Ek Detaylar', 'Destekleyici Bilgiler'],
                    'include_subheadings' => true,
                    'expansion_ratio' => true
                ],
                'settings' => [
                    'min_expansion_ratio' => 2.5,
                    'max_expansion_ratio' => 5.0,
                    'add_examples' => true,
                    'include_statistics' => true
                ]
            ],

            [
                'name' => 'Çoklu Dil Çeviri Uzmanı',
                'slug' => 'coklu-dil-ceviri-uzmani',
                'description' => 'Profesyonel kalitede çeviri hizmeti. Kültürel nüansları ve yerel ifadeleri koruyarak çevirir.',
                'emoji' => '🌐',
                'icon' => 'fas fa-language',
                'category' => 'translation',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 3,
                'helper_function' => 'ai_translate_content',
                'button_text' => 'Çeviri Yap',
                'helper_description' => 'Metinleri profesyonel kalitede hedef dile çevirir',
                'input_placeholder' => 'Çevirmek istediğiniz metni yazın...',
                'quick_prompt' => 'Sen uzman bir çevirmen ve dilbilimcisin. Metinleri sadece kelime kelime değil, kültürel ve bağlamsal anlam bütünlüğünü koruyarak çevir.',
                'response_template' => [
                    'format' => 'professional_translation',
                    'sections' => ['Çeviri', 'Alternatif İfadeler', 'Kültürel Notlar'],
                    'include_original' => true,
                    'quality_score' => true
                ],
                'settings' => [
                    'supported_languages' => ['en', 'fr', 'de', 'es', 'it', 'pt', 'ru', 'ar'],
                    'preserve_formatting' => true,
                    'cultural_adaptation' => true
                ]
            ],

            // 🎯 2. ÖNCELİK - SEO VE OPTİMİZASYON
            [
                'name' => 'SEO Çoklu Dil Optimizasyonu',
                'slug' => 'seo-coklu-dil-optimizasyonu',
                'description' => 'SEO meta verilerini ve içeriği farklı dillere optimize ederek çevirir. Her dil için yerel SEO kurallarını uygular.',
                'emoji' => '🔍',
                'icon' => 'fas fa-search',
                'category' => 'seo_optimization',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 4,
                'helper_function' => 'ai_seo_multilang_optimize',
                'button_text' => 'SEO Çoklu Dil',
                'helper_description' => 'SEO verilerini hedef dile optimizasyon ile çevirir',
                'input_placeholder' => 'SEO optimizasyonu yapılacak içerik...',
                'quick_prompt' => 'Sen bir SEO uzmanı ve çok dilli optimizasyon uzmanısın. İçeriği hedef dil için SEO kurallarına uygun şekilde çevir ve optimize et.',
                'response_template' => [
                    'format' => 'multilingual_seo',
                    'sections' => ['Title Tag', 'Meta Description', 'Keywords', 'Content Optimization'],
                    'include_local_seo' => true,
                    'competitor_analysis' => true
                ],
                'settings' => [
                    'target_languages' => ['en', 'de', 'fr', 'es'],
                    'local_keyword_research' => true,
                    'cultural_seo_adaptation' => true
                ]
            ],

            [
                'name' => 'Gelişmiş Anahtar Kelime Araştırması',
                'slug' => 'gelismis-anahtar-kelime-arastirmasi',
                'description' => 'Rekabet analizi ile birlikte kapsamlı anahtar kelime araştırması. Long-tail ve LSI keywords dahil.',
                'emoji' => '🔑',
                'icon' => 'fas fa-key',
                'category' => 'seo_optimization',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 5,
                'helper_function' => 'ai_keyword_research',
                'button_text' => 'Anahtar Kelime Bul',
                'helper_description' => 'Kapsamlı anahtar kelime araştırması ve analizi',
                'input_placeholder' => 'Ana konu veya sektörünüzü yazın...',
                'quick_prompt' => 'Sen deneyimli bir SEO uzmanı ve anahtar kelime araştırmacısısın. Verilen konu için kapsamlı anahtar kelime analizi yap.',
                'response_template' => [
                    'format' => 'keyword_research',
                    'sections' => ['Primary Keywords', 'Long-tail Keywords', 'LSI Keywords', 'Competitor Keywords'],
                    'include_search_volume' => true,
                    'difficulty_scores' => true
                ]
            ],

            // 🎯 3. ÖNCELİK - İÇERİK ÜRETİMİ
            [
                'name' => 'Blog Yazısı Jeneratörü',
                'slug' => 'blog-yazisi-jeneratoru',
                'description' => 'SEO uyumlu, özgün blog yazıları oluşturur. Giriş, gelişme, sonuç yapısıyla profesyonel içerik.',
                'emoji' => '✍️',
                'icon' => 'fas fa-blog',
                'category' => 'content_creation',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 6,
                'helper_function' => 'ai_generate_blog_post',
                'button_text' => 'Blog Yazısı Oluştur',
                'helper_description' => 'Kapsamlı ve SEO uyumlu blog yazıları yaratır',
                'input_placeholder' => 'Blog yazısı konusunu ve ana noktaları yazın...',
                'quick_prompt' => 'Sen profesyonel bir blog yazarı ve içerik pazarlama uzmanısın. Verilen konu hakkında kapsamlı ve okuyucu dostu blog yazısı oluştur.',
                'response_template' => [
                    'format' => 'complete_blog_post',
                    'sections' => ['Başlık', 'Giriş', 'Ana İçerik', 'Sonuç', 'CTA'],
                    'include_seo_optimization' => true,
                    'readability_score' => true
                ]
            ],

            [
                'name' => 'Sosyal Medya İçerik Paketi',
                'slug' => 'sosyal-medya-icerik-paketi',
                'description' => 'Farklı platformlar için optimize edilmiş sosyal medya içerikleri. Facebook, Instagram, Twitter, LinkedIn.',
                'emoji' => '📱',
                'icon' => 'fas fa-share-alt',
                'category' => 'content_creation',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 7,
                'helper_function' => 'ai_social_media_content',
                'button_text' => 'Sosyal Medya Paketi',
                'helper_description' => 'Çoklu platform için sosyal medya içerikleri üretir',
                'input_placeholder' => 'Paylaşmak istediğiniz konuyu yazın...',
                'quick_prompt' => 'Sen sosyal medya uzmanı ve dijital pazarlama uzmanısın. Verilen konu için farklı platformlara uygun içerikler oluştur.',
                'response_template' => [
                    'format' => 'social_media_package',
                    'sections' => ['Facebook Post', 'Instagram Caption', 'Twitter Thread', 'LinkedIn Post'],
                    'include_hashtags' => true,
                    'engagement_optimization' => true
                ]
            ],

            [
                'name' => 'E-ticaret Ürün Açıklaması',
                'slug' => 'e-ticaret-urun-aciklamasi',
                'description' => 'Satış odaklı, ikna edici ürün açıklamaları. Teknik özellikler ve fayda odaklı içerik.',
                'emoji' => '🛍️',
                'icon' => 'fas fa-shopping-cart',
                'category' => 'e_commerce',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 8,
                'helper_function' => 'ai_product_description',
                'button_text' => 'Ürün Açıklaması Yaz',
                'helper_description' => 'Satış odaklı ürün açıklamaları oluşturur',
                'input_placeholder' => 'Ürün bilgilerini ve özelliklerini yazın...',
                'quick_prompt' => 'Sen deneyimli bir copywriter ve e-ticaret uzmanısın. Verilen ürün için ikna edici ve satış odaklı açıklama yaz.',
                'response_template' => [
                    'format' => 'product_description',
                    'sections' => ['Ana Başlık', 'Öne Çıkan Özellikler', 'Detaylı Açıklama', 'Teknik Özellikler'],
                    'include_benefits' => true,
                    'persuasion_elements' => true
                ]
            ],

            // 🎯 4. ÖNCELİK - ANALİZ VE OPTİMİZASYON
            [
                'name' => 'İçerik Performans Analizi',
                'slug' => 'icerik-performans-analizi',
                'description' => 'Mevcut içeriklerin SEO, okunabilirlik ve engagement performansını analiz eder.',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-line',
                'category' => 'analytics',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 9,
                'helper_function' => 'ai_content_performance_analysis',
                'button_text' => 'Performans Analizi',
                'helper_description' => 'İçerik performansını kapsamlı olarak analiz eder',
                'input_placeholder' => 'Analiz edilecek içeriği yapıştırın...',
                'quick_prompt' => 'Sen dijital pazarlama analisti ve SEO uzmanısın. Verilen içeriğin performansını çok boyutlu olarak analiz et.',
                'response_template' => [
                    'format' => 'performance_analysis',
                    'sections' => ['SEO Skoru', 'Okunabilirlik', 'Engagement Potansiyeli', 'İyileştirme Önerileri'],
                    'include_metrics' => true,
                    'actionable_recommendations' => true
                ]
            ],

            [
                'name' => 'Rekabet Analizi Uzmanı',
                'slug' => 'rekabet-analizi-uzmani',
                'description' => 'Rakip içerikleri analiz ederek farklılaştırma stratejileri ve rekabet avantajı önerileri sunar.',
                'emoji' => '🎯',
                'icon' => 'fas fa-crosshairs',
                'category' => 'analytics',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 10,
                'helper_function' => 'ai_competitor_analysis',
                'button_text' => 'Rekabet Analizi',
                'helper_description' => 'Rekabet ortamını analiz ederek strateji önerileri sunar',
                'input_placeholder' => 'Sektörünüzü ve ana rakiplerinizi yazın...',
                'quick_prompt' => 'Sen strateji danışmanı ve rekabet analizi uzmanısın. Verilen sektör için kapsamlı rekabet analizi yap.',
                'response_template' => [
                    'format' => 'competitor_analysis',
                    'sections' => ['Rekabet Haritası', 'Güçlü/Zayıf Yönler', 'Fırsat Alanları', 'Strateji Önerileri'],
                    'include_swot' => true,
                    'actionable_strategies' => true
                ]
            ],

            // 🎯 5. ÖNCELİK - YARATICI İÇERİK
            [
                'name' => 'Yaratıcı Başlık Üreticisi',
                'slug' => 'yaratici-baslik-ureticisi',
                'description' => 'Dikkat çekici, SEO uyumlu ve tıklanabilir başlıklar üretir. A/B test için alternatifler sunar.',
                'emoji' => '💡',
                'icon' => 'fas fa-lightbulb',
                'category' => 'creative_content',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 11,
                'helper_function' => 'ai_creative_headlines',
                'button_text' => 'Başlık Üret',
                'helper_description' => 'Yaratıcı ve etkili başlık alternatifleri üretir',
                'input_placeholder' => 'İçerik konusunu ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen yaratıcı copywriter ve başlık uzmanısın. Verilen konu için dikkat çekici ve tıklanabilir başlıklar üret.',
                'response_template' => [
                    'format' => 'headline_variations',
                    'sections' => ['Dikkat Çekici Başlıklar', 'SEO Odaklı Başlıklar', 'Emotif Başlıklar', 'A/B Test Alternatifleri'],
                    'include_click_prediction' => true,
                    'testing_recommendations' => true
                ]
            ],

            [
                'name' => 'Hikaye Anlatıcısı',
                'slug' => 'hikaye-anlaticisi',
                'description' => 'Marka hikayeleri ve içerik hikayeleştirme. Duygusal bağ kuran narrative içerik üretir.',
                'emoji' => '📖',
                'icon' => 'fas fa-book-open',
                'category' => 'creative_content',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 12,
                'helper_function' => 'ai_storytelling',
                'button_text' => 'Hikaye Oluştur',
                'helper_description' => 'Duygusal bağ kuran hikayeler oluşturur',
                'input_placeholder' => 'Hikaye konusunu ve ana karakterleri yazın...',
                'quick_prompt' => 'Sen deneyimli bir hikaye yazarı ve brand storytelling uzmanısın. Verilen elementi etkileyici bir hikayeye dönüştür.',
                'response_template' => [
                    'format' => 'narrative_content',
                    'sections' => ['Hikaye Kurgusu', 'Karakter Gelişimi', 'Duygusal Çekicilik', 'Marka Bağlantısı'],
                    'include_emotional_arc' => true,
                    'brand_integration' => true
                ]
            ],

            // 🎯 6. ÖNCELİK - TEKNİK İÇERİK
            [
                'name' => 'Teknik Dokümantasyon Yazarı',
                'slug' => 'teknik-dokumantasyon-yazari',
                'description' => 'Karmaşık teknik konuları anlaşılır dilde açıklayan dokümantasyon ve kılavuzlar üretir.',
                'emoji' => '📋',
                'icon' => 'fas fa-file-alt',
                'category' => 'technical_content',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 13,
                'helper_function' => 'ai_technical_documentation',
                'button_text' => 'Teknik Döküman',
                'helper_description' => 'Teknik konuları anlaşılır dokümantasyona dönüştürür',
                'input_placeholder' => 'Açıklanacak teknik konu ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen teknik yazar ve dokümantasyon uzmanısın. Karmaşık teknik bilgileri herkesin anlayabileceği şekilde açıkla.',
                'response_template' => [
                    'format' => 'technical_documentation',
                    'sections' => ['Genel Bakış', 'Adım Adım Kılavuz', 'Kod Örnekleri', 'Sorun Giderme'],
                    'include_examples' => true,
                    'difficulty_levels' => true
                ]
            ],

            [
                'name' => 'FAQ Oluşturucu',
                'slug' => 'faq-olusturucu',
                'description' => 'Ürün/hizmet hakkında kapsamlı SSS (Sıkça Sorulan Sorular) listeleri oluşturur.',
                'emoji' => '❓',
                'icon' => 'fas fa-question-circle',
                'category' => 'technical_content',
                'complexity_level' => 2,
                'status' => 'active',
                'sort_order' => 14,
                'helper_function' => 'ai_faq_generator',
                'button_text' => 'FAQ Oluştur',
                'helper_description' => 'Kapsamlı SSS listeleri oluşturur',
                'input_placeholder' => 'Ürün/hizmet bilgilerini yazın...',
                'quick_prompt' => 'Sen müşteri hizmetleri uzmanı ve bilgi mimarisın. Verilen ürün/hizmet için kapsamlı FAQ listesi oluştur.',
                'response_template' => [
                    'format' => 'faq_list',
                    'sections' => ['Genel Sorular', 'Teknik Sorular', 'Ödeme & Teslimat', 'Destek'],
                    'include_search_keywords' => true,
                    'user_journey_based' => true
                ]
            ],

            // 🎯 7. ÖNCELİK - E-MAIL & PAZARLAMA
            [
                'name' => 'E-mail Pazarlama Uzmanı',
                'slug' => 'email-pazarlama-uzmani',
                'description' => 'Açılma ve tıklama oranı yüksek e-mail kampanyaları. Newsletter, promosyon ve nurturing e-mailleri.',
                'emoji' => '📧',
                'icon' => 'fas fa-envelope',
                'category' => 'marketing',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 15,
                'helper_function' => 'ai_email_marketing',
                'button_text' => 'E-mail Kampanyası',
                'helper_description' => 'Etkili e-mail pazarlama içerikleri üretir',
                'input_placeholder' => 'Kampanya konusu ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen e-mail pazarlama uzmanı ve conversion optimization uzmanısın. Yüksek açılma ve tıklama oranına sahip e-mail kampanyası oluştur.',
                'response_template' => [
                    'format' => 'email_campaign',
                    'sections' => ['Konu Başlığı', 'Ön İzleme Metni', 'E-mail İçeriği', 'CTA Butonları'],
                    'include_ab_test_variants' => true,
                    'conversion_optimization' => true
                ]
            ],

            [
                'name' => 'İçerik Takvimi Planlayıcısı',
                'slug' => 'icerik-takvimi-planlayicisi',
                'description' => 'Aylık içerik takvimi ve stratejik içerik planlama. Sosyal medya ve blog entegrasyonu.',
                'emoji' => '📅',
                'icon' => 'fas fa-calendar-alt',
                'category' => 'planning',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 16,
                'helper_function' => 'ai_content_calendar',
                'button_text' => 'İçerik Takvimi',
                'helper_description' => 'Stratejik içerik takvimi planlar',
                'input_placeholder' => 'Sektör, hedef kitle ve içerik hedeflerinizi yazın...',
                'quick_prompt' => 'Sen içerik stratejisti ve pazarlama planlama uzmanısın. Verilen bilgiler doğrultusunda kapsamlı içerik takvimi planla.',
                'response_template' => [
                    'format' => 'content_calendar',
                    'sections' => ['Aylık Plan', 'Haftalık Dağılım', 'Platform Bazlı İçerik', 'Özel Günler'],
                    'include_content_themes' => true,
                    'seasonal_optimization' => true
                ]
            ],

            // 🎯 8. ÖNCELİK - SATIŞ & DIJİTAL
            [
                'name' => 'Satış Sayfası Yazarı',
                'slug' => 'satis-sayfasi-yazari',
                'description' => 'Yüksek dönüşüm oranlı satış sayfaları. AIDA, PAS ve diğer persuasion formülleri.',
                'emoji' => '💰',
                'icon' => 'fas fa-dollar-sign',
                'category' => 'sales',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 17,
                'helper_function' => 'ai_sales_page_writer',
                'button_text' => 'Satış Sayfası Yaz',
                'helper_description' => 'Yüksek dönüşümlü satış sayfaları oluşturur',
                'input_placeholder' => 'Ürün/hizmet detaylarını ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen direct response copywriter ve satış uzmanısın. Yüksek dönüşüm oranlı ikna edici satış sayfası yaz.',
                'response_template' => [
                    'format' => 'sales_page',
                    'sections' => ['Dikkat Çekici Başlık', 'Problem & Çözüm', 'Faydalar', 'Sosyal Kanıt', 'CTA'],
                    'include_persuasion_elements' => true,
                    'conversion_optimization' => true
                ]
            ],

            [
                'name' => 'Video Script Yazarı',
                'slug' => 'video-script-yazari',
                'description' => 'YouTube, sosyal medya ve reklam videoları için script. Hook, story, CTA yapısı.',
                'emoji' => '🎬',
                'icon' => 'fas fa-video',
                'category' => 'video_content',
                'complexity_level' => 3,
                'status' => 'active',
                'sort_order' => 18,
                'helper_function' => 'ai_video_script',
                'button_text' => 'Video Script Yaz',
                'helper_description' => 'Etkili video scriptleri oluşturur',
                'input_placeholder' => 'Video konusu, süresi ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen video içerik uzmanı ve senaryo yazarısın. Verilen konu için izleyici retention yüksek video scripti yaz.',
                'response_template' => [
                    'format' => 'video_script',
                    'sections' => ['Hook (0-5sn)', 'Ana İçerik', 'CTA', 'Görsel Notlar'],
                    'include_timing' => true,
                    'engagement_hooks' => true
                ]
            ],

            // 🎯 9. ÖNCELİK - RAPOR & ANALİZ
            [
                'name' => 'İş Raporu Hazırlayıcısı',
                'slug' => 'is-raporu-hazirlayicisi',
                'description' => 'Profesyonel iş raporları, analiz raporları ve sunum materyalleri hazırlar.',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-bar',
                'category' => 'business_reports',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 19,
                'helper_function' => 'ai_business_report',
                'button_text' => 'İş Raporu Hazırla',
                'helper_description' => 'Profesyonel iş raporları oluşturur',
                'input_placeholder' => 'Rapor konusu, veriler ve hedef kitleyi yazın...',
                'quick_prompt' => 'Sen iş analisti ve rapor uzmanısın. Verilen veriler doğrultusunda kapsamlı ve profesyonel iş raporu hazırla.',
                'response_template' => [
                    'format' => 'business_report',
                    'sections' => ['Yönetici Özeti', 'Analiz', 'Bulgular', 'Öneriler', 'Sonuç'],
                    'include_charts_suggestions' => true,
                    'actionable_insights' => true
                ]
            ],

            [
                'name' => 'Trend Analizi Uzmanı',
                'slug' => 'trend-analizi-uzmani',
                'description' => 'Sektör trendleri, pazar analizleri ve gelecek öngörüleri. Veri tabanlı trend raporları.',
                'emoji' => '📈',
                'icon' => 'fas fa-trending-up',
                'category' => 'trend_analysis',
                'complexity_level' => 4,
                'status' => 'active',
                'sort_order' => 20,
                'helper_function' => 'ai_trend_analysis',
                'button_text' => 'Trend Analizi',
                'helper_description' => 'Sektör trend analizleri yapar',
                'input_placeholder' => 'Analiz edilecek sektör ve zaman dilimini yazın...',
                'quick_prompt' => 'Sen trend analisti ve pazar araştırmacısısın. Verilen sektör için kapsamlı trend analizi ve gelecek öngörüleri yap.',
                'response_template' => [
                    'format' => 'trend_analysis',
                    'sections' => ['Mevcut Durum', 'Gelişen Trendler', 'Fırsatlar', 'Tehditler', 'Strateji Önerileri'],
                    'include_predictions' => true,
                    'time_based_analysis' => true
                ]
            ]
        ];

        foreach ($features as $index => $featureData) {
            AIFeature::updateOrCreate(
                ['slug' => $featureData['slug']],
                $featureData
            );
        }

        // $this->command->info("✅ AI Pro Features başarıyla eklendi: " . count($features) . " adet feature");
    }
}