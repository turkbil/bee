<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX3_301_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFX3-301';
        $titleTr = 'İXTİF EFX3 301 - 3.0 Ton Li-Ion Denge Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '3000 kg kapasiteli, 500 mm yük merkezi ve 1650 mm dingil mesafesine sahip kompakt gövde; 2250 mm dönüş yarıçapı ile dar alanlarda çevik. PMSM tahrik (S2 60 dk 8 kW) ve 80V/150Ah Li‑Ion batarya, fırsat şarjı ve tek faz 16A entegre şarj ile çok vardiyada verim sunar. 3000 mm kaldırma, 11/12 km/s hız ve su korumalı yapı, iç/dış kullanım için idealdir.'], JSON_UNESCAPED_UNICODE),
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
                    "Sürüş": "Elektrikli",
                    "Kapasite (Q)": "3000 kg",
                    "Yük Merkezi (c)": "500 mm",
                    "Servis Ağırlığı": "4255 kg",
                    "Teker Tipi": "Dolgu (Solid rubber)",
                    "Ön Teker Ebat": "7.00-12",
                    "Arka Teker Ebat": "18×7-8",
                    "Dingil Mesafesi (y)": "1650 mm",
                    "Direk Eğim (α/β)": "6° / 10°",
                    "Direk Kapalı Yükseklik (h1)": "2070 mm",
                    "Serbest Kaldırma (h2)": "135 mm",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Açık Yükseklik (h4)": "4095 mm",
                    "Kabin Yüksekliği (h6)": "2170 mm",
                    "Oturma Yüksekliği (h7)": "1110 mm",
                    "Toplam Uzunluk (l1)": "3611 mm",
                    "Çatala Kadar Uzunluk (l2)": "2541 mm",
                    "Toplam Genişlik (b1/b2)": "1154 mm",
                    "Çatal Ölçüsü (s/e/l)": "50×122×1070 mm",
                    "Çatal Sınıfı": "ISO 2A",
                    "Çatal Taşıyıcı Genişliği (b3)": "1040 mm",
                    "Ön İz Genişliği": "975 mm",
                    "Arka İz Genişliği": "955 mm",
                    "Zemin Boşluğu (mast/orta)": "120 / 150 mm",
                    "Koridor Genişliği 1000×1200 (Ast)": "3946 mm",
                    "Koridor Genişliği 800×1200 (Ast)": "4146 mm",
                    "Dönüş Yarıçapı (Wa)": "2250 mm",
                    "Yürüyüş Hızı (yük/boş)": "11/12 km/s",
                    "Kaldırma Hızı (yük/boş)": "0.28/0.36 m/s",
                    "İndirme Hızı (yük/boş)": "0.4/0.43 m/s",
                    "Maks. Eğim (yük/boş)": "15% / 15%",
                    "Sürüş Motoru (S2 60dk)": "8 kW",
                    "Kaldırma Motoru (S3 15%)": "16 kW",
                    "Batarya": "80V/150Ah",
                    "Opsiyon Batarya": "80V/280Ah (çıkarılabilir, yandan çekme)",
                    "Entegre Şarj": "Tek faz, 16A fiş (fırsat şarj)",
                    "Su Korumasi": "Dış mekân kullanımına uygun (yağmurda çalışma)"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "80V Li‑Ion batarya ile fırsat şarjı ve sıfır bakım"
                    },
                    {
                        "icon": "bolt",
                        "text": "PMSM tahrik ile %10’a kadar enerji tasarrufu ve uzun çalışma süresi"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ve 2250 mm dönüş yarıçapı ile dar alan çevikliği"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Suya karşı korumalı tasarım; iç/dış kullanım güveni"
                    },
                    {
                        "icon": "cog",
                        "text": "Yanlamasına çekilip değişebilen çıkarılabilir batarya modülü"
                    },
                    {
                        "icon": "star",
                        "text": "Ergonomik bölge: ayarlanabilir direksiyon ve rahat pedal düzeni"
                    },
                    {
                        "icon": "industry",
                        "text": "Yüksek yerden yükseklik ve iri lastikler ile bozuk zeminde ilerleme"
                    },
                    {
                        "icon": "plug",
                        "text": "Tek faz 16A entegre şarj cihazı ile kolay besleme"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master eklendi: {$sku }");
    }
}
