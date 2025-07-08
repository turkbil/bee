<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileQuestionsOptimizerSeeder extends Seeder
{
    /**
     * AI PROFIL SORULARI OPTİMİZASYONU
     * 
     * Bu seeder:
     * 1. Gereksiz/yorucu sorularları kaldırır
     * 2. Priority sistemini ekler
     * 3. AI için en önemli soruları vurgular
     * 4. Müşteriyi yormayan, odaklanmış sorular bırakır
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        // Önce mevcut sorulara priority ve kategori ekle
        $this->addPriorityToExistingQuestions();
        
        // Gereksiz soruları kaldır
        $this->removeUnnecessaryQuestions();
        
        // Eksik önemli soruları ekle
        $this->addMissingEssentialQuestions();
        
        echo "✅ AI Profil soruları optimize edildi!\n";
        echo "🎯 Priority sistemi eklendi\n";
        echo "🗑️ Gereksiz sorular kaldırıldı\n";
        echo "➕ Eksik önemli sorular eklendi\n";
    }
    
    private function addPriorityToExistingQuestions(): void
    {
        // ÇOK ÖNEMLİ (Priority 1) - Marka kimliği
        $criticalQuestions = [
            'brand_name' => ['priority' => 1, 'always_include' => true, 'category' => 'brand_identity'],
            'main_service' => ['priority' => 1, 'always_include' => true, 'category' => 'brand_identity'],
            'brand_personality' => ['priority' => 1, 'always_include' => true, 'category' => 'brand_identity'],
            'writing_tone' => ['priority' => 1, 'always_include' => true, 'category' => 'behavior_rules'],
        ];
        
        // ÖNEMLİ (Priority 2) - İş stratejisi
        $importantQuestions = [
            'target_audience' => ['priority' => 2, 'always_include' => true, 'category' => 'business_info'],
            'emphasis_points' => ['priority' => 2, 'always_include' => true, 'category' => 'behavior_rules'],
            'competitive_advantage' => ['priority' => 2, 'always_include' => true, 'category' => 'behavior_rules'],
            'communication_style' => ['priority' => 2, 'always_include' => true, 'category' => 'behavior_rules'],
            'avoid_topics' => ['priority' => 2, 'always_include' => true, 'category' => 'behavior_rules'],
        ];
        
        // NORMAL (Priority 3) - Detay bilgiler
        $normalQuestions = [
            'company_size' => ['priority' => 3, 'always_include' => false, 'category' => 'business_info'],
            'company_age_advantage' => ['priority' => 3, 'always_include' => false, 'category' => 'business_info'],
            'success_indicators' => ['priority' => 3, 'always_include' => false, 'category' => 'business_info'],
            'work_approach' => ['priority' => 3, 'always_include' => false, 'category' => 'behavior_rules'],
            'content_approach' => ['priority' => 3, 'always_include' => false, 'category' => 'behavior_rules'],
        ];
        
        // OPSİYONEL (Priority 4) - Ek bilgiler
        $optionalQuestions = [
            'city' => ['priority' => 4, 'always_include' => false, 'category' => 'business_info'],
            'branches' => ['priority' => 4, 'always_include' => false, 'category' => 'business_info'],
            'contact_info' => ['priority' => 4, 'always_include' => false, 'category' => 'business_info'],
            'brand_age' => ['priority' => 4, 'always_include' => false, 'category' => 'business_info'],
        ];
        
        // Priority değerlerini uygula
        $allQuestions = array_merge($criticalQuestions, $importantQuestions, $normalQuestions, $optionalQuestions);
        
        foreach ($allQuestions as $questionKey => $settings) {
            AIProfileQuestion::where('question_key', $questionKey)
                ->update([
                    'ai_priority' => $settings['priority'],
                    'always_include' => $settings['always_include'],
                    'context_category' => $settings['category']
                ]);
        }
    }
    
    private function removeUnnecessaryQuestions(): void
    {
        // KALDIRILACAK GEREKSIZ SORULAR
        $unnecessaryQuestions = [
            // Duplicate/Redundant sorular
            'market_position',  // brand_personality ile duplicate
            
            // Çok detaylı sektör soruları (AI'yı karıştırıyor)
            'product_categories', // E-ticaret için çok detaylı
            'price_range',       // Fiyat bilgisi gereksiz confusion
            'delivery_time',     // Çok spesifik, AI karışıyor
            'payment_methods',   // Çok detaylı, gereksiz
            
            // Sağlık sektörü çok detaylı sorular
            'health_branches',   // Çok spesifik
            'doctor_count',      // Gereksiz sayı bilgisi
            'health_services',   // Çok detaylı
            
            // Eğitim sektörü çok detaylı sorular
            'education_level',   // Çok spesifik
            'education_subjects', // Çok detaylı
            'education_method',  // AI karıştırıyor
            
            // Restoran çok detaylı sorular
            'cuisine_type',      // Çok spesifik
            'service_types',     // Gereksiz detay
            
            // Teknoloji çok detaylı sorular
            'tech_services',     // main_service yeterli
            'project_types',     // Çok detaylı, karıştırıyor
            
            // Gereksiz founder detayları
            'founder_name',      // Gizlilik sorunu + gereksiz
            'founder_background', // Çok detaylı
            'founder_qualities', // Yeterince var
        ];
        
        foreach ($unnecessaryQuestions as $questionKey) {
            AIProfileQuestion::where('question_key', $questionKey)->delete();
            echo "🗑️ Removed unnecessary question: {$questionKey}\n";
        }
    }
    
    private function addMissingEssentialQuestions(): void
    {
        // EKSİK ÖNEMLİ SORULAR - AI için gerekli
        $missingQuestions = [
            [
                'step' => 2,
                'question_key' => 'business_mission',
                'question_text' => 'İş Misyonunuz',
                'help_text' => 'Firmanızın temel amacı nedir? Neden bu işi yapıyorsunuz?',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 10,
                'ai_priority' => 2,
                'always_include' => true,
                'context_category' => 'brand_identity'
            ],
            [
                'step' => 6,
                'question_key' => 'brand_voice',
                'question_text' => 'Marka Sesi',
                'help_text' => 'AI\'nız müşterilerle nasıl konuşsun?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'expert', 'label' => 'Uzman ve Bilgili', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'friend', 'label' => 'Arkadaş Canlısı', 'icon' => 'fas fa-smile'],
                    ['value' => 'advisor', 'label' => 'Danışman Tavrı', 'icon' => 'fas fa-handshake'],
                    ['value' => 'leader', 'label' => 'Lider ve Yönlendirici', 'icon' => 'fas fa-crown']
                ],
                'is_required' => true,
                'sort_order' => 6,
                'ai_priority' => 1,
                'always_include' => true,
                'context_category' => 'behavior_rules'
            ],
            [
                'step' => 6,
                'question_key' => 'content_focus',
                'question_text' => 'İçerik Odağı',
                'help_text' => 'İçeriklerde en çok neyi vurgulamalı?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'solution', 'label' => 'Çözüm Odaklı', 'icon' => 'fas fa-lightbulb'],
                    ['value' => 'relationship', 'label' => 'İlişki Odaklı', 'icon' => 'fas fa-heart'],
                    ['value' => 'result', 'label' => 'Sonuç Odaklı', 'icon' => 'fas fa-trophy'],
                    ['value' => 'process', 'label' => 'Süreç Odaklı', 'icon' => 'fas fa-cogs']
                ],
                'is_required' => true,
                'sort_order' => 7,
                'ai_priority' => 2,
                'always_include' => true,
                'context_category' => 'behavior_rules'
            ]
        ];
        
        foreach ($missingQuestions as $question) {
            $existing = AIProfileQuestion::where('question_key', $question['question_key'])->first();
            if (!$existing) {
                AIProfileQuestion::create($question);
                echo "➕ Added missing essential question: {$question['question_key']}\n";
            }
        }
    }
}