<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESi161_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ESi161';
        $titleTr = 'İXTİF ESi161 - 1.6 Ton Li-Ion Çift Katlı İstif Makinesi';
        $shortTr = 'ESi161, Li-Ion batarya ve entegre şarj cihazı ile iki paleti aynı anda taşıyabilen, dar alanlarda kaplumbağa modu ve merkez tahrikli stabilizasyon tekerlekleri sayesinde üstün manevra sunan, 1520 mm kaldırma ve 4/4.5 km/sa hız değerlerine sahip kompakt istif makinesidir.';

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
                'Model' => 'ESi161',
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Yaya',
                'Kapasite (Q)' => '1600 kg',
                'Mast ile Kapasite (Q1)' => '800 kg',
                'Taşıyıcı Kollar ile Kapasite (Q2)' => '1600 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik Aksına Mesafe (x)' => '798 mm',
                'Dingil Mesafesi (y)' => '1265 mm',
                'Servis Ağırlığı' => '510 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Ön Lastik' => 'Ø210×70 mm',
                'Arka Lastik' => 'Ø80×61 mm',
                'Destek Tekerleri' => 'Ø130×55 mm',
                'Teker Sayısı (ön/arka)' => '1,2 / 4',
                'İz Genişliği Ön (b10)' => '592 mm',
                'İz Genişliği Arka (b11)' => '370 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '1608 mm',
                'Direk Kapalı Yükseklik (h1)' => '1942 mm',
                'Serbest Kaldırma (h2)' => '1515 mm',
                'Kaldırma Yüksekliği (h3)' => '1520 mm',
                'Direk Açık Yükseklik (h4)' => '1986 mm',
                'İlk Kaldırma (h5)' => '115 mm',
                'Tiller Yüksekliği (h14)' => '800/1190 mm',
                'Alçaltılmış Çatal Yüksekliği (h13)' => '91 mm',
                'Toplam Uzunluk (l1)' => '1770 mm',
                'Yüze Kadar Uzunluk (l2)' => '620 mm',
                'Toplam Genişlik (b1/b2)' => '800 mm',
                'Çatal Ölçüleri (s/e/l)' => '55×190×1150 mm',
                'Fork Carriage Genişliği (b3)' => '680 mm',
                'Çatallar Arası Mesafe (b5)' => '560 mm',
                'Şasi Orta Boşluk (m2)' => '33 mm',
                'Koridor Genişliği 1000×1200 enine (Ast)' => '2306 mm',
                'Koridor Genişliği 800×1200 boyuna (Ast)' => '2240 mm',
                'Dönüş Yarıçapı (Wa)' => '1473 mm',
                'Sürüş Hızı (yüklü/boş)' => '4 / 4.5 km/sa',
                'Kaldırma Hızı (yüklü/boş)' => '0.1 / 0.12 m/sn',
                'İndirme Hızı (yüklü/boş)' => '0.1 / 0.07 m/sn',
                'Maks. Eğim (yüklü/boş)' => '3% / 10%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru' => '0.75 kW (S2 60 dk)',
                'Kaldırma Motoru' => '2.2 kW (S3 15%)',
                'Batarya' => '24V / 100Ah Li-Ion',
                'Batarya Ağırlığı' => '28 kg',
                'Enerji Tüketimi (EN 16796)' => '0.4 kWh/s',
                'Verim (VDI 2198)' => '22.73 t/saat | 63.72 t/kWh',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses Seviyesi' => '74 dB(A)',
                'Şarj Cihazı Akımı' => '30 A'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'star', 'text' => 'Mono direk ve şeffaf panel ile tüm yönlerde görüş ve hassas istifleme'],
                ['icon' => 'bolt', 'text' => 'Kompakt şasi ve kaplumbağa modu ile dar alanlarda üstün manevra'],
                ['icon' => 'battery-full', 'text' => 'Li-Ion batarya ve entegre şarj cihazı ile esnek ve hızlı şarj'],
                ['icon' => 'industry', 'text' => 'Merkez tahrik ve denge tekerleriyle daha iyi çekiş ve stabilite'],
                ['icon' => 'arrows-alt', 'text' => 'İkili kaldırma ile aynı anda iki palet taşıma olanağı'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ile güvenli duruş ve kontrol'],
                ['icon' => 'briefcase', 'text' => 'Rampa ve düzensiz zeminlerde operasyon kolaylığı'],
                ['icon' => 'cog', 'text' => 'Düşük bakım ihtiyacı ve yüksek çalışma süresi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
