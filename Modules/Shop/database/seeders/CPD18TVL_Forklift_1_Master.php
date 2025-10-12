<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CPD18TVL_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'CPD18TVL';
        $titleTr = 'İXTİF CPD18TVL - Li-Ion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'Dar alanlarda çevik, Li-Ion ile hızlı şarj ve düşük işletme maliyeti.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(json_decode(<<<'JSON'
                {
                    "Kapasite": "1800 kg",
                    "Yük Merkezi": "500 mm",
                    "Kaldırma Yüksekliği": "3000 mm",
                    "Batarya": "48 V / 360 Ah",
                    "Seyir Hızı": "12 km/h",
                    "Dönüş Yarıçapı": "1500 mm"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "Li-Ion batarya ile hızlı şarj ve kesintisiz vardiya verimliliği"
                    },
                    {
                        "icon": "bolt",
                        "text": "Bakım ihtiyacı düşük, fırçasız AC sürüş motoru"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Eğimde geri kaymayı önleyen rampa tutuş fonksiyonu"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ile dar koridorlarda yüksek manevra"
                    },
                    {
                        "icon": "star",
                        "text": "Operatör konforu için titreşim azaltan kabin düzeni"
                    },
                    {
                        "icon": "gauge",
                        "text": "Hassas hız kontrolü ve yumuşak kalkış"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Hızlı günlük kontroller için kolay erişimli servis kapakları"
                    },
                    {
                        "icon": "plug",
                        "text": "Harici şarj cihazı ile esnek şarj çözümleri"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
    }
}
