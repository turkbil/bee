<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileQuestionsSectorSeeder extends Seeder
{
    /**
     * AI PROFİL SORULARI - SEKTÖRE ÖZEL SORULAR (PART 2)
     * 
     * Her ana kategori için özel sorular
     * Step 7: Sektöre özel detay sorular (8 ana sektör için)
     */
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Questions - SEKTÖRE ÖZEL SORULAR (Part 2/2) Yükleniyor...\n";
        
        // ADIM 7: Sektöre Özel Sorular (Her ana kategori için)
        $this->createSectorSpecificQuestions();
        
        echo "\n🎯 Sektöre özel sorular tamamlandı! (Part 2/2)\n";
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
                'sector_code' => 'technology',
                'question_key' => 'tech_specialization',
                'question_text' => 'Hangi teknolojilerde uzmanlaştınız?',
                'help_text' => 'Kullandığınız programlama dilleri, framework\'ler, teknolojiler',
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
                'sector_code' => 'technology',
                'question_key' => 'project_duration',
                'question_text' => 'Tipik proje süreniz ne kadar?',
                'help_text' => 'Genellikle projeleriniz ne kadar sürede tamamlanır?',
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
                'sector_code' => 'technology',
                'question_key' => 'support_model',
                'question_text' => 'Proje sonrası destek modeliniz?',
                'help_text' => 'Projeyi teslim ettikten sonra nasıl destek veriyorsunuz?',
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
        
        echo "   ✅ Teknoloji & Bilişim soruları (3 soru)\n";
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
                'sector_code' => 'health',
                'question_key' => 'medical_specialties',
                'question_text' => 'Hangi tıbbi alanlarda hizmet veriyorsunuz?',
                'help_text' => 'Uzmanlık alanlarınızı belirtin',
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
                'sector_code' => 'health',
                'question_key' => 'appointment_system',
                'question_text' => 'Randevu sisteminiz nasıl çalışır?',
                'help_text' => 'Hastalar nasıl randevu alabilir?',
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
                'sector_code' => 'health',
                'question_key' => 'insurance_acceptance',
                'question_text' => 'Hangi sigortalarla çalışıyorsunuz?',
                'help_text' => 'Kabul ettiğiniz sağlık sigortaları',
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
        
        echo "   ✅ Sağlık & Tıp soruları (3 soru)\n";
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
                'sector_code' => 'education',
                'question_key' => 'education_levels',
                'question_text' => 'Hangi seviyelerde eğitim veriyorsunuz?',
                'help_text' => 'Hedef yaş grupları ve seviyeler',
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
                'sector_code' => 'education',
                'question_key' => 'education_format',
                'question_text' => 'Eğitim formatınız nasıl?',
                'help_text' => 'Ders verme şeklinizi belirtin',
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
                'sector_code' => 'education',
                'question_key' => 'success_tracking',
                'question_text' => 'Başarıyı nasıl ölçüyorsunuz?',
                'help_text' => 'Öğrenci ilerlemesini takip şekliniz',
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
        
        echo "   ✅ Eğitim & Öğretim soruları (3 soru)\n";
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
                'sector_code' => 'food',
                'question_key' => 'cuisine_type',
                'question_text' => 'Hangi mutfak türlerinde uzmanlaştınız?',
                'help_text' => 'Sunduğunuz yemek kategorileri',
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
                'sector_code' => 'food',
                'question_key' => 'service_style',
                'question_text' => 'Hizmet modeliniz nasıl?',
                'help_text' => 'Müşterilere nasıl hizmet veriyorsunuz?',
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
                'sector_code' => 'food',
                'question_key' => 'special_features',
                'question_text' => 'Özel özellikleriniz neler?',
                'help_text' => 'Sizi farklı kılan hizmetler',
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
        
        echo "   ✅ Yiyecek & İçecek soruları (3 soru)\n";
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
                'sector_code' => 'commerce',
                'question_key' => 'product_categories',
                'question_text' => 'Hangi ürün kategorilerinde satış yapıyorsunuz?',
                'help_text' => 'Ana ürün gruplarınızı belirtin',
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
                'sector_code' => 'commerce',
                'question_key' => 'sales_channels',
                'question_text' => 'Hangi kanallardan satış yapıyorsunuz?',
                'help_text' => 'Satış platformlarınızı belirtin',
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
                'sector_code' => 'commerce',
                'question_key' => 'shipping_payment',
                'question_text' => 'Kargo ve ödeme seçenekleriniz?',
                'help_text' => 'Sunduğunuz hizmetleri belirtin',
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
        
        echo "   ✅ E-ticaret & Perakende soruları (3 soru)\n";
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
                'sector_code' => 'construction',
                'question_key' => 'construction_types',
                'question_text' => 'Hangi tür projelerde çalışıyorsunuz?',
                'help_text' => 'Uzmanlaştığınız inşaat türleri',
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
                'sector_code' => 'construction',
                'question_key' => 'project_scale',
                'question_text' => 'Hangi büyüklükte projeler yapıyorsunuz?',
                'help_text' => 'Aldığınız projelerin ölçeği',
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
                'sector_code' => 'construction',
                'question_key' => 'services_included',
                'question_text' => 'Hangi hizmetleri dahil ediyorsunuz?',
                'help_text' => 'Proje kapsamında sunduğunuz hizmetler',
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
        
        echo "   ✅ İnşaat & Emlak soruları (3 soru)\n";
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
                'sector_code' => 'finance',
                'question_key' => 'finance_services',
                'question_text' => 'Hangi finansal hizmetleri sunuyorsunuz?',
                'help_text' => 'Uzmanlık alanlarınızı belirtin',
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
                'sector_code' => 'finance',
                'question_key' => 'client_segments',
                'question_text' => 'Hangi müşteri segmentlerine hizmet veriyorsunuz?',
                'help_text' => 'Hedef müşteri gruplarınız',
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
                'sector_code' => 'finance',
                'question_key' => 'digital_tools',
                'question_text' => 'Hangi dijital araçları kullanıyorsunuz?',
                'help_text' => 'Müşterilerinize sunduğunuz teknolojik hizmetler',
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
        
        echo "   ✅ Finans & Muhasebe soruları (3 soru)\n";
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
                'sector_code' => 'industry',
                'question_key' => 'production_type',
                'question_text' => 'Hangi tür üretim yapıyorsunuz?',
                'help_text' => 'Ana üretim alanlarınızı belirtin',
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
                'sector_code' => 'industry',
                'question_key' => 'production_capacity',
                'question_text' => 'Üretim kapasiteniz nedir?',
                'help_text' => 'Aylık veya yıllık üretim miktarınız',
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
                'sector_code' => 'industry',
                'question_key' => 'quality_certifications',
                'question_text' => 'Hangi kalite sertifikalarınız var?',
                'help_text' => 'Sahip olduğunuz kalite ve standart belgeler',
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
        
        echo "   ✅ Sanayi & Üretim soruları (3 soru)\n";
    }
}