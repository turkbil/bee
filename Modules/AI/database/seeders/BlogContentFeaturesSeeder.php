<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use App\Helpers\TenantHelpers;

/**
 * 🎯 BLOG YAZISI OLUŞTURUCU SEEDER - V3 UNIVERSAL INPUT SYSTEM
 * 
 * Bu seeder sadece Blog Yazısı Oluşturucu feature'ını oluşturur.
 * Temiz ve basit bir AI blog yazma sistemi sağlar.
 * 
 * FEATURE:
 * - Blog Yazısı Oluşturucu (ID: 201) - Kolay kullanılabilir blog yazma AI'ı
 * 
 * NASIL ÇALIŞIR:
 * 1. Kullanıcı blog konusunu yazar
 * 2. AI uzman bilgisiyle blog yazısı oluşturur
 * 3. SEO-optimized, okunabilir içerik üretir
 * 
 * ÖZELLİKLER:
 * - Yazım tonu seçeneği (Profesyonel, Samimi, Eğitici)
 * - İçerik uzunluğu seçeneği (Kısa, Orta, Uzun)
 * - SEO-friendly başlık ve meta açıklama
 * - Otomatik anahtar kelime önerileri
 */
class BlogContentFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Blog Yazısı Oluşturucu feature\'ı ekleniyor...');
        
        // Varolan blog feature'larını temizle (re-seed için)
        $this->clearExistingBlogFeatures();
        
        // Sadece blog yazısı oluşturucu feature'ını oluştur
        $this->seedBlogPostCreator();          // FT201
        
        $this->command->info('✅ Blog Yazısı Oluşturucu feature\'ı başarıyla eklendi!');
    }
    
    /**
     * Varolan blog feature'larını temizle
     */
    private function clearExistingBlogFeatures(): void
    {
        // Direct database operation - tenant context
            // Sadece blog yazısı oluşturucu (ID: 201) temizle
            AIFeature::where('id', 201)
                     ->where('ai_feature_category_id', 2)
                     ->delete();
        $this->command->warn('🧹 Varolan blog yazısı oluşturucu temizlendi.');
    }
    
    /**
     * FT201 - Blog Yazısı Oluşturucu (Primary Blog Feature)
     * 
     * PROMPT HIERARCHY:
     * 1. Quick Prompt: "Sen profesyonel bir blog yazarısın"  
     * 2. Expert Prompts (Relations ile bağlanacak):
     *    - EP1001 (İçerik Üretim Uzmanı) - Primary, Priority: 1
     *    - EP1003 (Blog Yazarı Uzmanı) - Specialized, Priority: 2
     *    - EP1002 (SEO İçerik Uzmanı) - Supportive, Priority: 2
     *    - EP1004 (Yaratıcı İçerik Uzmanı) - Optional, Priority: 3
     * 
     * RESPONSE TEMPLATE:
     * - Sections: Başlık, Giriş, Ana İçerik Bölümleri, Sonuç, Meta Açıklama, Anahtar Kelimeler
     * - Format: structured_blog_content
     * - Features: seo_optimized, social_ready, engaging_format
     * 
     * UNIVERSAL INPUT SYSTEM:
     * - Primary Input: Blog konusu (textarea, required)
     * - Optional Groups: SEO Ayarları, Sosyal Medya, Gelişmiş Seçenekler
     * 
     * HELPER FUNCTION: 
     * - Name: ai_blog_yaz
     * - Usage: ai_blog_yaz('Yapay zeka trendleri 2025', ['uzunluk' => 800, 'ton' => 'profesyonel'])
     */
    private function seedBlogPostCreator(): void
    {
        // Direct database operation - tenant context
            AIFeature::create([
                'id' => 201, // Content kategorisi: 200-299
                'ai_feature_category_id' => 2, // İçerik Üretimi kategorisi
                'name' => 'Blog Yazısı Oluşturucu',
                'slug' => 'blog-yazisi-olusturucu',
                'description' => 'Kolay kullanımlı AI blog yazma asistanı. Sadece konunuzu yazın, AI sizin için profesyonel, SEO uyumlu ve okunabilir blog yazıları oluştursun.',
                
                // V3 UNIVERSAL INPUT SYSTEM - NEW FIELDS
                'module_type' => 'blog',
                'category' => 'content_generation',
                'supported_modules' => json_encode(['page', 'blog', 'portfolio', 'announcement']),
                'context_rules' => json_encode([
                    'auto_activate' => ['blog_creation', 'content_writing'],
                    'module_specific' => ['blog' => true, 'page' => true],
                    'user_level' => ['beginner', 'intermediate', 'advanced'],
                    'content_type' => ['blog', 'article', 'post']
                ]),
                'template_support' => true,
                'bulk_support' => true,
                'streaming_support' => true,
                
                // PROMPT SİSTEMİ (V3 Hybrid System)
                'quick_prompt' => 'Sen profesyonel bir blog yazarısın. Verilen konuda engaging, SEO-friendly ve okuyucu odaklı blog yazıları oluştururun. İçeriğin informatif, okunabilir ve actionable olmasına odaklan.',
                'response_template' => json_encode([
                    'sections' => [
                        'Çekici Başlık (50-60 karakter)',
                        'Meta Açıklama (150-160 karakter)', 
                        'Giriş (Hook + Konu Tanıtımı)',
                        'Ana İçerik (Alt Başlıklar ile Bölümlenmiş)',
                        'Sonuç (Özet + Call-to-Action)',
                        'SEO Anahtar Kelimeler',
                        'Sosyal Medya Önerileri'
                    ],
                    'format' => 'structured_blog_content',
                    'features' => ['seo_optimized', 'social_ready', 'engaging_format'],
                    'word_count_range' => [400, 1500],
                    'seo_requirements' => ['meta_description', 'keywords', 'headers'],
                    'social_elements' => ['shareable_quotes', 'hashtag_suggestions']
                ]),
                
                // HELPER SİSTEMİ
                'helper_function' => 'ai_blog_yaz',
                'helper_examples' => json_encode([
                    'basic' => [
                        'description' => 'Basit blog yazısı oluşturma',
                        'code' => "ai_blog_yaz('Web tasarım trendleri 2025')",
                        'estimated_tokens' => '800-1200'
                    ],
                    'advanced' => [
                        'description' => 'Detaylı ayarlar ile blog yazısı',
                        'code' => "ai_blog_yaz('SEO ipuçları', ['uzunluk' => 1000, 'ton' => 'profesyonel', 'hedef_kitle' => 'dijital pazarlamacılar'])",
                        'estimated_tokens' => '1200-1800'
                    ],
                    'seo_focused' => [
                        'description' => 'SEO odaklı blog yazısı',
                        'code' => "ai_blog_yaz('E-ticaret SEO rehberi', ['seo' => true, 'anahtar_kelime' => 'e-ticaret seo', 'meta' => true])",
                        'estimated_tokens' => '1500-2000'
                    ]
                ]),
                'helper_parameters' => json_encode([
                    'konu' => 'string - Blog yazısının ana konusu (required)',
                    'uzunluk' => 'integer - Hedef kelime sayısı (400-1500, default: 800)',
                    'ton' => 'string - Yazım tonu (profesyonel, samimi, eğitici, default: profesyonel)',
                    'hedef_kitle' => 'string - Target audience (genel, uzman, başlangıç, default: genel)',
                    'seo' => 'boolean - SEO optimizasyonu aktif (default: true)',
                    'anahtar_kelime' => 'string - Ana anahtar kelime (optional)',
                    'meta' => 'boolean - Meta description oluştur (default: true)',
                    'sosyal_medya' => 'boolean - Sosyal medya önerileri ekle (default: false)'
                ]),
                'helper_description' => 'Professional blog yazıları oluşturan AI helper function. SEO-optimized, engaging ve target audience-specific içerikler üretir.',
                'helper_returns' => json_encode([
                    'baslik' => 'string - SEO-optimized blog başlığı',
                    'meta_aciklama' => 'string - Meta description (150-160 kar.)',
                    'icerik' => 'string - Tam blog içeriği (HTML formatted)',
                    'anahtar_kelimeler' => 'array - SEO anahtar kelimeler',
                    'sosyal_medya' => 'array - Sosyal medya önerileri (optional)',
                    'word_count' => 'integer - Toplam kelime sayısı',
                    'seo_score' => 'integer - SEO optimizasyon puanı (%)'
                ]),
                
                // UI AYARLARI
                'icon' => 'ti ti-pencil',
                'emoji' => '📝',
                'badge_color' => 'primary',
                'complexity_level' => 'intermediate',
                'requires_input' => true,
                'input_placeholder' => 'Hangi konu hakkında blog yazısı yazmak istiyorsunuz? (örn: "Evden çalışma ipuçları" veya "Sağlıklı yaşam tavsiyeleri")',
                'button_text' => 'Blog Yazısı Oluştur',
                
                // USAGE & ANALYTICS
                'example_inputs' => json_encode([
                    ['text' => 'Yapay zeka ve dijital pazarlama', 'label' => 'Teknoloji Konusu'],
                    ['text' => 'Uzaktan çalışma verimliliği', 'label' => 'İş Hayatı Konusu'],
                    ['text' => 'Sürdürülebilir yaşam ipuçları', 'label' => 'Yaşam Tarzı Konusu']
                ]),
                
                // DURUM VE SİSTEM
                'status' => 'active',
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 1,
                'is_system' => false,
                'usage_count' => 0,
                'avg_rating' => 0.0,
                'rating_count' => 0,
                
                // V3 ADVANCED FEATURES
                'hybrid_system_type' => 'advanced', // Uses both quick_prompt and expert_prompts
                'has_custom_prompt' => true,
                'has_related_prompts' => true, // Will be connected via relations
                
                // INPUT VALIDATION & SETTINGS
                'input_validation' => json_encode([
                    'konu' => ['required', 'string', 'min:10', 'max:500'],
                    'uzunluk' => ['integer', 'min:300', 'max:2000'],
                    'ton' => ['string', 'in:profesyonel,samimi,eğitici,satış_odaklı'],
                    'hedef_kitle' => ['string', 'max:100']
                ]),
                'settings' => json_encode([
                    'max_processing_time' => 120, // seconds
                    'auto_save_drafts' => true,
                    'enable_preview' => true,
                    'allow_regeneration' => true,
                    'social_sharing' => true
                ]),
                'error_messages' => json_encode([
                    'validation_failed' => 'Girilen bilgiler geçersiz. Lütfen kontrol edin.',
                    'processing_timeout' => 'İşlem zaman aşımına uğradı. Lütfen tekrar deneyin.',
                    'api_error' => 'AI servisinde geçici bir sorun oluştu.',
                    'token_exceeded' => 'Token limiti aşıldı. Daha kısa bir metin deneyin.'
                ]),
                'success_messages' => json_encode([
                    'content_generated' => 'Blog yazısı başarıyla oluşturuldu!',
                    'draft_saved' => 'Taslak kaydedildi.',
                    'preview_ready' => 'Önizleme hazır.'
                ])
            ]);
        $this->command->info('  ✓ Blog Yazısı Oluşturucu oluşturuldu (FT201)');
    }
    
}