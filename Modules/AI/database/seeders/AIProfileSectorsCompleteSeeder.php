<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorsCompleteSeeder extends Seeder
{
    /**
     * AI PROFİL SEKTÖR VERİLERİ - KAPSAMLI VE EKSİKSİZ YAPIDA
     * 
     * index.php + AIProfileSanayiSeeder + Yeni kategorilerin birleşimi
     * Ana kategoriler + Alt kategoriler (tüm gerçek verilerle)
     * ID'ler tutarlı ve organize, index.php'deki TÜM kategoriler dahil
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Sectors - KAPSAMLI YAPIDA (index.php + Sanayi) Yükleniyor...\n";
        
        // Önce mevcut verileri temizle
        AIProfileSector::truncate();
        
        $this->createMainCategories();
        $this->createSubcategories();
        
        echo "\n🎯 Kapsamlı kategorizasyon tamamlandı! (index.php + Sanayi data)\n";
    }
    
    /**
     * Ana kategorileri oluştur (category_id = null) - index.php'deki TÜM kategoriler dahil
     */
    private function createMainCategories(): void
    {
        $mainCategories = [
            [
                'id' => 1,
                'code' => 'technology_main',
                'category_id' => null,
                'name' => 'Teknoloji & Bilişim',
                'emoji' => '💻',
                'color' => 'blue',
                'description' => 'Yazılım, donanım, IT hizmetleri ve teknoloji çözümleri',
                'keywords' => 'teknoloji, bilişim, yazılım, software, development, programming, coding, IT, bilgisayar, computer, sistem, system, web, app, mobile, developer, geliştirici, programcı, coder, teknisyen, otomasyon, automation, digital, dijital, backend, frontend, fullstack, database, veritabanı, API, framework',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 2,
                'code' => 'health_main',
                'category_id' => null,
                'name' => 'Sağlık & Tıp',
                'emoji' => '🏥',
                'color' => 'green',
                'description' => 'Hastane, klinik, doktor ve sağlık hizmetleri',
                'keywords' => 'sağlık, health, tıp, medical, doktor, doctor, hekim, hastane, hospital, klinik, clinic, tedavi, treatment, hasta, patient, sağlık hizmetleri, healthcare, tıbbi, medicine, ilaç, pharmacy, eczane, hasta bakımı, patient care, tanı, diagnosis, muayene, examination, ameliyat, surgery',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 3,
                'code' => 'education_main',
                'category_id' => null,
                'name' => 'Eğitim & Öğretim',
                'emoji' => '🎓',
                'color' => 'yellow',
                'description' => 'Okul, kurs, eğitim kurumları ve online eğitim',
                'keywords' => 'eğitim, education, öğretim, teaching, okul, school, ders, lesson, kurs, course, öğretmen, teacher, akademi, academy, öğrenci, student, öğrenme, learning, ders verme, tutoring, özel ders, private lesson, online eğitim, online education, uzaktan eğitim, distance learning',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 4,
                'code' => 'food_main',
                'category_id' => null,
                'name' => 'Yiyecek & İçecek',
                'emoji' => '🍽️',
                'color' => 'orange',
                'description' => 'Gıda üretimi, restoran, kafe ve yemek hizmetleri',
                'keywords' => 'yemek, food, restoran, restaurant, cafe, mutfak, kitchen, aşçı, chef, yiyecek, içecek, drink, catering, gıda, pastane, fırın, fast food, healthy food, bar, pub, delivery, servis',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 5,
                'code' => 'commerce_main',
                'category_id' => null,
                'name' => 'E-ticaret & Perakende',
                'emoji' => '🛍️',
                'color' => 'purple',
                'description' => 'Online satış, mağaza, alışveriş ve ticaret',
                'keywords' => 'satış, sales, mağaza, store, alışveriş, shopping, e-ticaret, ecommerce, online, perakende, retail, ürün, product, marketplace, fashion, elektronik, ev, kitap, oyuncak, kozmetik, otomotiv',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 6,
                'code' => 'construction_main',
                'category_id' => null,
                'name' => 'İnşaat & Emlak',
                'emoji' => '🏗️',
                'color' => 'gray',
                'description' => 'İnşaat, mimarlık, emlak ve yapı sektörü',
                'keywords' => 'inşaat, construction, emlak, real estate, ev, house, bina, building, konut, proje, project, müteahhit, contractor, mimari, architecture',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 7,
                'code' => 'finance_main',
                'category_id' => null,
                'name' => 'Finans & Muhasebe',
                'emoji' => '💰',
                'color' => 'green',
                'description' => 'Finans, muhasebe, bankacılık ve mali hizmetler',
                'keywords' => 'finans, finance, muhasebe, accounting, para, money, banka, bank, yatırım, investment, sigorta, insurance, kredi, credit',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 8,
                'code' => 'industry_main',
                'category_id' => null,
                'name' => 'Sanayi & Üretim',
                'emoji' => '🏭',
                'color' => 'brown',
                'description' => 'Endüstriyel üretim, imalat, sanayi sektörü',
                'keywords' => 'sanayi, endüstri, imalat, üretim, fabrika, makina, industry, manufacturing',
                'is_active' => true,
                'sort_order' => 80
            ]
        ];
        
        foreach ($mainCategories as $category) {
            AIProfileSector::create($category);
            echo "✅ Ana Kategori: {$category['name']} - ID: {$category['id']}\n";
        }
    }
    
    /**
     * Alt kategorileri (sektörleri) oluştur - index.php + Sanayi verilerinin kombinasyonu
     */
    private function createSubcategories(): void
    {
        $subcategories = [
            // TEKNOLOJI & BİLİŞİM (Ana Kategori ID: 1) - index.php technology section
            [
                'id' => 101,
                'code' => 'technology_development',
                'category_id' => 1,
                'name' => 'Teknoloji & Yazılım Geliştirme',
                'emoji' => '🏢',
                'description' => 'Web, mobil, desktop uygulamalar, sistem geliştirme, özel yazılım çözümleri',
                'keywords' => 'teknoloji yazılım software development programming kodlama app uygulama',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 102,
                'code' => 'it_consultancy',
                'category_id' => 1,
                'name' => 'BT Danışmanlığı & Sistem Entegrasyonu',
                'emoji' => '💾',
                'description' => 'IT altyapı, sistem kurulumu, teknik danışmanlık, network çözümleri',
                'keywords' => 'IT bilişim sistem danışmanlık entegrasyon consulting system',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 103,
                'code' => 'web_design',
                'category_id' => 1,
                'name' => 'Web Tasarım & Dijital Ajans',
                'emoji' => '🌐',
                'description' => 'Website tasarımı, e-ticaret, dijital pazarlama, SEO hizmetleri',
                'keywords' => 'web website tasarım design dijital digital ajans agency',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 104,
                'code' => 'mobile_app',
                'category_id' => 1,
                'name' => 'Mobil Uygulama Geliştirme',
                'emoji' => '📱',
                'description' => 'Android, iOS, hybrid mobil uygulamalar, app store optimizasyonu',
                'keywords' => 'mobil mobile app uygulama android ios telefon phone',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 105,
                'code' => 'ai_ml',
                'category_id' => 1,
                'name' => 'Yapay Zeka & Makine Öğrenmesi',
                'emoji' => '🤖',
                'description' => 'AI çözümleri, chatbot, otomasyon sistemleri, veri analizi',
                'keywords' => 'AI yapay zeka makine öğrenmesi machine learning chatbot otomasyon',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 106,
                'code' => 'cloud_devops',
                'category_id' => 1,
                'name' => 'Bulut Bilişim & DevOps',
                'emoji' => '☁️',
                'description' => 'Cloud hosting, sunucu yönetimi, altyapı hizmetleri, migration',
                'keywords' => 'cloud bulut hosting server sunucu devops infrastructure',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 107,
                'code' => 'cybersecurity',
                'category_id' => 1,
                'name' => 'Siber Güvenlik & Veri Koruma',
                'emoji' => '🔒',
                'description' => 'Sistem güvenliği, veri koruma, siber tehdit önleme, güvenlik denetimi',
                'keywords' => 'güvenlik security siber cyber veri data koruma protection',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 108,
                'code' => 'data_analytics',
                'category_id' => 1,
                'name' => 'Veri Analizi & İş Zekası',
                'emoji' => '📊',
                'description' => 'Büyük veri analizi, raporlama, karar destek sistemleri, BI çözümleri',
                'keywords' => 'veri data analiz analysis iş zekası business intelligence BI',
                'is_active' => true,
                'sort_order' => 80
            ],

            // SAĞLIK & TIP (Ana Kategori ID: 2) - index.php health section
            [
                'id' => 201,
                'code' => 'hospital',
                'category_id' => 2,
                'name' => 'Hastane & Sağlık Merkezi',
                'emoji' => '🏥',
                'description' => 'Genel hastane, devlet hastanesi, özel hastane, sağlık kompleksi, acil servis',
                'keywords' => 'hastane hospital sağlık merkezi health center tıp merkezi',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 202,
                'code' => 'clinic',
                'category_id' => 2,
                'name' => 'Özel Muayenehane & Klinik',
                'emoji' => '🩺',
                'description' => 'Özel doktor muayenehanesi, uzman kliniği, poliklinik, check-up merkezi',
                'keywords' => 'muayenehane klinik clinic özel private doktor doctor',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 203,
                'code' => 'dental',
                'category_id' => 2,
                'name' => 'Diş Hekimliği & Ağız Sağlığı',
                'emoji' => '🦷',
                'description' => 'Diş tedavisi, implant, ortodonti, ağız cerrahisi, estetik diş hekimliği',
                'keywords' => 'diş dental ağız oral implant ortodonti diş hekimi',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 204,
                'code' => 'optometry',
                'category_id' => 2,
                'name' => 'Göz Sağlığı & Optisyenlik',
                'emoji' => '👁️',
                'description' => 'Göz muayenesi, gözlük, lens, görme bozuklukları, lazer göz ameliyatı',
                'keywords' => 'göz eye optisyen gözlük lens görme vision',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 205,
                'code' => 'pharmacy',
                'category_id' => 2,
                'name' => 'Eczane & İlaç Sektörü',
                'emoji' => '💊',
                'description' => 'Reçeteli ilaç, OTC ürünler, sağlık malzemeleri, vitamin takviyesi',
                'keywords' => 'eczane pharmacy ilaç medicine farmasötik pharmaceutical',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 206,
                'code' => 'laboratory',
                'category_id' => 2,
                'name' => 'Laboratuvar & Tıbbi Testler',
                'emoji' => '🧬',
                'description' => 'Kan tahlili, görüntüleme, patoloji, mikrobiyoloji, genetik testler',
                'keywords' => 'laboratuvar lab tıbbi test kan tahlil',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 207,
                'code' => 'aesthetic',
                'category_id' => 2,
                'name' => 'Estetik & Plastik Cerrahi',
                'emoji' => '💉',
                'description' => 'Estetik operasyonlar, botox, dolgu, güzellik, anti-aging',
                'keywords' => 'estetik plastik cerrahi güzellik botox',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 208,
                'code' => 'alternative_medicine',
                'category_id' => 2,
                'name' => 'Alternatif Tıp & Wellness',
                'emoji' => '🧘',
                'description' => 'Homeopati, akupunktur, fitoterapii, yoga terapisi, reiki',
                'keywords' => 'alternatif tıp wellness homeopati akupunktur',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 209,
                'code' => 'physiotherapy',
                'category_id' => 2,
                'name' => 'Fizyoterapi & Rehabilitasyon',
                'emoji' => '🦴',
                'description' => 'Fizik tedavi, manuel terapi, spor yaralanmaları, ortez protez',
                'keywords' => 'fizyoterapi rehabilitasyon fizik tedavi',
                'is_active' => true,
                'sort_order' => 90
            ],
            [
                'id' => 210,
                'code' => 'medical_devices',
                'category_id' => 2,
                'name' => 'Tıbbi Cihaz & Malzeme',
                'emoji' => '🩹',
                'description' => 'Medikal ekipman, ortez, protez, tıbbi sarf malzeme, hasta bakım',
                'keywords' => 'tıbbi cihaz malzeme medikal ekipman',
                'is_active' => true,
                'sort_order' => 100
            ],

            // EĞİTİM & ÖĞRETİM (Ana Kategori ID: 3) - index.php education section
            [
                'id' => 301,
                'code' => 'school_institutions',
                'category_id' => 3,
                'name' => 'Okul & Eğitim Kurumları',
                'emoji' => '🏫',
                'description' => 'İlkokul, ortaokul, lise, üniversite, kreş, anaokulu, dersane',
                'keywords' => 'okul school eğitim education kurum institution academy',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 302,
                'code' => 'private_tutoring',
                'category_id' => 3,
                'name' => 'Özel Ders & Koçluk',
                'emoji' => '👨‍🏫',
                'description' => 'Birebir eğitim, home teaching, akademik mentoring, sınav hazırlık',
                'keywords' => 'özel ders private lesson koçluk coaching mentoring tutor',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 303,
                'code' => 'online_education',
                'category_id' => 3,
                'name' => 'Online Eğitim Platformları',
                'emoji' => '💻',
                'description' => 'Uzaktan eğitim, e-learning, webinar, online kurslar, LMS',
                'keywords' => 'online uzaktan distance platform e-learning digital',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 304,
                'code' => 'language_training',
                'category_id' => 3,
                'name' => 'Dil Eğitimi & Çeviri',
                'emoji' => '🌍',
                'description' => 'Yabancı dil kursu, çeviri hizmetleri, tercümanlık, sözlü çeviri',
                'keywords' => 'dil language İngilizce english çeviri translation yabancı dil',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 305,
                'code' => 'vocational_training',
                'category_id' => 3,
                'name' => 'Mesleki Eğitim & Sertifikasyon',
                'emoji' => '🎯',
                'description' => 'Meslek kursları, teknik eğitim, sertifika programları, beceri geliştirme',
                'keywords' => 'mesleki vocational sertifika certificate diploma kariyer',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 306,
                'code' => 'preschool',
                'category_id' => 3,
                'name' => 'Okul Öncesi & Anaokulu',
                'emoji' => '👶',
                'description' => 'Erken çocukluk eğitimi, oyun-based learning, gelişim programları',
                'keywords' => 'okul öncesi anaokulu kreş çocuk preschool',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 307,
                'code' => 'course_seminars',
                'category_id' => 3,
                'name' => 'Kurs & Seminer Organizasyonu',
                'emoji' => '📚',
                'description' => 'Seminer düzenleme, workshop, atölye çalışmaları, eğitim etkinlikleri',
                'keywords' => 'kurs course seminer workshop atölye etkinlik',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 308,
                'code' => 'arts_music_education',
                'category_id' => 3,
                'name' => 'Sanat & Müzik Eğitimi',
                'emoji' => '🎨',
                'description' => 'Resim kursu, müzik dersleri, dans eğitimi, yaratıcı atölyeler',
                'keywords' => 'sanat art müzik music dans resim yaratıcı',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 309,
                'code' => 'sports_training',
                'category_id' => 3,
                'name' => 'Spor Eğitimi & Antrenörlük',
                'emoji' => '🏋️',
                'description' => 'Spor kursları, fitness antrenörlüğü, takım sporları, bireysel antrenman',
                'keywords' => 'spor sport antrenör coach fitness egzersiz',
                'is_active' => true,
                'sort_order' => 90
            ],

            // YIYECEK & İÇECEK (Ana Kategori ID: 4) - index.php food section
            [
                'id' => 401,
                'code' => 'restaurant',
                'category_id' => 4,
                'name' => 'Restoran & Lokanta',
                'emoji' => '🍕',
                'description' => 'Fine dining, casual dining, fast casual, etnik mutfaklar, konsept restoranlar',
                'keywords' => 'restoran restaurant lokanta yemek food aşçı chef',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 402,
                'code' => 'cafe_coffeehouse',
                'category_id' => 4,
                'name' => 'Kafe & Kahvehane',
                'emoji' => '☕',
                'description' => 'Specialty coffee, çay evi, internet kafe, co-working cafe, brunch',
                'keywords' => 'kafe cafe kahve coffee çay tea kahvehane',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 403,
                'code' => 'bakery_patisserie',
                'category_id' => 4,
                'name' => 'Pastane & Fırın',
                'emoji' => '🍰',
                'description' => 'Artisan pastane, ekmek fırını, butik pasta, özel tasarım kekler',
                'keywords' => 'pastane patisserie fırın bakery pasta cake ekmek bread',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 404,
                'code' => 'fast_food',
                'category_id' => 4,
                'name' => 'Fast Food & Sokak Lezzetleri',
                'emoji' => '🍔',
                'description' => 'Burger, pizza, döner, street food, quick service restaurant',
                'keywords' => 'fast food hızlı burger pizza sokak street food',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 405,
                'code' => 'healthy_food',
                'category_id' => 4,
                'name' => 'Healthy Food & Vegan',
                'emoji' => '🥗',
                'description' => 'Sağlıklı beslenme, organik gıda, vegan menü, detox, raw food',
                'keywords' => 'healthy sağlıklı vegan organik organic diyet diet',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 406,
                'code' => 'bar_pub',
                'category_id' => 4,
                'name' => 'Bar & Pub',
                'emoji' => '🍻',
                'description' => 'Cocktail bar, craft beer, wine bar, sports bar, live music venue',
                'keywords' => 'bar pub cocktail bira beer şarap wine',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 407,
                'code' => 'catering_delivery',
                'category_id' => 4,
                'name' => 'Yemek Servisi & Catering',
                'emoji' => '🚚',
                'description' => 'Toplu yemek, etkinlik catering, delivery, kurumsal yemek hizmetleri',
                'keywords' => 'catering servis delivery yemek dağıtım',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 408,
                'code' => 'food_production',
                'category_id' => 4,
                'name' => 'Gıda Üretimi & Dağıtım',
                'emoji' => '🛒',
                'description' => 'Gıda üretim, toptan gıda, tedarik zinciri, packaging, food processing',
                'keywords' => 'gıda üretim toptan wholesale dağıtım distribution',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 409,
                'code' => 'wine_spirits',
                'category_id' => 4,
                'name' => 'Şarap & İçki Üretimi',
                'emoji' => '🍷',
                'description' => 'Bağcılık, şarap üretimi, craft distillery, spirits, alkollü içecek',
                'keywords' => 'şarap wine alkol alcohol içki spirits',
                'is_active' => true,
                'sort_order' => 90
            ],

            // E-TİCARET & PERAKENDE (Ana Kategori ID: 5) - index.php retail section
            [
                'id' => 501,
                'code' => 'general_ecommerce',
                'category_id' => 5,
                'name' => 'Genel E-ticaret & Marketplace',
                'emoji' => '🛒',
                'description' => 'Online mağaza, çoklu satıcı platformu, B2B-B2C satış, dropshipping',
                'keywords' => 'e-ticaret ecommerce online marketplace satış shopping',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 502,
                'code' => 'fashion',
                'category_id' => 5,
                'name' => 'Moda & Giyim',
                'emoji' => '👕',
                'description' => 'Hazır giyim, aksesuar, ayakkabı, çanta, moda tasarımı, butik',
                'keywords' => 'moda fashion giyim clothing tekstil style trend',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 503,
                'code' => 'electronics_tech',
                'category_id' => 5,
                'name' => 'Elektronik & Teknoloji Ürünleri',
                'emoji' => '💻',
                'description' => 'Bilgisayar, telefon, gaming, elektronik aksesuar, teknik servis',
                'keywords' => 'elektronik electronic teknoloji technology bilgisayar computer',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 504,
                'code' => 'home_lifestyle',
                'category_id' => 5,
                'name' => 'Ev & Yaşam Ürünleri',
                'emoji' => '🏠',
                'description' => 'Mobilya, dekorasyon, ev tekstili, bahçe, mutfak eşyaları, organizasyon',
                'keywords' => 'ev home yaşam lifestyle dekorasyon decoration eşya',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 505,
                'code' => 'books_stationery',
                'category_id' => 5,
                'name' => 'Kitap & Kırtasiye',
                'emoji' => '📚',
                'description' => 'Kitap satış, akademik yayın, ofis malzemeleri, sanat malzemeleri',
                'keywords' => 'kitap book kırtasiye stationery yayınevi publisher',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 506,
                'code' => 'toys_hobbies',
                'category_id' => 5,
                'name' => 'Oyuncak & Hobi Ürünleri',
                'emoji' => '🎮',
                'description' => 'Çocuk oyuncağı, board games, koleksiyon, hobi malzemeleri, craft',
                'keywords' => 'oyuncak toy hobi hobby oyun game koleksiyon',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 507,
                'code' => 'hardware_building',
                'category_id' => 5,
                'name' => 'Hırdavat & Yapı Malzemeleri',
                'emoji' => '🔧',
                'description' => 'İnşaat malzeme, el aletleri, elektrik malzeme, bahçe aletleri',
                'keywords' => 'hırdavat hardware yapı building malzeme tool',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 508,
                'code' => 'automotive_parts',
                'category_id' => 5,
                'name' => 'Otomotiv Yedek Parça',
                'emoji' => '🚗',
                'description' => 'Araç yedek parça, motor yağı, lastik, aksesuar, tuning ürünleri',
                'keywords' => 'otomotiv automotive yedek parça car araba',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 509,
                'code' => 'cosmetics_personal',
                'category_id' => 5,
                'name' => 'Kozmetik & Kişisel Bakım',
                'emoji' => '💄',
                'description' => 'Makyaj ürünleri, cilt bakımı, parfüm, kişisel hijyen, organic kozmetik',
                'keywords' => 'kozmetik cosmetic güzellik beauty makyaj parfum',
                'is_active' => true,
                'sort_order' => 90
            ],

            // İNŞAAT & EMLAK (Ana Kategori ID: 6) - index.php construction section
            [
                'id' => 601,
                'code' => 'residential_construction',
                'category_id' => 6,
                'name' => 'Konut İnşaatı & Müteahhitlik',
                'emoji' => '🏠',
                'description' => 'Konut projeleri, villa inşaatı, site geliştirme, kentsel dönüşüm',
                'keywords' => 'konut housing müteahhit contractor villa residence',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 602,
                'code' => 'commercial_construction',
                'category_id' => 6,
                'name' => 'Ticari & Endüstriyel İnşaat',
                'emoji' => '🏢',
                'description' => 'AVM, ofis binası, fabrika, depo, endüstriyel tesis inşaatı',
                'keywords' => 'ticari commercial endüstriyel industrial fabrika factory',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 603,
                'code' => 'real_estate',
                'category_id' => 6,
                'name' => 'Emlak Danışmanlığı & Satış',
                'emoji' => '🏡',
                'description' => 'Emlak alım-satım, kiralama, yatırım danışmanlığı, ekspertiz',
                'keywords' => 'emlak real estate satış sales kiralama rental gayrimenkul',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 604,
                'code' => 'interior_design',
                'category_id' => 6,
                'name' => 'İç Mimari & Dekorasyon',
                'emoji' => '🎨',
                'description' => 'İç mekan tasarımı, dekoratif ürünler, mobilya tasarımı, lighting',
                'keywords' => 'iç mimari interior dekorasyon decoration tasarım design',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 605,
                'code' => 'landscape_garden',
                'category_id' => 6,
                'name' => 'Peyzaj & Bahçe Tasarımı',
                'emoji' => '🌿',
                'description' => 'Landscape architecture, bahçe düzenleme, sulama sistemleri, hardscape',
                'keywords' => 'peyzaj landscape bahçe garden çevre düzenleme',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 606,
                'code' => 'renovation',
                'category_id' => 6,
                'name' => 'Tadilat & Renovasyon',
                'emoji' => '🔨',
                'description' => 'Ev yenileme, restorasyon, bakım onarım, iyileştirme projeleri',
                'keywords' => 'tadilat renovation restorasyon yenileme bakım',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 607,
                'code' => 'project_management',
                'category_id' => 6,
                'name' => 'Proje Yönetimi & Müşavirlik',
                'emoji' => '🏗️',
                'description' => 'İnşaat proje yönetimi, teknik müşavirlik, kontrollük hizmetleri',
                'keywords' => 'proje management müşavirlik consulting kontrol',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 608,
                'code' => 'construction_materials',
                'category_id' => 6,
                'name' => 'İnşaat Malzemesi & Satış',
                'emoji' => '📏',
                'description' => 'Yapı malzemesi, prefabrik, çelik konstrüksiyon, izolasyon malzemeleri',
                'keywords' => 'malzeme material yapı construction çelik steel',
                'is_active' => true,
                'sort_order' => 80
            ],

            // FINANS & MUHASEBE (Ana Kategori ID: 7) - index.php finance section
            [
                'id' => 701,
                'code' => 'accounting',
                'category_id' => 7,
                'name' => 'Muhasebe & Mali Müşavirlik',
                'emoji' => '📊',
                'description' => 'Defter tutma, vergi beyannamesi, bordro, SGK işlemleri, mali müşavirlik',
                'keywords' => 'muhasebe accounting mali müşavir CPA finansal financial',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 702,
                'code' => 'banking',
                'category_id' => 7,
                'name' => 'Bankacılık & Finansal Hizmetler',
                'emoji' => '🏦',
                'description' => 'Kredi, mevduat, döviz, para transferi, financial planning',
                'keywords' => 'banka bank finansal financial banking kredi loan',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 703,
                'code' => 'investment',
                'category_id' => 7,
                'name' => 'Yatırım Danışmanlığı',
                'emoji' => '📈',
                'description' => 'Portföy yönetimi, borsa analizi, emlak yatırımı, wealth management',
                'keywords' => 'yatırım investment portföy portfolio borsa stock',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 704,
                'code' => 'insurance',
                'category_id' => 7,
                'name' => 'Sigorta & Risk Yönetimi',
                'emoji' => '🛡️',
                'description' => 'Hayat sigortası, kasko, sağlık sigortası, işyeri sigortası, risk analizi',
                'keywords' => 'sigorta insurance risk güvence protection poliçe',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 705,
                'code' => 'payment_systems',
                'category_id' => 7,
                'name' => 'Ödeme Sistemleri & Fintech',
                'emoji' => '💳',
                'description' => 'POS sistemleri, mobil ödeme, digital wallet, blockchain, fintech çözümleri',
                'keywords' => 'ödeme payment fintech POS mobil ödeme digital',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 706,
                'code' => 'tax_consultancy',
                'category_id' => 7,
                'name' => 'Vergi Danışmanlığı',
                'emoji' => '📋',
                'description' => 'Vergi optimizasyonu, beyanname hazırlığı, vergi denetimi, tax planning',
                'keywords' => 'vergi tax danışmanlık beyanname denetim',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 707,
                'code' => 'property_valuation',
                'category_id' => 7,
                'name' => 'Emlak Değerleme & Ekspertiz',
                'emoji' => '💎',
                'description' => 'Emlak ekspertizi, değerleme raporu, pazar analizi, investment advisory',
                'keywords' => 'emlak değerleme ekspertiz pazar market analiz',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 708,
                'code' => 'lending_factoring',
                'category_id' => 7,
                'name' => 'Kredilendirme & Faktoring',
                'emoji' => '🎯',
                'description' => 'Ticari krediler, faktoring, leasing, forfaiting, trade finance',
                'keywords' => 'kredi credit faktoring leasing ticari commercial',
                'is_active' => true,
                'sort_order' => 80
            ],

            // SANAYİ & ÜRETİM (Ana Kategori ID: 8) - AIProfileSanayiSeeder'dan alınan veriler
            [
                'id' => 801,
                'code' => 'machinery_manufacturing',
                'category_id' => 8,
                'name' => 'Makina & Ekipman İmalatı',
                'emoji' => '⚙️',
                'description' => 'Endüstriyel makina, otomasyon, CNC tezgah üretimi',
                'keywords' => 'makina machinery imalat manufacturing endüstriyel industrial otomasyon automation CNC',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 802,
                'code' => 'chemical_petrochemical',
                'category_id' => 8,
                'name' => 'Kimya & Petrokimya',
                'emoji' => '🧪',
                'description' => 'Kimyasal üretim, petrokimya, plastik sanayi',
                'keywords' => 'kimya chemical petrokimya petrochemical plastik plastic kimyasal',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 803,
                'code' => 'energy_production',
                'category_id' => 8,
                'name' => 'Enerji & Elektrik Üretimi',
                'emoji' => '⚡',
                'description' => 'Elektrik üretimi, enerji santrali, güç sistemleri',
                'keywords' => 'enerji energy elektrik electric üretim production santral power',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 804,
                'code' => 'mining_raw_materials',
                'category_id' => 8,
                'name' => 'Maden & Ham Madde',
                'emoji' => '⛏️',
                'description' => 'Maden çıkarma, ham madde üretimi, taş ocağı',
                'keywords' => 'maden mining ham madde raw materials taş stone ocak',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 805,
                'code' => 'automotive_industry',
                'category_id' => 8,
                'name' => 'Otomotiv Yan Sanayi',
                'emoji' => '🚗',
                'description' => 'Araç parça üretimi, OEM, otomotiv sanayii',
                'keywords' => 'otomotiv automotive yan sanayi OEM parça parts',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 806,
                'code' => 'metallurgy',
                'category_id' => 8,
                'name' => 'Metal & Demir Çelik',
                'emoji' => '🔩',
                'description' => 'Metal işleme, demir çelik üretimi, döküm',
                'keywords' => 'metal demir iron çelik steel döküm casting işleme processing',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 807,
                'code' => 'textile_manufacturing',
                'category_id' => 8,
                'name' => 'Tekstil & Konfeksiyon',
                'emoji' => '🧵',
                'description' => 'Tekstil üretimi, konfeksiyon, dokuma',
                'keywords' => 'tekstil textile konfeksiyon confection dokuma weaving',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 808,
                'code' => 'food_processing',
                'category_id' => 8,
                'name' => 'Gıda İşleme & Üretim',
                'emoji' => '🏭',
                'description' => 'Endüstriyel gıda üretimi, işleme, ambalajlama',
                'keywords' => 'gıda food işleme processing üretim production ambalaj packaging',
                'is_active' => true,
                'sort_order' => 80
            ]
        ];
        
        $categoryGroups = [
            1 => 'Teknoloji & Bilişim',
            2 => 'Sağlık & Tıp', 
            3 => 'Eğitim & Öğretim',
            4 => 'Yiyecek & İçecek',
            5 => 'E-ticaret & Perakende',
            6 => 'İnşaat & Emlak',
            7 => 'Finans & Muhasebe',
            8 => 'Sanayi & Üretim'
        ];
        
        foreach ($subcategories as $subcategory) {
            AIProfileSector::create($subcategory);
            $categoryName = $categoryGroups[$subcategory['category_id']] ?? 'Bilinmeyen';
            echo "   → {$subcategory['name']} ({$categoryName}) - ID: {$subcategory['id']}\n";
        }
        
        echo "\n📊 Toplam: " . count($subcategories) . " alt kategori eklendi\n";
        echo "🗂️ 8 ana kategori + " . count($subcategories) . " alt kategori = Tam kapsamlı seeder\n";
    }
}