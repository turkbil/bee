<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES18_40WA_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3;
        $brandId = 1;
        $sku = 'ES18-40WA';
        $titleTr = 'İXTİF ES18-40WA - 1.8 Ton Elektrikli Geniş Bacaklı İstif';
        $shortTr = 'ES18-40WA, 1.8 ton kapasiteli, geniş bacaklı (straddle) tasarımıyla farklı palet ölçülerine uyum sağlar. Kaplumbağa hızıyla dar alanlarda hassas manevra, oransal kaldırma ve AC dikey çekiş motoru sunar. 24V/280Ah akü ve Li‑Ion seçeneğiyle esnek, güvenli ve verimli operasyon.';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
            'category_id' => $categoryId,
            'brand_id' => $brandId,
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
                    "Model": "ES18-40WA",
                    "Sürüş": "Elektrik",
                    "Operatör Tipi": "Yaya (Pedestrian)",
                    "Kapasite": "1800 kg",
                    "Yük Merkez Mesafesi (c)": "600 mm",
                    "Tahrik Aksı Merkezine Kadar Yük Mesafesi (x)": "610 mm",
                    "Dingil Mesafesi (y)": "1455 mm",
                    "Servis Ağırlığı": "1520 kg",
                    "Ön/Araç Aks Yükü (yüklü)": "945/2375 kg",
                    "Ön/Araç Aks Yükü (yüksüz)": "925/595 kg",
                    "Lastik Tipi": "Poliüretan",
                    "Tahrik Tekerleği Boyutu (ön)": "Ø230×75 mm",
                    "Yük Tekerleği Boyutu (arka)": "Ø102×73 mm",
                    "Destek Tekerleği": "Ø102×63.5 mm",
                    "Tekerlek Düzeni (ön/arka)": "1x+2/4",
                    "Ön İz Genişliği (b10)": "580 mm",
                    "Arka İz Genişliği (b11)": "1170/1270/1370 mm",
                    "Maks. Kaldırma Yüksekliği (H)": "3200 mm",
                    "Direk Kapalı Yüksekliği (h1)": "2118 mm",
                    "Serbest Kaldırma (h2)": "150 mm",
                    "Direk Kaldırma Yüksekliği (h3)": "3135 mm",
                    "Direk Açık Yüksekliği (h4)": "4115 mm",
                    "Tiller Yüksekliği (min/max) (h14)": "990/1500 mm",
                    "Teker Kolları Yüksekliği (h8)": "100 mm",
                    "İnmiş Çatal Yüksekliği (h13)": "65 mm",
                    "Toplam Uzunluk (l1)": "2092 mm",
                    "Yüze Kadar Uzunluk (l2)": "1022 mm",
                    "Toplam Genişlik (b1/b2)": "1270/1370/1470 mm",
                    "Çatal Ölçüleri (s/e/l)": "45/100/1070 mm (ops. 920/1150/1220)",
                    "Taşıyıcı Genişliği (b3)": "800 mm",
                    "Çatallar Arası Mesafe (b5)": "200–760 mm",
                    "Teker Kolları Arası Mesafe (b4)": "1070/1170/1270 mm",
                    "Şase Altı Açıklık (m1)": "81 mm",
                    "Şase Orta Açıklık (m2)": "50 mm",
                    "Koridor Genişliği 1000×1200 (Ast)": "2560 mm",
                    "Koridor Genişliği 800×1200 (Ast)": "2560 mm",
                    "Dönüş Yarıçapı (Wa)": "1645 mm",
                    "Sürüş Hızı (yüklü/boş)": "4.5 / 5.0 km/s",
                    "Kaldırma Hızı (yüklü/boş)": "0.127 / 0.23 m/s",
                    "İndirme Hızı (yüklü/boş)": "0.26 / 0.20 m/s",
                    "% Eğim (yüklü/boş)": "6 / 10 %",
                    "Servis Freni": "Elektromanyetik",
                    "Sürüş Motoru (S2 60 dk)": "1.1 kW AC",
                    "Kaldırma Motoru (S3 15%)": "3.0 kW",
                    "Akü Gerilim/Kapasite": "24V / 280Ah (ops. 205Ah Li‑Ion / 360Ah)",
                    "Akü Ağırlığı": "250 kg",
                    "Sürüş Kontrolü": "AC",
                    "Direksiyon Tasarımı": "Mekanik",
                    "Gürültü Seviyesi (kulak)": "74 dB(A)",
                    "Mast Opsiyonları": "Standart/Serbest kaldırma; 3000–5000 mm h3 seçenekleri",
                    "Fork Seçenekleri": "1070 (●), 1150/1220/920 (○)",
                    "Şarj Cihazı": "24V-30A/50A harici; 24V-100A Li‑Ion opsiyon",
                    "Zamanlayıcı/Bluetooth": "Zamanlayıcı ●, Bluetooth ○"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "industry",
                        "text": "Geniş bacaklı (straddle) tasarım ile farklı palet ve konteyner tiplerine uyum"
                    },
                    {
                        "icon": "gauge",
                        "text": "Kaplumbağa hızı modu sayesinde dar koridorlarda üstün manevra"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Oransal kaldırma ile milimetrik ve kontrollü istifleme"
                    },
                    {
                        "icon": "bolt",
                        "text": "Güçlü AC dikey çekiş motoru ve 3.0 kW kaldırma motoru"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V/280Ah akü; Li‑Ion seçenekle hızlı şarj ve kesintisiz vardiya"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik servis freni ile güvenli duruş"
                    },
                    {
                        "icon": "circle-notch",
                        "text": "Poliüretan tekerleklerle sessiz ve düşük titreşimli sürüş"
                    },
                    {
                        "icon": "layer-group",
                        "text": "Farklı mast yüksekliği ve çatal uzunluğu opsiyonları"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
