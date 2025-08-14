<?php

namespace Modules\AI\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Prompt;

/**
 * AI PROMPTS PRIORITY SEEDER
 * 
 * Tüm AI prompt türlerine priority, ai_weight ve prompt_category değerlerini atar
 * Priority sistemi: 1=critical, 5=rarely used
 * AIPriorityEngine ile tam uyumlu mapping
 */
class AIPromptsPrioritySeeder extends Seeder
{
    public function run(): void
    {
        echo "🚀 AI PROMPTS PRIORITY SYSTEM - BAŞLADI\n\n";
        
        // 1. System Prompts Priority Assignment
        $this->updateSystemPrompts();
        
        // 2. Feature Prompts Priority Assignment  
        $this->updateFeaturePrompts();
        
        // 3. Response Template Priority Assignment
        $this->updateResponseTemplates();
        
        // 4. Validation
        $this->validatePrioritySystem();
        
        echo "\n✅ TÜM AI PROMPT TÜRLERI PRIORITY SİSTEMİNE DAHİL EDİLDİ!\n";
    }

    /**
     * System prompt'ları güncelle
     */
    private function updateSystemPrompts(): void
    {
        echo "📋 SYSTEM PROMPTS GÜNCELLEME...\n";

        // PRIORITY 1: CRITICAL SYSTEM PROMPTS (×1.5 boost)
        $criticalSystemPrompts = [
            // Ortak özellikler - EN ÖNEMLİ
            [
                'name' => 'Ortak Özellikler',
                'priority' => 1,
                'ai_weight' => 100,
                'prompt_category' => 'system_common'
            ],
            
            // Gizli sistem kuralları
            [
                'prompt_type' => 'hidden_system',
                'priority' => 1,
                'ai_weight' => 95,
                'prompt_category' => 'system_hidden'
            ],
        ];

        // PRIORITY 2: IMPORTANT SYSTEM PROMPTS (×1.2 boost)
        $importantSystemPrompts = [
            // Gizli bilgi tabanı
            [
                'prompt_type' => 'secret_knowledge',
                'priority' => 2,
                'ai_weight' => 80,
                'prompt_category' => 'secret_knowledge'
            ],
        ];

        // PRIORITY 4: OPTIONAL SYSTEM PROMPTS (×0.6 penalty)
        $optionalSystemPrompts = [
            // Şartlı yanıtlar - düşük priority
            [
                'prompt_type' => 'conditional',
                'priority' => 4,
                'ai_weight' => 30,
                'prompt_category' => 'conditional_info'
            ],
        ];

        // Güncelleme işlemleri
        foreach ($criticalSystemPrompts as $promptData) {
            $this->updatePromptByIdentifier($promptData);
        }
        
        foreach ($importantSystemPrompts as $promptData) {
            $this->updatePromptByIdentifier($promptData);
        }
        
        foreach ($optionalSystemPrompts as $promptData) {
            $this->updatePromptByIdentifier($promptData);
        }
        
        echo "✅ System prompts güncellendi\n";
    }

    /**
     * Feature prompt'ları güncelle
     */
    private function updateFeaturePrompts(): void
    {
        echo "🎯 FEATURE PROMPTS GÜNCELLEME...\n";

        // PRIORITY 1: CRITICAL FEATURE COMPONENTS (×1.5 boost)
        // Quick Prompts - Feature'ın NE yapacağını tanımlar
        Prompt::where('prompt_type', 'feature')
               ->whereNotNull('name')
               ->where('is_system', false)
               ->update([
                   'priority' => 1,
                   'ai_weight' => 85,
                   'prompt_category' => 'feature_definition'
               ]);

        // PRIORITY 2: IMPORTANT EXPERT PROMPTS (×1.2 boost)
        // Expert Prompts - NASIL yapacağını anlatır
        $expertPrompts = [
            'İçerik Üretim Uzmanı' => ['priority' => 2, 'ai_weight' => 90],
            'SEO İçerik Uzmanı' => ['priority' => 2, 'ai_weight' => 85],
            'Blog Yazısı Uzmanı' => ['priority' => 2, 'ai_weight' => 80],
            'Twitter İçerik Uzmanı' => ['priority' => 2, 'ai_weight' => 75],
            'Instagram İçerik Uzmanı' => ['priority' => 2, 'ai_weight' => 75],
            'Ürün Açıklama Uzmanı' => ['priority' => 2, 'ai_weight' => 70],
            'YouTube SEO Uzmanı' => ['priority' => 2, 'ai_weight' => 70],
            'Email Pazarlama Uzmanı' => ['priority' => 2, 'ai_weight' => 65],
            'Yerel SEO Uzmanı' => ['priority' => 2, 'ai_weight' => 65],
            'Dönüşüm Optimizasyon Uzmanı' => ['priority' => 2, 'ai_weight' => 60],
        ];

        foreach ($expertPrompts as $promptName => $settings) {
            Prompt::where('name', $promptName)
                  ->where('prompt_type', 'feature')
                  ->update([
                      'priority' => $settings['priority'],
                      'ai_weight' => $settings['ai_weight'],
                      'prompt_category' => 'expert_knowledge'
                  ]);
            echo "  ✅ {$promptName}: Priority {$settings['priority']}, Weight {$settings['ai_weight']}\n";
        }

        // PRIORITY 3: NORMAL SPECIALIZED PROMPTS (×1.0 no change)
        $normalPrompts = [
            'Sivaslı Asistan' => ['priority' => 3, 'ai_weight' => 50],
            'Eğlenceli Asistan' => ['priority' => 3, 'ai_weight' => 45],
            'Resmi Asistan' => ['priority' => 3, 'ai_weight' => 40],
            'Kısa ve Öz Asistan' => ['priority' => 3, 'ai_weight' => 35],
        ];

        foreach ($normalPrompts as $promptName => $settings) {
            Prompt::where('name', $promptName)
                  ->update([
                      'priority' => $settings['priority'],
                      'ai_weight' => $settings['ai_weight'],
                      'prompt_category' => 'expert_knowledge'
                  ]);
            echo "  ✅ {$promptName}: Priority {$settings['priority']}, Weight {$settings['ai_weight']}\n";
        }
        
        echo "✅ Feature prompts güncellendi\n";
    }

