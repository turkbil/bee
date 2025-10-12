<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL_201_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFL201';
        $titleTr = 'İXTİF EFL201 - 2.0 Ton 80V Li-Ion Denge Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'İXTİF EFL201; 2.0 ton kapasiteli, 80V Li‑Ion (150Ah) bataryalı, 4 tekerlekli karşı dengeli elektrikli forklift. 11/14 km/s sürüş, 0.25/0.30 m/s kaldırma, 120 mm yerden yükseklik, yeni görüş açıklığı sağlayan direk ve entegre 80V/35A şarj ile gün boyu verim.'], JSON_UNESCAPED_UNICODE),
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
            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'EFL201',
                'Sürüş Ünitesi' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Oturmalı',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Tahrik aksına kadar yük mesafesi (x)' => '465.5 mm',
                'Dingil mesafesi (y)' => '1448 mm',
                'Servis Ağırlığı' => '3137 kg',
                'Aks yükü, yüklü (ön/arka)' => '4604 / 533 kg',
                'Aks yükü, yüksüz (ön/arka)' => '1270 / 1867 kg',
                'Lastik Tipi' => 'Katı lastik',
                'Ön Lastik Ölçüsü' => '6.5-10',
                'Arka Lastik Ölçüsü' => '5.00-8',
                'Tekerlek (ön/arka, x=çekiş)' => '2x / 2',
                'İz genişliği ön (b10)' => '905 mm',
                'İz genişliği arka (b11)' => '920 mm',
                'Direk eğimi (ileri/geri)' => '6° / 10°',
                'Direk kapalı yükseklik (h1)' => '2020 mm',
                'Serbest kaldırma (h2)' => '100 mm',
                'Kaldırma yüksekliği (h3)' => '3000 mm (standart)',
                'Direk açık yükseklik (h4)' => '4028 mm',
                'Üst koruma yüksekliği (h6)' => '2080 mm',
                'Koltuk yüksekliği (h7)' => '1060 mm',
                'Çeki kanca yüksekliği (h10)' => '370 mm',
                'Toplam uzunluk (l1)' => '3342 mm',
                'Yüze kadar uzunluk (l2)' => '2272 mm',
                'Toplam genişlik (b1/b2)' => '1080 mm',
                'Çatal ölçüsü (s/e/l)' => '40 × 122 × 1070 mm',
                'Çatal kancası sınıfı' => '2A',
                'Çatal taşıyıcı genişliği (b3)' => '1040 mm',
                'Şasi altı açıklık mast altında (m1)' => '115 mm',
                'Dingil ortasında yerden yükseklik (m2)' => '120 mm',
                'Koridor genişliği 1000×1200 çapraz (Ast)' => '3765.5 mm',
                'Koridor genişliği 800×1200 çapraz (Ast)' => '3965.5 mm',
                'Dönüş yarıçapı (Wa)' => '2100 mm',
                'Sürüş hızı (yüklü/boş)' => '11 / 14 km/s',
                'Kaldırma hızı (yüklü/boş)' => '0.25 / 0.30 m/s',
                'İndirme hızı (yüklü/boş)' => '0.43 / 0.45 m/s',
                'Maks. çekiş kuvveti (yüklü/boş)' => '10000 N',
                'Tırmanma kabiliyeti (yüklü/boş)' => '12% / 15%',
                'Servis freni' => 'Hidrolik',
                'Park freni' => 'Mekanik',
                'Sürüş motoru (S2 60dk)' => '6 kW',
                'Kaldırma motoru (S3 15%)' => '11 kW',
                'Batarya' => '80V / 150Ah Li‑Ion',
                'Entegre şarj cihazı' => '80V / 35A',
                'Sürücü kulak seviyesi' => '70 dB(A)',
                'Yer açıklığı' => '120 mm (dingil ortası)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V/150Ah Li‑Ion batarya ve 80V/35A entegre şarj ile hızlı ve fırsat şarjı'],
                ['icon' => 'arrows-alt', 'text' => 'Yeni tasarım direk ile geniş görüş alanı ve güvenli yük konumlandırma'],
                ['icon' => 'briefcase', 'text' => 'Geniş bacak alanı, ayarlanabilir direksiyon ve konforlu koltuk ergonomisi'],
                ['icon' => 'shield-alt', 'text' => 'Suya dayanıklı yapı ve dış mekânda güvenli kullanım'],
                ['icon' => 'star', 'text' => '120 mm yüksek yer açıklığı ve büyük lastikler ile her zeminde tutunma'],
                ['icon' => 'bolt', 'text' => '80V tahrik mimarisiyle verimli performans ve yüksek üretkenlik'],
                ['icon' => 'cog', 'text' => 'Basit yerleşim sayesinde ana bileşenlere kolay bakım erişimi'],
                ['icon' => 'microchip', 'text' => 'AC sürüş ünitesi ile pürüzsüz hızlanma ve hassas kontrol']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
