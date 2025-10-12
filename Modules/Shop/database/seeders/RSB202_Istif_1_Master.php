<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSB202_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'RSB202';
        $titleTr = 'İXTİF RSB202 - 2.0 Ton Li-Ion Platformlu İstif Makinesi (3.9 m)';

        $shortTr = 'RSB202; 2000 kg kapasite, 600 mm yük merkezi ve 24V/100Ah Li-Ion batarya ile gelir. 6 km/s seyir hızı, elektronik güç direksiyonu ve oransal kaldırma sayesinde dar alanlarda hassas, güvenli ve hızlı yığma sağlar. Entegre şarj cihazı ve katlanır platform standarttır.';

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
                'Model' => 'RSB202',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Ayakta (platformlu)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik aksı-merkezine yük mesafesi (x)' => '690 mm',
                'Dingil Mesafesi (y)' => '1256 mm',
                'Servis Ağırlığı' => '940 kg',
                'Aks yükü (yüklü) ön/arka' => '1000 / 1940 kg',
                'Aks yükü (yüksüz) ön/arka' => '657 / 270 kg',
                'Teker tipleri' => 'PU',
                'Ön teker ölçüsü' => '230×75 mm',
                'Arka teker ölçüsü' => '85×70 mm',
                'Destek tekeri (kastor) ölçüsü' => '130×55 mm',
                'Teker sayısı (ön/arka)' => '1x,1/4',
                'İz genişliği ön' => '608 mm',
                'İz genişliği arka' => '507 mm',
                'Maks. Kaldırma Yüksekliği (h3)' => '3000 mm (opsiyon 3900 mm)',
                'Direk Kapalı Yüksekliği (h1)' => '1900 mm',
                'Kaldırma Yüksekliği (h3+h13)' => '2912 mm',
                'Direk Açık Yüksekliği (h4)' => '3470 mm',
                'Sürüş kolu yüksekliği min./maks. (h14)' => '1040 / 1290 mm',
                'Çatal Altı Yükseklik (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '2000 mm',
                'Yüze Kadar Uzunluk (l2)' => '850 mm',
                'Toplam Genişlik (b1/b2)' => '850 mm',
                'Çatal Ölçüleri (s/e/l)' => '65×185×1150 mm',
                'Çatal Dıştan Dışa (b5)' => '685 mm',
                'Boşluk (mast altı) (m1)' => '25 mm',
                'Boşluk (dingil merkezi) (m2)' => '25 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2530 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2430 mm',
                'Dönüş Yarıçapı (Wa)' => '1620 mm',
                'Seyir Hızı (yüklü/yüksüz)' => '5.5 / 6 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.12 / 0.2 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.3 / 0.2 m/s',
                'Maks. Eğim (yüklü/yüksüz)' => '8% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.6 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Akü Gerilim/Kapasite' => '24V / 100Ah Li-Ion (entegre şarjlı)',
                'Akü Ağırlığı' => '40 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Elektronik (EPS)',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Şarj Cihazı Çıkış Akımı' => '30 A'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Yeni H-profil direk yapısı ile yüksek rijitlik ve 3.9 m’ye kadar sağlam istifleme'],
                ['icon' => 'hand', 'text' => 'EPS ile hafif ve hassas manevra; dar koridorlarda kolay dönüş'],
                ['icon' => 'arrows-alt', 'text' => 'Oransal kaldırma fonksiyonu ile milimetrik konumlandırma ve hassas yük koruması'],
                ['icon' => 'battery-full', 'text' => '24V/100Ah Li-Ion akü ve entegre şarj cihazı ile fırsat şarjı ve sıfır bakım'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve kol korumalarıyla güvenli sürüş'],
                ['icon' => 'gauge', 'text' => 'Yüksüz 6 km/s hız ile orta ölçekli depolarda verimli taşıma'],
                ['icon' => 'couch', 'text' => 'Katlanır, darbeyi emen platform ile konforlu operatör deneyimi'],
                ['icon' => 'compress', 'text' => 'Kompakt 850 mm genişlik ve 1620 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku} güncellendi/eklendi");
    }
}
