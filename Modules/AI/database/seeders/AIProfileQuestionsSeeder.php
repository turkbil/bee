<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
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
        $this->createFounderQuestions();
        
        // ADIM 5: Başarı Hikayeleri (Tüm sektörler için ortak)
        $this->createSuccessStoryQuestions();
        
        // ADIM 6: Yapay Zeka Davranış Kuralları (Tüm sektörler için ortak)
        $this->createAIBehaviorQuestions();
        
        // ADIM 3'e sektöre özel sorular da ekleyelim
        $this->createSectorSpecificQuestions();
    }
    
    private function createBasicInfoQuestions(): void
    {
        // ADIM 2: Temel Bilgiler - Olmazsa olmaz
        $questions = [
            [
                'step' => 2,
                'question_key' => 'brand_name',
                'question_text' => 'Marka/Firma Adı',
                'help_text' => 'Markanızın ya da firmanızın adını yazın',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 2,
                'question_key' => 'city',
                'question_text' => 'Şehir',
                'help_text' => 'Hangi şehirde faaliyet gösteriyorsunuz?',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'step' => 2,
                'question_key' => 'main_service',
                'question_text' => 'Ana Hizmet/Ürün',
                'help_text' => 'Ana olarak ne satıyorsunuz veya hangi hizmeti veriyorsunuz?',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'step' => 2,
                'question_key' => 'contact_info',
                'question_text' => 'İletişim Tercihi',
                'help_text' => 'Müşteriler size nasıl ulaşsın?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'phone', 'label' => 'Telefon', 'icon' => 'fas fa-phone'],
                    ['value' => 'whatsapp', 'label' => 'WhatsApp', 'icon' => 'fab fa-whatsapp'],
                    ['value' => 'email', 'label' => 'E-mail', 'icon' => 'fas fa-envelope'],
                    ['value' => 'website', 'label' => 'Website', 'icon' => 'fas fa-globe'],
                    ['value' => 'instagram', 'label' => 'Instagram', 'icon' => 'fab fa-instagram'],
                    ['value' => 'facebook', 'label' => 'Facebook', 'icon' => 'fab fa-facebook'],
                    ['value' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin']
                ],
                'is_required' => true,
                'sort_order' => 4
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createBrandDetailsQuestions(): void
    {
        // ADIM 3: Marka Detayları - Şubeleşme, büyüklük, vb
        $questions = [
            [
                'step' => 3,
                'question_key' => 'brand_personality',
                'question_text' => 'Marka Kişiliği',
                'help_text' => 'Markanızın kişilik özelliklerini seçin (birden fazla seçebilirsiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'modern', 'label' => 'Modern ve Yenilikçi', 'icon' => 'fas fa-rocket'],
                    ['value' => 'trustworthy', 'label' => 'Güvenilir ve Kurumsal', 'icon' => 'fas fa-university'],
                    ['value' => 'friendly', 'label' => 'Samimi ve Yakın', 'icon' => 'fas fa-smile'],
                    ['value' => 'professional', 'label' => 'Profesyonel ve Ciddi', 'icon' => 'fas fa-briefcase'],
                    ['value' => 'creative', 'label' => 'Yaratıcı ve Özgün', 'icon' => 'fas fa-palette'],
                    ['value' => 'luxury', 'label' => 'Premium ve Kaliteli', 'icon' => 'fas fa-gem'],
                    ['value' => 'energetic', 'label' => 'Dinamik ve Enerjik', 'icon' => 'fas fa-bolt'],
                    ['value' => 'conservative', 'label' => 'Klasik ve Muhafazakar', 'icon' => 'fas fa-landmark']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 3,
                'question_key' => 'brand_age',
                'question_text' => 'Marka Yaşı',
                'help_text' => 'Markanız ne kadar süredir faaliyet gösteriyor?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'new', 'label' => 'Yeni (0-2 yıl) - Fresh ve güncel', 'icon' => 'fas fa-seedling'],
                    ['value' => 'growing', 'label' => 'Gelişen (3-7 yıl) - Deneyim kazanan', 'icon' => 'fas fa-leaf'],
                    ['value' => 'established', 'label' => 'Yerleşik (8-15 yıl) - Deneyimli', 'icon' => 'fas fa-tree'],
                    ['value' => 'mature', 'label' => 'Köklü (15+ yıl) - Sektör lideri', 'icon' => 'fas fa-university']
                ],
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'step' => 3,
                'question_key' => 'company_size',
                'question_text' => 'Şirket Büyüklüğü',
                'help_text' => 'Çalışan sayınız ve firma ölçeğiniz',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'solo', 'label' => 'Bireysel/Serbest meslek', 'icon' => 'fas fa-user'],
                    ['value' => 'micro', 'label' => '2-5 kişi (Mikro işletme)', 'icon' => 'fas fa-users'],
                    ['value' => 'small', 'label' => '6-25 kişi (Küçük işletme)', 'icon' => 'fas fa-user-friends'],
                    ['value' => 'medium', 'label' => '26-100 kişi (Orta ölçekli)', 'icon' => 'fas fa-building'],
                    ['value' => 'large', 'label' => '100+ kişi (Büyük şirket)', 'icon' => 'fas fa-city']
                ],
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'step' => 3,
                'question_key' => 'branches',
                'question_text' => 'Şube Durumu',
                'help_text' => 'Kaç farklı lokasyonda hizmet veriyorsunuz?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'single', 'label' => 'Tek lokasyon', 'icon' => 'fas fa-map-marker-alt'],
                    ['value' => 'multi-city', 'label' => 'Birden fazla şehir', 'icon' => 'fal fa-city'],
                    ['value' => 'multi-branch', 'label' => 'Aynı şehirde birden fazla şube', 'icon' => 'fas fa-store'],
                    ['value' => 'online-only', 'label' => 'Sadece online', 'icon' => 'fas fa-laptop'],
                    ['value' => 'hybrid', 'label' => 'Fiziksel + Online', 'icon' => 'fas fa-sync-alt']
                ],
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'step' => 3,
                'question_key' => 'target_audience',
                'question_text' => 'Hedef Kitle',
                'help_text' => 'Kimler sizin müşterileriniz? (birden fazla seçebilirsiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'b2b-small', 'label' => 'Küçük işletmeler', 'icon' => 'fas fa-store'],
                    ['value' => 'b2b-medium', 'label' => 'Orta ölçekli şirketler', 'icon' => 'fas fa-building'],
                    ['value' => 'b2b-large', 'label' => 'Büyük kurumlar', 'icon' => 'fas fa-industry'],
                    ['value' => 'b2c-young', 'label' => 'Genç bireyler (18-35)', 'icon' => 'fas fa-user-graduate'],
                    ['value' => 'b2c-family', 'label' => 'Aileler (35-55)', 'icon' => 'fas fa-home'],
                    ['value' => 'b2c-senior', 'label' => 'Deneyimli yaş grubu (55+)', 'icon' => 'far fa-user-clock'],
                    ['value' => 'government', 'label' => 'Kamu kurumları', 'icon' => 'fas fa-university'],
                    ['value' => 'ngo', 'label' => 'STK/Dernekler', 'icon' => 'fas fa-handshake']
                ],
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'step' => 3,
                'question_key' => 'market_position',
                'question_text' => 'Pazar Konumu',
                'help_text' => 'Pazarda nasıl konumlanmak istiyorsunuz?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'budget', 'label' => 'Ekonomik/Uygun fiyat', 'icon' => 'fas fa-dollar-sign'],
                    ['value' => 'value', 'label' => 'Fiyat-performans', 'icon' => 'fas fa-balance-scale'],
                    ['value' => 'premium', 'label' => 'Premium/Kaliteli', 'icon' => 'fas fa-star'],
                    ['value' => 'luxury', 'label' => 'Lüks/Özel', 'icon' => 'fas fa-gem'],
                    ['value' => 'innovative', 'label' => 'Yenilikçi/Teknoloji odaklı', 'icon' => 'fas fa-rocket'],
                    ['value' => 'specialist', 'label' => 'Uzman/Niş alan', 'icon' => 'fas fa-bullseye']
                ],
                'is_required' => true,
                'sort_order' => 6
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createFounderPermissionQuestion(): void
    {
        // ADIM 4: Kurucu İzin Sorusu
        $question = [
            'step' => 4,
            'question_key' => 'founder_permission',
            'question_text' => 'Kurucu/Sahibi Bilgilerini Sisteme Eklemek İster misiniz?',
            'help_text' => 'Yapay Zeka kurucu/sahibi bilgilerini içeriklerde kullanabilir mi? Bu bilgiler markanızı kişiselleştirir ve daha samimi hale getirir.',
            'input_type' => 'radio',
            'options' => [
                ['value' => 'yes_full', 'label' => 'Evet, detaylı kurucu bilgileri ekle', 'icon' => 'fas fa-check-circle'],
                ['value' => 'yes_limited', 'label' => 'Evet, sadece genel bilgiler', 'icon' => 'fas fa-check'],
                ['value' => 'no', 'label' => 'Hayır, kurucu bilgilerini ekleme', 'icon' => 'fas fa-times-circle']
            ],
            'is_required' => true,
            'sort_order' => 1
        ];
        
        AIProfileQuestion::create($question);
    }
    
    private function createSectorSelectionQuestion(): void
    {
        AIProfileQuestion::create([
            'step' => 1,
            'question_key' => 'sector',
            'question_text' => 'Sektörünüz',
            'help_text' => 'Faaliyet gösterdiğiniz ana sektörü seçin',
            'input_type' => 'select',
            'options' => [], // Dinamik olarak AIProfileSector'den gelecek
            'is_required' => true,
            'sort_order' => 1
        ]);
    }
    
    private function createSectorSpecificQuestions(): void
    {
        // E-Ticaret Soruları
        $this->createEcommerceQuestions();
        
        // Sağlık Soruları
        $this->createHealthQuestions();
        
        // Eğitim Soruları
        $this->createEducationQuestions();
        
        // Restoran Soruları
        $this->createRestaurantQuestions();
        
        // Teknoloji Soruları
        $this->createTechnologyQuestions();
        
        // Diğer sektörler için de benzer şekilde eklenebilir...
    }
    
    private function createEcommerceQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'e-commerce',
                'step' => 3,
                'question_key' => 'product_categories',
                'question_text' => 'Ürün Kategorileriniz',
                'help_text' => 'Sattığınız ana ürün kategorilerini seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'giyim', 'label' => 'Giyim & Moda', 'icon' => 'fas fa-tshirt'],
                    ['value' => 'elektronik', 'label' => 'Elektronik', 'icon' => 'fas fa-laptop'],
                    ['value' => 'ev-yasam', 'label' => 'Ev & Yaşam', 'icon' => 'fas fa-home'],
                    ['value' => 'kozmetik', 'label' => 'Kozmetik & Kişisel Bakım', 'icon' => 'fas fa-spray-can'],
                    ['value' => 'gida', 'label' => 'Gıda & İçecek', 'icon' => 'fas fa-utensils'],
                    ['value' => 'kitap-hobi', 'label' => 'Kitap & Hobi', 'icon' => 'fas fa-book'],
                    ['value' => 'spor', 'label' => 'Spor & Outdoor', 'icon' => 'fas fa-dumbbell'],
                    ['value' => 'bebek-cocuk', 'label' => 'Anne & Bebek & Çocuk', 'icon' => 'fas fa-baby'],
                    ['value' => 'diger', 'label' => 'Diğer', 'icon' => 'fas fa-ellipsis-h']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'e-commerce',
                'step' => 3,
                'question_key' => 'price_range',
                'question_text' => 'Ortalama Ürün Fiyat Aralığı',
                'help_text' => 'Ürünlerinizin ortalama fiyat aralığı',
                'input_type' => 'select',
                'options' => [
                    ['value' => '0-100', 'label' => '0-100 TL', 'icon' => 'fal fa-coins'],
                    ['value' => '100-500', 'label' => '100-500 TL', 'icon' => 'fas fa-coins'],
                    ['value' => '500-1000', 'label' => '500-1000 TL', 'icon' => 'fas fa-money-bill'],
                    ['value' => '1000-5000', 'label' => '1000-5000 TL', 'icon' => 'fas fa-money-bill-wave'],
                    ['value' => '5000+', 'label' => '5000 TL üzeri', 'icon' => 'fas fa-gem']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'sector_code' => 'e-commerce',
                'step' => 3,
                'question_key' => 'delivery_time',
                'question_text' => 'Ortalama Teslimat Süresi',
                'help_text' => 'Müşterilerinize ortalama teslimat süreniz',
                'input_type' => 'select',
                'options' => [
                    ['value' => 'same-day', 'label' => 'Aynı Gün', 'icon' => 'fas fa-bolt'],
                    ['value' => '1-2-days', 'label' => '1-2 Gün', 'icon' => 'fas fa-shipping-fast'],
                    ['value' => '3-5-days', 'label' => '3-5 Gün', 'icon' => 'fas fa-truck'],
                    ['value' => '5-7-days', 'label' => '5-7 Gün', 'icon' => 'fal fa-truck'],
                    ['value' => '7+days', 'label' => '7+ Gün', 'icon' => 'far fa-clock']
                ],
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'sector_code' => 'e-commerce',
                'step' => 3,
                'question_key' => 'payment_methods',
                'question_text' => 'Kabul Ettiğiniz Ödeme Yöntemleri',
                'help_text' => 'Müşterilerinizin kullanabileceği ödeme yöntemleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'kredi-karti', 'label' => 'Kredi Kartı', 'icon' => 'fas fa-credit-card'],
                    ['value' => 'havale-eft', 'label' => 'Havale/EFT', 'icon' => 'fas fa-university'],
                    ['value' => 'kapida-odeme', 'label' => 'Kapıda Ödeme', 'icon' => 'fas fa-hand-holding-usd'],
                    ['value' => 'mobil-odeme', 'label' => 'Mobil Ödeme', 'icon' => 'fas fa-mobile-alt'],
                    ['value' => 'taksit', 'label' => 'Taksitli Ödeme', 'icon' => 'fas fa-calculator']
                ],
                'is_required' => false,
                'sort_order' => 4
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createHealthQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'health',
                'step' => 3,
                'question_key' => 'health_branches',
                'question_text' => 'Hizmet Verdiğiniz Branşlar',
                'help_text' => 'Sağlık hizmeti verdiğiniz branşları seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'genel-cerrahi', 'label' => 'Genel Cerrahi', 'icon' => 'fas fa-cut'],
                    ['value' => 'kardiyoloji', 'label' => 'Kardiyoloji', 'icon' => 'fas fa-heartbeat'],
                    ['value' => 'ortopedi', 'label' => 'Ortopedi', 'icon' => 'fas fa-bone'],
                    ['value' => 'goz', 'label' => 'Göz Hastalıkları', 'icon' => 'fas fa-eye'],
                    ['value' => 'dis', 'label' => 'Diş Hekimliği', 'icon' => 'fas fa-tooth'],
                    ['value' => 'estetik', 'label' => 'Estetik Cerrahi', 'icon' => 'fas fa-magic'],
                    ['value' => 'dermatoloji', 'label' => 'Dermatoloji', 'icon' => 'fas fa-hand-paper'],
                    ['value' => 'pediatri', 'label' => 'Çocuk Hastalıkları', 'icon' => 'fas fa-baby'],
                    ['value' => 'kadin-dogum', 'label' => 'Kadın Doğum', 'icon' => 'fas fa-female'],
                    ['value' => 'diger', 'label' => 'Diğer', 'icon' => 'fas fa-stethoscope']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'health',
                'step' => 3,
                'question_key' => 'doctor_count',
                'question_text' => 'Doktor Sayısı',
                'help_text' => 'Kurumunuzda çalışan doktor sayısı',
                'input_type' => 'select',
                'options' => [
                    ['value' => '1-5', 'label' => '1-5 Doktor', 'icon' => 'fas fa-user-md'],
                    ['value' => '6-10', 'label' => '6-10 Doktor', 'icon' => 'fas fa-users'],
                    ['value' => '11-25', 'label' => '11-25 Doktor', 'icon' => 'fas fa-user-friends'],
                    ['value' => '26-50', 'label' => '26-50 Doktor', 'icon' => 'fas fa-hospital'],
                    ['value' => '50+', 'label' => '50+ Doktor', 'icon' => 'fas fa-hospital-alt']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'sector_code' => 'health',
                'step' => 3,
                'question_key' => 'health_services',
                'question_text' => 'Özel Hizmetler',
                'help_text' => 'Sunduğunuz özel hizmetleri işaretleyin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => '7-24-acil', 'label' => '7/24 Acil Servis', 'icon' => 'fas fa-ambulance'],
                    ['value' => 'ameliyathane', 'label' => 'Ameliyathane', 'icon' => 'fas fa-procedures'],
                    ['value' => 'yogun-bakim', 'label' => 'Yoğun Bakım', 'icon' => 'fas fa-bed'],
                    ['value' => 'goruntuleme', 'label' => 'Görüntüleme Merkezi', 'icon' => 'fas fa-x-ray'],
                    ['value' => 'laboratuvar', 'label' => 'Laboratuvar', 'icon' => 'fas fa-vial'],
                    ['value' => 'fizik-tedavi', 'label' => 'Fizik Tedavi', 'icon' => 'fas fa-walking'],
                    ['value' => 'check-up', 'label' => 'Check-up Merkezi', 'icon' => 'fas fa-notes-medical']
                ],
                'is_required' => false,
                'sort_order' => 3
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createEducationQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'education',
                'step' => 3,
                'question_key' => 'education_level',
                'question_text' => 'Eğitim Seviyesi',
                'help_text' => 'Hizmet verdiğiniz eğitim seviyelerini seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'anaokulu', 'label' => 'Anaokulu', 'icon' => 'fas fa-baby'],
                    ['value' => 'ilkokul', 'label' => 'İlkokul', 'icon' => 'fas fa-child'],
                    ['value' => 'ortaokul', 'label' => 'Ortaokul', 'icon' => 'fas fa-user-graduate'],
                    ['value' => 'lise', 'label' => 'Lise', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'universite', 'label' => 'Üniversite', 'icon' => 'fas fa-university'],
                    ['value' => 'yuksek-lisans', 'label' => 'Yüksek Lisans', 'icon' => 'fas fa-user-tie'],
                    ['value' => 'kurs-sertifika', 'label' => 'Kurs/Sertifika', 'icon' => 'fas fa-certificate'],
                    ['value' => 'ozel-ders', 'label' => 'Özel Ders', 'icon' => 'fas fa-chalkboard-teacher']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'education',
                'step' => 3,
                'question_key' => 'education_subjects',
                'question_text' => 'Eğitim Konuları',
                'help_text' => 'Eğitim verdiğiniz ana konuları seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'yabanci-dil', 'label' => 'Yabancı Dil', 'icon' => 'fas fa-language'],
                    ['value' => 'bilgisayar', 'label' => 'Bilgisayar/Yazılım', 'icon' => 'fas fa-code'],
                    ['value' => 'matematik-fen', 'label' => 'Matematik/Fen', 'icon' => 'fas fa-calculator'],
                    ['value' => 'sanat', 'label' => 'Sanat/Müzik', 'icon' => 'fas fa-palette'],
                    ['value' => 'spor', 'label' => 'Spor', 'icon' => 'fas fa-dumbbell'],
                    ['value' => 'kisisel-gelisim', 'label' => 'Kişisel Gelişim', 'icon' => 'fas fa-brain'],
                    ['value' => 'meslek-edindirme', 'label' => 'Meslek Edindirme', 'icon' => 'fas fa-tools'],
                    ['value' => 'akademik', 'label' => 'Akademik Dersler', 'icon' => 'fas fa-book-open']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'sector_code' => 'education',
                'step' => 3,
                'question_key' => 'education_method',
                'question_text' => 'Eğitim Yöntemi',
                'help_text' => 'Kullandığınız eğitim yöntemleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'yuz-yuze', 'label' => 'Yüz Yüze Eğitim', 'icon' => 'fas fa-users'],
                    ['value' => 'online-canli', 'label' => 'Online Canlı Ders', 'icon' => 'fas fa-video'],
                    ['value' => 'video-egitim', 'label' => 'Video Eğitim', 'icon' => 'fas fa-play-circle'],
                    ['value' => 'hibrit', 'label' => 'Hibrit (Yüz yüze + Online)', 'icon' => 'fas fa-sync-alt']
                ],
                'is_required' => false,
                'sort_order' => 3
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createRestaurantQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'restaurant',
                'step' => 3,
                'question_key' => 'cuisine_type',
                'question_text' => 'Mutfak Türü',
                'help_text' => 'Restoranınızın mutfak türlerini seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'turk-mutfagi', 'label' => 'Türk Mutfağı'],
                    ['value' => 'italyan', 'label' => 'İtalyan'],
                    ['value' => 'cin', 'label' => 'Çin/Uzakdoğu'],
                    ['value' => 'fast-food', 'label' => 'Fast Food'],
                    ['value' => 'deniz-urunleri', 'label' => 'Deniz Ürünleri'],
                    ['value' => 'vejetaryen', 'label' => 'Vejetaryen/Vegan'],
                    ['value' => 'dunya-mutfagi', 'label' => 'Dünya Mutfağı'],
                    ['value' => 'tatli-pasta', 'label' => 'Tatlı/Pasta'],
                    ['value' => 'kahvalti', 'label' => 'Kahvaltı']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'restaurant',
                'step' => 3,
                'question_key' => 'service_types',
                'question_text' => 'Hizmet Türleri',
                'help_text' => 'Sunduğunuz hizmet türlerini seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'restoran', 'label' => 'Restoran Servisi'],
                    ['value' => 'paket-servis', 'label' => 'Paket Servis'],
                    ['value' => 'gel-al', 'label' => 'Gel Al'],
                    ['value' => 'catering', 'label' => 'Catering'],
                    ['value' => 'online-siparis', 'label' => 'Online Sipariş'],
                    ['value' => 'rezervasyon', 'label' => 'Rezervasyon']
                ],
                'is_required' => false,
                'sort_order' => 2
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createTechnologyQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'technology',
                'step' => 3,
                'question_key' => 'tech_services',
                'question_text' => 'Teknoloji Hizmetleriniz',
                'help_text' => 'Sunduğunuz teknoloji hizmetlerini seçin',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'yazilim-gelistirme', 'label' => 'Yazılım Geliştirme'],
                    ['value' => 'web-tasarim', 'label' => 'Web Tasarım'],
                    ['value' => 'mobil-uygulama', 'label' => 'Mobil Uygulama'],
                    ['value' => 'e-ticaret', 'label' => 'E-Ticaret Çözümleri'],
                    ['value' => 'bulut-hizmetleri', 'label' => 'Bulut Hizmetleri'],
                    ['value' => 'siber-guvenlik', 'label' => 'Siber Güvenlik'],
                    ['value' => 'veri-analizi', 'label' => 'Veri Analizi'],
                    ['value' => 'yapay-zeka', 'label' => 'Yapay Zeka/ML'],
                    ['value' => 'donanim', 'label' => 'Donanım Çözümleri'],
                    ['value' => 'it-danismanlik', 'label' => 'IT Danışmanlık']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'technology',
                'step' => 3,
                'question_key' => 'project_types',
                'question_text' => 'Proje Türleriniz',
                'help_text' => 'Hangi tür projeler geliştiriyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'web-apps', 'label' => 'Web Uygulamaları'],
                    ['value' => 'mobile-apps', 'label' => 'Mobil Uygulamalar'],
                    ['value' => 'desktop-software', 'label' => 'Masaüstü Yazılım'],
                    ['value' => 'ecommerce', 'label' => 'E-Ticaret Siteleri'],
                    ['value' => 'crm-erp', 'label' => 'CRM/ERP Sistemleri'],
                    ['value' => 'api-integration', 'label' => 'API Entegrasyonları'],
                    ['value' => 'data-analytics', 'label' => 'Veri Analiz Sistemleri'],
                    ['value' => 'ai-ml', 'label' => 'Yapay Zeka/ML Projeleri']
                ],
                'is_required' => false,
                'sort_order' => 2
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createSuccessStoryQuestions(): void
    {
        $questions = [
            [
                'step' => 6,
                'question_key' => 'company_age_advantage',
                'question_text' => 'Deneyim Avantajınız',
                'help_text' => 'Firmanızın deneyimini nasıl vurgulayalım?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'innovation', 'label' => 'Yenilikçi ve güncel yaklaşımlar'],
                    ['value' => 'experience', 'label' => 'Yıllarca biriken deneyim'],
                    ['value' => 'stability', 'label' => 'Güvenilir ve istikrarlı hizmet'],
                    ['value' => 'flexibility', 'label' => 'Esnek ve çeviklik']
                ],
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'step' => 6,
                'question_key' => 'client_types',
                'question_text' => 'Müşteri Türleriniz',
                'help_text' => 'Hangi tür müşterilerle çalışıyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'kucuk-isletmeler', 'label' => 'Küçük İşletmeler'],
                    ['value' => 'orta-olcekli', 'label' => 'Orta Ölçekli Şirketler'],
                    ['value' => 'buyuk-kurumlar', 'label' => 'Büyük Kurumlar'],
                    ['value' => 'kamu', 'label' => 'Kamu Kurumları'],
                    ['value' => 'bireysel', 'label' => 'Bireysel Müşteriler'],
                    ['value' => 'uluslararasi', 'label' => 'Uluslararası Firmalar']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'step' => 6,
                'question_key' => 'success_indicators',
                'question_text' => 'Başarı Göstergeleriniz',
                'help_text' => 'Hangi başarılarınızı öne çıkaralım?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'customer-satisfaction', 'label' => 'Yüksek müşteri memnuniyeti'],
                    ['value' => 'project-count', 'label' => 'Çok sayıda başarılı proje'],
                    ['value' => 'return-customers', 'label' => 'Müşteri sadakati ve tekrar alım'],
                    ['value' => 'industry-recognition', 'label' => 'Sektörel tanınırlık'],
                    ['value' => 'innovation-awards', 'label' => 'Yenilik ödülleri'],
                    ['value' => 'fast-growth', 'label' => 'Hızlı büyüme'],
                    ['value' => 'quality-certificates', 'label' => 'Kalite sertifikaları']
                ],
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'step' => 6,
                'question_key' => 'competitive_advantage',
                'question_text' => 'Rekabet Avantajınız',
                'help_text' => 'Sizi rakiplerden ayıran özellikler neler?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'price', 'label' => 'Uygun fiyat'],
                    ['value' => 'quality', 'label' => 'Üstün kalite'],
                    ['value' => 'speed', 'label' => 'Hızlı teslimat/hizmet'],
                    ['value' => 'customization', 'label' => 'Kişiselleştirme'],
                    ['value' => 'customer-service', 'label' => 'Mükemmel müşteri hizmeti'],
                    ['value' => 'technology', 'label' => 'Gelişmiş teknoloji'],
                    ['value' => 'expertise', 'label' => 'Uzmanlık ve bilgi birikimi'],
                    ['value' => 'innovation', 'label' => 'Sürekli yenilik']
                ],
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'step' => 6,
                'question_key' => 'work_approach',
                'question_text' => 'Çalışma Yaklaşımınız',
                'help_text' => 'Müşterilerle nasıl çalıştığınızı tanımlar',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'collaborative', 'label' => 'İşbirlikçi yaklaşım'],
                    ['value' => 'consultative', 'label' => 'Danışmanlık odaklı'],
                    ['value' => 'agile', 'label' => 'Çevik proje yönetimi'],
                    ['value' => 'transparent', 'label' => 'Şeffaf süreç yönetimi'],
                    ['value' => 'result-oriented', 'label' => 'Sonuç odaklı'],
                    ['value' => 'customer-centric', 'label' => 'Müşteri merkezli']
                ],
                'is_required' => false,
                'sort_order' => 5
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createAIBehaviorQuestions(): void
    {
        $questions = [
            [
                'step' => 6,
                'question_key' => 'writing_tone',
                'question_text' => 'Yazı Tonu',
                'help_text' => 'Yapay Zeka hangi tonda içerik üretsin? (birden fazla seçebilirsiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'professional', 'label' => 'Profesyonel', 'icon' => 'fas fa-briefcase'],
                    ['value' => 'friendly', 'label' => 'Samimi', 'icon' => 'fas fa-smile'],
                    ['value' => 'formal', 'label' => 'Resmi', 'icon' => 'fas fa-university'],
                    ['value' => 'casual', 'label' => 'Günlük', 'icon' => 'far fa-smile'],
                    ['value' => 'enthusiastic', 'label' => 'Coşkulu', 'icon' => 'fas fa-fire'],
                    ['value' => 'informative', 'label' => 'Bilgilendirici', 'icon' => 'fas fa-info-circle']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 6,
                'question_key' => 'emphasis_points',
                'question_text' => 'Vurgu Noktaları',
                'help_text' => 'İçeriklerde öne çıkarılmasını istediğiniz özellikler',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'quality', 'label' => 'Kalite', 'icon' => 'fas fa-star'],
                    ['value' => 'price', 'label' => 'Uygun Fiyat', 'icon' => 'fas fa-dollar-sign'],
                    ['value' => 'speed', 'label' => 'Hız', 'icon' => 'fas fa-bolt'],
                    ['value' => 'experience', 'label' => 'Deneyim', 'icon' => 'fas fa-medal'],
                    ['value' => 'innovation', 'label' => 'Yenilikçilik', 'icon' => 'fas fa-lightbulb'],
                    ['value' => 'trust', 'label' => 'Güvenilirlik', 'icon' => 'fas fa-shield-alt'],
                    ['value' => 'customer-focus', 'label' => 'Müşteri Odaklılık', 'icon' => 'fas fa-heart'],
                    ['value' => 'technology', 'label' => 'Teknoloji', 'icon' => 'fas fa-microchip']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'step' => 6,
                'question_key' => 'avoid_topics',
                'question_text' => 'Kaçınılacak Konular',
                'help_text' => 'Yapay Zeka\'nın içeriklerde bahsetmemesi gereken konular',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'politics', 'label' => 'Siyaset', 'icon' => 'fas fa-ban'],
                    ['value' => 'religion', 'label' => 'Din', 'icon' => 'fas fa-times-circle'],
                    ['value' => 'competitors', 'label' => 'Rakipler', 'icon' => 'fas fa-user-slash'],
                    ['value' => 'negative-news', 'label' => 'Olumsuz Haberler', 'icon' => 'fas fa-exclamation-triangle'],
                    ['value' => 'controversy', 'label' => 'Tartışmalı Konular', 'icon' => 'fas fa-minus-circle']
                ],
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'step' => 6,
                'question_key' => 'communication_style',
                'question_text' => 'İletişim Tarzı',
                'help_text' => 'Müşterilerle nasıl iletişim kurmak istiyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'personal', 'label' => 'Kişisel ve samimi'],
                    ['value' => 'professional', 'label' => 'Profesyonel ve ciddi'],
                    ['value' => 'helpful', 'label' => 'Yardımsever ve çözüm odaklı'],
                    ['value' => 'educational', 'label' => 'Öğretici ve bilgilendirici'],
                    ['value' => 'inspiring', 'label' => 'İlham verici ve motive edici']
                ],
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'step' => 6,
                'question_key' => 'content_approach',
                'question_text' => 'İçerik Yaklaşımı',
                'help_text' => 'Yapay Zeka hangi tarzda içerik üretsin?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'story-telling', 'label' => 'Hikaye anlatıcı'],
                    ['value' => 'data-driven', 'label' => 'Veri odaklı'],
                    ['value' => 'emotional', 'label' => 'Duygusal bağ kuran'],
                    ['value' => 'logical', 'label' => 'Mantıklı ve analitik'],
                    ['value' => 'creative', 'label' => 'Yaratıcı ve özgün'],
                    ['value' => 'simple', 'label' => 'Sade ve anlaşılır']
                ],
                'is_required' => false,
                'sort_order' => 5
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createFounderQuestions(): void
    {
        $questions = [
            [
                'step' => 4,
                'step' => 4,
                'section' => 'founder_info',
                'depends_on' => 'founder_permission',
                'question_key' => 'founder_name',
                'question_text' => 'Kurucu Adı Soyadı',
                'help_text' => 'Kurucunun tam adı',
                'input_type' => 'text',
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'step' => 4,
                'section' => 'founder_info',
                'depends_on' => 'founder_permission',
                'question_key' => 'founder_title',
                'question_text' => 'Kurucu Unvanı',
                'help_text' => 'Kurucunun firma içindeki pozisyonu',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'ceo', 'label' => 'CEO (Genel Müdür)', 'icon' => 'fas fa-crown'],
                    ['value' => 'founder-partner', 'label' => 'Kurucu Ortak', 'icon' => 'fas fa-handshake'],
                    ['value' => 'chairman', 'label' => 'Yönetim Kurulu Başkanı', 'icon' => 'fas fa-chair'],
                    ['value' => 'managing-director', 'label' => 'Yönetici Müdürü', 'icon' => 'fas fa-user-tie'],
                    ['value' => 'owner', 'label' => 'İşletme Sahibi', 'icon' => 'fas fa-key'],
                    ['value' => 'president', 'label' => 'Başkan', 'icon' => 'fas fa-medal']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'step' => 4,
                'section' => 'founder_info',
                'depends_on' => 'founder_permission',
                'question_key' => 'founder_background',
                'question_text' => 'Kurucu Geçmişi',
                'help_text' => 'Kurucunun hangi alanda deneyimli?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'industry-expert', 'label' => 'Sektör uzmanı', 'icon' => 'fas fa-user-cog'],
                    ['value' => 'entrepreneur', 'label' => 'Seri girişimci', 'icon' => 'fas fa-lightbulb'],
                    ['value' => 'academic', 'label' => 'Akademik geçmiş', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'corporate', 'label' => 'Kurumsal deneyim', 'icon' => 'fas fa-building'],
                    ['value' => 'technical', 'label' => 'Teknik uzmanlık', 'icon' => 'fas fa-cogs'],
                    ['value' => 'international', 'label' => 'Uluslararası deneyim', 'icon' => 'fas fa-globe-americas']
                ],
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'step' => 4,
                'section' => 'founder_info',
                'depends_on' => 'founder_permission',
                'question_key' => 'founder_qualities',
                'question_text' => 'Kurucu Özellikleri',
                'help_text' => 'Kurucunun öne çıkan kişilik özellikleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'visionary', 'label' => 'Vizyoner', 'icon' => 'far fa-eye'],
                    ['value' => 'innovative', 'label' => 'Yenilikçi', 'icon' => 'fas fa-rocket'],
                    ['value' => 'experienced', 'label' => 'Deneyimli', 'icon' => 'fas fa-user-clock'],
                    ['value' => 'customer-focused', 'label' => 'Müşteri odaklı', 'icon' => 'fas fa-heart'],
                    ['value' => 'detail-oriented', 'label' => 'Detay odaklı', 'icon' => 'fas fa-search'],
                    ['value' => 'team-leader', 'label' => 'Takım lideri', 'icon' => 'fas fa-users-cog'],
                    ['value' => 'strategic', 'label' => 'Stratejik düşünen', 'icon' => 'fas fa-chess']
                ],
                'is_required' => false,
                'sort_order' => 4
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
}