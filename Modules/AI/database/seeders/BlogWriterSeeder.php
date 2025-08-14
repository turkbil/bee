<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;
use Modules\AI\App\Models\AIFeature;

/**
 * 🎯 BLOG WRITER SEEDER
 * 
 * Blog Yazısı Oluşturucu feature'ını ve relations'larını oluşturur.
 * 
 * FEATURE: Blog Yazısı Oluşturucu (ID: 201)
 * KATEGORI: İçerik Yazıcılığı (ID: 2)
 * 
 * PROMPT İLİŞKİLERİ:
 * - EP1001 (İçerik Üretim Uzmanı) - Primary, Priority: 1
 * - EP1002 (SEO İçerik Uzmanı) - Supportive, Priority: 2  
 * - EP1003 (Blog Yazım Uzmanı) - Secondary, Priority: 3
 * 
 * BAĞIMLILIKLAR:
 * - AIFeatureCategoriesSeeder (kategori mevcut olmalı)
 * - ContentExpertPromptsSeeder (expert prompt'lar hazır olmalı)
 */
class BlogWriterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Blog Yazısı Oluşturucu feature\'ı ekleniyor...');
        
        // Önce mevcut verileri temizle (güvenli restart)
        $this->clearExistingData();
        
        // Feature'ı oluştur
        $this->seedBlogWriter();
        
        // Feature-Prompt ilişkilerini kur
        $this->createBlogWriterRelations();
        
        $this->command->info('✅ Blog Yazısı Oluşturucu başarıyla eklendi!');
    }
    
    /**
     * Mevcut verileri güvenli şekilde temizle
     */
    private function clearExistingData(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            // İlişkili relations'ları temizle
            DB::table('ai_feature_prompt_relations')->where('feature_id', 201)->delete();
            
            // Feature'ı temizle
            AIFeature::where('id', 201)->delete();
            AIFeature::where('slug', 'blog-yazisi-olusturucu')->delete();
        });
        
        $this->command->warn('🧹 Mevcut Blog Writer veriler temizlendi.');
    }
    
    /**
     * Blog Yazısı Oluşturucu Feature
     * 
     * PROMPT HIERARCHY:
     * 1. Quick Prompt: "Sen profesyonel bir blog yazarısın..."  
     * 2. Expert Prompts (Relations ile bağlanacak):
     *    - EP1001 (İçerik Üretim Uzmanı) - Primary, Priority: 1
     *    - EP1002 (SEO İçerik Uzmanı) - Supportive, Priority: 2
     *    - EP1003 (Blog Yazım Uzmanı) - Secondary, Priority: 3
     * 
     * RESPONSE TEMPLATE:
     * - Sections: Başlık, Meta Açıklama, Giriş, Ana İçerik, Sonuç, Anahtar Kelimeler
     * - Format: structured_content
     * - Features: SEO optimization, engaging tone, CTA inclusion
     * 
     * HELPER FUNCTION: 
     * - Name: ai_blog_yaz
     * - Usage: ai_blog_yaz('Web tasarım trendleri 2025', ['uzunluk' => 800, 'ton' => 'profesyonel'])
     */
    private function seedBlogWriter(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            AIFeature::create([
            'id' => 201, // İçerik kategorisi: 200-299
            'ai_feature_category_id' => 2, // İçerik Yazıcılığı kategorisi (ai_feature_category_id: 2)
            'name' => 'Blog Yazısı Oluşturucu',
            'slug' => 'blog-yazisi-olusturucu',
            'description' => 'SEO uyumlu, okuyucu dostu ve etkileşimli blog yazıları oluşturan AI asistanı. Modern blog yazım tekniklerini kullanarak hedef kitlenize ulaşın.',
            
            // PROMPT SİSTEMİ
            'quick_prompt' => 'Sen profesyonel bir blog yazarısın. Verilen konuyla ilgili engaging, SEO-friendly ve okuyucu odaklı blog yazıları oluştururun. Her yazınızda güçlü bir giriş, değerli içerik ve net bir sonuç bulunur.',
            'response_template' => json_encode([
                'sections' => ['📝 Başlık ve Alt Başlık', '🎯 Meta Açıklama (150-160 karakter)', '🚀 Giriş Paragrafı', '📖 Ana İçerik (Alt başlıklarla)', '✨ Sonuç ve CTA', '🔍 SEO Anahtar Kelimeler', '💡 İç Link Önerileri'],
                'format' => 'structured_blog_content',
                'features' => ['seo_optimization', 'engaging_tone', 'cta_inclusion', 'keyword_density'],
                'word_count_range' => [400, 1500],
                'seo_score' => true
            ]),
            
            // HELPER SİSTEMİ
            'helper_function' => 'ai_blog_yaz',
            'helper_examples' => json_encode([
                'basic' => [
                    'description' => 'Basit blog yazısı oluşturma',
                    'code' => "ai_blog_yaz('Web tasarım trendleri 2025')",
                    'estimated_tokens' => 150
                ],
                'advanced' => [
                    'description' => 'Detayları özelleştirme',
                    'code' => "ai_blog_yaz('E-ticaret SEO rehberi', ['uzunluk' => 1200, 'ton' => 'uzman', 'hedef_kitle' => 'girişimciler'])",
                    'estimated_tokens' => 200
                ],
                'seo_focused' => [
                    'description' => 'SEO odaklı blog yazısı',
                    'code' => "ai_blog_yaz('Dijital pazarlama stratejileri', ['seo_odakli' => true, 'anahtar_kelime' => 'dijital pazarlama'])",
                    'estimated_tokens' => 180
                ]
            ]),
            'helper_parameters' => json_encode([
                'konu' => 'string - Blog yazısının ana konusu (zorunlu)',
                'options' => 'array - Ek ayarlar',
                'options.uzunluk' => 'int - Kelime sayısı (400-1500)',
                'options.ton' => 'string - Yazım tonu (samimi, profesyonel, uzman)',
                'options.hedef_kitle' => 'string - Hedef okuyucu kitlesi',
                'options.seo_odakli' => 'bool - SEO optimizasyonu aktif/pasif',
                'options.anahtar_kelime' => 'string - Ana anahtar kelime'
            ]),
            'helper_description' => 'Verilen konuya göre SEO uyumlu, okunabilir ve etkileşimli blog yazıları oluşturur.',
            'helper_returns' => json_encode([
                'type' => 'array',
                'structure' => [
                    'baslik' => 'string - Ana başlık',
                    'meta_aciklama' => 'string - SEO meta description',
                    'giris' => 'string - Giriş paragrafı',
                    'ana_icerik' => 'string - Ana içerik (HTML formatında)',
                    'sonuc' => 'string - Sonuç ve CTA',
                    'anahtar_kelimeler' => 'array - SEO anahtar kelimeleri',
                    'ic_linkler' => 'array - İç link önerileri',
                    'seo_puani' => 'int - SEO puanı (0-100)'
                ]
            ]),
            
            // UI AYARLARI
            'icon' => 'fas fa-blog',
            'emoji' => '📝',
            'badge_color' => 'primary',
            'complexity_level' => 'intermediate',
            'requires_input' => true,
            'input_placeholder' => 'Blog yazısı konusunu detaylarıyla açıklayın... (Örn: "2025 Web Tasarım Trendleri ve Kullanıcı Deneyimi")',
            
            // DURUM
            'status' => 'active',
            'is_featured' => true,
            'show_in_examples' => true,
            'sort_order' => 1,
            'is_system' => false,
            'has_custom_prompt' => true,
            'has_related_prompts' => true,
            'hybrid_system_type' => 'advanced'
            ]);
        });
        
        $this->command->info('  ✓ Blog Yazısı Oluşturucu oluşturuldu (ID: 201)');
    }

    /**
     * 🔗 BLOG WRITER RELATIONS
     * 
     * Blog Yazısı Oluşturucu feature'ının expert prompt'larla ilişkilerini kurar.
     * 
     * PROMPT İLİŞKİLERİ:
     * - EP1001 (İçerik Üretim Uzmanı) - Primary, Priority: 1
     * - EP1002 (SEO İçerik Uzmanı) - Supportive, Priority: 2  
     * - EP1003 (Blog Yazım Uzmanı) - Secondary, Priority: 3
     * 
     * ÇALIŞMA SIRASI:
     * 1. Quick Prompt (feature'ın kendi prompt'u)
     * 2. İçerik Üretim Uzmanı (genel içerik prensipleri)
     * 3. SEO İçerik Uzmanı (arama motoru optimizasyonu)
     * 4. Blog Yazım Uzmanı (blog-specific teknikleri)
     * 5. Response Template (yapılandırılmış çıktı)
     */
    private function createBlogWriterRelations(): void
    {
        TenantHelpers::central(function() {
            $blogFeatureId = 201; // Blog Yazısı Oluşturucu feature ID'si
            
            // Önce mevcut ilişkileri temizle (eğer varsa)
            DB::table('ai_feature_prompt_relations')
                ->where('feature_id', $blogFeatureId)
                ->delete();
            
            // Primary Expert: İçerik Üretim Uzmanı
            $this->createRelation($blogFeatureId, 1001, [
                'priority' => 1,
                'role' => 'primary',
                'is_active' => true,
                'conditions' => json_encode([
                    'applies_to' => 'all_content_types',
                    'user_level' => ['beginner', 'intermediate', 'advanced'],
                    'content_length' => ['short', 'medium', 'long']
                ]),
                'notes' => 'Genel içerik üretim prensipleri ve kalite standartları için kullanılır'
            ]);
            
            // Supportive Expert: SEO İçerik Uzmanı
            $this->createRelation($blogFeatureId, 1002, [
                'priority' => 2,
                'role' => 'supportive',
                'is_active' => true,
                'conditions' => json_encode([
                    'applies_when' => 'seo_optimization_enabled',
                    'content_type' => ['blog', 'article', 'web_content'],
                    'min_content_length' => 300
                ]),
                'notes' => 'Arama motoru optimizasyonu ve organik trafik artışı için kullanılır'
            ]);
            
            // Secondary Expert: Blog Yazım Uzmanı  
            $this->createRelation($blogFeatureId, 1003, [
                'priority' => 3,
                'role' => 'secondary',
                'is_active' => true,
                'conditions' => json_encode([
                    'content_type' => 'blog_post',
                    'engagement_focus' => true,
                    'social_sharing' => 'enabled'
                ]),
                'notes' => 'Blog formatına özel teknikler ve engagement stratejileri için kullanılır'
            ]);
            
            $this->command->info('  ✓ Blog Writer feature-prompt ilişkileri oluşturuldu');
        });
    }
    
    /**
     * Feature-Prompt ilişkisi oluştur
     */
    private function createRelation(int $featureId, int $promptId, array $data): void
    {
        $relationData = array_merge([
            'feature_id' => $featureId,
            'prompt_id' => null, // AI Prompts tablosuna referans yok
            'feature_prompt_id' => $promptId, // AI Feature Prompts tablosuna referans
            'created_at' => now(),
            'updated_at' => now()
        ], $data);
        
        DB::table('ai_feature_prompt_relations')->insert($relationData);
    }
}