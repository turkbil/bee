<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET2H_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'EPT20-15ET2H';
        $titleTr = 'İXTİF EPT20-15ET2H - 1.5 Ton Elektrikli Transpalet (AGM, Yüksek Şasi Boşluğu)';
        $shortTr = '1.500 kg kapasiteli, 24V/65Ah AGM akülü ve entegre 24V-10A şarj cihazlı elektrikli transpalet. Yüksek şasi boşluğu, kauçuk tahrik tekeri ve toz/su korumalı tasarım ile engebeli zeminlerde güvenilir performans ve kolay bakım sunar.';

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

            // TECHNICAL SPECS (PDF)
            'technical_specs' => json_encode([
                'Sürüş Tipi' => 'Elektrik',
                'Operatör Tipi' => 'Yaya',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik Aks Merkezi - Çatal Ucu Mesafesi (x)' => '946 mm',
                'Dingil Mesafesi (y)' => '1301 mm',
                'Servis Ağırlığı' => '216 kg',
                'Aks Yükü (Yüklü) Ön/Arka' => '724 / 1468 kg',
                'Aks Yükü (Yüksüz) Ön/Arka' => '165 / 40 kg',
                'Lastik Tipi' => 'Tahrik: Kauçuk, Yük: PU',
                'Ön Teker Ölçüsü' => 'Ø210×70 mm',
                'Arka Teker Ölçüsü' => 'Ø80×61 / 105×88 mm',
                'Ön İz Genişliği' => '—',
                'Arka İz Genişliği' => '410 / 535 mm',
                'Kaldırma Yüksekliği (h3)' => '115 mm',
                'Sürüş Kolu Yüksekliği min./maks. (h14)' => '790 / 1250 mm',
                'Çatal Altı Yükseklik (h13)' => '80 mm',
                'Toplam Uzunluk (l1)' => '1704 mm',
                'Ön Kısma Kadar Uzunluk (l2)' => '554 mm',
                'Toplam Genişlik (b1/b2)' => '685 mm',
                'Çatal Ölçüleri (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatallar Arası Mesafe (b5)' => '560 / 685 mm',
                'Şasi Altı Boşluk (m2)' => '30 mm',
                'Raf Koridoru Genişliği Ast (1000×1200 enine)' => '2307 mm',
                'Raf Koridoru Genişliği Ast (800×1200 boyuna)' => '2179 mm',
                'Dönüş Yarıçapı (Wa)' => '1505 mm',
                'Seyir Hızı (Yüklü/Yüksüz)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (Yüklü/Yüksüz)' => '0.027 / 0.038 m/s',
                'İndirme Hızı (Yüklü/Yüksüz)' => '0.059 / 0.039 m/s',
                'Tırmanma Kabiliyeti (Yüklü/Yüksüz)' => '5% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Akü' => '24V / 65Ah AGM (yaklaşık 34 kg)',
                'Tahrik Kontrolü' => 'DC',
                'Direksiyon Tasarımı' => 'Mekanik',
                'Ses Seviyesi (Kulak)' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            // FEATURES (8)
            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Piyasada kanıtlanmış bileşenlerle güvenilir mimari'],
                ['icon' => 'battery-full', 'text' => 'AGM batarya ve entegre 24V-10A şarj cihazı'],
                ['icon' => 'arrows-alt', 'text' => 'Yüksek şasi boşluğu ile dış ortam uygundur'],
                ['icon' => 'shield-alt', 'text' => 'Toz ve suya karşı korumalı gövde, sızdırmaz dişli kutusu'],
                ['icon' => 'cog', 'text' => 'Optimize tahrik sistemi, yatay motor/fren yerleşimi'],
                ['icon' => 'circle-notch', 'text' => 'Kauçuk tahrik tekeri: kayma direnci ve uzun ömür'],
                ['icon' => 'wrench', 'text' => 'Tahrik tekeri kolay değişim: hızlı bakım'],
                ['icon' => 'check-circle', 'text' => 'Dayanıklılık ve işletme sürekliliği için tasarlandı']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
