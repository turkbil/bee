<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1;    // İXTİF
        $sku = 'F4-201';

        $titleTr = 'İXTİF F4 201 - 2.0 Ton Li-Ion Transpalet';
        $shortTr = '48V sistem gücüyle 2.0 ton kapasite sunan, l2=400 mm kompakt gövdeye ve yalnızca 140 kg servis ağırlığına sahip elektrikli transpalet. Çıkarılabilir 2×24V/20Ah Li-Ion batarya, hızlı şarj ve düşük bakım ile verimliliği artırır.';

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

            // PDF verisi
            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'F4 201',
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Yük Mesafesi (x)' => '950 mm',
                'Dingil Mesafesi (y)' => '1180 mm',
                'Servis Ağırlığı' => '140 kg',
                'Teker Tipi' => 'Poliüretan (PU)',
                'Teker Ölçüsü - Ön' => '210×70 mm',
                'Teker Ölçüsü - Arka' => '80×60 mm',
                'Destek Teker (opsiyon)' => '74×30 mm',
                'Teker Yapısı (ön/arka)' => '1x — / 4',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Tiller Yüksekliği min./max. (h14)' => '750 / 1190 mm',
                'İndirilmiş Yükseklik (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Çatala Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '590 / 695 mm',
                'Çatal Boyutları (s/e/l)' => '50 × 150 × 1150 mm',
                'Çatal Arası Mesafe (b5)' => '560 / 685 mm',
                'Zemin Boşluğu (m2)' => '30 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2160 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2025 mm',
                'Dönüş Yarıçapı (Wa)' => '1360 mm',
                'Sürüş Hızı (yüklü/boş)' => '4.5 / 5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.016 / 0.020 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.058 / 0.046 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '8% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.9 kW',
                'Kaldırma Motoru (S3 15%)' => '0.7 kW',
                'Akü (volt/kapasite)' => '48 V / 20 Ah (2×24V/20Ah, çıkarılabilir Li-Ion)',
                'Akü Ağırlığı' => '≈10 kg (modül başına)',
                'Sürüş Kontrolü' => 'BLDC',
                'Direksiyon Tasarımı' => 'Mekanik',
                'Ses Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V elektrik sistemiyle artan güç ve güvenilirlik'],
                ['icon' => 'plug', 'text' => 'Tak-çıkar (plug-in/out) Li-Ion modüllerle esnek enerji yönetimi'],
                ['icon' => 'compress', 'text' => 'l2=400 mm kompakt şasi ile dar koridor çevikliği'],
                ['icon' => 'weight-hanging', 'text' => '2.0 ton nominal taşıma kapasitesi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ve sağlam kumanda kolu'],
                ['icon' => 'cog', 'text' => 'Bakım ihtiyacını düşüren basit ve modüler tasarım'],
                ['icon' => 'layer-group', 'text' => 'Platform F mimarisiyle farklı şasi/çatal seçenekleri'],
                ['icon' => 'box-open', 'text' => '4 ünite/kolide tedarik ve 40’ konteynere 164 ünite ile lojistik tasarrufu']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master: F4-201 oluşturuldu / güncellendi');
    }
}
