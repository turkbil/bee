<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CQE15S_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'CQE15S')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: CQE15S'); return; }

        $variants = [
            [
                'sku' => 'CQE15S-126',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF CQE15S - 126" Direk (Standart Mast)',
                'short_description' => '126 inç maksimum kaldırma, 6 inç serbest kaldırma ve 162.8 inç direk yükselmiş yükseklikle düşük tavanlı alanlarda ideal, 3000 lb kapasite ve 23 inç erişim.',
                'long_description' => '<section><h3>126" Direk: Alçak Tavanlarda Yüksek Verim</h3><p>126 inç maksimum kaldırma yüksekliği ve 6 inç serbest kaldırma ile alçak tavanlı depolarda rahat hareket sağlayan bu varyant, pantograf erişim sayesinde çift derin yerleşimlerde palet alımını kolaylaştırır. 3.1/3.4 mph seyir hızı, 20/26 fpm kaldırma ve 52/33 fpm indirme hızlarıyla gün boyu akıcı bir ritim sunar.</p></section><section><h3>Teknik Çerçeve</h3><p>Direk yükseldiğinde 162.8 inç yüksekliğe ulaşır; 62.6 inç dönüş yarıçapı ve 23 inç erişim mesafesi dar koridorlarda çeviklik sağlar. PU tekerler ve elektromanyetik frenler güvenli çalışma hissini destekler. 24V Li-ion/AGM/kurşun-asit akülerle enerji esnekliği sunar.</p></section><section><h3>Kapanış</h3><p>Alçak tavanlı depolar, çekme katlı ara depolar ve rampa girişlerinde güvenli kullanım için optimize edilmiştir. Günlük operasyon verimini artırmak için bu kısa direk paketini tercih edin.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Alçak tavanlı koridorlarda istif ve yer değiştirme'],
                    ['icon' => 'box-open', 'text' => 'Çift derin raflarda alt seviye palet hareketleri'],
                    ['icon' => 'store', 'text' => 'Mağaza arka depo alanlarında sipariş toplama'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG hızlı dönüşlü ürün alanlarında kısa mesafe besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında sık konum değişimi'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı yakınında WIP tampon alan yönetimi']
                ]
            ],
            [
                'sku' => 'CQE15S-157',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF CQE15S - 157" Direk (Free Mast)',
                'short_description' => '157 inç maksimum kaldırma, 39 inç serbest kaldırma ve 195.25 inç direk yükselmiş yükseklik; kapı ve tünel geçişlerinde esnek çalışma, 3000 lb kapasite.',
                'long_description' => '<section><h3>157" Direk: Serbest Kaldırmanın Konforu</h3><p>39 inç serbest kaldırma ile yükü erken aşamada yükselterek kapı, tünel veya sprinkler bölgelerinde çarpışma riskini azaltır. Pantograf erişim çift derin slotlarda ikinci palete uzanmayı mümkün kılar.</p></section><section><h3>Teknik Çerçeve</h3><p>Direk yükseldiğinde yükseklik 195.25 inçe ulaşır. 23 inç erişim, 62.6 inç dönüş yarıçapı ve dengeli hız değerleri günlük iş akışına uyum sağlar. Enerji tarafında Li-ion dahil geniş akü seçenekleri, hızlı vardiya dönüşleri için uygundur.</p></section><section><h3>Kapanış</h3><p>Kapı geçişlerinin yoğun olduğu cross-dock ve karma depolarda, serbest kaldırma kabiliyeti ile raf ve kapı eşiği arasında güvenli geçiş sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Cross-dock alanlarında kapı önü yükleme/boşaltma'],
                    ['icon' => 'box-open', 'text' => 'Çift derin rafların orta seviyelerinde yük elleçleme'],
                    ['icon' => 'store', 'text' => 'Perakende sevkiyat konsolidasyonu'],
                    ['icon' => 'cart-shopping', 'text' => 'Yüksek devirli ürünlerde tazeleme ve besleme'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda kapı giriş-çıkış bölgelerinde güvenli geçiş'],
                    ['icon' => 'industry', 'text' => 'Üretim besleme koridorlarında serbest kaldırma ile hareket']
                ]
            ],
            [
                'sku' => 'CQE15S-189',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF CQE15S - 189" Direk (Yüksek Kaldırma)',
                'short_description' => '189 inç maksimum kaldırma, 41 inç serbest kaldırma ve 227 inç direk yükselmiş yükseklik; yüksek raf erişimi ve çift derin palet alımında maksimum kapsama.',
                'long_description' => '<section><h3>189" Direk: En Üst Seviyeye Kadar Erişim</h3><p>41 inç serbest kaldırma ve 189 inç tepe yüksekliği, yoğun raflı tesislerde maksimum dikey alan kullanımını sağlar. Çift derin istifleme ile koridor sayısını azaltıp kapasiteyi artırabilirsiniz.</p></section><section><h3>Teknik Çerçeve</h3><p>227 inç direk yükselmiş yüksekliğe rağmen 62.6 inç dönüş yarıçapı ve 23 inç erişim mesafesi ile manevra kabiliyeti korunur. Elektromanyetik frenler ve ofset tiller görüşü güveni destekler.</p></section><section><h3>Kapanış</h3><p>Yüksek raflı e-ticaret merkezleri ve bölgesel dağıtım hub’ları için en kapsamlı erişim çözümüdür; daha az taşıma turunda daha fazla palet hareketi sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı koridorlarda üst seviye palet yerleştirme'],
                    ['icon' => 'box-open', 'text' => 'Çift derin konumların ikinci paletine erişim'],
                    ['icon' => 'cart-shopping', 'text' => 'Bölgesel merkezlerde yoğun besleme ve çekme'],
                    ['icon' => 'building', 'text' => 'Yüksek tavanlı depo ve antrepolarda dikey alan kullanımı'],
                    ['icon' => 'pills', 'text' => 'Hassas stokların üst raflara güvenli transferi'],
                    ['icon' => 'industry', 'text' => 'Yarı mamul tampon alanlarının üst seviyeden beslenmesi']
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
            $this->command->info("🔀 Varyant eklendi: {$v['sku']}");
        }
    }
}
