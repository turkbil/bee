<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_ET_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT20-ET')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EPT20-ET'); return; }

        $variants = [
            [
                'sku' => 'EPT20-ET-560x1150',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20 ET - 560×1150 mm Çatal',
                'short_description' => '560 mm çatal aralığıyla EUR paletlerde optimum denge ve dar koridor çevikliği; 48V sistem, 2.0t kapasite ve 1550 mm dönüş yarıçapı ile şehir depolarında verimli, sessiz ve güvenli çalışır.',
                'long_description' => '<section><h2>560×1150: EUR paletin standart çözümü</h2><p>560 mm çatal mesafesi, EUR/EPAL paletlerde yük ağırlık merkezini ideal bölgede tutarak yürüme ve manevra stabilitesini artırır. İXTİF EPT20 ET’nin 48V mimarisi ve fırçasız DC motoru, dar koridorlarda kesintisiz akış sağlar. 80 mm alçak giriş ve 140 mm kaldırma, palet ceplerine yumuşak yaklaşım sunar.</p></section><section><h3>Uygulama Faydaları</h3><p>Şehir içi depo, süpermarket arka alanı ve 3PL hat beslemesinde 4/5.5 km/s yürüyüş hızlarıyla çevrim süresi düşer. 1550 mm dönüş yarıçapı ve 1685 mm gövde boyu, cross-dock ve rampalarda çevik manevra sağlar. Opsiyonel döner yük tekeri yüksek eşikleri aşmayı kolaylaştırır.</p></section><section><h3>Neden 560 mm?</h3><p>Standart EUR yüklerinde palet ceplerine tam oturum ve dar raf koridorlarında minimum sürtünme. Daha geniş 685 mm’ye göre raf arası temas riski azalır, dar kapılardan geçiş kolaylaşır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'EUR paletli raf koridorlarında standart malzeme akışı'],
                    ['icon' => 'store', 'text' => 'Süpermarket arka depolarında dar alan manevrası'],
                    ['icon' => 'cart-shopping', 'text' => 'Cross-dock merkezlerinde hattın hızlı beslenmesi'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve hat içi lojistik'],
                    ['icon' => 'road', 'text' => 'Yol tümsekleri ve eşiklerde güvenli geçiş'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça raf sistemlerine uygun çözüm']
                ]
            ],
            [
                'sku' => 'EPT20-ET-685x1150',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20 ET - 685×1150 mm Çatal',
                'short_description' => '685 mm çatal aralığı geniş paletlerde daha geniş taban ve yük stabilitesi sunar; 48V sistem ve şok emici şasi ile bozuk zeminde titreşimi azaltarak güvenli vardiya sağlar.',
                'long_description' => '<section><h2>685×1150: Geniş taban, yüksek stabilite</h2><p>685 mm çatal aralığı, geniş gövdeli paletlerde yan stabiliteyi artırır. İXTİF EPT20 ET’nin silindirik şasi mimarisi ağırlığı alt kısımda toplar; alçak ağırlık merkezi dar dönüşlerde bile kontrollü sürüş sunar. DC fırçasız motor düşük bakım maliyeti ile TCO’yu düşürür.</p></section><section><h3>Uygulama Faydaları</h3><p>İçecek kasaları, beyaz eşya ve hacimli kolilerde taban yayılımı sayesinde devrilme momenti düşer. 48V-5A dahili şarj ve süre göstergeli indikatör, vardiya içi fırsat şarjını destekler.</p></section><section><h3>Neden 685 mm?</h3><p>Geniş paletlerde daha iyi yan destek, uzun ve üst üste istiflenmiş yüklerde kontrollü ilerleme. 560 mm’ye göre orta-ofset yüklerde sapma azalır.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve fıçı paletlerinin elleçlenmesi'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya ve büyük hacimli koli transferi'],
                    ['icon' => 'couch', 'text' => 'Mobilya ve ev dekorasyonu ürün akışı'],
                    ['icon' => 'warehouse', 'text' => 'Geniş koridorlu depolarda toplu sevkiyat'],
                    ['icon' => 'industry', 'text' => 'Ağır yan destek gerektiren üretim istasyonları'],
                    ['icon' => 'cart-shopping', 'text' => 'Toplu sipariş hazırlama ve çapraz sevkiyat']
                ]
            ],
            [
                'sku' => 'EPT20-ET-475x1150',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPT20 ET - 475×1150 mm Çatal',
                'short_description' => '475 mm çatal aralığı dar palet cepleri ve özel kutu paletlerde giriş kolaylığı sağlar; 2.0 ton kapasiteyi korurken dar kapılardan geçiş ve park esnekliği sunar.',
                'long_description' => '<section><h2>475×1150: Dar paletlere özel hassas yaklaşım</h2><p>475 mm çatal aralığı, dar cepli özel paletlerde giriş ve çıkışta sürtünmeyi azaltır. 80 mm alçaltılmış yükseklik ve 140 mm kaldırma ile düşük profilli paletler için uygundur. 0.75 kW sürüş ve 0.84 kW kaldırma motorları, düşük ağırlık (220 kg) sayesinde seri manevra sağlar.</p></section><section><h3>Uygulama Faydaları</h3><p>Kısıtlı kapı açıklıkları, dar yükleme iskeleleri ve mini depo alanlarında yüksek çeviklik. Opsiyonel döner yük tekeri ile yüksek eşiklerde kesintisiz akış.</p></section><section><h3>Neden 475 mm?</h3><p>Özel üretim tezgâh içi paletler ve dar cepli ambalajlarda giriş kolaylığı. 560/685 mm seçeneklerine göre raf arası çarpma riski daha düşüktür.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Özel dar cepli paletlerde hat besleme'],
                    ['icon' => 'hammer', 'text' => 'DIY ve yapı market arkası dar koridorlar'],
                    ['icon' => 'pills', 'text' => 'Medikal kutu paletlerinde dikkatli elleçleme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalajlarda güvenli taşıma'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve matbaa yarı mamul akışı'],
                    ['icon' => 'seedling', 'text' => 'Bahçe/seracılık dar geçit uygulamaları']
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
        $this->command->info('✅ Variants oluşturuldu: 3 adet');
    }
}
