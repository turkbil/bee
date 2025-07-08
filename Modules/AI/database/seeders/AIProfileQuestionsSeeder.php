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
                'help_text' => 'Markanızın ya da firmanızın adını yazın (özel belirtiniz)',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 2,
                'question_key' => 'city',
                'question_text' => 'Şehir',
                'help_text' => 'Hangi şehirde faaliyet gösteriyorsunuz? (özel belirtiniz)',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'step' => 2,
                'question_key' => 'main_service',
                'question_text' => 'Ana Hizmet/Ürün',
                'help_text' => 'Ana olarak ne satıyorsunuz veya hangi hizmeti veriyorsunuz? (özel belirtiniz)',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 3
            ],
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createBrandDetailsQuestions(): void
    {
        // ADIM 3: Marka Detayları - Önce somut veriler, sonra soyut olanlar
        $questions = [
            // SOMUT VERİLER ÖNCE (Tarih, çalışan sayısı, şube)
            [
                'step' => 3,
                'question_key' => 'brand_age',
                'question_text' => 'Kuruluş Tarihi / Marka Yaşı',
                'help_text' => 'Markanız ne kadar süredir faaliyet gösteriyor? (özel belirtiniz)',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'new', 'label' => 'Yeni (0-2 yıl) - Fresh ve güncel', 'icon' => 'fas fa-seedling'],
                    ['value' => 'growing', 'label' => 'Gelişen (3-7 yıl) - Deneyim kazanan', 'icon' => 'fas fa-leaf'],
                    ['value' => 'established', 'label' => 'Yerleşik (8-15 yıl) - Deneyimli', 'icon' => 'fas fa-tree'],
                    ['value' => 'mature', 'label' => 'Köklü (15+ yıl) - Sektör lideri', 'icon' => 'fas fa-university'],
                    ['value' => 'custom', 'label' => 'Diğer (özel belirtiniz)', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: 1987\'den beri, 25 yıllık deneyim']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'step' => 3,
                'question_key' => 'company_size',
                'question_text' => 'Çalışan Sayısı / Şirket Büyüklüğü',
                'help_text' => 'Çalışan sayınız ve firma ölçeğiniz (özel belirtiniz)',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'solo', 'label' => 'Bireysel/Serbest meslek', 'icon' => 'fas fa-user'],
                    ['value' => 'micro', 'label' => '2-5 kişi (Mikro işletme)', 'icon' => 'fas fa-users'],
                    ['value' => 'small', 'label' => '6-25 kişi (Küçük işletme)', 'icon' => 'fas fa-user-friends'],
                    ['value' => 'medium', 'label' => '26-100 kişi (Orta ölçekli)', 'icon' => 'fas fa-building'],
                    ['value' => 'large', 'label' => '100+ kişi (Büyük şirket)', 'icon' => 'fas fa-city'],
                    ['value' => 'custom', 'label' => 'Diğer (özel belirtiniz)', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: 500 kişi, 1200 çalışan']
                ],
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'step' => 3,
                'question_key' => 'branches',
                'question_text' => 'Şube Durumu / Lokasyon Sayısı',
                'help_text' => 'Kaç farklı lokasyonda hizmet veriyorsunuz? (özel belirtiniz)',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'single', 'label' => 'Tek lokasyon', 'icon' => 'fas fa-map-marker-alt'],
                    ['value' => 'multi-city', 'label' => 'Birden fazla şehir', 'icon' => 'fal fa-city'],
                    ['value' => 'multi-branch', 'label' => 'Aynı şehirde birden fazla şube', 'icon' => 'fas fa-store'],
                    ['value' => 'online-only', 'label' => 'Sadece online', 'icon' => 'fas fa-laptop'],
                    ['value' => 'hybrid', 'label' => 'Fiziksel + Online', 'icon' => 'fas fa-sync-alt'],
                    ['value' => 'custom', 'label' => 'Diğer (özel belirtiniz)', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: 12 ilde 45 şube, 8 şehirde faaliyet']
                ],
                'is_required' => true,
                'sort_order' => 3
            ],
            // SOYUT VERİLER SONRA (Düşünceler, konumlandırma, kişilik)
            [
                'step' => 3,
                'question_key' => 'target_audience',
                'question_text' => 'Hedef Kitle',
                'help_text' => 'Kimler sizin müşterileriniz? (birden fazla seçebilirsiniz) (özel belirtiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'b2b-small', 'label' => 'Küçük işletmeler', 'icon' => 'fas fa-store'],
                    ['value' => 'b2b-medium', 'label' => 'Orta ölçekli şirketler', 'icon' => 'fas fa-building'],
                    ['value' => 'b2b-large', 'label' => 'Büyük kurumlar', 'icon' => 'fas fa-industry'],
                    ['value' => 'b2c-young', 'label' => 'Genç bireyler (18-35)', 'icon' => 'fas fa-user-graduate'],
                    ['value' => 'b2c-family', 'label' => 'Aileler (35-55)', 'icon' => 'fas fa-home'],
                    ['value' => 'b2c-senior', 'label' => 'Deneyimli yaş grubu (55+)', 'icon' => 'far fa-user-clock'],
                    ['value' => 'government', 'label' => 'Kamu kurumları', 'icon' => 'fas fa-university'],
                    ['value' => 'ngo', 'label' => 'STK/Dernekler', 'icon' => 'fas fa-handshake'],
                    ['value' => 'custom', 'label' => 'Diğer müşteri grubu', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Freelancer\'lar, Start-up\'lar, Özel sektör']
                ],
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'step' => 3,
                'question_key' => 'market_position',
                'question_text' => 'Pazar Konumu',
                'help_text' => 'Pazarda nasıl konumlanmak istiyorsunuz? (özel belirtiniz)',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'budget', 'label' => 'Ekonomik/Uygun fiyat', 'icon' => 'fas fa-dollar-sign'],
                    ['value' => 'value', 'label' => 'Fiyat-performans', 'icon' => 'fas fa-balance-scale'],
                    ['value' => 'premium', 'label' => 'Premium/Kaliteli', 'icon' => 'fas fa-star'],
                    ['value' => 'luxury', 'label' => 'Lüks/Özel', 'icon' => 'fas fa-gem'],
                    ['value' => 'innovative', 'label' => 'Yenilikçi/Teknoloji odaklı', 'icon' => 'fas fa-rocket'],
                    ['value' => 'specialist', 'label' => 'Uzman/Niş alan', 'icon' => 'fas fa-bullseye'],
                    ['value' => 'custom', 'label' => 'Diğer (özel belirtiniz)', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Sosyal sorumluluk odaklı, Çevre dostu']
                ],
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'step' => 3,
                'question_key' => 'brand_personality',
                'question_text' => 'Marka Kişiliği',
                'help_text' => 'Markanızın kişilik özelliklerini seçin (birden fazla seçebilirsiniz) (özel belirtiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'modern', 'label' => 'Modern/Yenilikçi', 'icon' => 'fas fa-rocket'],
                    ['value' => 'trustworthy', 'label' => 'Güvenilir/Kurumsal', 'icon' => 'fas fa-university'],
                    ['value' => 'friendly', 'label' => 'Samimi/Yakın', 'icon' => 'fas fa-smile'],
                    ['value' => 'creative', 'label' => 'Yaratıcı/Özgün', 'icon' => 'fas fa-palette'],
                    ['value' => 'luxury', 'label' => 'Premium/Kaliteli', 'icon' => 'fas fa-gem'],
                    ['value' => 'conservative', 'label' => 'Klasik/Muhafazakar', 'icon' => 'fas fa-landmark']
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
            'section' => 'company_info',
            'question_key' => 'founder_permission',
            'question_text' => 'Kurucu/Sahibi Bilgilerini Sisteme Eklemek İster misiniz?',
            'help_text' => 'Yapay Zeka kurucu/sahibi bilgilerini içeriklerde kullanabilir mi? Bu bilgiler markanızı kişiselleştirir ve daha samimi hale getirir. (özel belirtiniz)',
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
            'help_text' => 'Faaliyet gösterdiğiniz ana sektörü seçin (özel belirtiniz)',
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
        
        // Yeni Sektör Soruları
        $this->createAdvertisingQuestions();
        $this->createArtsCraftsQuestions();
        $this->createAutomotiveQuestions();
        $this->createBeautyQuestions();
        $this->createBooksPublishingQuestions();
        $this->createConstructionQuestions();
        $this->createEventsQuestions();
        $this->createFinanceQuestions();
        $this->createRealEstateQuestions();
        $this->createHomeGardenQuestions();
        $this->createJewelryQuestions();
        $this->createMusicQuestions();
        $this->createNonprofitQuestions();
        $this->createPetsQuestions();
        $this->createPhotographyQuestions();
        $this->createSecurityQuestions();
        $this->createSportsQuestions();
        $this->createTextileQuestions();
        $this->createTourismQuestions();
        $this->createTransportationQuestions();
        $this->createWeddingQuestions();
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
                'step' => 5,
                'question_key' => 'company_age_advantage',
                'question_text' => 'Deneyim Avantajınız',
                'help_text' => 'Firmanızın deneyimini nasıl vurgulayalım?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'innovation', 'label' => 'Yenilikçi ve güncel yaklaşımlar'],
                    ['value' => 'experience', 'label' => 'Yıllarca biriken deneyim'],
                    ['value' => 'stability', 'label' => 'Güvenilir ve istikrarlı hizmet'],
                    ['value' => 'flexibility', 'label' => 'Esnek ve çeviklik'],
                    ['value' => 'custom', 'label' => 'Diğer avantaj', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Sosyal sorumluluk, Çevre bilinci, Şeffaflık']
                ],
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'step' => 5,
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
                    ['value' => 'uluslararasi', 'label' => 'Uluslararası Firmalar'],
                    ['value' => 'custom', 'label' => 'Diğer müşteri tipi', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Kooperatifler, Sendikalar, Sosyal işletmeler']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'step' => 5,
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
                    ['value' => 'quality-certificates', 'label' => 'Kalite sertifikaları'],
                    ['value' => 'custom', 'label' => 'Diğer başarı', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Sosyal etki, Çevre dostu projeler, Toplumsal katkı']
                ],
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'step' => 5,
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
                    ['value' => 'innovation', 'label' => 'Sürekli yenilik'],
                    ['value' => 'custom', 'label' => 'Diğer avantaj', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Yerel ağ, Kültürel yakınlık, Esnek ödeme']
                ],
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'step' => 5,
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
                    ['value' => 'customer-centric', 'label' => 'Müşteri merkezli'],
                    ['value' => 'custom', 'label' => 'Diğer yaklaşım', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Mentoring yaklaşımı, Eğitim odaklı, Sürdürülebilirlik']
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
        // ADIM 6: Yapay Zeka Davranış Kuralları - Priority sırası (önemli olanlar önce)
        $questions = [
            // EN ÖNEMLİ: Yazı tonu (Priority 1)
            [
                'step' => 6,
                'question_key' => 'writing_tone',
                'question_text' => 'Yazı Tonu',
                'help_text' => 'Yapay Zeka hangi tonda içerik üretsin? (birden fazla seçebilirsiniz) (özel belirtiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'professional', 'label' => 'Profesyonel', 'icon' => 'fas fa-briefcase'],
                    ['value' => 'friendly', 'label' => 'Samimi', 'icon' => 'fas fa-smile'],
                    ['value' => 'formal', 'label' => 'Resmi', 'icon' => 'fas fa-university'],
                    ['value' => 'casual', 'label' => 'Günlük', 'icon' => 'far fa-smile'],
                    ['value' => 'informative', 'label' => 'Bilgilendirici', 'icon' => 'fas fa-info-circle'],
                    ['value' => 'custom', 'label' => 'Diğer ton', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Eğlenceli, Mizahi, Duygusal, Lider ton']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            // ÖNEMLİ: Vurgu noktaları (Priority 2)
            [
                'step' => 6,
                'question_key' => 'emphasis_points',
                'question_text' => 'Vurgu Noktaları',
                'help_text' => 'İçeriklerde öne çıkarılmasını istediğiniz özellikler (özel belirtiniz)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'quality', 'label' => 'Kalite', 'icon' => 'fas fa-star'],
                    ['value' => 'price', 'label' => 'Uygun Fiyat', 'icon' => 'fas fa-dollar-sign'],
                    ['value' => 'speed', 'label' => 'Hız', 'icon' => 'fas fa-bolt'],
                    ['value' => 'experience', 'label' => 'Deneyim', 'icon' => 'fas fa-medal'],
                    ['value' => 'innovation', 'label' => 'Yenilikçilik', 'icon' => 'fas fa-lightbulb'],
                    ['value' => 'trust', 'label' => 'Güvenilirlik', 'icon' => 'fas fa-shield-alt'],
                    ['value' => 'customer-focus', 'label' => 'Müşteri Odaklılık', 'icon' => 'fas fa-heart'],
                    ['value' => 'custom', 'label' => 'Diğer vurgu', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Sosyal sorumluluk, Çevre bilinci, Yerellik']
                ],
                'is_required' => false,
                'sort_order' => 2
            ],
            // ORTA: Kaçınılacak konular (Priority 3)
            [
                'step' => 6,
                'question_key' => 'avoid_topics',
                'question_text' => 'Kaçınılacak Konular',
                'help_text' => 'Yapay Zeka\'nın içeriklerde bahsetmemesi gereken konular (özel belirtiniz)',
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
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    
    private function createAdvertisingQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'advertising',
                'step' => 3,
                'question_key' => 'advertising_services',
                'question_text' => 'Reklamcılık Hizmetleriniz',
                'help_text' => 'Sunduğunuz reklam ve pazarlama hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'dijital-reklam', 'label' => 'Dijital Reklam', 'icon' => 'fas fa-laptop'],
                    ['value' => 'sosyal-medya', 'label' => 'Sosyal Medya Yönetimi', 'icon' => 'fab fa-facebook'],
                    ['value' => 'marka-kimlik', 'label' => 'Marka Kimliği', 'icon' => 'fas fa-palette'],
                    ['value' => 'web-tasarim', 'label' => 'Web Tasarım', 'icon' => 'fas fa-code'],
                    ['value' => 'seo', 'label' => 'SEO Hizmetleri', 'icon' => 'fas fa-search'],
                    ['value' => 'video-produksiyon', 'label' => 'Video Prodüksiyon', 'icon' => 'fas fa-video'],
                    ['value' => 'fotograf', 'label' => 'Fotoğraf Çekimi', 'icon' => 'fas fa-camera'],
                    ['value' => 'matbaa', 'label' => 'Matbaa ve Tasarım', 'icon' => 'fas fa-print'],
                    ['value' => 'etkinlik-yonetimi', 'label' => 'Etkinlik Yönetimi', 'icon' => 'fas fa-calendar-alt'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Radyo reklamları, Outdoor reklam, Influencer pazarlama']
                ],
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'sector_code' => 'advertising',
                'step' => 3,
                'question_key' => 'target_sectors',
                'question_text' => 'Hedef Sektörler',
                'help_text' => 'Hangi sektörlere hizmet veriyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'e-ticaret', 'label' => 'E-Ticaret', 'icon' => 'fas fa-shopping-cart'],
                    ['value' => 'saglik', 'label' => 'Sağlık', 'icon' => 'fas fa-heartbeat'],
                    ['value' => 'egitim', 'label' => 'Eğitim', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'teknoloji', 'label' => 'Teknoloji', 'icon' => 'fas fa-microchip'],
                    ['value' => 'turizm', 'label' => 'Turizm', 'icon' => 'fas fa-plane'],
                    ['value' => 'gida', 'label' => 'Gıda & İçecek', 'icon' => 'fas fa-utensils'],
                    ['value' => 'custom', 'label' => 'Diğer sektör', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Tarım, Otomotiv, Finans']
                ],
                'is_required' => false,
                'sort_order' => 2
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createArtsCraftsQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'arts-crafts',
                'step' => 3,
                'question_key' => 'art_categories',
                'question_text' => 'Sanat Kategorileriniz',
                'help_text' => 'Hangi sanat dallarında faaliyet gösteriyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'resim', 'label' => 'Resim', 'icon' => 'fas fa-palette'],
                    ['value' => 'heykel', 'label' => 'Heykel', 'icon' => 'fas fa-monument'],
                    ['value' => 'seramik', 'label' => 'Seramik', 'icon' => 'fas fa-fire'],
                    ['value' => 'el-sanatlari', 'label' => 'El Sanatları', 'icon' => 'fas fa-hand-paper'],
                    ['value' => 'takı', 'label' => 'Takı Tasarımı', 'icon' => 'fas fa-gem'],
                    ['value' => 'dokuma', 'label' => 'Dokuma', 'icon' => 'fas fa-tshirt'],
                    ['value' => 'ahsap', 'label' => 'Ahşap İşleri', 'icon' => 'fas fa-tree'],
                    ['value' => 'custom', 'label' => 'Diğer sanat dalı', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Cam sanatı, Metal işleri, Deri işleri']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createAutomotiveQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'automotive',
                'step' => 3,
                'question_key' => 'automotive_services',
                'question_text' => 'Otomotiv Hizmetleriniz',
                'help_text' => 'Sunduğunuz otomotiv hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'satis', 'label' => 'Araç Satış', 'icon' => 'fas fa-car'],
                    ['value' => 'servis', 'label' => 'Servis & Bakım', 'icon' => 'fas fa-wrench'],
                    ['value' => 'yedek-parca', 'label' => 'Yedek Parça', 'icon' => 'fas fa-cogs'],
                    ['value' => 'ekspertiz', 'label' => 'Ekspertiz', 'icon' => 'fas fa-search'],
                    ['value' => 'kaporta', 'label' => 'Kaporta', 'icon' => 'fas fa-hammer'],
                    ['value' => 'boya', 'label' => 'Boya', 'icon' => 'fas fa-paint-brush'],
                    ['value' => 'lastik', 'label' => 'Lastik', 'icon' => 'fas fa-circle'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Cam değişimi, Klima bakımı, Alarm sistemi']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createBeautyQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'beauty-personal-care',
                'step' => 3,
                'question_key' => 'beauty_services',
                'question_text' => 'Güzellik Hizmetleriniz',
                'help_text' => 'Sunduğunuz güzellik ve kişisel bakım hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'sac-kesim', 'label' => 'Saç Kesim & Şekillendirme', 'icon' => 'fas fa-cut'],
                    ['value' => 'sac-boyama', 'label' => 'Saç Boyama', 'icon' => 'fas fa-palette'],
                    ['value' => 'makyaj', 'label' => 'Makyaj', 'icon' => 'fas fa-magic'],
                    ['value' => 'nail-art', 'label' => 'Nail Art', 'icon' => 'fas fa-hand-paper'],
                    ['value' => 'cilt-bakimi', 'label' => 'Cilt Bakımı', 'icon' => 'fas fa-spa'],
                    ['value' => 'epilasyon', 'label' => 'Epilasyon', 'icon' => 'fas fa-female'],
                    ['value' => 'masaj', 'label' => 'Masaj', 'icon' => 'fas fa-hand-holding-heart'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Solaryum, Mikropigmentasyon, Saç uzatma']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createBooksPublishingQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'books-publishing',
                'step' => 3,
                'question_key' => 'publishing_services',
                'question_text' => 'Yayıncılık Hizmetleriniz',
                'help_text' => 'Sunduğunuz yayıncılık ve kitap hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'kitap-yayinlama', 'label' => 'Kitap Yayınlama', 'icon' => 'fas fa-book'],
                    ['value' => 'dergi', 'label' => 'Dergi', 'icon' => 'fas fa-newspaper'],
                    ['value' => 'editörlük', 'label' => 'Editörlük', 'icon' => 'fas fa-edit'],
                    ['value' => 'ceviri', 'label' => 'Çeviri', 'icon' => 'fas fa-language'],
                    ['value' => 'tasarim', 'label' => 'Grafik Tasarım', 'icon' => 'fas fa-palette'],
                    ['value' => 'baski', 'label' => 'Baskı', 'icon' => 'fas fa-print'],
                    ['value' => 'dagitim', 'label' => 'Dağıtım', 'icon' => 'fas fa-truck'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: E-kitap, Sesli kitap, Özel baskı']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createConstructionQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'construction',
                'step' => 3,
                'question_key' => 'construction_services',
                'question_text' => 'İnşaat Hizmetleriniz',
                'help_text' => 'Sunduğunuz inşaat ve yapı hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'konut', 'label' => 'Konut İnşaatı', 'icon' => 'fas fa-home'],
                    ['value' => 'ticari', 'label' => 'Ticari Yapı', 'icon' => 'fas fa-building'],
                    ['value' => 'renovasyon', 'label' => 'Renovasyon', 'icon' => 'fas fa-hammer'],
                    ['value' => 'peyzaj', 'label' => 'Peyzaj', 'icon' => 'fas fa-tree'],
                    ['value' => 'yapi-malzeme', 'label' => 'Yapı Malzemesi', 'icon' => 'fas fa-cubes'],
                    ['value' => 'proje-tasarim', 'label' => 'Proje Tasarım', 'icon' => 'fas fa-drafting-compass'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Hafriyat, Kaba inşaat, Ince işler']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createEventsQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'events',
                'step' => 3,
                'question_key' => 'event_types',
                'question_text' => 'Etkinlik Türleriniz',
                'help_text' => 'Organize ettiğiniz etkinlik türleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'dugun', 'label' => 'Düğün', 'icon' => 'fas fa-ring'],
                    ['value' => 'konferans', 'label' => 'Konferans', 'icon' => 'fas fa-microphone'],
                    ['value' => 'seminer', 'label' => 'Seminer', 'icon' => 'fas fa-chalkboard-teacher'],
                    ['value' => 'dogumgunu', 'label' => 'Doğum Günü', 'icon' => 'fas fa-birthday-cake'],
                    ['value' => 'kurumsal', 'label' => 'Kurumsal Etkinlik', 'icon' => 'fas fa-building'],
                    ['value' => 'lansman', 'label' => 'Ürün Lansmanı', 'icon' => 'fas fa-rocket'],
                    ['value' => 'custom', 'label' => 'Diğer etkinlik', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Fashion show, Konser, Fuar']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createFinanceQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'finance',
                'step' => 3,
                'question_key' => 'finance_services',
                'question_text' => 'Finans Hizmetleriniz',
                'help_text' => 'Sunduğunuz finans ve bankacılık hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'kredi', 'label' => 'Kredi', 'icon' => 'fas fa-credit-card'],
                    ['value' => 'sigorta', 'label' => 'Sigorta', 'icon' => 'fas fa-shield-alt'],
                    ['value' => 'yatirim', 'label' => 'Yatırım Danışmanlığı', 'icon' => 'fas fa-chart-line'],
                    ['value' => 'emeklilik', 'label' => 'Emeklilik', 'icon' => 'fas fa-user-clock'],
                    ['value' => 'muhasebe', 'label' => 'Muhasebe', 'icon' => 'fas fa-calculator'],
                    ['value' => 'vergi', 'label' => 'Vergi Danışmanlığı', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Forex, Kripto para, Finansal planlama']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createRealEstateQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'real-estate',
                'step' => 3,
                'question_key' => 'realestate_services',
                'question_text' => 'Gayrimenkul Hizmetleriniz',
                'help_text' => 'Sunduğunuz gayrimenkul hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'satis', 'label' => 'Satış', 'icon' => 'fas fa-home'],
                    ['value' => 'kiralama', 'label' => 'Kiralama', 'icon' => 'fas fa-key'],
                    ['value' => 'degerleme', 'label' => 'Değerleme', 'icon' => 'fas fa-calculator'],
                    ['value' => 'yatirim', 'label' => 'Yatırım Danışmanlığı', 'icon' => 'fas fa-chart-line'],
                    ['value' => 'proje', 'label' => 'Proje Pazarlama', 'icon' => 'fas fa-building'],
                    ['value' => 'yonetim', 'label' => 'Emlak Yönetimi', 'icon' => 'fas fa-tasks'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Arsa değerleme, Ticari emlak, Turizm emlak']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createHomeGardenQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'home-garden',
                'step' => 3,
                'question_key' => 'home_services',
                'question_text' => 'Ev & Bahçe Hizmetleriniz',
                'help_text' => 'Sunduğunuz ev ve bahçe hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'dekorasyon', 'label' => 'İç Dekorasyon', 'icon' => 'fas fa-couch'],
                    ['value' => 'bahce-duzenle', 'label' => 'Bahçe Düzenleme', 'icon' => 'fas fa-seedling'],
                    ['value' => 'temizlik', 'label' => 'Temizlik', 'icon' => 'fas fa-broom'],
                    ['value' => 'tadilat', 'label' => 'Tadilat', 'icon' => 'fas fa-hammer'],
                    ['value' => 'boyama', 'label' => 'Boyama', 'icon' => 'fas fa-paint-brush'],
                    ['value' => 'mobilya', 'label' => 'Mobilya', 'icon' => 'fas fa-chair'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Elektrik, Tesisatçı, Cam balkon']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createJewelryQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'jewelry',
                'step' => 3,
                'question_key' => 'jewelry_products',
                'question_text' => 'Mücevher Ürünleriniz',
                'help_text' => 'Sattığınız mücevher ve aksesuar türleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'altin', 'label' => 'Altın', 'icon' => 'fas fa-coins'],
                    ['value' => 'gumus', 'label' => 'Gümüş', 'icon' => 'fas fa-circle'],
                    ['value' => 'pırlanta', 'label' => 'Pırlanta', 'icon' => 'fas fa-gem'],
                    ['value' => 'saat', 'label' => 'Saat', 'icon' => 'fas fa-clock'],
                    ['value' => 'yuzuk', 'label' => 'Yüzük', 'icon' => 'fas fa-ring'],
                    ['value' => 'kolye', 'label' => 'Kolye', 'icon' => 'fas fa-necklace'],
                    ['value' => 'custom', 'label' => 'Diğer ürün', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Küpe, Bilezik, Broş']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createMusicQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'music',
                'step' => 3,
                'question_key' => 'music_services',
                'question_text' => 'Müzik Hizmetleriniz',
                'help_text' => 'Sunduğunuz müzik ve ses hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'produksiyon', 'label' => 'Müzik Prodüksiyonu', 'icon' => 'fas fa-music'],
                    ['value' => 'egitim', 'label' => 'Müzik Eğitimi', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'kayit', 'label' => 'Kayıt Stüdyosu', 'icon' => 'fas fa-microphone'],
                    ['value' => 'miksleme', 'label' => 'Miksleme & Mastering', 'icon' => 'fas fa-sliders-h'],
                    ['value' => 'canli-performans', 'label' => 'Canlı Performans', 'icon' => 'fas fa-guitar'],
                    ['value' => 'enstruman', 'label' => 'Enstrüman Satış', 'icon' => 'fas fa-drum'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Ses düzenleme, Podcast, Jingle']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createNonprofitQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'nonprofit',
                'step' => 3,
                'question_key' => 'nonprofit_focus',
                'question_text' => 'Faaliyet Alanınız',
                'help_text' => 'Kar amacı gütmeyen organizasyonunuzun faaliyet alanları',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'egitim', 'label' => 'Eğitim', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'saglik', 'label' => 'Sağlık', 'icon' => 'fas fa-heartbeat'],
                    ['value' => 'cevre', 'label' => 'Çevre', 'icon' => 'fas fa-leaf'],
                    ['value' => 'hayvan', 'label' => 'Hayvan Hakları', 'icon' => 'fas fa-paw'],
                    ['value' => 'sosyal', 'label' => 'Sosyal Yardım', 'icon' => 'fas fa-hands-helping'],
                    ['value' => 'kultur', 'label' => 'Kültür & Sanat', 'icon' => 'fas fa-palette'],
                    ['value' => 'custom', 'label' => 'Diğer alan', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Kadın hakları, Engelli hakları, Çocuk hakları']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createPetsQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'pets',
                'step' => 3,
                'question_key' => 'pet_services',
                'question_text' => 'Evcil Hayvan Hizmetleriniz',
                'help_text' => 'Sunduğunuz evcil hayvan hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'veteriner', 'label' => 'Veteriner', 'icon' => 'fas fa-stethoscope'],
                    ['value' => 'pet-shop', 'label' => 'Pet Shop', 'icon' => 'fas fa-store'],
                    ['value' => 'bakim', 'label' => 'Bakım & Tımar', 'icon' => 'fas fa-cut'],
                    ['value' => 'egitim', 'label' => 'Eğitim', 'icon' => 'fas fa-graduation-cap'],
                    ['value' => 'pansiyon', 'label' => 'Pansiyon', 'icon' => 'fas fa-bed'],
                    ['value' => 'beslenme', 'label' => 'Beslenme & Vitamin', 'icon' => 'fas fa-pills'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Pet taksi, Fotoğrafçılık, Oyuncak']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createPhotographyQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'photography',
                'step' => 3,
                'question_key' => 'photography_types',
                'question_text' => 'Fotoğrafçılık Türleriniz',
                'help_text' => 'Sunduğunuz fotoğrafçılık hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'dugun', 'label' => 'Düğün', 'icon' => 'fas fa-ring'],
                    ['value' => 'portrait', 'label' => 'Portre', 'icon' => 'fas fa-user'],
                    ['value' => 'ticari', 'label' => 'Ticari', 'icon' => 'fas fa-briefcase'],
                    ['value' => 'etkinlik', 'label' => 'Etkinlik', 'icon' => 'fas fa-calendar-alt'],
                    ['value' => 'urun', 'label' => 'Ürün', 'icon' => 'fas fa-box'],
                    ['value' => 'video', 'label' => 'Video Çekimi', 'icon' => 'fas fa-video'],
                    ['value' => 'custom', 'label' => 'Diğer tür', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Moda, Mimari, Doğa']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createSecurityQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'security',
                'step' => 3,
                'question_key' => 'security_services',
                'question_text' => 'Güvenlik Hizmetleriniz',
                'help_text' => 'Sunduğunuz güvenlik hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'fiziksel', 'label' => 'Fiziksel Güvenlik', 'icon' => 'fas fa-shield-alt'],
                    ['value' => 'kamera', 'label' => 'Kamera Sistemi', 'icon' => 'fas fa-video'],
                    ['value' => 'alarm', 'label' => 'Alarm Sistemi', 'icon' => 'fas fa-bell'],
                    ['value' => 'siber', 'label' => 'Siber Güvenlik', 'icon' => 'fas fa-laptop'],
                    ['value' => 'erisim', 'label' => 'Erişim Kontrolü', 'icon' => 'fas fa-key'],
                    ['value' => 'yangin', 'label' => 'Yangın Güvenliği', 'icon' => 'fas fa-fire-extinguisher'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: VIP koruma, Etkinlik güvenliği']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createSportsQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'sports-fitness',
                'step' => 3,
                'question_key' => 'sports_services',
                'question_text' => 'Spor & Fitness Hizmetleriniz',
                'help_text' => 'Sunduğunuz spor ve fitness hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'fitness', 'label' => 'Fitness', 'icon' => 'fas fa-dumbbell'],
                    ['value' => 'personal-training', 'label' => 'Personal Training', 'icon' => 'fas fa-user-plus'],
                    ['value' => 'grup-dersleri', 'label' => 'Grup Dersleri', 'icon' => 'fas fa-users'],
                    ['value' => 'pilates', 'label' => 'Pilates', 'icon' => 'fas fa-leaf'],
                    ['value' => 'yoga', 'label' => 'Yoga', 'icon' => 'fas fa-meditation'],
                    ['value' => 'beslenme', 'label' => 'Beslenme Koçluğu', 'icon' => 'fas fa-apple-alt'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Crossfit, Martial arts, Swim']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createTextileQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'textile-fashion',
                'step' => 3,
                'question_key' => 'textile_products',
                'question_text' => 'Tekstil Ürünleriniz',
                'help_text' => 'Ürettiğiniz veya sattığınız tekstil ürünleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'giyim', 'label' => 'Giyim', 'icon' => 'fas fa-tshirt'],
                    ['value' => 'ayakkabi', 'label' => 'Ayakkabı', 'icon' => 'fas fa-shoe-prints'],
                    ['value' => 'canta', 'label' => 'Çanta', 'icon' => 'fas fa-shopping-bag'],
                    ['value' => 'aksesuar', 'label' => 'Aksesuar', 'icon' => 'fas fa-glasses'],
                    ['value' => 'ev-tekstil', 'label' => 'Ev Tekstili', 'icon' => 'fas fa-bed'],
                    ['value' => 'bebek', 'label' => 'Bebek Giyim', 'icon' => 'fas fa-baby'],
                    ['value' => 'custom', 'label' => 'Diğer ürün', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Iş kıyafeti, Spor giyim, Özel tasarım']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createTourismQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'tourism',
                'step' => 3,
                'question_key' => 'tourism_services',
                'question_text' => 'Turizm Hizmetleriniz',
                'help_text' => 'Sunduğunuz turizm ve seyahat hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'otel', 'label' => 'Otel', 'icon' => 'fas fa-bed'],
                    ['value' => 'tur', 'label' => 'Tur Organizasyonu', 'icon' => 'fas fa-route'],
                    ['value' => 'ucak', 'label' => 'Uçak Bileti', 'icon' => 'fas fa-plane'],
                    ['value' => 'transfer', 'label' => 'Transfer', 'icon' => 'fas fa-bus'],
                    ['value' => 'kiralama', 'label' => 'Araç Kiralama', 'icon' => 'fas fa-car'],
                    ['value' => 'rehberlik', 'label' => 'Rehberlik', 'icon' => 'fas fa-map-signs'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Yacht kiralama, Kamp, Macera turları']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createTransportationQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'transportation',
                'step' => 3,
                'question_key' => 'transport_services',
                'question_text' => 'Ulaştırma Hizmetleriniz',
                'help_text' => 'Sunduğunuz ulaştırma ve taşımacılık hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'otobus', 'label' => 'Otobüs', 'icon' => 'fas fa-bus'],
                    ['value' => 'taksi', 'label' => 'Taksi', 'icon' => 'fas fa-taxi'],
                    ['value' => 'minibus', 'label' => 'Minibüs', 'icon' => 'fas fa-shuttle-van'],
                    ['value' => 'kargo', 'label' => 'Kargo', 'icon' => 'fas fa-box'],
                    ['value' => 'nakliye', 'label' => 'Nakliye', 'icon' => 'fas fa-truck'],
                    ['value' => 'vip-transfer', 'label' => 'VIP Transfer', 'icon' => 'fas fa-crown'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Ambulans, Okul servisi, Yük taşıma']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
    
    private function createWeddingQuestions(): void
    {
        $questions = [
            [
                'sector_code' => 'wedding',
                'step' => 3,
                'question_key' => 'wedding_services',
                'question_text' => 'Düğün Hizmetleriniz',
                'help_text' => 'Sunduğunuz düğün ve nikah hizmetleri',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'organizasyon', 'label' => 'Düğün Organizasyonu', 'icon' => 'fas fa-ring'],
                    ['value' => 'fotograf', 'label' => 'Düğün Fotoğrafçısı', 'icon' => 'fas fa-camera'],
                    ['value' => 'video', 'label' => 'Video Çekimi', 'icon' => 'fas fa-video'],
                    ['value' => 'musik', 'label' => 'Müzik & DJ', 'icon' => 'fas fa-music'],
                    ['value' => 'catering', 'label' => 'Catering', 'icon' => 'fas fa-utensils'],
                    ['value' => 'dekorasyon', 'label' => 'Dekorasyon', 'icon' => 'fas fa-palette'],
                    ['value' => 'custom', 'label' => 'Diğer hizmet', 'icon' => 'fas fa-edit', 'has_custom_input' => true, 'custom_placeholder' => 'Örn: Çiçek, Düğün arabası, Animasyon']
                ],
                'is_required' => true,
                'sort_order' => 1
            ]
        ];
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
        }
    }
}