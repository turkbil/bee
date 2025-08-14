<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\TenantHelpers;
// Universal seeder'lar artık AIDatabaseSeeder'dan çalışıyor

/**
 * Modern Blog Content Seeder - SADELEŞEN AI SEEDER SİSTEMİ
 * 
 * Bu seeder sadeleştirilmiş AI sistem yapısını kullanır:
 * - AI_PROMPTS: Universal sistem promptları (yazım tonu, içerik uzunluğu)
 * - AI_FEATURE_PROMPTS: Expert knowledge promptları
 * - AI_FEATURE_PROMPT_RELATIONS: Çoklu bağlantı sistemi
 * - SADECE 1 FEATURE: Blog Yazısı Oluşturma (201)
 * 
 * ÇALIŞMA SIRASI:
 * 1. Universal Writing Tone Prompts (ai_prompts tablosuna)
 * 2. Universal Content Length Prompts (ai_prompts tablosuna)
 * 3. Blog Expert Prompts (ai_feature_prompts tablosuna)
 * 4. Tek Blog Feature (Blog Yazısı Oluşturma)
 * 5. Feature-Prompt Relations (5 expert ile bağlantı)
 */
class ModernBlogContentSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            return;
        }

        $this->command->info('🚀 SADELEŞEN AI SEEDER SİSTEMİ BAŞLIYOR...');
        $this->command->info('📋 Tek Feature Sistemi: ai_prompts (universal) + ai_feature_prompts (experts) + relations');
        
        // PHASE 1: Universal System Prompts (Artık AIDatabaseSeeder'da çalışıyor)
        $this->command->info('');
        $this->command->info('📝 PHASE 1: Universal System Prompts (AIDatabaseSeeder\'dan çalıştırılıyor)');
        
        // PHASE 2: Expert Prompts (AI_FEATURE_PROMPTS tablosu)
        $this->command->info('');
        $this->command->info('🧠 PHASE 2: Expert Prompts (AI_FEATURE_PROMPTS)');
        $this->call(\Modules\AI\Database\Seeders\BlogContentExpertPromptsSeeder::class);
        
        // PHASE 3: Tek Feature (Blog Yazısı Oluşturma)
        $this->command->info('');
        $this->command->info('⚡ PHASE 3: Tek Blog Feature (201)');
        $this->call(\Modules\AI\Database\Seeders\BlogContentFeaturesSeeder::class);
        
        // PHASE 4: Feature-Prompt Relations (Çoklu bağlantı)
        $this->command->info('');
        $this->command->info('🔗 PHASE 4: Feature-Prompt Relations');
        $this->call(\Modules\AI\Database\Seeders\BlogContentFeaturePromptRelationsSeeder::class);
        
        $this->command->info('');
        $this->command->info('✅ SADELEŞEN AI SEEDER SİSTEMİ BAŞARIYLA TAMAMLANDI!');
        $this->command->info('');
        
        // BAŞARI RAPORU
        $this->showSuccessReport();
    }
    
    private function showSuccessReport(): void
    {
        $this->command->info('📊 BAŞARI RAPORU:');
        $this->command->info('');
        $this->command->info('🎨 Universal Writing Tone Prompts: 8 adet (Professional, Casual, Friendly, vs.)');
        $this->command->info('📏 Universal Content Length Prompts: 8 adet (Short, Medium, Long, vs.)');
        $this->command->info('🧠 Blog Expert Prompts: 5 adet (İçerik Üretim, SEO, Blog Yazarı, vs.)');
        $this->command->info('⚡ Tek Blog Feature: 1 adet (Blog Yazısı Oluşturma - ID: 201)');
        $this->command->info('🔗 Feature-Prompt Relations: 5 adet (Tek feature için 5 expert bağlantısı)');
        $this->command->info('');
        $this->command->info('🎯 SADELEŞEN SİSTEM ÖZELLİKLERİ:');
        $this->command->info('   ✓ Universal yazım tonu prompts (tüm feature\'larda kullanılabilir)');
        $this->command->info('   ✓ Universal içerik uzunluğu prompts (tüm feature\'larda kullanılabilir)');
        $this->command->info('   ✓ Expert knowledge\'lar ai_feature_prompts tablosunda');
        $this->command->info('   ✓ Tek feature 5 farklı expert prompt kullanıyor');
        $this->command->info('   ✓ Priority ve role tabanlı expert sistem (primary, secondary, supportive)');
        $this->command->info('   ✓ Tamamen database-driven, hardcode yok');
        $this->command->info('   ✓ Kolay kullanımlı blog yazma asistanı hazır');
        $this->command->info('');
        $this->command->info('🚀 Sistem hazır! Blog yazma feature\'ı kullanılabilir.');
    }
}