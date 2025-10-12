<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES12_12WA_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3;
        $brandId = 1;
        $sku = 'ES12-12WA';
        $titleTr = 'İXTİF ES12-12WA - 1.2 Ton Elektrikli Yaya Tipi İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1200 kg kapasite, 600 mm yük merkezi ve 5.0 / 5.5 km/s (yüklü/boş) seyir hızıyla dar koridorlarda çevik istifleme. 800 mm gövde genişliği, 1463 mm dönüş yarıçapı ve 24V 210Ah (190 kg) enerji sistemiyle gün boyu verimli operasyon; iki kademeli indirme hassasiyeti.'], JSON_UNESCAPED_UNICODE),
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
                    "Kapasite": "1200 kg",
                    "Yük Merkez Mesafesi": "600 mm",
                    "Servis Ağırlığı": "955 kg",
                    "Tahrik": "Elektrikli (yaya kumandalı)",
                    "Teker Tipi": "Poliüretan sürüş / yük tekerlekleri",
                    "Ön Teker Ölçüsü": "Ø230 × 75 mm",
                    "Arka Teker Ölçüsü": "Ø85 × 70 mm (çift)",
                    "Ön İz Genişliği (b10)": "538 mm",
                    "Arka İz Genişliği (b11)": "380 / 515 mm",
                    "Aks Mesafesi (y)": "1315 mm",
                    "Toplam Uzunluk (l1)": "1826 mm",
                    "Çatala Kadar Uzunluk (l2)": "676 mm",
                    "Toplam Genişlik (b1/b2)": "800 mm",
                    "Maks. Kaldırma Yüksekliği (h3)": "3000 mm (standart)",
                    "Direk Kapalı Yükseklik (h1)": "1970 mm",
                    "Serbest Kaldırma (h2)": "100 mm",
                    "Direk Açık Yükseklik (h4)": "3420 mm",
                    "Şasi Altı Yerden Yükseklik (m2)": "28 mm",
                    "Koridor Genişliği 1000×1200 (Ast)": "2333 mm",
                    "Koridor Genişliği 800×1200 (Ast)": "2303 mm",
                    "Dönüş Yarıçapı (Wa)": "1463 mm",
                    "Seyir Hızı (yüklü/boş)": "5.0 / 5.5 km/s (yüklü/boş)",
                    "Kaldırma Hızı (yüklü/boş)": "0.10 / 0.16 m/s (yüklü/boş)",
                    "İndirme Hızı (yüklü/boş)": "0.19 / 0.18 m/s (yüklü/boş)",
                    "Tırmanma Kabiliyeti (yüklü/boş)": "8% / 16% (yüklü/boş)",
                    "Sürüş Motoru": "1.1 kW (S2 60 dk)",
                    "Kaldırma Motoru": "2.2 kW (S3 15%)",
                    "Akü": "24V 210Ah (190 kg)",
                    "Sürüş Kontrolü": "AC sürüş kontrolü",
                    "Fren Sistemi": "Elektromanyetik",
                    "Gürültü Seviyesi": "74 dB(A)",
                    "İki Kademeli İndirme": "İki kademeli indirme (yük istiflemede hassasiyet)"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "24V yüksek kapasiteli akü seçenekleri (210–230Ah) ile uzun vardiya dayanımı"
                    },
                    {
                        "icon": "bolt",
                        "text": "AC sürüş kontrolü ile dengeli hızlanma ve verimli tırmanma"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ve kısa dönüş yarıçapı ile dar koridor çevikliği"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik park freni ve akıllı hız kesme ile güvenli çalışma"
                    },
                    {
                        "icon": "weight-hanging",
                        "text": "1.2–1.6 ton aralığında nominal kapasite, 600 mm yük merkezi"
                    },
                    {
                        "icon": "warehouse",
                        "text": "Çok kademeli direk seçenekleri (2500–4800 mm) ile esnek istifleme"
                    },
                    {
                        "icon": "cog",
                        "text": "Basit mekanik direksiyon ve düşük bakım gerektiren tasarım"
                    },
                    {
                        "icon": "star",
                        "text": "İki kademeli indirme ile hassas ve sarsıntısız yük bırakma"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku} güncellendi/eklendi");
    }
}
