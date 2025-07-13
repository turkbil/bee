<?php

namespace Modules\AI\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;

/**
 * AI PROFILE QUESTIONS PRIORITY SEEDER
 * 
 * Mevcut sorulara priority, ai_weight ve category değerlerini atar
 * Priority sistemi: 1=critical, 5=rarely used
 * City priority=4 (düşük vurgu)
 */
class AIProfileQuestionsPrioritySeeder extends Seeder
{
    public function run(): void
    {
        // Mevcut soruları priority sistemine göre güncelle
        $this->updateQuestionPriorities();
    }

    /**
     * Update existing questions with priority, ai_weight, category
     */
    private function updateQuestionPriorities(): void
    {
        $priorityMap = [
            // PRIORITY 1: CRITICAL (×1.5 boost) - En önemli alanlar
            'brand_name' => ['priority' => 1, 'ai_weight' => 95, 'category' => 'company'],
            'brand_character' => ['priority' => 1, 'ai_weight' => 90, 'category' => 'ai'],
            'writing_style' => ['priority' => 1, 'ai_weight' => 90, 'category' => 'ai'],
            
            // PRIORITY 2: IMPORTANT (×1.2 boost) - Önemli alanlar
            'sector_selection' => ['priority' => 2, 'ai_weight' => 85, 'category' => 'sector'],
            'main_business_activities' => ['priority' => 2, 'ai_weight' => 80, 'category' => 'sector'],
            'target_customers' => ['priority' => 2, 'ai_weight' => 75, 'category' => 'sector'],
            'brand_personality' => ['priority' => 2, 'ai_weight' => 75, 'category' => 'sector'],
            
            // PRIORITY 3: NORMAL (×1.0 no change) - Normal alanlar
            'sales_approach' => ['priority' => 3, 'ai_weight' => 70, 'category' => 'ai'],
            'business_start_year' => ['priority' => 3, 'ai_weight' => 60, 'category' => 'company'],
            'founder_name' => ['priority' => 3, 'ai_weight' => 60, 'category' => 'founder'],
            'ai_response_style' => ['priority' => 3, 'ai_weight' => 65, 'category' => 'ai'],
            
            // PRIORITY 4: OPTIONAL (×0.6 penalty) - Opsiyonel alanlar  
            'city' => ['priority' => 4, 'ai_weight' => 40, 'category' => 'company'],           // ⬇️ DÜŞÜRÜLDÜ
            'founder_story' => ['priority' => 4, 'ai_weight' => 50, 'category' => 'founder'],
            'founder_position' => ['priority' => 4, 'ai_weight' => 45, 'category' => 'founder'],
            'founder_qualities' => ['priority' => 4, 'ai_weight' => 45, 'category' => 'founder'],
            'share_founder_info' => ['priority' => 4, 'ai_weight' => 30, 'category' => 'founder'],
            
            // PRIORITY 5: RARELY USED (×0.3 penalty) - Nadir kullanılan
            // (Şu an yok, ileride eklenebilir)
        ];

        foreach ($priorityMap as $questionKey => $settings) {
            AIProfileQuestion::where('question_key', $questionKey)
                ->update([
                    'priority' => $settings['priority'],
                    'ai_weight' => $settings['ai_weight'],
                    'category' => $settings['category']
                ]);
                
            echo "✅ Updated {$questionKey}: Priority {$settings['priority']}, Weight {$settings['ai_weight']}, Category {$settings['category']}\n";
        }
        
        // Güncellenen kayıtları kontrol et
        $this->validateUpdates();
    }

    /**
     * Güncellemeleri doğrula
     */
    private function validateUpdates(): void
    {
        $criticalCount = AIProfileQuestion::where('priority', 1)->count();
        $importantCount = AIProfileQuestion::where('priority', 2)->count();
        $normalCount = AIProfileQuestion::where('priority', 3)->count();
        $optionalCount = AIProfileQuestion::where('priority', 4)->count();
        
        echo "\n📊 PRIORITY DISTRIBUTION:\n";
        echo "🔥 Critical (Priority 1): {$criticalCount} questions\n";
        echo "⚡ Important (Priority 2): {$importantCount} questions\n"; 
        echo "📝 Normal (Priority 3): {$normalCount} questions\n";
        echo "📋 Optional (Priority 4): {$optionalCount} questions\n";
        
        // City'nin düştüğünü doğrula
        $cityQuestion = AIProfileQuestion::where('question_key', 'city')->first();
        if ($cityQuestion) {
            echo "\n🎯 CITY QUESTION STATUS:\n";
            echo "Priority: {$cityQuestion->priority} (4=Optional) ✅\n";
            echo "AI Weight: {$cityQuestion->ai_weight} (Düşük vurgu) ✅\n";
            echo "Final Weight: " . $cityQuestion->getFinalAIWeight() . " (40 × 0.6 = 24) ✅\n";
        }
        
        echo "\n✅ Priority system başarıyla uygulandı!\n";
    }
}