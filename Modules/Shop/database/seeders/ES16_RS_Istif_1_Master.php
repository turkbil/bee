<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES16_RS_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES16-RS';

        $titleTr = 'İXTİF ES16-RS - 1.6 Ton Elektrikli Ayakta Kullanım İstif Makinesi';
        $shortTr = 'İXTİF ES16-RS; 1.6 ton kapasite, 600 mm yük merkezi ve 24V/210Ah aküyle 5.5/6.0 km/s hız sunar. 3.0 kW kaldırma, 1.6 kW sürüş motoru, iki kademeli alçaltma ile hassas istif ve 3000 mm’e kadar kaldırma sağlar.';

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
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Ayakta kullanım',
                'Kapasite (Q)' => '1600 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik Aksına Mesafe (x)' => '693 mm',
                'Dingil Mesafesi (y)' => '1375 mm',
                'Servis Ağırlığı' => '1240 kg',
                'Aks Yükü (Yüklü) Ön/Arka' => '960 / 1880 kg',
                'Aks Yükü (Boş) Ön/Arka' => '860 / 380 kg',
                'Teker Tipi' => 'Poliüretan',
                'Ön Teker Ölçüsü' => 'Ø230×75 mm',
                'Arka Teker Ölçüsü' => 'Ø85×70 mm',
                'Destek Teker (Caster)' => 'Ø130×55 mm',
                'Teker Sayısı (ön/arka)' => '1x +1 / 4',
                'İz Genişliği Ön (b10)' => '574 mm',
                'İz Genişliği Arka (b11)' => '380 / 495 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3000 mm',
                'Direk Kapalı Yüksekliği (h1)' => '2020 mm',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Kaldırma (h3)' => '2912 mm',
                'Direk Açık Yüksekliği (h4)' => '3465 mm',
                'Kumanda Kolu Yüksekliği (h14) min./maks.' => '1150 / 1480 mm',
                'Çatal Altı Yükseklik (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '2035 mm',
                'Ön Yüze Kadar Uzunluk (l2)' => '885 mm',
                'Toplam Genişlik (b1/b2)' => '850 mm',
                'Çatal Ölçüsü (s/e/l)' => '60 × 190 × 1150 mm',
                'Kızak Genişliği (b3)' => '800 mm',
                'Çatallar Arası Mesafe (b5)' => '570 / 685 mm',
                'Şasi Altı Boşluk (m2)' => '28 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2605 / 2965 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2575 / 2935 mm',
                'Dönüş Yarıçapı (Wa)' => '1730 / 2090 mm',
                'Sürüş Hızı (Yüklü/Boş)' => '5.5 / 6.0 km/s',
                'Kaldırma Hızı (Yüklü/Boş)' => '0.13 / 0.16 m/s',
                'İndirme Hızı (Yüklü/Boş)' => '0.30 / 0.22 m/s',
                'Tırmanma Kabiliyeti (Yüklü/Boş)' => '8% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.6 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Akü Gerilim/Kapasite' => '24V / 210Ah (opsiyonlar mevcut)',
                'Akü Ağırlığı' => '200 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Elektronik',
                'Gürültü Seviyesi (dB(A))' => '74'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'İki kademeli alçaltma ile stabil ve hassas istif'],
                ['icon' => 'battery-full', 'text' => '24V/210Ah akü; yan çekme ile hızlı değişim olanağı'],
                ['icon' => 'bolt', 'text' => '3.0 kW kaldırma ve 1.6 kW sürüş motoru ile güçlü performans'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ve elektronik direksiyon'],
                ['icon' => 'warehouse', 'text' => '850 mm şasi genişliğiyle dar koridor çevikliği'],
                ['icon' => 'star', 'text' => 'PU tekerler ile sessiz ve titreşimsiz sürüş'],
                ['icon' => 'plug', 'text' => 'Harici şarj cihazı desteği (24V-30A)'],
                ['icon' => 'certificate', 'text' => 'CE uygunluğu ve güvenlik standartları']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
