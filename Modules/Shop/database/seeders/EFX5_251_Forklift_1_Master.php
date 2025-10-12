<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX5_251_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFX5-251';
        $titleTr = 'İXTİF EFX5 251 - 2. Ton Li-Ion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2. ton kapasiteli, 80V Li-Ion modüler akü (80V/280Ah (tek), 80V/560Ah (çift)), 16/17 km/s seyir, 0.38/0.45 m/s kaldırma ve 17/25% eğim performansı. 150 mm yerden yükseklik, geniş lastikler ve artırılmış görüşlü yeni direk ile 2270 mm dönüş yarıçapında verimli ve güvenli çalışma.'], JSON_UNESCAPED_UNICODE),
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
                    "Model": "EFX5-251",
                    "Kapasite (Q)": "2500 kg",
                    "Yük Merkezi (c)": "500 mm",
                    "Servis Ağırlığı": "3835 kg",
                    "Operatör Tipi": "Oturmalı",
                    "Sürüş": "Elektrik",
                    "Teker Tipi": "Pnömatik",
                    "Aks Mesafesi (y)": "1685 mm",
                    "Tahrik Aksına Uzaklık (x)": "—",
                    "Ön İz Genişliği": "975 mm",
                    "Arka İz Genişliği": "955 mm",
                    "Direk Eğimi (Ön/Arka)": "6° / 10°",
                    "Direk Kapalı Yükseklik (h1)": "2090 mm",
                    "Serbest Kaldırma (h2)": "135–195 mm (opsiyona bağlı)",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Açık Yükseklik (h4)": "4065 mm",
                    "Üst Koruma Yüksekliği": "2160–2165 mm",
                    "Koltuk Yüksekliği": "1095 mm",
                    "Toplam Uzunluk (l1)": "—",
                    "Çatala Kadar Uzunluk (l2)": "2520 mm",
                    "Toplam Genişlik (b1/b2)": "1154 mm",
                    "Çatal Ölçüleri (s×e×l)": "40×122×1070 mm",
                    "Dönüş Yarıçapı (Wa)": "2270 mm",
                    "Koridor Genişliği 1000×1200 (Ast)": "3965 mm",
                    "Koridor Genişliği 800×1200 (Ast)": "4165 mm",
                    "Seyir Hızı (Yüklü/Boş)": "16/17 km/s",
                    "Kaldırma Hızı (Yüklü/Boş)": "0.38/0.45 m/s",
                    "İndirme Hızı (Yüklü/Boş)": "0.45/0.52 m/s",
                    "Maks. Tırmanma (Yüklü/Boş)": "17/25%",
                    "Sürüş Motoru (S2 60dk)": "16 kW",
                    "Kaldırma Motoru (S3 15%)": "24 kW",
                    "Akü": "80V/280Ah (tek), 80V/560Ah (çift)",
                    "Tahrik Kontrol": "AC",
                    "Direksiyon": "Hidrolik",
                    "Servis Freni": "Hidrolik",
                    "Park Freni": "Mekanik",
                    "Gürültü Seviyesi": "≤74 dB(A)",
                    "Ön Lastik": "7.00-12-12PR",
                    "Arka Lastik": "18×7-8-14PR",
                    "Yer Açıklığı (Merkez)": "150 mm",
                    "Yer Açıklığı (Direk Altı)": "100–120 mm"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "Modüler Li-Ion akü mimarisi: 80V 280Ah, çift modülle 560Ah’a yükseltilebilir"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Farklı iş yüklerine göre kolay kapasite uyarlaması (tek/çift batarya)"
                    },
                    {
                        "icon": "warehouse",
                        "text": "150 mm yerden yükseklik ve büyük lastikler ile engebeli zeminlerde akıcı sürüş"
                    },
                    {
                        "icon": "star",
                        "text": "Yüksek görüşlü yeni triplex direk ve geniş pedal ergonomisi"
                    },
                    {
                        "icon": "bolt",
                        "text": "16 kW AC sürüş ve 24 kW kaldırma motoru ile hızlı tepki"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Hidrolik servis freni ve mekanik park freni ile güvenli duruş"
                    },
                    {
                        "icon": "building",
                        "text": "Geliştirilmiş LED aydınlatma ve geniş ekranlı gösterge"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Yoğun vardiyalarda fırsat şarjı ile kesintisiz operasyon"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master eklendi: {$sku}");
    }
}
