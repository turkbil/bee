<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL161_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'TDL-161';
        $titleTr = 'İXTİF TDL161 - 1.6 Ton 3 Teker Li-Ion Karşı Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1.6 ton kapasiteli, 3 tekerli çift tahrik AC motorlara sahip, 48V Li-Ion mimaride tasarlanmış kompakt forklift. 5.4kW×2 sürüş, 11kW kaldırma motoru, 15/16 km/s hız ve 48V/280Ah batarya ile verimli, entegre 48V-50A şarj cihazı ve bilgi dolu LCD ekrana sahip.'], JSON_UNESCAPED_UNICODE),
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
                'Kapasite (Q)' => '1600 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Sürüş' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Oturmalı',
                'Servis Ağırlığı' => '3380 kg',
                'Tahrik Aksına Mesafe (x)' => '420 mm',
                'Dingil Mesafesi (y)' => '1358 mm',
                'Ön/Araç İz Genişliği (ön/arka)' => '910 / 175 mm',
                'Teker Sayısı (ön/arka)' => '2x / 2 (çift tahrik)',
                'Lastik Tipi' => 'Katı (solid)',
                'Ön Lastik Ölçüsü' => '18x7-8',
                'Arka Lastik Ölçüsü' => '15/4.5-8',
                'Direk Eğimi (ileri/geri)' => '5° / 6°',
                'Direk Alçak Yüksekliği (h1)' => '2075 mm',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Yükselmiş Yüksekliği (h4)' => '4058 mm',
                'Üst Koruma Yüksekliği' => '2140 mm',
                'Koltuk Yüksekliği' => '1070 mm',
                'Çeki Kancası Yüksekliği' => '560 mm',
                'Toplam Uzunluk (l1)' => '2945 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '2025 mm',
                'Toplam Genişlik (b1/b2)' => '1050 mm',
                'Çatal Ölçüleri (s/e/l)' => '40×100×920 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A',
                'Çatal Taşıyıcı Genişliği' => '1040 mm',
                'Yer Yüksekliği Alt Mast' => '86 mm',
                'Yer Yüksekliği Dingil Orta' => '104 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '3348 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '3485 mm',
                'Dönüş Yarıçapı (Wa)' => '1605 mm',
                'Sürüş Hızı (yüklü/boş)' => '15 / 16 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.35 / 0.43 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.45 / 0.37 m/s',
                'Azami Eğim (yüklü/boş)' => '15% / 17%',
                'Servis Freni' => 'Elektromanyetik',
                'Park Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '5.4 kW × 2',
                'Kaldırma Motoru (S3 15%)' => '11 kW',
                'Batarya' => '48V / 280Ah (≈165 kg)',
                'Ses Seviyesi (kulak)' => '75 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V Li-Ion mimari, fırsat şarjı ile vardiya boyunca sabit güç'],
                ['icon' => 'bolt', 'text' => 'Çift AC tahrik: 5.4kW×2 ile dinamik ivmelenme ve çekiş'],
                ['icon' => 'star', 'text' => 'Ergonomik kabin: ayarlanabilir direksiyon ve konforlu koltuk'],
                ['icon' => 'building', 'text' => 'Optimize tek parça şasi: kabin montaj/sökme kolaylığı'],
                ['icon' => 'microchip', 'text' => 'Üst köşede yüksek çözünürlüklü LCD ekran'],
                ['icon' => 'cog', 'text' => 'Hidrolik direksiyon ile hassas ve düşük bakım yapısı'],
                ['icon' => 'plug', 'text' => 'Entegre 48V/50A şarj cihazı, 16A priz ile uyumlu'],
                ['icon' => 'cart-shopping', 'text' => 'Opsiyonel elektromanyetik joystick ile avuç içi kontrol']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
