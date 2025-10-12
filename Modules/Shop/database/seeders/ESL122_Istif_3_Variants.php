<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESL122_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ESL122')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ESL122'); return; }

        $variants = [
            [
                'sku' => 'ESL122-2513',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESL122 - 2513 mm Kaldırma (Standart Direk)',
                'short_description' => 'Hafif raf yükseklikleri için optimize 2513 mm kaldırma; dar koridor operasyonlarında hızlı yerleştirme ve sessiz taşıma sağlayan Li-Ion enerji sistemiyle düşük TCO.',
                'long_description' => '<section><h3>Özet</h3><p>2513 mm kaldırma varyantı, giriş seviyesi raf yüksekliklerinin yoğun olduğu dağıtım merkezleri için tasarlanmıştır. 24V/100Ah Li-Ion enerji, entegre şarj ve DC sürüş ile molalarda kısa şarj, vardiyada uzun süreli tutarlı performans sunar. Dar koridorlarda 1464 mm dönüş yarıçapı ve ofset tiller ergonomisi, operatörün palete güvenli yaklaşımını ve konumlandırmasını kolaylaştırır.</p></section><section><h3>Teknik Uygunluk</h3><p>Rijit direk yapısı, 2513 mm seviyesinde sarsıntısız kaldırma sağlar. 4.2/4.5 km/s seyir hızları, 0.09/0.13 m/s kaldırma hızlarıyla kısa çevrimli toplama ve yerleştirme işlerinde akışı hızlandırır. Poliüretan tekerler düz iç zeminlerde sessizdir; elektromanyetik fren rampalarda kontrollü duruş verir. 560 kg servis ağırlığı ve 1212 mm dingil açıklığı dengeyi destekler.</p></section><section><h3>Kullanım Senaryosu</h3><p>Back-of-store alanları, mikro dağıtım merkezleri ve düşük raflı 3PL istasyonlarında temel istif işlerini verimli kılar. Entegre şarj altyapı yatırımını azaltır, düşük toplam sahip olma maliyeti sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Mağaza arkası ve perakende ayrıştırma alanları'],
                    ['icon' => 'warehouse', 'text' => '3PL istasyonlarında düşük raflı konsolidasyon'],
                    ['icon' => 'box-open', 'text' => 'Kargo mikro hub birimlerinde hızlı yerleştirme'],
                    ['icon' => 'car', 'text' => 'Otomotiv küçük parça raflarında kutu istifi'],
                    ['icon' => 'pills', 'text' => 'İlaç/kozmetik bölmelerinde palet bazlı taşıma'],
                    ['icon' => 'industry', 'text' => 'Hafif üretim hücrelerinde WIP besleme']
                ]
            ],
            [
                'sku' => 'ESL122-3013',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESL122 - 3013 mm Kaldırma (Duplex Direk)',
                'short_description' => '3013 mm kaldırma, depo içi rafların çoğuna erişim sağlar; entegre şarj ve DC kontrol ile sürekli ve öngörülebilir malzeme akışı sunar.',
                'long_description' => '<section><h3>Özet</h3><p>3013 mm varyantı, e-ticaret ve perakende depolarında yaygın raf yüksekliklerine erişimi kolaylaştırır. Uzun ofset tiller ve kaplumbağa modu, yoğun trafikte operatör ile yük arasında güvenli mesafeyi korur.</p></section><section><h3>Teknik Uygunluk</h3><p>2.2 kW kaldırma motoru ve kaliteli hidrolik pompa, yüklü/boş 0.09/0.13 m/s kaldırma hızlarını istikrarlı tutar. 4.2/4.5 km/s seyir, elektromanyetik fren ile birleşerek sık dur-kalk çevrimlerinde kontrol sağlar. Poliüretan teker seti sessizdir; 2296/2230 mm koridor gereksinimleri raf arası akışı destekler.</p></section><section><h3>Kullanım Senaryosu</h3><p>E-ticaret konsolidasyon adaları, perakende back-of-store ve paketleme hatlarında standart paletlerin toplanması ve istiflenmesinde verimlilik sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret raf besleme ve konsolidasyon adaları'],
                    ['icon' => 'store', 'text' => 'Perakende dağıtımda sipariş hazırlama'],
                    ['icon' => 'warehouse', 'text' => '3PL çapraz sevkiyat alanları'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış koridorları'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalaj depolama rafları'],
                    ['icon' => 'briefcase', 'text' => 'Arşiv ve kırtasiye raf düzenleri']
                ]
            ],
            [
                'sku' => 'ESL122-3313',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESL122 - 3313 mm Kaldırma (Duplex Direk)',
                'short_description' => '3313 mm kaldırma, daha yüksek ikinci-üçüncü seviye raflara ulaşırken kompakt şasi ve 1464 mm dönüş yarıçapıyla dar koridor çevikliğini korur.',
                'long_description' => '<section><h3>Özet</h3><p>3313 mm varyantı, standart yüksekliği aşan raflara erişim gerektiren depo alanları için geliştirilmiştir. Li-Ion enerji sistemi hızlı şarj ve düşük bakım avantajı sağlar; DC kontrol ve elektromanyetik fren öngörülebilir sürüş hissi verir.</p></section><section><h3>Teknik Uygunluk</h3><p>Rijit direk mimarisi yük altında sapmayı minimize eder. 0.62 kWh/saat tipik tüketim ile enerji verimliliği korunur. 4.2/4.5 km/s seyir, 0.09/0.13 m/s kaldırma hızlarıyla birlikte talep dalgalanmalarında süreklilik sunar.</p></section><section><h3>Kullanım Senaryosu</h3><p>3PL yüksek raf adaları, elektronik ve kozmetik depo alanlarında yüksek hacimli, hafif paletlerin güvenli yerleştirilmesini mümkün kılar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL yüksek raf adalarında yerleştirme'],
                    ['icon' => 'microchip', 'text' => 'Elektronik komponent paletleri'],
                    ['icon' => 'pills', 'text' => 'Kozmetik depolama rafları'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG üst raf besleme'],
                    ['icon' => 'industry', 'text' => 'Hafif üretim çıkış paletleri'],
                    ['icon' => 'box-open', 'text' => 'Mikro hub’larda üst seviye stoklama']
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

        $this->command->info('✅ Variants eklendi: ESL122 (2513 / 3013 / 3313)');
    }
}
