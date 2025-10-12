<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WSA121_Istif_1_Master extends Seeder {
    public function run(): void {
        $sku = 'WSA121';
        $titleTr = 'İXTİF WSA121 - 1.0 Ton Li-Ion İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'WSA serisi, Li-Ion teknolojisi ve entegre şarj ile dar alanlarda yüksek verim sunan ağır hizmet yaya kumandalı istifleyicidir. 1200 kg kapasite, 714 mm l2, 1446 mm dönüş yarıçapı ve 5/5.5 km/s seyir hızlarıyla hızlı ve hassas istif sağlar; 24V/100Ah batarya ile esnek şarj imkanına sahiptir.'], JSON_UNESCAPED_UNICODE),
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
                    "Sürüş Tipi": "Elektrikli",
                    "Kapasite (Q)": "1200 kg",
                    "Yük Merkezi (c)": "600 mm",
                    "Servis Ağırlığı": "955 kg",
                    "Direk Kapalı Yüksekliği (h1)": "1970 mm",
                    "Kaldırma Yüksekliği (h3)": "2915 mm",
                    "Çatala Kadar Uzunluk (l2)": "714 mm",
                    "Toplam Genişlik (b1/b2)": "810 mm",
                    "Çatal Ölçüleri (s/e/l)": "65×170×1150 mm",
                    "Dönüş Yarıçapı (Wa)": "1446 mm",
                    "Eğim Kabiliyeti (yüklü/boş)": "8% / 16%",
                    "Seyir Hızı (yüklü/boş)": "5/5.5 km/s",
                    "Kaldırma Hızı (yüklü/boş)": "0.23/0.3 m/s",
                    "İndirme Hızı (yüklü/boş)": "0.4/0.36 m/s",
                    "Fren": "Elektromanyetik",
                    "Sürüş Motoru": "AC, 1.6 kW (S2 60 dk)",
                    "Kaldırma Motoru": "4.5 kW (S3 15%)",
                    "Batarya": "24V/100Ah"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "Li-Ion batarya mimarisi; hızlı ve esnek şarj, sıfır bakım"
                    },
                    {
                        "icon": "bolt",
                        "text": "Yüksek kaldırma/indirme hızlarıyla hızlı istif performansı"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ve 1446 mm dönüş yarıçapı ile dar alan çevikliği"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik fren ve kontrollü indirme ile güvenlik"
                    },
                    {
                        "icon": "star",
                        "text": "Oransal kaldırma sistemi ile milimetrik hassas konumlama"
                    },
                    {
                        "icon": "cog",
                        "text": "Güçlü şasi ve servis dostu tasarım ile düşük TCO"
                    },
                    {
                        "icon": "building",
                        "text": "Geliştirilmiş kapak; evrak cebi ve USB çıkışı ile pratik kullanım"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Depo raflarında hızlı istif ve yüksek devir verimliliği"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info('✅ Master: WSA121 oluşturuldu');
    }
}
