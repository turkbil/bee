<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F2_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'F2')->first();
        if (!$m) { echo "❌ Master bulunamadı\n"; return; }

        $variants = [
            [
                'sku' => 'F2-1150-560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F2 - 1150×560 mm Çatal',
                'short' => 'EUR paletler için standart 1150×560 mm çatal; market, mağaza ve mikro depo akışlarında yüksek çeviklik ve düşük manevra alanı ihtiyacı.',
                'long' => '<section><h3>Standart 1150×560 mm için ideal denge</h3><p>İXTİF F2’nin 1150×560 mm çatal kombinasyonu, perakende ve e‑ticaret operasyonlarının çoğunda kullanılan standart palet ölçüleriyle tam uyum sağlar. 1360 mm dönüş yarıçapı dar koridorlarda hız kesmeden dönüşe izin verirken, 24V/20Ah Li‑Ion batarya fırsat şarj ile gün boyu erişilebilirlik sunar. 0.75 kW sürüş ve 0.5 kW kaldırma motoru, yüklü 4.0 km/s hızda dahi akıcı ilerleme sağlar.</p></section><section><h3>Teknik özet</h3><p>55/150/1150 mm çatal ölçüsü, 685/560 mm çatallar arası seçenekleri ve PU tekerlekler ile sessiz çalışma karakteri sunar. Elektromanyetik fren güvenliği artırır; kompakt 1550 mm toplam uzunluk alan kullanımını optimize eder.</p></section><section><h3>Kullanım</h3><p>Market koridorları, geri oda stok hareketleri ve mikro depolarda toplama & besleme görevleri için önerilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Market raf aralarında paletli replenishment'],
                    ['icon' => 'cart-shopping', 'text' => 'Perakende geri oda içi malzeme akışı'],
                    ['icon' => 'warehouse', 'text' => 'Mikro depo toplama istasyonları'],
                    ['icon' => 'box-open', 'text' => 'E‑ticaret paketleme ön beslemesi'],
                    ['icon' => 'industry', 'text' => 'Hafif üretim WIP taşıma'],
                    ['icon' => 'building', 'text' => 'Cross‑dock hatlarında yönlendirme']
                ]
            ],
            [
                'sku' => 'F2-1220-685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F2 - 1220×685 mm Çatal',
                'short' => '1220×685 mm çatal, geniş tabanlı paletlerde daha iyi stabilite ve yük dağılımı sunar; 3PL ve dağıtım merkezleri için uygundur.',
                'long' => '<section><h3>Geniş paletler için yüksek stabilite</h3><p>1220×685 mm kombinasyon, geniş tabanlı paletlerde denge ve yanal stabiliteyi artırır. F2’nin Platform F temelli mimarisi, bu ölçüde dahi 1360 mm dönüş yarıçapını koruyarak dar alan manevralarını mümkün kılar. Li‑Ion enerji sistemi fırsat şarj ile yoğun vardiyalarda kesintisiz kullanılabilir.</p></section><section><h3>Teknik özet</h3><p>PU tekerlekler sessiz çalışır, elektromanyetik fren güvenlik sağlar. 25 mm yerden yükseklik ve 1550 mm toplam uzunluk rampa ve kapı eşiklerinden geçişi kolaylaştırır.</p></section><section><h3>Kullanım</h3><p>3PL merkezleri, bölgesel dağıtım depoları ve FMCG back‑of‑house alanlarında tercih edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL toplama ve sevk hazırlığı'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtım back‑of‑house lojistik'],
                    ['icon' => 'box-open', 'text' => 'Koli konsolidasyon ve staging alanları'],
                    ['icon' => 'industry', 'text' => 'Hafif üretim hat besleme'],
                    ['icon' => 'briefcase', 'text' => 'Bölgesel hub operasyonları'],
                    ['icon' => 'arrows-alt', 'text' => 'Dar koridorlu yüksek yoğunluklu raflar']
                ]
            ],
            [
                'sku' => 'F2-1000-560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F2 - 1000×560 mm Çatal',
                'short' => '1000×560 mm kısa çatal, dar dönüşlü alanlarda daha hızlı manevra ve raf arası hızlanma sağlayan çevik çözüm sunar.',
                'long' => '<section><h3>Dar alanlarda hız ve çeviklik</h3><p>Kısa 1000×560 mm çatal, raf aralarının sıkışık olduğu şehir içi mikro merkezlerde hız kazandırır. 4.0/4.5 km/s hız, 1360 mm dönüş yarıçapı ve düşük kendi ağırlığı ile seri hareketler mümkün olur. Li‑Ion batarya, fırsat şarjla kesintisiz kullanılabilir.</p></section><section><h3>Teknik özet</h3><p>55 mm çatal kalınlığı ve PU tekerlek yapısı zemini korur. Elektromanyetik fren güvenliği artırır, düşük gürültü (<74 dB(A)) müşteri alanlarında konfor sağlar.</p></section><section><h3>Kullanım</h3><p>Dark store, micro‑fulfillment ve şehir içi dağıtım noktalarında önerilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Küçük mağaza arka alanlarında hızlı taşıma'],
                    ['icon' => 'warehouse', 'text' => 'Dark store toplama hatları'],
                    ['icon' => 'box-open', 'text' => 'Mikro‑fulfillment içi transfer'],
                    ['icon' => 'building', 'text' => 'Şehir içi hub geçişleri'],
                    ['icon' => 'industry', 'text' => 'Hafif montaj sahaları'],
                    ['icon' => 'cart-shopping', 'text' => 'Promosyon dönemi yük artışı yönetimi']
                ]
            ],
            [
                'sku' => 'F2-1350-685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F2 - 1350×685 mm Çatal',
                'short' => '1350×685 mm uzun çatal, hacimli kolilerde dengeyi artırır ve tek seferde daha uzun yükleri taşımak için uygundur.',
                'long' => '<section><h3>Uzun yükler için taşıma esnekliği</h3><p>1350×685 mm çatal varyantı, uzun ve hacimli yüklerde tek seferde taşıma verimliliğini artırır. Platform F tabanlı tasarım, geniş yüklerde dahi ekipmanı çevik ve ekonomik kılar. 24V/20Ah Li‑Ion enerji, vardiya içi molalarda takviye şarj ile erişilebilir kalır.</p></section><section><h3>Teknik özet</h3><p>PU tekerlekler sessizdir; elektromanyetik fren güvenlidir. 25 mm yerden yükseklik eşik geçişlerini kolaylaştırır. 1550 mm toplam uzunluk ve 1360 mm dönüş yarıçapı yön çevirmeyi destekler.</p></section><section><h3>Kullanım</h3><p>Mobilya kutuları, beyaz eşya paketleri ve hacimli e‑ticaret gönderilerinde tercih edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'couch', 'text' => 'Mobilya kutulu ürünlerin iç transferi'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya ve büyük paketli yükler'],
                    ['icon' => 'box-open', 'text' => 'Hacimli e‑ticaret konsolidasyonu'],
                    ['icon' => 'warehouse', 'text' => 'Dağıtım merkezi uzun yük hattı'],
                    ['icon' => 'industry', 'text' => 'Üretimde yarı mamul uzun parçalar'],
                    ['icon' => 'building', 'text' => 'Cross‑dock uzun yük geçişleri']
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
                'short_description' => json_encode(['tr' => $v['short']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long']], JSON_UNESCAPED_UNICODE),
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

        echo "✅ Varyantlar güncellendi: F2\n";
    }
}
