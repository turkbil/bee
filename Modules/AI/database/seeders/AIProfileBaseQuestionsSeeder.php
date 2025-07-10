<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileBaseQuestionsSeeder extends Seeder
{
    /**
     * AI PROFİL TEMEL SORULAR - 5 STEP SİSTEMİ
     * 
     * Demo sistemindeki 5-step yapısına uygun temel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 AI Profile Temel Sorular - 5-Step Wizard Sistemi...\n";
        
        // Önce mevcut temel soruları sil
        AIProfileQuestion::whereNull('sector_code')->delete();
        
        $questions = $this->getBaseQuestions();
        $questionCount = 0;
        
        foreach ($questions as $step => $stepQuestions) {
            echo "📋 Step {$step}: " . count($stepQuestions) . " soru\n";
            
            foreach ($stepQuestions as $question) {
                AIProfileQuestion::create($question);
                $questionCount++;
            }
        }
        
        echo "\n🎉 Toplam {$questionCount} temel soru eklendi!\n";
    }
    
    private function getBaseQuestions(): array
    {
        return [
            // STEP 1: Sektör Seçimi
            1 => [
                [
                    'step' => 1,
                    'section' => 'sector_selection',
                    'question_key' => 'sector',
                    'question_text' => '🏭 Hangi sektörde faaliyet gösteriyorsunuz?',
                    'help_text' => 'Size özel AI deneyimi için sektörünüzü seçin',
                    'input_type' => 'select',
                    'options' => [], // Dinamik olarak sektörlerden gelecek
                    'is_required' => true,
                    'sort_order' => 10,
                    'ai_priority' => 1,
                    'always_include' => true,
                    'context_category' => 'sector'
                ]
            ],
            
            // STEP 2: Temel Bilgiler
            2 => [
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'brand_name',
                    'question_text' => '🏢 Marka/Firma Adınız',
                    'help_text' => 'AI size bu isimle hitap edecek',
                    'input_type' => 'text',
                    'is_required' => true,
                    'sort_order' => 10,
                    'ai_priority' => 1,
                    'always_include' => true,
                    'context_category' => 'basic'
                ],
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'city',
                    'question_text' => '🌍 Bulunduğunuz Şehir/Lokasyon',
                    'help_text' => 'AI yerel referanslar verebilmesi için',
                    'input_type' => 'text',
                    'is_required' => false,
                    'sort_order' => 20,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'basic'
                ],
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'main_service',
                    'question_text' => '🎯 Ana Hizmet/Ürününüz',
                    'help_text' => 'Ne konuda uzmanınız?',
                    'input_type' => 'text',
                    'is_required' => true,
                    'sort_order' => 30,
                    'ai_priority' => 1,
                    'always_include' => true,
                    'context_category' => 'business'
                ],
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'founding_year',
                    'question_text' => '📅 Kuruluş Tarihi',
                    'help_text' => 'Deneyiminizi vurgulamak için',
                    'input_type' => 'select',
                    'options' => $this->generateYearOptions(),
                    'is_required' => false,
                    'sort_order' => 40,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'business'
                ],
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'employee_count',
                    'question_text' => '👥 Çalışan Sayınız',
                    'help_text' => 'Şirket büyüklüğünüzü belirtin',
                    'input_type' => 'radio',
                    'options' => [
                        ['value' => 'solo', 'label' => 'Bireysel (Sadece Ben)'],
                        ['value' => '2_5', 'label' => '2-5 Kişi'],
                        ['value' => '6_20', 'label' => '6-20 Kişi'],
                        ['value' => '21_50', 'label' => '21-50 Kişi'],
                        ['value' => '51_100', 'label' => '51-100 Kişi'],
                        ['value' => '101_plus', 'label' => '100+ Kişi'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ],
                    'is_required' => false,
                    'sort_order' => 50,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'business'
                ],
                [
                    'step' => 2,
                    'section' => 'basic_info',
                    'question_key' => 'target_audience',
                    'question_text' => '🎯 Hedef Müşteri Grubunuz',
                    'help_text' => 'AI bu kitleye uygun dil kullanacak',
                    'input_type' => 'checkbox',
                    'options' => [
                        ['value' => 'b2b_small', 'label' => 'Küçük İşletmeler'],
                        ['value' => 'b2b_medium', 'label' => 'Orta Ölçekli Şirketler'],
                        ['value' => 'b2b_large', 'label' => 'Büyük Korporasyonlar'],
                        ['value' => 'b2c_young', 'label' => 'Genç Bireyler (18-35)'],
                        ['value' => 'b2c_middle', 'label' => 'Orta Yaş (35-55)'],
                        ['value' => 'b2c_senior', 'label' => 'Olgun Yaş (55+)'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ],
                    'is_required' => false,
                    'sort_order' => 60,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'audience'
                ]
            ],
            
            // STEP 3: Marka Detayları
            3 => [
                [
                    'step' => 3,
                    'section' => 'brand_details',
                    'question_key' => 'brand_personality',
                    'question_text' => '🎭 Marka Kişiliğiniz',
                    'help_text' => 'AI bu tarzda konuşacak',
                    'input_type' => 'radio',
                    'options' => [
                        ['value' => 'professional', 'label' => 'Profesyonel ve Ciddi'],
                        ['value' => 'friendly', 'label' => 'Samimi ve Arkadaşça'],
                        ['value' => 'expert', 'label' => 'Uzman ve Güvenilir'],
                        ['value' => 'innovative', 'label' => 'Yenilikçi ve Modern'],
                        ['value' => 'casual', 'label' => 'Rahat ve Eğlenceli']
                    ],
                    'is_required' => false,
                    'sort_order' => 10,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'brand'
                ],
                [
                    'step' => 3,
                    'section' => 'brand_details',
                    'question_key' => 'competitive_advantages',
                    'question_text' => '🏆 Rekabet Avantajlarınız',
                    'help_text' => 'Sizi rakiplerinizden ayıran özellikler',
                    'input_type' => 'checkbox',
                    'options' => [
                        ['value' => 'price', 'label' => 'Uygun Fiyat'],
                        ['value' => 'quality', 'label' => 'Yüksek Kalite'],
                        ['value' => 'speed', 'label' => 'Hızlı Teslimat'],
                        ['value' => 'experience', 'label' => 'Deneyim'],
                        ['value' => 'innovation', 'label' => 'Yenilik'],
                        ['value' => 'service', 'label' => 'Müşteri Hizmeti'],
                        ['value' => 'local', 'label' => 'Yerel Avantaj'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ],
                    'is_required' => false,
                    'sort_order' => 20,
                    'ai_priority' => 2,
                    'always_include' => false,
                    'context_category' => 'brand'
                ]
            ],
            
            // STEP 4: Kurucu Bilgileri
            4 => [
                [
                    'step' => 4,
                    'section' => 'founder_info',
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
                ]
            ],
            
            // STEP 5: Ekstra Bilgiler
            5 => [
                [
                    'step' => 5,
                    'section' => 'extra_info',
                    'question_key' => 'additional_info',
                    'question_text' => '📝 Markanızla İlgili Eklemek İstedikleriniz',
                    'help_text' => 'Referanslar, ödüller, özel durumlar vs.',
                    'input_type' => 'textarea',
                    'is_required' => false,
                    'sort_order' => 10,
                    'ai_priority' => 3,
                    'always_include' => false,
                    'context_category' => 'extra'
                ]
            ]
        ];
    }
    
    private function generateYearOptions(): array
    {
        $options = [];
        $currentYear = date('Y');
        
        for ($year = $currentYear; $year >= 1950; $year--) {
            $options[] = [
                'value' => $year,
                'label' => $year
            ];
        }
        
        $options[] = ['value' => 'other', 'label' => 'Diğer'];
        
        return $options;
    }
}