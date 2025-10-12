<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESi161_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ESi161')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ESi161'); return; }

        $variants = [
            [
                'sku' => 'ESi161-1000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ESi161 - 1000 mm Çatal',
                'short_description' => '1000 mm çatal ile kısa paletli yüklerde daha sıkı dönüş ve dar alan hakimiyeti; iki palet taşıma ve hızlı fırsat şarjı ile yoğun hat beslemeleri için optimize edildi.',
                'long_description' => '<section><h3>1000 mm Çatal: Sıkı Manevra</h3><p>ESi161-1000, dar koridorlar ve lift içi operasyonlar için 1000 mm çatal uzunluğu ile çeviklik sunar. İkili kaldırma mimarisi iki paleti aynı anda hareket ettirmeyi mümkün kılar; kısa çatal, paletin dışarı taşmasını azaltarak kapı eşiklerinde ve rampalarda daha güvenli yaklaşım sağlar. Mono direk ve şeffaf panel, çatal uçlarının anlık takibini mümkün kılar; elektromanyetik fren ise kontrollü yavaşlama sağlar.</p></section><section><h3>Teknik Uyum</h3><p>Temel platform; 24V/100Ah Li-Ion batarya, 0.75 kW tahrik ve 2.2 kW kaldırma motoruyla standart ESi161 performansını korur. 4/4.5 km/sa hız ve 1473 mm dönüş yarıçapı, kısa çatalın getirdiği hareket kabiliyetiyle birleşerek araç içi yükleme/boşaltma sürelerini kısaltır.</p></section><section><h3>Kullanım Senaryosu</h3><p>Dağıtım merkezleri, mağaza arkaları ve mikro-fulfillment alanlarında küçük hacimli yüklerle çalışırken çeviklik ön plandadır.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Mağaza arkası dar alanlarda kısa dönüş'],
                    ['icon' => 'warehouse', 'text' => 'Mikro-fulfillment istasyon besleme'],
                    ['icon' => 'box-open', 'text' => 'Küçük paletlerde hızlı çapraz sevkiyat'],
                    ['icon' => 'car', 'text' => 'Araç içine yakın yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Hücresel üretimde WIP akışı'],
                    ['icon' => 'shield-alt', 'text' => 'Eşik ve rampalarda güvenli yaklaşım']
                ]
            ],
            [
                'sku' => 'ESi161-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ESi161 - 1150 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal, EUR palet uyumu ve iki paletli taşıma ile çok amaçlı kullanım; hızlı şarj ve merkez tahrikli denge tekerleri ile kesintisiz akış.',
                'long_description' => '<section><h3>1150 mm Çatal: Evrensel Standart</h3><p>ESi161-1150, 55×190×1150 mm çatal ölçüsüyle EUR paletin doğal eşleşmesidir. İkili kaldırma sayesinde rampada veya düz zeminde aynı anda iki palet transferi yapılabilir. Şeffaf görüş paneli ve mono direk yapı, palet yuvalarına ilk seferde doğru giriş sağlar; elektromanyetik fren ise güvenli duruş sağlar.</p></section><section><h3>Performans Dengesi</h3><p>24V/100Ah Li-Ion, 0.75 kW sürüş ve 2.2 kW kaldırma kombinasyonu, 4/4.5 km/sa seyir ve 0.1/0.12 m/sn kaldırma hızlarıyla günlük vardiya ritmini yakalar. 800/1190 mm tiller yüksekliği ve 800 mm gövde genişliği operatör konforu ve dar alan performansını bir arada sunar.</p></section><section><h3>Kullanım Senaryosu</h3><p>Genel depo içi transfer, raf besleme ve araç yükleme/boşaltmada en dengeli seçenektir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo içi transfer'],
                    ['icon' => 'box-open', 'text' => 'EUR palet ile çapraz sevkiyat'],
                    ['icon' => 'store', 'text' => 'DC → mağaza sevkiyat süreçleri'],
                    ['icon' => 'industry', 'text' => 'Hat besleme ve ara stok yönetimi'],
                    ['icon' => 'arrows-alt', 'text' => 'Dar koridorda hassas manevra'],
                    ['icon' => 'shield-alt', 'text' => 'Güvenli frenleme ve yavaş mod']
                ]
            ],
            [
                'sku' => 'ESi161-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ESi161 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yükler ve farklı palet tipleriyle daha geniş temas alanı; ikili kaldırma ile tur sayısını azaltıp hat verimliliğini artırır.',
                'long_description' => '<section><h3>1220 mm Çatal: Geniş Temas, Dengeli Taşıma</h3><p>ESi161-1220, daha uzun yüklerin dengeli kavranması gereken uygulamalar için tasarlanır. Uzamış çatal, palet üzerinde taşıma esnasında daha geniş bir temas yüzeyi sağlayarak stabiliteyi artırır. İkili kaldırma mimarisi iki paleti tek turda taşımayı mümkün kılar ve toplam çevrim süresini kısaltır.</p></section><section><h3>Operasyonel Esneklik</h3><p>Standart platform performansı korunurken, 1473 mm dönüş yarıçapı ve 2306 mm koridor ihtiyacı ile dar alan verimliliği yüksek kalır. 24V 100Ah Li-Ion fırsat şarjı sayesinde uzun vardiyalarda bile yüksek erişilebilirlik elde edilir.</p></section><section><h3>Kullanım Senaryosu</h3><p>İçecek, ambalaj ve elektronik gibi yüklerde uzun paletlerle stabil taşıma gerekir; bu varyant bu ihtiyaçları hedefler.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Uzun paletli yüklerin dengeli taşınması'],
                    ['icon' => 'warehouse', 'text' => 'Yoğun hatlarda tur sayısının azaltılması'],
                    ['icon' => 'car', 'text' => 'Araç rampasında iki paletli transfer'],
                    ['icon' => 'industry', 'text' => 'Üretim çıkışında güvenli taşıma'],
                    ['icon' => 'flask', 'text' => 'Ambalaj/kimya sektöründe farklı palet tipleri'],
                    ['icon' => 'pills', 'text' => 'Hassas ürünlerde geniş temas alanı']
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
            $this->command->info("✅ Varyant eklendi: {$v['sku']}");
        }
    }
}
