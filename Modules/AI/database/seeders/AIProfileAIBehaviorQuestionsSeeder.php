<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileAIBehaviorQuestionsSeeder extends Seeder
{
    /**
     * AI PROFİL YAPAY ZEKA DAVRANIŞI SORULARI - STEP 5
     * 
     * Step 5'te AI davranış ayarları, ton, stil vb.
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🤖 AI Profile Yapay Zeka Davranış Soruları - Step 5...\n";
        
        // Önce Step 5 sorularını sil
        AIProfileQuestion::where('step', 5)->delete();
        
        // Bu seeder'da oluşturacağımız question_key'leri de sil (güvenlik için)
        $questionKeys = ['ai_tone', 'ai_personality', 'emphasis_topics', 'avoid_topics', 'communication_style', 'special_instructions'];
        AIProfileQuestion::whereIn('question_key', $questionKeys)->delete();
        
        $questions = $this->getAIBehaviorQuestions();
        $questionCount = 0;
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
            $questionCount++;
        }
        
        echo "\n🎉 {$questionCount} yapay zeka davranış sorusu eklendi!\n";
    }
    
    private function getAIBehaviorQuestions(): array
    {
        return [
            // AI Ton/Stil
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'ai_tone',
                'question_text' => '🎭 AI Asistanınızın Konuşma Tonu',
                'help_text' => 'AI size nasıl bir dille hitap etsin?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'formal', 'label' => 'Resmi ve Profesyonel'],
                    ['value' => 'friendly', 'label' => 'Samimi ve Arkadaşça'],
                    ['value' => 'casual', 'label' => 'Rahat ve Günlük'],
                    ['value' => 'expert', 'label' => 'Uzman ve Teknik'],
                    ['value' => 'balanced', 'label' => 'Dengeli (Duruma Göre)']
                ],
                'is_required' => false,
                'sort_order' => 10,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ],
            
            // AI Kişiliği
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'ai_personality',
                'question_text' => '🧠 AI Asistanınızın Kişilik Özellikleri',
                'help_text' => 'AI hangi özelliklere sahip olsun?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'helpful', 'label' => 'Yardımsever'],
                    ['value' => 'creative', 'label' => 'Yaratıcı'],
                    ['value' => 'analytical', 'label' => 'Analitik'],
                    ['value' => 'patient', 'label' => 'Sabırlı'],
                    ['value' => 'proactive', 'label' => 'Proaktif'],
                    ['value' => 'detail_oriented', 'label' => 'Detayist'],
                    ['value' => 'solution_focused', 'label' => 'Çözüm Odaklı'],
                    ['value' => 'empathetic', 'label' => 'Empatik']
                ],
                'is_required' => false,
                'sort_order' => 20,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ],
            
            // Önemli Konular
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'emphasis_topics',
                'question_text' => '📢 AI Hangi Konuları Vurgulasın?',
                'help_text' => 'Markanızla ilgili hangi noktalara odaklansın?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'quality', 'label' => 'Kalite'],
                    ['value' => 'price', 'label' => 'Fiyat Avantajı'],
                    ['value' => 'speed', 'label' => 'Hız'],
                    ['value' => 'experience', 'label' => 'Deneyim'],
                    ['value' => 'innovation', 'label' => 'Yenilik'],
                    ['value' => 'reliability', 'label' => 'Güvenilirlik'],
                    ['value' => 'customer_service', 'label' => 'Müşteri Hizmeti'],
                    ['value' => 'local_advantage', 'label' => 'Yerel Avantaj'],
                    ['value' => 'other', 'label' => 'Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer vurgu konunuzu belirtiniz...']
                ],
                'is_required' => false,
                'sort_order' => 30,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ],
            
            // Kaçınılacak Konular
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'avoid_topics',
                'question_text' => '🚫 AI Hangi Konulardan Kaçınsın?',
                'help_text' => 'Hassas veya istenmeyen konular',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'politics', 'label' => 'Siyaset'],
                    ['value' => 'religion', 'label' => 'Din'],
                    ['value' => 'competitors', 'label' => 'Rakipler'],
                    ['value' => 'negative_reviews', 'label' => 'Olumsuz Yorumlar'],
                    ['value' => 'personal_info', 'label' => 'Kişisel Bilgiler'],
                    ['value' => 'pricing_details', 'label' => 'Fiyat Detayları'],
                    ['value' => 'financial_info', 'label' => 'Mali Bilgiler'],
                    ['value' => 'other', 'label' => 'Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer kaçınılacak konuları belirtiniz...']
                ],
                'is_required' => false,
                'sort_order' => 40,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ],
            
            // İletişim Tarzı
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'communication_style',
                'question_text' => '💬 AI İletişim Tarzı',
                'help_text' => 'AI nasıl bir yaklaşım sergilesin?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'direct', 'label' => 'Doğrudan ve Net'],
                    ['value' => 'detailed', 'label' => 'Detaylı ve Açıklayıcı'],
                    ['value' => 'concise', 'label' => 'Kısa ve Öz'],
                    ['value' => 'storytelling', 'label' => 'Hikaye Anlatıcı'],
                    ['value' => 'educational', 'label' => 'Eğitici ve Öğretici']
                ],
                'is_required' => false,
                'sort_order' => 50,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ],
            
            // Özel Talimatlar
            [
                'step' => 5,
                'section' => 'ai_behavior',
                'question_key' => 'special_instructions',
                'question_text' => '📝 AI İçin Özel Talimatlar (Opsiyonel)',
                'help_text' => 'AI\'ya özel davranış kuralları veya talimatlar',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 60,
                'ai_priority' => 3,
                'always_include' => false,
                'context_category' => 'ai_behavior'
            ]
        ];
    }
}