<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES12_25WA_Istif_1_Master extends Seeder {
    public function run(): void {
        $sku = 'ES12-25WA';
        $titleTr = 'İXTİF ES12-25WA - 1.2 Ton Yürüyüş Tipi İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1.200 kg kapasite, 24V/210Ah enerji, 3.280 mm azami kaldırma ve 5.0/5.5 km/s seyir hızlarıyla kompakt depolarda güvenli istifleme sunar. Çift kademeli indirme hızı, PU tekerlekler ve elektromanyetik fren ile hassas kontrol ve düşük bakım maliyeti sağlar.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 3,
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
                    "Üretici": "İXTİF",
                    "Model": "ES12-25WA",
                    "Sürüş": "Elektrik",
                    "Operatör Tipi": "Yaya (Pedestrian)",
                    "Kapasite (Q)": "1200 kg",
                    "Yük Merkezi Mesafesi (c)": "600 mm",
                    "Tahrik Aksından Çatala Mesafe (x)": "618 mm",
                    "Dingil Mesafesi (y)": "1320 mm",
                    "Servis Ağırlığı": "1150 kg",
                    "Aks Yükü, Yüklü (ön/arka)": "685 / 1665 kg",
                    "Aks Yükü, Yüksüz (ön/arka)": "665 / 485 kg",
                    "Tekerlek Tipi": "Poliüretan (PU)",
                    "Sürüş Tekerleği (ön)": "Φ230×75 mm",
                    "Yük Tekerleği (arka)": "Φ102×50 mm (çift)",
                    "Tekerlek Dizilimi": "1x+0/4",
                    "Arka İz Genişliği (b11)": "1045 / 1145 / 1245 / 1345 mm",
                    "Maks. Kaldırma Yüksekliği (H)": "3280 mm",
                    "Direk Kapalı Yüksekliği (h1)": "2110 mm",
                    "Serbest Kaldırma (h2)": "150 mm",
                    "Nominal Kaldırma (h3)": "3220 mm",
                    "Direk Açık Yüksekliği (h4)": "4185 mm",
                    "Tiller Yüksekliği (min/maks, h14)": "715 / 1200 mm",
                    "Tekerlek Kolları Yüksekliği (h8)": "100 mm",
                    "Kapalı Çatal Yüksekliği (h13)": "60 mm",
                    "Toplam Uzunluk (l1)": "1947 mm",
                    "Yüze Kadar Uzunluk (l2)": "877 mm",
                    "Toplam Genişlik (b1/b2)": "1120 / 1220 / 1320 / 1420 mm",
                    "Çatal Ölçüleri (s/e/l)": "40 / 100 / 1070 mm",
                    "Taşıyıcı Genişliği (b3)": "800 mm",
                    "Çatallar Arası Mesafe (b5)": "200–760 mm",
                    "Tekerlek Kolları Arası (b4)": "970 / 1070 / 1170 / 1270 mm",
                    "Şasi Altı Yerden Yükseklik (m1)": "78 mm",
                    "Dingil Orta Yerden Yükseklik (m2)": "60 mm",
                    "Koridor Genişliği 1000×1200 (Ast)": "2405 mm",
                    "Koridor Genişliği 800×1200 (Ast)": "2400 mm",
                    "Dönüş Yarıçapı (Wa)": "1490 mm",
                    "Seyir Hızı (yüklü/yüksüz)": "5.0 / 5.5 km/s",
                    "Kaldırma Hızı (yüklü/yüksüz)": "0.127 / 0.23 m/s",
                    "İndirme Hızı (yüklü/yüksüz)": "0.26 / 0.20 m/s (iki kademeli)",
                    "Tırmanma Kabiliyeti (yüklü/yüksüz)": "8% / 16%",
                    "Fren": "Elektromanyetik",
                    "Sürüş Motoru (S2 60dk)": "1.1 kW",
                    "Kaldırma Motoru (S3 15%)": "2.2 kW",
                    "Batarya (V/Ah)": "24V / 210Ah (kurşun-asit, Li-ion opsiyonel)",
                    "Batarya Ağırlığı": "160 kg",
                    "Tahrik Kontrolü": "AC",
                    "Direksiyon": "Mekanik",
                    "Operatör Kulak Seviyesi Gürültü": "74 dB(A)"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "arrows-alt",
                        "text": "İki kademeli indirme ile istifte yüksek stabilite ve hassasiyet"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V/210Ah enerji sistemi, Li-ion seçenekle hızlı şarj entegrasyonu"
                    },
                    {
                        "icon": "circle-notch",
                        "text": "PU tekerlekler ve kompakt şasi ile dar koridor çevikliği"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik servis freni ile güvenli duruş"
                    },
                    {
                        "icon": "gauge",
                        "text": "5.0/5.5 km/s seyir, 0.127/0.23 m/s kaldırma hızları"
                    },
                    {
                        "icon": "cog",
                        "text": "Mekanik direksiyon ve basit mimariyle düşük bakım ihtiyacı"
                    },
                    {
                        "icon": "layer-group",
                        "text": "Farklı şasi ve direk ölçüleriyle geniş konfigürasyon"
                    },
                    {
                        "icon": "bolt",
                        "text": "AC sürüş kontrolü ile akıcı hızlanma"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
