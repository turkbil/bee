<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WSA161i_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'WSA161i')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        $variants = [
            [
                'sku' => 'WSA161i-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF WSA161i - 3.0 m Direk',
                'short_description' => '3.0 metre kaldırma yüksekliği ile düşük-orta raflı depolar için optimum çeviklik; 1600 kg mast kaldırma ve 2000 kg destek kolu kapasitesiyle güvenli istif.',
                'body' => '<section><h3>3.0 m Direk: Çevik ve Hızlı</h3><p>3.0 m direkli WSA161i, düşük ve orta seviye raf yüksekliğine sahip depolarda en hızlı çevrimleri sunar. 2015 mm kapalı direk yüksekliği kapı geçişlerini kolaylaştırır; 2915 mm kaldırma ile tek vardiyada binlerce palet hareketi güvenle tamamlanır. Başlangıç kaldırma, rampaları ve eşikleri rahat aşar, çift kat palet taşıma özelliği yoğun saatlerde throughput’u artırır.</p></section><section><h3>Teknik Odak</h3><p>24V/100Ah Li-ion batarya ve entegre 24V/30A şarj cihazı, molalarda fırsat şarjı ile kesintisiz akış sağlar. 1.6 kW AC sürüş ve 4.5 kW kaldırma motoru, 5.0/5.5 km/s sürüş ve 0.23/0.30 m/s kaldırma hızlarını destekler. 1826 mm dönüş yarıçapı ve 878 mm yük yüzüne uzunluk dar koridorlarda seri manevrayı mümkün kılar.</p></section><section><h3>Uygulama</h3><p>Toplama sonrası geri besleme, konsolidasyon ve kısa mesafe hat besleme süreçlerinde en ekonomik çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Düşük raflı 3PL alanlarında hızlı replenishment çevrimleri'],
                    ['icon' => 'store', 'text' => 'Perakende DC içinde kapı-raf kısa mesafe transferleri'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret iade kabul alanlarında katmanlı istif'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP ara stok taşıma'],
                    ['icon' => 'car', 'text' => 'Aftermarket yedek parça raf servisleri'],
                    ['icon' => 'flask', 'text' => 'Kimyasal depolarda düşük seviyeli raflama']
                ]
            ],
            [
                'sku' => 'WSA161i-4500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF WSA161i - 4.5 m Direk',
                'short_description' => '4.5 metreye kadar kaldırma ile orta-yüksek raf uygulamalarında hızlı yerleştirme; oransal hidrolik kontrolle hassas konumlandırma ve düşük çevrim süresi.',
                'body' => '<section><h3>4.5 m Direk: Yüksekliği Hızla Yönetin</h3><p>4.5 m seçenek, çok seviyeli raflı depolarda toplama ve yerleştirme hızını artırır. Oransal kaldırma/indirme, paletin raf başında yumuşak ve kontrollü şekilde hareket etmesini sağlar. Başlangıç kaldırma, rampalarda ve eşiklerde kesintisiz akış sunar.</p></section><section><h3>Teknik Odak</h3><p>24V Li-ion enerji sistemi ile fırsat şarjı; 1.6 kW AC tahrik ve 4.5 kW kaldırma motoru ile yüksek hidrolik performans. 1826 mm dönüş yarıçapı ve kompakt gövde dar koridorlarda verimlilik sağlar.</p></section><section><h3>Uygulama</h3><p>Yoğun palet trafiğinde çift kat taşımanın avantajı ile konsolidasyon ve yükleme öncesi sıralamada güçlü katkı verir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Raf arası 3-4 seviye istif uygulamaları'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depolarda yükleme rampası yaklaşımı'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment yoğun saatlerinde toplama sonrası geri besleme'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ve yumuşak yerleştirme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında yüksek frekanslı konveyör beslemesi'],
                    ['icon' => 'briefcase', 'text' => 'Operatör geçişlerinde güvenli konumlandırma']
                ]
            ],
            [
                'sku' => 'WSA161i-5500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF WSA161i - 5.5 m Direk',
                'short_description' => '5.5 metre maksimum kaldırma yüksekliği ile yüksek raflı tesislerde verim; Li-ion enerji ve AC tahrik ile kesintisiz vardiya uyumu.',
                'body' => '<section><h3>5.5 m Direk: En Yüksek Raflara Erişim</h3><p>WSA161i’nin 5.5 m mast seçeneği, yüksek raflı modern depolarda alan kullanımını en üst düzeye taşır. Oransal hidrolik kontrol, üst seviyelerde dahi hassas hizalama sağlar. Başlangıç kaldırma ve destek kolu yapısı iki paleti bir arada hareket ettirerek yoğun saatlerde akışı hızlandırır.</p></section><section><h3>Teknik Odak</h3><p>24V/100Ah Li-ion batarya ve entegre şarj ile molalarda hızla takviye; opsiyonel 205Ah paketle tek vardiyada daha uzun süre. 1.6 kW AC sürüş ve 4.5 kW kaldırma motoru, yüksek seviyelerde bile tutarlı hız ve güç sunar.</p></section><section><h3>Uygulama</h3><p>Çok seviyeli raflı 3PL merkezleri, yüksek katlı perakende DC ve yedek parça megaparklarında optimum çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı 3PL mega depolarında istif'],
                    ['icon' => 'building', 'text' => 'Çok katlı DC alanlarında alan verimliliği'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası finished goods depolama'],
                    ['icon' => 'car', 'text' => 'OEM yedek parça megaparklarında üst seviye raflama'],
                    ['icon' => 'flask', 'text' => 'Kimya ve boya depolarında üst seviye güvenli istif'],
                    ['icon' => 'star', 'text' => 'Hassas yerleştirme gerektiren premium operasyonlar']
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
                'product_type' => 'physical',
                'condition' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant: {$v['sku']}");
        }
    }
}
