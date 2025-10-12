<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL185_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EPL185';
        $titleTr = 'İXTİF EPL185 - 1.8 Ton Li-Ion Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1.8 ton kapasiteli, 48V Li-Ion (20/30Ah) çıkarılabilir batarya, entegre 48V-10A şarj, 170 kg hafif şasi ve l2=400 mm kompakt gövde ile dar alanlarda çevik ve güvenli kullanım.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),

            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'EPL185',
                'Sürüş' => 'Akülü (Yaya Kumandalı)',
                'Kapasite (Q)' => '1800 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Yük Mesafesi (x)' => '940 mm',
                'Dingil Mesafesi (y)' => '1200 mm',
                'Servis Ağırlığı' => '170 kg (batarya dahil)',
                'Aks Yükü (yüklü sürüş/yük)' => '640 / 1330 kg',
                'Aks Yükü (yüksüz sürüş/yük)' => '130 / 40 kg',
                'Lastik Tipi' => 'PU (Poliüretan)',
                'Sürüş Tekerleği (ØxW)' => 'Ø210 × 70 mm',
                'Yük Tekerleği (ØxW)' => 'Ø80 × 60 mm (ops. Ø74 × 88 mm)',
                'Denge Tekeri (ØxW)' => 'Ø74 × 30 mm',
                'Teker Sayısı (sürüş/denge/yük)' => '1×2 / 4 (1×2 / 2)',
                'İz Genişliği (ön/arka)' => 'b10=450 mm, b11=390 (535) mm',
                'Kaldırma Yüksekliği (h3)' => '115 mm',
                'Çatal Alt Yükseklik (h13)' => '80 mm',
                'Sürüş Kolu Yükseklik (h14)' => '650 / 1170 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Yüke Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '610 (695) mm',
                'Çatal Ölçüsü (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal Aralığı (b5)' => '540 (685) mm',
                'Merkez Yerden Yükseklik (m2)' => '30 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2094 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2146 mm',
                'Dönüş Yarıçapı (Wa)' => '1330 mm',
                'Sürüş Hızı (yüklü/yüksüz)' => '5.0 / 5.5 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.020 / 0.025 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.065 / 0.030 m/s',
                'Tırmanma Kabiliyeti (yüklü/yüksüz)' => '6% / 16%',
                'Fren Tipi' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.9 kW (fırçasız DC)',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Batarya (V/Ah)' => '48V / 20 Ah (ops. 30 Ah) Li-Ion',
                'Batarya Ağırlığı' => '14 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)',
                'Şarj Cihazı' => '48V-10A, entegre (ops. harici 48V-10A)',
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V Li-Ion batarya (20/30Ah) BMS ile güvenli ve sürdürülebilir enerji'],
                ['icon' => 'plug', 'text' => 'Entegre 48V-10A şarj ile vardiya aralarında hızlı fırsat şarjı'],
                ['icon' => 'industry', 'text' => 'Yüzer denge tekerleriyle stabil şasi ve yüksek geçiş kabiliyeti'],
                ['icon' => 'arrows-alt', 'text' => 'l2=400 mm kompakt gövde ve 1330 mm dönüş yarıçapı ile çeviklik'],
                ['icon' => 'bolt', 'text' => 'Fırçasız DC tahrik ile verimli, bakım dostu performans'],
                ['icon' => 'shield-alt', 'text' => 'Metal korumalı batarya kapağı ile güvenli bağlantı ve servis erişimi'],
                ['icon' => 'cog', 'text' => 'Modüler yapı: teker ve enerji seçenekleriyle filoya uyarlanabilirlik'],
                ['icon' => 'star', 'text' => 'Creep modu ile dar alanda hassas ve güvenli manevra']
            ], JSON_UNESCAPED_UNICODE),
        ]);
    }
}
