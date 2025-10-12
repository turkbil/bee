<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES12_12ES_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES12-12ES';

        $titleTr = 'İXTİF ES12-12ES - 1.2 Ton Elektrikli İstifleyici';
        $shortTr = '1.2 ton kapasite, 600 mm yük merkezi ve 3015 mm maksimum kaldırma yüksekliği sunan elektrikli yaya kumandalı istifleyici. 2x12V/105Ah akü, 2.2 kW kaldırma motoru, elektromanyetik fren ve 1408 mm dönüş yarıçapıyla dar alanlarda verimli.';

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
                'Model' => 'ES12-12ES',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya',
                'Kapasite (Q)' => '1200 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Tahrik Aksından Çatala Mesafe (x)' => '795 mm',
                'Dingil Açıklığı (y)' => '1240 mm',
                'Servis Ağırlığı' => '661 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Ön Tekerlek' => 'Ø210×70 mm',
                'Arka Tekerlek' => 'Ø80×60 mm',
                'Destek Tekerlekleri' => 'Ø130×55 mm',
                'Tekerlek Dizilimi (ön/arka)' => '1x+1/4',
                'Ön İz Genişliği (b10)' => '533 mm',
                'Arka İz Genişliği (b11)' => '400 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3015 mm',
                'Kapalı Direk Yüksekliği (h1)' => '2056 mm',
                'Serbest Kaldırma (h2)' => '—',
                'Kaldırma (h3)' => '2927 mm',
                'Açık Direk Yüksekliği (h4)' => '3521 mm',
                'Tiller Yüksekliği min./maks. (h14)' => '860 / 1200 mm',
                'Alçaltılmış Çatal Yüksekliği (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '1740 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '590 mm',
                'Toplam Genişlik (b1/b2)' => '800 mm',
                'Çatal Ölçüleri (s/e/l)' => '60 / 170 / 1150 mm',
                'Taşıyıcı Genişliği (b3)' => '680 mm',
                'Çatallar Arası Mesafe (b5)' => '570 mm',
                'Şasi Altı Boşluk (m2)' => '30 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2225 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2150 mm',
                'Dönüş Yarıçapı (Wa)' => '1408 mm',
                'Sürüş Hızı (yüklü/boş)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.12 / 0.22 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.12 / 0.11 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '3% / 10%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.65 kW',
                'Kaldırma Motoru (S3 15%)' => '2.2 kW',
                'Akü (V/Ah)' => '2×12V / 105Ah',
                'Akü Ağırlığı' => '2×30 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '2×12V/105Ah akü ile vardiya boyunca kesintisiz enerji'],
                ['icon' => 'bolt', 'text' => '2.2 kW kaldırma motoru ile güven veren hız ve tork'],
                ['icon' => 'arrows-alt', 'text' => '1408 mm dönüş yarıçapı ile dar alan manevrası'],
                ['icon' => 'weight-hanging', 'text' => '1.2 ton nominal kapasite, 600 mm yük merkezi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ile emniyetli duruş'],
                ['icon' => 'warehouse', 'text' => '3015 mm’ye kadar istifleme ile yüksek raf erişimi'],
                ['icon' => 'microchip', 'text' => 'DC sürüş kontrolü ile dengeli hızlanma ve verimlilik'],
                ['icon' => 'cog', 'text' => 'Poliüretan tekerlekler ve düşük bakım gereksinimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
