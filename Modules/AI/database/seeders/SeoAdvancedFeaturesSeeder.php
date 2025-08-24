<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;

class SeoAdvancedFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Advanced Features Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        echo "🚀 SEO ADVANCED FEATURES SEEDER (PHASE 2) BAŞLIYOR...\n";
        echo "🎯 Hedef: 4 ileri düzey SEO AI feature (Phase 2)\n\n";

        $features = [
            [
                'name' => 'İçerik Türü Optimizasyonu',
                'slug' => 'seo-content-type-optimizer',
                'description' => 'İçeriği analiz ederek en uygun content_type ve schema.org türü önerir',
                'module_type' => 'seo',
                'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
                'quick_prompt' => 'Sen bir SEO uzmanısın. İçeriği analiz et ve en uygun schema.org content type öner. Seçim gerekçesini de açıkla.',
                'response_template' => json_encode([
                    'format' => 'structured',
                    'sections' => [
                        'recommended_type' => 'Önerilen Content Type',
                        'confidence_score' => 'Güvenilirlik Puanı (1-10)',
                        'reasoning' => 'Seçim Gerekçesi',
                        'alternative_types' => 'Alternatif Türler',
                        'schema_benefits' => 'Schema.org Faydaları',
                        'seo_impact' => 'SEO Etkisi'
                    ]
                ]),
                'is_featured' => false,
                'show_in_examples' => false,
                'show_in_prowess' => false, // SEO'ya özel, genel sayfada gizli
                'status' => 'active'
            ],
            [
                'name' => 'Sosyal Medya Optimizasyonu',
                'slug' => 'seo-social-media-optimizer',
                'description' => 'Sosyal medya paylaşımları için özel og_title ve og_description üretir',
                'module_type' => 'seo',
                'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
                'quick_prompt' => 'Sen bir sosyal medya uzmanısın. Sosyal medya paylaşımları için çekici og_title ve og_description oluştur. Etkileşim artırıcı olsun.',
                'response_template' => json_encode([
                    'format' => 'structured',
                    'sections' => [
                        'og_title_recommendations' => 'OpenGraph Başlık Önerileri (3 adet)',
                        'og_description_recommendations' => 'OpenGraph Açıklama Önerileri (2 adet)',
                        'platform_optimization' => 'Platform Bazlı Optimizasyon (Facebook, Twitter, LinkedIn)',
                        'engagement_prediction' => 'Etkileşim Tahmini',
                        'viral_potential' => 'Viral Potansiyel Analizi',
                        'emoji_suggestions' => 'Emoji Önerileri'
                    ]
                ]),
                'is_featured' => false,
                'show_in_examples' => false,
                'show_in_prowess' => false, // SEO'ya özel, genel sayfada gizli
                'status' => 'active'
            ],
            [
                'name' => 'SEO Öncelik Hesaplayıcı',
                'slug' => 'seo-priority-calculator',
                'description' => 'İçeriği analiz ederek 1-10 arası optimal priority_score hesaplar',
                'module_type' => 'seo',
                'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
                'quick_prompt' => 'Sen bir SEO uzmanısın. İçerik önemini analiz et ve 1-10 arası SEO öncelik puanı ver. Gerekçesini detaylı açıkla.',
                'response_template' => json_encode([
                    'format' => 'structured',
                    'sections' => [
                        'recommended_priority' => 'Önerilen Öncelik Puanı (1-10)',
                        'content_importance' => 'İçerik Önem Analizi',
                        'business_impact' => 'İş Etkisi Değerlendirmesi',
                        'competition_analysis' => 'Rekabet Analizi',
                        'priority_justification' => 'Puan Gerekçesi',
                        'optimization_timeline' => 'Optimizasyon Zaman Planı'
                    ]
                ]),
                'is_featured' => false,
                'show_in_examples' => false,
                'show_in_prowess' => false, // SEO'ya özel, genel sayfada gizli
                'status' => 'active'
            ],
            [
                'name' => 'Kapsamlı SEO Denetimi',
                'slug' => 'seo-comprehensive-audit',
                'description' => 'Tüm SEO alanlarını analiz ederek detaylı rapor ve aksiyon planı sunar',
                'module_type' => 'seo',
                'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
                'quick_prompt' => 'Sen bir uzman SEO danışmanısın. Tüm SEO verilerini kapsamlı analiz et ve detaylı iyileştirme raporu hazırla. Puanla ve öncelikli aksiyon planı ver.',
                'response_template' => json_encode([
                    'format' => 'comprehensive_report',
                    'sections' => [
                        'executive_summary' => 'Yönetici Özeti',
                        'overall_seo_score' => 'Genel SEO Puanı (1-10)',
                        'title_optimization' => 'Meta Title Optimizasyonu',
                        'description_optimization' => 'Meta Description Optimizasyonu',
                        'content_type_optimization' => 'İçerik Türü Optimizasyonu',
                        'social_media_optimization' => 'Sosyal Medya Optimizasyonu',
                        'priority_optimization' => 'Öncelik Optimizasyonu',
                        'competitive_analysis' => 'Rekabetçi Analiz',
                        'technical_recommendations' => 'Teknik Öneriler',
                        'action_plan' => 'Öncelikli Aksiyon Planı (1-3-6 ay)',
                        'performance_forecast' => 'Performans Tahmini'
                    ],
                    'charts' => true,
                    'downloadable' => true
                ]),
                'is_featured' => true,
                'show_in_examples' => false,
                'show_in_prowess' => false, // SEO'ya özel, genel sayfada gizli
                'status' => 'active'
            ]
        ];

        echo "📝 PHASE 2: Advanced SEO Features ekleniyor...\n";

        foreach ($features as $featureData) {
            // Mevcut feature kontrolü
            $existing = AIFeature::where('slug', $featureData['slug'])->first();
            
            if ($existing) {
                echo "⚠️  Feature zaten mevcut: {$featureData['slug']}\n";
                continue;
            }

            // Yeni feature oluştur
            AIFeature::create($featureData);
            echo "✅ {$featureData['name']} feature'ı oluşturuldu\n";
        }

        echo "\n✅ SEO ADVANCED FEATURES (PHASE 2) BAŞARIYLA EKLENDİ!\n\n";

        echo "📊 BAŞARI RAPORU:\n\n";
        echo "🎯 PHASE 2 TAMAMLANDI:\n";
        echo "   ✓ İçerik Türü Optimizasyonu (seo-content-type-optimizer)\n";
        echo "   ✓ Sosyal Medya Optimizasyonu (seo-social-media-optimizer)\n";
        echo "   ✓ SEO Öncelik Hesaplayıcı (seo-priority-calculator)\n";
        echo "   ✓ Kapsamlı SEO Denetimi (seo-comprehensive-audit)\n\n";

        echo "🔒 GÜVENLİK ÖZELLİKLERİ:\n";
        echo "   ✓ show_in_prowess = false (Prowess sayfasında gizli)\n";
        echo "   ✓ Universal Input System V3 uyumlu\n";
        echo "   ✓ Structured response template ile standart çıktı\n";
        echo "   ✓ SEO-specific quick prompt'lar hazır\n\n";

        echo "📋 SONRAKI ADIMLAR:\n";
        echo "   1. Enhanced frontend UI geliştir\n";
        echo "   2. Comprehensive analysis JavaScript entegrasyonu\n";
        echo "   3. AI conversation system ile entegrasyon\n";
        echo "   4. Chart ve visualization ekleme\n\n";

        echo "🚀 SEO ADVANCED FEATURES HAZIR! Test edilebilir.\n";
    }
}