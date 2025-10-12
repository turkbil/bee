<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_20ETC_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT20-20ETC')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EPT20-20ETC'); return; }

        $variants = [
            [
                'sku' => 'EPT20-20ETC-560',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20-20ETC - 560 mm Çatal Aralığı',
                'short_description' => '560 mm çatal aralığıyla EUR paletlerde maksimum denge ve hızlı giriş-çıkış; -25℃ koşullarda IP67 koruma ve 1505 mm dönüş yarıçapı ile dar alan çevikliği.',
                'long_description' => '<section><h2>560 mm Çatal Aralığı</h2><p>Standart 560 mm çatal aralığı, EUR paletlerde hızlı ve güvenli giriş sağlar. Soğuk oda kapıları ve dar koridorlarda 1505 mm dönüş yarıçapının sağladığı çeviklikle toplama ve yükleme süreçleri hızlanır.</p></section><section><h3>Teknik</h3><p>50/150/1150 mm çatal ölçüleri, 4/4.5 km/s hız ve 0.027/0.038 m/s kaldırma performansı ile sabit ritim sunar. IP67 korumalı tahrik, kauçuk tahrik tekeri ve elektromanyetik fren soğuk ve kaygan zeminlerde güven verir.</p></section><section><h3>Kullanım</h3><p>Süpermarket arka depoları, 3PL ayrıştırma alanları ve merkezi mutfaklarda palet transferi için idealdir. -25℃ koşullarda verimli ve sessiz çalışır.</p></section>',
                'use_cases' => json_encode([
                    ['icon' => 'store', 'text' => 'Perakende arka depoda EUR palet akışı'],
                    ['icon' => 'warehouse', 'text' => '3PL ayrıştırma ve konsolidasyon'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda içi kısa mesafe taşımalar'],
                    ['icon' => 'cart-shopping', 'text' => 'Rampa yükleme ve boşaltma'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ürün transferi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret soğuk zincir sipariş toplama']
                ], JSON_UNESCAPED_UNICODE)
            ],
            [
                'sku' => 'EPT20-20ETC-685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20-20ETC - 685 mm Çatal Aralığı',
                'short_description' => '685 mm çatal aralığı geniş palet ve kasalarda stabil taşıma sunar; IP67 koruma ve Li‑Ion enerji ile -25℃ depolarda güvenilir operasyon sağlar.',
                'long_description' => '<section><h2>685 mm Çatal Aralığı</h2><p>Geniş palet ve kasalarda stabilite için 685 mm aralık idealdir. Kompakt şasi ve 1673 mm toplam uzunluk koridor dönüşlerini kolaylaştırır.</p></section><section><h3>Teknik</h3><p>48V 40Ah Li‑Ion batarya 0.9 kW tahrik ve 0.8 kW kaldırma motorunu besler. 8/20% eğim kabiliyeti, elektromanyetik fren ve kauçuk tahrik tekeri güvenli çekiş sağlar.</p></section><section><h3>Kullanım</h3><p>Dağıtım merkezleri, içecek lojistiği ve catering operasyonlarında geniş yük formatlarında verimli, sessiz ve güvenli taşıma sağlar.</p></section>',
                'use_cases' => json_encode([
                    ['icon' => 'warehouse', 'text' => 'Soğuk zincir dağıtım merkezleri'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasalarında geniş tabanlı taşıma'],
                    ['icon' => 'industry', 'text' => 'Üretim yan hatlarında malzeme besleme'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda kapı eşiklerinde kontrollü manevra'],
                    ['icon' => 'flask', 'text' => 'Kimyasal depolarda sızdırmaz operasyon'],
                    ['icon' => 'cart-shopping', 'text' => 'Kargo ayrıştırma ve yükleme']
                ], JSON_UNESCAPED_UNICODE)
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
                'use_cases' => $v['use_cases'],
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info('✅ Varyant kaydedildi: ' . $v['sku']);
        }
    }
}
