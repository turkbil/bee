<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EST122_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EST122')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EST122'); return; }

        $variants = [
            [
                'sku' => 'EST122-570',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EST122 - 570 mm Çatallar Arası',
                'short_description' => 'Standart 570 mm çatallar arası ölçüsü, EUR palet ağırlıklı depolarda evrensel uyum ve hızlı yaklaşım sağlar. Kompakt gövde ve kaplumbağa modu dar raf aralarında güvenli manevra ve hassas raf yerleştirme sunar.',
                'long_description' => '<section><h2>570 mm: EUR Palet Odaklı Evrensel Uyum</h2><p>İXTİF EST122’nin 570 mm çatallar arası ölçüsü, Avrupa standart palet (EUR) akışının yoğun olduğu depolarda evrensel uyum sunar. Operatör, offset tiller sayesinde palet uçlarını net görür; kaplumbağa butonu yaklaşım hızını sınırlayarak paletin yanaklarına zarar verme riskini azaltır. 792 mm gövde genişliği ve 1458 mm dönüş yarıçapı, raf aralarındaki hareketleri akıcı hale getirir. 24V 85Ah enerji paketi, entegre şarj ile mola aralarında takviye edilerek yüksek erişilebilirlik sağlar. Rijit mast kiriş yapısı, yük altında direk sapmasını minimumda tutarak, 2430 mm nominal kaldırma yüksekliğinde düzgün konumlandırma yapar.</p></section><section><h3>Teknik Çerçeve</h3><p>60/170/1150 mm çatal ölçüleri, tipik kutu ve koli boyutlarında geniş temas yüzeyi sunar; 680 mm taşıyıcı genişliği, palet kanatlarına uygun giriş yapısını destekler. Sürüş hızları 4.2/4.5 km/s, kaldırma hızları 0.10/0.14 m/s seviyesindedir. Elektromanyetik fren ve DC sürüş kontrolü basit, güvenilir ve öngörülebilir bir kullanıcı deneyimi sağlar. Poliüretan tekerler düşük yuvarlanma direnci ile zemin aşınmasını sınırlar. Bu varyant, RFID, e-ticaret sortlama hatları ve iade süreçlerinde standart paletlerle yoğun çalışan operasyonlar için optimum denge sunar.</p></section><section><h3>Sonuç</h3><p>Standart 570 mm ölçüsüyle depodaki çoğu palete ilk andan uyum sağlayın; raf başına yaklaşım hızınızı artırın, hataları azaltın.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR palet iade ve stok dönüş işlemleri'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de satışa hazırlık istiflemeleri'],
                    ['icon' => 'warehouse', 'text' => '3PL konsolidasyon alanlarında ara istasyon'],
                    ['icon' => 'industry', 'text' => 'Hafif montaj hücrelerinde WIP palet akışı'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG toplama sonu raf yerleştirme'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve etiket rulosu paletleme']
                ]
            ],
            [
                'sku' => 'EST122-685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EST122 - 685 mm Çatallar Arası',
                'short_description' => '685 mm çatallar arası, geniş tabanlı kasalar ve özel konteyner paletlerinde giriş konforu sağlar. Daha geniş ayak açıklığı, dengesiz yüklerde hizalamayı kolaylaştırır ve istif sırasında operatör güvenini artırır.',
                'long_description' => '<section><h2>685 mm: Geniş Palet ve Kasalarda Rahat Giriş</h2><p>Geniş konteyner tabanları ve özel ölçülü kasalarla çalışan depolar için 685 mm çatallar arası ölçüsü kritik bir avantaj sağlar. Yük yüzeyine daha geniş açıyla yaklaşım, palet kenarı toleranslarını telafi eder. EST122’nin rijit mast yapısı ve yanal tahrik kurgusu, bu genişlikte dahi yük altında sağlam bir geometri sunar. Offset tiller ile geniş tabanlı paletlerde dahi uç noktayı görmek kolaylaşır; kaplumbağa butonu hassas yerleştirmeye yardımcı olur.</p></section><section><h3>Teknik Çerçeve</h3><p>Standart 60/170/1150 mm çatal kesiti, geniş aralıkla birlikte taşıyıcı genişliğini 680 mm seviyesinde korur; bu sayede gövde ölçüleri değişmeden kalır. 24V (2×12V) 85Ah güç paketi ve verimli hidrolik pompa, 0.10/0.14 m/s kaldırma hızlarını korur. Poliüretan teker seti, geniş paletlerde artan yük dağılımını zemin dostu şekilde taşır. 1713 mm toplam uzunluk ve 1458 mm dönüş yarıçapı, geniş aralığa rağmen raf önlerinde çevik kalmanızı sağlar.</p></section><section><h3>Sonuç</h3><p>Geniş tabanlı palet ve kasalarla çalışan süreçlerde giriş hatalarını azaltın; elle düzeltme ihtiyacını en aza indirin.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Geniş konteyner paletli ürünlerin raflanması'],
                    ['icon' => 'car', 'text' => 'Otomotiv kasalarında geniş tabanlı yedek parçalar'],
                    ['icon' => 'flask', 'text' => 'Kimyasal varil tepsileri ve kasaları'],
                    ['icon' => 'couch', 'text' => 'Mobilya parçalarının ara stok alanları'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve gıda konteynerleri'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil arabaları ve özel taşıma kasaları']
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
            $this->command->info('✅ Varyant: ' . $v['sku']);
        }
    }
}
