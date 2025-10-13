<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL_201_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFL201')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFL201');
            return;
        }

        $variants = [
            [
                'sku' => 'EFL201-2W300',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL201 - 2W300 (3000 mm Kaldırma)',
                'short_description' => '2W300 direk: 3000 mm kaldırma, 2020 mm kapalı yükseklik ve 4028 mm açık yükseklik. 2.0 ton kapasite ve 6°/10° tilt ile genel depo operasyonları için ideal.',
                'body' => '<section><h2>2W300: Genel Depo Standardı</h2><p>EFL201 2W300 konfigürasyonu; 3000 mm standart kaldırma ile en yaygın depo senaryolarını kapsar. 2020 mm kapalı direk yüksekliği kapı geçişlerini kolaylaştırır; 4028 mm açık yükseklik raf seviyelerinde güvenli istiflemeyi mümkün kılar. 2.0 ton nominal kapasite ve 500 mm yük merkezi, EUR palet akışlarında esnekliği artırır. 80V/150Ah Li‑Ion enerji ve 11/14 km/s sürüş hızları, yoğun vardiyalarda süreklilik sağlar.</p></section><section><h3>Teknik Odak</h3><p>6 kW AC sürüş ve 11 kW kaldırma motoru ile 0.25/0.30 m/s kaldırma değerlerine ulaşırsınız. 2100 mm dönüş yarıçapı dar koridorlarda çevik manevralar sunar. 120 mm yer açıklığı ve katı lastikler rampalı alanlarda güven verir; hidrolik servis ve mekanik park freni kombinasyonu ise yüklü duruşları güvenle yönetir.</p></section><section><h3>Sonuç</h3><p>2W300, maliyet/performans dengesini arayan işletmeler için doğru başlangıç noktasıdır; kapalı alanlarla açık sahalar arasındaki geçişleri kolaylaştırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo içi palet toplama ve sevkiyat hazırlığı'],
                    ['icon' => 'box-open', 'text' => 'E‑ticaret çıkış hatlarında yoğun besleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf arası replenishment'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parçalarında ara stok taşıma'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP akışı'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli palet hareketi']
                ]
            ],
            [
                'sku' => 'EFL201-2W330',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL201 - 2W330 (3300 mm Kaldırma)',
                'short_description' => '2W330 direk: 3300 mm kaldırma, 2170 mm kapalı yükseklik ve 4328 mm açık yükseklik. Görüşü iyileştirilmiş direk ile güvenli istifleme ve geniş uygulama.',
                'body' => '<section><h2>2W330: Esneklik ve Erişim</h2><p>3300 mm kaldırma yüksekliği, 2W300 standardının erişemediği raf seviyelerine uzanır. 2170 mm kapalı yükseklik geçişler için kabul edilebilir kalırken, 4328 mm açık yükseklik daha fazla istif senaryosu açar. Güçlü 80V platform, 12/15% tırmanma ve 10000 N çekiş verileriyle rampalı alanlarda da işinizi kolaylaştırır.</p></section><section><h3>Teknik Odak</h3><p>AC tahrik, hidrolik fren ve mekanik park freni kombinasyonu, yüklü‑boş hızlar arasında net ve kontrollü geçişler sağlar. 905/920 mm iz genişlikleri ve 1080 mm toplam genişlik, denge ile çevikliği dengeler.</p></section><section><h3>Sonuç</h3><p>2W330, raf yüksekliği gereksinimi artan depolarda verimden ödün vermeden erişimi büyütmek isteyen işletmeler için doğru tercihtir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raf geçişlerinde güvenli palet alma‑yerleştirme'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment alanında üst seviye toplama'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı üst kot beslemeleri'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depo kapı geçişlerinde hızlı operasyon'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ürün istifi'],
                    ['icon' => 'car', 'text' => 'CKD/SKD hat beslemede ara stok erişimi']
                ]
            ],
            [
                'sku' => 'EFL201-3F450',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL201 - 3F450 (4500 mm Triplex)',
                'short_description' => '3F450 triplex direk: 4500 mm kaldırma, 2085 mm kapalı ve 5528 mm açık yükseklik; serbest kaldırma ile kapı altı uygulamalarda geniş erişim.',
                'body' => '<section><h2>3F450: Triplex ile Yüksek Erişim</h2><p>Triplex 3F450 mast, serbest kaldırma kapasitesiyle kapalı girişlerde çalışırken kafes temasını azaltır; 4500 mm kaldırma ile çok katlı raf yapılarına erişir. 2085 mm kapalı yükseklik, alçak kapı ve yükleme noktalarında manevrayı kolaylaştırır. 80V/150Ah enerji sistemi ve hızlı fırsat şarjı, yüksek istif döngülerinde dahi temponuzu korur.</p></section><section><h3>Teknik Odak</h3><p>0.25/0.30 m/s kaldırma hızları, 70 dB(A) kabin içi gürültü ve 2100 mm dönüş yarıçapı, operatör verimliliğini artırır. 2A çatal arabası ve 40×122×1070 mm çatal ölçüleri farklı palet türlerine uyumu sürdürür.</p></section><section><h3>Sonuç</h3><p>3F450; hem alçak eşiklerde çalışıp hem de yüksek kotlara erişmesi gereken işletmeler için optimum çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Çok katlı raflı depolarda üst kot istifi'],
                    ['icon' => 'box-open', 'text' => 'Toplama‑yerleştirme döngülerinde hızlı çevrim'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası mamul depoları'],
                    ['icon' => 'flask', 'text' => 'Kimyasal stok alanlarında güvenli erişim'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent kutularında yüksek raflar'],
                    ['icon' => 'store', 'text' => 'Perakende DC üst raf replenishment']
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
            $this->command->info('✅ Variant: ' . $v['sku']);
        }
    }
}
