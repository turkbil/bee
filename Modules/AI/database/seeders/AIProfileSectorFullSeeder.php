<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorFullSeeder extends Seeder
{
    public function run()
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        // Önce mevcut seçimleri kontrol et (hem name hem code)
        $existingNames = AIProfileSector::whereNull('category_id')->pluck('name')->toArray();
        $existingCodes = AIProfileSector::pluck('code')->toArray();
        
        $fullSectorData = [
            // 4. Yiyecek & İçecek
            [
                'name' => 'Yiyecek & İçecek',
                'code' => 'food',
                'emoji' => '🍽️',
                'description' => 'Restoran, kafe, catering, gıda üretimi',
                'keywords' => 'yemek, restoran, kafe, mutfak, catering, gıda',
                'subcategories' => [
                    ['name' => 'Restoran & Lokanta', 'code' => 'food_1', 'emoji' => '🍕', 'description' => 'Fine dining, casual dining, etnik mutfaklar'],
                    ['name' => 'Kafe & Kahvehane', 'code' => 'food_2', 'emoji' => '☕', 'description' => 'Specialty coffee, çay evi, brunch'],
                    ['name' => 'Pastane & Fırın', 'code' => 'food_3', 'emoji' => '🍰', 'description' => 'Artisan pastane, ekmek fırını, butik pasta'],
                    ['name' => 'Fast Food & Sokak Lezzetleri', 'code' => 'food_4', 'emoji' => '🍔', 'description' => 'Burger, pizza, döner, street food'],
                    ['name' => 'Healthy Food & Vegan', 'code' => 'food_5', 'emoji' => '🥗', 'description' => 'Sağlıklı beslenme, organik gıda, vegan menü'],
                    ['name' => 'Bar & Pub', 'code' => 'food_6', 'emoji' => '🍻', 'description' => 'Cocktail bar, craft beer, wine bar'],
                    ['name' => 'Yemek Servisi & Catering', 'code' => 'food_7', 'emoji' => '🚚', 'description' => 'Toplu yemek, etkinlik catering, delivery'],
                    ['name' => 'Gıda Üretimi & Dağıtım', 'code' => 'food_8', 'emoji' => '🛒', 'description' => 'Gıda üretim, toptan gıda, tedarik zinciri']
                ]
            ],
            
            // 5. Retail & E-ticaret
            [
                'name' => 'Retail & E-ticaret',
                'code' => 'retail',
                'emoji' => '🛍️',
                'description' => 'Perakende satış, e-ticaret, mağazacılık',
                'keywords' => 'satış, mağaza, e-ticaret, perakende, alışveriş',
                'subcategories' => [
                    ['name' => 'Giyim & Moda', 'code' => 'retail_1', 'emoji' => '👕', 'description' => 'Tekstil, giyim, ayakkabı, aksesuar'],
                    ['name' => 'Elektronik & Teknoloji Ürünleri', 'code' => 'retail_2', 'emoji' => '💻', 'description' => 'Bilgisayar, telefon, elektronik cihaz'],
                    ['name' => 'Ev & Yaşam Ürünleri', 'code' => 'retail_3', 'emoji' => '🏠', 'description' => 'Mobilya, dekorasyon, ev tekstili'],
                    ['name' => 'Sağlık & Kişisel Bakım', 'code' => 'retail_4', 'emoji' => '💄', 'description' => 'Kozmetik, parfüm, kişisel bakım'],
                    ['name' => 'Spor & Outdoor Ürünleri', 'code' => 'retail_5', 'emoji' => '⚽', 'description' => 'Spor malzemeleri, outdoor ekipman'],
                    ['name' => 'E-ticaret Platformları', 'code' => 'retail_6', 'emoji' => '🛒', 'description' => 'Online mağaza, marketplace, dropshipping'],
                    ['name' => 'Otomotiv Yedek Parça', 'code' => 'retail_7', 'emoji' => '🚗', 'description' => 'Araç yedek parça, aksesuar satış'],
                    ['name' => 'Kitap & Kırtasiye', 'code' => 'retail_8', 'emoji' => '📚', 'description' => 'Kitap, dergi, kırtasiye malzemeleri']
                ]
            ],
            
            // 6. İnşaat & Emlak
            [
                'name' => 'İnşaat & Emlak',
                'code' => 'construction',
                'emoji' => '🏗️',
                'description' => 'İnşaat, gayrimenkul, mimarlık, mühendislik',
                'keywords' => 'inşaat, emlak, gayrimenkul, müteahhit, mimarlık',
                'subcategories' => [
                    ['name' => 'Konut İnşaatı & Müteahhitlik', 'code' => 'construction_1', 'emoji' => '🏠', 'description' => 'Villa, apartman, konut projeleri'],
                    ['name' => 'Ticari & Endüstriyel İnşaat', 'code' => 'construction_2', 'emoji' => '🏢', 'description' => 'Fabrika, ofis, alışveriş merkezi'],
                    ['name' => 'Altyapı & Kamu İnşaatları', 'code' => 'construction_3', 'emoji' => '🛣️', 'description' => 'Yol, köprü, tünel, su şebekesi'],
                    ['name' => 'İnşaat Malzemesi & Satış', 'code' => 'construction_4', 'emoji' => '📏', 'description' => 'Çimento, demir, tuğla, malzeme'],
                    ['name' => 'Mimarlık & Tasarım', 'code' => 'construction_5', 'emoji' => '📐', 'description' => 'Mimari proje, iç mimarlık, planlama'],
                    ['name' => 'Gayrimenkul & Emlak', 'code' => 'construction_6', 'emoji' => '🏘️', 'description' => 'Emlak danışmanlığı, satış, kiralama'],
                    ['name' => 'Tadilat & Renovasyon', 'code' => 'construction_7', 'emoji' => '🔨', 'description' => 'Ev tadilat, restorasyon, yenileme'],
                    ['name' => 'Peyzaj & Bahçe Düzenleme', 'code' => 'construction_8', 'emoji' => '🌿', 'description' => 'Bahçe tasarım, peyzaj mimarlığı']
                ]
            ],
            
            // 7. Finans & Muhasebe
            [
                'name' => 'Finans & Muhasebe',
                'code' => 'finance',
                'emoji' => '💰',
                'description' => 'Bankacılık, muhasebe, finansal danışmanlık',
                'keywords' => 'finans, muhasebe, banka, sigorta, yatırım',
                'subcategories' => [
                    ['name' => 'Bankacılık & Finansal Hizmetler', 'code' => 'finance_1', 'emoji' => '🏦', 'description' => 'Banka şubesi, kredi, mevduat'],
                    ['name' => 'Muhasebe & Mali Müşavirlik', 'code' => 'finance_2', 'emoji' => '📊', 'description' => 'Muhasebe, vergi danışmanlığı, mali müşavir'],
                    ['name' => 'Sigorta & Risk Yönetimi', 'code' => 'finance_3', 'emoji' => '🛡️', 'description' => 'Hayat, kasko, dask, sağlık sigortası'],
                    ['name' => 'Yatırım & Portföy Yönetimi', 'code' => 'finance_4', 'emoji' => '📈', 'description' => 'Borsa, fon, yatırım danışmanlığı'],
                    ['name' => 'Kripto Para & Blockchain', 'code' => 'finance_5', 'emoji' => '₿', 'description' => 'Bitcoin, altcoin, blockchain teknoloji'],
                    ['name' => 'Finansal Danışmanlık', 'code' => 'finance_6', 'emoji' => '💼', 'description' => 'Mali planlama, bütçe yönetimi'],
                    ['name' => 'Leasing & Factoring', 'code' => 'finance_7', 'emoji' => '🤝', 'description' => 'Finansal kiralama, fatura finansmanı'],
                    ['name' => 'Borsa & Forex', 'code' => 'finance_8', 'emoji' => '💹', 'description' => 'Döviz alım satım, borsa işlemleri']
                ]
            ],
            
            // 8. Sanat & Tasarım
            [
                'name' => 'Sanat & Tasarım',
                'code' => 'art_design',
                'emoji' => '🎨',
                'description' => 'Grafik tasarım, sanat, kreatif hizmetler',
                'keywords' => 'tasarım, sanat, grafik, kreatif, reklam',
                'subcategories' => [
                    ['name' => 'Grafik Tasarım & Reklam', 'code' => 'art_1', 'emoji' => '🖼️', 'description' => 'Logo, afiş, reklam tasarımı'],
                    ['name' => 'Web & UI/UX Tasarım', 'code' => 'art_2', 'emoji' => '💻', 'description' => 'Website tasarım, kullanıcı deneyimi'],
                    ['name' => 'Fotoğrafçılık & Video', 'code' => 'art_3', 'emoji' => '📸', 'description' => 'Düğün, ürün, kurumsal fotoğraf'],
                    ['name' => 'İç Mimarlık & Dekorasyon', 'code' => 'art_4', 'emoji' => '🏠', 'description' => 'İç mekan tasarım, dekorasyon'],
                    ['name' => 'Müzik & Ses Prodüksiyon', 'code' => 'art_5', 'emoji' => '🎵', 'description' => 'Müzik prodüksiyon, ses teknisyeni'],
                    ['name' => 'Film & Video Prodüksiyon', 'code' => 'art_6', 'emoji' => '🎬', 'description' => 'Film çekim, video montaj, animasyon'],
                    ['name' => 'El Sanatları & Hobi', 'code' => 'art_7', 'emoji' => '🖐️', 'description' => 'Seramik, takı, el yapımı ürünler'],
                    ['name' => 'Sanat Galerisi & Müze', 'code' => 'art_8', 'emoji' => '🖼️', 'description' => 'Sanat eseri, galeri, müze hizmetleri']
                ]
            ],
            
            // 9. Spor & Fitness
            [
                'name' => 'Spor & Fitness',
                'code' => 'sports',
                'emoji' => '🏋️',
                'description' => 'Spor kulübü, fitness, antrenörlük',
                'keywords' => 'spor, fitness, antrenör, kulüp, spor salonu',
                'subcategories' => [
                    ['name' => 'Fitness & Spor Salonu', 'code' => 'sports_1', 'emoji' => '💪', 'description' => 'Gym, fitness merkezi, ağırlık antrenmanı'],
                    ['name' => 'Pilates & Yoga', 'code' => 'sports_2', 'emoji' => '🧘', 'description' => 'Yoga dersi, pilates, meditasyon'],
                    ['name' => 'Dövüş Sanatları & Savunma', 'code' => 'sports_3', 'emoji' => '🥋', 'description' => 'Karate, taekwondo, boks, kick boks'],
                    ['name' => 'Su Sporları & Yüzme', 'code' => 'sports_4', 'emoji' => '🏊', 'description' => 'Yüzme dersi, su polo, aqua fitness'],
                    ['name' => 'Takım Sporları', 'code' => 'sports_5', 'emoji' => '⚽', 'description' => 'Futbol, basketbol, voleybol kulübü'],
                    ['name' => 'Kişisel Antrenörlük', 'code' => 'sports_6', 'emoji' => '🏃', 'description' => 'Personal trainer, özel antrenman'],
                    ['name' => 'Outdoor & Macera Sporları', 'code' => 'sports_7', 'emoji' => '🧗', 'description' => 'Dağcılık, tırmanış, kamp, doğa sporları'],
                    ['name' => 'Dans & Hareket', 'code' => 'sports_8', 'emoji' => '💃', 'description' => 'Bale, modern dans, latin dans, zumba']
                ]
            ],
            
            // 10. Otomotiv
            [
                'name' => 'Otomotiv',
                'code' => 'automotive',
                'emoji' => '🚗',
                'description' => 'Araç satış, servis, yedek parça, rent a car',
                'keywords' => 'otomotiv, araç, servis, yedek parça, galeri',
                'subcategories' => [
                    ['name' => 'Otomobil Galeri & Bayi', 'code' => 'automotive_1', 'emoji' => '🚙', 'description' => 'Sıfır araç, ikinci el, otomobil satış'],
                    ['name' => 'Otomotiv Servis & Tamirci', 'code' => 'automotive_2', 'emoji' => '🔧', 'description' => 'Araç bakım, tamır, periyodikbakim'],
                    ['name' => 'Yedek Parça & Aksesuar', 'code' => 'automotive_3', 'emoji' => '⚙️', 'description' => 'Orijinal yedek parça, modifiye'],
                    ['name' => 'Rent a Car & Araç Kiralama', 'code' => 'automotive_4', 'emoji' => '🚘', 'description' => 'Günlük, aylık araç kiralama'],
                    ['name' => 'Lastik & Jant', 'code' => 'automotive_5', 'emoji' => '🛞', 'description' => 'Lastik satış, balans, jant'],
                    ['name' => 'Oto Yıkama & Detailing', 'code' => 'automotive_6', 'emoji' => '🧽', 'description' => 'Araç yıkama, cilalama, detailing'],
                    ['name' => 'Kurtarma & Çekici', 'code' => 'automotive_7', 'emoji' => '🚛', 'description' => 'Araç kurtarma, çekici, yol yardım'],
                    ['name' => 'Sürücü Kursu & Ehliyet', 'code' => 'automotive_8', 'emoji' => '🪪', 'description' => 'Direksiyon eğitimi, ehliyet kursu']
                ]
            ]
        ];
        
        foreach ($fullSectorData as $sector) {
            // Ana kategori kontrolü (hem name hem code)
            if (!in_array($sector['name'], $existingNames) && !in_array($sector['code'], $existingCodes)) {
                \Log::info("Creating main category: " . $sector['name']);
                
                $mainCategory = AIProfileSector::create([
                    'name' => $sector['name'],
                    'code' => $sector['code'],
                    'emoji' => $sector['emoji'],
                    'description' => $sector['description'],
                    'keywords' => $sector['keywords'],
                    'category_id' => null,
                    'is_active' => true,
                    'sort_order' => 100
                ]);
                
                // Ana kategori code'unu listeye ekle
                $existingCodes[] = $sector['code'];
                
                // Alt kategoriler
                foreach ($sector['subcategories'] as $index => $sub) {
                    // Subcategory code kontrolü
                    if (!in_array($sub['code'], $existingCodes)) {
                        AIProfileSector::create([
                            'name' => $sub['name'],
                            'code' => $sub['code'],
                            'emoji' => $sub['emoji'],
                            'description' => $sub['description'],
                            'keywords' => $sub['description'] . ' ' . $sector['keywords'],
                            'category_id' => $mainCategory->id,
                            'is_active' => true,
                            'sort_order' => ($index + 1) * 10
                        ]);
                        
                        // Code listesine ekle
                        $existingCodes[] = $sub['code'];
                    }
                }
                
                \Log::info("Created " . count($sector['subcategories']) . " subcategories for " . $sector['name']);
            }
        }
        
        \Log::info("AIProfileSectorFullSeeder completed");
    }
}