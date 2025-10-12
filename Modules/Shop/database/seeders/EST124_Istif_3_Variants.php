<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EST124_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EST124')->first();
        if (!$m) { echo "❌ Master bulunamadı: EST124\n"; return; }

        $variants = [
            [
                'sku' => 'EST124-2513',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EST124 - 2.513 mm Direk',
                'short_description' => '2.513 mm kaldırma yüksekliği, düşük kapı ve tavan açıklıklarına sahip alanlarda ekonomik istifleme sağlar; aynı sağlam şasi, 24V/80Ah AGM akü ve entegre şarj cihazı korunur.',
                'long_description' => '<section><h3>2.513 mm Direk: Düşük tavanlı alanlar için akıllı çözüm</h3><p>Depo girişleri, yükleme koridorları veya asma katlar gibi sınırlı tavan yüksekliklerinin bulunduğu yerlerde 2.513 mm direk seçeneği, makinenin toplam kapalı yüksekliğini düşürerek erişilebilirliği artırır. EST124 platformunun tüm avantajları—pazarca kanıtlanmış sürüş-hidrolik modüller, 24V/80Ah AGM enerji sistemi ve 24V/10A entegre şarj—bu varyantta aynen sunulur. 925 mm gövde genişliği ve 1415 mm dönüş yarıçapı, dar koridorlarda akıcı manevra sağlar.</p><p>0.75 kW sürüş motoru ve 2.2 kW kaldırma motoru, 0.10/0.15 m/s kaldırma hızlarını korur. Elektromanyetik frenleme, yük altında dengeli duruş ve güvenli park imkânı verir. PU tekerlek kombinasyonu farklı zeminlerde sessiz ve iz bırakmayan çalışma üretir.</p></section><section><h3>Operasyonel etkiler</h3><p>Bu konfigürasyon, hızlı sirkülasyonlu mal kabul alanlarında ve düşük raflı sahalarda palet hareketini hızlandırır. Mola aralarında yapılan kısa şarjlarla vardiya sürekliliği desteklenir. Enerji tüketimi sınıfında rekabetçidir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Düşük tavanlı mal kabul alanlarında istifleme'],
                    ['icon' => 'store', 'text' => 'Perakende arka oda raflarına hızlı yerleştirme'],
                    ['icon' => 'box-open', 'text' => 'Evrak ve arşiv depolarında kompakt stok yönetimi'],
                    ['icon' => 'car', 'text' => 'Servis parça depolarında alçak raflı istifleme'],
                    ['icon' => 'industry', 'text' => 'Hafif üretim sahalarında WIP ara stok'],
                    ['icon' => 'print', 'text' => 'Ambalaj/Matbaa alanlarında düşük yükseklikli raflar']
                ]
            ],
            [
                'sku' => 'EST124-2713',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EST124 - 2.713 mm Direk',
                'short_description' => '2.713 mm direk, kompakt tesislerde daha yüksek stoklama imkânı sunar; 4.5 km/s boş hız, 925 mm genişlik ve 1415 mm dönüş yarıçapı dar koridor ergonomisini korur.',
                'long_description' => '<section><h3>2.713 mm Direk: Esnek raf düzenine uygun istif</h3><p>Bu konfigürasyon, koridor optimizasyonu yapılmış tesislerde maksimum hacim verimliliği hedefleyen ekipler için geliştirilmiştir. Kaldırma yüksekliği 2.713 mm’ye uzanırken, kapalı direk ölçüleri kapı ve rampa geçişlerinde sorun çıkarmaz. EN 16796’ya göre 0.57 kWh/h tüketim, işletme maliyetlerini kontrol altında tutar.</p><p>AGM akü kimyası günlük depo operasyonlarına iyi uyum sağlar; entegre şarj cihazı ile altyapı gereksinimi düşüktür. Mekanik direksiyon ve elektromanyetik fren, sezgisel kullanım ve güvenli duruşla operatör yorgunluğunu azaltır.</p></section><section><h3>Uygulama alanları</h3><p>Hızlı ürün devirli raflar, e-ticaret backroom ve 3PL bölümlerinde etkin çözüm üretir; yoğun vardiyalarda mola şarjlarıyla süreklilik korunur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL raf içi yerleştirme ve toplama'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret iade ve sıralama alanları'],
                    ['icon' => 'store', 'text' => 'Market dağıtım merkezlerinde bölgesel stoklama'],
                    ['icon' => 'snowflake', 'text' => 'Soğutmalı depolarda orta yükseklikli raflar'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında lot bazlı istif'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil koli transferi ve tampon stok']
                ]
            ],
            [
                'sku' => 'EST124-3013',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EST124 - 3.013 mm Direk',
                'short_description' => '3.013 mm kaldırma yüksekliği, çoğu orta yükseklikli raf sistemini kapsar; sessiz PU tekerlekler ve kanıtlı hidroliklerle titreşimi düşük, akışkan istifleme.',
                'long_description' => '<section><h3>3.013 mm Direk: Çok yönlü standart</h3><p>Burada tanımlanan yükseklik, pek çok depo tipinde “tatlı nokta” olarak kabul edilir. Yüksekliği ve kapalı direk boyu dengeli olup, tesis içi alanların çoğuna uyum sağlar. 60/170/1150 mm çatal seti EUR paletlerle uyumludur; 680 mm taşıyıcı genişliği stabil hareket sağlar.</p><p>Performans tarafında 4.0/4.5 km/s sürüş hızları hat besleme ve replenishment süreçlerine ritim kazandırır. Kaldırma/indirme hızları ile palet devir süresi kısalır. DC sürüş kontrolü, yumuşak ivmelenme/geri besleme karakteriyle operatör güvenini artırır.</p></section><section><h3>Operasyonel çıktılar</h3><p>Daha hızlı raf dönüşü, normalleştirilmiş enerji maliyeti ve düşük gürültü seviyesi ile vardiya içi ergonomi gelişir.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Hafif imalat besleme hatları'],
                    ['icon' => 'warehouse', 'text' => 'Depo içi transfer ve raf istifi'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim malları bölümü'],
                    ['icon' => 'book', 'text' => 'Kırtasiye ve yayın stok sahaları'],
                    ['icon' => 'car', 'text' => 'Oto aksesuar arka depo istifleme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalaj palet yönetimi']
                ]
            ],
            [
                'sku' => 'EST124-3313',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EST124 - 3.313 mm Direk',
                'short_description' => '3.313 mm direk, hacmi artırırken manevra kabiliyetini korur; elektromanyetik fren ve mekanik direksiyon güvenli ve öngörülebilir kullanım sunar.',
                'long_description' => '<section><h3>3.313 mm Direk: Hacmi yukarı taşı</h3><p>Raf yüksekliğini artırarak aynı alan içinde daha fazla SKU stoklamak isteyen işletmeler için geliştirilmiştir. Yüksekliğin artması, operatör eğitimi ve palet merkez sağlığı ile desteklenmelidir. EST124’ün şasi dengesi, iz genişlikleri ve tekerlek konfigürasyonu yükseklerde de stabildir.</p><p>Enerji tarafında 24V/80Ah akü ve entegre şarj cihazı günlük çevrimlere uygundur. EN 16796’ya göre ölçülen tüketim değeri maliyetleri öngörülebilir kılar.</p></section><section><h3>İş sonuçları</h3><p>Depo yoğunluğu artar, raf erişimi hızlanır ve hat besleme beklemeleri azalır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek stok devirli raflarda replenishment'],
                    ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde katlar arası istif'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret kampanya dönemleri stok yönetimi'],
                    ['icon' => 'tshirt', 'text' => 'Hazır giyim ayrıştırma alanları'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda kapı çevresi istif'],
                    ['icon' => 'pills', 'text' => 'Medikal sarf malzeme rafları']
                ]
            ],
            [
                'sku' => 'EST124-3613',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EST124 - 3.613 mm Direk',
                'short_description' => '3.613 mm’ye kadar kaldırma, orta yükseklikte raf sistemlerinin tamamına yakınını kapsar; 0.10/0.15 m/s kaldırma hızlarıyla seri yerleştirme yapılır.',
                'long_description' => '<section><h3>3.613 mm Direk: Maksimum erişim, kontrollü denge</h3><p>Bu en yüksek direk seçeneği, alan kullanımını en üst düzeye taşımak isteyen depolara yöneliktir. Yükün ağırlık merkezi ve palet yapısı göz önüne alınarak güvenli çalışma rutinleri oluşturulmalıdır. EST124’ün güçlü şasisi ve elektromanyetik fren sistemi yüksek irtifada dahi güven verir.</p><p>AGM akü kimyası, planlı mola şarjları ile vardiya içi süreklilik sağlar. DC sürüş kontrolü, yüksekte hassas hız yönetimi ile operatörün hata payını düşürür.</p></section><section><h3>Uygulama ve değer</h3><p>Mevcut raf yatırımlarından maksimum geri dönüş, daha düzenli lokasyon yönetimi ve daha hızlı sipariş hazırlığı elde edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Orta-yüksek raflı merkezi depolar'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG ana depo bölümleri'],
                    ['icon' => 'couch', 'text' => 'Mobilya yarı mamul stok alanları'],
                    ['icon' => 'print', 'text' => 'Ambalaj bobin palet istifi'],
                    ['icon' => 'industry', 'text' => 'Montaj öncesi hazır palet parkları'],
                    ['icon' => 'book', 'text' => 'Arşiv ve dokümantasyon rafları']
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
                'product_type' => 'physical',
                'condition' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }
        echo "✅ Variants eklendi: EST124 (5 varyant)\n";
    }
}
