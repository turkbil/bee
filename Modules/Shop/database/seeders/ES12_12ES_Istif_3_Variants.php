<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES12_12ES_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'ES12-12ES')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: ES12-12ES');
            return;
        }

        $variants = [
            [
                'sku' => 'ES12-12ES-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES12-12ES - 1150 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal ile EUR/ISO paletlerde optimum denge. 1.2 ton kapasite, 3015 mm erişim ve 1408 mm dönüş yarıçapı ile dar koridorlarda kusursuz uyum.',
                'body' => '<section><h3>1150 mm Standart Çatal: Evrensel Uyum</h3><p>1150 mm çatal uzunluğu, Avrupa ve uluslararası palet standartlarında en yaygın boy olup ES12-12ES gövdesiyle uyumlu ağırlık dağılımı sağlar. 1740 mm şasi uzunluğu ve 800 mm genişlik, 2150–2225 mm koridor gereksinimi ile raf aralarında dengeli akış oluşturur. 2×12V/105Ah akü ve 2.2 kW kaldırma motoru, 1200 kg yüklerin 3015 mm seviyesine güvenle taşınmasını mümkün kılar. Poliüretan tekerlekler düşük yuvarlanma direnci sunarken elektromanyetik fren ani duruşlarda kontrol sağlar.</p><p>Bu varyant, inbound–outbound trafiğinde palet kabul, ara stoklama ve replenishment operasyonlarında evrensel çözüm sunar. Proje gereksinimlerine göre taşıyıcı genişliği ve yük koruma ızgarası gibi aksesuarlar eklenebilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Genel depo operasyonlarında EUR palet taşıma'],
                    ['icon' => 'warehouse', 'text' => '3PL içinde cross-dock ve kısa mesafe istif'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf içi replenishment'],
                    ['icon' => 'industry', 'text' => 'Üretim WIP besleme ve toplama'],
                    ['icon' => 'flask', 'text' => 'Kimya tesislerinde kapalı alan transferi'],
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında tampon bölge istifi']
                ]
            ],
            [
                'sku' => 'ES12-12ES-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES12-12ES - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yüklerin güvenli taşınması için ek destek sunar. 1.2 ton kapasite ve dengeli DC sürüş ile orta-uzun paletlerde stabil istif performansı.',
                'body' => '<section><h3>1220 mm Uzatılmış Çatal: Uzun Paletler İçin Destek</h3><p>1220 mm çatal, uzun veya çıkıntılı yüklerin dengeli taşınmasına yardım eder. 600 mm yük merkezi korunarak 1200 kg nominal kapasite ile çalışır; yük geometrisine bağlı olarak efektif kapasite grafiği dikkate alınmalıdır. ES12-12ES’in DC sürüş karakteri ve 1408 mm dönüş yarıçapı, koridor planlaması doğru yapıldığında uzun çatalın getirdiği moment artışını operasyonel olarak tolere eder. Kaldırma/indirme hızları (0.12/0.22 ve 0.12/0.11 m/s) hassas istifleme için yeterli kontrol sağlar.</p><p>Bu varyant; beyaz eşya, mobilya ve otomotiv yan sanayi gibi daha uzun tabanlı paletlerle çalışan işletmelerde kullanım kolaylığı sunar. Uygun yük koruma ızgarası ve bağlama ekipmanları ile güvenlik artırılabilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'car', 'text' => 'Otomotiv yan sanayide uzun palet transferleri'],
                    ['icon' => 'briefcase', 'text' => 'Beyaz eşya tedarik depolarında hat besleme'],
                    ['icon' => 'building', 'text' => 'Mobilya lojistiğinde geniş tabanlı ürün istifi'],
                    ['icon' => 'cart-shopping', 'text' => 'Hacimli perakende ürünlerin ara stoklaması'],
                    ['icon' => 'warehouse', 'text' => 'Konsolidasyon alanlarında düzenli yığma'],
                    ['icon' => 'industry', 'text' => 'Genel sanayi projelerinde uzun yük operasyonları']
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
        }
        $this->command->info('✅ Variants: ES12-12ES (1150 & 1220)');
    }
}
