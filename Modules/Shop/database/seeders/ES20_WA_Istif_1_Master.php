<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES20_WA_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES20-WA';
        $titleTr = 'İXTİF ES20-WA - 2.0 Ton Elektrikli Yaya Tipi İstif Makinesi';

        $shortTr = 'İXTİF ES20-WA, 2.0 ton kapasite ve 600 mm yük merkeziyle dar alanlarda güvenli istifleme sunar. 24V enerji sistemi, 3.0 kW kaldırma motoru ve 4.5/5.0 km/s hız ile verimli operasyon sağlar. İki kademeli indirme, elektromanyetik fren ve AC sürüş kontrolü ile stabilite ve hassasiyet sağlar.';

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
                'Üretici' => 'İXTİF',
                'Model' => 'ES20-WA',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Tahrik Aksına Mesafe (x)' => '693 mm',
                'Aks Aralığı (y)' => '1305 mm',
                'Servis Ağırlığı' => '1170 kg',
                'Aks Yükü (yüklü/boş) ön/arka' => '850/2320 kg, 780/390 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Tahrik Tekerlek ölçüsü (ön)' => '⌀230×75 mm',
                'Yük Tekerlek ölçüsü (arka)' => '⌀85×70 mm',
                'Dengeleme tekeri (caster)' => '⌀130×55 mm',
                'Tekerlek adedi (ön/arka)' => '1x+1 / 4',
                'Ön İz Genişliği (b10)' => '538 mm',
                'Arka İz Genişliği (b11)' => '410 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3000 mm (örnek mast)',
                'Direk Kapalı Yükseklik (h1)' => '2020 mm',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Kaldırma Yüksekliği (h3)' => '2912 mm',
                'Direk Açık Yükseklik (h4)' => '3465 mm',
                'Tutamak Sürüş Konumu Yükseklik (h14)' => '715/1200 mm',
                'Çatal Altı Yükseklik (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '1940 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '790 mm',
                'Toplam Genişlik (b1/b2)' => '800 mm',
                'Çatal Ölçüsü (s/e/l)' => '60×190×1150 mm',
                'Çatal Aralığı (b5)' => '600 mm',
                'Tekerlek Tabanı Yerden Yükseklik (m2)' => '18 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2465 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2440 mm',
                'Dönüş Yarıçapı (Wa)' => '1589 mm',
                'Sürüş Hızı (yüklü/boş)' => '4.5 / 5.0 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.11 / 0.16 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.32 / 0.23 m/s',
                'Tırmanma Eğimi (yüklü/boş)' => '6% / 12%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.1 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Akü Voltaj/Kapasite' => '24V / 280Ah (opsiyon Li-ion 205Ah/150Ah)',
                'Akü Ağırlığı' => '240 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon Tipi' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V enerji platformu ve yüksek verimli AC sürüş kontrolü'],
                ['icon' => 'bolt', 'text' => '3.0 kW kaldırma motoru ile hızlı ve dengeli istifleme'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ile güvenli duruş'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt 1589 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'star', 'text' => 'İki kademeli indirme ile hassas ve stabil yük bırakma'],
                ['icon' => 'cog', 'text' => 'Mekanik direksiyon ile düşük bakım ve kolay kullanım'],
                ['icon' => 'cart-shopping', 'text' => '800 mm gövde genişliği ile palet içinde rahat manevra'],
                ['icon' => 'microchip', 'text' => 'Akıllı kontrol ünitesi ile tutarlı performans']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}