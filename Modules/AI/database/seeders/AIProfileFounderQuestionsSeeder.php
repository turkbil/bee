<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileFounderQuestionsSeeder extends Seeder
{
    /**
     * AI PROFİL KURUCU BİLGİLERİ SORULARI - STEP 4
     * 
     * Step 4'te founder_permission'a göre kurucu bilgileri toplanır
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "👤 AI Profile Kurucu Bilgileri Soruları - Step 4...\n";
        
        // Önce Step 4 sorularını sil
        AIProfileQuestion::where('step', 4)->delete();
        
        $questions = $this->getFounderQuestions();
        $questionCount = 0;
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
            $questionCount++;
        }
        
        echo "\n🎉 {$questionCount} kurucu sorusu eklendi!\n";
    }
    
    private function getFounderQuestions(): array
    {
        return [
            // İzin Sorusu (Her zaman gösterilir)
            [
                'step' => 4,
                'section' => 'founder_permission',
                'question_key' => 'founder_permission',
                'question_text' => '👤 Kurucu/Sahip Bilgilerini AI Kullanabilir mi?',
                'help_text' => 'Kişisel hikayeleri paylaşmak için izin',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'yes_full', 'label' => 'Evet, Tamamını Kullanabilir'],
                    ['value' => 'yes_limited', 'label' => 'Evet, Sınırlı Bilgi'],
                    ['value' => 'no', 'label' => 'Hayır, Kullanmasın']
                ],
                'is_required' => false,
                'sort_order' => 10,
                'ai_priority' => 3,
                'always_include' => false,
                'context_category' => 'founder'
            ],
            
            // Kurucu Adı (Koşullu - permission varsa)
            [
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_name',
                'question_text' => '👋 Kurucu/Sahip Adınız',
                'help_text' => 'AI size nasıl hitap etsin?',
                'input_type' => 'text',
                'is_required' => false,
                'sort_order' => 20,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'founder',
                'depends_on' => 'founder_permission',
                'show_if' => ['yes_full', 'yes_limited']
            ],
            
            // Kurucu Unvanı (Koşullu - permission varsa)
            [
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_title',
                'question_text' => '🏷️ Unvanınız/Pozisyonunuz',
                'help_text' => 'Şirket içindeki pozisyonunuz',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'founder', 'label' => 'Kurucu'],
                    ['value' => 'ceo', 'label' => 'CEO'],
                    ['value' => 'owner', 'label' => 'Sahip'],
                    ['value' => 'partner', 'label' => 'Ortak'],
                    ['value' => 'director', 'label' => 'Genel Müdür'],
                    ['value' => 'manager', 'label' => 'Müdür'],
                    ['value' => 'other', 'label' => 'Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Unvanınızı belirtiniz...']
                ],
                'is_required' => false,
                'sort_order' => 30,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'founder',
                'depends_on' => 'founder_permission',
                'show_if' => ['yes_full', 'yes_limited']
            ],
            
            // Kurucu Özellikleri (Sadece full permission)
            [
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_qualities',
                'question_text' => '🌟 Kişisel Özellikleriniz',
                'help_text' => 'AI sizi tanıyabilmesi için',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'innovative', 'label' => 'Yenilikçi'],
                    ['value' => 'experienced', 'label' => 'Deneyimli'],
                    ['value' => 'customer_focused', 'label' => 'Müşteri Odaklı'],
                    ['value' => 'team_player', 'label' => 'Takım Oyuncusu'],
                    ['value' => 'solution_oriented', 'label' => 'Çözüm Odaklı'],
                    ['value' => 'detail_oriented', 'label' => 'Detayist'],
                    ['value' => 'creative', 'label' => 'Yaratıcı'],
                    ['value' => 'reliable', 'label' => 'Güvenilir'],
                    ['value' => 'other', 'label' => 'Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer özelliğinizi belirtiniz...']
                ],
                'is_required' => false,
                'sort_order' => 40,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'founder',
                'depends_on' => 'founder_permission',
                'show_if' => ['yes_full']
            ],
            
            // Kurucu Geçmişi (Sadece full permission)
            [
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_background',
                'question_text' => '📚 Profesyonel Geçmişiniz',
                'help_text' => 'Deneyim alanlarınız',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'sector_expert', 'label' => 'Bu Sektörde Uzman'],
                    ['value' => 'entrepreneur', 'label' => 'Seri Girişimci'],
                    ['value' => 'corporate', 'label' => 'Kurumsal Deneyim'],
                    ['value' => 'technical', 'label' => 'Teknik Geçmiş'],
                    ['value' => 'sales', 'label' => 'Satış Deneyimi'],
                    ['value' => 'management', 'label' => 'Yöneticilik'],
                    ['value' => 'international', 'label' => 'Uluslararası Deneyim'],
                    ['value' => 'startup', 'label' => 'Startup Deneyimi'],
                    ['value' => 'other', 'label' => 'Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer deneyiminizi belirtiniz...']
                ],
                'is_required' => false,
                'sort_order' => 50,
                'ai_priority' => 2,
                'always_include' => false,
                'context_category' => 'founder',
                'depends_on' => 'founder_permission',
                'show_if' => ['yes_full']
            ],
            
            // Kurucu Hikayesi (Sadece full permission)
            [
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_story',
                'question_text' => '📖 Kuruluş Hikayeniz (Opsiyonel)',
                'help_text' => 'İş fikriniz nasıl doğdu? Neden bu işe başladınız?',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 60,
                'ai_priority' => 3,
                'always_include' => false,
                'context_category' => 'founder',
                'depends_on' => 'founder_permission',
                'show_if' => ['yes_full']
            ]
        ];
    }
}