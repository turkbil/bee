<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1;    // İXTİF
        $sku = 'EFL252';
        $titleTr = 'İXTİF EFL252 - 2.5 Ton Li-Ion Elektrikli Denge Ağırlıklı Forklift';

        $shortTr = 'EFL252; 2.5 ton kapasiteli, 500 mm yük merkezi, 3573 mm toplam uzunluk ve 2290 mm dönüş yarıçapı ile dar alanlarda çevik çalışır. LFP Li-ion 80V 205Ah batarya, fırsat şarjını destekler ve IPX4 su koruması ile dış mekânda güven verir.';

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
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Oturmalı',
                'Kapasite (Q)' => '2500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Tahrik Aksına Mesafe (x)' => '495 mm',
                'Dingil Mesafesi (y)' => '1595 mm',
                'Servis Ağırlığı' => '3830 kg',
                'Ön/Arka Dingil Yükü (Yüklü)' => '5530 / 800 kg',
                'Ön/Arka Dingil Yükü (Yüksüz)' => '1480 / 2350 kg',
                'Lastik Tipi' => 'Katı (solid rubber)',
                'Ön Lastik Ölçüsü' => '7.00-12',
                'Arka Lastik Ölçüsü' => '6.00-9',
                'Tekerlek Sayısı (Ön/Arka)' => '2x / 2',
                'İz Genişliği (Ön b10)' => '970 mm',
                'İz Genişliği (Arka b11)' => '975 mm',
                'Direk İleri/Geri Eğim (α/β)' => '6° / 10°',
                'Direk Kapalı Yükseklik (h1)' => '2060 mm',
                'Serbest Kaldırma (h2)' => '140 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yükseklik (h4)' => '4050 mm',
                'Üst Koruma Yüksekliği (h6)' => '2160 mm',
                'Koltuk Yüksekliği (h7)' => '1095 mm',
                'Çeki Kancası Yüksekliği (h10)' => '435 mm',
                'Toplam Uzunluk (l1)' => '3573 mm',
                'Çatala Kadar Uzunluk (l2)' => '2503 mm',
                'Toplam Genişlik (b1/b2)' => '1154 mm',
                'Çatal Ölçüsü (s/e/l)' => '40 × 122 × 1070 mm',
                'Fork Carriage Sınıfı' => '2A',
                'Fork Carriage Genişliği (b3)' => '1090 mm',
                'Zemin Boşluğu (mast altı m1)' => '100 mm',
                'Zemin Boşluğu (dingil arası m2)' => '150 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '3985 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '4195 mm',
                'Dönüş Yarıçapı (Wa)' => '2290 mm',
                'Yürüyüş Hızı (Yüklü/Boş)' => '11 / 12 km/s',
                'Kaldırma Hızı (Yüklü/Boş)' => '0.28 / 0.37 m/s',
                'İndirme Hızı (Yüklü/Boş)' => '0.45 / 0.50 m/s',
                'Tırmanma Kabiliyeti (Yüklü/Boş)' => '15% / 15%',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '10 kW',
                'Kaldırma Motoru (S3 15%)' => '16 kW',
                'Batarya Volt/Ah' => '80V 205Ah (LFP)',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Hidrolik',
                'Sürücü Kulak Seviyesi Gürültü' => '74 dB(A)',
                'Not' => 'Yan kaydırıcı ile nominal kapasiteden ~150 kg düşülür'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'LFP Li-ion batarya güvenlidir, kendi kendine tutuşmayı önler ve fırsat şarjını destekler.'],
                ['icon' => 'arrows-alt', 'text' => '3573 mm toplam uzunluk ile tipik IC forkliftlere kıyasla ~%5 daha kompakt şasi.'],
                ['icon' => 'bolt', 'text' => 'Düşük TCO: Hava filtresi, yağ filtresi, motor yağı ve marş aküsü bakımı yok.'],
                ['icon' => 'shield-alt', 'text' => 'IPX4 su koruması sayesinde yağmur altında dış mekân operasyonları.'],
                ['icon' => 'microchip', 'text' => 'Telematics: gerçek zamanlı konum, kullanım raporları, teşhis ve kart erişim güncellemeleri.'],
                ['icon' => 'industry', 'text' => 'Yüksek dayanımlı bileşenler ve sağlam şasi mimarisi.'],
                ['icon' => 'cart-shopping', 'text' => 'Yüksek verimlilik için 11/12 km/s sürüş, 0.28/0.37 m/s kaldırma hızları.'],
                ['icon' => 'star', 'text' => '74 dB(A) düşük gürültü seviyesi ile konforlu sürüş.']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
