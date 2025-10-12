<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL201_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'TDL-201')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: TDL-201'); return; }

        $variants = [
            [
                'sku' => 'TDL-201-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL201 - 3.0 m Direk (Standart Mast)',
                'short_description' => '2.0 ton kapasiteyi 3.0 m standart kaldırma ile birleştirir. 48V/405Ah Li-Ion seçenek, entegre şarj ve çift AC tahrik ile yoğun vardiya verimliliği.',
                'long_description' => '<section><h2>3.0 m Standart Mast</h2><p>3000 mm kaldırma; genel depo ve cross-dock akışlarının çoğu için yeterli erişimi sunar. 2A sınıfı taşıyıcı ve 40×122×1070 mm çatal seti, geniş ürün gamıyla uyumludur.</p></section><section><h3>Teknik</h3><p>5.4kW×2 sürüş ve 11kW kaldırma motoru dengeli döngüler sağlar. Entegre 48V-50A şarj ünitesi ile fırsat şarjı yapılabilir.</p></section><section><h3>Kullanım</h3><p>Perakende DC, FMCG ve otomotiv tedarik depolarında günlük yükleme/boşaltma.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Rampa yükleme-boşaltma'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock akış yönetimi'],
                    ['icon' => 'store', 'text' => 'Sipariş konsolidasyonu'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtım merkezleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv bileşen depoları'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresi lojistiği']
                ]
            ],
            [
                'sku' => 'TDL-201-4800',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL201 - 4.8 m Direk (Serbest Kaldırma)',
                'short_description' => '4.8 m mast ve yüksek serbest kaldırma ile kapı-sprinkler kısıtlarında kontrollü yükseliş. Operatör için geniş görüş ve LCD ekran kolaylığı.',
                'long_description' => '<section><h2>4.8 m Serbest Kaldırma</h2><p>Serbest kaldırma ile mast toplam yüksekliği artmadan çatallar yükseltilir; dar kapı geçişleri veya düşük tavan kısıtlarında verim sağlar.</p></section><section><h3>Performans</h3><p>Çift AC tahrik ve Li-Ion batarya ile süreklilik; elektromanyetik frenler güvenli duruş sağlar.</p></section><section><h3>Senaryolar</h3><p>Perakende, ilaç ve elektronik depolarında üst raf erişimi ve toplama.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Üst raf istifleme'],
                    ['icon' => 'box-open', 'text' => 'Toplama ve konsolidasyon'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası depo'],
                    ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depo'],
                    ['icon' => 'microchip', 'text' => 'Elektronik dağıtım'],
                    ['icon' => 'industry', 'text' => 'WIP alanları']
                ]
            ],
            [
                'sku' => 'TDL-201-6000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL201 - 6.0 m Direk (Serbest Kaldırma)',
                'short_description' => '6.0 m’e kadar erişim, 2.0 ton sınıfında yüksek raflı depolar için geniş esneklik. Li-Ion enerjiyle düşük bakım ve hızlı ara şarj.',
                'long_description' => '<section><h2>6.0 m Yüksek Erişim</h2><p>6000 mm mast, yüksek raflı DC’lerde alan verimliliğini artırır. Serbest kaldırma ile sprinkler altında güvenli çalışma mümkündür.</p></section><section><h3>Teknik</h3><p>0.35/0.43 m/s kaldırma ve 15/16 km/s seyir değerleriyle yoğun vardiyada akıcı döngüler sunar.</p></section><section><h3>Uygulama</h3><p>3PL, FMCG ve elektronik dağıtım merkezlerinde üst raf erişimi.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı istifleme'],
                    ['icon' => 'box-open', 'text' => 'Yoğun palet akışı'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim depoları'],
                    ['icon' => 'microchip', 'text' => 'Elektronik DC'],
                    ['icon' => 'car', 'text' => 'Otomotiv tedarik deposu'],
                    ['icon' => 'industry', 'text' => 'Üretim sonu depo']
                ]
            ]
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->updateOrInsert(['sku' => $v['sku']], [
                'sku' => $v['sku'],
                'parent_product_id' => $m->product_id,
                'variant_type' => $v['variant_type'],
                'category_id' => $m->category_id,
                'brand_id' => $m->brand_id,
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }
        $this->command->info('✅ Variants: TDL-201 (3 adet)');
    }
}
