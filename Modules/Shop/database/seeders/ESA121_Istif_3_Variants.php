<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESA121_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ESA121')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ESA121'); return; }

        $variants = [
            [
                'sku' => 'ESA121-2513',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESA121 - 2.513 m Kaldırma (Duplex)',
                'short_description' => '2.513 m kaldırma yüksekliği ile düşük-orta raf seviyelerinde hızlı çevrim sağlayan, H-kesit direkli ve 24V/105Ah enerji paketli konfigürasyon. Dar koridor istifine uygundur.',
                'long_description' => '<section><h2>2.513 m kaldırmada verim ve denge</h2><p>Bu ESA121 varyantı, 2.513 m kaldırma yüksekliğini hedef alan, dar koridorlarda hız ve stabiliteyi dengeleyen bir yapı sunar. H-kesit direk tasarımı ve iki yan silindir, yük altında burulmayı azaltır; soft landing mekanizması kırılgan yükleri korur. 24V/105Ah AGM veya Li-ion batarya seçenekleri ve standart entegre şarj cihazı, vardiya içi kesintisiz akış için pratiklik sağlar.</p></section><section><h3>Teknik Odağımız</h3><p>Nominal kapasite 1200 kg (c=600 mm) seviyesinde korunur. Seyir hızları 4.0/4.5 km/s, kaldırma 0.15/0.24 m/s ve dönüş yarıçapı 1480 mm’dir. 1760 mm toplam uzunluk ve 826 mm genişlik, 1000×1200 çapraz ve 800×1200 uzunlamasına palet akışlarını destekler. DC sürüş kontrolü ve elektromanyetik fren, operatörün hassas komutlarını güvenli hale getirir.</p></section><section><h3>Sonuç</h3><p>2.5 metre bandında rafları olan depolar için ideal denge: çevik manevra, yeterli seviye ve düşük gürültü. Projenize özel konfigürasyon için 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticarette düşük raflı istif ve hat besleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de hızlı sipariş tamamlama'],
                    ['icon' => 'warehouse', 'text' => '3PL’de çapraz yükleme öncesi staging'],
                    ['icon' => 'snowflake', 'text' => 'Gıda soğuk odalarında sessiz operasyon'],
                    ['icon' => 'pills', 'text' => 'Kozmetik ve ilaçta kırılgan yük yönetimi'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresi WIP taşıma']
                ]
            ],
            [
                'sku' => 'ESA121-3313',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESA121 - 3.313 m Kaldırma (Duplex)',
                'short_description' => '3.313 m seviyesine uzanan, 1200 kg nominal kapasiteyi koruyan, hızlı kaldırma ve yumuşak iniş özellikleriyle raf içi operasyonları hızlandıran konfigürasyon.',
                'long_description' => '<section><h2>3.313 m’de esnek istif kapasitesi</h2><p>Bu konfigürasyon, orta seviye raf yapıları için 3.313 m kaldırma sağlar. H-kesit direk yapısı ve iki yan silindir stabiliteyi artırır; soft landing standarttır. 24V/105Ah enerji mimarisi AGM veya Li-ion olarak seçilebilir, entegre şarj cihazı ile altyapı yatırımı minimumdur.</p></section><section><h3>Teknik Odağımız</h3><p>4.0/4.5 km/s seyir ve 0.15/0.24 m/s kaldırma hızları çevrimi kısaltır. 1760 mm toplam uzunluk, 826 mm genişlik ve 1480 mm dönüş yarıçapı, dar koridorlarda güvenli manevra sağlar. 60/170/1150 mm çatal seti, tipik EUR paletleri için uyumludur.</p></section><section><h3>Sonuç</h3><p>Operasyon kapasitesini artırmak isteyen depolar için dengeli bir orta yükseklik çözümü. Detay için 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret raf içi replenishment'],
                    ['icon' => 'store', 'text' => 'Mağaza sevki öncesi palet hazırlığı'],
                    ['icon' => 'warehouse', 'text' => 'Kontrat lojistiğinde sipariş konsolidasyonu'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında varil istifi'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça stok alanları'],
                    ['icon' => 'industry', 'text' => 'WIP istasyonu besleme']
                ]
            ],
            [
                'sku' => 'ESA121-4113',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESA121 - 4.113 m Kaldırma (Duplex, Sınıfının Zirvesi)',
                'short_description' => '4.113 m maksimum seviyeye ulaşan bu konfigürasyon, sınıfının en yüksek istif kapasitesini H-kesit direk rijitliği ve güvenlik donanımlarıyla birleştirir.',
                'long_description' => '<section><h2>4.113 m: Sınıfın en yüksek istifi</h2><p>ESA121’in 4.113 m kaldırma yüksekliğine sahip varyantı, yüksek raflı depolarda dahi denge ve görüşten ödün vermez. H-kesit mast ve iki yandan silindir, yüksek elevasyonda salınımı sınırlar. Soft landing, kırılgan yükleri korur; opsiyonel üst seviye hız azaltma ek güvenlik katmanı sağlar.</p></section><section><h3>Teknik Odağımız</h3><p>1200 kg @ 600 mm kapasite korunur. 4.0/4.5 km/s seyir ve 0.15/0.24 m/s kaldırma hızlarıyla toplama-istif döngüleri hızlanır. 1760×826 mm kompakt ayak izi ve 1480 mm dönüş yarıçapı dar koridor uyumluluğu sunar. DC sürüş, elektromanyetik fren ve PU teker seti standarttır.</p></section><section><h3>Sonuç</h3><p>Yüksek raflı depolar için güvenli, sessiz ve hızlı istif çözümü. Projeniz için uygunluk ve uygulama detayları: 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı 3PL depolarında istif'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde üst seviye tampon alanlar'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk zincirde yüksek elevasyonlu raflar'],
                    ['icon' => 'pills', 'text' => 'İlaç paletlerinde dikkatli konumlama'],
                    ['icon' => 'building', 'text' => 'Çok katlı depo katlarında transfer'],
                    ['icon' => 'industry', 'text' => 'Üretim hatlarında ana depo entegrasyonu']
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

        $this->command->info('✅ Variants: ESA121 (İstif) 3 varyant güncellendi/eklendi');
    }
}
