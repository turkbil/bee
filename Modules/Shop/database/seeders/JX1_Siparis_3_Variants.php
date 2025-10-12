<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX1_Siparis_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'JX1')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (JX1)'); return; }

        $variants = [
            [
                'sku' => 'JX1-126',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF JX1 - 126 inç Platform Yüksekliği',
                'short_description' => '126” platform yüksekliği ve 178.6” mast ile düşük tavanlı iç alanlarda güvenli erişim; 52.5” dönüş yarıçapı ve kompakt 59” uzunlukla raf arası çeviklik.',
                'long_description' => '<section><h3>Dar Tavanlı Alanlar İçin Doğru Seçim</h3><p>126” platform seviyesine erişen bu yapılandırma, özellikle düşük tavanlı depolarda ve arka oda (backstore) alanlarında emniyetli erişim sağlar. 59” toplam uzunluk ve 31.5” genişlik ile dar koridorlarda akış bozulmaz; 180° eklemli tahrik hattı sıfır dönüş manevrasını mümkün kılar. 24V mimaride AGM, kurşun asit veya Li‑ion akü seçenekleri, vardiya uzunluğunuza göre esnek planlama sunar.</p></section><section><h3>Teknik Denge</h3><p>Servis ağırlığı yaklaşık 2617 lb olan 126” konfigürasyonunda mast tam yükselmiş yükseklik 178.6” değerindedir. Sürüş 3.4 mph standart, 5 mph opsiyon olarak yapılandırılabilir. Kaldırma/indirme hızları 33.5/41.3 ve 68.9/51.2 fpm seviyesindedir; rejeneratif elektromanyetik fren güvenlik ve verimliliği birlikte sağlar.</p></section><section><h3>Sonuç</h3><p>Alan sınırlıysa ve ürün çeşitliliği yüksekse, bu varyant erişim ve çevikliği optimum dengede sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Perakende arka odalarda yükseğe erişim ve görsel düzenleme'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret istasyonlarında küçük koli toplama'],
                    ['icon' => 'warehouse', 'text' => '3PL mikro-fulfillment alanlarında ikmal'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı üst raflarında parça erişimi'],
                    ['icon' => 'flask', 'text' => 'Laboratuvar sarf depolarında kontrollü iç taşıma'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG kampanya stoklarının hızlı yerleştirilmesi']
                ]
            ],
            [
                'sku' => 'JX1-162',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF JX1 - 162 inç Platform Yüksekliği',
                'short_description' => '162” platform ve 235” mast ile orta-uzun raflarda hızlı erişim; 45.2” dingil aralığı, 52.5” dönüş yarıçapı ve geniş ön platform ile verimli toplama.',
                'long_description' => '<section><h3>Orta Yükseklikte Maksimum Verim</h3><p>162” platform yüksekliği, yaygın depo raf yükseklikleri için ideal bir denge sunar. Sezgisel kontroller ve anti‑yorgunluk mat, operatör performansını vardiya sonuna kadar yüksek tutar. Ön platformda 500 lb’ye kadar yük; arka tepside 200 lb taşıma kapasitesi ile tek turda daha fazla iş.</p></section><section><h3>Performans Ayrıntıları</h3><p>Mast tam yükselmiş yükseklik 235” ve servis ağırlığı yaklaşık 2992 lb’dir. 24V güç mimarisi 1.7 kW sürüş ve 2.2 kW kaldırma motorlarıyla dengelenir. 3.4 mph standart hız ve 5 mph opsiyon, güvenlik parametreleri korunarak seçilebilir.</p></section><section><h3>Sonuç</h3><p>Koridor başına düşen dur-kalk sayısını azaltır, operatörün kat ettiği mesafeyi kısaltır ve sipariş tamamlama hızını artırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Bölgesel dağıtım merkezlerinde sepet toplama'],
                    ['icon' => 'box-open', 'text' => 'Birleştirme ve ayırma istasyonları arasında ara taşıma'],
                    ['icon' => 'store', 'text' => 'Mağaza içi depo alanlarında stok sayımı'],
                    ['icon' => 'industry', 'text' => 'Yarı mamul WIP raflarına erişim'],
                    ['icon' => 'cart-shopping', 'text' => 'Promosyon hazırlık alanlarında hızlı yerleştirme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal sarf malzemelerinin iç lojistiği']
                ]
            ],
            [
                'sku' => 'JX1-192',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF JX1 - 192 inç Platform Yüksekliği',
                'short_description' => '192” platform, 265” mast ve 56” dönüş yarıçapı ile yüksek raflı depolarda maksimum erişim; 63.25” uzunluk ve 34” genişlikle dengeli stabilite.',
                'long_description' => '<section><h3>Yüksek Raflı Depolarda Üst Sınır</h3><p>En yüksek 192” platform erişimi, yüksek hacimli depolarda üst seviyelerde güvenle çalışma imkânı sunar. Genişleyen şasi ölçüleri (63.25” uzunluk, 34” genişlik) ve 49” dingil aralığı, yüksek seviyede stabilite sağlar.</p></section><section><h3>Kontrollü Hız, Güvenli Çıkış</h3><p>33.5/41.3 fpm kaldırma ve 68.9/51.2 fpm indirme hızı, yüksekte hassas pozisyonlamaya yardımcı olur. Rejeneratif elektromanyetik fren, riskli konumlarda dahi kontrollü yavaşlamayı mümkün kılar.</p></section><section><h3>Sonuç</h3><p>Üst raflardan ürün toplama, sezonluk stok yerleştirme ve teknik bakım çalışmaları için ideal ve güvenlidir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı lojistik merkezlerinde üst seviye toplama'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret tepe raflarından sezonluk stok çekimi'],
                    ['icon' => 'store', 'text' => 'Büyük mağaza arka depolarında üst raf yerleşimi'],
                    ['icon' => 'industry', 'text' => 'Üst ekipman bakımında güvenli erişim'],
                    ['icon' => 'cart-shopping', 'text' => 'Yoğun kampanya dönemlerinde üst seviye ikmal'],
                    ['icon' => 'flask', 'text' => 'Hassas ürün raflarında kontrollü erişim']
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
            $this->command->info("✅ Variant eklendi: {$v['sku']}");
        }
    }
}
