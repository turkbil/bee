<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorsCategorizedSeeder extends Seeder
{
    /**
     * AI PROFİL SEKTÖR VERİLERİ - KATEGORİZE EDİLMİŞ YAPIDA
     * 
     * Ana kategoriler (category_id = null) ve alt kategoriler (sektörler)
     * organize edilmiş yapıda. ID'ler elle belirlenmiş karışıklık önlenmesi için.
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Sectors - Kategorize Edilmiş Yapı Yükleniyor...\n";
        
        // Önce mevcut verileri temizle
        AIProfileSector::truncate();
        
        $this->createMainCategories();
        $this->createSubcategories();
        
        echo "\n🎯 Kategorizasyon tamamlandı!\n";
    }
    
    /**
     * Ana kategorileri oluştur (category_id = null)
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
                'keywords' => 'teknoloji, bilişim, yazılım, IT, bilgisayar, internet, web, mobil, yazılım geliştirme',
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
                'keywords' => 'sağlık, tıp, hastane, doktor, klinik, sağlık hizmetleri, tedavi, hasta bakımı',
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
                'keywords' => 'eğitim, okul, öğretim, kurs, özel ders, online eğitim, öğretmen, akademi',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 4,
                'code' => 'business_main',
                'category_id' => null,
                'name' => 'İş & Finans',
                'emoji' => '💼',
                'color' => 'purple',
                'description' => 'Danışmanlık, finans, bankacılık ve profesyonel hizmetler',
                'keywords' => 'danışmanlık, finans, banka, muhasebe, hukuk, iş geliştirme, yatırım',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 5,
                'code' => 'commerce_main',
                'category_id' => null,
                'name' => 'Ticaret & Satış',
                'emoji' => '🛒',
                'color' => 'orange',
                'description' => 'E-ticaret, perakende, mağaza ve satış hizmetleri',
                'keywords' => 'e-ticaret, mağaza, perakende, satış, market, alışveriş, online satış',
                'is_active' => true,
                'sort_order' => 50
            ],
            [
                'id' => 6,
                'code' => 'construction_main',
                'category_id' => null,
                'name' => 'İnşaat & Emlak',
                'emoji' => '🏗️',
                'color' => 'brown',
                'description' => 'İnşaat, mimarlık, emlak ve yapı sektörü',
                'keywords' => 'inşaat, emlak, mimarlık, yapı, konut, gayrimenkul, ev, bina',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 7,
                'code' => 'food_main',
                'category_id' => null,
                'name' => 'Gıda & Yiyecek',
                'emoji' => '🍽️',
                'color' => 'red',
                'description' => 'Gıda üretimi, restoran, kafe ve yemek hizmetleri',
                'keywords' => 'gıda, restoran, kafe, yemek, catering, gıda üretimi, içecek',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 8,
                'code' => 'beauty_main',
                'category_id' => null,
                'name' => 'Güzellik & Bakım',
                'emoji' => '💄',
                'color' => 'pink',
                'description' => 'Güzellik, kişisel bakım, kuaförlük ve estetik hizmetler',
                'keywords' => 'güzellik, kuaför, estetik, spa, kişisel bakım, makyaj, cilt bakımı',
                'is_active' => true,
                'sort_order' => 80
            ],
            [
                'id' => 9,
                'code' => 'transportation_main',
                'category_id' => null,
                'name' => 'Ulaşım & Lojistik',
                'emoji' => '🚛',
                'color' => 'indigo',
                'description' => 'Ulaştırma, lojistik, kargo ve nakliye hizmetleri',
                'keywords' => 'ulaşım, lojistik, kargo, nakliye, taşımacılık, araç, otobüs',
                'is_active' => true,
                'sort_order' => 90
            ],
            [
                'id' => 10,
                'code' => 'entertainment_main',
                'category_id' => null,
                'name' => 'Eğlence & Medya',
                'emoji' => '🎬',
                'color' => 'teal',
                'description' => 'Medya, eğlence, organizasyon ve yaratıcı hizmetler',
                'keywords' => 'eğlence, medya, organizasyon, etkinlik, müzik, sanat, yaratıcı',
                'is_active' => true,
                'sort_order' => 100
            ]
        ];
        
        foreach ($mainCategories as $category) {
            AIProfileSector::create($category);
            echo "✅ Ana Kategori: {$category['name']} - ID: {$category['id']}\n";
        }
    }
    
    /**
     * Alt kategorileri (sektörleri) oluştur
     */
    private function createSubcategories(): void
    {
        $subcategories = [
            // TEKNOLOJI & BİLİŞİM (Ana Kategori ID: 1)
            [
                'id' => 101,
                'code' => 'technology',
                'category_id' => 1,
                'name' => 'Yazılım Geliştirme',
                'icon' => 'fas fa-laptop-code',
                'description' => 'Web, mobil, desktop yazılım geliştirme hizmetleri',
                'keywords' => 'yazılım, software, development, web, mobil, uygulama',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 102,
                'code' => 'web-design',
                'category_id' => 1,
                'name' => 'Web Tasarım & Dijital Ajans',
                'icon' => 'fas fa-paint-brush',
                'description' => 'Website tasarımı, dijital pazarlama ve SEO hizmetleri',
                'keywords' => 'web tasarım, dijital ajans, SEO, website, pazarlama',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 103,
                'code' => 'it-consultancy',
                'category_id' => 1,
                'name' => 'BT Danışmanlığı',
                'icon' => 'fas fa-server',
                'description' => 'IT altyapı, sistem kurulumu ve teknik danışmanlık',
                'keywords' => 'IT, danışmanlık, altyapı, sistem, network, server',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 104,
                'code' => 'cybersecurity',
                'category_id' => 1,
                'name' => 'Siber Güvenlik',
                'icon' => 'fas fa-shield-alt',
                'description' => 'Sistem güvenliği, veri koruma ve siber tehdit önleme',
                'keywords' => 'güvenlik, siber, koruma, firewall, security',
                'is_active' => true,
                'sort_order' => 40
            ],

            // SAĞLIK & TIP (Ana Kategori ID: 2)
            [
                'id' => 201,
                'code' => 'health',
                'category_id' => 2,
                'name' => 'Hastane & Sağlık Merkezi',
                'icon' => 'fas fa-hospital',
                'description' => 'Genel hastane, özel hastane, sağlık kompleksi',
                'keywords' => 'hastane, sağlık merkezi, tıp merkezi, acil servis',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 202,
                'code' => 'dental',
                'category_id' => 2,
                'name' => 'Diş Hekimliği',
                'icon' => 'fas fa-tooth',
                'description' => 'Diş tedavisi, implant, ortodonti, ağız cerrahisi',
                'keywords' => 'diş, dental, implant, ortodonti, ağız',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 203,
                'code' => 'pharmacy',
                'category_id' => 2,
                'name' => 'Eczane & İlaç',
                'icon' => 'fas fa-pills',
                'description' => 'Reçeteli ilaç, OTC ürünler, sağlık malzemeleri',
                'keywords' => 'eczane, ilaç, farmasötik, reçete, vitamin',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 204,
                'code' => 'aesthetic',
                'category_id' => 2,
                'name' => 'Estetik & Plastik Cerrahi',
                'icon' => 'fas fa-user-md',
                'description' => 'Estetik operasyonlar, botox, dolgu, güzellik',
                'keywords' => 'estetik, plastik cerrahi, botox, dolgu, güzellik',
                'is_active' => true,
                'sort_order' => 40
            ],

            // EĞİTİM & ÖĞRETİM (Ana Kategori ID: 3)
            [
                'id' => 301,
                'code' => 'education',
                'category_id' => 3,
                'name' => 'Okul & Eğitim Kurumları',
                'icon' => 'fas fa-school',
                'description' => 'Anaokulu, ilkokul, ortaokul, lise, üniversite',
                'keywords' => 'okul, eğitim, anaokulu, ilkokul, lise, üniversite',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 302,
                'code' => 'private-tutoring',
                'category_id' => 3,
                'name' => 'Özel Ders & Koçluk',
                'icon' => 'fas fa-chalkboard-teacher',
                'description' => 'Birebir özel ders, grup dersleri, akademik koçluk',
                'keywords' => 'özel ders, koçluk, mentoring, tutor, öğretmen',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 303,
                'code' => 'online-education',
                'category_id' => 3,
                'name' => 'Online Eğitim',
                'icon' => 'fas fa-laptop',
                'description' => 'E-learning, uzaktan eğitim, video dersler',
                'keywords' => 'online eğitim, e-learning, uzaktan eğitim, video ders',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 304,
                'code' => 'language-training',
                'category_id' => 3,
                'name' => 'Dil Eğitimi',
                'icon' => 'fas fa-language',
                'description' => 'İngilizce, Almanca, Fransızca, çeviri hizmetleri',
                'keywords' => 'dil, İngilizce, almanca, fransızca, çeviri',
                'is_active' => true,
                'sort_order' => 40
            ],

            // İŞ & FİNANS (Ana Kategori ID: 4)
            [
                'id' => 401,
                'code' => 'consultancy',
                'category_id' => 4,
                'name' => 'İş Danışmanlığı',
                'icon' => 'fas fa-briefcase',
                'description' => 'İş danışmanlığı, hukuk, muhasebe ve profesyonel hizmetler',
                'keywords' => 'danışmanlık, hukuk, muhasebe, profesyonel hizmetler',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 402,
                'code' => 'finance',
                'category_id' => 4,
                'name' => 'Finans & Bankacılık',
                'icon' => 'fas fa-chart-line',
                'description' => 'Banka, sigorta, yatırım ve finansal hizmetler',
                'keywords' => 'finans, banka, sigorta, yatırım, finansal hizmetler',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 403,
                'code' => 'accounting',
                'category_id' => 4,
                'name' => 'Muhasebe & Vergi',
                'icon' => 'fas fa-calculator',
                'description' => 'Muhasebe hizmetleri, vergi danışmanlığı, defterdarlık',
                'keywords' => 'muhasebe, vergi, defterdarlık, mali müşavir',
                'is_active' => true,
                'sort_order' => 30
            ],

            // TİCARET & SATIŞ (Ana Kategori ID: 5)
            [
                'id' => 501,
                'code' => 'e-commerce',
                'category_id' => 5,
                'name' => 'E-Ticaret',
                'icon' => 'fas fa-shopping-cart',
                'description' => 'Online satış, mağaza yönetimi ve e-ticaret çözümleri',
                'keywords' => 'e-ticaret, online satış, mağaza yönetimi',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 502,
                'code' => 'retail',
                'category_id' => 5,
                'name' => 'Perakende & Mağaza',
                'icon' => 'fas fa-shopping-bag',
                'description' => 'Mağaza, market, butik ve perakende satış',
                'keywords' => 'perakende, mağaza, market, butik, satış',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 503,
                'code' => 'automotive',
                'category_id' => 5,
                'name' => 'Otomotiv',
                'icon' => 'fas fa-car',
                'description' => 'Oto galeri, servis, yedek parça ve otomotiv hizmetleri',
                'keywords' => 'otomotiv, oto galeri, servis, yedek parça',
                'is_active' => true,
                'sort_order' => 30
            ],

            // İNŞAAT & EMLAK (Ana Kategori ID: 6)
            [
                'id' => 601,
                'code' => 'construction',
                'category_id' => 6,
                'name' => 'İnşaat & Yapı',
                'icon' => 'fas fa-hammer',
                'description' => 'İnşaat, yapı malzemeleri, mimarlık ve mühendislik',
                'keywords' => 'inşaat, yapı, mimarlık, mühendislik, yapı malzemeleri',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 602,
                'code' => 'real-estate',
                'category_id' => 6,
                'name' => 'Emlak & Gayrimenkul',
                'icon' => 'fas fa-building',
                'description' => 'Emlak, gayrimenkul danışmanlığı, kiralama ve değerleme',
                'keywords' => 'emlak, gayrimenkul, kiralama, değerleme',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 603,
                'code' => 'home-garden',
                'category_id' => 6,
                'name' => 'Ev & Bahçe',
                'icon' => 'fas fa-home',
                'description' => 'Ev dekorasyonu, bahçe düzenleme ve ev geliştirme',
                'keywords' => 'ev, bahçe, dekorasyon, peyzaj, tadilat',
                'is_active' => true,
                'sort_order' => 30
            ],

            // GIDA & YİYECEK (Ana Kategori ID: 7)
            [
                'id' => 701,
                'code' => 'restaurant',
                'category_id' => 7,
                'name' => 'Restoran & Kafe',
                'icon' => 'fas fa-utensils',
                'description' => 'Restoran, kafe, catering ve yemek hizmetleri',
                'keywords' => 'restoran, kafe, catering, yemek hizmetleri',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 702,
                'code' => 'food-beverage',
                'category_id' => 7,
                'name' => 'Gıda Üretimi',
                'icon' => 'fas fa-industry',
                'description' => 'Gıda üretimi, içecek, toptan gıda ve gıda teknolojisi',
                'keywords' => 'gıda üretimi, içecek, toptan gıda, gıda teknolojisi',
                'is_active' => true,
                'sort_order' => 20
            ],

            // GÜZELLİK & BAKIM (Ana Kategori ID: 8)
            [
                'id' => 801,
                'code' => 'beauty-personal-care',
                'category_id' => 8,
                'name' => 'Kuaför & Güzellik',
                'icon' => 'fas fa-spa',
                'description' => 'Kuaför, güzellik salonu, spa ve kişisel bakım hizmetleri',
                'keywords' => 'kuaför, güzellik salonu, spa, kişisel bakım',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 802,
                'code' => 'sports-fitness',
                'category_id' => 8,
                'name' => 'Spor & Fitness',
                'icon' => 'fas fa-dumbbell',
                'description' => 'Spor salonu, fitness, spor kulübü ve spor ekipmanları',
                'keywords' => 'spor, fitness, spor salonu, spor kulübü',
                'is_active' => true,
                'sort_order' => 20
            ],

            // ULAŞIM & LOJİSTİK (Ana Kategori ID: 9)
            [
                'id' => 901,
                'code' => 'logistics',
                'category_id' => 9,
                'name' => 'Lojistik & Kargo',
                'icon' => 'fas fa-truck',
                'description' => 'Kargo, nakliye, depolama ve lojistik hizmetleri',
                'keywords' => 'lojistik, kargo, nakliye, depolama',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 902,
                'code' => 'transportation',
                'category_id' => 9,
                'name' => 'Ulaştırma & Taşımacılık',
                'icon' => 'fas fa-bus',
                'description' => 'Otobüs, taksi, şehir içi ulaşım ve yolcu taşımacılığı',
                'keywords' => 'ulaştırma, taşımacılık, otobüs, taksi',
                'is_active' => true,
                'sort_order' => 20
            ],

            // EĞLENCE & MEDYA (Ana Kategori ID: 10)
            [
                'id' => 1001,
                'code' => 'entertainment',
                'category_id' => 10,
                'name' => 'Eğlence & Medya',
                'icon' => 'fas fa-tv',
                'description' => 'Sinema, müzik, oyun, medya ve eğlence sektörü',
                'keywords' => 'eğlence, medya, sinema, müzik, oyun',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'id' => 1002,
                'code' => 'events',
                'category_id' => 10,
                'name' => 'Etkinlik & Organizasyon',
                'icon' => 'fas fa-calendar-alt',
                'description' => 'Düğün, konferans, etkinlik planlama ve organizasyon',
                'keywords' => 'etkinlik, organizasyon, düğün, konferans',
                'is_active' => true,
                'sort_order' => 20
            ],
            [
                'id' => 1003,
                'code' => 'photography',
                'category_id' => 10,
                'name' => 'Fotoğrafçılık',
                'icon' => 'fas fa-camera',
                'description' => 'Fotoğraf çekimi, video prodüksiyonu ve görsel hizmetler',
                'keywords' => 'fotoğrafçılık, video prodüksiyonu, görsel hizmetler',
                'is_active' => true,
                'sort_order' => 30
            ],
            [
                'id' => 1004,
                'code' => 'arts-crafts',
                'category_id' => 10,
                'name' => 'Sanat & El Sanatları',
                'icon' => 'fas fa-palette',
                'description' => 'Sanat eserleri, el sanatları, galeri ve yaratıcı hizmetler',
                'keywords' => 'sanat, el sanatları, galeri, yaratıcı hizmetler',
                'is_active' => true,
                'sort_order' => 40
            ],
            [
                'id' => 1005,
                'code' => 'music',
                'category_id' => 10,
                'name' => 'Müzik & Ses',
                'icon' => 'fas fa-music',
                'description' => 'Müzik prodüksiyonu, ses teknolojileri ve müzik eğitimi',
                'keywords' => 'müzik, ses teknolojileri, müzik eğitimi',
                'is_active' => true,
                'sort_order' => 50
            ],

            // DİĞER SEKTÖRLER - Kategorize edilemeyen sektörler
            [
                'id' => 1101,
                'code' => 'tourism',
                'category_id' => 10,
                'name' => 'Turizm & Seyahat',
                'icon' => 'fas fa-plane',
                'description' => 'Otel, tur, seyahat acentesi ve turizm hizmetleri',
                'keywords' => 'turizm, seyahat, otel, tur, seyahat acentesi',
                'is_active' => true,
                'sort_order' => 60
            ],
            [
                'id' => 1102,
                'code' => 'agriculture',
                'category_id' => 10,
                'name' => 'Tarım & Hayvancılık',
                'icon' => 'fas fa-seedling',
                'description' => 'Tarım, hayvancılık, gıda üretimi ve tarımsal teknolojiler',
                'keywords' => 'tarım, hayvancılık, gıda üretimi, tarımsal teknolojiler',
                'is_active' => true,
                'sort_order' => 70
            ],
            [
                'id' => 1103,
                'code' => 'other',
                'category_id' => 10,
                'name' => 'Diğer Sektörler',
                'icon' => 'fas fa-ellipsis-h',
                'description' => 'Diğer sektörler ve özel çözümler',
                'keywords' => 'diğer, özel çözümler, çeşitli',
                'is_active' => true,
                'sort_order' => 999
            ]
        ];
        
        $categoryGroups = [
            1 => 'Teknoloji & Bilişim',
            2 => 'Sağlık & Tıp', 
            3 => 'Eğitim & Öğretim',
            4 => 'İş & Finans',
            5 => 'Ticaret & Satış',
            6 => 'İnşaat & Emlak',
            7 => 'Gıda & Yiyecek',
            8 => 'Güzellik & Bakım',
            9 => 'Ulaşım & Lojistik',
            10 => 'Eğlence & Medya'
        ];
        
        foreach ($subcategories as $subcategory) {
            AIProfileSector::create($subcategory);
            $categoryName = $categoryGroups[$subcategory['category_id']] ?? 'Bilinmeyen';
            echo "   → {$subcategory['name']} ({$categoryName}) - ID: {$subcategory['id']}\n";
        }
        
        echo "\n📊 Toplam: " . count($subcategories) . " alt kategori eklendi\n";
    }
}