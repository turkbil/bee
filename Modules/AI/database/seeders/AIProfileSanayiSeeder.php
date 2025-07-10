<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSanayiSeeder extends Seeder
{
    public function run()
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        \Log::info("AIProfileSanayiSeeder başlatıldı");
        
        // 1. Sanayi ana kategorisini oluştur
        $sanayiCategory = AIProfileSector::create([
            'name' => 'Sanayi',
            'code' => 'industry',
            'emoji' => '🏭',
            'description' => 'Endüstriyel üretim, imalat, sanayi sektörü',
            'keywords' => 'sanayi, endüstri, imalat, üretim, fabrika, makina',
            'category_id' => null,
            'is_active' => true,
            'sort_order' => 50
        ]);
        
        \Log::info("Sanayi ana kategorisi oluşturuldu", ['id' => $sanayiCategory->id]);
        
        // 2. Mevcut kategorileri Sanayi altına taşı
        $categoriesToMove = [
            'manufacturing' => 'Endüstri & İmalat',
            'metallurgy' => 'Metal & Demir Çelik'
        ];
        
        foreach ($categoriesToMove as $code => $name) {
            $category = AIProfileSector::where('code', $code)->whereNull('category_id')->first();
            if ($category) {
                // Ana kategoriyi sanayi altına taşı
                $category->update([
                    'category_id' => $sanayiCategory->id,
                    'sort_order' => 10
                ]);
                
                \Log::info("Kategori Sanayi altına taşındı", [
                    'name' => $name,
                    'code' => $code,
                    'new_parent_id' => $sanayiCategory->id
                ]);
            }
        }
        
        // 3. Yeni sanayi alt kategorileri ekle
        $newIndustrialCategories = [
            [
                'name' => 'Makina & Ekipman İmalatı',
                'code' => 'industry_machinery',
                'emoji' => '⚙️',
                'description' => 'Endüstriyel makina, otomasyon, CNC tezgah üretimi',
                'subcategories' => [
                    ['name' => 'CNC Tezgah & Otomasyon', 'code' => 'industry_machinery_1', 'emoji' => '🔧', 'description' => 'CNC torna, freze, otomasyon sistemleri'],
                    ['name' => 'Endüstriyel Makina Üretimi', 'code' => 'industry_machinery_2', 'emoji' => '🏭', 'description' => 'Üretim bantları, endüstriyel ekipman'],
                    ['name' => 'Tarım Makinaları', 'code' => 'industry_machinery_3', 'emoji' => '🚜', 'description' => 'Traktör, hasat makinası, tarım aleti'],
                    ['name' => 'İnşaat Makinaları', 'code' => 'industry_machinery_4', 'emoji' => '🚧', 'description' => 'Ekskavatör, buldozer, vinç'],
                    ['name' => 'Gıda İşleme Makinaları', 'code' => 'industry_machinery_5', 'emoji' => '🥫', 'description' => 'Gıda üretim, ambalaj makinaları'],
                    ['name' => 'Tekstil Makinaları', 'code' => 'industry_machinery_6', 'emoji' => '🧵', 'description' => 'Dokuma, konfeksiyon makinaları'],
                ]
            ],
            [
                'name' => 'Kimya & Petrokimya',
                'code' => 'industry_chemical',
                'emoji' => '🧪',
                'description' => 'Kimyasal üretim, petrokimya, plastik sanayi',
                'subcategories' => [
                    ['name' => 'Kimyasal Üretim', 'code' => 'industry_chemical_1', 'emoji' => '⚗️', 'description' => 'Endüstriyel kimyasal, asit, alkali'],
                    ['name' => 'Plastik & Polimer', 'code' => 'industry_chemical_2', 'emoji' => '🪣', 'description' => 'Plastik hammadde, polimer üretim'],
                    ['name' => 'Boya & Vernik', 'code' => 'industry_chemical_3', 'emoji' => '🎨', 'description' => 'Endüstriyel boya, koruyucu kaplama'],
                    ['name' => 'Gübre & Tarım Kimyasalları', 'code' => 'industry_chemical_4', 'emoji' => '🌱', 'description' => 'Kimyevi gübre, tarım ilacı'],
                    ['name' => 'Deterjan & Temizlik', 'code' => 'industry_chemical_5', 'emoji' => '🧽', 'description' => 'Endüstriyel deterjan, temizlik kimyasalı'],
                    ['name' => 'Petrokimya Ürünleri', 'code' => 'industry_chemical_6', 'emoji' => '⛽', 'description' => 'Petrol türevleri, yakıt katkıları'],
                ]
            ],
            [
                'name' => 'Enerji & Elektrik Üretimi',
                'code' => 'industry_energy',
                'emoji' => '⚡',
                'description' => 'Elektrik üretimi, enerji santrali, güç sistemleri',
                'subcategories' => [
                    ['name' => 'Elektrik Santrali', 'code' => 'industry_energy_1', 'emoji' => '🏭', 'description' => 'Termik, doğalgaz, kömür santrali'],
                    ['name' => 'Hidroelektrik Santral', 'code' => 'industry_energy_2', 'emoji' => '🌊', 'description' => 'Su gücü, baraj, hidroelektrik'],
                    ['name' => 'Güneş Enerji Santrali', 'code' => 'industry_energy_3', 'emoji' => '☀️', 'description' => 'Solar panel, güneş enerjisi'],
                    ['name' => 'Rüzgar Enerji Santrali', 'code' => 'industry_energy_4', 'emoji' => '💨', 'description' => 'Rüzgar türbini, eolian enerji'],
                    ['name' => 'Elektrik Ekipmanları', 'code' => 'industry_energy_5', 'emoji' => '🔌', 'description' => 'Trafo, switchgear, elektrik panosu'],
                    ['name' => 'Enerji Depolama', 'code' => 'industry_energy_6', 'emoji' => '🔋', 'description' => 'Batarya, enerji depolama sistemleri'],
                ]
            ],
            [
                'name' => 'Maden & Ham Madde',
                'code' => 'industry_mining',
                'emoji' => '⛏️',
                'description' => 'Maden çıkarma, ham madde üretimi, taş ocağı',
                'subcategories' => [
                    ['name' => 'Kömür Madeni', 'code' => 'industry_mining_1', 'emoji' => '⚫', 'description' => 'Linyit, taş kömürü çıkarma'],
                    ['name' => 'Metal Madeni', 'code' => 'industry_mining_2', 'emoji' => '🏗️', 'description' => 'Demir, bakır, altın madenciliği'],
                    ['name' => 'Taş Ocağı & Mermer', 'code' => 'industry_mining_3', 'emoji' => '🪨', 'description' => 'Mermer, granit, doğal taş'],
                    ['name' => 'Çimento Hammaddesi', 'code' => 'industry_mining_4', 'emoji' => '🏭', 'description' => 'Kireç taşı, kil, çimento hammadde'],
                    ['name' => 'İnşaat Malzemesi', 'code' => 'industry_mining_5', 'emoji' => '🧱', 'description' => 'Kum, çakıl, agrega üretimi'],
                    ['name' => 'Endüstriyel Mineraller', 'code' => 'industry_mining_6', 'emoji' => '💎', 'description' => 'Bor, kaolin, feldispat'],
                ]
            ],
            [
                'name' => 'Otomotiv Yan Sanayi',
                'code' => 'industry_automotive',
                'emoji' => '🚗',
                'description' => 'Araç parça üretimi, OEM, otomotiv sanayii',
                'subcategories' => [
                    ['name' => 'Motor & Şanzıman Parçaları', 'code' => 'industry_automotive_1', 'emoji' => '🔧', 'description' => 'Motor bloğu, şanzıman, vites kutusu'],
                    ['name' => 'Fren & Süspansiyon', 'code' => 'industry_automotive_2', 'emoji' => '🛞', 'description' => 'Fren balata, disk, amortisör'],
                    ['name' => 'Elektrik & Elektronik', 'code' => 'industry_automotive_3', 'emoji' => '⚡', 'description' => 'ECU, sensör, kablo demeti'],
                    ['name' => 'Kaportaj & Döküm', 'code' => 'industry_automotive_4', 'emoji' => '🚙', 'description' => 'Kaportaj parça, döküm parça'],
                    ['name' => 'İç & Dış Aksesuar', 'code' => 'industry_automotive_5', 'emoji' => '🪑', 'description' => 'Koltuk, torpido, plastik aksesuar'],
                    ['name' => 'Lastik & Kauçuk', 'code' => 'industry_automotive_6', 'emoji' => '🛞', 'description' => 'Lastik üretimi, kauçuk parça'],
                ]
            ]
        ];
        
        // Yeni alt kategorileri ve subcategory'lerini oluştur
        foreach ($newIndustrialCategories as $category) {
            \Log::info("Yeni sanayi kategorisi oluşturuluyor", ['name' => $category['name']]);
            
            $mainCat = AIProfileSector::create([
                'name' => $category['name'],
                'code' => $category['code'],
                'emoji' => $category['emoji'],
                'description' => $category['description'],
                'keywords' => $category['description'] . ' sanayi endüstri üretim',
                'category_id' => $sanayiCategory->id,
                'is_active' => true,
                'sort_order' => 20
            ]);
            
            // Alt kategorilerin subcategory'lerini oluştur
            if (isset($category['subcategories'])) {
                foreach ($category['subcategories'] as $index => $sub) {
                    AIProfileSector::create([
                        'name' => $sub['name'],
                        'code' => $sub['code'],
                        'emoji' => $sub['emoji'],
                        'description' => $sub['description'],
                        'keywords' => $sub['description'] . ' ' . $category['description'],
                        'category_id' => $mainCat->id,
                        'is_active' => true,
                        'sort_order' => ($index + 1) * 10
                    ]);
                }
                
                \Log::info("Subcategory'ler oluşturuldu", [
                    'parent' => $category['name'],
                    'subcategory_count' => count($category['subcategories'])
                ]);
            }
        }
        
        \Log::info("AIProfileSanayiSeeder tamamlandı", [
            'sanayi_category_id' => $sanayiCategory->id,
            'moved_categories' => count($categoriesToMove),
            'new_categories' => count($newIndustrialCategories)
        ]);
    }
}