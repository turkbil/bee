<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\DB;

/**
 * 🎯 BLOG İÇERİK RELATIONS SEEDER - V3 UNIVERSAL INPUT SYSTEM
 * 
 * Bu seeder Blog feature'ları ile Expert Prompt'ları arasındaki ilişkileri kurar.
 * Dokümantasyon: /readme/plan/ai-prompts/UNIVERALSAL-AI-SEEDER-CREATION-GUIDE.md
 * 
 * İLİŞKİ KURULACAK FEATURE'LAR:
 * - FT201: Blog Yazısı Oluşturucu → EP1001, EP1002, EP1003, EP1004
 * - FT202: Blog SEO Optimizasyonu → EP1002, EP1001
 * - FT203: Blog İçerik Planlama → EP1001, EP1004
 * - FT204: Blog Sosyal Medya Entegrasyonu → EP1005, EP1001
 * - FT205: Blog Performans Analizi → (Sadece kendi prompt'u)
 * 
 * KULLANILAN EXPERT PROMPTS:
 * - EP1001: İçerik Üretim Uzmanı (Primary Expert)
 * - EP1002: SEO İçerik Uzmanı (SEO Expert)
 * - EP1003: Blog Yazarı Uzmanı (Blog Specialist)
 * - EP1004: Yaratıcı İçerik Uzmanı (Creative Expert)
 * - EP1005: Sosyal Medya Entegrasyonu Uzmanı (Social Media Expert)
 * 
 * BAĞIMLILIKLAR:
 * - BlogContentExpertPromptsSeeder (Expert prompt'lar oluşturulmuş olmalı)
 * - BlogContentFeaturesSeeder (Blog feature'lar oluşturulmuş olmalı)
 * 
 * V3 İLİŞKİ SİSTEMİ ÖZELLİKLERİ:
 * - Priority-based prompt weighting (1=Kritik, 2=Önemli, 3=Normal)
 * - Role-based prompt categorization (primary, secondary, supportive, optional)
 * - Context-aware activation rules
 * - Conditional prompt loading (user level, content type, etc.)
 * - Performance optimization through smart caching
 */
class BlogContentRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Blog İçerik Feature-Prompt ilişkileri kuruluyor...');
        
        // Varolan ilişkileri temizle (re-seed için)
        $this->clearExistingRelations();
        
        // Feature-Prompt ilişkilerini kur
        $this->createBlogPostCreatorRelations();      // FT201 relations
        $this->createBlogSEOOptimizerRelations();     // FT202 relations  
        $this->createBlogContentPlannerRelations();   // FT203 relations
        $this->createBlogSocialIntegrationRelations(); // FT204 relations
        // FT205 (Blog Performans Analizi) kendi prompt'unu kullanıyor - relation yok
        
        $this->command->info('✅ Blog İçerik Feature-Prompt ilişkileri başarıyla kuruldu!');
    }
    
    /**
     * Varolan blog feature-prompt ilişkilerini temizle
     */
    private function clearExistingRelations(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            // Blog feature'ların ilişkilerini temizle (FT201-FT205)
            DB::table('ai_feature_prompt_relations')
              ->whereIn('feature_id', [201, 202, 203, 204, 205])
              ->delete();
        });
        $this->command->warn('🧹 Varolan blog feature-prompt ilişkileri temizlendi.');
    }
    
    /**
     * FT201 - Blog Yazısı Oluşturucu Relations
     * 
     * Bu feature en kapsamlı prompt zincirine sahip:
     * - EP1001 (İçerik Üretim Uzmanı) → Primary Expert, Priority: 1
     * - EP1003 (Blog Yazarı Uzmanı) → Blog Specialist, Priority: 1  
     * - EP1002 (SEO İçerik Uzmanı) → SEO Support, Priority: 2
     * - EP1004 (Yaratıcı İçerik Uzmanı) → Creative Enhancement, Priority: 3
     * 
     * CONTEXT RULES:
     * - Tüm expert'lar her durumda aktif (comprehensive blog creation)
     * - User level'a göre prompt ağırlığı değişebilir
     * - Content type'a göre bazı expert'lar ön plana çıkabilir
     */
    private function createBlogPostCreatorRelations(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            $relations = [
                // EP1001 - İçerik Üretim Uzmanı (Primary Expert)
                [
                    'feature_id' => 201, // Blog Yazısı Oluşturucu
                    'prompt_id' => null, // AI Prompts tablosuna değil
                    'feature_prompt_id' => 1001, // AI Feature Prompts tablosuna referans
                    'role' => 'primary',
                    'priority' => 1, // En yüksek öncelik
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'always_active' => true,
                        'user_level' => ['beginner', 'intermediate', 'advanced'],
                        'content_type' => ['blog', 'article', 'post']
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Primary content creation expert - always required for blog posts'
                ],
                
                // EP1003 - Blog Yazarı Uzmanı (Blog Specialist)  
                [
                    'feature_id' => 201,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1003, // Blog Yazarı Uzmanı
                    'role' => 'primary',
                    'priority' => 1, // Eş öncelik
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'always_active' => true,
                        'module_type' => ['blog'],
                        'content_format' => ['blog_post', 'article']
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Blog-specific writing expert - essential for blog format'
                ],
                
                // EP1002 - SEO İçerik Uzmanı (SEO Support)
                [
                    'feature_id' => 201,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1002, // SEO İçerik Uzmanı
                    'role' => 'supportive',
                    'priority' => 2, // Önemli seviye
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'seo_enabled' => true,
                        'keyword_provided' => false, // Hem keyword var hem yok durumlar
                        'search_optimization' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'SEO optimization support - critical for search visibility'
                ],
                
                // EP1004 - Yaratıcı İçerik Uzmanı (Creative Enhancement)
                [
                    'feature_id' => 201,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1004, // Yaratıcı İçerik Uzmanı
                    'role' => 'supportive', // Migration'da optional enum değeri yok, supportive kullanıyoruz
                    'priority' => 3, // Normal seviye
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'creative_content' => true,
                        'user_level' => ['intermediate', 'advanced'],
                        'brand_voice_important' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Creative enhancement - optional for unique voice and style'
                ]
            ];
            
            foreach ($relations as $relation) {
                DB::table('ai_feature_prompt_relations')->insert(array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        });
        $this->command->info('  ✓ Blog Yazısı Oluşturucu ilişkileri kuruldu (4 expert prompt)');
    }
    
    /**
     * FT202 - Blog SEO Optimizasyonu Relations
     * 
     * SEO odaklı feature - daha az ama focused expert'lar:
     * - EP1002 (SEO İçerik Uzmanı) → Primary SEO Expert, Priority: 1
     * - EP1001 (İçerik Üretim Uzmanı) → Content Structure Support, Priority: 2
     */
    private function createBlogSEOOptimizerRelations(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            $relations = [
                // EP1002 - SEO İçerik Uzmanı (Primary SEO Expert)
                [
                    'feature_id' => 202, // Blog SEO Optimizasyonu
                    'prompt_id' => null,
                    'feature_prompt_id' => 1002, // SEO İçerik Uzmanı
                    'role' => 'primary',
                    'priority' => 1, // En yüksek öncelik
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'always_active' => true,
                        'seo_analysis' => true,
                        'existing_content' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Primary SEO expert - leads all optimization analysis'
                ],
                
                // EP1001 - İçerik Üretim Uzmanı (Content Structure Support)
                [
                    'feature_id' => 202,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1001, // İçerik Üretim Uzmanı
                    'role' => 'supportive',
                    'priority' => 2, // Destekleyici rol
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'content_structure_analysis' => true,
                        'readability_check' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Content structure and readability support for SEO'
                ]
            ];
            
            foreach ($relations as $relation) {
                DB::table('ai_feature_prompt_relations')->insert(array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        });
        $this->command->info('  ✓ Blog SEO Optimizasyonu ilişkileri kuruldu (2 expert prompt)');
    }
    
    /**
     * FT203 - Blog İçerik Planlama Relations
     * 
     * Content strategy odaklı feature:
     * - EP1001 (İçerik Üretim Uzmanı) → Strategy Lead, Priority: 1
     * - EP1004 (Yaratıcı İçerik Uzmanı) → Creative Planning, Priority: 2
     */
    private function createBlogContentPlannerRelations(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            $relations = [
                // EP1001 - İçerik Üretim Uzmanı (Strategy Lead)
                [
                    'feature_id' => 203, // Blog İçerik Planlama
                    'prompt_id' => null,
                    'feature_prompt_id' => 1001, // İçerik Üretim Uzmanı
                    'role' => 'primary',
                    'priority' => 1,
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'content_planning' => true,
                        'strategy_development' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Primary content strategy expert for planning'
                ],
                
                // EP1004 - Yaratıcı İçerik Uzmanı (Creative Planning)
                [
                    'feature_id' => 203,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1004, // Yaratıcı İçerik Uzmanı
                    'role' => 'supportive',
                    'priority' => 2,
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'creative_planning' => true,
                        'brand_voice_planning' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Creative approach to content planning and unique angles'
                ]
            ];
            
            foreach ($relations as $relation) {
                DB::table('ai_feature_prompt_relations')->insert(array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        });
        $this->command->info('  ✓ Blog İçerik Planlama ilişkileri kuruldu (2 expert prompt)');
    }
    
    /**
     * FT204 - Blog Sosyal Medya Entegrasyonu Relations
     * 
     * Social media odaklı feature:
     * - EP1005 (Sosyal Medya Entegrasyonu Uzmanı) → Primary Social Expert, Priority: 1
     * - EP1001 (İçerik Üretim Uzmanı) → Content Adaptation Support, Priority: 2
     */
    private function createBlogSocialIntegrationRelations(): void
    {
        // AI tabloları sadece central'da - TenantHelpers kullan
        TenantHelpers::central(function() {
            $relations = [
                // EP1005 - Sosyal Medya Entegrasyonu Uzmanı (Primary Social Expert)
                [
                    'feature_id' => 204, // Blog Sosyal Medya Entegrasyonu
                    'prompt_id' => null,
                    'feature_prompt_id' => 1005, // Sosyal Medya Entegrasyonu Uzmanı
                    'role' => 'primary',
                    'priority' => 1,
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'social_media_optimization' => true,
                        'platform_adaptation' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Primary social media expert - leads platform optimization'
                ],
                
                // EP1001 - İçerik Üretim Uzmanı (Content Adaptation Support)
                [
                    'feature_id' => 204,
                    'prompt_id' => null,
                    'feature_prompt_id' => 1001, // İçerik Üretim Uzmanı
                    'role' => 'supportive',
                    'priority' => 2,
                    'is_active' => true,
                    // is_required kolonu yok - is_active kullanılıyor
                    'conditions' => json_encode([
                        'content_adaptation' => true,
                        'multi_format_content' => true
                    ]),
                    // parameters kolonu migration'da yok - conditions'da saklayacağız
                    'notes' => 'Content adaptation and multi-format content support'
                ]
            ];
            
            foreach ($relations as $relation) {
                DB::table('ai_feature_prompt_relations')->insert(array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        });
        $this->command->info('  ✓ Blog Sosyal Medya Entegrasyonu ilişkileri kuruldu (2 expert prompt)');
    }
    
    /**
     * İlişki kurma sonrası doğrulama
     */
    private function validateRelations(): void
    {
        // Direct database operation - tenant context
            // Tüm blog feature'ların ilişki sayısını kontrol et
            $relationCounts = DB::table('ai_feature_prompt_relations')
                ->whereIn('feature_id', [201, 202, 203, 204])
                ->selectRaw('feature_id, COUNT(*) as relation_count')
                ->groupBy('feature_id')
                ->get()
                ->keyBy('feature_id');
                
            $expectedCounts = [
                201 => 4, // Blog Post Creator: 4 expert prompt
                202 => 2, // Blog SEO Optimizer: 2 expert prompt
                203 => 2, // Blog Content Planner: 2 expert prompt
                204 => 2, // Blog Social Integration: 2 expert prompt
            ];
            
            foreach ($expectedCounts as $featureId => $expectedCount) {
                $actualCount = $relationCounts->get($featureId)?->relation_count ?? 0;
                
                if ($actualCount !== $expectedCount) {
                    $this->command->error("⚠️ Feature {$featureId}: Expected {$expectedCount} relations, found {$actualCount}");
                } else {
                    $this->command->info("✓ Feature {$featureId}: {$actualCount} relations validated");
                }
            }
    }
}