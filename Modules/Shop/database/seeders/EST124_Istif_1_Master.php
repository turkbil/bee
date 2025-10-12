<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EST124_Istif_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EST124';
        $titleTr = 'İXTİF EST124 - 1.2 Ton Elektrikli Yürüyüş Tipi İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'İXTİF EST124; 1.200 kg kapasiteli, 600 mm yük merkezinde çalışan, 24V/80Ah AGM akü ve 24V/10A entegre şarj cihazı ile günlük istiflemeyi kolaylaştıran kompakt bir yürüyüş tipi istif makinesidir. 3.6 metreye kadar kaldırma ve 4.5 km/s hıza ulaşır.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 3, // İstif Makinesi
            'brand_id' => 1,    // İXTİF
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
                'Kapasite (Q)' => '1200 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik' => 'Elektrik (DC kontrol)',
                'Operatör Tipi' => 'Yürüyüş (Pedestrian)',
                'Aks Mesafesi (y)' => '1195 mm',
                'Servis Ağırlığı' => '470 kg',
                'Tekerlek Tipi' => 'Poliüretan (PU)',
                'Tahrik Tekerleği' => 'Ø210×70 mm',
                'Yük Tekerleği' => 'Ø74×72 mm',
                'Destek Tekerleri' => 'Ø130×55 mm',
                'Ön/Arka İz Genişliği (b10/b11)' => '645 / 418 mm',
                'Direk Kapalı Yükseklik (h1)' => '2067 mm (3.013 m direk ör.)',
                'Kaldırma Yüksekliği (h3)' => '2930 mm (opsiyonla 3613 mm)',
                'Direk Açık Yükseklik (h4)' => '3532 mm',
                'Kumanda Kolu Yüksekliği (h14)' => '760 / 1140 mm',
                'Toplam Uzunluk (l1)' => '1706 mm',
                'Çatal Ucu Hariç Uzunluk (l2)' => '556 mm',
                'Toplam Genişlik (b1/b2)' => '925 mm',
                'Çatal Ölçüleri (s/e/l)' => '60 / 170 / 1150 mm',
                'Fork Carriage Genişliği (b3)' => '680 mm',
                'Çatal Aralığı (b5)' => '570 mm',
                'Zemin Yüksekliği (m2)' => '23 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2246 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2180 mm',
                'Dönüş Yarıçapı (Wa)' => '1415 mm',
                'Sürüş Hızı (yüklü/boş)' => '4.0 / 4.5 km/s',
                'Kaldırma İniş Hızı (yüklü/boş)' => '0.10/0.15 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.12/0.10 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '3% / 10%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '2.2 kW',
                'Akü' => '24V / 80Ah AGM (38 kg)',
                'Enerji Tüketimi (EN 16796)' => '0.57 kWh/h',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Direksiyon' => 'Mekanik',
                'Şarj Cihazı' => '24V / 10A entegre (AGM)',
                'Direk Seçenekleri (h3)' => '2513 / 2713 / 3013 / 3313 / 3613 mm'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'shield-alt', 'text' => 'Metal kapak ve güçlendirilmiş şasi ile sağlamlık ve stabilite'],
                ['icon' => 'plug', 'text' => '24V/10A entegre şarj cihazı ile pratik enerji yönetimi'],
                ['icon' => 'battery-full', 'text' => '24V/80Ah AGM akü ile esnek vardiya kullanımı'],
                ['icon' => 'arrows-alt', 'text' => '925 mm genişlik ve 1415 mm dönüş yarıçapı ile dar alan çevikliği'],
                ['icon' => 'gauge', 'text' => '4.5 km/s maksimum hız ile verimli malzeme akışı'],
                ['icon' => 'weight-hanging', 'text' => '1.2 ton nominal kapasite ve 600 mm yük merkezi'],
                ['icon' => 'cog', 'text' => 'Pazar tarafından kanıtlanmış sürüş ve hidrolik bileşenler'],
                ['icon' => 'warehouse', 'text' => '2.5–3.6 m raf sistemleri için uygun kaldırma seçenekleri']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
