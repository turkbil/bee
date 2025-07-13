<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorsCompleteSeeder extends Seeder
{
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        // Önce mevcut verileri temizle
        AIProfileSector::truncate();
        
        $this->createMainCategories();
        $this->createSubcategories();
        
        echo "\n🎯 Kapsamlı kategorizasyon tamamlandı! Teknoloji & Bilişim dahil\n";
    }
    
    private function createMainCategories(): void
    {
        $mainCategories = [
            [
                'id' => 1,
                'code' => 'technology',
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
                'code' => 'health',
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
                'code' => 'education',
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
                'code' => 'food',
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
                'code' => 'retail',
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
                'code' => 'construction',
                'category_id' => null,
                'name' => 'İnşaat & Emlak',
                'emoji' => '🏗️',
                'color' => 'teal',
                'description' => 'İnşaat, gayrimenkul, mimarlık, mühendislik',
                'keywords' => 'inşaat, emlak, gayrimenkul, müteahhit, mimarlık, mühendislik, construction, real estate, architecture, engineering',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 7,
                'code' => 'finance',
                'category_id' => null,
                'name' => 'Finans & Muhasebe',
                'emoji' => '💰',
                'color' => 'cyan',
                'description' => 'Bankacılık, muhasebe, finansal danışmanlık',
                'keywords' => 'finans, muhasebe, banka, sigorta, yatırım, finance, accounting, bank, insurance, investment',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 8,
                'code' => 'art_design',
                'category_id' => null,
                'name' => 'Sanat & Tasarım',
                'emoji' => '🎨',
                'color' => 'pink',
                'description' => 'Grafik tasarım, sanat, kreatif hizmetler',
                'keywords' => 'tasarım, sanat, grafik, kreatif, reklam, design, art, creative, advertising',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 9,
                'code' => 'sports',
                'category_id' => null,
                'name' => 'Spor & Fitness',
                'emoji' => '🏋️',
                'color' => 'indigo',
                'description' => 'Spor kulübü, fitness, antrenörlük, spor hizmetleri',
                'keywords' => 'spor, fitness, antrenör, kulüp, spor salonu, gym, pilates, yoga, dövüş, yüzme, futbol, basketbol',
                'is_active' => true,
                'sort_order' => 90
            ],
            [
                'id' => 10,
                'code' => 'automotive',
                'category_id' => null,
                'name' => 'Otomotiv',
                'emoji' => '🚗',
                'color' => 'gray',
                'description' => 'Araç satış, servis, yedek parça, rent a car',
                'keywords' => 'otomotiv, araç, servis, yedek parça, galeri, rent a car, tamirci, lastik, yıkama, ehliyet',
                'is_active' => true,
                'sort_order' => 100
            ],
            [
                'id' => 11,
                'code' => 'manufacturing',
                'category_id' => null,
                'name' => 'Endüstri & İmalat',
                'emoji' => '🏭',
                'color' => 'stone',
                'description' => 'Sanayi üretimi, imalat, fabrikasyon, makina parça',
                'keywords' => 'imalat, üretim, sanayi, fabrika, endüstri, makina, parça, metal, tekstil, kimya, elektronik',
                'is_active' => true,
                'sort_order' => 110
            ],
            [
                'id' => 12,
                'code' => 'agriculture',
                'category_id' => null,
                'name' => 'Tarım & Hayvancılık',
                'emoji' => '🌾',
                'color' => 'emerald',
                'description' => 'Tarımsal üretim, hayvancılık, gıda üretimi',
                'keywords' => 'tarım, hayvancılık, çiftlik, üretici, gıda, meyve, sebze, hayvan, balık, arıcılık, tohum',
                'is_active' => true,
                'sort_order' => 120
            ],
            [
                'id' => 13,
                'code' => 'media',
                'category_id' => null,
                'name' => 'Medya & İletişim',
                'emoji' => '📺',
                'color' => 'violet',
                'description' => 'Medya, yayıncılık, reklam, halkla ilişkiler',
                'keywords' => 'medya, televizyon, radyo, gazete, reklam, haber, yayın, basın, etkinlik, podcast',
                'is_active' => true,
                'sort_order' => 130
            ],
            [
                'id' => 14,
                'code' => 'freelance',
                'category_id' => null,
                'name' => 'Bireysel & Freelance',
                'emoji' => '👤',
                'color' => 'amber',
                'description' => 'Bireysel hizmetler, freelance, danışmanlık',
                'keywords' => 'freelance, bireysel, danışman, uzman, hizmet, yazar, çevirmen, sanatçı, kuaför, temizlik',
                'is_active' => true,
                'sort_order' => 140
            ],
            [
                'id' => 15,
                'code' => 'legal',
                'category_id' => null,
                'name' => 'Hukuk & Danışmanlık',
                'emoji' => '⚖️',
                'color' => 'slate',
                'description' => 'Avukat, hukuk bürosu, yasal danışmanlık',
                'keywords' => 'avukat, hukuk, dava, mahkeme, danışmanlık, boşanma, emlak, iş, bilişim, trafik',
                'is_active' => true,
                'sort_order' => 150
            ],
            [
                'id' => 16,
                'code' => 'environment',
                'category_id' => null,
                'name' => 'Çevre & Geri Dönüşüm',
                'emoji' => '♻️',
                'color' => 'lime',
                'description' => 'Çevre hizmetleri, geri dönüşüm, temizlik',
                'keywords' => 'çevre, geri dönüşüm, atık, temizlik, yeşil, su arıtma, enerji, organik',
                'is_active' => true,
                'sort_order' => 160
            ],
            [
                'id' => 17,
                'code' => 'metallurgy',
                'category_id' => null,
                'name' => 'Metal & Demir Çelik',
                'emoji' => '🔩',
                'color' => 'zinc',
                'description' => 'Metal işleme, demir çelik, metal ürünleri',
                'keywords' => 'metal, demir, çelik, kaynak, işleme, konstrüksiyon, hurda, kaplama, civata',
                'is_active' => true,
                'sort_order' => 170
            ],
            [
                'id' => 18,
                'code' => 'crafts_services',
                'category_id' => null,
                'name' => 'Esnaf & Sanatkarlar',
                'emoji' => '🔧',
                'color' => 'orange',
                'description' => 'Esnaf, sanatkar, teknik servis, tamir hizmetleri',
                'keywords' => 'esnaf, sanatkar, tamirci, teknisyen, klima, elektrik, su, boyacı, marangoz, kaportacı',
                'is_active' => true,
                'sort_order' => 180
            ]
        ];
        
        foreach ($mainCategories as $category) {
            AIProfileSector::create($category);
        }
    }
    
    private function createSubcategories(): void
    {
        $subcategories = [
            // Teknoloji & Bilişim Alt Kategorileri
            ['id' => 11, 'code' => 'web', 'category_id' => 1, 'name' => 'Web Tasarım & Geliştirme', 'emoji' => '🌐', 'description' => 'Website, e-ticaret, web uygulaması geliştirme'],
            ['id' => 12, 'code' => 'mobile', 'category_id' => 1, 'name' => 'Mobil Uygulama', 'emoji' => '📱', 'description' => 'iOS, Android uygulama geliştirme'],
            ['id' => 13, 'code' => 'software', 'category_id' => 1, 'name' => 'Yazılım Geliştirme', 'emoji' => '⚙️', 'description' => 'Desktop, backend, API geliştirme'],
            ['id' => 14, 'code' => 'graphic_design', 'category_id' => 1, 'name' => 'Grafik & UI/UX Tasarım', 'emoji' => '🎨', 'description' => 'Logo, arayüz, kullanıcı deneyimi tasarımı'],
            ['id' => 15, 'code' => 'digital_marketing', 'category_id' => 1, 'name' => 'Dijital Pazarlama', 'emoji' => '📊', 'description' => 'SEO, sosyal medya, online reklam'],
            ['id' => 16, 'code' => 'it_support', 'category_id' => 1, 'name' => 'IT Destek & Danışmanlık', 'emoji' => '🛠️', 'description' => 'Teknik destek, sistem yönetimi'],
            ['id' => 17, 'code' => 'data_analytics', 'category_id' => 1, 'name' => 'Veri Analizi & AI', 'emoji' => '🤖', 'description' => 'Big data, machine learning, yapay zeka'],
            ['id' => 18, 'code' => 'cybersecurity', 'category_id' => 1, 'name' => 'Siber Güvenlik', 'emoji' => '🔒', 'description' => 'Güvenlik audit, penetrasyon test'],

            // Sağlık & Tıp Alt Kategorileri
            ['id' => 21, 'code' => 'hospital', 'category_id' => 2, 'name' => 'Hastane & Klinik', 'emoji' => '🏥', 'description' => 'Genel hastane, özel klinik, poliklinik'],
            ['id' => 22, 'code' => 'dental', 'category_id' => 2, 'name' => 'Diş Hekimliği', 'emoji' => '🦷', 'description' => 'Diş tedavisi, ortodonti, implant'],
            ['id' => 23, 'code' => 'aesthetic', 'category_id' => 2, 'name' => 'Estetik & Plastik Cerrahi', 'emoji' => '💄', 'description' => 'Estetik operasyon, güzellik merkezi'],
            ['id' => 24, 'code' => 'pharmacy', 'category_id' => 2, 'name' => 'Eczane & İlaç', 'emoji' => '💊', 'description' => 'Eczane, ilaç satış, medikal malzeme'],
            ['id' => 25, 'code' => 'veterinary', 'category_id' => 2, 'name' => 'Veterinerlik', 'emoji' => '🐕', 'description' => 'Hayvan hastanesi, pet bakım'],
            ['id' => 26, 'code' => 'physiotherapy', 'category_id' => 2, 'name' => 'Fizyoterapi & Rehabilitasyon', 'emoji' => '🤲', 'description' => 'Fizik tedavi, spor yaralanmaları'],
            ['id' => 27, 'code' => 'psychology', 'category_id' => 2, 'name' => 'Psikoloji & Danışmanlık', 'emoji' => '🧠', 'description' => 'Psikolojik danışmanlık, terapi'],
            ['id' => 28, 'code' => 'lab', 'category_id' => 2, 'name' => 'Laboratuvar & Tanı', 'emoji' => '🔬', 'description' => 'Tıbbi laboratuvar, görüntüleme'],

            // Eğitim & Öğretim Alt Kategorileri
            ['id' => 31, 'code' => 'school', 'category_id' => 3, 'name' => 'Okul & Akademi', 'emoji' => '🏫', 'description' => 'Özel okul, dershane, akademi'],
            ['id' => 32, 'code' => 'language', 'category_id' => 3, 'name' => 'Dil Eğitimi', 'emoji' => '🗣️', 'description' => 'İngilizce, Almanca, dil kursu'],
            ['id' => 33, 'code' => 'tech_education', 'category_id' => 3, 'name' => 'Teknoloji Eğitimi', 'emoji' => '💻', 'description' => 'Yazılım, coding, bilgisayar kursu'],
            ['id' => 34, 'code' => 'music', 'category_id' => 3, 'name' => 'Müzik & Sanat Eğitimi', 'emoji' => '🎵', 'description' => 'Müzik dersi, enstrüman, resim'],
            ['id' => 35, 'code' => 'sports_education', 'category_id' => 3, 'name' => 'Spor Eğitimi', 'emoji' => '⚽', 'description' => 'Futbol, basketbol, yüzme dersi'],
            ['id' => 36, 'code' => 'vocational', 'category_id' => 3, 'name' => 'Meslek Edindirme', 'emoji' => '🔧', 'description' => 'Meslek kursu, sertifika programı'],
            ['id' => 37, 'code' => 'online_education', 'category_id' => 3, 'name' => 'Online Eğitim', 'emoji' => '🌐', 'description' => 'Uzaktan eğitim, e-learning platform'],
            ['id' => 38, 'code' => 'tutoring', 'category_id' => 3, 'name' => 'Özel Ders & Danışmanlık', 'emoji' => '👨‍🏫', 'description' => 'Birebir ders, eğitim danışmanlığı'],

            // Yiyecek & İçecek Alt Kategorileri
            ['id' => 41, 'code' => 'restaurant', 'category_id' => 4, 'name' => 'Restoran & Lokanta', 'emoji' => '🍽️', 'description' => 'Fine dining, casual dining, etnik mutfak'],
            ['id' => 42, 'code' => 'cafe', 'category_id' => 4, 'name' => 'Kafe & Kahvehane', 'emoji' => '☕', 'description' => 'Specialty coffee, çay evi, brunch'],
            ['id' => 43, 'code' => 'bakery', 'category_id' => 4, 'name' => 'Pastane & Fırın', 'emoji' => '🍰', 'description' => 'Artisan pastane, ekmek fırını'],
            ['id' => 44, 'code' => 'fastfood', 'category_id' => 4, 'name' => 'Fast Food', 'emoji' => '🍔', 'description' => 'Burger, pizza, döner, street food'],
            ['id' => 45, 'code' => 'healthy', 'category_id' => 4, 'name' => 'Healthy Food & Vegan', 'emoji' => '🥗', 'description' => 'Sağlıklı beslenme, organik gıda'],
            ['id' => 46, 'code' => 'bar', 'category_id' => 4, 'name' => 'Bar & Pub', 'emoji' => '🍻', 'description' => 'Cocktail bar, craft beer, wine bar'],
            ['id' => 47, 'code' => 'catering', 'category_id' => 4, 'name' => 'Catering & Toplu Yemek', 'emoji' => '🚚', 'description' => 'Etkinlik catering, delivery'],
            ['id' => 48, 'code' => 'food_production', 'category_id' => 4, 'name' => 'Gıda Üretimi', 'emoji' => '🏭', 'description' => 'Gıda üretim, toptan gıda'],

            // E-ticaret & Perakende Alt Kategorileri
            ['id' => 51, 'code' => 'fashion', 'category_id' => 5, 'name' => 'Giyim & Moda', 'emoji' => '👕', 'description' => 'Tekstil, giyim, ayakkabı, aksesuar'],
            ['id' => 52, 'code' => 'electronics', 'category_id' => 5, 'name' => 'Elektronik & Teknoloji', 'emoji' => '💻', 'description' => 'Bilgisayar, telefon, elektronik'],
            ['id' => 53, 'code' => 'home', 'category_id' => 5, 'name' => 'Ev & Yaşam', 'emoji' => '🏠', 'description' => 'Mobilya, dekorasyon, ev tekstili'],
            ['id' => 54, 'code' => 'beauty', 'category_id' => 5, 'name' => 'Güzellik & Kişisel Bakım', 'emoji' => '💄', 'description' => 'Kozmetik, parfüm, kişisel bakım'],
            ['id' => 55, 'code' => 'sports_retail', 'category_id' => 5, 'name' => 'Spor & Outdoor', 'emoji' => '⚽', 'description' => 'Spor malzemeleri, outdoor ekipman'],
            ['id' => 56, 'code' => 'books', 'category_id' => 5, 'name' => 'Kitap & Kırtasiye', 'emoji' => '📚', 'description' => 'Kitap, dergi, kırtasiye'],
            ['id' => 57, 'code' => 'marketplace', 'category_id' => 5, 'name' => 'E-ticaret Platform', 'emoji' => '🛒', 'description' => 'Online mağaza, marketplace'],
            ['id' => 58, 'code' => 'automotive_retail', 'category_id' => 5, 'name' => 'Otomotiv Ürünleri', 'emoji' => '🚗', 'description' => 'Araç yedek parça, aksesuar'],

            // İnşaat & Emlak Alt Kategorileri
            ['id' => 61, 'code' => 'residential', 'category_id' => 6, 'name' => 'Konut İnşaatı', 'emoji' => '🏠', 'description' => 'Villa, apartman, konut projeleri'],
            ['id' => 62, 'code' => 'commercial', 'category_id' => 6, 'name' => 'Ticari İnşaat', 'emoji' => '🏢', 'description' => 'Fabrika, ofis, alışveriş merkezi'],
            ['id' => 63, 'code' => 'infrastructure', 'category_id' => 6, 'name' => 'Altyapı İnşaatı', 'emoji' => '🛣️', 'description' => 'Yol, köprü, tünel, su şebekesi'],
            ['id' => 64, 'code' => 'materials', 'category_id' => 6, 'name' => 'İnşaat Malzemesi', 'emoji' => '🧱', 'description' => 'Çimento, demir, tuğla, malzeme'],
            ['id' => 65, 'code' => 'architecture', 'category_id' => 6, 'name' => 'Mimarlık & Tasarım', 'emoji' => '📐', 'description' => 'Mimari proje, iç mimarlık'],
            ['id' => 66, 'code' => 'realestate', 'category_id' => 6, 'name' => 'Gayrimenkul', 'emoji' => '🏘️', 'description' => 'Emlak danışmanlığı, satış'],
            ['id' => 67, 'code' => 'renovation', 'category_id' => 6, 'name' => 'Tadilat & Renovasyon', 'emoji' => '🔨', 'description' => 'Ev tadilat, restorasyon'],
            ['id' => 68, 'code' => 'landscape', 'category_id' => 6, 'name' => 'Peyzaj & Bahçe', 'emoji' => '🌿', 'description' => 'Bahçe tasarım, peyzaj mimarlığı'],

            // Finans & Muhasebe Alt Kategorileri
            ['id' => 71, 'code' => 'banking', 'category_id' => 7, 'name' => 'Bankacılık', 'emoji' => '🏦', 'description' => 'Banka şubesi, kredi, mevduat'],
            ['id' => 72, 'code' => 'accounting', 'category_id' => 7, 'name' => 'Muhasebe', 'emoji' => '📊', 'description' => 'Muhasebe, vergi danışmanlığı'],
            ['id' => 73, 'code' => 'insurance', 'category_id' => 7, 'name' => 'Sigorta', 'emoji' => '🛡️', 'description' => 'Hayat, kasko, dask, sağlık sigortası'],
            ['id' => 74, 'code' => 'investment', 'category_id' => 7, 'name' => 'Yatırım', 'emoji' => '📈', 'description' => 'Borsa, fon, yatırım danışmanlığı'],
            ['id' => 75, 'code' => 'crypto', 'category_id' => 7, 'name' => 'Kripto Para', 'emoji' => '₿', 'description' => 'Bitcoin, altcoin, blockchain'],
            ['id' => 76, 'code' => 'financial_consulting', 'category_id' => 7, 'name' => 'Finansal Danışmanlık', 'emoji' => '💼', 'description' => 'Mali planlama, bütçe yönetimi'],
            ['id' => 77, 'code' => 'leasing', 'category_id' => 7, 'name' => 'Leasing & Factoring', 'emoji' => '🤝', 'description' => 'Finansal kiralama, faktoring'],
            ['id' => 78, 'code' => 'forex', 'category_id' => 7, 'name' => 'Forex & Borsa', 'emoji' => '💹', 'description' => 'Döviz alım satım, borsa'],

            // Sanat & Tasarım Alt Kategorileri
            ['id' => 81, 'code' => 'graphic', 'category_id' => 8, 'name' => 'Grafik Tasarım', 'emoji' => '🖼️', 'description' => 'Logo, afiş, reklam tasarımı'],
            ['id' => 82, 'code' => 'web_design', 'category_id' => 8, 'name' => 'Web Tasarım', 'emoji' => '💻', 'description' => 'Website tasarım, UI/UX'],
            ['id' => 83, 'code' => 'photography', 'category_id' => 8, 'name' => 'Fotoğrafçılık', 'emoji' => '📸', 'description' => 'Düğün, ürün, kurumsal fotoğraf'],
            ['id' => 84, 'code' => 'interior', 'category_id' => 8, 'name' => 'İç Mimarlık', 'emoji' => '🏠', 'description' => 'İç mekan tasarım, dekorasyon'],
            ['id' => 85, 'code' => 'music_production', 'category_id' => 8, 'name' => 'Müzik Prodüksiyon', 'emoji' => '🎵', 'description' => 'Müzik prodüksiyon, ses teknisyeni'],
            ['id' => 86, 'code' => 'video', 'category_id' => 8, 'name' => 'Video Prodüksiyon', 'emoji' => '🎬', 'description' => 'Film çekim, video montaj'],
            ['id' => 87, 'code' => 'handcraft', 'category_id' => 8, 'name' => 'El Sanatları', 'emoji' => '🖐️', 'description' => 'Seramik, takı, el yapımı ürünler'],
            ['id' => 88, 'code' => 'gallery', 'category_id' => 8, 'name' => 'Sanat Galerisi', 'emoji' => '🖼️', 'description' => 'Sanat eseri, galeri, müze'],

            // Spor & Fitness Alt Kategorileri
            ['id' => 91, 'code' => 'fitness_gym', 'category_id' => 9, 'name' => 'Fitness & Spor Salonu', 'emoji' => '💪', 'description' => 'Gym, fitness merkezi, ağırlık antrenmanı'],
            ['id' => 92, 'code' => 'pilates_yoga', 'category_id' => 9, 'name' => 'Pilates & Yoga', 'emoji' => '🧘', 'description' => 'Yoga dersi, pilates, meditasyon'],
            ['id' => 93, 'code' => 'martial_arts', 'category_id' => 9, 'name' => 'Dövüş Sanatları', 'emoji' => '🥋', 'description' => 'Karate, taekwondo, boks, kick boks'],
            ['id' => 94, 'code' => 'swimming', 'category_id' => 9, 'name' => 'Su Sporları & Yüzme', 'emoji' => '🏊', 'description' => 'Yüzme dersi, su polo, aqua fitness'],
            ['id' => 95, 'code' => 'team_sports', 'category_id' => 9, 'name' => 'Takım Sporları', 'emoji' => '⚽', 'description' => 'Futbol, basketbol, voleybol kulübü'],
            ['id' => 96, 'code' => 'personal_training', 'category_id' => 9, 'name' => 'Kişisel Antrenörlük', 'emoji' => '🏃', 'description' => 'Personal trainer, özel antrenman'],
            ['id' => 97, 'code' => 'outdoor_sports', 'category_id' => 9, 'name' => 'Outdoor & Macera Sporları', 'emoji' => '🧗', 'description' => 'Dağcılık, tırmanış, kamp, doğa sporları'],
            ['id' => 98, 'code' => 'dance', 'category_id' => 9, 'name' => 'Dans & Hareket', 'emoji' => '💃', 'description' => 'Bale, modern dans, latin dans, zumba'],

            // Otomotiv Alt Kategorileri
            ['id' => 101, 'code' => 'auto_dealer', 'category_id' => 10, 'name' => 'Otomobil Galeri & Bayi', 'emoji' => '🚙', 'description' => 'Sıfır araç, ikinci el, otomobil satış'],
            ['id' => 102, 'code' => 'auto_service', 'category_id' => 10, 'name' => 'Otomotiv Servis & Tamirci', 'emoji' => '🔧', 'description' => 'Araç bakım, tamır, periyodik bakım'],
            ['id' => 103, 'code' => 'spare_parts', 'category_id' => 10, 'name' => 'Yedek Parça & Aksesuar', 'emoji' => '⚙️', 'description' => 'Orijinal yedek parça, modifiye'],
            ['id' => 104, 'code' => 'rent_car', 'category_id' => 10, 'name' => 'Rent a Car & Araç Kiralama', 'emoji' => '🚘', 'description' => 'Günlük, aylık araç kiralama'],
            ['id' => 105, 'code' => 'tire_rim', 'category_id' => 10, 'name' => 'Lastik & Jant', 'emoji' => '🛞', 'description' => 'Lastik satış, balans, jant'],
            ['id' => 106, 'code' => 'car_wash', 'category_id' => 10, 'name' => 'Oto Yıkama & Detailing', 'emoji' => '🧽', 'description' => 'Araç yıkama, cilalama, detailing'],
            ['id' => 107, 'code' => 'car_rescue', 'category_id' => 10, 'name' => 'Kurtarma & Çekici', 'emoji' => '🚛', 'description' => 'Araç kurtarma, çekici, yol yardım'],
            ['id' => 108, 'code' => 'driving_school', 'category_id' => 10, 'name' => 'Sürücü Kursu & Ehliyet', 'emoji' => '🪪', 'description' => 'Direksiyon eğitimi, ehliyet kursu'],

            // Endüstri & İmalat Alt Kategorileri  
            ['id' => 111, 'code' => 'machine_parts', 'category_id' => 11, 'name' => 'Makina & Parça İmalatı', 'emoji' => '⚙️', 'description' => 'CNC torna, freze, parça üretim'],
            ['id' => 112, 'code' => 'metal_processing', 'category_id' => 11, 'name' => 'Metal Ürünleri & İşleme', 'emoji' => '🔩', 'description' => 'Çelik işleme, metal parça üretim'],
            ['id' => 113, 'code' => 'textile_manufacturing', 'category_id' => 11, 'name' => 'Tekstil & Konfeksiyon', 'emoji' => '🧵', 'description' => 'Kumaş, giyim, ev tekstili üretim'],
            ['id' => 114, 'code' => 'food_manufacturing', 'category_id' => 11, 'name' => 'Gıda & İçecek Üretimi', 'emoji' => '🏭', 'description' => 'Gıda işleme, ambalaj, içecek üretimi'],
            ['id' => 115, 'code' => 'chemical', 'category_id' => 11, 'name' => 'Kimya & Petrokimya', 'emoji' => '🧪', 'description' => 'Kimyasal üretim, plastik, deterjan'],
            ['id' => 116, 'code' => 'electronics_manufacturing', 'category_id' => 11, 'name' => 'Elektronik & Elektrik', 'emoji' => '⚡', 'description' => 'Elektronik kart, kablo, elektrik malzeme'],
            ['id' => 117, 'code' => 'construction_materials', 'category_id' => 11, 'name' => 'Cam, Çimento & İnşaat Malzemesi', 'emoji' => '🏠', 'description' => 'Cam üretim, çimento, tuğla, kiremit'],
            ['id' => 118, 'code' => 'automotive_manufacturing', 'category_id' => 11, 'name' => 'Otomotiv Yan Sanayi', 'emoji' => '🚗', 'description' => 'Araç parça üretimi, OEM, yedek parça'],

            // Tarım & Hayvancılık Alt Kategorileri
            ['id' => 121, 'code' => 'crop_production', 'category_id' => 12, 'name' => 'Bitkisel Üretim & Tarım', 'emoji' => '🌱', 'description' => 'Meyve, sebze, hububat, endüstri bitkileri'],
            ['id' => 122, 'code' => 'livestock', 'category_id' => 12, 'name' => 'Hayvancılık & Çiftlik', 'emoji' => '🐄', 'description' => 'Büyükbaş, küçükbaş, kümes hayvanları'],
            ['id' => 123, 'code' => 'fishery', 'category_id' => 12, 'name' => 'Su Ürünleri & Balıkçılık', 'emoji' => '🐟', 'description' => 'Balık üretimi, su ürünleri, akvakültür'],
            ['id' => 124, 'code' => 'agricultural_machinery', 'category_id' => 12, 'name' => 'Tarım Makinaları & Ekipman', 'emoji' => '🚜', 'description' => 'Traktör, tarım aleti, sulama sistemleri'],
            ['id' => 125, 'code' => 'fertilizer', 'category_id' => 12, 'name' => 'Gübre & Tarım İlaçları', 'emoji' => '🧪', 'description' => 'Organik gübre, kimyasal gübre, zirai ilaç'],
            ['id' => 126, 'code' => 'seeds', 'category_id' => 12, 'name' => 'Tohum & Fide Üretimi', 'emoji' => '🌰', 'description' => 'Sertifikalı tohum, fide, fidan üretimi'],
            ['id' => 127, 'code' => 'beekeeping', 'category_id' => 12, 'name' => 'Arıcılık & Bal Üretimi', 'emoji' => '🐝', 'description' => 'Bal, polen, propolis, arı ürünleri'],
            ['id' => 128, 'code' => 'agricultural_consulting', 'category_id' => 12, 'name' => 'Tarımsal Danışmanlık', 'emoji' => '👨‍🌾', 'description' => 'Tarım tekniği, verim artırma, eğitim'],

            // Medya & İletişim Alt Kategorileri
            ['id' => 131, 'code' => 'tv_radio', 'category_id' => 13, 'name' => 'Televizyon & Radyo', 'emoji' => '📻', 'description' => 'TV kanalı, radyo istasyonu, yayıncılık'],
            ['id' => 132, 'code' => 'newspaper', 'category_id' => 13, 'name' => 'Gazete & Dergi', 'emoji' => '📰', 'description' => 'Yerel gazete, dergi, basılı yayın'],
            ['id' => 133, 'code' => 'digital_media', 'category_id' => 13, 'name' => 'Dijital Medya & Sosyal Medya', 'emoji' => '📱', 'description' => 'Haber sitesi, sosyal medya yönetimi'],
            ['id' => 134, 'code' => 'advertising', 'category_id' => 13, 'name' => 'Reklam Ajansı & Pazarlama', 'emoji' => '📢', 'description' => 'Reklam kampanya, marka yönetimi'],
            ['id' => 135, 'code' => 'public_relations', 'category_id' => 13, 'name' => 'Halkla İlişkiler & PR', 'emoji' => '🤝', 'description' => 'Kurumsal iletişim, basın sözcülüğü'],
            ['id' => 136, 'code' => 'event_organization', 'category_id' => 13, 'name' => 'Etkinlik & Organizasyon', 'emoji' => '🎪', 'description' => 'Konser, festival, fuar organizasyonu'],
            ['id' => 137, 'code' => 'content_creation', 'category_id' => 13, 'name' => 'İçerik Üretimi & Podcasting', 'emoji' => '🎙️', 'description' => 'Podcast, YouTube, blog içeriği'],
            ['id' => 138, 'code' => 'printing', 'category_id' => 13, 'name' => 'Basım & Matbaa', 'emoji' => '🖨️', 'description' => 'Ofset basım, dijital baskı, matbaacılık'],

            // Bireysel & Freelance Alt Kategorileri
            ['id' => 141, 'code' => 'consultant', 'category_id' => 14, 'name' => 'Danışman & Uzman', 'emoji' => '🧠', 'description' => 'Serbest danışman, uzman, mentor'],
            ['id' => 142, 'code' => 'writer', 'category_id' => 14, 'name' => 'Yazar & İçerik Üretici', 'emoji' => '✍️', 'description' => 'Copywriter, blog yazarı, editör'],
            ['id' => 143, 'code' => 'translator', 'category_id' => 14, 'name' => 'Çevirmen & Dil Uzmanı', 'emoji' => '🌍', 'description' => 'Tercüman, sözlü çeviri, çeviri'],
            ['id' => 144, 'code' => 'artist', 'category_id' => 14, 'name' => 'Sanatçı & Portfolyo', 'emoji' => '🎨', 'description' => 'Ressam, heykeltıraş, sanat eseri'],
            ['id' => 145, 'code' => 'musician', 'category_id' => 14, 'name' => 'Müzisyen & Ses Sanatçısı', 'emoji' => '🎵', 'description' => 'Müzik öğretmeni, icracı, beste'],
            ['id' => 146, 'code' => 'beauty_care', 'category_id' => 14, 'name' => 'Kişisel Bakım & Güzellik', 'emoji' => '💅', 'description' => 'Kuaför, estetisyen, masöz'],
            ['id' => 147, 'code' => 'home_cleaning', 'category_id' => 14, 'name' => 'Ev Temizlik & Bakım', 'emoji' => '🧹', 'description' => 'Temizlik, bahçıvan, ev bakım'],
            ['id' => 148, 'code' => 'transportation', 'category_id' => 14, 'name' => 'Ulaşım & Şoförlük', 'emoji' => '🚗', 'description' => 'Taksi, şoför, kurye, nakliye'],

            // Hukuk & Danışmanlık Alt Kategorileri
            ['id' => 151, 'code' => 'law_office', 'category_id' => 15, 'name' => 'Avukatlık & Hukuk Bürosu', 'emoji' => '⚖️', 'description' => 'Genel hukuk, dava takibi, hukuki danışmanlık'],
            ['id' => 152, 'code' => 'corporate_law', 'category_id' => 15, 'name' => 'Kurumsal Hukuk & Ticaret Hukuku', 'emoji' => '🏢', 'description' => 'Şirket hukuku, sözleşme, ticari dava'],
            ['id' => 153, 'code' => 'real_estate_law', 'category_id' => 15, 'name' => 'Emlak Hukuku & Gayrimenkul', 'emoji' => '🏠', 'description' => 'Tapu işlemleri, kira hukuku, inşaat'],
            ['id' => 154, 'code' => 'family_law', 'category_id' => 15, 'name' => 'Aile Hukuku & Boşanma', 'emoji' => '👨‍👩‍👧‍👦', 'description' => 'Boşanma, velayet, nafaka, miras'],
            ['id' => 155, 'code' => 'labor_law', 'category_id' => 15, 'name' => 'İş Hukuku & İşçi Hakları', 'emoji' => '⚡', 'description' => 'İş sözleşmesi, işçi hakları, tazminat'],
            ['id' => 156, 'code' => 'cyber_law', 'category_id' => 15, 'name' => 'Bilişim Hukuku & Kişisel Veri', 'emoji' => '💻', 'description' => 'KVKK, cyber hukuk, e-ticaret hukuku'],
            ['id' => 157, 'code' => 'traffic_law', 'category_id' => 15, 'name' => 'Trafik Hukuku & Sigorta', 'emoji' => '🚗', 'description' => 'Trafik kazası, sigorta tazminatı'],
            ['id' => 158, 'code' => 'administrative_law', 'category_id' => 15, 'name' => 'İdare Hukuku & Kamu', 'emoji' => '🏛️', 'description' => 'İdari dava, belediye, ihale, vergi'],

            // Çevre & Geri Dönüşüm Alt Kategorileri
            ['id' => 161, 'code' => 'recycling', 'category_id' => 16, 'name' => 'Geri Dönüşüm & Atık Yönetimi', 'emoji' => '♻️', 'description' => 'Plastik, kağıt, cam geri dönüşüm'],
            ['id' => 162, 'code' => 'environmental_consulting', 'category_id' => 16, 'name' => 'Çevre Danışmanlığı & Sürdürülebilirlik', 'emoji' => '🌱', 'description' => 'ISO 14001, sürdürülebilirlik raporu'],
            ['id' => 163, 'code' => 'cleaning_services', 'category_id' => 16, 'name' => 'Temizlik & Hijyen Hizmetleri', 'emoji' => '🧹', 'description' => 'Endüstriyel temizlik, ofis hijyeni'],
            ['id' => 164, 'code' => 'landscaping', 'category_id' => 16, 'name' => 'Peyzaj & Bahçıvanlık', 'emoji' => '🌿', 'description' => 'Bahçe bakımı, ağaçlandırma, yeşil alan'],
            ['id' => 165, 'code' => 'water_treatment', 'category_id' => 16, 'name' => 'Su Arıtma & Çevre Teknolojileri', 'emoji' => '💧', 'description' => 'Su arıtma, hava filtreleme teknoloji'],
            ['id' => 166, 'code' => 'renewable_energy', 'category_id' => 16, 'name' => 'Yenilenebilir Enerji', 'emoji' => '☀️', 'description' => 'Güneş, rüzgar, hidrolik enerji'],
            ['id' => 167, 'code' => 'industrial_environment', 'category_id' => 16, 'name' => 'Endüstriyel Çevre Çözümleri', 'emoji' => '🏭', 'description' => 'Emisyon kontrolü, atık su arıtma'],
            ['id' => 168, 'code' => 'organic_agriculture', 'category_id' => 16, 'name' => 'Organik Tarım & Ekoloji', 'emoji' => '🌾', 'description' => 'Organik ürün, permakültür, ekoloji'],

            // Metal & Demir Çelik Alt Kategorileri
            ['id' => 171, 'code' => 'steel_production', 'category_id' => 17, 'name' => 'Demir Çelik Üretimi', 'emoji' => '🏭', 'description' => 'Ham çelik, profil çelik, sac üretimi'],
            ['id' => 172, 'code' => 'metal_machining', 'category_id' => 17, 'name' => 'Metal İşleme & Makina Parçaları', 'emoji' => '⚙️', 'description' => 'CNC işleme, torna, freze'],
            ['id' => 173, 'code' => 'steel_construction', 'category_id' => 17, 'name' => 'Metal Konstrüksiyon & Çelik Yapı', 'emoji' => '🏗️', 'description' => 'Çelik konstrüksiyon, hangar, fabrika'],
            ['id' => 174, 'code' => 'welding', 'category_id' => 17, 'name' => 'Kaynak & Metal Birleştirme', 'emoji' => '⚡', 'description' => 'Argon kaynak, elektrik kaynak, lehim'],
            ['id' => 175, 'code' => 'metal_coating', 'category_id' => 17, 'name' => 'Metal Kaplama & Yüzey İşlemi', 'emoji' => '🎨', 'description' => 'Galvaniz, boyama, krom kaplama'],
            ['id' => 176, 'code' => 'fasteners', 'category_id' => 17, 'name' => 'Bağlantı Elemanları', 'emoji' => '🔩', 'description' => 'Civata, somun, vida, metal aksesuar'],
            ['id' => 177, 'code' => 'metal_packaging', 'category_id' => 17, 'name' => 'Metal Ambalaj & Teneke', 'emoji' => '📦', 'description' => 'Konserve kutusu, metal ambalaj'],
            ['id' => 178, 'code' => 'metal_scrap', 'category_id' => 17, 'name' => 'Metal Hurda & Geri Dönüşüm', 'emoji' => '🔧', 'description' => 'Demir hurda, metal geri dönüşüm'],

            // Esnaf & Sanatkarlar Alt Kategorileri
            ['id' => 181, 'code' => 'hvac_services', 'category_id' => 18, 'name' => 'Klimacı & HVAC Servisleri', 'emoji' => '❄️', 'description' => 'Klima montaj, servis, havalandırma sistemi'],
            ['id' => 182, 'code' => 'electrician', 'category_id' => 18, 'name' => 'Elektrikçi & Elektrik Servisi', 'emoji' => '⚡', 'description' => 'Elektrik tesisatı, pano, aydınlatma'],
            ['id' => 183, 'code' => 'plumber', 'category_id' => 18, 'name' => 'Tesisatçı & Su Tesisatı', 'emoji' => '🚰', 'description' => 'Su tesisatı, kalorifer, doğalgaz tesisatı'],
            ['id' => 184, 'code' => 'painter', 'category_id' => 18, 'name' => 'Boyacı & Badanacı', 'emoji' => '🎨', 'description' => 'İç dış boyama, dekoratif boyama'],
            ['id' => 185, 'code' => 'carpenter', 'category_id' => 18, 'name' => 'Marangoz & Mobilyacı', 'emoji' => '🪚', 'description' => 'Dolap, kapı, pencere, mobilya yapımı'],
            ['id' => 186, 'code' => 'appliance_repair', 'category_id' => 18, 'name' => 'Beyaz Eşya Tamircisi', 'emoji' => '🔧', 'description' => 'Buzdolabı, çamaşır makinesi, fırın tamiri'],
            ['id' => 187, 'code' => 'locksmith', 'category_id' => 18, 'name' => 'Anahtarcı & Çilingir', 'emoji' => '🔑', 'description' => 'Kilit değişimi, kasa açma, anahtar'],
            ['id' => 188, 'code' => 'upholsterer', 'category_id' => 18, 'name' => 'Döşemeci & Koltuk Tamircisi', 'emoji' => '🛋️', 'description' => 'Koltuk döşeme, perde, yatak tamiri']
        ];
        
        foreach ($subcategories as $subcategory) {
            AIProfileSector::create([
                'code' => $subcategory['code'],
                'category_id' => $subcategory['category_id'],
                'name' => $subcategory['name'],
                'emoji' => $subcategory['emoji'],
                'description' => $subcategory['description'],
                'is_active' => true,
                'sort_order' => ($subcategory['id'] % 10) * 10
            ]);
        }
    }
}