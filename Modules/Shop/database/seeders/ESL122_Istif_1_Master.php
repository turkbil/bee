<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESL122_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1;    // İXTİF
        $sku = 'ESL122';

        $titleTr = 'İXTİF ESL122 - 1.2 Ton Yaya Tipi Li-Ion İstif Makinesi';
        $shortTr = 'ESL122, 1.200 kg kapasite, 24V/100Ah Li-Ion akü, 2930 mm kaldırma, 4.5 km/s azami hız ve 1464 mm dönüş yarıçapıyla dar alanlarda güvenli istif sunar. Entegre şarj ve kaplumbağa moduyla kolay, sessiz ve verimli çalışır.';

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
                'Model' => 'ESL122',
                'Sürüş' => 'Elektrikli (DC)',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '1200 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Yük Mesafesi (x)' => '798 mm',
                'Dingil Açıklığı (y)' => '1212 mm',
                'Servis Ağırlığı' => '560 kg',
                'Aks Yükleri, Yüklü (ön/arka)' => '610 / 1150 kg',
                'Aks Yükleri, Boş (ön/arka)' => '410 / 150 kg',
                'Teker Tipi' => 'Poliüretan',
                'Teker Ölçüsü, Ön' => 'Ø210×70 mm',
                'Teker Ölçüsü, Arka' => 'Ø74×72 mm',
                'Destek Tekerleri' => 'Ø130×55 mm',
                'Teker Sayısı (x=tahrik)' => '1x 1/4',
                'İz Genişliği, Ön' => '531 mm',
                'İz Genişliği, Arka' => '405 mm',
                'Direk Kapalı Yükseklik (h1)' => '2067 mm',
                'Kaldırma Yüksekliği (h3)' => '2930 mm',
                'Direk Tam Açık Yükseklik (h4)' => '3532 mm',
                'Tiller Yüksekliği (min/max)' => '760 / 1140 mm',
                'Çatal Ölçüleri (s×e×l)' => '60 / 170 / 1150 mm',
                'Toplam Uzunluk (l1)' => '1715 mm',
                'Yüke Bakan Uzunluk (l2)' => '565 mm',
                'Toplam Genişlik (b1/b2)' => '792 mm',
                'Dönüş Yarıçapı (Wa)' => '1464 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2296 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2230 mm',
                'Yer Açıklığı, Orta Dingil (m2)' => '30 mm',
                'Sürat, Yüklü/Boş' => '4.2 / 4.5 km/s',
                'Kaldırma Hızı, Yüklü/Boş' => '0.09 / 0.13 m/s',
                'İndirme Hızı, Yüklü/Boş' => '0.10 / 0.085 m/s',
                'Maks. Eğim Kabiliyeti, Yüklü/Boş' => '3% / 10%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '2.2 kW',
                'Batarya' => '24V / 100Ah Li-Ion',
                'Batarya Ağırlığı' => '28 kg',
                'Enerji Tüketimi (EN 16796)' => '0.62 kWh/h',
                'VDI 2198 Verimi' => '55.12 t/kWh',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon Tasarımı' => 'Mekanik',
                'Ses Seviyesi' => '74 dB(A)',
                'Şarj Cihazı Akımı' => '30 A'
            ], JSON_UNESCAPED_UNICODE),

            // 8 özellik (icon + text)
            'features' => json_encode([
                ['icon' => 'industry', 'text' => 'Güçlendirilmiş şasi ve yan darbe kirişleriyle dayanıklı yapı'],
                ['icon' => 'industry', 'text' => 'Kirişli rijit direk tasarımı ile pürüzsüz kaldırma ve istif'],
                ['icon' => 'arrows-alt', 'text' => 'Uzun ve offset tiller ile dar alanlarda kıvrak manevra'],
                ['icon' => 'battery-full', 'text' => '24V/100Ah Li-Ion akü; modüler, bakımsız enerji sistemi'],
                ['icon' => 'bolt', 'text' => 'Entegre şarj ile hızlı şarj/dinamik vardiya planı'],
                ['icon' => 'cog', 'text' => 'Kaliteli hidrolik pompa ile düşük gürültü ve kısa kaldırma süresi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve güvenli yokuş kontrolü'],
                ['icon' => 'star', 'text' => 'Hafif hizmet uygulamaları için kompakt, verimli ve ekonomik çözüm']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master oluşturuldu: ' . $sku);
    }
}
