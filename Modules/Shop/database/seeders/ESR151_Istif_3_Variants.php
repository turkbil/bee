<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESR151_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ESR151')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ESR151'); return; }

        $variants = [
            [
                'sku' => 'ESR151-2516',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESR151 - 2516 mm Direk',
                'short_description' => '2516 mm kaldırma yüksekliğiyle düşük-orta raf hatlarına uygun, kompakt depo düzenlerinde hızlı yaklaşma ve güvenli istif sağlayan sürücülü konfigürasyon.',
                'long_description' => '<section><h3>2516 mm: Hızlı Akış İçin İdeal</h3><p>Bu varyant, 2516 mm direk yüksekliği ile düşük-orta raf yapılarında hız ve çevikliği öne çıkarır. 1488 mm dönüş yarıçapı ve 850 mm gövde genişliği dar koridorları avantaja çevirir. 24V/15A entegre şarj ve 2×12V/105Ah akü düzeni, gün içi kısa şarj molalarıyla vardiya ritmini korur. Çatallar 720 mm üzerine çıktığında otomatik düşük hız modu devreye girer, mast üzerindeki metal ağ olası düşmelere karşı operatörü korur.</p></section><section><h3>Teknik Denge</h3><p>0.75 kW tahrik ve 2.2 kW kaldırma motoru, 4.0/4.5 km/s sürüş hızlarıyla dengeli bir tempo sunar. 60/170/1150 mm çatal ölçüleri EUR paletlerle tam uyumludur. PU tekerler sessiz ve titreşimsiz taşıma sağlar; elektromanyetik fren, hassas duruş ve rampa kontrolünde güven verir.</p></section><section><h3>Kapanış</h3><p>Yoğun toplama ve replenishment hatlarında verimlilik arayan ekipler için önerilir. Detay ve fiyatlandırma: 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Kısa raflı 3PL koridorlarında hızlı palet istifi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret ayrıştırma alanında ara stok yönetimi'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf önü replenishment işleri'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG tampon alan beslemesi'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresi girişlerinde WIP akışı'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış noktalarında kısa mesafe taşıma']
                ]
            ],
            [
                'sku' => 'ESR151-3016',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESR151 - 3016 mm Direk',
                'short_description' => '3016 mm maksimum kaldırma ile orta yükseklik raflarına erişim; otomatik düşük hız ve PU tekerlerle dengeli, sessiz ve güvenli istif performansı.',
                'long_description' => '<section><h3>3016 mm: Denge ve Erişim</h3><p>ESR151-3016, daha yüksek raf hatlarına ulaşırken stabiliteyi koruyacak şekilde ölçeklenmiştir. 1215 mm dingil mesafesi ve optimize teker izleri, yük altında ağırlık dağılımını dengeler. Entegre 24V/15A şarj ile vardiya içi planlama esnektir.</p></section><section><h3>Operasyonel Avantaj</h3><p>0.10/0.14 m/s kaldırma hızları ve 4.0/4.5 km/s sürüş hızları, operasyon akışını ritmik tutar. 720 mm üstünde otomatik yavaşlama, hassas konumlandırmayı kolaylaştırır; elektromanyetik fren güvenli duruş sağlar.</p></section><section><h3>Sonuç</h3><p>Genel depo uygulamalarında tek makine ile daha fazla lokasyonu kapsamak isteyen işletmeler için dengeli bir seçenektir. Bilgi için: 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Orta yükseklikli raflarda standart palet istifi'],
                    ['icon' => 'box-open', 'text' => 'Sipariş konsolidasyon alanı besleme'],
                    ['icon' => 'store', 'text' => 'Zincir mağaza dönüş paleti yönetimi'],
                    ['icon' => 'industry', 'text' => 'Hammadde ve yarı mamul ara stok istasyonu'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı yükleme tampon alanı düzeni'],
                    ['icon' => 'flask', 'text' => 'Kimyasal paket depolama raflarına erişim']
                ]
            ],
            [
                'sku' => 'ESR151-3316',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ESR151 - 3316 mm Direk',
                'short_description' => '3316 mm direk ile daha yüksek raf senaryoları; stabilite testi onaylı şasi ve otomatik hız kısıtlama ile güvenli, kontrollü istifleme.',
                'long_description' => '<section><h3>3316 mm: Yükseğe Çıkan Esneklik</h3><p>Bu konfigürasyon, maksimum 3316 mm direk ile daha yüksek raf hatlarına erişim sağlar. Mast arkasındaki metal ağ ve 720 mm üstü otomatik düşük hız sayesinde, yüksek noktalarda dahi güven ve kontrol korunur.</p></section><section><h3>Performans Profili</h3><p>2.2 kW kaldırma motoru ve DC sürüş kontrolü istifleme sırasında akıcı hızlanma sunar. PU tekerler ve mekanik direksiyon, bakım basitliğini ve sürüş hissini iyileştirir.</p></section><section><h3>Kapanış</h3><p>Yüksek raflı hatları olan depolar için uygundur; alan verimliliğini artırır. Detay için 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raf hatlarında palet istifi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret üst raf lokasyonlarına erişim'],
                    ['icon' => 'industry', 'text' => 'Bitmiş ürün depolama alanında katmanlı istif'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depo yüksek raf uygulamaları'],
                    ['icon' => 'pills', 'text' => 'İlaç paletlerinde yüksek raf güvenli konumlandırma'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent rafları yüksek lokasyonlar']
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
            $this->command->info("✅ Varyant eklendi: {$v['sku']}");
        }
    }
}
