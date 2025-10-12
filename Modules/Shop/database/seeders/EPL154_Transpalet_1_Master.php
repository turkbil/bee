<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL154_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'EPL154';

        $titleTr = 'İXTİF EPL154 - 1.5 Ton Li-Ion Palet Transpaleti';
        $shortTr = '1.5 ton kapasiteli, 24V-30Ah çıkarılabilir Li-Ion bataryalı, l2=400 mm kompakt gövde, 160 kg servis ağırlığı, 1330 mm dönüş yarıçapı, 4.5/5 km/s hız ve entegre şarj cihazı ile dar alanlarda güvenli ve çevik kullanım.';

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
                'Model' => 'EPL154',
                'Sürüş' => 'Akülü (Yaya Kumandalı)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Yük Mesafesi (x)' => '940 (875) mm',
                'Dingil Mesafesi (y)' => '1200 (1135) mm',
                'Servis Ağırlığı' => '160 kg (batarya dahil)',
                'Aks Yükü (yüklü sürüş/yük)' => '555 / 1105 kg',
                'Aks Yükü (yüksüz sürüş/yük)' => '130 / 30 kg',
                'Lastik Tipi (sürüş/yük)' => 'PU / PU',
                'Sürüş Tekerleği (çap x en)' => 'Ø210 x 70 mm',
                'Yük Tekerleği (çap x en)' => 'Ø80 x 60 (Ø74 x 88) mm',
                'Denge Tekeri (çap x en)' => 'Ø74 x 30 mm',
                'Teker Sayısı (sürüş/denge/yük)' => '1 x 2 / 4 (1 x 2 / 2)',
                'İz Genişliği (ön/sürüş) b10' => '450 mm',
                'İz Genişliği (arka/yük) b11' => '390 (535) mm',
                'Kaldırma Yüksekliği (h3)' => '115 mm',
                'Sürüş Kolu Yükseklik (min/max) h14' => '650 / 1170 mm',
                'Yerden Yükseklik (m2)' => '30 mm',
                'Toplam Uzunluk (l1)' => '1550 mm',
                'Yüke Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '610 (695) mm',
                'Çatal Ölçüleri (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal Aralığı (b5)' => '540 (685) mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2094 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2146 mm',
                'Dönüş Yarıçapı (Wa)' => '1330 mm',
                'Sürüş Hızı (yüklü/yüksüz)' => '4.5 / 5 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.028 / 0.031 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.068 / 0.043 m/s',
                'Tırmanma Kabiliyeti (yüklü/yüksüz)' => '6% / 16%',
                'Fren Tipi' => 'Elektrik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Maks. Batarya Ölçüsü' => '270 × 110 × 400 mm',
                'Batarya' => '24 V / 30 Ah Li-Ion (çıkarılabilir)',
                'Batarya Ağırlığı' => '10 kg',
                'Sürüş Kontrol Tipi' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '< 74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V-30Ah çıkarılabilir Li-Ion batarya, metal korumalı kapak ile güvenli erişim'],
                ['icon' => 'bolt', 'text' => 'Entegre (on-board) şarj cihazı ile hızlı ve esnek şarj imkânı'],
                ['icon' => 'layer-group', 'text' => 'Endüstriyel yüzer denge tekerleri ile yüksek stabilite ve geçiş kabiliyeti'],
                ['icon' => 'compress', 'text' => 'Kompakt yapı (l2=400 mm) ve 160 kg düşük ağırlık ile dar koridor çevikliği'],
                ['icon' => 'arrows-alt', 'text' => '1330 mm dönüş yarıçapı ile raf aralarında kolay manevra'],
                ['icon' => 'shield-alt', 'text' => 'BMS’li Li-Ion sistem; koruma, izleme ve kullanım kolaylığı'],
                ['icon' => 'hand', 'text' => 'Kaplumbağa (creep) modu: dikey tutuşta yavaş ve güvenli sürüş'],
                ['icon' => 'cog', 'text' => 'EPT20-15ET ile ortak olgun tahrik sistemi: dayanıklı ve servis dostu']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master oluşturuldu: ' . $sku);
    }
}
