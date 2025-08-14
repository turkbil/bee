<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class AIFeatureCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 18 AI Feature Kategorisini tek seferde oluşturur
     */
    public function run(): void
    {
        // Central veritabanında çalışır
        TenantHelpers::central(function() {
            
            $categories = [
                // 🔥 ÇOK YÜKSEK ÖNCELİK (1-6)
                [
                    'ai_feature_category_id' => 1,
                    'title' => 'SEO ve Optimizasyon',
                    'slug' => 'seo-optimization',
                    'description' => 'Arama motoru optimizasyonu ve web site performansı',
                    'order' => 1,
                    'icon' => 'fas fa-search',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 2,
                    'title' => 'İçerik Yazıcılığı',
                    'slug' => 'content-writing',
                    'description' => 'Blog, makale, sosyal medya içerik üretimi',
                    'order' => 2,
                    'icon' => 'fas fa-pen-fancy',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 3,
                    'title' => 'Çeviri ve Lokalizasyon',
                    'slug' => 'translation',
                    'description' => 'Çoklu dil çeviri ve yerelleştirme hizmetleri',
                    'order' => 3,
                    'icon' => 'fas fa-language',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 4,
                    'title' => 'Pazarlama & Reklam',
                    'slug' => 'marketing-advertising',
                    'description' => 'Reklam metinleri, kampanya içerikleri, landing page',
                    'order' => 4,
                    'icon' => 'fas fa-bullhorn',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 5,
                    'title' => 'E-ticaret ve Satış',
                    'slug' => 'ecommerce-sales',
                    'description' => 'Ürün açıklamaları, satış metinleri, e-ticaret içerikleri',
                    'order' => 5,
                    'icon' => 'fas fa-shopping-cart',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 6,
                    'title' => 'Sosyal Medya',
                    'slug' => 'social-media',
                    'description' => 'Sosyal medya paylaşımları, hashtag önerileri, engagement',
                    'order' => 6,
                    'icon' => 'fas fa-share-alt',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],

                // ⚡ YÜKSEK ÖNCELİK (7-12)
                [
                    'ai_feature_category_id' => 7,
                    'title' => 'Email & İletişim',
                    'slug' => 'email-communication',
                    'description' => 'Newsletter, email marketing, iş iletişimi',
                    'order' => 7,
                    'icon' => 'fas fa-envelope',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 8,
                    'title' => 'Analiz ve Raporlama',
                    'slug' => 'analytics-reporting',
                    'description' => 'Veri analizi, rapor yazımı, istatistiksel değerlendirmeler',
                    'order' => 8,
                    'icon' => 'fas fa-chart-line',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 9,
                    'title' => 'Müşteri Hizmetleri',
                    'slug' => 'customer-service',
                    'description' => 'Müşteri yanıtları, destek metinleri, FAQ\'lar',
                    'order' => 9,
                    'icon' => 'fas fa-headset',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 10,
                    'title' => 'İş Geliştirme',
                    'slug' => 'business-development',
                    'description' => 'İş planları, sunum metinleri, kurumsal içerikler',
                    'order' => 10,
                    'icon' => 'fas fa-briefcase',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 11,
                    'title' => 'Araştırma & Pazar',
                    'slug' => 'research-market',
                    'description' => 'Pazar araştırması, competitor analizi, survey',
                    'order' => 11,
                    'icon' => 'fas fa-chart-pie',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 12,
                    'title' => 'Yaratıcı İçerik',
                    'slug' => 'creative-content',
                    'description' => 'Hikaye yazımı, yaratıcı metinler, senaryolar',
                    'order' => 12,
                    'icon' => 'fas fa-palette',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],

                // 🔧 ORTA ÖNCELİK (13-18)
                [
                    'ai_feature_category_id' => 13,
                    'title' => 'Teknik Dokümantasyon',
                    'slug' => 'technical-docs',
                    'description' => 'API dokümantasyonu, kullanıcı kılavuzları, teknik açıklamalar',
                    'order' => 13,
                    'icon' => 'fas fa-book',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 14,
                    'title' => 'Kod & Yazılım',
                    'slug' => 'code-software',
                    'description' => 'API dokümantasyonu, kod açıklamaları, tutorial',
                    'order' => 14,
                    'icon' => 'fas fa-laptop-code',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 15,
                    'title' => 'Tasarım & UI/UX',
                    'slug' => 'design-ui-ux',
                    'description' => 'Microcopy, error messages, UI metinleri',
                    'order' => 15,
                    'icon' => 'fas fa-paint-brush',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 16,
                    'title' => 'Eğitim ve Öğretim',
                    'slug' => 'education',
                    'description' => 'Eğitim materyalleri, kurs içerikleri, sınav soruları',
                    'order' => 16,
                    'icon' => 'fas fa-graduation-cap',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 17,
                    'title' => 'Finans & İş',
                    'slug' => 'finance-business',
                    'description' => 'İş planları, finansal analiz, ROI raporları',
                    'order' => 17,
                    'icon' => 'fas fa-calculator',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
                [
                    'ai_feature_category_id' => 18,
                    'title' => 'Hukuki ve Uyumluluk',
                    'slug' => 'legal-compliance',
                    'description' => 'Sözleşmeler, kullanım şartları, gizlilik politikaları',
                    'order' => 18,
                    'icon' => 'fas fa-gavel',
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false,
                ],
            ];

            // Kategorileri ekle - insertOrIgnore ile çakışma önle
            foreach ($categories as $category) {
                $category['created_at'] = now();
                $category['updated_at'] = now();
                
                DB::table('ai_feature_categories')->insertOrIgnore($category);
            }

        });
    }
}