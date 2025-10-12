<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL181_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFL181';
        $titleTr = 'İXTİF EFL181 - 1.8 Ton 48V Li-Ion Denge Ağırlıklı Forklift';
        $shortTr = 'İXTİF EFL181, 1.8 ton kapasiteli, 48V/150Ah Li-Ion bataryalı kompakt 4 tekerlekli elektrikli forklift. 8.5/9 km/s hız, 3000 mm kaldırma, 1920 mm dönüş yarıçapı ve dahili tek faz şarj cihazı ile iç/dış mekanda esnek ve ekonomik operasyon sunar.';

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
                'Model' => 'EFL181',
                'Sürüş' => 'Elektrikli (AC)',
                'Operatör Tipi' => 'Oturmalı',
                'Kapasite (Q)' => '1800 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Yük Mesafesi (x)' => '405 mm',
                'Dingil Mesafesi (y)' => '1260 mm',
                'Servis Ağırlığı' => '3030 kg',
                'Ön/Araç İzi (b10/b11)' => '905 / 920 mm',
                'Lastik Tipi' => 'Katı (Solid Rubber)',
                'Ön Lastik' => '6.5-10',
                'Arka Lastik' => '5.00-8',
                'Direk Eğimi (α/β)' => '6° / 10°',
                'Direk, Alçalmış Yükseklik (h1)' => '1995 mm (3.0 m direk)',
                'Serbest Kaldırma (h2)' => '130 mm (standart direk)',
                'Kaldırma Yüksekliği (h3)' => '3000 mm (standart)',
                'Direk, Tam Açılmış (h4)' => '4028 mm (3.0 m direk)',
                'Üst Koruma (h6)' => '2080 mm',
                'Koltuk Yüksekliği (h7)' => '1060 mm',
                'Çeki Kancası Yüksekliği (h10)' => '370 mm',
                'Toplam Uzunluk (l1)' => '2935 mm',
                'Çatala Kadar Uzunluk (l2)' => '2015 mm',
                'Toplam Genişlik (b1/b2)' => '1080 mm',
                'Çatal Ölçüsü (s/e/l)' => '40 × 100 × 920 mm',
                'Forklift Sınıfı' => 'ISO 2A',
                'Fork Taşıyıcı Genişliği (b3)' => '1040 mm',
                'Yer Açıklığı Alt Direk (m1)' => '115 mm',
                'Yer Açıklığı Orta Dingil (m2)' => '120 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '3525 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '3725 mm',
                'Dönüş Yarıçapı (Wa)' => '1920 mm',
                'Sürüş Hızı (Yüklü/Yüksüz)' => '8.5 / 9 km/s',
                'Kaldırma Hızı (Yüklü/Yüksüz)' => '0.25 / 0.30 m/s',
                'İndirme Hızı (Yüklü/Yüksüz)' => '0.43 / 0.45 m/s',
                'Maks. Eğilme Kabiliyeti' => '10.5% / 15%',
                'Çekiş Motoru (S2 60 dk)' => '6 kW',
                'Kaldırma Motoru (S3 15%)' => '7.5 kW',
                'Batarya' => '48V / 150Ah Li-Ion',
                'Batarya Ağırlığı' => '115 kg',
                'Sürüş Kontrolü' => 'AC',
                'Fren Sistemi' => 'Hidrolik servis, mekanik park',
                'Gürültü Seviyesi' => '70 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V/150Ah Li-Ion batarya ile fırsat şarjı ve yüksek verim'],
                ['icon' => 'plug', 'text' => 'Tek faz entegre şarj cihazı (16A priz, Plug&Play)'],
                ['icon' => 'industry', 'text' => 'I/C şasi mimarisi üzerinde tasarlanan kompakt 4 teker yapısı'],
                ['icon' => 'arrows-alt', 'text' => '1920 mm dönüş yarıçapı ile dar alanda çeviklik'],
                ['icon' => 'bolt', 'text' => 'Güçlü AC çekiş motoru ve güvenilir tahrik'],
                ['icon' => 'shield-alt', 'text' => 'Yağmura dayanıklı bileşenler ile iç/dış kullanım'],
                ['icon' => 'star', 'text' => 'Geliştirilmiş ergonomi: ayarlanabilir direksiyon ve bucket koltuk'],
                ['icon' => 'briefcase', 'text' => 'Giriş seviyesi TCO: az bakım, uygun toplam sahip olma maliyeti']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
