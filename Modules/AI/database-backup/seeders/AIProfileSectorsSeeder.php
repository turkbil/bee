<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorsSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        // Alfabetik sıralama - "Diğer Sektörler" her zaman en sonda
        $sectors = [
            [
                'code' => 'advertising',
                'name' => 'Reklamcılık & PR',
                'icon' => 'fas fa-bullhorn',
                'description' => 'Reklam ajansı, halkla ilişkiler ve pazarlama hizmetleri',
                'sort_order' => 1
            ],
            [
                'code' => 'agriculture',
                'name' => 'Tarım & Hayvancılık',
                'icon' => 'fas fa-seedling',
                'description' => 'Tarım, hayvancılık, gıda üretimi ve tarımsal teknolojiler',
                'sort_order' => 2
            ],
            [
                'code' => 'arts-crafts',
                'name' => 'Sanat & El Sanatları',
                'icon' => 'fas fa-palette',
                'description' => 'Sanat eserleri, el sanatları, galeri ve yaratıcı hizmetler',
                'sort_order' => 3
            ],
            [
                'code' => 'automotive',
                'name' => 'Otomotiv',
                'icon' => 'fas fa-car',
                'description' => 'Oto galeri, servis, yedek parça ve otomotiv hizmetleri',
                'sort_order' => 4
            ],
            [
                'code' => 'beauty-personal-care',
                'name' => 'Güzellik & Kişisel Bakım',
                'icon' => 'fas fa-spa',
                'description' => 'Kuaför, güzellik salonu, spa ve kişisel bakım hizmetleri',
                'sort_order' => 5
            ],
            [
                'code' => 'books-publishing',
                'name' => 'Kitap & Yayıncılık',
                'icon' => 'fas fa-book',
                'description' => 'Kitap, dergi, yayınevi ve matbaacılık hizmetleri',
                'sort_order' => 6
            ],
            [
                'code' => 'consultancy',
                'name' => 'Danışmanlık',
                'icon' => 'fas fa-briefcase',
                'description' => 'İş danışmanlığı, hukuk, muhasebe ve profesyonel hizmetler',
                'sort_order' => 7
            ],
            [
                'code' => 'construction',
                'name' => 'İnşaat & Yapı',
                'icon' => 'fas fa-hammer',
                'description' => 'İnşaat, yapı malzemeleri, mimarlık ve mühendislik',
                'sort_order' => 8
            ],
            [
                'code' => 'e-commerce',
                'name' => 'E-Ticaret',
                'icon' => 'fas fa-shopping-cart',
                'description' => 'Online satış, mağaza yönetimi ve e-ticaret çözümleri',
                'sort_order' => 9
            ],
            [
                'code' => 'education',
                'name' => 'Eğitim',
                'icon' => 'fas fa-graduation-cap',
                'description' => 'Okul, kurs, eğitim kurumları ve online eğitim',
                'sort_order' => 10
            ],
            [
                'code' => 'energy',
                'name' => 'Enerji & Çevre',
                'icon' => 'fas fa-bolt',
                'description' => 'Elektrik, doğalgaz, yenilenebilir enerji ve çevre teknolojileri',
                'sort_order' => 11
            ],
            [
                'code' => 'entertainment',
                'name' => 'Eğlence & Medya',
                'icon' => 'fas fa-tv',
                'description' => 'Sinema, müzik, oyun, medya ve eğlence sektörü',
                'sort_order' => 12
            ],
            [
                'code' => 'events',
                'name' => 'Etkinlik & Organizasyon',
                'icon' => 'fas fa-calendar-alt',
                'description' => 'Düğün, konferans, etkinlik planlama ve organizasyon',
                'sort_order' => 13
            ],
            [
                'code' => 'finance',
                'name' => 'Finans & Bankacılık',
                'icon' => 'fas fa-chart-line',
                'description' => 'Banka, sigorta, yatırım ve finansal hizmetler',
                'sort_order' => 14
            ],
            [
                'code' => 'food-beverage',
                'name' => 'Gıda & İçecek',
                'icon' => 'fas fa-utensils',
                'description' => 'Gıda üretimi, içecek, toptan gıda ve gıda teknolojisi',
                'sort_order' => 15
            ],
            [
                'code' => 'real-estate',
                'name' => 'Gayrimenkul',
                'icon' => 'fas fa-building',
                'description' => 'Emlak, gayrimenkul danışmanlığı, kiralama ve değerleme',
                'sort_order' => 16
            ],
            [
                'code' => 'home-garden',
                'name' => 'Ev & Bahçe',
                'icon' => 'fas fa-home',
                'description' => 'Ev dekorasyonu, bahçe düzenleme ve ev geliştirme',
                'sort_order' => 17
            ],
            [
                'code' => 'jewelry',
                'name' => 'Mücevher & Aksesuar',
                'icon' => 'fas fa-gem',
                'description' => 'Mücevher, saat, aksesuar ve değerli taşlar',
                'sort_order' => 18
            ],
            [
                'code' => 'logistics',
                'name' => 'Lojistik & Nakliye',
                'icon' => 'fas fa-truck',
                'description' => 'Kargo, nakliye, depolama ve lojistik hizmetleri',
                'sort_order' => 19
            ],
            [
                'code' => 'manufacturing',
                'name' => 'Üretim & İmalat',
                'icon' => 'fas fa-tools',
                'description' => 'Fabrika, üretim, imalat ve endüstriyel çözümler',
                'sort_order' => 20
            ],
            [
                'code' => 'music',
                'name' => 'Müzik & Ses',
                'icon' => 'fas fa-music',
                'description' => 'Müzik prodüksiyonu, ses teknolojileri ve müzik eğitimi',
                'sort_order' => 21
            ],
            [
                'code' => 'nonprofit',
                'name' => 'Kar Amacı Gütmeyen',
                'icon' => 'fas fa-heart',
                'description' => 'Vakıf, dernek, hayır kurumları ve sosyal projeler',
                'sort_order' => 22
            ],
            [
                'code' => 'pets',
                'name' => 'Evcil Hayvan',
                'icon' => 'fas fa-paw',
                'description' => 'Veteriner, pet shop, hayvan bakım ve evcil hayvan hizmetleri',
                'sort_order' => 23
            ],
            [
                'code' => 'photography',
                'name' => 'Fotoğrafçılık',
                'icon' => 'fas fa-camera',
                'description' => 'Fotoğraf çekimi, video prodüksiyonu ve görsel hizmetler',
                'sort_order' => 24
            ],
            [
                'code' => 'retail',
                'name' => 'Perakende & Mağaza',
                'icon' => 'fas fa-shopping-bag',
                'description' => 'Mağaza, market, butik ve perakende satış',
                'sort_order' => 25
            ],
            [
                'code' => 'restaurant',
                'name' => 'Restoran & Kafe',
                'icon' => 'fas fa-utensils',
                'description' => 'Restoran, kafe, catering ve yemek hizmetleri',
                'sort_order' => 26
            ],
            [
                'code' => 'health',
                'name' => 'Sağlık',
                'icon' => 'fas fa-heart',
                'description' => 'Hastane, klinik, doktor ve sağlık hizmetleri',
                'sort_order' => 27
            ],
            [
                'code' => 'security',
                'name' => 'Güvenlik',
                'icon' => 'fas fa-shield-alt',
                'description' => 'Güvenlik hizmetleri, alarm sistemi ve güvenlik teknolojileri',
                'sort_order' => 28
            ],
            [
                'code' => 'sports-fitness',
                'name' => 'Spor & Fitness',
                'icon' => 'fas fa-dumbbell',
                'description' => 'Spor salonu, fitness, spor kulübü ve spor ekipmanları',
                'sort_order' => 29
            ],
            [
                'code' => 'technology',
                'name' => 'Teknoloji & Yazılım',
                'icon' => 'fas fa-laptop',
                'description' => 'Yazılım, donanım, IT hizmetleri ve teknoloji çözümleri',
                'sort_order' => 30
            ],
            [
                'code' => 'textile-fashion',
                'name' => 'Tekstil & Moda',
                'icon' => 'fas fa-tshirt',
                'description' => 'Giyim, ayakkabı, aksesuar ve tekstil üretimi',
                'sort_order' => 31
            ],
            [
                'code' => 'tourism',
                'name' => 'Turizm & Seyahat',
                'icon' => 'fas fa-plane',
                'description' => 'Otel, tur, seyahat acentesi ve turizm hizmetleri',
                'sort_order' => 32
            ],
            [
                'code' => 'transportation',
                'name' => 'Ulaştırma & Taşımacılık',
                'icon' => 'fas fa-bus',
                'description' => 'Otobüs, taksi, şehir içi ulaşım ve yolcu taşımacılığı',
                'sort_order' => 33
            ],
            [
                'code' => 'wedding',
                'name' => 'Düğün & Nikah',
                'icon' => 'fas fa-ring',
                'description' => 'Düğün organizasyonu, nikah hizmetleri ve düğün fotoğrafçılığı',
                'sort_order' => 34
            ],
            // Diğer sektörler her zaman en sonda
            [
                'code' => 'other',
                'name' => 'Diğer Sektörler',
                'icon' => 'fas fa-ellipsis-h',
                'description' => 'Diğer sektörler ve özel çözümler',
                'sort_order' => 999
            ]
        ];

        foreach ($sectors as $sector) {
            AIProfileSector::updateOrCreate(
                ['code' => $sector['code']],
                $sector
            );
        }
    }
}