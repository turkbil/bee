<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX0_Siparis_1_Master extends Seeder {
    public function run(): void {
        $sku = 'JX0';
        $titleTr = 'İXTİF JX0 - 200 kg Elektrikli Sipariş Toplayıcı';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'JX0 platformundan gelen sezgisel sürüş, 180° yönlendirme ve 1260 mm dönüş yarıçapı ile dar koridorlarda hızlı toplama sağlar. 3615 mm kaldırma, 4090 mm maksimum direk yüksekliği ve 24V 135Ah Li-Ion (24V) aküyle fırsat şarjı; 6/6.5 km/saat hız ve sensörlü güvenlik sistemi sunar.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 4,
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
                    "Sürüş": "Elektrik",
                    "Kapasite (Q)": "200 kg",
                    "Tekerlek Tabanı (y)": "1095 mm",
                    "Servis Ağırlığı": "800 kg",
                    "Direk (çekili) Yükseklik (h1/h2/h3*)": "1375 mm (çekili), 3615 mm kaldırma",
                    "Maksimum Direk Yüksekliği (h4)": "4090 mm",
                    "Üst Koruma/ Kabin Yüksekliği (h6)": "1425 mm",
                    "Platform Yükselti (h12)": "3000 mm (operator stand yüksekliği, yükseltilmiş)",
                    "Toplam Uzunluk (l2)": "1440 mm",
                    "Toplam Genişlik (b1/b2)": "750 mm",
                    "Dönüş Yarıçapı (Wa)": "1260 mm",
                    "Sürüş Hızı (yüklü/boş)": "6 / 6.5 km/saat",
                    "Kaldırma Hızı (yüklü/boş)": "0.22 / 0.27 m/s",
                    "İndirme Hızı (yüklü/boş)": "0.31 / 0.25 m/s",
                    "Maksimum Eğim (yüklü/boş)": "5% / 8%",
                    "Sürüş Motoru (S2 60dk)": "0.65 kW",
                    "Kaldırma Motoru (S3 15%)": "2.2 kW",
                    "Batarya (V/Ah)": "135Ah Li-Ion (24V)",
                    "Lastik Tipi": "Poliüretan / Katı",
                    "Ön Tekerlek": "φ210×70",
                    "Arka Tekerlek": "φ250×100",
                    "Destek Tekerleri": "φ74×48",
                    "Aks Yükleri (yüklü ön/arka)": "673 / 467 kg",
                    "Aks Yükleri (boş ön/arka)": "380 / 420 kg"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "hand",
                        "text": "Mini direksiyon ve hassas hız yön kumandaları ile sezgisel sürüş"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt yapı ve 1260 mm dönüş yarıçapı ile dar koridor çevikliği"
                    },
                    {
                        "icon": "box-open",
                        "text": "Elektrik tahrikli yüksek kapasiteli yük tepsisi ile verimli toplama"
                    },
                    {
                        "icon": "couch",
                        "text": "Geniş operatör bölmesi ve rahat kapaklarla ergonomik kullanım"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Otomatik kapı kilidi, operatör sensörleri ve mavi uyarı ışığı"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V Li-Ion seçenek ile bakım gerektirmeyen enerji ve fırsat şarjı"
                    },
                    {
                        "icon": "gauge",
                        "text": "Kaldırma yüksekliğine göre otomatik hız düzenleme"
                    },
                    {
                        "icon": "star",
                        "text": "Standart buzzer ve blue spot ile yüksek güvenlik görünürlüğü"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master: {$sku }");
    }
}
