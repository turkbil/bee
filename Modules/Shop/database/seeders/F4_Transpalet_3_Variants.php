<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'F4-1500')->first();
        if (!$m) {
            echo "❌ Master bulunamadı\n";
            return;
        }
        $variants = [
            [
                'sku' => 'F4-1500-1150x560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F4 - 1150×560 mm Çatal',
                'short_description' => 'Standart 1150×560 mm çatal, EUR paletlerle maksimum uyum ve dar koridor çevikliği sunar; 1360 mm dönüş yarıçapı ile hızlı yön değiştirir.',
                'body' => '<section><h3>1150×560 mm: Depo standartları için altın oran</h3><p>F4\'ün 1150×560 mm çatal konfigürasyonu, Avrupa standardı EUR paletlerle tam uyum sağlayarak sipariş hazırlama ve çapraz sevkiyat hatlarında akışı hızlandırır. 400 mm l2 ölçüsü, dar koridorlar ve kapı eşikleri arasında manevrayı kolaylaştırır; 590 mm toplam genişlik ile raf arası dolaşım sorunsuzdur. 1.5 ton kapasite, 0.75 kW sürüş ve elektromanyetik frenle güvenli taşıma performansı sunar. Operatör, 750–1190 mm aralığındaki tiller yüksekliği sayesinde farklı boylarda konforlu kullanım elde eder.</p><p>Tekli 24V 20Ah Li‑Ion modül, fırsat şarjı ile kesintisiz akış sağlar; yoğun kullanımlar için ikinci modül eklenerek 2×24V 20Ah yapılandırmaya geçilebilir. PU tekerlekler sessiz çalışma ve düşük zemin aşınması sağlar. 0.017/0.020 m/s kaldırma hızları, ürün hasarını azaltan kontrol hissi verir.</p></section><section><h3>Operasyon uyumu</h3><p>Bu varyant; lokasyon içi kısa mesafeli transferler, mağaza hazırlığı ve rampa yaklaşımlarında optimum denge sağlar. 2025–2160 mm koridor ihtiyaçları ve 1360 mm dönüş yarıçapı; sipariş ve replenishment akışlarında verimliliği artırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR palet ile sipariş konsolidasyonu'],
                    ['icon' => 'store', 'text' => 'Mağaza hazırlık alanlarında hızlı taşıma'],
                    ['icon' => 'warehouse', 'text' => 'Raf arası kısa mesafe besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG kutu ve koli hareketi'],
                    ['icon' => 'industry', 'text' => 'Montaj hücrelerinde WIP akışı'],
                    ['icon' => 'car', 'text' => 'Yedek parça depolarında rampa yaklaşımı']
                ]
            ],
            [
                'sku' => 'F4-1500-1220x685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF F4 - 1220×685 mm Çatal',
                'short_description' => '1220×685 mm çatal, geniş paletler ve stabil yüklerde güven verir; 695 mm gövde genişliğiyle denge ve yön kararlılığı artar.',
                'body' => '<section><h3>Geniş paletler için 1220×685 mm çözüm</h3><p>Geniş 685 mm çatal açıklığı ve 1220 mm uzunluk, iri hacimli veya esnemeye yatkın paletlerde yük denge merkezini iyileştirir. Bu konfigürasyon, stabilize teker opsiyonu ile birleştiğinde bozuk zeminlerde hasar ve düşme riskini azaltır. 1.5 ton kapasite, DC sürüş kontrolü ve elektromanyetik fren ile güvenli bir kullanıcı deneyimi sunar.</p><p>2×24V 20Ah batarya seçeneği vardiya sürekliliğini destekler. 333.33 t/kWh verimlilik ve 0.18 kWh/h tüketim değerleri, toplam sahip olma maliyetini aşağıya çeker. 1360 mm dönüş yarıçapı operasyon esnekliği sağlar.</p></section><section><h3>Operasyon uyumu</h3><p>Bu varyant; içecek kasaları, ev aleti ambalajları ve geniş plastik paletler gibi yüklerde daha düzgün bir iz ve daha az salınım sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve variller'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya ve büyük kutular'],
                    ['icon' => 'print', 'text' => 'Ambalaj ruloları ve geniş paletler'],
                    ['icon' => 'warehouse', 'text' => 'Depo içi toplu transfer'],
                    ['icon' => 'shield-alt', 'text' => 'Bozuk zeminlerde stabil taşıma'],
                    ['icon' => 'cart-shopping', 'text' => 'Toplu perakende sevkiyatı']
                ]
            ],
            [
                'sku' => 'F4-1500-1000x560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F4 - 1000×560 mm Çatal',
                'short_description' => '1000×560 mm çatal, daha kısa paletlerde manevra kabiliyetini artırır; dar alanlı arka odalar ve soğuk odalarda pratiktir.',
                'body' => '<section><h3>Kısa paletler için çevik seçenek</h3><p>1000×560 mm konfigürasyon, kısa paletler ve dar kapılı alanlarda dönüş ve hizalama kolaylığı sağlar. 400 mm l2 ve 590 mm gövde genişliği ile birlikte kullanıldığında, sipariş toplama ve replenishment görevlerinde hızlı tepkiler verir. 4.0/4.5 km/s yürüyüş hızları ve PU tekerlekler sessiz ve kontrollü hareket üretir.</p><p>Tek modül 24V 20Ah ile hafif, iki modül ile uzun soluklu çalışma mümkündür. 0.017/0.020 m/s kaldırma hızı ve elektromanyetik fren, yük hassasiyetini artırır.</p></section><section><h3>Operasyon uyumu</h3><p>Backroom, eczane ve market arkası alanlar gibi dar mekanlarda hızlı taşıma ve raf öncesi akış için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Backroom ve market arkası lojistik'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda içi kısa mesafe taşıma'],
                    ['icon' => 'pills', 'text' => 'Eczane dağıtım merkezlerinde küçük paletler'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret paketleme adası besleme'],
                    ['icon' => 'industry', 'text' => 'Hücre içi malzeme hareketi'],
                    ['icon' => 'car', 'text' => 'Araç içi yükleme/indirme']
                ]
            ],
            [
                'sku' => 'F4-1500-1350x685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F4 - 1350×685 mm Çatal',
                'short_description' => '1350×685 mm çatal, esneyen veya uzun yüklerde daha iyi ağırlık dağılımı ve denge sağlar; stabilite tekeri ile riskleri azaltır.',
                'body' => '<section><h3>Uzun yüklerde denge odaklı yapı</h3><p>1350×685 mm seçeneği; esnek, uzun veya gövdesi taşma yapan paletlerin dengeli taşınmasında üstündür. Geniş açıklık, ağırlık merkezinin teker izine daha iyi yayılmasına yardımcı olur. 6%/16% eğim kabiliyeti ve elektromanyetik fren, rampalarda güven verir. İkinci aküyle vardiya sürekliliği mümkün olur.</p><p>VDI 2198’e göre 60 t/h iş çevrimi, yüksek verimlilik ile yoğun hatlarda kapasite yaratır.</p></section><section><h3>Operasyon uyumu</h3><p>Ev aleti, mobilya ve geniş plastik palet lojistiğinde çizgisel denge sağlayarak hasar riskini düşürür.</p></section>',
                'use_cases' => [
                    ['icon' => 'couch', 'text' => 'Mobilya ve ev dekorasyonu paletleri'],
                    ['icon' => 'tv', 'text' => 'Büyük hacimli dayanıklı tüketim'],
                    ['icon' => 'cart-shopping', 'text' => 'Toplu mağaza sevkiyatları'],
                    ['icon' => 'warehouse', 'text' => 'Uzun palet hat besleme'],
                    ['icon' => 'shield-alt', 'text' => 'Düşük hasar riskiyle taşıma'],
                    ['icon' => 'bolt', 'text' => 'İş akışında süreklilik']
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
        echo "✅ Variants: 4 kayıt eklendi\n";
    }
}
