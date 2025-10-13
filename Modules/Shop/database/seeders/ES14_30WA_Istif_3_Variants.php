<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES14_30WA_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'ES14-30WA')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı (ES14-30WA)');
            return;
        }

        $variants = [
            [
                'sku' => 'ES14-30WA-920',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-30WA 920 mm Çatal',
                'short_description' => '920 mm çatal, kısa paletlerde çevik giriş-çıkış ve dar koridorlarda minimum overhang ile hızlı istif sağlar. Straddle bacak açıklığı sayesinde farklı taban genişlikleri güvenle kavranır.',
                'body' => '<section><h3>920 mm Çatal: Kısa Palet Uzmanı</h3><p>920 mm çatal konfigürasyonu, özellikle kısa EUR ve iç lojistik paletlerinde raf içi manevrayı hızlandırır. 1987 mm toplam uzunluk ve 1545 mm dönüş yarıçapı ile koridor içi dönüşler kısalırken, yükün ağırlık merkezi makineye daha yakın konumlanır. Bu, hem dengeyi güçlendirir hem de düşük hasar riski sağlar.</p></section><section><h3>Teknik Uyum</h3><p>ES14-30WA’nın oransal kaldırma sistemi 0.127 m/s yüklü kaldırma hızıyla hassas yerleştirme yaparken, poliüretan teker seti sessiz ve titreşimsiz bir sürüş sunar. 24V/210Ah akü gün boyu stabil güç sağlar; Li-ion opsiyon fırsat şarjlarını destekler.</p></section><section><h3>Kullanım Örnekleri</h3><p>Kısa paletli üretim hücreleri, e-ticaret iade alanları ve paket ayırma hatlarında hız ve güven sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-iade alanlarında kısa paletlerin hızlı yer değiştirmesi'],
                    ['icon' => 'warehouse', 'text' => 'Dar koridorlu toplama raflarında çevik dönüş'],
                    ['icon' => 'store', 'text' => 'Perakende mağaza arkası kompakt stok alanları'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP tepsilerinin beslenmesi'],
                    ['icon' => 'car', 'text' => 'Otomotiv küçük parça kasalarının istifi'],
                    ['icon' => 'pills', 'text' => 'İlaç kolilerinde hassas istif ve yumuşak indirme']
                ]
            ],
            [
                'sku' => 'ES14-30WA-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-30WA 1070 mm Çatal',
                'short_description' => '1070 mm çatal, genel amaçlı istif operasyonlarının standart seçeneğidir. 200–760 mm çatallar arası ayar ve ayarlanabilir bacaklar sayesinde geniş ürün yelpazesinde çok yönlü performans sunar.',
                'body' => '<section><h3>1070 mm Çatal: Çok Yönlü Standart</h3><p>1070 mm, depo operasyonlarında en yaygın palet boylarıyla uyumlu çalışır. ES14-30WA’nın 5.5/6.0 km/s seyir hızları ve kaplumbağa modu, kalabalık alanlarda güvenli hız yönetimi sağlar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>0.127/0.23 m/s kaldırma hızları ve elektromanyetik fren, güvenli ve kontrollü istif akışı sunar. PU tekerlekler sessiz çalışır; AC sürüş mimarisi bakım ihtiyacını düşürür.</p></section><section><h3>Kullanım Örnekleri</h3><p>Genel depo, 3PL ve perakende dağıtım operasyonlarında standartlaştırılmış süreçler için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL çapraz sevkiyat ve stok besleme'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment istasyonları arasında palet transferi'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf içi replenishment'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış görevleri'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalaj paletlerinin istifi'],
                    ['icon' => 'industry', 'text' => 'Montaj öncesi yarı mamul istif']
                ]
            ],
            [
                'sku' => 'ES14-30WA-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-30WA 1150 mm Çatal',
                'short_description' => '1150 mm çatal, EUR paletlerde dengeli giriş ve çıkış sağlar. Uzun kol mesafesi, raf içi yerleştirmede dengeyi korurken, oransal kaldırma raf hasarını azaltır ve ürün bütünlüğünü destekler.',
                'body' => '<section><h3>1150 mm Çatal: EUR Palet Ustası</h3><p>EUR paletlerde yaygın kullanılan 1150 mm uzunluk, yük merkezine uyumlu ağırlık dağılımı sunar. 4115 mm açık mast yüksekliği ile üst seviye raflara erişim, titreşim kontrolü ve hassas duruş birlikte sağlanır.</p></section><section><h3>Performans</h3><p>3.0 kW kaldırma motoru büyük yüklerde stabil hız sağlarken, 24V enerji altyapısı gün boyu kararlı performans verir. Elektromanyetik fren, eğimde geri kaymayı önlemeye yardımcıdır.</p></section><section><h3>Kullanım Örnekleri</h3><p>Gıda, kozmetik ve elektronik depolarda standart palet akışı ile hatasız istifleme sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında EUR palet stok yönetimi'],
                    ['icon' => 'pills', 'text' => 'Kozmetik/ilaç kolilerinde yumuşak bırakma'],
                    ['icon' => 'microchip', 'text' => 'Elektronik komponent paletleri'],
                    ['icon' => 'warehouse', 'text' => 'Dağıtım merkezinde raf içi istif'],
                    ['icon' => 'box-open', 'text' => 'Sipariş toplama sonrası konsolidasyon'],
                    ['icon' => 'industry', 'text' => 'İmalat sonrası bitmiş ürün rafları']
                ]
            ],
            [
                'sku' => 'ES14-30WA-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-30WA 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yüklerde overhang’i azaltarak stabil giriş sağlar. Ayarlanabilir bacak yapısı geniş tabanlı yükleri destekler; dar koridorlarda kaplumbağa modu güvenli manevra sunar.',
                'body' => '<section><h3>1220 mm Çatal: Uzun Yük Dengesini Artırır</h3><p>Uzun yüklerde çatalların palete temas yüzeyi genişler, denge iyileşir. ES14-30WA’nın 1987 mm toplam uzunluğu, 2460 mm koridorlarda kontrollü dönüşle birleşir.</p></section><section><h3>Teknik Uygunluk</h3><p>0.26/0.20 m/s indirme hızları, ağır ve hacimli yüklerin güvenli bırakılmasını sağlar. PU tekerler titreşimi sönümler; AC sürüş mimarisi enerji verimini yükseltir.</p></section><section><h3>Kullanım Örnekleri</h3><p>Mobilya, beyaz eşya ve ambalaj sektöründe uzun kolili yüklerin raflanmasında verim sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'building', 'text' => 'Mobilya depolarında uzun yük raflama'],
                    ['icon' => 'cart-shopping', 'text' => 'Beyaz eşya lojistiğinde paletli ev aletleri'],
                    ['icon' => 'warehouse', 'text' => 'Konsolidasyon alanlarında geçici stoklama'],
                    ['icon' => 'box-open', 'text' => 'Ambalaj rulolarının istifi'],
                    ['icon' => 'car', 'text' => 'Otomotiv sac ve panel yükleri'],
                    ['icon' => 'industry', 'text' => 'Genel imalatta bitmiş ürün rafları']
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
        }

        $this->command->info('✅ Variants oluşturuldu: ES14-30WA (920/1070/1150/1220)');
    }
}
