<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSC202_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'RSC202';
        $titleTr = 'İXTİF RSC202 - 2.0 Ton Elektrikli Karşı Ağırlıklı İstif Makinesi';

        $shortTr = 'İXTİF RSC202; 2000 kg kapasite, 500 mm yük merkezi ve 24V/280Ah enerjiyle kompakt bir şaside yüksek manevra kabiliyeti sunar. 1915 mm dönüş yarıçapı, 116 mm yerden yükseklik, oransal kaldırma ve elektronik direksiyon ile dar koridorlarda hassas istifleme sağlar.';

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
                'Model' => 'RSC202',
                'Sürüş' => 'Elektrik (AC)',
                'Kapasite' => '2000 kg',
                'Yük Merkezi' => '500 mm',
                'Servis Ağırlığı' => '2480 kg',
                'Teker Tipi' => 'Poliüretan',
                'Teker Boyutu (ön/arka)' => '260×105 / 254×102',
                'Teker Adedi (x=tahrik)' => '1x / 2',
                'Teker İz Genişliği (arka)' => '787 mm',
                'Direk Eğimi (ileri/geri)' => '1.5° / 7°',
                'Direk Kapalı Yüksekliği (h1)' => '2118 mm (3000 mm kaldırma için)',
                'Serbest Kaldırma (h2)' => '150 mm (standart direk)',
                'Kaldırma Yüksekliği (h3)' => '3000 mm (opsiyon 2600–5000 mm)',
                'Direk Açık Yüksekliği (h4)' => '3915 mm (h3=3000 mm)',
                'Toplam Uzunluk (l1)' => '3178 mm',
                'Çatala Kadar Uzunluk (l2)' => '2018 mm',
                'Toplam Genişlik (b1/b2)' => '900 mm',
                'Çatal Ölçüleri (s/e/l)' => '40 × 122 × 1070 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A, 800 mm genişlik',
                'Yer Yüksekliği (orta noktada)' => '116 mm',
                'Yer Yüksekliği (direk altında)' => '80 mm',
                'Koridor Genişliği 1000×1200 (enine)' => '3478 mm',
                'Koridor Genişliği 800×1200 (boyuna)' => '3588 mm',
                'Dönüş Yarıçapı (Wa)' => '1915 mm',
                'Sürat (yük/boş)' => '5.5 / 6 km/s',
                'Kaldırma Hızı (yük/boş)' => '0.10 / 0.16 m/s',
                'İndirme Hızı (yük/boş)' => '0.19 / 0.16 m/s',
                'Eğim Kabiliyeti (yük/boş)' => '5% / 8%',
                'Servis Freni / Park Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '3.3 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Akü' => '24V / 280Ah (Li-ion opsiyonu mevcut)',
                'Akü Ağırlığı' => '190 kg',
                'Direksiyon' => 'Elektronik',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Şarj Cihazı Çıkışı' => '30 A'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => '1915 mm dönüş yarıçapı ile kompakt şaside üstün manevra'],
                ['icon' => 'battery-full', 'text' => '24V/280Ah enerji; hızlı şarjlı Li-ion seçeneği'],
                ['icon' => 'microchip', 'text' => 'Oransal kaldırma sistemi ile hassas istifleme'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis ve park freni'],
                ['icon' => 'bolt', 'text' => '3.3 kW AC sürüş + 3.0 kW kaldırma motoru'],
                ['icon' => 'industry', 'text' => '116 mm yerden yükseklik; bozuk zeminde akıcı ilerleme'],
                ['icon' => 'hand', 'text' => 'Elektronik direksiyonla yumuşak ve kontrollü sürüş'],
                ['icon' => 'layer-group', 'text' => '2600–5000 mm arasında geniş direk seçenekleri']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
