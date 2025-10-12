<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET2H_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT20-15ET2H')->first();
        if (!$m) { echo "❌ Master bulunamadı"; return; }

        $variants = [
            [
                'sku' => 'EPT20-15ET2H-560x1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-15ET2H - 560×1220 mm Çatal',
                'short_description' => '560 mm genişlikte 1220 mm çatal uzunluğu, uzun EUR/US paletlerinde burun desteğini artırır. Dar koridor çevikliğini koruyarak açık alanda stabil besleme sağlar.',
                'long_description' => '<section><h4>Uzatılmış Çatal, Dengeli Manevra</h4><p>1220 mm çatal, uzun paletlerin dengeli taşınmasını kolaylaştırır. 1704 mm toplam uzunluk ve 1505 mm dönüş yarıçapı sayesinde dar raf aralarında kontrol korunurken, 30 mm şasi altı boşluk bozuk zemin geçişlerinde avantaj sağlar. Kauçuk tahrik tekeri kaymayı azaltır; PU yük tekerleri yüzey dostudur.</p></section><section><h4>Teknik Uyum</h4><p>1.500 kg kapasite (c=600 mm), 24V/65Ah AGM enerji ve 4.0/4.5 km/s seyir hızları standarttır. Entegre 24V-10A şarj cihazı vardiya arasında pratik ara şarja olanak verir. 115 mm kaldırma ve 80 mm minimum çatal yüksekliği, rampalarda palet alçaltma/yerleştirmeyi hızlandırır.</p></section><section><h4>Kullanım Önerileri</h4><p>Uzun koli ve içecek kasası paletleri, beyaz eşya ve elektronik lojistiğinde koruyucu ambalajlı ürünlerin raf arası transferleri için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret outbound’da uzun paletlerin hat beslemesi'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasalarında koridor içi transfer'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya ve elektronik paketli ürün akışı'],
                    ['icon' => 'warehouse', 'text' => '3PL depolarda çapraz sevkiyat ve staging'],
                    ['icon' => 'store', 'text' => 'Perakende DC’lerde toplama sonrası yükleme'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde yarı mamul taşıma']
                ]
            ],
            [
                'sku' => 'EPT20-15ET2H-685x1150',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20-15ET2H - 685 mm Geniş Palet Çatalı (1150 mm)',
                'short_description' => '685 mm çatallar, 1000×1200 paletlerde yanal dengeyi artırır. 1150 mm uzunlukla standart koridor ölçülerini koruyarak ağır yüklerde stabilite sağlar.',
                'long_description' => '<section><h4>Geniş Palette Stabilite</h4><p>685 mm çatal aralığı, geniş tabanlı paletlerde yana devrilme riskini azaltır. 1505 mm dönüş yarıçapı ve 2179/2307 mm koridor gereksinimleri ile raf akışında verimlidir.</p></section><section><h4>Zemin ve Tutuş</h4><p>Kauçuk tahrik tekeri ıslak/ tozlu zeminlerde tutuş sağlar. Sızdırmaz şanzıman ve yüksek şasi boşluğu, dış saha operasyonlarını destekler.</p></section><section><h4>Operasyonel Kazanımlar</h4><p>İstif öncesi hat besleme, inbound ayrıştırma ve yükleme rampası yaklaşımında palet merkezlemesini kolaylaştırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parçalarında geniş palet sevkiyatı'],
                    ['icon' => 'couch', 'text' => 'Mobilya ve ev dekor paletleri'],
                    ['icon' => 'warehouse', 'text' => 'Geniş tabanlı paletlerin staging alanı hareketi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depo içi replenishment'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve baskı malzemeleri taşıma'],
                    ['icon' => 'tshirt', 'text' => 'Tekstilde koli paletleme ve hat besleme']
                ]
            ],
            [
                'sku' => 'EPT20-15ET2H-85AhAGM',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EPT20-15ET2H - 85Ah AGM Batarya',
                'short_description' => 'Standart 65Ah yerine 85Ah AGM batarya ile daha uzun vardiya döngüsü. Basit DC kontrol mimarisiyle enerji verimliliği korunur.',
                'long_description' => '<section><h4>Daha Uzun Çevrim</h4><p>85Ah AGM, daha uzun çalışma aralığı gerektiren yoğun toplama ve besleme hatları için tasarlandı. Entegre 24V-10A şarj ile altyapı değişikliği gerekmez.</p></section><section><h4>Koruma ve Dayanıklılık</h4><p>Yüksek şasi boşluğu ve yatay motor/fren düzeni, enerji sisteminin darbe ve kirden etkilenmesini azaltır. Sızdırmaz şanzıman, dış etkenlere karşı ilave koruma sunar.</p></section><section><h4>Uygulama Senaryoları</h4><p>Çok vardiyalı operasyonlarda ara şarj sayısını azaltır, atıl zamanı düşürür. Yedek batarya ihtiyacını minimize ederek toplam sahip olma maliyetine katkı sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yoğun toplama alanlarında uzun vardiya'],
                    ['icon' => 'bolt', 'text' => 'Hızlı dönüşlü staging hatları'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret sipariş tamamlama'],
                    ['icon' => 'industry', 'text' => 'Üretim besleme ve WIP akışı'],
                    ['icon' => 'store', 'text' => 'Perakende DC dalgalı talep dönemleri'],
                    ['icon' => 'briefcase', 'text' => 'B2B sevkiyat konsolidasyonu']
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
        echo "✅ Variants: ".count($variants)." kayıt güncellendi/eklendi";
    }
}