    /**
     * Response template'ları güncelle
     */
    private function updateResponseTemplates(): void
    {
        echo "📝 RESPONSE TEMPLATES GÜNCELLEME...\n";

        // PRIORITY 3: NORMAL RESPONSE FORMATTING (×1.0 no change)
        // Response template'lar genellikle normal priority
        Prompt::where('prompt_type', 'response')
               ->orWhere('name', 'LIKE', '%Template%')
               ->orWhere('name', 'LIKE', '%Format%')
               ->update([
                   'priority' => 3,
                   'ai_weight' => 60,
                   'prompt_category' => 'response_format'
               ]);
        
        echo "✅ Response templates güncellendi\n";
    }

    /**
     * Prompt identifier'a göre güncelle (name veya prompt_type)
     */
    private function updatePromptByIdentifier(array $promptData): void
    {
        $query = Prompt::query();
        
        if (isset($promptData['name'])) {
            $query->where('name', $promptData['name']);
            $identifier = "Name: {$promptData['name']}";
        } elseif (isset($promptData['prompt_type'])) {
            $query->where('prompt_type', $promptData['prompt_type']);
            $identifier = "Type: {$promptData['prompt_type']}";
        } else {
            return;
        }
        
        $updateData = [
            'priority' => $promptData['priority'],
            'ai_weight' => $promptData['ai_weight'],
            'prompt_category' => $promptData['prompt_category']
        ];
        
        $updated = $query->update($updateData);
        
        if ($updated > 0) {
            echo "  ✅ {$identifier}: Priority {$promptData['priority']}, Weight {$promptData['ai_weight']}, Category {$promptData['prompt_category']}\n";
        } else {
            echo "  ⚠️ {$identifier}: Bulunamadı\n";
        }
    }

    /**
     * Priority sistemini doğrula
     */
    private function validatePrioritySystem(): void
    {
        echo "\n📊 PRİORİTY SİSTEM DOĞRULAMA:\n";
        
        // Priority dağılımı
        for ($i = 1; $i <= 5; $i++) {
            $count = Prompt::where('priority', $i)->count();
            $label = [
                1 => 'Critical (×1.5)',
                2 => 'Important (×1.2)', 
                3 => 'Normal (×1.0)',
                4 => 'Optional (×0.6)',
                5 => 'Rarely (×0.3)'
            ][$i];
            echo "🔸 Priority {$i} ({$label}): {$count} prompts\n";
        }
        
        // Category dağılımı
        echo "\n📂 CATEGORY DAĞILIMI:\n";
        $categories = [
            'system_common' => 'Ortak Sistem',
            'system_hidden' => 'Gizli Sistem',
            'feature_definition' => 'Feature Tanımı',
            'expert_knowledge' => 'Uzman Bilgisi',
            'secret_knowledge' => 'Gizli Bilgi',
            'response_format' => 'Yanıt Formatı',
            'conditional_info' => 'Şartlı Bilgi'
        ];
        
        foreach ($categories as $category => $label) {
            $count = Prompt::where('prompt_category', $category)->count();
            echo "📁 {$label} ({$category}): {$count} prompts\n";
        }
        
        // Top 5 highest weight prompts
        echo "\n🏆 EN YÜKSEK AĞIRLIKLI PROMPTLAR (Top 5):\n";
        $topPrompts = Prompt::byFinalWeight()->limit(5)->get();
        foreach ($topPrompts as $prompt) {
            $finalWeight = $prompt->getFinalAIWeight();
            echo "⭐ {$prompt->name}: {$finalWeight} (P{$prompt->priority}, W{$prompt->ai_weight}, {$prompt->prompt_category})\n";
        }
        
        echo "\n✅ Priority sistem doğrulaması tamamlandı!\n";
    }
}