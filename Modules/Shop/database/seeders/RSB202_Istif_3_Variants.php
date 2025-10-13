<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSB202_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'RSB202')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: RSB202');
            return;
        }

        $variants = [
            [
                'sku' => 'RSB202-3300',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSB202 - 3.3 m Direk (Platformlu)',
                'short_description' => '3300 mm kaldırma yüksekliği; 2000 kg kapasite ve EPS ile dar koridorlarda güvenli yığma. Li-Ion akü ve entegre şarj, vardiya arasında fırsat şarjını mümkün kılar.',
                'body' => '<section><h2>3.3 metre ile raf yüksekliğine nokta atışı</h2><p>İXTİF RSB202’nin 3.3 m direk varyantı, orta yükseklikte raf sistemlerinin en verimli kullanımını hedefler. 850 mm genişlikteki kompakt gövde ve 1620 mm dönüş yarıçapı, sipariş toplama ve replenishment arasında hızlı geçişler sağlar. Operatör, darbe emici katlanır platformda gün boyu konforludur; yan kol korumaları platform açıkken sürüş güvenliğini artırır.</p></section><section><h3>Teknik</h3><p>Bu varyant, 2000 kg kapasiteyi 600 mm yük merkezinde korurken 3300 mm’e kadar kaldırma sunar. 1.6 kW AC sürüş motoru ve 3.0 kW kaldırma motoru; 5.5/6 km/s seyir, 0.12/0.2 m/s kaldırma ve 0.3/0.2 m/s indirme hızlarına sahiptir. Oransal kaldırma, raf hizasında hassasiyet sağlayarak cam, elektronik ve kozmetik ürünlerde darbeyi azaltır. 24V/100Ah Li-Ion batarya, 30A entegre şarj cihazı ile molalarda hızla takviye edilir; düşük bakım ihtiyacı ile toplam sahip olma maliyetini düşürür.</p></section><section><h3>Sonuç</h3><p>3.3 m varyant, orta yükseklikli rafların hakim olduğu operasyonlarda en iyi dengeyi sunar. Depo akışını hızlandırmak ve operatör konforunu artırmak için ideal kombinasyon.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Orta yükseklikli raflarda replenishment ve istif'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret outbound’da hızlı konsolidasyon'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de mağaza paleti hazırlığı'],
                    ['icon' => 'pills', 'text' => 'Kozmetik ve ilaç kolilerinde hassas yükleme'],
                    ['icon' => 'microchip', 'text' => 'Elektronik ürün raflarında dar toleranslı istif'],
                    ['icon' => 'industry', 'text' => 'Üretim içi hat besleme ve WIP akışı']
                ]
            ],
            [
                'sku' => 'RSB202-3900',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSB202 - 3.9 m Direk (Platformlu)',
                'short_description' => '3900 mm maksimum kaldırma; yeni H-profil direkle yüksek rijitlik. EPS ve oransal kaldırma ile yüksek seviyelerde hassas ve güvenli konumlandırma.',
                'body' => '<section><h2>3.9 metre: yüksek seviyede kararlılık ve kontrol</h2><p>Yüksek raflarda çalışmak dikkat ve hassasiyet ister. RSB202 3.9 m varyantı, güçlendirilmiş H-profil direk yapısıyla üst seviyelerde dahi salınımı azaltır. Entegre Li-Ion enerji sistemi ve amortisörlü platform, operatörün vardiya boyunca konforlu ve verimli kalmasını sağlar.</p></section><section><h3>Teknik</h3><p>2000 kg kapasite (c=600 mm) korunurken, 3900 mm’e kadar kaldırma mümkündür. 1.6 kW sürüş ve 3.0 kW kaldırma motorları; 5.5/6 km/s seyir, 0.12/0.2 m/s kaldırma ve 0.3/0.2 m/s indirme hızlarıyla dengeli performans sunar. 850 mm genişlik, 1620 mm dönüş yarıçapı ve EPS; dar koridorlarda yüksek seviyeye güvenle yaklaşmayı kolaylaştırır. Oransal kaldırma, yükün rafla hizalanmasını milimetrik hale getirir.</p></section><section><h3>Sonuç</h3><p>Yüksek istif gerektiren depolarda hızlı, hassas ve güvenli operasyon için 3.9 m varyantı seçin. Uzun vadeli verimlilik için Li-Ion sistem düşük bakım maliyeti sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı depo koridorlarında istif'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depo giriş-çıkışında kontrollü konumlandırma'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça yüksek raf uygulamaları'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında hassas ürün raflama'],
                    ['icon' => 'briefcase', 'text' => '3PL operasyonlarında tepe raf replenishment'],
                    ['icon' => 'star', 'text' => 'Kalite kontrol istasyonuna üst seviye besleme']
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
            $this->command->info("✅ Variant eklendi/güncellendi: {$v['sku']}");
        }
    }
}
