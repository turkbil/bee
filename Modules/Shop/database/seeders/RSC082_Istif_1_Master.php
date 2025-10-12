<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSC082_Istif_1_Master extends Seeder {
    public function run(): void {
        $sku = 'RSC082';
        $titleTr = 'İXTİF RSC082 - 0.8 Ton Elektrikli Karşı Ağırlıklı İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'RSC082, 800 kg kapasite ve 400 mm yük merkeziyle kompakt bir elektrikli karşı ağırlıklı istiftir. 24V/210Ah akü, 5.5/6 km/sa hız, 3000 mm kaldırma ve 1250 mm dönüş yarıçapı ile dar koridorlarda hassas ve güvenli istifleme sunar.'], JSON_UNESCAPED_UNICODE),
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
                    "Kapasite": "800 kg",
                    "Yük Merkez Mesafesi (c)": "400 mm",
                    "Sürüş": "Elektrikli, Yaya kumandalı",
                    "Servis Ağırlığı": "1760 kg",
                    "Tahrik Aksine Kadar Yük Mesafesi (x)": "118 mm",
                    "Dingil Mesafesi (y)": "810 mm",
                    "Teker Tipi": "Poliüretan",
                    "Ön Teker Ölçüsü": "230×90 mm",
                    "Arka Teker Ölçüsü": "180×65 mm",
                    "Teker Düzeni (Ön/Arka)": "1x/2",
                    "Arka İz Genişliği": "778 mm",
                    "Direk İleri/Geri Yatma (α/β)": "—",
                    "Direk Kapalı Yüksekliği (h1)": "2061 mm",
                    "Serbest Kaldırma (h2)": "—",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Açık Yüksekliği (h4)": "3908 mm",
                    "Ayakta Çalışma Yüksekliği": "148 mm",
                    "Toplam Uzunluk (l1)": "2404 mm",
                    "Çatala Kadar Uzunluk (l2)": "1334 mm",
                    "Toplam Genişlik (b1/b2)": "900 mm",
                    "Çatal Ölçüleri (s/e/l)": "35×100×1070 mm",
                    "Fork Klası": "2A",
                    "Fork Taşıyıcı Genişliği": "620 mm",
                    "Şasi Altı Boşluk (yük altında)": "60 mm",
                    "Dingil Merkezi Boşluk": "116 mm",
                    "Koridor Genişliği 1000×1200 (çapraz)": "2719 mm",
                    "Koridor Genişliği 800×1200 (boyuna)": "2828 mm",
                    "Dönüş Yarıçapı (Wa)": "1250 mm",
                    "Yürüyüş Hızı (yük/boş)": "5.5 / 6 km/sa",
                    "Kaldırma Hızı (yük/boş)": "0.13 / 0.20 m/sn",
                    "İndirme Hızı (yük/boş)": "0.16 / 0.15 m/sn",
                    "Maks. Eğim (yük/boş)": "—",
                    "Servis/Fren": "Elektromanyetik",
                    "Sürüş Motoru (S2 60dk)": "1.6 kW",
                    "Kaldırma Motoru (S3 15%)": "2.2 kW",
                    "Akü": "24V / 210Ah",
                    "Akü Ağırlığı": "190 kg",
                    "Sürüş Kontrolü": "AC",
                    "Direksiyon": "Elektronik",
                    "Ses Basınç Seviyesi": "74 dB(A)",
                    "Şarj Cihazı Akımı": "30 A"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "arrows-alt",
                        "text": "1250 mm dönüş yarıçapı ile dar alanlarda yüksek manevra kabiliyeti"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V/210Ah akü ile uzun vardiyalarda kesintisiz çalışma"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik fren ve elektronik direksiyon ile güvenli kontrol"
                    },
                    {
                        "icon": "bolt",
                        "text": "1.6 kW AC sürüş ve 2.2 kW kaldırma motoru ile dengeli performans"
                    },
                    {
                        "icon": "industry",
                        "text": "Karşı ağırlıklı şasi ile çeşitli palet ve kutularda evrensel kullanım"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3000 mm kaldırma yüksekliği ile çok katlı raf erişimi"
                    },
                    {
                        "icon": "cog",
                        "text": "PU tekerlekler sayesinde düşük bakım ve sessiz çalışma"
                    },
                    {
                        "icon": "star",
                        "text": "Kompakt boyutlar, operatör platformu opsiyonu ve hassas kaldırma"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master eklendi: {$sku}");
    }
}
