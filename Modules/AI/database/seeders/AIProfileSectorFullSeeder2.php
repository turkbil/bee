<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorFullSeeder2 extends Seeder
{
    public function run()
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        // Önce mevcut seçimleri kontrol et (hem name hem code)
        $existingNames = AIProfileSector::whereNull('category_id')->pluck('name')->toArray();
        $existingCodes = AIProfileSector::pluck('code')->toArray();
        
        // Kalan kategoriler
        $remainingSectorData = [
            // 11. Endüstri & İmalat
            [
                'name' => 'Endüstri & İmalat',
                'code' => 'manufacturing',
                'emoji' => '🏭',
                'description' => 'Sanayi üretimi, imalat, fabrikasyon',
                'keywords' => 'imalat, üretim, sanayi, fabrika, endüstri',
                'subcategories' => [
                    ['name' => 'Makina & Parça İmalatı', 'code' => 'manufacturing_1', 'emoji' => '⚙️', 'description' => 'CNC torna, freze, parça üretim'],
                    ['name' => 'Metal Ürünleri & İşleme', 'code' => 'manufacturing_2', 'emoji' => '🔩', 'description' => 'Çelik işleme, metal parça üretim'],
                    ['name' => 'Tekstil & Konfeksiyon', 'code' => 'manufacturing_3', 'emoji' => '🧵', 'description' => 'Kumaş, giyim, ev tekstili üretim'],
                    ['name' => 'Gıda & İçecek Üretimi', 'code' => 'manufacturing_4', 'emoji' => '🏭', 'description' => 'Gıda işleme, ambalaj, içecek üretimi'],
                    ['name' => 'Kimya & Petrokimya', 'code' => 'manufacturing_5', 'emoji' => '🧪', 'description' => 'Kimyasal üretim, plastik, deterjan'],
                    ['name' => 'Elektronik & Elektrik', 'code' => 'manufacturing_6', 'emoji' => '⚡', 'description' => 'Elektronik kart, kablo, elektrik malzeme'],
                    ['name' => 'Cam, Çimento & İnşaat Malzemesi', 'code' => 'manufacturing_7', 'emoji' => '🏠', 'description' => 'Cam üretim, çimento, tuğla, kiremit'],
                    ['name' => 'Otomotiv Yan Sanayi', 'code' => 'manufacturing_8', 'emoji' => '🚗', 'description' => 'Araç parça üretimi, OEM, yedek parça']
                ]
            ],
            
            // 12. Tarım & Hayvancılık
            [
                'name' => 'Tarım & Hayvancılık',
                'code' => 'agriculture',
                'emoji' => '🌾',
                'description' => 'Tarımsal üretim, hayvancılık, gıda',
                'keywords' => 'tarım, hayvancılık, çiftlik, üretici, gıda',
                'subcategories' => [
                    ['name' => 'Bitkisel Üretim & Tarım', 'code' => 'agriculture_1', 'emoji' => '🌱', 'description' => 'Meyve, sebze, hububat, endüstri bitkileri'],
                    ['name' => 'Hayvancılık & Çiftlik', 'code' => 'agriculture_2', 'emoji' => '🐄', 'description' => 'Büyükbaş, küçükbaş, kümes hayvanları'],
                    ['name' => 'Su Ürünleri & Balıkçılık', 'code' => 'agriculture_3', 'emoji' => '🐟', 'description' => 'Balık üretimi, su ürünleri, akvakültür'],
                    ['name' => 'Tarım Makinaları & Ekipman', 'code' => 'agriculture_4', 'emoji' => '🚜', 'description' => 'Traktör, tarım aleti, sulama sistemleri'],
                    ['name' => 'Gübre & Tarım İlaçları', 'code' => 'agriculture_5', 'emoji' => '🧪', 'description' => 'Organik gübre, kimyasal gübre, zirai ilaç'],
                    ['name' => 'Tohum & Fide Üretimi', 'code' => 'agriculture_6', 'emoji' => '🌰', 'description' => 'Sertifikalı tohum, fide, fidan üretimi'],
                    ['name' => 'Arıcılık & Bal Üretimi', 'code' => 'agriculture_7', 'emoji' => '🐝', 'description' => 'Bal, polen, propolis, arı ürünleri'],
                    ['name' => 'Tarımsal Danışmanlık', 'code' => 'agriculture_8', 'emoji' => '👨‍🌾', 'description' => 'Tarım tekniği, verim artırma, eğitim']
                ]
            ],
            
            // 13. Medya & İletişim
            [
                'name' => 'Medya & İletişim',
                'code' => 'media',
                'emoji' => '📺',
                'description' => 'Medya, yayıncılık, reklam, halkla ilişkiler',
                'keywords' => 'medya, televizyon, radyo, gazete, reklam',
                'subcategories' => [
                    ['name' => 'Televizyon & Radyo', 'code' => 'media_1', 'emoji' => '📻', 'description' => 'TV kanalı, radyo istasyonu, yayıncılık'],
                    ['name' => 'Gazete & Dergi', 'code' => 'media_2', 'emoji' => '📰', 'description' => 'Yerel gazete, dergi, basılı yayın'],
                    ['name' => 'Dijital Medya & Sosyal Medya', 'code' => 'media_3', 'emoji' => '📱', 'description' => 'Haber sitesi, sosyal medya yönetimi'],
                    ['name' => 'Reklam Ajansı & Pazarlama', 'code' => 'media_4', 'emoji' => '📢', 'description' => 'Reklam kampanya, marka yönetimi'],
                    ['name' => 'Halkla İlişkiler & PR', 'code' => 'media_5', 'emoji' => '🤝', 'description' => 'Kurumsal iletişim, basın sözcülüğü'],
                    ['name' => 'Etkinlik & Organizasyon', 'code' => 'media_6', 'emoji' => '🎪', 'description' => 'Konser, festival, fuar organizasyonu'],
                    ['name' => 'İçerik Üretimi & Podcasting', 'code' => 'media_7', 'emoji' => '🎙️', 'description' => 'Podcast, YouTube, blog içeriği'],
                    ['name' => 'Basım & Matbaa', 'code' => 'media_8', 'emoji' => '🖨️', 'description' => 'Ofset basım, dijital baskı, matbaacılık']
                ]
            ],
            
            // 14. Bireysel & Freelance
            [
                'name' => 'Bireysel & Freelance',
                'code' => 'freelance',
                'emoji' => '👤',
                'description' => 'Bireysel hizmetler, freelance, danışmanlık',
                'keywords' => 'freelance, bireysel, danışman, uzman, hizmet',
                'subcategories' => [
                    ['name' => 'Danışman & Uzman', 'code' => 'freelance_1', 'emoji' => '🧠', 'description' => 'Serbest danışman, uzman, mentor'],
                    ['name' => 'Yazar & İçerik Üretici', 'code' => 'freelance_2', 'emoji' => '✍️', 'description' => 'Copywriter, blog yazarı, editör'],
                    ['name' => 'Çevirmen & Dil Uzmanı', 'code' => 'freelance_3', 'emoji' => '🌍', 'description' => 'Tercüman, sözlü çeviri, çeviri'],
                    ['name' => 'Sanatçı & Portfolyo', 'code' => 'freelance_4', 'emoji' => '🎨', 'description' => 'Ressam, heykeltıraş, sanat eseri'],
                    ['name' => 'Müzisyen & Ses Sanatçısı', 'code' => 'freelance_5', 'emoji' => '🎵', 'description' => 'Müzik öğretmeni, icracı, beste'],
                    ['name' => 'Kişisel Bakım & Güzellik', 'code' => 'freelance_6', 'emoji' => '💅', 'description' => 'Kuaför, estetisyen, masöz'],
                    ['name' => 'Ev Temizlik & Bakım', 'code' => 'freelance_7', 'emoji' => '🧹', 'description' => 'Temizlik, bahçıvan, ev bakım'],
                    ['name' => 'Ulaşım & Şoförlük', 'code' => 'freelance_8', 'emoji' => '🚗', 'description' => 'Taksi, şoför, kurye, nakliye']
                ]
            ],
            
            // 15. Hukuk & Danışmanlık
            [
                'name' => 'Hukuk & Danışmanlık',
                'code' => 'legal',
                'emoji' => '⚖️',
                'description' => 'Avukat, hukuk bürosu, yasal danışmanlık',
                'keywords' => 'avukat, hukuk, dava, mahkeme, danışmanlık',
                'subcategories' => [
                    ['name' => 'Avukatlık & Hukuk Bürosu', 'code' => 'legal_1', 'emoji' => '⚖️', 'description' => 'Genel hukuk, dava takibi, hukuki danışmanlık'],
                    ['name' => 'Kurumsal Hukuk & Ticaret Hukuku', 'code' => 'legal_2', 'emoji' => '🏢', 'description' => 'Şirket hukuku, sözleşme, ticari dava'],
                    ['name' => 'Emlak Hukuku & Gayrimenkul', 'code' => 'legal_3', 'emoji' => '🏠', 'description' => 'Tapu işlemleri, kira hukuku, inşaat'],
                    ['name' => 'Aile Hukuku & Boşanma', 'code' => 'legal_4', 'emoji' => '👨‍👩‍👧‍👦', 'description' => 'Boşanma, velayet, nafaka, miras'],
                    ['name' => 'İş Hukuku & İşçi Hakları', 'code' => 'legal_5', 'emoji' => '⚡', 'description' => 'İş sözleşmesi, işçi hakları, tazminat'],
                    ['name' => 'Bilişim Hukuku & Kişisel Veri', 'code' => 'legal_6', 'emoji' => '💻', 'description' => 'KVKK, cyber hukuk, e-ticaret hukuku'],
                    ['name' => 'Trafik Hukuku & Sigorta', 'code' => 'legal_7', 'emoji' => '🚗', 'description' => 'Trafik kazası, sigorta tazminatı'],
                    ['name' => 'İdare Hukuku & Kamu', 'code' => 'legal_8', 'emoji' => '🏛️', 'description' => 'İdari dava, belediye, ihale, vergi']
                ]
            ],
            
            // 16. Çevre & Geri Dönüşüm
            [
                'name' => 'Çevre & Geri Dönüşüm',
                'code' => 'environment',
                'emoji' => '♻️',
                'description' => 'Çevre hizmetleri, geri dönüşüm, temizlik',
                'keywords' => 'çevre, geri dönüşüm, atık, temizlik, yeşil',
                'subcategories' => [
                    ['name' => 'Geri Dönüşüm & Atık Yönetimi', 'code' => 'environment_1', 'emoji' => '♻️', 'description' => 'Plastik, kağıt, cam geri dönüşüm'],
                    ['name' => 'Çevre Danışmanlığı & Sürdürülebilirlik', 'code' => 'environment_2', 'emoji' => '🌱', 'description' => 'ISO 14001, sürdürülebilirlik raporu'],
                    ['name' => 'Temizlik & Hijyen Hizmetleri', 'code' => 'environment_3', 'emoji' => '🧹', 'description' => 'Endüstriyel temizlik, ofis hijyeni'],
                    ['name' => 'Peyzaj & Bahçıvanlık', 'code' => 'environment_4', 'emoji' => '🌿', 'description' => 'Bahçe bakımı, ağaçlandırma, yeşil alan'],
                    ['name' => 'Su Arıtma & Çevre Teknolojileri', 'code' => 'environment_5', 'emoji' => '💧', 'description' => 'Su arıtma, hava filtreleme teknoloji'],
                    ['name' => 'Yenilenebilir Enerji', 'code' => 'environment_6', 'emoji' => '☀️', 'description' => 'Güneş, rüzgar, hidrolik enerji'],
                    ['name' => 'Endüstriyel Çevre Çözümleri', 'code' => 'environment_7', 'emoji' => '🏭', 'description' => 'Emisyon kontrolü, atık su arıtma'],
                    ['name' => 'Organik Tarım & Ekoloji', 'code' => 'environment_8', 'emoji' => '🌾', 'description' => 'Organik ürün, permakültür, ekoloji']
                ]
            ],
            
            // 17. Metal & Demir Çelik
            [
                'name' => 'Metal & Demir Çelik',
                'code' => 'metallurgy',
                'emoji' => '🔩',
                'description' => 'Metal işleme, demir çelik, metal ürünleri',
                'keywords' => 'metal, demir, çelik, kaynak, işleme',
                'subcategories' => [
                    ['name' => 'Demir Çelik Üretimi', 'code' => 'metallurgy_1', 'emoji' => '🏭', 'description' => 'Ham çelik, profil çelik, sac üretimi'],
                    ['name' => 'Metal İşleme & Makina Parçaları', 'code' => 'metallurgy_2', 'emoji' => '⚙️', 'description' => 'CNC işleme, torna, freze'],
                    ['name' => 'Metal Konstrüksiyon & Çelik Yapı', 'code' => 'metallurgy_3', 'emoji' => '🏗️', 'description' => 'Çelik konstrüksiyon, hangar, fabrika'],
                    ['name' => 'Kaynak & Metal Birleştirme', 'code' => 'metallurgy_4', 'emoji' => '⚡', 'description' => 'Argon kaynak, elektrik kaynak, lehim'],
                    ['name' => 'Metal Kaplama & Yüzey İşlemi', 'code' => 'metallurgy_5', 'emoji' => '🎨', 'description' => 'Galvaniz, boyama, krom kaplama'],
                    ['name' => 'Bağlantı Elemanları', 'code' => 'metallurgy_6', 'emoji' => '🔩', 'description' => 'Civata, somun, vida, metal aksesu'],
                    ['name' => 'Metal Ambalaj & Teneke', 'code' => 'metallurgy_7', 'emoji' => '📦', 'description' => 'Konserve kutusu, metal ambalaj'],
                    ['name' => 'Metal Hurda & Geri Dönüşüm', 'code' => 'metallurgy_8', 'emoji' => '🔧', 'description' => 'Demir hurda, metal geri dönüşüm']
                ]
            ]
        ];
        
        foreach ($remainingSectorData as $sector) {
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
        
        \Log::info("AIProfileSectorFullSeeder2 completed");
    }
}