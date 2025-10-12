<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL201_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'TDL-201';
        $titleTr = 'İXTİF TDL201 - 2.0 Ton 3 Teker Li-Ion Karşı Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2.0 ton kapasiteli, 3 tekerli çift tahrik AC motorlarla 48V Li-Ion güç sistemi. 5.4kW×2 sürüş ve 11kW kaldırma motoru, 15/16 km/s hız ve 48V/405Ah batarya ile yüksek verim. Geniş bacak alanı, ergonomik koltuk, yeni nesil LCD ekran ve entegre 48V-50A şarj.'], JSON_UNESCAPED_UNICODE),
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
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Sürüş' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Oturmalı',
                'Servis Ağırlığı' => '3575 kg',
                'Tahrik Aksına Mesafe (x)' => '420 mm',
                'Dingil Mesafesi (y)' => '1358 mm',
                'Ön/Araç İz Genişliği (ön/arka)' => '936 / 175 mm',
                'Teker Sayısı (ön/arka)' => '2x / 2 (çift tahrik)',
                'Lastik Tipi' => 'Katı (solid)',
                'Ön Lastik Ölçüsü' => '200/50-10',
                'Arka Lastik Ölçüsü' => '15/4.5-8',
                'Direk Eğimi (ileri/geri)' => '5° / 6°',
                'Direk Alçak Yüksekliği (h1)' => '2075 mm',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Yükselmiş Yüksekliği (h4)' => '4058 mm',
                'Üst Koruma Yüksekliği' => '2140 mm',
                'Koltuk Yüksekliği' => '1070 mm',
                'Çeki Kancası Yüksekliği' => '560 mm',
                'Toplam Uzunluk (l1)' => '3095 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '2025 mm',
                'Toplam Genişlik (b1/b2)' => '1150 mm',
                'Çatal Ölçüleri (s/e/l)' => '40×122×1070 mm',
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
                'Batarya' => '48V / 405Ah (≈207 kg)',
                'Enerji Tüketimi (EN 16796)' => '5.288 kWh/h (bilgi amaçlı)',
                'Sürücü Kulak Seviyesi' => '79 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V Li-Ion yüksek kapasite: 405Ah seçenek ile uzun vardiya'],
                ['icon' => 'bolt', 'text' => '5.4kW×2 çift tahrik AC motor ile sürekli performans'],
                ['icon' => 'star', 'text' => 'Geniş bacak alanı ve konforlu kova tipi koltuk'],
                ['icon' => 'microchip', 'text' => 'Sağ üstte yüksek çözünürlüklü LCD gösterge'],
                ['icon' => 'cog', 'text' => 'Hidrolik direksiyon, hassas ve güvenilir kontrol'],
                ['icon' => 'building', 'text' => 'Optimize şasi; kabin montajı/sökümü pratik'],
                ['icon' => 'plug', 'text' => '48V/50A entegre şarj, 16A priz ile uyumlu'],
                ['icon' => 'cart-shopping', 'text' => 'Opsiyonel elektromanyetik joystick ile tek elde kumanda']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
