<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSC082_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'RSC082')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: RSC082'); return; }
        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "RSC082-2500",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF RSC082 - 2500 mm Direk",
                    "short_description": "2500 mm kaldırma ile alçak tavanlı alanlarda optimum erişim ve 800 kg kapasiteyi koruyan kompakt çözüm. Dar koridorlarda hassas manevra sağlar.",
                    "long_description": "<section><h2>2500 mm: Alçak Tavanlar için Dengeli Performans</h2>\n<p>2500 mm kaldırma yüksekliği; düşük tavanlı raf hatlarında, yükseklik kısıtı olan kapı geçişlerinde ve mezanin altında ideal çözümdür. 800 kg kapasite ve 400 mm yük merkezi korunurken, 2061 mm civarında mast kapalı yüksekliği ile alan verimliliği artar. Oransal kaldırma, palet seviyesine yaklaşırken hızın incelmesini sağlar.</p>\n</section><section><h3>Teknik Odak</h3>\n<p>AC sürüş (1.6 kW) ve 2.2 kW kaldırma motoru; 5.5/6 km/sa sürüş ve 0.13/0.20 m/sn kaldırma hızlarını destekler. 1250 mm dönüş yarıçapı dar koridorlarda rahat manevra sunar. PU tekerlekler sessiz ve zemin dostudur.</p>\n</section><section><h3>Kullanım Sonu</h3>\n<p>Kapalı depo geçişlerinde, mezanin altı raflarda ve alçak tavanlı üretim hücrelerinde güvenli erişim sağlar.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Mezanin altı raf bölgelerinde istifleme"
                        },
                        {
                            "icon": "store",
                            "text": "Mağaza arka oda stok alanlarında malzeme kaldırma"
                        },
                        {
                            "icon": "industry",
                            "text": "Alçak tavanlı üretim hücrelerinde WIP besleme"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "FMCG mikrodepo alanlarında hızlı çevrim"
                        },
                        {
                            "icon": "box-open",
                            "text": "Koli istasyonu giriş-çıkışlarında kısa transfer"
                        },
                        {
                            "icon": "car",
                            "text": "Servis atölyelerinde parça raflarına erişim"
                        }
                    ]
                },
                {
                    "sku": "RSC082-3000",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF RSC082 - 3000 mm Direk",
                    "short_description": "3000 mm standart direk ile çok seviyeli raflarda güvenli erişim; 24V/210Ah akü ve elektronik direksiyonla dengeli sürüş.",
                    "long_description": "<section><h2>3000 mm: Standart Çok Yönlülük</h2>\n<p>Standart 3000 mm direk, çoğu depo uygulaması için ideal erişim sunar. Kapalı yükseklik ve açık yükseklik değerleri dengeli tutulurken, operatör oransal kaldırma ile raf girişlerini nazikçe tamamlar. 800 kg kapasite, farklı boy paletlerde güvenilir kaldırma sağlar.</p>\n</section><section><h3>Teknik Odak</h3>\n<p>1.6 kW AC sürüş ve 2.2 kW kaldırma motoru; 5.5/6 km/sa yürüyüş ve 0.13/0.20 m/sn kaldırma hızlarına imkan verir. 1250 mm dönüş yarıçapı ve 2404 mm uzunluk dar koridorlara uygundur.</p>\n</section><section><h3>Kullanım Sonu</h3>\n<p>Genel amaçlı depolama, 3PL raf sistemleri ve perakende DC’lerde optimum çözüm sunar.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "3PL depo rafları arasında günlük istifleme"
                        },
                        {
                            "icon": "box-open",
                            "text": "E-ticaret inbound/outbound akışlarında palet alma-bırakma"
                        },
                        {
                            "icon": "pills",
                            "text": "İlaç depolarında hassas kutu paletleri"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk oda ön alanında hızlı erişim"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyasal ambalaj paletlerinde güvenli taşıma"
                        },
                        {
                            "icon": "tshirt",
                            "text": "Tekstil kolilerinde kat seviyesi besleme"
                        }
                    ]
                },
                {
                    "sku": "RSC082-3300",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF RSC082 - 3300 mm Direk",
                    "short_description": "3300 mm’ye uzayan direk ile daha yüksek raflara erişim; kompakt şasi ve 1250 mm dönüşle dar alanlarda çeviklik.",
                    "long_description": "<section><h2>3300 mm: Yüksek Raf Erişimi</h2>\n<p>3300 mm kaldırma seçeneği, yüksek raflı alanlarda erişimi genişletir. Mast geometrisi ve şasi dengesi, yük altında yatma eğilimini minimumda tutar. Oransal kaldırma, paletin raf içine yerleşmesinde titreşimi azaltır.</p>\n</section><section><h3>Teknik Odak</h3>\n<p>AC tahrik verimliliği, elektromanyetik fren güvenliği ve PU teker sessizliği birleşerek yoğun vardiyalarda sürdürülebilir performans üretir. 24V/210Ah akü ile planlı şarj ve hızlı vardiya geçişleri desteklenir.</p>\n</section><section><h3>Kullanım Sonu</h3>\n<p>Yüksek raflı e-ticaret depoları ve yedek parça merkezleri için idealdir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Yüksek raflı bölgelerde istifleme"
                        },
                        {
                            "icon": "briefcase",
                            "text": "B2B toptancı alanlarında stok yükseltme"
                        },
                        {
                            "icon": "building",
                            "text": "Tesis back-of-house alanlarında besleme"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "Yoğun SKU çeşitliliğinde kat seviyesi erişim"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv raflarında uzun referanslı paletler"
                        },
                        {
                            "icon": "box-open",
                            "text": "Koli konsolidasyon alanlarında üst seviye yerleşim"
                        }
                    ]
                }
            ]
JSON
        , true);

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
            $this->command->info("✅ Varyant güncellendi/eklendi: " . $v['sku']);
        }
    }
}
