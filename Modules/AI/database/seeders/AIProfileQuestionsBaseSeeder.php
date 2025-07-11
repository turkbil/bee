<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileQuestionsBaseSeeder extends Seeder
{
    /**
     * AI PROFİL SORULARI - TEMEL SORULAR (PART 1)
     * 
     * Tüm sektörler için ortak temel sorular
     * Step 1-6: Sektör seçimi, temel bilgiler, marka detayları, kurucu hikayesi, başarı hikayeleri, AI davranış kuralları
     */
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Questions - TEMEL SORULAR (Part 1/2) Yükleniyor...\n";
        
        // Önce mevcut soruları temizle
        AIProfileQuestion::truncate();
        
        // ADIM 1: Sektör Seçimi (Önce sektör belirleyelim)
        $this->createSectorSelectionQuestion();
        
        // ADIM 2: Temel Bilgiler (İsim, şehir, olmazsa olmaz)
        $this->createBasicInfoQuestions();
        
        // ADIM 3: Marka Detayları (Şubeleşme, büyüklük, vb)
        $this->createBrandDetailsQuestions();
        
        // ADIM 4: Kurucu Bilgileri (İzin sistemi ile)
        $this->createFounderPermissionQuestion();
        
        // ADIM 5: Yapay Zeka Yanıt Tarzı (Tüm sektörler için ortak)
        $this->createAIResponseStyleQuestions();
        
        // ADIM 6: Yapay Zeka Davranış Kuralları (Tüm sektörler için ortak)
        $this->createAIBehaviorQuestions();
        
        echo "\n🎯 Temel sorular tamamlandı! (Part 1/2)\n";
    }
    
    /**
     * ADIM 1: Sektör Seçimi Sorusu
     */
    private function createSectorSelectionQuestion(): void
    {
        AIProfileQuestion::create([
            'id' => 1,
            'step' => 1,
            'question_key' => 'sector_selection',
            'question_text' => 'Hangi sektörde faaliyet gösteriyorsunuz?',
            'help_text' => 'Lütfen ana sektörünüzü seçin. Bu seçim sonraki soruları belirleyecektir.',
            'input_type' => 'sector_select',
            'is_required' => true,
            'sort_order' => 10,
            'is_active' => true
        ]);
        
        echo "✅ Sektör seçimi sorusu eklendi\n";
    }
    
    /**
     * ADIM 2: Temel Bilgiler - Olmazsa olmaz
     */
    private function createBasicInfoQuestions(): void
    {
        $questions = [
            [
                'id' => 101,
                'step' => 2,
                'question_key' => 'brand_name',
                'question_text' => 'Marka/Firma Adı',
                'help_text' => 'Resmi firma adınızı yazın',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 102,
                'step' => 2,
                'question_key' => 'city',
                'question_text' => 'Hangi şehirdesiniz?',
                'help_text' => 'Ana faaliyet şehrinizi belirtin',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 103,
                'step' => 2,
                'question_key' => 'experience_years',
                'question_text' => 'Hangi yıldan beri bu işi yapıyorsunuz?',
                'help_text' => 'İşe başladığınız yılı yazın veya deneyim sürenizi belirtin (Örn: 2020, 2015 yılından beri, 10+ yıllık deneyim, aile işi vb.)',
                'input_type' => 'select_with_custom',
                'options' => json_encode([]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Temel bilgi soruları eklendi (4 soru)\n";
    }
    
    /**
     * ADIM 3: Marka Detayları
     */
    private function createBrandDetailsQuestions(): void
    {
        $questions = [
            [
                'id' => 201,
                'step' => 3,
                'question_key' => 'target_audience',
                'question_text' => 'Ana müşteri kitleniz kimler?',
                'help_text' => 'Öncelikli hedef müşterilerinizi seçin (çoklu seçim)',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Bireysel müşteriler',
                    'Küçük işletmeler',
                    'Büyük şirketler',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Hedef müşteri kitlenizi belirtiniz'
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 202,
                'step' => 3,
                'question_key' => 'brand_voice',
                'question_text' => 'Marka kişiliğiniz nasıl olmalı?',
                'help_text' => 'AI asistanınızın nasıl konuşmasını istiyorsunuz?',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Uzman ve güvenilir',
                    'Samimi ve yakın',
                    'Profesyonel ve ciddi',
                    'Yenilikçi ve modern',
                    'Prestijli ve lüks',
                    'Hızlı ve dinamik',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Marka kişiliğinizi belirtiniz'
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => true,
                'sort_order' => 20
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Marka detayları soruları eklendi (2 soru)\n";
    }
    
    /**
     * ADIM 4: Kurucu Bilgileri Soruları
     */
    private function createFounderPermissionQuestion(): void
    {
        $questions = [
            // İzin sorusu
            [
                'id' => 301,
                'step' => 4,
                'section' => 'company_info',
                'question_key' => 'founder_permission',
                'question_text' => 'Kurucu hakkında bilgi paylaşmak ister misiniz?',
                'help_text' => 'Kişisel hikayeniz marka güvenilirliğini artırır. Paylaşmak tamamen isteğe bağlıdır.',
                'input_type' => 'radio',
                'options' => json_encode([
                    'Evet, bilgilerimi paylaşmak istiyorum',
                    'Hayır, sadece işletme bilgileri yeterli'
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => true,
                'sort_order' => 10
            ],
            
            // Conditional sorular (izin verilirse açılacak)
            [
                'id' => 302,
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_name',
                'question_text' => 'Kurucunun adı soyadı',
                'help_text' => 'Müşterilerle paylaşılacak kurucu isim bilgisi',
                'input_type' => 'text',
                'is_required' => false,
                'sort_order' => 20,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, bilgilerimi paylaşmak istiyorum'], JSON_UNESCAPED_UNICODE)
            ],
            
            [
                'id' => 303,
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_position',
                'question_text' => 'Pozisyonunuz',
                'help_text' => 'Şirketteki pozisyonunuzu seçin',
                'input_type' => 'radio',
                'options' => json_encode([
                    'Kurucu & Sahip',
                    'Genel Müdür',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Pozisyonunuzu belirtiniz'
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => false,
                'sort_order' => 30,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, bilgilerimi paylaşmak istiyorum'], JSON_UNESCAPED_UNICODE)
            ],
            
            [
                'id' => 304,
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_qualities',
                'question_text' => 'Kurucuyu tanıtan özellikler',
                'help_text' => 'Kendinizi en iyi hangi özellikler tanımlıyor? (çoklu seçim)',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Liderlik',
                    'Vizyonerlik',
                    'Yaratıcılık',
                    'Güvenilirlik',
                    'Kararlılık',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Diğer özelliklerinizi belirtiniz'
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => false,
                'sort_order' => 40,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, bilgilerimi paylaşmak istiyorum'], JSON_UNESCAPED_UNICODE)
            ],
            
            [
                'id' => 305,
                'step' => 4,
                'section' => 'founder_info',
                'question_key' => 'founder_story',
                'question_text' => 'Kendiniz hakkında ne paylaşmak istersiniz?',
                'help_text' => 'Hikayeniz, deneyimleriniz, eğitiminiz, başarılarınız, vizyonunuz... İstediğiniz her şeyi yazabilirsiniz. Ne kadar detaylandırırsanız AI o kadar kişisel yanıtlar verebilir.',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 50,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, bilgilerimi paylaşmak istiyorum'], JSON_UNESCAPED_UNICODE)
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Kurucu bilgileri soruları eklendi (5 soru)\n";
    }
    
    /**
     * ADIM 5: Yapay Zeka Yanıt Tarzı
     */
    private function createAIResponseStyleQuestions(): void
    {
        $questions = [
            [
                'id' => 401,
                'step' => 5,
                'question_key' => 'ai_response_style',
                'question_text' => 'AI asistanınız nasıl yanıt versin?',
                'help_text' => 'AI asistanınızın yanıt verme karakterini seçin',
                'input_type' => 'radio',
                'options' => json_encode([
                    'Profesyonel ve ciddi',
                    'Samimi ve dostane',
                    'Komik ve eğlenceli',
                    'Hızlı ve pratik',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'AI yanıt tarzını belirtiniz'
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => true,
                'sort_order' => 10
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ AI yanıt tarzı soruları eklendi (1 soru)\n";
    }
    
    /**
     * ADIM 6: AI Davranış Kuralları
     */
    private function createAIBehaviorQuestions(): void
    {
        $questions = [
            [
                'id' => 601,
                'step' => 6,
                'question_key' => 'response_style',
                'question_text' => 'Yanıtlar nasıl olmalı?',
                'help_text' => 'AI asistanınızın yanıt verme şeklini seçin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Uzun ve kapsamlı',
                    'Detaylı açıklamalar',
                    'Örneklerle destekli',
                    'Harekete geçirici'
                ], JSON_UNESCAPED_UNICODE),
                'is_required' => true,
                'sort_order' => 10
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ AI davranış kuralları soruları eklendi (1 soru)\n";
    }
}