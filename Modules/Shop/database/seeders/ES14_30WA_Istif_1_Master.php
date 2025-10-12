<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES14_30WA_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES14-30WA';
        $titleTr = 'İXTİF ES14-30WA - 1.4 Ton Elektrikli Geniş Bacaklı İstif Makinesi';
        $shortTr = 'ES14-30WA; 1400 kg kapasite, 600 mm yük merkezi, ayarlanabilir geniş bacak (straddle) tasarım, 24V/210Ah akü (Li-ion opsiyon), 5.5/6.0 km/s seyir, 0.127/0.23 m/s kaldırma, 1545 mm dönüş yarıçapı ve elektromanyetik fren ile dar alanlarda hassas istif performansı sunar.';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
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

            'technical_specs' => json_encode([
                'Model' => 'ES14-30WA',
                'Sürüş' => 'Elektrik (Yaya kumandalı)',
                'Kapasite (Q)' => '1400 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik aksına mesafe (x)' => '610 mm',
                'Dingil mesafesi (y)' => '1350 mm',
                'Servis Ağırlığı' => '1320 kg',
                'Ön/Araç dingil yükü (yüklü)' => '815/1905 kg',
                'Ön/Araç dingil yükü (yüksüz)' => '805/515 kg',
                'Lastik tipi' => 'Poliüretan',
                'Tahrik tekeri boyutu (ön)' => 'Φ230×75 mm',
                'Yük tekeri boyutu (arka)' => 'Φ102×73 mm',
                'Ek tekerlek (caster)' => 'Φ85×48 mm',
                'Tekerlek adedi (ön/arka)' => '1x+2/4',
                'Ön iz genişliği (b10)' => '580 mm',
                'Arka iz genişliği (b11)' => '1170/1270/1370 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3200 mm',
                'Direk kapalı yükseklik (h1)' => '2118 mm',
                'Serbest kaldırma (h2)' => '150 mm',
                'Kaldırma yüksekliği (h3)' => '3140 mm',
                'Direk açık yükseklik (h4)' => '4115 mm',
                'Tiller min./max. (h14)' => '990/1500 mm',
                'Tekerlek kolları yüksekliği (h8)' => '100 mm',
                'Çatal altı yükseklik (h13)' => '60 mm',
                'Toplam uzunluk (l1)' => '1987 mm',
                'Yüze kadar uzunluk (l2)' => '917 mm',
                'Toplam genişlik (b1/b2)' => '1270/1370/1470 mm',
                'Çatal ölçüleri (s/e/l)' => '40/100/1070 mm',
                'Taşıyıcı genişliği (b3)' => '800 mm',
                'Çatallar arası mesafe (b5)' => '200–760 mm',
                'Tekerlek kolları arası (b4)' => '1070/1170/1270 mm',
                'Mast altı yerden yükseklik (m1)' => '81 mm',
                'Dingil ortasında yerden yükseklik (m2)' => '50 mm',
                'Koridor genişliği Ast (1000×1200 yan)' => '2460 mm',
                'Koridor genişliği Ast (800×1200 yan)' => '2460 mm',
                'Dönüş yarıçapı (Wa)' => '1545 mm',
                'Hız (yüklü/boş)' => '5.5/6.0 km/s',
                'Kaldırma hızı (yüklü/boş)' => '0.127/0.23 m/s',
                'İndirme hızı (yüklü/boş)' => '0.26/0.20 m/s',
                'Tırmanma kabiliyeti (yüklü/boş)' => '8/10 %',
                'Servis freni' => 'Elektromanyetik',
                'Sürüş motoru (S2 60dk)' => '1.1 kW',
                'Kaldırma motoru (S3 15%)' => '3.0 kW',
                'Akü' => '24V / 210Ah (205Ah Li-ion opsiyonu)',
                'Akü ağırlığı' => '190 kg',
                'Sürüş kontrolü' => 'AC',
                'Direksiyon' => 'Mekanik',
                'Ses seviyesi (kulak)' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Ayarlanabilir geniş bacak (straddle) tasarım ile farklı palet tiplerine uyum'],
                ['icon' => 'store', 'text' => 'Kaplumbağa hız modu sayesinde dar alanlarda güvenli hareket'],
                ['icon' => 'star', 'text' => 'Standart oransal kaldırma ile hassas ve kontrollü istif'],
                ['icon' => 'battery-full', 'text' => 'Güçlü 24V enerji; 210Ah kurşun-asit veya Li-ion opsiyonu'],
                ['icon' => 'industry', 'text' => 'Dikey AC tahrik motoru ile yüksek verim ve dayanıklılık'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ve güvenlik kilitlemeleri'],
                ['icon' => 'warehouse', 'text' => '1545 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'bolt', 'text' => '3.0 kW kaldırma motoru ile stabil ve seri kaldırma']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
