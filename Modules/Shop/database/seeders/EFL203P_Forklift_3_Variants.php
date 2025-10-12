<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL203P_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL203P')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL203P'); return; }

        $variants = [
            [
                'sku' => 'EFL203P-230AH',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL203P - 80V 230Ah Li-Ion',
                'short_description' => 'Premium sürüş ve kaldırma hızlarını 80V 230Ah paketle sunar; fırsat şarjı ile tempo kesmeden çoklu dur-kalk operasyonlar için idealdir.',
                'long_description' => '<section><h3>Premium hız, standart paket</h3><p>230Ah paket, EFL203P’nin yüksek performans eğrisini fırsat şarjıyla destekler. Kısa ama sık şarj molalarıyla, vardiya içi üretkenliği korur. 19/20 km/s hız ve 0.48/0.54 m/s kaldırma, liman arkası ve rampalı sahalarda çevik akış sağlar.</p></section><section><h3>Kontrol ve görüş</h3><p>Optimize hortum güzergâhı ve cıvatalı OHG, çatala ve çevreye net görüş kazandırır. Büyük LED ekran, akü durumu ve arıza kodlarını okunaklı sunarak operatör karar hızını artırır.</p></section><section><h3>Dayanıklılık</h3><p>Yüksek şasi açıklığı, pnömatik lastikler ve suya dayanıklı yapı, dış sahada yağış ve bozuk satıhta tutarlı çekiş sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yoğun rampalı cross-dock alanları'],
                    ['icon' => 'box-open', 'text' => 'Hızlı sevkiyat konsolidasyonu'],
                    ['icon' => 'car', 'text' => 'Otomotiv sekanslama ve hat besleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC pik saat toplama'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk zincirde hızlı devir'],
                    ['icon' => 'industry', 'text' => 'Ağır üretimde çevik iç lojistik']
                ]
            ],
            [
                'sku' => 'EFL203P-460AH',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL203P - 80V 460Ah Li-Ion',
                'short_description' => '460Ah paket ile premium performans uzun otonomiye taşınır; çok vardiyalı ve ağır yük döngülerinde daha az şarj durağıyla daha fazla üretkenlik.',
                'long_description' => '<section><h3>Uzun menzil, yüksek tempo</h3><p>460Ah Li-Ion, rampa ve dur-kalk yoğun operasyonlarda daha uzun aralıksız çalışma sağlar. BMS, ısı ve akım yönetimiyle güç teslimi stabil kalır; vardiya planlamasında şarj pencereleri seyrekleşir.</p></section><section><h3>Operasyonel avantaj</h3><p>Yüksek hız ve tırmanma kapasitesi ile birleşen büyük batarya, tur başına süreyi kısaltırken saatlik çıktı miktarını artırır.</p></section><section><h3>Planlama</h3><p>Şarj altyapısı üzerindeki baskı azalır; filo kullanım oranı ve görev eşlemesi daha esnek hâle gelir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3 vardiya çalışan 3PL depoları'],
                    ['icon' => 'flask', 'text' => 'Kimya lojistiğinde aralıksız akış'],
                    ['icon' => 'car', 'text' => 'OEM tedarik parkurlarında yoğun çekiş'],
                    ['icon' => 'store', 'text' => 'Büyük perakende DC aktarma merkezleri'],
                    ['icon' => 'snowflake', 'text' => 'Düşük sıcaklıkta yüksek süreklilik'],
                    ['icon' => 'industry', 'text' => 'Ağır üretim, çok vardiyalı hat besleme']
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
        $this->command->info('🧬 Variants eklendi: EFL203P');
    }
}
