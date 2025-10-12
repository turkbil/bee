<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KSi201_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'KSi201';
        $titleTr = 'İXTİF KSi201 - 2.0 Ton Li-Ion Ride-on Çift Katlı İstif Makinesi';

        $shortTr = 'Kompakt şasi ve 2236 mm dönüş yarıçapıyla dar koridorlarda çevik çalışan KSi201; 2.5 kW AC sürüş motoru, 24V/205Ah Li‑Ion akü ve entegre 24V/30A şarj cihazıyla 10 km/s hıza ulaşır. Çift kaldırma yapısıyla iki paleti aynı anda taşır, rampalarda 8/16% tırmanır ve ergonomik platformuyla uzun mesafede konfor sunar.';

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

            // PDF: KSi201 (KSi201-EN-Brochure.pdf)
            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'KSi201',
                'Sürüş' => 'Elektrikli, AC kontrol',
                'Operatör Tipi' => 'Ayakta (sürmeli platform)',
                'Kapasite (Q)' => '2000 kg',
                'Çift Kaldırma Kapasitesi' => 'Destek kolu 2000 kg, Direk ile yük 1000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik Aksı – Yük Mesafesi (x)' => '926 mm',
                'Dingil Mesafesi (y)' => '1550 mm',
                'Servis Ağırlığı' => '920 kg',
                'Aks Yükü (yüklü, ön/arka)' => '990 / 1930 kg',
                'Aks Yükü (yüksüz, ön/arka)' => '680 / 240 kg',
                'Teker Tipi' => 'Poliüretan',
                'Ön Teker (boyut)' => '230×75 mm',
                'Arka Teker (boyut)' => '85×70 mm',
                'Destek Teker (boyut)' => '130×55 mm',
                'Teker Sayısı (ön/arka)' => '1x,2 / 4',
                'Ön İz Genişliği (b10)' => '514 mm',
                'Arka İz Genişliği (b11)' => '385 mm',
                'Direk Kaldırma Yüksekliği (h3)' => '1600 mm (opsiyon 2100 mm)',
                'Direk İniş Yüksekliği (h1)' => '1316 mm (1600 mast)',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Direk Tavan Yüksekliği (h4)' => '2112 mm (1600 mast)',
                'İlave Kaldırma (h5)' => '120 mm',
                'Tiller Yüksekliği (h14)' => '1190 / 1290 mm',
                'Alt Yükseklik (h13)' => '92 mm',
                'Toplam Uzunluk (l1)' => '2456 mm',
                'Çatala Kadar Uzunluk (l2)' => '1306 mm',
                'Toplam Genişlik (b1/b2)' => '734 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 × 185 × 1150 mm',
                'Forklar Arası Mesafe (b5)' => '570 mm',
                'Şasi Altı Yerden Yükseklik (m1/m2)' => '16 / 16 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '3026 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2920 mm',
                'Dönüş Yarıçapı (Wa)' => '2236 mm',
                'Sürüş Hızı (yüklü/boş)' => '8.5 / 10 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.18 / 0.23 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.36 / 0.18 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '8% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '2.5 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Batarya' => '24V / 205Ah Li‑Ion, ~70 kg',
                'Şarj Cihazı' => 'Entegre 24V / 30A (ops. 24V/100A harici)',
                'Sürüş Kontrol' => 'AC',
                'Direksiyon' => 'Elektronik',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Standart Direk Seçenekleri' => '1600 mm, 2100 mm (opsiyon)'
            ], JSON_UNESCAPED_UNICODE),

            // 8 özellik (icon + text)
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/205Ah Li‑Ion akü ile fırsat şarjı ve maksimum çalışma süresi'],
                ['icon' => 'bolt', 'text' => '2.5 kW AC tahrik ve 3.0 kW kaldırma motoru ile güçlü performans'],
                ['icon' => 'arrows-alt', 'text' => '2236 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'Çelik koruma kabuğu ve elektromanyetik fren ile güvenlik'],
                ['icon' => 'star', 'text' => '10 km/s’e varan hız, uzun mesafede verimlilik'],
                ['icon' => 'cog', 'text' => 'Pazarda kanıtlanmış komponentlerle düşük bakım ihtiyacı'],
                ['icon' => 'plug', 'text' => 'Entegre 24V/30A şarj cihazı – fişe tak ve şarj et'],
                ['icon' => 'industry', 'text' => 'Çift katlı taşıma: aynı anda iki palet hareketi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
