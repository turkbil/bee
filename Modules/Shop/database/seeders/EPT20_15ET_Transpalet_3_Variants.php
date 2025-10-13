<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EPT20-15ET')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        $variants = [
            [
                'sku' => 'EPT20-15ET-1000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-15ET - 1000 mm Çatal',
                'short_description' => 'Kısa çatal (1000 mm) seçeneği; dar koridor ve hafif paletlerde çevik manevra, düşük kuyruk salınımı ve hızlı ürün alma-bırakma döngüleri için optimize edilmiştir.',
                'body' => '<section><h2>1000 mm: Dar Alanların Çevik Oyuncusu</h2><p>1000 mm çatal uzunluğu, raf arası yaklaşım ve kısa mesafe manevralarında kuyruk salınımını azaltır. Bu sayede operatör, paleti hizalayıp kaldırma/indirme döngülerini daha az düzeltme ile tamamlar. Özellikle paketleme istasyonları, cross-dock hatları ve sık dönüş gerektiren mikro dağıtım alanlarında zaman kazancı sağlar. 1.5 ton kapasite, DC sürüş kontrolü ve 1485 mm dönüş yarıçapı kombinasyonu, kompakt alanlarda güvenli hız yönetimi ile verimi artırır.</p></section><section><h3>Teknik Avantajlar</h3><p>Standart gövde ölçüleri (l1=1632 mm) korunurken, 1000 mm çatal ile palet burun mesafesi kısalır. Bu, rampaya yaklaşırken hassas konumlandırmayı kolaylaştırır. PU tahrik ve çift yük tekerleri titreşimi sönümler; elektromanyetik fren yüklü inişlerde kontrollü duruş sağlar. 2×12V/85Ah akü ve 24V-15A dahili şarj altyapısı, farklı priz noktalarında ara şarjı destekler. Yüksek kullanım döngülerinde zamanlı batarya göstergesi opsiyonu vardiya planlamasını şeffaflaştırır.</p></section><section><h3>Kullanım Önerileri</h3><p>Euro paletlerde dar koridor yaklaşımı, hafif-orta yükler, paketleme ve sıralama istasyonları. Soğuk olmayan iç mekân zeminlerinde iz bırakmayan tahrik tekeri ile hijyen avantajı elde edilir. Güvenlik için her manevrada görüş hattını koruyun.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Mikro fulfillment istasyonlarında kısa mesafe taşıma'],
                    ['icon' => 'warehouse', 'text' => 'Cross-dock alanlarında hızlı yön değiştirme'],
                    ['icon' => 'store', 'text' => 'Perakende arka depo raf içi akış'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı toplama ve tampon stok besleme'],
                    ['icon' => 'industry', 'text' => 'Hücre içi WIP transferleri'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve etiketleme hatlarına besleme']
                ]
            ],
            [
                'sku' => 'EPT20-15ET-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-15ET - 1220 mm Çatal',
                'short_description' => 'Uzun çatal (1220 mm) ile hacimli paletlerde denge ve destek artar; rampa yaklaşımı ve istif alanı geçişlerinde paletin tabanına daha geniş temas sağlar.',
                'body' => '<section><h2>1220 mm: Hacimli Paletlere Geniş Temas</h2><p>1220 mm çatal, geniş tabanlı ve hacimli paletlerde dengeyi artırır. Palet tabanına temas yüzeyi büyüdüğü için yük dağılımı daha dengeli olur, özellikle düzensiz kutu kombinasyonlarında sallantı eğilimi azalır. 1.5 ton kapasite ve DC kontrollü sürüş, uzun çatalın getirdiği kol etkisini dengeler; elektromanyetik frenleme eğim geçişlerinde öngörülebilir duruş sunar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>Standart şasi boyutları korunurken, çatal burun mesafesi uzar; bu durum dar alanlarda ekstra manevra alanı gerektirir. Operasyon planında dönüş noktaları ve tampon alanlar bu ihtiyaca göre ayarlanmalıdır. PU tekerlek seti sessiz çalışma ve zemin koruması sağlar; opsiyonel caster tekerleri, düzensiz yüzeylerde iz takibini iyileştirir. 2×12V/85Ah akü ve dahili şarj ile kesintisiz çalışma süreleri arasında ara şarj yapılabilir.</p></section><section><h3>Kullanım Önerileri</h3><p>İçecek kasaları, hacimli koli kombinasyonları ve yükleme alanına hat besleyen paletler. Rampa yaklaşımında paleti dik ve dengeli konumlandırın; uzun çatalda dönüş yarıçapı hissedilir, operatör görüşünü önceliklendirin.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasası paletlerinde geniş taban desteği'],
                    ['icon' => 'couch', 'text' => 'Mobilya komponent paletlerinde denge'],
                    ['icon' => 'car', 'text' => 'Otomotiv hacimli kutu transferleri'],
                    ['icon' => 'warehouse', 'text' => 'Yükleme alanı tampon stok yönetimi'],
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında hacimli koli akışı'],
                    ['icon' => 'layer-group', 'text' => 'Karışık palet kombinasyonlarında stabilizasyon']
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

        $this->command->info("✅ Variants: EPT20-15ET → " . count($variants) . " adet");
    }
}
