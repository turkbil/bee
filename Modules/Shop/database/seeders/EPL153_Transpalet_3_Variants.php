<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL153_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EPL153')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EPL153');
            return;
        }
        $variants = [
            [
                'sku' => 'EPL153-900',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL153 - 900 mm Çatal',
                'short_description' => '900 mm çatal uzunluğu, dar koridor ve mikro depo alanlarında hızlı manevra ve hafif paletlerde hız/çeviklik önceliği sunar. 1500 kg sınıfında kısa palet uyumu artırır.',
                'body' => '<section><h3>900 mm çatal ile kompakt manevra</h3><p>900 mm çatal uzunluğu, özellikle dar raf arası ve mikro depo alanlarında palet pozisyonlamayı kolaylaştırır. Kısa paletler ve yarım palet uygulamalarında sürüş çevikliğini artırır; 400 mm l2 ve 1330 mm dönüş yarıçapı ile birlikte yüksek manevra sağlar.</p></section><section><h3>Teknik uyum</h3><p>Temel platform, 1500 kg kapasite, 24V/20Ah Li-Ion batarya ve DC tahrik ile aynıdır. Poliüretan tekerlekler düşük gürültü ve zemin koruması sağlar. 4.5/5.0 km/s hız değerleri dar alanda verimli akış üretir.</p></section><section><h3>Kullanım senaryoları</h3><p>Hızlı toplama, mağaza arka depo ve servis asansörlü binalarda yük taşıma için optimize edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Mağaza arka depo dar koridor toplama'],
                    ['icon' => 'box-open', 'text' => 'Küçük paketli e-ticaret sipariş hazırlama'],
                    ['icon' => 'warehouse', 'text' => 'Kompakt raf alanlarında cross-dock'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketimde raf dolumu'],
                    ['icon' => 'industry', 'text' => 'Hücresel üretimde ara istasyon taşıma'],
                    ['icon' => 'car', 'text' => 'Servis asansörü bulunan otopark lojistiği']
                ]
            ],
            [
                'sku' => 'EPL153-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL153 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, 1000×1200 paletlerde daha uzun taban desteği ve geçiş stabilitesi sağlar. Uzun kutu ve koli dizilimlerinde palet dengesi ve operatör kontrolünü artırır.',
                'body' => '<section><h3>1220 mm çatal ile geniş palet uyumu</h3><p>1220 mm çatal uzunluğu, 1000×1200 paletlerin çapraz taşınmasında daha iyi destek sağlar. Dönüş yarıçapı ve koridor gereksinimleri platformla uyumlu kalır; palet altı temas alanı artar.</p></section><section><h3>Operasyon verimi</h3><p>Uzun kutu ve karışık koli dizilimlerinde yük taşınırken çatal ucunda esneme ve devrilme risklerini azaltmaya yardımcı olur. 24V/20Ah batarya ve DC tahrik ile gün içi süreklilik sağlar.</p></section><section><h3>Kullanım senaryoları</h3><p>Perakende dağıtım merkezleri ve 3PL hat besleme operasyonları için uygundur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL hat besleme ve ayrıştırma'],
                    ['icon' => 'box-open', 'text' => 'Karma koli paletlerinde taşıma'],
                    ['icon' => 'store', 'text' => 'Perakende merkezlerinde sipariş konsolidasyonu'],
                    ['icon' => 'industry', 'text' => 'Montaj hattı ara stok taşıma'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG bölgesel dağıtım depoları'],
                    ['icon' => 'car', 'text' => 'Ara depo-rampa arası transfer']
                ]
            ],
            [
                'sku' => 'EPL153-685W',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPL153 - 685 mm Çatal Genişliği',
                'short_description' => '685 mm çatal aralığı, geniş tabanlı palet ve kasalarda denge sağlar. Özellikle içecek, kimya ve otomotiv kasalarında palet altı destek alanı artar, titreşim ve esneme azalır.',
                'body' => '<section><h3>685 mm genişlik ile stabil taşıma</h3><p>Geniş çatal aralığı, tabanı geniş ürün gruplarında palet altı destek alanını artırır ve titreşimi azaltır. 50×150×1150 mm çatal kesiti platform ile uyumludur.</p></section><section><h3>Performans</h3><p>1500 kg kapasite ve 4.5/5.0 km/s hız değerleri korunur. PU tekerlek bileşenleri zemin koruması ve sessiz çalışma sunar.</p></section><section><h3>Kullanım senaryoları</h3><p>İçecek kasaları, kimyasal bidon paletleri ve otomotiv kasalarında tercih edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'cart-shopping', 'text' => 'İçecek kasası paletleri'],
                    ['icon' => 'flask', 'text' => 'Kimyasal bidon ve varil paletleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv kasa ve komponent taşıma'],
                    ['icon' => 'warehouse', 'text' => 'Dağıtım merkezi rampaları'],
                    ['icon' => 'industry', 'text' => 'Üretim içi yarı mamul akışı'],
                    ['icon' => 'box-open', 'text' => 'Ağır koli dizilimlerinde denge']
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
            $this->command->info("✅ Variant: {$v['sku']}");
        }
    }
}
