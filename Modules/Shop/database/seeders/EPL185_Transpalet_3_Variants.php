<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL185_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPL185')->first();
        $variants = [
            [
                'sku' => 'EPL185-1150x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL185 - 1150×540 mm Çatal',
                'short_description' => 'EUR palete tam uyumlu 1150×540 mm çatal; 48V Li-Ion enerji ve l2=400 mm gövdeyle dar koridorlarda çevik kullanım.',
                'long_description' => '<section><h3>Standartın gücü: 1150×540 mm</h3><p>1150×540 mm konfigürasyonu, Avrupa standardı EUR paletlerle mükemmel uyum sunar. İXTİF EPL185’in 48V Li‑Ion enerji sistemi (20/30Ah) ve entegre 48V‑10A şarj cihazı, vardiyalarda kesintisiz akış sağlar. Yüzer denge tekerleri ve PU teker kombinasyonu; eşik, rampa ve genleşme derzlerinde stabil tutuş üreterek ürün güvenliğini destekler. <strong>l2=400 mm</strong> kompakt gövde ve <strong>1330 mm</strong> dönüş yarıçapı dar alanlarda ikinci manevra ihtiyacını azaltır. <em>Creep</em> modu, kapı girişlerinde milimetrik hizalama sağlar.</p><p>50/150/1150 mm çatal geometrisi ve 80 mm alçaltılmış çatal yüksekliği, palet kanalına pürüzsüz giriş çıkış verir. 0.020/0.025 m/s kaldırma hızı ve düşük 74 dB(A) gürültü seviyesi, gece vardiyalarında dahi konforlu çalışma oluşturur. Fırçasız DC tahrik mimarisi bakım gereksinimlerini düşürerek toplam sahip olma maliyetini iyileştirir.</p><h3>Sonuç</h3><p>Standart EUR palet akışının yoğun olduğu e‑ticaret, 3PL ve perakende depolarda, bu varyant ergonomi ve verimliliği bir araya getirir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticarette sipariş toplama ve konsolidasyon'],
                    ['icon' => 'warehouse', 'text' => '3PL raf arası besleme ve sevkiyat önü düzenleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de mağaza bazlı ayrıştırma'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG koli paletlerinin hat başına akışı'],
                    ['icon' => 'industry', 'text' => 'WIP taşımaları ve hat besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça iç lojistiği']
                ]
            ],
            [
                'sku' => 'EPL185-1220x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL185 - 1220×540 mm Çatal',
                'short_description' => '1220×540 mm uzun çatal; hacimli paletlerde daha geniş temas ve güvenli denge, dar alanda hassas manevra.',
                'long_description' => '<section><h3>Uzun paletlerde ekstra temas</h3><p>1220×540 mm çatal, standart EUR’a ek olarak uzun ve hacimli paletlerde daha geniş destek yüzeyi sunar. Yüzer denge tekerleri, geniş temas alanıyla birlikte rampalarda tutarlı çekiş üretir. 48V Li‑Ion batarya (20/30Ah) çıkarılabilir kartuş yapısındadır; entegre 48V‑10A şarj cihazı fırsat şarjını kolaylaştırır. Metal korumalı kapak, güç konnektörünün kazara çıkmasını önlemeye yardımcı olur ve servis erişimini hızlandırır.</p><p>Kompakt <strong>l2=400 mm</strong> ölçüsü korunurken, daha uzun çatal boyu palet kanallarında tam destek sağlar. <strong>1330 mm</strong> dönüş yarıçapı ve <em>creep</em> modu ile raf ağzında palet hizalama risksiz ve düzenli yapılır. Düşük gürültü profili (<strong>74 dB(A)</strong>) kapalı alan konforuna katkı sunar.</p><h3>Sonuç</h3><p>Beyaz eşya kutuları, matbaa rulosu veya mobilya parçaları gibi uzun yüklerde bu varyant, denge ve ürün emniyetini artırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Üretimde uzun paletlerin ara taşıması'],
                    ['icon' => 'cart-shopping', 'text' => 'Hacimli kampanya paletleri sevkiyat hazırlığı'],
                    ['icon' => 'warehouse', 'text' => 'Uzun paletli 3PL müşteri operasyonları'],
                    ['icon' => 'box-open', 'text' => 'Depo içi cross-dock akışı'],
                    ['icon' => 'building', 'text' => 'Kampüs içi bakım depoları lojistiği'],
                    ['icon' => 'briefcase', 'text' => 'Kurumsal depo içi proje lojistiği']
                ]
            ],
            [
                'sku' => 'EPL185-1150x685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPL185 - 1150×685 mm Geniş Çatal',
                'short_description' => '1150×685 mm geniş aralık; geniş tabanlı paletlerde yalpalamayı azaltan denge ve sessiz PU teker akışı.',
                'long_description' => '<section><h3>Geniş tabanda güven ve kontrol</h3><p>1150×685 mm çatal aralığı, geniş tabanlı kasalar ve düzensiz yüklerde merkez dağılımını iyileştirerek taşıma sırasında yalpalamayı azaltır. İXTİF EPL185’in yüzer stabilite tekerleri ve PU teker yapısı zemine nazik, sessiz ve öngörülebilir hareket üretir. 48V Li‑Ion enerji (20/30Ah) ve entegre 48V‑10A şarj cihazı, vardiya arasında kısa mola şarjlarına olanak tanır. Metal batarya kapağı, bağlantı güvenliğini artırır ve bakım erişimini hızlandırır.</p><p>Kompakt <strong>l2=400 mm</strong> gövde, geniş çatal olmasına rağmen raf arası yaklaşımı korur; <strong>1330 mm</strong> dönüş yarıçapı ve mekanik direksiyonun doğal geri bildirimi dar dönüşlerde palet köşe riskini düşürür. <em>Creep</em> modu, rampabaşı ve kapı eşiklerinde milimetrik konumlandırma sunar.</p><h3>Sonuç</h3><p>İçecek, evcil hayvan maması ve mobilya gibi geniş tabanlı taşımalarda, bu varyant daha az hasar ve daha yüksek akış sürekliliği sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'cart-shopping', 'text' => 'Kasalı içecek ve paketli gıda paletleri'],
                    ['icon' => 'box-open', 'text' => 'Karma paletli e-ticaret sevkiyat hazırlığı'],
                    ['icon' => 'warehouse', 'text' => 'Geniş tabanlı paletlerin raf içi akışı'],
                    ['icon' => 'industry', 'text' => 'Üretimden ambara geniş yük transferi'],
                    ['icon' => 'building', 'text' => 'Tesis içi bakım ve yedek parça hareketi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış yaklaşımı']
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
    }
}
