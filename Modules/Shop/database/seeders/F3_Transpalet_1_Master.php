<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F3_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'F3';
        $titleTr = 'İXTİF F3 - 1.5 Ton Li-Ion Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1.5 ton kapasiteli, kompakt ve güçlü İXTİF F3; Li‑Ion tak‑çıkar batarya (24V/20Ah), ergonomik yeni tiller başı, Platform F mimarisi ve flip kapaklı batarya güvenliğiyle lojistik merkezlerinde maliyetleri düşürür.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2, // Transpalet
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
                'Üretici' => 'İXTİF',
                'Model' => 'F3',
                'Sürüş' => 'Elektrik',
                'Kullanım Tipi' => 'Yayan (Pedestrian)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik Aksı - Çatal Ucu Mesafesi (x)' => '950 mm',
                'Dingil Mesafesi (y)' => '1180 mm',
                'Servis Ağırlığı' => '120 kg',
                'Aks Yükü, Yüklü (ön/arka)' => '480 / 1140 kg',
                'Aks Yükü, Yüksüz (ön/arka)' => '90 / 30 kg',
                'Teker Tipi' => 'Poliüretan (PU)',
                'Ön Teker Ölçüsü' => '210×70 mm',
                'Arka Teker Ölçüsü' => 'Ø80×60 (Ø74×88) mm',
                'Destek Tekerleri (opsiyon)' => 'Ø74×30 mm',
                'Teker Sayısı (ön/arka)' => '1x 2/4 veya 1x 2/2',
                'İz Genişliği Arka (b11)' => '535 / 410 mm',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Sürüş Kolu Yüksekliği (min/max) (h14)' => '750 / 1190 mm',
                'Alçaltılmış Yükseklik (h13)' => '82 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Çatala Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '695 / 590 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 / 150 / 1150 mm',
                'Çatallar Arası Mesafe (b5)' => '685 / 560 mm',
                'Zemin Yüksekliği (m2)' => '25 mm',
                'Koridor Genişliği Ast (1000×1200 çapraz)' => '2160 mm',
                'Koridor Genişliği Ast (800×1200 çapraz)' => '2025 mm',
                'Dönüş Yarıçapı (Wa)' => '1360 mm',
                'Seyir Hızı (yüklü/yüksüz)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.017 / 0.020 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.046 / 0.058 m/s',
                'Maks. Eğim (yüklü/yüksüz)' => '%5 / %16',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.5 kW',
                'Batarya (gerilim/kapasite)' => '24V / 20Ah Li‑Ion',
                'Batarya Ağırlığı' => '5 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses Seviyesi (kulak)' => '<74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'Tak‑çıkar Li‑Ion batarya (24V/20Ah) ile vardiyaya uygun esnek enerji'],
                ['icon' => 'hand', 'text' => 'Yükseltilmiş ergonomik tiller başı; avuç içiyle rahat kontrol'],
                ['icon' => 'shield-alt', 'text' => 'Flip kapaklı batarya; su girişine karşı koruma ve güvenlik'],
                ['icon' => 'layer-group', 'text' => 'Platform F mimarisi; sade konfigürasyon ve esnek şasi seçenekleri'],
                ['icon' => 'industry', 'text' => 'Basit ve güçlü şasi; zorlu lojistik taşımalarında dayanıklılık'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 1360 mm dönüş yarıçapı ile çeviklik'],
                ['icon' => 'box-open', 'text' => '4 ünite/kutu toptan paket; 40’ konteynere 176 ünite ile navlun tasarrufu'],
                ['icon' => 'star', 'text' => 'Lojistik merkezlerinde kanıtlanmış “tough” performans']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
