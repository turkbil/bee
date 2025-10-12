<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL162_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'TDL162';

        $titleTr = 'İXTİF TDL162 - 1.6 Ton 80V Li-Ion 3 Teker Forklift';
        $shortTr = 'TDL162, 80V çift sürüşlü, 1.6 ton kapasiteli kompakt 3 teker forklift ile hız, ivmelenme ve eğim performansında sınıfına göre belirgin artış sunar. 80V/280Ah Li-Ion batarya, entegre şarj + harici şarj girişi ve geniş görüş/ergonomi ile çok vardiyalı depolar için tasarlanmıştır.';

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
                'Sürüş' => 'Elektrik (80V çift sürüş)',
                'Kapasite' => '1600 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Servis Ağırlığı' => '3146 kg',
                'Teker Tipi' => 'Katı (Solid rubber)',
                'Ön Lastik' => '18x7-8',
                'Arka Lastik' => '140/55-9',
                'Ön/Arka Teker Adedi' => '2x / 2',
                'İz Genişliği (ön/arka)' => '910 / 210 mm',
                'Direk Eğimi (ileri/geri)' => '7° / 6°',
                'Direk Kapalı Yükseklik (h1)' => '1995 mm',
                'Serbest Kaldırma (h2)' => '100 mm (standart direk)',
                'Kaldırma Yüksekliği (h3)' => '3000 mm (standart direk)',
                'Direk Açık Yükseklik (h4)' => '4058 mm (standart direk)',
                'Üst Koruma Yüksekliği' => '2140 mm',
                'Oturma Yüksekliği' => '1070 mm',
                'Çeki Kancası Yüksekliği' => '580 mm',
                'Toplam Uzunluk (l1)' => '2935 mm',
                'Yük İleri Ucu Uzunluğu (l2)' => '2015 mm',
                'Toplam Genişlik (b1/b2)' => '1050 mm',
                'Çatal Ölçüleri (s/e/l)' => '40×100×920 mm',
                'Çatal Taşıyıcı Sınıf/Tip' => '2A',
                'Çatal Taşıyıcı Genişliği' => '1040 mm',
                'Şasi Altı Boşluk (mast altında)' => '90 mm',
                'Aks Orta Boşluk' => '107 mm',
                'Koridor Genişliği 1000×1200 (enine)' => '3339 mm',
                'Koridor Genişliği 800×1200 (boyuna)' => '3464 mm',
                'Dönüş Yarıçapı (Wa)' => '1639 mm',
                'Hız (yükle/boş)' => '16 / 17 km/s',
                'Kaldırma Hızı (yükle/boş)' => '0.50 / 0.52 m/s',
                'İndirme Hızı (yükle/boş)' => '0.55 / 0.55 m/s',
                'Maks. Tırmanma (yükle/boş)' => '20% / 25%',
                'Servis Freni' => 'Elektromanyetik',
                'Park Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '5.4 kW × 2',
                'Kaldırma Motoru (S3 15%)' => '18 kW',
                'Akü' => '80V / 280Ah Li-Ion',
                'Akü Ağırlığı' => '220 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon Tipi' => 'Hidrolik',
                'Sürücü Kulak Seviyesi Ses' => '65 dB(A)',
                'Şarj Cihazı Çıkış Akımı' => '35 A',
                'Aks Yükü (yükle ön/arka)' => '4129 / 617 kg',
                'Aks Yükü (boş ön/arka)' => '1576 / 1570 kg',
                'Dingil Mesafesi (y)' => '1470 mm',
                'Tahrik Aksına Mesafe (x)' => '375 mm'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'bolt', 'text' => '80V çift sürüş sistemiyle güçlü çekiş ve yüksek ivmelenme'],
                ['icon' => 'battery-full', 'text' => '80V/280Ah Li-Ion batarya ile uzun menzil ve fırsat şarjı'],
                ['icon' => 'plug', 'text' => 'Aynı makinede entegre şarj + harici hızlı şarj girişi'],
                ['icon' => 'industry', 'text' => 'Nominal 1600 kg kapasiteyi 4.5 m’ye kadar koruyan sağlam yapı'],
                ['icon' => 'star', 'text' => 'Geniş görüş alanı ve düşük gürültü ile konforlu sürüş'],
                ['icon' => 'briefcase', 'text' => 'Joystick hidrolik kontrol opsiyonu ile tek elle ergonomi'],
                ['icon' => 'cog', 'text' => 'Optimize şasi ile artırılmış hız, kaldırma ve eğim performansı'],
                ['icon' => 'microchip', 'text' => 'LED ekran ve performans modu seçici ile sezgisel kullanım']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
