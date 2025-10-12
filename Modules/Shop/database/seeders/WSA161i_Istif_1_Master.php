<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WSA161i_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'WSA161i';
        $titleTr = 'İXTİF WSA161i - 1.6 Ton Ağır Hizmet Başlangıç Kaldırmalı İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'WSA161i; 1.6 t mast kaldırma (Q1) ve 2.0 t destek kolu kaldırma (Q2) kapasiteleri, 24V/100Ah Li-ion batarya ve entegre 24V/30A şarj cihazı ile hızlı istifleme sunar. 5/5.5 km/s sürüş, 0.23/0.30 m/s kaldırma ve 1826 mm dönüş yarıçapı ile dar koridorlarda çeviktir.'], JSON_UNESCAPED_UNICODE),
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
                'Sürüş' => 'Elektrikli (AC sürüş kontrol)',
                'Operatör Tipi' => 'Yürüyen (pedestrian)',
                'Kapasite (mast kaldırma, Q1)' => '1600 kg',
                'Kapasite (destek kolu kaldırma, Q2)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik Aksı – Forka Mesafe (x)' => '906 mm',
                'Aks Mesafesi (y)' => '1608 mm',
                'Servis Ağırlığı' => '1120 kg',
                'Ön/Arka Dingil Yükü (yüklü)' => '1090 / 2030 kg',
                'Ön/Arka Dingil Yükü (yüksüz)' => '780 / 340 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Ön Tekerlek (çekiş) ölçüsü' => 'Φ230×75 mm',
                'Arka Tekerlek ölçüsü' => 'Φ85×70 mm',
                'Ek Tekerlek (kastor) ölçüsü' => 'Φ130×55 mm',
                'Tekerlek adedi (ön/arka)' => '1x +1 / 4',
                'Ön İz Genişliği (b10)' => '538 mm',
                'Arka İz Genişliği (b11)' => '385 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3000 mm (opsiyonlarla 5500 mm)',
                'Direk Altı Yükseklik (h13)' => '92 mm',
                'Direk Kapalı Yükseklik (h1)' => '2015 mm',
                'Direk Açık Yükseklik (h4)' => '3495 mm (3.0 m direk)',
                'Kaldırma Yüksekliği (h3)' => '2915 mm (3.0 m direk)',
                'İlk Kaldırma (h5)' => '120 mm',
                'Tiller Kolu Sürüş Konumu (min/max, h14)' => '715 / 1200 mm',
                'Toplam Uzunluk (l1)' => '2028 mm',
                'Yüke Kadar Uzunluk (l2)' => '878 mm',
                'Toplam Genişlik (b1/b2)' => '810 mm',
                'Çatal Ölçüleri (s/e/l)' => '60 / 185 / 1150 mm',
                'Fork Taşıyıcı Genişliği (b3)' => '750 mm',
                'Çatallar Arası Mesafe (b5)' => '570 mm',
                'Yerden Yükseklik – Direk Altı (m1)' => '14 mm',
                'Yer boşluğu – dingil ortası (m2)' => '14 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2646 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2560 mm',
                'Dönüş Yarıçapı (Wa)' => '1826 mm',
                'Sürüş Hızı (yüklü/boş)' => '5.0 / 5.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.23 / 0.30 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.40 / 0.36 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '8% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '1.6 kW',
                'Kaldırma Motoru (S3 15%)' => '4.5 kW',
                'Batarya' => '24V / 100Ah Li-ion (opsiyon 205Ah)',
                'Batarya Ağırlığı' => '40 kg',
                'Entegre Şarj Cihazı' => '24V / 30A (standart)',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Direk Seçenekleri' => '2-kademeli/3-kademeli, 3.0–5.5 m',
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V Li-ion batarya ve 24V/30A entegre şarj ile fırsat şarjı ve bakım gerektirmeyen yapı'],
                ['icon' => 'arrows-alt', 'text' => 'Başlangıç kaldırma ve destek kolları ile çift kat palet taşıma, verimi ikiye katlar'],
                ['icon' => 'gauge', 'text' => 'Sınıfında hızlı kaldırma/indirme hızları ile 5.5 m’ye kadar seri istifleme'],
                ['icon' => 'shield-alt', 'text' => 'Oransal kaldırma/indirme kontrolü ile hassas ve güvenli yerleştirme'],
                ['icon' => 'cog', 'text' => 'Dikey AC tahrik motoru ile güçlü çekiş ve düşük bakım ihtiyacı'],
                ['icon' => 'layer-group', 'text' => 'Kompakt şasi ve kaplumbağa modu ile dar koridor manevrası'],
                ['icon' => 'briefcase', 'text' => 'Üst kapakta eşya gözü ve USB çıkış ile pratik kullanım'],
                ['icon' => 'bolt', 'text' => 'Ağır hizmete uygun sağlam şase ve taşıyıcı kollar']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
