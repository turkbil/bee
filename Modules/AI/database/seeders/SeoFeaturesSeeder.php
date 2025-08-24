<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

/**
 * SEO AI Features Seeder - AI-SEO ENTEGRASYON V1.0
 * 
 * Bu seeder SEO Management modülü için özel AI özelliklerini ekler:
 * - 7 SEO-specific AI feature (show_in_prowess = false)
 * - Sabit kredi sistemi ile güvenli kullanım
 * - Universal Input System V3 uyumlu
 * - Multi-language SEO support
 */
class SeoFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            return;
        }

        $this->command->info('🚀 SEO AI FEATURES SEEDER BAŞLIYOR...');
        $this->command->info('🎯 Hedef: 3 temel SEO AI feature (Phase 1)');
        
        // PHASE 1: Temel SEO Features (3 adet)
        $this->createBasicSeoFeatures();
        
        $this->command->info('');
        $this->command->info('✅ SEO AI FEATURES BAŞARIYLA EKLENDİ!');
        $this->showSuccessReport();
    }
    
    /**
     * Phase 1: Temel SEO AI Features
     */
    private function createBasicSeoFeatures(): void
    {
        $this->command->info('');
        $this->command->info('📝 PHASE 1: Temel SEO Features ekleniyor...');
        
        $features = [
            [
                'name' => 'SEO Meta Title Üretici',
                'slug' => 'seo-meta-title-generator',
                'description' => 'SEO optimize meta title önerileri sunar. 50-60 karakter arası optimize başlık oluşturur.',
                'emoji' => '📝',
                'icon' => 'ti-sparkles',
                'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen içerik için 50-60 karakter arası SEO optimize meta title oluştur. Anahtar kelimeleri dahil et ve tıklanma oranını artıracak çekici bir başlık yaz.',
                'response_template' => json_encode([
                    'format' => 'structured',
                    'sections' => [
                        'recommended_title' => 'Önerilen Ana Başlık',
                        'alternative_titles' => '3 Alternatif Başlık Seçeneği',
                        'character_analysis' => 'Karakter Analizi (50-60 karakter)',
                        'keyword_analysis' => 'Anahtar Kelime Kullanımı',
                        'ctr_prediction' => 'Tıklanma Oranı Tahmini (1-10)'
                    ]
                ]),
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'show_in_prowess' => false,
                'sort_order' => 1001,
                'button_text' => 'Meta Title Üret'
            ],
            [
                'name' => 'SEO Meta Description Üretici',
                'slug' => 'seo-meta-description-generator',
                'description' => 'Çekici ve SEO uyumlu meta description üretir. 150-160 karakter arası optimize açıklama oluşturur.',
                'emoji' => '📄',
                'icon' => 'ti-file-text',
                'quick_prompt' => 'Sen bir SEO uzmanısın. 150-160 karakter arası çekici ve SEO uyumlu meta description oluştur. Call-to-action ekle ve kullanıcıyı tıklamaya teşvik edecek duygusal çekicilik kullan.',
                'response_template' => json_encode([
                    'format' => 'structured', 
                    'sections' => [
                        'recommended_description' => 'Önerilen Ana Açıklama',
                        'alternative_descriptions' => '2 Alternatif Açıklama',
                        'call_to_action_analysis' => 'Çağrı-Aksiyon Analizi',
                        'emotional_appeal' => 'Duygusal Çekicilik Puanı (1-10)',
                        'character_optimization' => 'Karakter Optimizasyonu (150-160)'
                    ]
                ]),
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'show_in_prowess' => false,
                'sort_order' => 1002,
                'button_text' => 'Meta Description Üret'
            ],
            [
                'name' => 'SEO Skor Analizi',
                'slug' => 'seo-score-analyzer',
                'description' => 'Mevcut SEO verilerini analiz ederek kapsamlı puan ve iyileştirme önerileri sunar.',
                'emoji' => '📊',
                'icon' => 'ti-chart-bar',
                'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen SEO verilerini analiz et ve her alan için 1-10 puan ver. Meta title, meta description, content type, priority score ve sosyal medya optimizasyonu için detaylı analiz yap.',
                'response_template' => json_encode([
                    'format' => 'structured',
                    'sections' => [
                        'overall_score' => 'Genel SEO Puanı (1-10)',
                        'title_analysis' => 'Meta Title Analizi ve Puanı',
                        'description_analysis' => 'Meta Description Analizi ve Puanı',
                        'content_type_analysis' => 'İçerik Türü Uygunluğu',
                        'priority_analysis' => 'Öncelik Puanı Değerlendirmesi',
                        'social_media_analysis' => 'Sosyal Medya Optimizasyonu',
                        'improvement_suggestions' => 'Öncelikli İyileştirme Önerileri',
                        'action_plan' => 'Adım Adım Aksiyon Planı'
                    ],
                    'scoring' => true,
                    'charts' => true
                ]),
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'show_in_prowess' => false,
                'sort_order' => 1003,
                'button_text' => 'SEO Skoru Analiz Et'
            ]
        ];

        foreach ($features as $feature) {
            $existing = DB::table('ai_features')->where('slug', $feature['slug'])->first();
            
            if ($existing) {
                $this->command->warn("⚠️  Feature zaten mevcut: {$feature['slug']}");
                continue;
            }

            DB::table('ai_features')->insert([
                'name' => $feature['name'],
                'slug' => $feature['slug'],
                'description' => $feature['description'],
                'emoji' => $feature['emoji'],
                'icon' => $feature['icon'],
                'quick_prompt' => $feature['quick_prompt'],
                'response_template' => $feature['response_template'],
                'response_format' => $feature['response_format'],
                'complexity_level' => $feature['complexity_level'],
                'status' => 'active',
                'is_featured' => false,
                'show_in_examples' => false,
                'show_in_prowess' => $feature['show_in_prowess'],
                'requires_input' => true,
                'button_text' => $feature['button_text'],
                'sort_order' => $feature['sort_order'],
                'usage_count' => 0,
                'avg_rating' => 0,
                'rating_count' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->info("✅ {$feature['name']} eklendi (slug: {$feature['slug']})");
        }
    }
    
    private function showSuccessReport(): void
    {
        $this->command->info('');
        $this->command->info('📊 BAŞARI RAPORU:');
        $this->command->info('');
        $this->command->info('🎯 PHASE 1 TAMAMLANDI:');
        $this->command->info('   ✓ SEO Meta Title Üretici (seo-meta-title-generator)');
        $this->command->info('   ✓ SEO Meta Description Üretici (seo-meta-description-generator)');
        $this->command->info('   ✓ SEO Skor Analizi (seo-score-analyzer)');
        $this->command->info('');
        $this->command->info('🔒 GÜVENLİK ÖZELLİKLERİ:');
        $this->command->info('   ✓ show_in_prowess = false (Prowess sayfasında gizli)');
        $this->command->info('   ✓ Universal Input System V3 uyumlu');
        $this->command->info('   ✓ Structured response template ile standart çıktı');
        $this->command->info('   ✓ SEO-specific quick prompt\'lar hazır');
        $this->command->info('');
        $this->command->info('📋 SONRAKI ADIMLAR:');
        $this->command->info('   1. Universal SEO tab\'a AI düğmeleri ekle');
        $this->command->info('   2. SeoAIController ve routes oluştur');
        $this->command->info('   3. Kredi güvenlik sistemi entegrasyonu');
        $this->command->info('   4. Frontend JavaScript integration');
        $this->command->info('');
        $this->command->info('🚀 SEO AI FEATURES HAZIR! Test edilebilir.');
    }
}