<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CPD20FVL_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'CPD20FVL';
        $titleTr = 'İXTİF CPD20FVL - 2.0 Ton Li-Ion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2.0 ton kapasiteli, 80V/205Ah Li-Ion enerji sistemi ve 80V/35A entegre şarj cihazı ile çok vardiyalı çalışmaya hazır. Çift tahrikli 2×5.0 kW AC yapı, 1730 mm dönüş yarıçapı ve 1170 mm genişlikle dar alanlarda çevik manevra sağlar. Standart kaldırma 3000 mm, hız 13/14 km/s.'], JSON_UNESCAPED_UNICODE),
            'base_price' => 0.00,
            'price_on_request' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'is_master_product' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(json_decode(<<<'JSON'
                {
                    "Kapasite (Q)": "2000 kg",
                    "Yük Merkezi Mesafesi (c)": "500 mm",
                    "Servis Ağırlığı": "3270 kg",
                    "Sürüş Tipi": "Elektrikli, oturmalı",
                    "Tahrik": "4 teker, çift tahrik AC",
                    "Sürüş Motoru": "2×5.0 kW AC",
                    "Batarya": "80V/205Ah Li-Ion",
                    "Şarj Cihazı": "80V/35A entegre şarj cihazı",
                    "Dönüş Yarıçapı (Wa)": "1730 mm",
                    "Yüke kadar hız (dolu/boş)": "13/14 km/s",
                    "Kaldırma Hızı (dolu/boş)": "0.33/0.45 m/s",
                    "İndirme Hızı (dolu/boş)": "0.4/0.44 m/s",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Tavan Üstü Yüksekliği (h4)": "4055 mm",
                    "Çatala kadar uzunluk (l2)": "2150 mm",
                    "Toplam Genişlik (b1/b2)": "1170 mm",
                    "Çatal Ölçüleri (s/e/l)": "122×40×1070 mm",
                    "Teker Tipi": "Katı lastik",
                    "Mast Kapalı Yükseklik (h1)": "2075 mm",
                    "Hızlı Şarj / Fırsat Şarj": "Uygun (Li-Ion)"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "Çift tahrikli AC motorlar ile güçlü ivmelenme (2×5.0 kW)"
                    },
                    {
                        "icon": "battery-full",
                        "text": "80V/205Ah Li-Ion batarya ile uzun ömür ve fırsat şarjı"
                    },
                    {
                        "icon": "plug",
                        "text": "Tek faz 80V/35A entegre şarj cihazı standart"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ve küçük dönüş yarıçapı (1730 mm) ile çeviklik"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Güçlendirilmiş mast ile kararlılık ve yüksek görüş"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Ayarlanabilir direksiyon ve konforlu kova tip koltuk"
                    },
                    {
                        "icon": "industry",
                        "text": "Çok vardiyalı kullanım senaryolarına uygun tasarım"
                    },
                    {
                        "icon": "star",
                        "text": "Bakım gerektirmeyen Li-Ion teknolojiyle düşük TCO"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
