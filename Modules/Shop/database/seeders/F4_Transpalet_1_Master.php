<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'F4-1500';
        $titleTr = 'İXTİF F4 - 1.5 Ton Li-Ion Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'İXTİF F4, 1.5 ton kapasiteli, 24V 20Ah modüler Li‑Ion batarya yuvasına sahip, yalnızca 120 kg ağırlığında ve l2=400 mm kompakt şasiyle dar alanlarda çevik hareket eden elektrikli transpalettir. Çift batarya seçeneği, sökülebilir saklama gözü ve farklı çatal ölçüleriyle tam gün operasyonlara uygundur.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2,
            'brand_id' => 1,
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
                'Model' => 'F4',
                'Sürüş' => 'Elektrikli (yaya kumandalı)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik aksına mesafe (x)' => '950 mm',
                'Dingil mesafesi (y)' => '1180 mm',
                'Servis Ağırlığı' => '120 kg',
                'Aks Yükü, yüklü (ön/arka)' => '480 / 1140 kg',
                'Aks Yükü, yüksüz (ön/arka)' => '90 / 30 kg',
                'Tekerlek Tipi' => 'Poliüretan',
                'Ön Tekerlek' => '210 × 70 mm',
                'Arka Tekerlek' => '80 × 60 mm',
                'Destek (castor) tekeri' => '74 × 30 mm',
                'Teker sayısı (ön/arka)' => '1x — / 4',
                'Ön İz Genişliği (b10)' => '410 / 535 mm',
                'Arka İz Genişliği (b11)' => '—',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Tiller Yüksekliği min./max. (h14)' => '750 / 1190 mm',
                'İndirgenmiş Yükseklik (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Çatal Ucuna Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '590 / 695 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 × 150 × 1150 mm',
                'Çatal Açıklığı (b5)' => '560 / 685 mm',
                'Şasi Altı Açıklık (m2)' => '25 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2160 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2025 mm',
                'Dönüş Yarıçapı (Wa)' => '1360 mm',
                'Yürüyüş Hızı (yüklü/yüksüz)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.017 / 0.020 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.058 / 0.046 m/s',
                'Tırmanma Kabiliyeti (yüklü/yüksüz)' => '6% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.5 kW',
                'Akü (V/Ah)' => '24V / 20Ah (1 modül); 2×24V/20Ah opsiyon',
                'Akü Ağırlığı' => '5 kg (modül başına)',
                'Enerji Tüketimi (DIN EN 16796)' => '0.18 kWh/h',
                'VDI 2198 İş Çevrimi Çıkışı' => '60 t/h',
                'VDI 2198 Verimliliği' => '333.33 t/kWh',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'İki güç yuvalı tasarım: 2×24V 20Ah Li‑Ion ile vardiya boyu çalışma'],
                ['icon' => 'layer-group', 'text' => 'Platform F mimarisi: 4 farklı şasi seçimi ile kurulum esnekliği'],
                ['icon' => 'compress', 'text' => 'Kompakt gövde (l2=400 mm) dar koridor çevikliği sağlar'],
                ['icon' => 'weight-hanging', 'text' => '1.5 ton kapasite ile standart ve büyük yükleri güvenle taşır'],
                ['icon' => 'cog', 'text' => 'Sökülebilir saklama bölmesi günlük ekipman erişimini kolaylaştırır'],
                ['icon' => 'shield-alt', 'text' => 'Stabilize tekerlek opsiyonu: dengesiz zeminde ilave güvenlik'],
                ['icon' => 'arrows-alt', 'text' => 'Farklı çatal uzunluğu ve genişliği seçenekleri'],
                ['icon' => 'star', 'text' => 'Toptan sevkiyatta kutu başına 4 ünite ile maliyet avantajı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
