<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use App\Helpers\TenantHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Blog Content Feature-Prompt Relations Seeder
 * 
 * Blog Yazısı Oluşturma feature'ını AI_FEATURE_PROMPTS tablosundaki expert prompt'larla bağlar.
 * Sadece tek feature (201) için çoklu expert prompt sistemi.
 */
class BlogContentFeaturePromptRelationsSeeder extends Seeder
{
    /**
     * Blog Feature ID: 201 (Blog Yazısı Oluşturma)
     * Blog Expert Prompt ID'leri: 1001-1005
     */
    private const BLOG_FEATURE_ID = 201;
    private const BLOG_EXPERT_PROMPT_IDS = [1001, 1002, 1003, 1004, 1005];
    
    public function run(): void
    {
        $this->command->info('🔗 Blog Content Feature-Prompt Relations seeding başlıyor...');
        
        TenantHelpers::central(function() {
            $this->clearExistingRelations();
            $this->createFeaturePromptRelations();
        });
        
        $this->command->info('✅ Blog Content Feature-Prompt Relations başarıyla oluşturuldu!');
    }
    
    private function clearExistingRelations(): void
    {
        DB::table('ai_feature_prompt_relations')
            ->where('feature_id', self::BLOG_FEATURE_ID)
            ->delete();
        
        $this->command->info('🧹 Mevcut blog feature-prompt relations temizlendi');
    }
    
    private function createFeaturePromptRelations(): void
    {
        $relations = $this->getFeaturePromptRelations();
        
        foreach ($relations as $relation) {
            DB::table('ai_feature_prompt_relations')->insert([
                'feature_id' => $relation['feature_id'],
                'prompt_id' => null, // ai_prompts için (sistem prompt'ları için null)
                'feature_prompt_id' => $relation['feature_prompt_id'], // ai_feature_prompts için (expert prompt'lar)
                'role' => $relation['role'],
                'priority' => $relation['priority'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $featureName = $this->getFeatureName($relation['feature_id']);
            $expertName = $this->getExpertName($relation['feature_prompt_id']);
            
            $this->command->info("🔗 {$featureName} → {$expertName} ({$relation['role']}, priority: {$relation['priority']})");
        }
    }
    
    private function getFeaturePromptRelations(): array
    {
        return [
            // Feature 201: Blog Yazısı Oluşturma - Tüm Expert'ları kullanacak (5 expert)
            ['feature_id' => 201, 'feature_prompt_id' => 1001, 'role' => 'primary', 'priority' => 1, 'weight' => 100], // İçerik Üretim Uzmanı
            ['feature_id' => 201, 'feature_prompt_id' => 1002, 'role' => 'secondary', 'priority' => 2, 'weight' => 85], // SEO İçerik Uzmanı  
            ['feature_id' => 201, 'feature_prompt_id' => 1003, 'role' => 'secondary', 'priority' => 2, 'weight' => 80], // Blog Yazarı Uzmanı
            ['feature_id' => 201, 'feature_prompt_id' => 1004, 'role' => 'supportive', 'priority' => 3, 'weight' => 70], // Yaratıcı İçerik Uzmanı
            ['feature_id' => 201, 'feature_prompt_id' => 1005, 'role' => 'supportive', 'priority' => 3, 'weight' => 65], // Sosyal Medya Entegrasyonu Uzmanı
        ];
    }
    
    private function getFeatureName(int $featureId): string
    {
        return 'Blog Yazısı Oluşturma';
    }
    
    private function getExpertName(int $expertId): string
    {
        $experts = [
            1001 => 'İçerik Üretim Uzmanı',
            1002 => 'SEO İçerik Uzmanı',
            1003 => 'Blog Yazarı Uzmanı',
            1004 => 'Yaratıcı İçerik Uzmanı',
            1005 => 'Sosyal Medya Entegrasyonu Uzmanı',
        ];
        
        return $experts[$expertId] ?? "Expert {$expertId}";
    }
}