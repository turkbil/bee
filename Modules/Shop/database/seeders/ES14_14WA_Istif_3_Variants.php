<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES14_14WA_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ES14-14WA')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ES14-14WA'); return; }

        $variants = [
            [
                'sku' => 'ES14-14WA-1150-550',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-14WA - 1150 mm Çatal / 550 mm Aralık',
                'short_description' => '1150 mm çatal ve 550 mm çatallar arası açıklıkla standart EUR paletlerde hız ve denge; kompakt gövde ile dar koridorda seri istif.',
                'long_description' => '<section><h2>1150 mm Çatal - 550 mm Açıklık: Hız ve Çeviklik</h2>
<p>Standart EUR palet ölçüsü için optimize edilen 1150 mm çatal, dar koridorlarda paletin fazla taşmamasını sağlar. 
550 mm çatallar arası açıklık, palet ayaklarına güvenli giriş sunarak hızlı pozisyon almayı mümkün kılar. 
İki kademeli indirme sayesinde üst raflarda ürün bırakma esnasında sarsıntı minimuma iner; ürün hasarı ve düzeltme manevraları azalır.</p></section>
<section><h3>Teknik Vurgu</h3>
<p>AC sürüş kontrolü ve elektromanyetik fren, 5.0/5.5 km/s hız aralığında akıcı hareket sağlar. 
600 mm yük merkezi ile nominal kapasite korunur. Poliüretan tekerlekler beton zeminde sessiz ve düşük yuvarlanma dirençlidir. 
3000 mm standart mast yapılandırmasıyla kapalı yükseklik yaklaşık 1970–2030 mm aralığındadır.</p></section>
<section><h3>Sonuç</h3><p>Günlük yoğun istifleme için ideal dengedir. Sorularınız için 0216 755 3 555.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "box-open",
                            "text": "EUR paletlerde hızlı kaldırma-indirme ve çapraz yükleme"
                        },
                        {
                            "icon": "warehouse",
                            "text": "3PL lokasyonlarında kısa mesafe palet transferi"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende toplama sonrası blok istif"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk oda giriş-çıkışında kontrollü yaklaşma"
                        },
                        {
                            "icon": "pills",
                            "text": "Hassas ürünlerde sarsıntısız indirme"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv yedek parça paletlerinde güvenli taşınma"
                        }
                    ]
JSON
                , true)
            ],
            [
                'sku' => 'ES14-14WA-1220-685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES14-14WA - 1220 mm Çatal / 685 mm Aralık',
                'short_description' => '1220 mm çatal ve 685 mm açıklık, geniş tabanlı paletlerde ekstra stabilite ve erişim; uzun yüklerde daha dengeli taşıma.',
                'long_description' => '<section><h2>1220 mm Çatal - 685 mm Açıklık: Uzun Yüklerde Stabilite</h2>
<p>1220 mm çatal, uzun kutular ve geniş izdüşümlü paletlerde daha güvenli temas sunar. 
685 mm çatallar arası açıklık, geniş tabanlı paletlerde yana devrilme momentini azaltır; operatör üst raflarda daha az düzeltme yapar.</p></section>
<section><h3>Teknik Vurgu</h3>
<p>AC sürüş mimarisi eğimli zeminlerde kontrollü hızlanma sağlar. 
24V 210–230Ah akü seçenekleriyle vardiya planı esnektir. İki kademeli indirme, hassas yerleştirmeyi standartlaştırır.</p></section>
<section><h3>Sonuç</h3><p>Geniş palet ve uzun yükler için ilk tercih. Detay için 0216 755 3 555.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "industry",
                            "text": "Geniş paletli yarı mamullerde dengeli istifleme"
                        },
                        {
                            "icon": "tshirt",
                            "text": "Tekstil askılı kasalarda uzun yük yerleşimi"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dış hat beslemede uzun palet taşıma"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya ambalajlarında geniş palet stabilitesi"
                        },
                        {
                            "icon": "couch",
                            "text": "Mobilya kutularında izdüşüm dengesi"
                        },
                        {
                            "icon": "hammer",
                            "text": "DIY ürünlerinde uzun boy malzeme sevki"
                        }
                    ]
JSON
                , true)
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
                'product_type' => 'physical',
                'condition' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }
        $this->command->info('✅ Variants eklendi/güncellendi: ES14-14WA');
    }
}
