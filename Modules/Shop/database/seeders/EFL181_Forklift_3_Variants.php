<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL181_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFL181')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFL181');
            return;
        }

        $variants = [
            [
                'sku' => 'EFL181-920',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL181 - 920 mm Çatal',
                'short_description' => '920 mm çatal uzunluğu, kısa palet ve dar koridorlarda çeviklik sağlar. 1.8 ton kapasite ve 48V Li-Ion sistemle iç/dış mekânda hızlı, ekonomik ve sessiz operasyon sunar.',
                'body' => '<section><h3>Kısa çatal, yüksek çeviklik</h3><p>920 mm çatal, dar alan manevralarında paletin araca yaklaşmasını kolaylaştırır. Koridor içinde minimum “salınım” ile raf önü hizalamaları hızlanır, özellikle e-ticaret ve yedek parça depolarında verim artar.</p></section><section><h3>Teknik uyum</h3><p>48V/150Ah Li-Ion batarya ve AC tahrik altyapısı, 8.5/9 km/s seyir ve 0.25/0.30 m/s kaldırma hızlarıyla desteklenir. 1920 mm dönüş yarıçapı, 2015 mm L2 ve 1080 mm genişlik ile dar koridor standardına uyum sürer.</p></section><section><h3>Operasyonel sahneler</h3><p>Pickup istasyonları, cross-dock noktaları ve üretim hücrelerinde kısa mesafeli, sık manevralı görevler için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret istasyonlarında hızlı raf önü hizalama'],
                    ['icon' => 'warehouse', 'text' => 'Cross-dock geçişlerinde seri yük aktarımı'],
                    ['icon' => 'store', 'text' => 'Perakende arka depo dar koridor operasyonları'],
                    ['icon' => 'car', 'text' => 'Otomotiv kutu paletlerinde kısa dönüş mesafesi'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP yaklaşımı'],
                    ['icon' => 'building', 'text' => 'Tesis içi bakım malzemesi taşıma']
                ]
            ],
            [
                'sku' => 'EFL181-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL181 - 1070 mm Çatal',
                'short_description' => '1070 mm çatal, standart paletlerle uyumu korurken manevrayı kolaylaştırır. Giriş seviyesi bütçe ile Li-Ion fırsat şarjı ve AC tahrikin kombinasyonunu sunar.',
                'body' => '<section><h3>Dengeli çözüm</h3><p>1070 mm çatal, EUR paletlerde denge ve erişimi harmanlar. Hizalama hassasiyeti ve raf içi manevra kolaylığı, genel depo akışında hız kazandırır.</p></section><section><h3>Veri odaklı performans</h3><p>0.25/0.30 m/s kaldırma, 8.5/9 km/s seyir ve 10.5%/15% tırmanma kabiliyeti ile rampa ve iç saha geçişleri stabildir. 905/920 mm iz açıklıkları rijitliği artırır.</p></section><section><h3>Kullanım senaryosu</h3><p>Günde birkaç saatlik operasyonlarda, multi-use bölgelerde ve karma ürün portföyünde pratik seçimdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo akışında karma palet yönetimi'],
                    ['icon' => 'box-open', 'text' => 'Sipariş konsolidasyon alanında hat besleme'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası yükleme/boşaltma süreçleri'],
                    ['icon' => 'snowflake', 'text' => 'Gıda sevkiyatında kısa mesafe taşıma'],
                    ['icon' => 'pills', 'text' => 'Kozmetik-ilaç için hassas palet hareketleri'],
                    ['icon' => 'industry', 'text' => 'Yarı mamul ve ambalaj lojistiği']
                ]
            ],
            [
                'sku' => 'EFL181-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL181 - 1150 mm Çatal',
                'short_description' => '1150 mm çatal, standart EUR paletlerde güvenli denge sunar. 3000 mm kaldırma ve 48V Li-Ion enerjiyle vardiyada fırsat şarjı yaparak kesintisiz akış sağlar.',
                'body' => '<section><h3>Standart palet ustası</h3><p>1150 mm uzunluk, 1000×1200 ve 800×1200 paletlerle yaygın uyumu destekler. Yan kaydırma ile kullanıldığında kapasitede ~150–200 kg düşüm dikkate alınmalıdır.</p></section><section><h3>Koridor verimliliği</h3><p>Ast(1000×1200)=3525 mm, Ast(800×1200)=3725 mm değerleri, dar raf sistemlerinde planlı rotalama sağlar. 1920 mm dönüş yarıçapı ile raf değişimi çeviktir.</p></section><section><h3>Uygulama örnekleri</h3><p>Toplu sevkiyat alanlarında güvenli yük dağılımı ve düz girişli rampa operasyonları için tercih edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Toplu sevkiyat ve rampada güvenli giriş-çıkış'],
                    ['icon' => 'warehouse', 'text' => 'Raf arası standart palet transferi'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponentlerinde dengeli taşıma'],
                    ['icon' => 'industry', 'text' => 'İmalat besleme hatlarında rutin akış'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalajların dikkatli yerleşimi'],
                    ['icon' => 'building', 'text' => 'Tesis lojistiğinde genel amaçlı kullanım']
                ]
            ],
            [
                'sku' => 'EFL181-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL181 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yüklerde destek yüzeyini artırır. Li-Ion enerji ve AC tahrik ile daha yumuşak hızlanma ve hassas konumlandırma sağlar.',
                'body' => '<section><h3>Uzun yük stabilitesi</h3><p>1220 mm, uzun ambalajlı ürünlerde ağırlık dağılımını iyileştirir. Düz yüzeylerde daha düşük yük merkezi sapması ile operatör güveni artar.</p></section><section><h3>Enerji ve kontrol</h3><p>48V/150Ah batarya fırsat şarjıyla vardiyada süreklilik; AC sürüş ise hassas akış sağlar. 0.43/0.45 m/s indirme hızı dikkatli yük tahliyesi sunar.</p></section><section><h3>Uygulama alanları</h3><p>Mobilya, profil, kasa ve uzun ambalajlarda taşıma kalitesi yükselir.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Uzun ambalaj ve kasa taşımaları'],
                    ['icon' => 'warehouse', 'text' => 'Geniş koridorlarda derin raf yerleşimi'],
                    ['icon' => 'box-open', 'text' => 'Toplu yükleme alanlarında stabil istif'],
                    ['icon' => 'flask', 'text' => 'Kimyasal bidon ve varillerde denge'],
                    ['icon' => 'car', 'text' => 'Otomotiv gövde parçaları ve profiller'],
                    ['icon' => 'building', 'text' => 'Bakım atölyelerinde hacimli malzeme']
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
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant: {$v['sku']}");
        }
    }
}
