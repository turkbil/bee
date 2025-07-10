<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileQuestionsCompleteSeeder extends Seeder
{
    /**
     * AI PROFİL SORULARI - KAPSAMLI VE KATEGORİ BAZLI
     * 
     * Her ana kategori için özel sorular + ortak sorular
     * Sıralı step sistemi ile organize edilmiş
     * ID'ler tutarlı ve manuel atanmış
     */
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Questions - KATEGORİ BAZLI Yükleniyor...\n";
        
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
        
        // ADIM 7: Sektöre Özel Sorular (Her ana kategori için)
        $this->createSectorSpecificQuestions();
        
        echo "\n🎯 Kategori bazlı sorular tamamlandı!\n";
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
            'description' => 'Lütfen ana sektörünüzü seçin. Bu seçim sonraki soruları belirleyecektir.',
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
                'description' => 'Resmi firma adınızı yazın',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 102,
                'step' => 2,
                'question_key' => 'city',
                'question_text' => 'Hangi şehirdesiniz?',
                'description' => 'Ana faaliyet şehrinizi belirtin',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 103,
                'step' => 2,
                'question_key' => 'main_service',
                'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'description' => 'Temel olarak ne yapıyorsunuz? (örn: Web tasarımı, Diş tedavisi, Online satış)',
                'input_type' => 'textarea',
                'is_required' => true,
                'sort_order' => 30
            ],
            [
                'id' => 104,
                'step' => 2,
                'question_key' => 'experience_years',
                'question_text' => 'Kaç yıldır bu işi yapıyorsunuz?',
                'description' => 'Sektördeki deneyim sürenizi belirtin',
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
                'description' => 'Çalışan sayısına göre işletme büyüklüğünüzü belirtin',
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
                'description' => 'Öncelikli hedef müşterilerinizi seçin (çoklu seçim)',
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
                'description' => 'Hangi coğrafi alanda hizmet veriyorsunuz?',
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
                'description' => 'AI asistanınızın nasıl konuşmasını istiyorsunuz?',
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
            'description' => 'Kişisel hikayeniz marka güvenilirliğini artırır. Paylaşmak tamamen isteğe bağlıdır.',
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
                'description' => 'Bu işe nasıl başladığınızı, motivasyonunuzu kısaca anlatın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 20,
                'conditional_parent' => 'founder_permission',
                'conditional_value' => 'Evet, hikayemi paylaşmak istiyorum'
            ],
            [
                'id' => 303,
                'step' => 4,
                'question_key' => 'biggest_challenge',
                'question_text' => 'En büyük zorluğunuz neydi ve nasıl aştınız?',
                'description' => 'İşinizde karşılaştığınız önemli bir zorluğu ve çözümünüzü paylaşın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 30,
                'conditional_parent' => 'founder_permission',
                'conditional_value' => 'Evet, hikayemi paylaşmak istiyorum'
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
                'description' => 'Bir müşteri ile yaşadığınız pozitif deneyimi paylaşın',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 10
            ],
            [
                'id' => 402,
                'step' => 5,
                'question_key' => 'customer_testimonial',
                'question_text' => 'Bir müşteri görüşü (varsa)',
                'description' => 'Size yapılan olumlu bir yorum veya referans',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 20
            ],
            [
                'id' => 403,
                'step' => 5,
                'question_key' => 'competitive_advantage',
                'question_text' => 'Rakiplerinizden farkınız nedir?',
                'description' => 'Sizi özel kılan, müşterilerin sizi tercih etme sebebi',
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
                'description' => 'AI asistanınızın iletişim tarzını belirleyin',
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
                'description' => 'AI asistanınızın yanıt verme şeklini seçin',
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
                'description' => 'AI asistanınızın değinmemesini istediğiniz konular',
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
    
    /**
     * ADIM 7: Sektöre Özel Sorular - Her ana kategori için
     */
    private function createSectorSpecificQuestions(): void
    {
        // Teknoloji & Bilişim (sector_id = 1)
        $this->createTechnologyQuestions();
        
        // Sağlık & Tıp (sector_id = 2)
        $this->createHealthQuestions();
        
        // Eğitim & Öğretim (sector_id = 3)
        $this->createEducationQuestions();
        
        // Yiyecek & İçecek (sector_id = 4)
        $this->createFoodQuestions();
        
        // E-ticaret & Perakende (sector_id = 5)
        $this->createCommerceQuestions();
        
        // İnşaat & Emlak (sector_id = 6)
        $this->createConstructionQuestions();
        
        // Finans & Muhasebe (sector_id = 7)
        $this->createFinanceQuestions();
        
        // Sanayi & Üretim (sector_id = 8)
        $this->createIndustryQuestions();
        
        echo "✅ Sektöre özel sorular tamamlandı (8 sektör)\n";
    }
    
    /**
     * Teknoloji & Bilişim Soruları (sector_id = 1)
     */
    private function createTechnologyQuestions(): void
    {
        $questions = [
            [
                'id' => 1001,
                'step' => 7,
                'sector_id' => 1,
                'question_key' => 'tech_specialization',
                'question_text' => 'Hangi teknolojilerde uzmanlaştınız?',
                'description' => 'Kullandığınız programlama dilleri, framework\'ler, teknolojiler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'PHP/Laravel',
                    'JavaScript/React/Vue',
                    'Python/Django',
                    'Java/Spring',
                    '.NET/C#',
                    'Mobile (iOS/Android)',
                    'WordPress/CMS',
                    'E-ticaret platformları',
                    'Cloud (AWS/Azure/Google)',
                    'AI/Machine Learning'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 1002,
                'step' => 7,
                'sector_id' => 1,
                'question_key' => 'project_duration',
                'question_text' => 'Tipik proje süreniz ne kadar?',
                'description' => 'Genellikle projeleriniz ne kadar sürede tamamlanır?',
                'input_type' => 'select',
                'options' => json_encode([
                    '1-2 hafta (hızlı işler)',
                    '1-3 ay (orta projeler)',
                    '3-6 ay (büyük projeler)',
                    '6+ ay (kurumsal projeler)'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 1003,
                'step' => 7,
                'sector_id' => 1,
                'question_key' => 'support_model',
                'question_text' => 'Proje sonrası destek modeliniz?',
                'description' => 'Projeyi teslim ettikten sonra nasıl destek veriyorsunuz?',
                'input_type' => 'radio',
                'options' => json_encode([
                    '1 yıl ücretsiz destek',
                    '3-6 ay ücretsiz, sonrası ücretli',
                    'Sadece garanti süresi',
                    'Ayrı destek anlaşması'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * Sağlık & Tıp Soruları (sector_id = 2)
     */
    private function createHealthQuestions(): void
    {
        $questions = [
            [
                'id' => 2001,
                'step' => 7,
                'sector_id' => 2,
                'question_key' => 'medical_specialties',
                'question_text' => 'Hangi tıbbi alanlarda hizmet veriyorsunuz?',
                'description' => 'Uzmanlık alanlarınızı belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Genel Pratisyen',
                    'Diş Hekimliği',
                    'Göz Hastalıkları',
                    'Cildiye',
                    'Ortopedi',
                    'Kardiyoloji',
                    'Nöroloji',
                    'Kadın Doğum',
                    'Çocuk Hastalıkları',
                    'Estetik Cerrahi'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 2002,
                'step' => 7,
                'sector_id' => 2,
                'question_key' => 'appointment_system',
                'question_text' => 'Randevu sisteminiz nasıl çalışır?',
                'description' => 'Hastalar nasıl randevu alabilir?',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Online randevu sistemi',
                    'Telefon ile randevu',
                    'WhatsApp randevu',
                    'Gelip randevu alma',
                    'Acil durum kabul',
                    'Ev ziyareti'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 2003,
                'step' => 7,
                'sector_id' => 2,
                'question_key' => 'insurance_acceptance',
                'question_text' => 'Hangi sigortalarla çalışıyorsunuz?',
                'description' => 'Kabul ettiğiniz sağlık sigortaları',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'SGK',
                    'Özel sağlık sigortaları',
                    'Kurumsal anlaşmalar',
                    'Sadece özel ödeme',
                    'Taksitli ödeme',
                    'Kredi kartı'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * Eğitim & Öğretim Soruları (sector_id = 3)
     */
    private function createEducationQuestions(): void
    {
        $questions = [
            [
                'id' => 3001,
                'step' => 7,
                'sector_id' => 3,
                'question_key' => 'education_levels',
                'question_text' => 'Hangi seviyelerde eğitim veriyorsunuz?',
                'description' => 'Hedef yaş grupları ve seviyeler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Okul öncesi (3-6 yaş)',
                    'İlkokul (7-10 yaş)',
                    'Ortaokul (11-14 yaş)',
                    'Lise (15-18 yaş)',
                    'Üniversite öğrencileri',
                    'Yetişkinler',
                    'Kurumsal eğitimler'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 3002,
                'step' => 7,
                'sector_id' => 3,
                'question_key' => 'education_format',
                'question_text' => 'Eğitim formatınız nasıl?',
                'description' => 'Ders verme şeklinizi belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Birebir özel ders',
                    'Küçük grup (2-5 kişi)',
                    'Sınıf ortamı (10+ kişi)',
                    'Online canlı ders',
                    'Kayıtlı video dersler',
                    'Hibrit (online + yüz yüze)'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 3003,
                'step' => 7,
                'sector_id' => 3,
                'question_key' => 'success_tracking',
                'question_text' => 'Başarıyı nasıl ölçüyorsunuz?',
                'description' => 'Öğrenci ilerlemesini takip şekliniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Düzenli sınavlar',
                    'Proje tabanlı değerlendirme',
                    'Ödev takibi',
                    'Veli görüşmeleri',
                    'Ders içi katılım',
                    'Sertifika programları'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * Yiyecek & İçecek Soruları (sector_id = 4)
     */
    private function createFoodQuestions(): void
    {
        $questions = [
            [
                'id' => 4001,
                'step' => 7,
                'sector_id' => 4,
                'question_key' => 'cuisine_type',
                'question_text' => 'Hangi mutfak türlerinde uzmanlaştınız?',
                'description' => 'Sunduğunuz yemek kategorileri',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Türk mutfağı',
                    'İtalyan mutfağı',
                    'Uzak Doğu mutfağı',
                    'Fast food',
                    'Sağlıklı/organik yemekler',
                    'Vegan/vejetaryen',
                    'Deniz ürünleri',
                    'Et ürünleri',
                    'Tatlı/pasta',
                    'Kahve uzmanı'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 4002,
                'step' => 7,
                'sector_id' => 4,
                'question_key' => 'service_style',
                'question_text' => 'Hizmet modeliniz nasıl?',
                'description' => 'Müşterilere nasıl hizmet veriyorsunuz?',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Masa servisi',
                    'Self servis',
                    'Paket servis',
                    'Online sipariş',
                    'Ev teslimatı',
                    'Catering hizmeti',
                    'Açık büfe',
                    'Drive-thru'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 4003,
                'step' => 7,
                'sector_id' => 4,
                'question_key' => 'special_features',
                'question_text' => 'Özel özellikleriniz neler?',
                'description' => 'Sizi farklı kılan hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Canlı müzik',
                    'Özel etkinlik organizasyonu',
                    'Çocuk oyun alanı',
                    'Vale park',
                    'Wi-Fi',
                    'Terras/bahçe',
                    '24 saat açık',
                    'Özel diyet menüleri'
                ]),
                'is_required' => false,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * E-ticaret & Perakende Soruları (sector_id = 5)
     */
    private function createCommerceQuestions(): void
    {
        $questions = [
            [
                'id' => 5001,
                'step' => 7,
                'sector_id' => 5,
                'question_key' => 'product_categories',
                'question_text' => 'Hangi ürün kategorilerinde satış yapıyorsunuz?',
                'description' => 'Ana ürün gruplarınızı belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Moda ve giyim',
                    'Elektronik',
                    'Ev ve yaşam',
                    'Kitap ve kırtasiye',
                    'Spor ve outdoor',
                    'Kozmetik ve kişisel bakım',
                    'Bebek ve çocuk',
                    'Otomotiv',
                    'Sağlık ürünleri',
                    'Hediyelik eşya'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 5002,
                'step' => 7,
                'sector_id' => 5,
                'question_key' => 'sales_channels',
                'question_text' => 'Hangi kanallardan satış yapıyorsunuz?',
                'description' => 'Satış platformlarınızı belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Kendi web sitesi',
                    'Fiziksel mağaza',
                    'Trendyol',
                    'Hepsiburada',
                    'N11',
                    'Amazon',
                    'Instagram/Facebook',
                    'WhatsApp Business',
                    'Pazar yeri/stand'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 5003,
                'step' => 7,
                'sector_id' => 5,
                'question_key' => 'shipping_payment',
                'question_text' => 'Kargo ve ödeme seçenekleriniz?',
                'description' => 'Sunduğunuz hizmetleri belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Ücretsiz kargo (belirli tutar üzeri)',
                    'Aynı gün teslimat',
                    'Kapıda ödeme',
                    'Kredi kartı taksit',
                    'Havale/EFT',
                    'Kapıda kredi kartı',
                    'İade garantisi',
                    'Değişim hakkı'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * İnşaat & Emlak Soruları (sector_id = 6)
     */
    private function createConstructionQuestions(): void
    {
        $questions = [
            [
                'id' => 6001,
                'step' => 7,
                'sector_id' => 6,
                'question_key' => 'construction_types',
                'question_text' => 'Hangi tür projelerde çalışıyorsunuz?',
                'description' => 'Uzmanlaştığınız inşaat türleri',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Konut projeleri',
                    'Villa inşaatı',
                    'Ticari binalar',
                    'Endüstriyel yapılar',
                    'Tadilat/renovasyon',
                    'İç mimari',
                    'Peyzaj çalışmaları',
                    'Altyapı projeleri'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 6002,
                'step' => 7,
                'sector_id' => 6,
                'question_key' => 'project_scale',
                'question_text' => 'Hangi büyüklükte projeler yapıyorsunuz?',
                'description' => 'Aldığınız projelerin ölçeği',
                'input_type' => 'select',
                'options' => json_encode([
                    'Küçük işler (100K altı)',
                    'Orta projeler (100K-1M)',
                    'Büyük projeler (1M-10M)',
                    'Mega projeler (10M üzeri)'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 6003,
                'step' => 7,
                'sector_id' => 6,
                'question_key' => 'services_included',
                'question_text' => 'Hangi hizmetleri dahil ediyorsunuz?',
                'description' => 'Proje kapsamında sunduğunuz hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Proje tasarımı',
                    'İnşaat ruhsatı',
                    'Malzeme tedariki',
                    'İşçilik',
                    'Proje yönetimi',
                    'Kalite kontrol',
                    'Sigorta',
                    'Garanti hizmeti'
                ]),
                'is_required' => true,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * Finans & Muhasebe Soruları (sector_id = 7)
     */
    private function createFinanceQuestions(): void
    {
        $questions = [
            [
                'id' => 7001,
                'step' => 7,
                'sector_id' => 7,
                'question_key' => 'finance_services',
                'question_text' => 'Hangi finansal hizmetleri sunuyorsunuz?',
                'description' => 'Uzmanlık alanlarınızı belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Muhasebe ve defter tutma',
                    'Vergi danışmanlığı',
                    'SGK işlemleri',
                    'Bordro hazırlığı',
                    'Mali müşavirlik',
                    'Yatırım danışmanlığı',
                    'Sigorta hizmetleri',
                    'Kredilendirme'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 7002,
                'step' => 7,
                'sector_id' => 7,
                'question_key' => 'client_segments',
                'question_text' => 'Hangi müşteri segmentlerine hizmet veriyorsunuz?',
                'description' => 'Hedef müşteri gruplarınız',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Bireysel müşteriler',
                    'Küçük işletmeler',
                    'Orta ölçekli şirketler',
                    'Büyük korporasyonlar',
                    'Serbest meslek sahipleri',
                    'Emlak yatırımcıları',
                    'E-ticaret işletmeleri',
                    'Kamu kurumları'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 7003,
                'step' => 7,
                'sector_id' => 7,
                'question_key' => 'digital_tools',
                'question_text' => 'Hangi dijital araçları kullanıyorsunuz?',
                'description' => 'Müşterilerinize sunduğunuz teknolojik hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Online muhasebe programları',
                    'Dijital imza',
                    'E-Beyanname',
                    'E-Fatura/E-Arşiv',
                    'Online danışmanlık',
                    'Mobil uygulama',
                    'Cloud tabanlı hizmetler',
                    'Yapay zeka destekli analiz'
                ]),
                'is_required' => false,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
    
    /**
     * Sanayi & Üretim Soruları (sector_id = 8)
     */
    private function createIndustryQuestions(): void
    {
        $questions = [
            [
                'id' => 8001,
                'step' => 7,
                'sector_id' => 8,
                'question_key' => 'production_type',
                'question_text' => 'Hangi tür üretim yapıyorsunuz?',
                'description' => 'Ana üretim alanlarınızı belirtin',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'Makina imalatı',
                    'Kimyasal üretim',
                    'Gıda işleme',
                    'Tekstil üretimi',
                    'Metal işleme',
                    'Otomotiv parça',
                    'Elektrik/elektronik',
                    'Plastik üretimi'
                ]),
                'is_required' => true,
                'sort_order' => 10
            ],
            [
                'id' => 8002,
                'step' => 7,
                'sector_id' => 8,
                'question_key' => 'production_capacity',
                'question_text' => 'Üretim kapasiteniz nedir?',
                'description' => 'Aylık veya yıllık üretim miktarınız',
                'input_type' => 'select',
                'options' => json_encode([
                    'Küçük ölçek (manuel/yarı otomatik)',
                    'Orta ölçek (hibrit üretim)',
                    'Büyük ölçek (tam otomatik)',
                    'Seri üretim (endüstriyel)'
                ]),
                'is_required' => true,
                'sort_order' => 20
            ],
            [
                'id' => 8003,
                'step' => 7,
                'sector_id' => 8,
                'question_key' => 'quality_certifications',
                'question_text' => 'Hangi kalite sertifikalarınız var?',
                'description' => 'Sahip olduğunuz kalite ve standart belgeler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'ISO 9001',
                    'ISO 14001',
                    'TSE belgesi',
                    'CE markası',
                    'OHSAS 18001',
                    'HACCP',
                    'Helal sertifikası',
                    'Organik sertifika'
                ]),
                'is_required' => false,
                'sort_order' => 30
            ]
        ];
        
        foreach ($questions as $question) {
            $question['is_active'] = true;
            AIProfileQuestion::create($question);
        }
    }
}