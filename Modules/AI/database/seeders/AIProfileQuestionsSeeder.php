<?php

namespace Modules\AI\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;

class AIProfileQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        // Önce mevcut soruları temizle
        AIProfileQuestion::truncate();
        
        $this->createSectorSelectionQuestions();
        $this->createBasicInfoQuestions();
        $this->createBrandQuestions();
        $this->createFounderQuestions();
        $this->createAIBehaviorQuestions();
    }
    
    private function createSectorSelectionQuestions(): void
    {
        // ADIM 1: Sektör Seçimi
        $questions = [
            [
                'step' => 1,
                'question_key' => 'sector_selection',
                'question_text' => 'Sektörünüzü seçin',
                'help_text' => 'İşletmenizin faaliyet gösterdiği ana sektörü seçerek size özel AI profili oluşturalım',
                'input_type' => 'select',
                'is_required' => true,
                'sort_order' => 1,
                'options' => [
                    'instruction' => 'Aşağıdaki sektörlerden birini seçin. Seçiminize göre size özel sorular yüklenecektir.',
                    'placeholder' => 'Sektörünüzü seçin...',
                    'source' => 'ai_profile_sectors',
                    'display_field' => 'name',
                    'value_field' => 'id'
                ]
            ]
        ];
        
        $this->insertQuestions($questions);
    }
    
    private function createBasicInfoQuestions(): void
    {
        // ADIM 2: Temel Bilgiler
        $questions = [
            [
                'step' => 2,
                'question_key' => 'brand_name',
                'question_text' => 'Marka/Firma Adı',
                'help_text' => 'Resmi firma adınızı yazın',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 2,
                'question_key' => 'city',
                'question_text' => 'Hangi şehirdesiniz?',
                'help_text' => 'Ana faaliyet şehrinizi belirtin',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'step' => 2,
                'question_key' => 'business_start_year',
                'question_text' => 'Hangi yıldan beri bu işi yapıyorsunuz?',
                'help_text' => 'İşe başladığınız yılı yazın veya deneyim sürenizi belirtin (Örn: 2020, 2015 yılından beri, 10+ yıllık deneyim, aile işi vb.)',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 3
            ]
        ];
        
        $this->insertQuestions($questions);
    }
    
    private function createBrandQuestions(): void
    {
        // ADIM 3: Marka Detayları
        $questions = [
            [
                'step' => 3,
                'question_key' => 'main_business_activities',
                'question_text' => 'Yaptığınız ana iş kolları nelerdir?',
                'help_text' => 'İşletmenizin sunduğu hizmetleri veya ürünleri detaylı olarak açıklayın',
                'input_type' => 'textarea',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 3,
                'question_key' => 'target_customers',
                'question_text' => 'Ana müşteri kitleniz kimler?',
                'help_text' => 'Öncelikli hedef müşterilerinizi seçin (çoklu seçim)',
                'input_type' => 'checkbox',
                'is_required' => true,
                'sort_order' => 2,
                'options' => [
                    'bireysel_musteriler' => ['label' => 'Bireysel müşteriler', 'value' => 'bireysel_musteriler'],
                    'kucuk_isletmeler' => ['label' => 'Küçük işletmeler', 'value' => 'kucuk_isletmeler'], 
                    'buyuk_sirketler' => ['label' => 'Büyük şirketler', 'value' => 'buyuk_sirketler'],
                    'diger' => ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true, 'custom_placeholder' => 'Özel müşteri kitlenizi belirtiniz...']
                ]
            ],
        ];
        
        $this->insertQuestions($questions);
    }
    
    private function createFounderQuestions(): void
    {
        // ADIM 4: Kurucu Bilgileri
        $questions = [
            [
                'step' => 4,
                'question_key' => 'share_founder_info',
                'question_text' => 'Kurucu hakkında bilgi paylaşmak ister misiniz?',
                'help_text' => 'Kişisel hikayeniz marka güvenilirliğini artırır. Paylaşmak tamamen isteğe bağlıdır.',
                'input_type' => 'radio',
                'is_required' => true,
                'sort_order' => 91,
                'options' => [
                    'hayir' => ['label' => 'Hayır, sadece işletme bilgileri yeterli', 'value' => 'hayir'],
                    'evet' => ['label' => 'Evet, bilgilerimi paylaşmak istiyorum', 'value' => 'evet']
                ]
            ],
            [
                'step' => 4,
                'question_key' => 'founder_name',
                'question_text' => 'Kurucunun adı soyadı',
                'help_text' => 'Müşterilerle paylaşılacak kurucu isim bilgisi',
                'input_type' => 'text',
                'is_required' => false,
                'sort_order' => 92
            ],
            [
                'step' => 4,
                'question_key' => 'founder_position',
                'question_text' => 'Pozisyonunuz',
                'help_text' => 'Şirketteki pozisyonunuzu seçin',
                'input_type' => 'radio',
                'is_required' => false,
                'sort_order' => 93,
                'options' => [
                    'kurucu_sahip' => ['label' => 'Kurucu & Sahip', 'value' => 'kurucu_sahip'],
                    'genel_mudur' => ['label' => 'Genel Müdür', 'value' => 'genel_mudur'],
                    'diger' => ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true, 'custom_placeholder' => 'Pozisyonunuzu belirtiniz...']
                ]
            ],
            [
                'step' => 4,
                'question_key' => 'founder_qualities',
                'question_text' => 'Kurucuyu tanıtan özellikler',
                'help_text' => 'Kurucuyu en iyi hangi özellikler tanımlıyor? (çoklu seçim)',
                'input_type' => 'checkbox',
                'is_required' => false,
                'sort_order' => 94,
                'options' => [
                    'liderlik' => ['label' => 'Liderlik', 'value' => 'liderlik'],
                    'vizyonerlik' => ['label' => 'Vizyonerlik', 'value' => 'vizyonerlik'],
                    'yaraticilik' => ['label' => 'Yaratıcılık', 'value' => 'yaraticilik'],
                    'guvenilirlik' => ['label' => 'Güvenilirlik', 'value' => 'guvenilirlik'],
                    'kararlilik' => ['label' => 'Kararlılık', 'value' => 'kararlilik'],
                    'diger' => ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true, 'custom_placeholder' => 'Özelliklerinizi belirtiniz...']
                ]
            ],
            [
                'step' => 4,
                'question_key' => 'founder_story',
                'question_text' => 'Kurucu hakkında ne paylaşmak istersiniz?',
                'help_text' => 'Kurucunun hikayesi, deneyimleri, eğitimi, başarıları, vizyonu... İstediğiniz her şeyi yazabilirsiniz. Ne kadar detaylandırırsanız AI o kadar kişisel yanıtlar verebilir.',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 95
            ]
        ];
        
        $this->insertQuestions($questions);
    }
    
    private function createAIBehaviorQuestions(): void
    {
        // ADIM 5: Yapay Zeka Davranış ve İletişim Ayarları
        $questions = [
            [
                'step' => 5,
                'question_key' => 'brand_character',
                'question_text' => 'Marka karakteriniz nasıl?',
                'help_text' => 'Markanızın benzersiz kişilik özelliklerini seçin',
                'input_type' => 'checkbox',
                'is_required' => true,
                'sort_order' => 1,
                'options' => [
                    'samimi_dostane' => ['label' => 'Samimi ve dostane', 'value' => 'samimi_dostane'],
                    'ciddi_kurumsal' => ['label' => 'Ciddi ve kurumsal', 'value' => 'ciddi_kurumsal'],
                    'enerjik_heyecanli' => ['label' => 'Enerjik ve heyecanlı', 'value' => 'enerjik_heyecanli'],
                    'sakin_temkinli' => ['label' => 'Sakin ve temkinli', 'value' => 'sakin_temkinli'],
                    'yenilikci_cesur' => ['label' => 'Yenilikçi ve cesur', 'value' => 'yenilikci_cesur'],
                    'geleneksel_koklu' => ['label' => 'Geleneksel ve köklü', 'value' => 'geleneksel_koklu'],
                    'eglenceli_yaratici' => ['label' => 'Eğlenceli ve yaratıcı', 'value' => 'eglenceli_yaratici'],
                    'pratik_cozum_odakli' => ['label' => 'Pratik ve çözüm odaklı', 'value' => 'pratik_cozum_odakli'],
                    'diger' => ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true, 'custom_placeholder' => 'Özel marka karakterinizi belirtiniz...']
                ]
            ],
            [
                'step' => 5,
                'question_key' => 'writing_style',
                'question_text' => 'Genel yazım tavırınız nasıl olsun?',
                'help_text' => 'Web sitesi, blog, sosyal medya - her yerde kullanılacak genel dil tavrı',
                'input_type' => 'checkbox',
                'is_required' => true,
                'sort_order' => 2,
                'options' => [
                    'kisa_net' => ['label' => 'Kısa ve net ifadeler', 'value' => 'kisa_net'],
                    'detayli_kapsamli' => ['label' => 'Detaylı ve kapsamlı anlatım', 'value' => 'detayli_kapsamli'],
                    'teknik_bilimsel' => ['label' => 'Teknik ve bilimsel yaklaşım', 'value' => 'teknik_bilimsel'],
                    'sade_anlasilir' => ['label' => 'Sade ve anlaşılır dil', 'value' => 'sade_anlasilir'],
                    'duygusal_etkileyici' => ['label' => 'Duygusal ve etkileyici', 'value' => 'duygusal_etkileyici'],
                    'gunluk_konusma' => ['label' => 'Günlük konuşma tarzında', 'value' => 'gunluk_konusma'],
                    'formal_profesyonel' => ['label' => 'Formal ve profesyonel', 'value' => 'formal_profesyonel'],
                    'diger' => ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true, 'custom_placeholder' => 'Özel yazım tarzınızı belirtiniz...']
                ]
            ]
        ];
        
        $this->insertQuestions($questions);
    }
    
    private function insertQuestions(array $questions): void
    {
        foreach ($questions as $question) {
            // Options alanı array ise JSON'a çevir
            if (isset($question['options']) && is_array($question['options'])) {
                $question['options'] = json_encode($question['options'], JSON_UNESCAPED_UNICODE);
            }
            
            AIProfileQuestion::create($question);
        }
    }
}