<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F2_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'F2';

        $titleTr = 'İXTİF F2 - 1.5 Ton Li-Ion Transpalet';
        $shortTr = '1.5 ton kapasiteli, 120 kg kendi ağırlığına rağmen verimliliği artıran, perakende ve market alanları için tasarlanmış kompakt elektrikli transpalet. Yeni ergonomik tiller başı, Li‑Ion 24V/20Ah batarya, 4/4.5 km/s hız ve 1360 mm dönüş yarıçapıyla günlük operasyonları hızlandırır.';

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
                'Model' => 'F2',
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Yük Mesafesi (x)' => '950 mm',
                'Dingil Mesafesi (y)' => '1180 mm',
                'Servis Ağırlığı' => '120 kg',
                'Dingil Yükü (yüklü) ön/arka' => '480 / 1140 kg',
                'Dingil Yükü (yüksüz) ön/arka' => '90 / 30 kg',
                'Lastik Tipi' => 'Poliüretan (PU)',
                'Tahrik Tekerliği (ön)' => '210×70 mm',
                'Yük Tekerleri (arka)' => '⌀80×60 (⌀74×88) mm',
                'Denge Tekerleri' => '⌀74×30 mm (opsiyonel)',
                'Tekerlek Dizilimi (ön/arka)' => '1× / 2-4 | 1× / 2-2',
                'Arka İz Genişliği (b11)' => '535 / 410 mm',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Tiller Yüksekliği min./max. (h14)' => '750 / 1190 mm',
                'Alçaltılmış Yükseklik (h13)' => '82 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Çatal Ucu Hariç Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '695 / 590 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 / 150 / 1150 mm',
                'Çatallar Arası Mesafe (b5)' => '685 / 560 mm',
                'Şase Altı Yerden Yükseklik (m2)' => '25 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2160 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2025 mm',
                'Dönüş Yarıçapı (Wa)' => '1360 mm',
                'Sürüş Hızı (yüklü/boş)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.017 / 0.020 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.046 / 0.058 m/s',
                'Maks. Eğim (yüklü/boş)' => '5% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.5 kW',
                'Batarya' => '24V / 20Ah Li‑Ion',
                'Batarya Ağırlığı' => '5 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses Seviyesi (kulak)' => '<74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/20Ah Li‑Ion batarya ile fırsat şarj ve bakım gerektirmeyen yapı'],
                ['icon' => 'bolt', 'text' => '0.75 kW sürüş, 0.5 kW kaldırma motoru ile dengeli performans'],
                ['icon' => 'cart-shopping', 'text' => 'Perakende, market ve AVM koridorlarında çevik kullanım'],
                ['icon' => 'hand', 'text' => 'Yeni ergonomik tiller başı ile avuç içiyle rahat kontrol'],
                ['icon' => 'industry', 'text' => 'Kompakt şasi, 1360 mm dönüş yarıçapıyla dar alan manevrası'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ile güvenli duruş'],
                ['icon' => 'layer-group', 'text' => 'Platform F mimarisi: yapılandırmayı sadeleştirir ve esneklik sağlar'],
                ['icon' => 'cog', 'text' => 'Düşük TCO: kutu başına 4 ünite ve konteynerde 176 ünite ile lojistik tasarruf']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
