<?php
    namespace Modules\Shop\Database\Seeders;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    class EFL352_Forklift_3_Variants extends Seeder {
        public function run(): void {
            $m = DB::table('shop_products')->where('sku', 'EFL352')->first();
            if (!$m) {$this->command->error('❌ Master bulunamadı: EFL352'); return; }

            $variants = [
            [
                'sku' => 'EFL352-FL1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL352 - 1070 mm Çatal',
                'short_description' => '1070 mm çatal ile dar koridor manevrası ve palet içi denge; kısa paletlerde hızlı giriş-çıkış.',
                'long_description' => '<section><h2>1070 mm Çatal: Kompakt Paletlerde Hız</h2>
<p>1070 mm çatal uzunluğu, EUR ve kısa palet tiplerinde hızlı giriş-çıkış sağlayarak zaman kazandırır. Dar raf aralıklarında 2645 mm dönüş yarıçapı ile birleşince operatör, minimum manevrayla hedef konuma ulaşır. Kısa çatal boyu özellikle ağırlık merkezini şasiye yaklaştırdığı için stabilite hissini artırır, forkliftin titreşim ve salınım davranışı daha öngörülebilir hale gelir.</p></section>
<section><h3>Teknik Etki</h3>
<p>Kısa çatal boyu; yük taşıma döngülerinde çatal dalma mesafesini kısaltır, istif/boşaltma sürelerini azaltır. Dar koridor ve rampalı alanlarda kuyruk savrulmasının azalması, palet ve raf hasarlarını düşürür. Yan kaydırma ataşmanı ile kullanıldığında hassas hizalama daha da kolaylaşır; ancak tipik ~200 kg kapasite azaltımı hesaplanmalıdır.</p></section>
<section><h3>Operasyonel Sonuç</h3>
<p>Yoğun sipariş hazırlama ve kısa mesafe transferlerinde 1070 mm çatal; çeviklik ve çeviklikten doğan verimlilik sunar. Gün sonunda daha az manevra, daha çok tamamlanmış döngü demektir.</p></section>',
                'use_cases' => [['icon' => 'box-open', 'text' => 'E-ticaret raf aralarında yoğun toplama turu'], ['icon' => 'warehouse', 'text' => 'Cross-dock alanında sık yön değişimi'], ['icon' => 'store', 'text' => 'Perakende mağaza içi dar koridor transferleri'], ['icon' => 'industry', 'text' => 'Hücresel üretimde WIP besleme'], ['icon' => 'car', 'text' => 'Otomotiv komponent kutularında hızlı yerleştirme'], ['icon' => 'flask', 'text' => 'Kimyasal küçük paletlerde hassas istif']]
            ],
            [
                'sku' => 'EFL352-FL1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL352 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal ile daha uzun paletlerde tam destek; karma ambalajlarda güvenli kaldırma ve yerleştirme.',
                'long_description' => '<section><h2>1220 mm Çatal: Esnek Palet Uyum Yeteneği</h2>
<p>1220 mm çatal, 1070 mm’ye kıyasla daha uzun paletlerde tam destek sunar ve karma ambalajlı yüklerde palet dışına taşmanın önüne geçer. Kaldırma sırasında yükün daha geniş tabanda desteklenmesi, operatörün hızlanma ve yavaşlamalarda daha az düzeltme yapmasını sağlar. Bu sayede istif kuleleri daha düzgün oluşur.</p></section>
<section><h3>Teknik Etki</h3>
<p>Daha uzun çatal; giriş-çıkışta birkaç santimetre fazladan mesafe gerektirse de döngü kararlılığını artırır. Rampa üstü yüklemelerde palet esnemesi azalır, palet altı destek noktaları daha iyi karşılanır. Alan planlamasında koridor açıklıkları ve dönüş cepleri doğru belirlendiğinde, verimlilik kazancı sabittir.</p></section>
<section><h3>Operasyonel Sonuç</h3>
<p>Karma SKU paletleme, içecek kasaları ve beyaz eşya gibi büyük hacimli paketlerde 1220 mm çatal, hasar riskini düşürüp daha güvenli taşıma sağlar; operatör stresi azalır.</p></section>',
                'use_cases' => [['icon' => 'box-open', 'text' => 'Fulfillment merkezinde karma SKU paletleme'], ['icon' => 'wine-bottle', 'text' => 'İçecek kasalarında tam taban desteği'], ['icon' => 'tv', 'text' => 'Beyaz eşya paletlerinde esneme azaltma'], ['icon' => 'couch', 'text' => 'Mobilya koli paketlerinde geniş destek'], ['icon' => 'hammer', 'text' => 'DIY ürünlerinde uzun palet taşımaları'], ['icon' => 'print', 'text' => 'Ambalaj ve matbaa rulolarında denge']]
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
            $this->command->info("✅ Variants oluşturuldu: EFL352");
        }
    }
