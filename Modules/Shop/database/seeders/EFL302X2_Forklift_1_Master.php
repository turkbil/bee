<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL302X2_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFL-302X2';
        $titleTr = 'İXTİF EFL302X2 - 3.0 Ton Li-Ion Denge Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '80V/100Ah Li-Ion akü ve PMSM tahrik ile %10-15 enerji tasarrufu, 11/12 km/h hız ve 2258 mm dönüş yarıçapı sunar. Suya dayanıklı tasarım, büyük lastikler ve oransal kaldırma sistemi ile iç/dış mekânda güvenilir performans sağlar.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(json_decode(<<<'JSON'
                {
                    "Kapasite": "3000 kg",
                    "Yük Merkez Mesafesi": "500 mm",
                    "Servis Ağırlığı": "4125 kg",
                    "Tahrik": "Elektrikli",
                    "Operatör Tipi": "Oturmalı",
                    "Tekerlek Tabanı (y)": "1650 mm",
                    "Direk Kapalı Yükseklik (h1)": "2060 mm",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Açık Yükseklik (h4)": "4095 mm",
                    "Çatala Kadar Uzunluk (l2)": "2514 mm",
                    "Toplam Genişlik (b1/b2)": "1154 mm",
                    "Çatal Ölçüleri (s/e/l)": "45×122×1070 mm",
                    "Dönüş Yarıçapı (Wa)": "2258 mm",
                    "Sürüş Hızı (yük/boş)": "11/12 km/h",
                    "Kaldırma Hızı (yük/boş)": "0.28/0.36 m/s",
                    "İndirme Hızı (yük/boş)": "0.4/0.43 m/s",
                    "Eğim Kabiliyeti (yük/boş)": "15/15 %",
                    "Sürüş Motoru (S2 60dk)": "8 kW (S2 60 dk)",
                    "Kaldırma Motoru (S3 15%)": "16 kW (S3 15%)",
                    "Akü (V/Ah)": "80V 100Ah Li-Ion",
                    "Sürüş Kontrolü": "PMSM",
                    "Direksiyon": "Hidrolik",
                    "Gürültü Seviyesi (dB(A))": "≤74",
                    "Lastikler": "Pneumatic (Ön: 7.00-12-16PR, Arka: 18x7-8-14PR)",
                    "Direk/Serbest Kaldırma Seçenekleri": "3000, 3300 mm standart; 4500-4800 mm serbest kaldırma seçenekleri"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "microchip",
                        "text": "Çekiş ve kaldırma motorlarında PMSM teknolojisi"
                    },
                    {
                        "icon": "battery-full",
                        "text": "80V/100Ah Li-Ion akü, 35A dahili şarj cihazı"
                    },
                    {
                        "icon": "plug",
                        "text": "Fişe tak-şarj: Dahili şarj cihazı ile fırsat şarjı kolaydır"
                    },
                    {
                        "icon": "seedling",
                        "text": "Sıfır bakım ve emisyonsuz yapı ile her yerde güvenle şarj"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Hassas istif için oransal kaldırma sistemi"
                    },
                    {
                        "icon": "couch",
                        "text": "Ayarlanabilir direksiyon, geniş diz alanı ve süspansiyonlu koltuk"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Suya dayanıklı tasarım, içeride/dışarıda güvenli kullanım"
                    },
                    {
                        "icon": "warehouse",
                        "text": "Büyük lastikler ve yüksek yerden açıklık ile zorlu zeminde stabilite"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
