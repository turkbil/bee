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
        
        // ADIM 5: Başarı Hikayeleri (Tüm sektörler için ortak)
        $this->createSuccessStoryQuestions();
        
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
                'question_key' => 'main_service',
                'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'help_text' => 'Temel olarak ne yapıyorsunuz? (örn: Web tasarımı, Diş tedavisi, Online satış)',
                'input_type' => 'textarea',
                'is_required' => true,
                'sort_order' => 30
            ],
            [
                'id' => 104,
                'step' => 2,
                'question_key' => 'experience_years',
                'question_text' => 'Kaç yıldır bu işi yapıyorsunuz?',
                'help_text' => 'Sektördeki deneyim sürenizi belirtin',
                'input_type' => 'select',
                'options' => json_encode([
                    '1-3 yıl',
                    '4-7 yıl', 
                    '8-15 yıl',
                    '15+ yıl'
                ]),
                'is_required' => true,
                'sort_order' => 40
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
                'question_key' => 'business_size',
                'question_text' => 'İşletme büyüklüğünüz?',
                'help_text' => 'Çalışan sayısına göre işletme büyüklüğünüzü belirtin',
                'input_type' => 'select',
                'options' => json_encode([
                    'Sadece ben (tek kişi)',
                    '2-5 kişi (küçük ekip)',
                    '6-20 kişi (orta işletme)',
                    '21-50 kişi (büyük işletme)',
                    '50+ kişi (kurumsal)'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 202,
                'step' => 3,
                'question_key' => 'target_audience',
                'question_text' => 'Ana müşteri kitleniz kimler?',
                'help_text' => 'Öncelikli hedef müşterilerinizi seçin (çoklu seçim)',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Bireysel müşteriler (B2C)',
                    'Küçük işletmeler',
                    'Orta ölçekli şirketler',
                    'Büyük korporasyonlar',
                    'Kamu kurumları',
                    'Yabancı müşteriler'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 203,
                'step' => 3,
                'question_key' => 'service_area',
                'question_text' => 'Hizmet alanınız?',
                'help_text' => 'Hangi coğrafi alanda hizmet veriyorsunuz?',
                'input_type' => 'select',
                'options' => json_encode([
                    'Sadece kendi şehrim',
                    'Birkaç şehir (bölgesel)',
                    'Türkiye geneli',
                    'Uluslararası',
                    'Online (lokasyon bağımsız)'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ],
            [
                'id' => 204,
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
                    'Hızlı ve dinamik'
                ]),
                'is_required' => true,
                'sort_order' => 40
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Marka detayları soruları eklendi (4 soru)\n";
    }
    
    /**
     * ADIM 4: Kurucu Bilgileri İzin Sorusu
     */
    private function createFounderPermissionQuestion(): void
    {
        AIProfileQuestion::create([
            'id' => 301,
            'step' => 4,
            'question_key' => 'founder_permission',
            'question_text' => 'Kurucu hikayenizi AI ile paylaşmak ister misiniz?',
            'help_text' => 'Kişisel hikayeniz marka güvenilirliğini artırır. Paylaşmak tamamen isteğe bağlıdır.',
            'input_type' => 'radio',
            'options' => json_encode([
                'Evet, hikayemi paylaşmak istiyorum',
                'Hayır, sadece işletme bilgileri yeterli'
            ]),
            'is_required' => true,
            'sort_order' => 10,
            'is_active' => true
        ]);
        
        // Eğer izin verilirse açılacak sorular (conditional)
        $founderQuestions = [
            [
                'id' => 302,
                'step' => 4,
                'question_key' => 'founder_story',
                'question_text' => 'Nasıl başladınız? Kuruluş hikayeniz',
                'help_text' => 'Bu işe nasıl başladığınızı, motivasyonunuzu kısaca anlatın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 20,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, hikayemi paylaşmak istiyorum'])
            ],
            [
                'id' => 303,
                'step' => 4,
                'question_key' => 'biggest_challenge',
                'question_text' => 'En büyük zorluğunuz neydi ve nasıl aştınız?',
                'help_text' => 'İşinizde karşılaştığınız önemli bir zorluğu ve çözümünüzü paylaşın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 30,
                'depends_on' => 'founder_permission',
                'show_if' => json_encode(['Evet, hikayemi paylaşmak istiyorum'])
            ]
        ];
        
        foreach ($founderQuestions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Kurucu bilgileri soruları eklendi (3 soru)\n";
    }
    
    /**
     * ADIM 5: Başarı Hikayeleri
     */
    private function createSuccessStoryQuestions(): void
    {
        $questions = [
            [
                'id' => 401,
                'step' => 5,
                'question_key' => 'success_story',
                'question_text' => 'En gurur duyduğunuz başarı hikayen',
                'help_text' => 'Bir müşteri ile yaşadığınız pozitif deneyimi paylaşın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 10
            ],
            [
                'id' => 402,
                'step' => 5,
                'question_key' => 'customer_testimonial',
                'question_text' => 'Bir müşteri görüşü (varsa)',
                'help_text' => 'Size yapılan olumlu bir yorum veya referans',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 20
            ],
            [
                'id' => 403,
                'step' => 5,
                'question_key' => 'competitive_advantage',
                'question_text' => 'Rakiplerinizden farkınız nedir?',
                'help_text' => 'Sizi özel kılan, müşterilerin sizi tercih etme sebebi',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'En uygun fiyat',
                    'Üstün kalite',
                    'Hız ve verimlilik',
                    'Kişiselleştirilmiş hizmet',
                    'Uzmanlık ve deneyim',
                    'Güvenilirlik',
                    '24/7 destek',
                    'Yaratıcı çözümler'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ Başarı hikayeleri soruları eklendi (3 soru)\n";
    }
    
    /**
     * ADIM 6: AI Davranış Kuralları
     */
    private function createAIBehaviorQuestions(): void
    {
        $questions = [
            [
                'id' => 501,
                'step' => 6,
                'question_key' => 'communication_style',
                'question_text' => 'Müşterilerle nasıl iletişim kurmalı?',
                'help_text' => 'AI asistanınızın iletişim tarzını belirleyin',
                'input_type' => 'radio',
                'options' => json_encode([
                    'Sen diye hitap et (samimi)',
                    'Siz diye hitap et (saygılı)',
                    'Profesyonel ama sıcak',
                    'Çok resmi ve ciddi'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 502,
                'step' => 6,
                'question_key' => 'response_style',
                'question_text' => 'Yanıtlar nasıl olmalı?',
                'help_text' => 'AI asistanınızın yanıt verme şeklini seçin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Kısa ve öz',
                    'Detaylı açıklamalar',
                    'Örneklerle destekli',
                    'Sorular sorarak anlayışlı',
                    'Harekete geçirici',
                    'Sabırlı ve anlayışlı'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 503,
                'step' => 6,
                'question_key' => 'forbidden_topics',
                'question_text' => 'Hangi konularda konuşmamalı?',
                'help_text' => 'AI asistanınızın değinmemesini istediğiniz konular',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Rakip firmaları övme',
                    'Fiyat indirimi teklif etme',
                    'Garanti veremeyeceği sözler',
                    'Kişisel bilgi talep etme',
                    'Politik konular',
                    'Sektör dışı tavsiyelerde bulunma'
                ]),
                'is_required' => false,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
        
        echo "✅ AI davranış kuralları soruları eklendi (3 soru)\n";
    }
}