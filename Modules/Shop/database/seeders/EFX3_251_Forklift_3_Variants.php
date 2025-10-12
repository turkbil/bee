<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX3_251_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFX3-251')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFX3-251'); return; }
        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "EFX3-251-1070",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF EFX3 251 - 1070 mm Çatal",
                    "short_description": "1070 mm çatal, standart EUR paletlerde yüksek denge ve kompakt dönüş sağlar; dar koridorlarda çeviklik ve hızlı yerleştirme hedefleyen operasyonlar için optimize edildi.",
                    "long_description": "<section>\n    <h3>Standart palet, maksimum çeviklik</h3>\n    <p>1070 mm çatal uzunluğu, Avrupa palet ölçülerine uyumlu konumlandırma ile dar raf koridorlarında minimum düzeltme manevrası sağlar. \n    2217 mm dönüş yarıçapı ile birleştiğinde, giriş-çıkış döngülerinde verimlilik artar.</p>\n</section>\n<section>\n    <h3>Teknik uyum</h3>\n    <p>Çatal kesiti, yük taşıma yüzeyinin rijitliği ve denge noktası korunarak yüksek istif kalitesi sunar. Kısa çatal, \n    özellikle kısa boylu yüklerin araç içine hizalanmasında avantaj sağlar.</p>\n</section>\n<section>\n    <h3>Uygulama sonucu</h3>\n    <p>Hızlı çevrimli DC uygulamalarında ürün dönüşünü hızlandırır, hat içi beslemelerde alan kazandırır.</p>\n</section>",
                    "use_cases": [
                        {
                            "icon": "box-open",
                            "text": "E‑ticaret hat çıkışında hızlı palet tanzimi"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dar koridor raf arası konumlandırma"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de çabuk replenishment"
                        },
                        {
                            "icon": "car",
                            "text": "Araç içi hızlı yükleme/boşaltma"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hücresinde WIP taşıma"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyada kısa palet konteynerleri"
                        }
                    ]
                },
                {
                    "sku": "EFX3-251-1220",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF EFX3 251 - 1220 mm Çatal",
                    "short_description": "1220 mm çatal, uzun veya dengesiz yüklerde daha geniş temas yüzeyi sunar; açık alan stok sahalarında ve karışık palet boylarında esnek kullanım sağlar.",
                    "long_description": "<section>\n    <h3>Uzun yüklerde güven</h3>\n    <p>1220 mm çatal, palet dışına taşan yüklerde ağırlık merkezini daha güvenli karşılayarak sapmaları azaltır. \n    Açık saha stokta yön değişimleri daha kontrollüdür.</p>\n</section>\n<section>\n    <h3>Teknik kazanımlar</h3>\n    <p>Daha uzun temas yüzeyi, çatal uçlarındaki noktasal gerilimleri düşürür; hassas ambalajlı ürünlerde deformasyon riskini azaltır.</p>\n</section>\n<section>\n    <h3>Uygulama sonucu</h3>\n    <p>Karışık palet boylarında standart dışı siparişler için esneklik sağlar; sevkiyat planlamasını kolaylaştırır.</p>\n</section>",
                    "use_cases": [
                        {
                            "icon": "box-open",
                            "text": "Karışık palet boylarında toplama alanı"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Açık stok sahasında uzun yükler"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk zincirde geniş paletler"
                        },
                        {
                            "icon": "pills",
                            "text": "İlaçta hassas ambalaj destekli taşıma"
                        },
                        {
                            "icon": "industry",
                            "text": "Ağır sanayide kalıp ve aparatlar"
                        },
                        {
                            "icon": "couch",
                            "text": "Mobilya sektöründe geniş paketler"
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
                'published_at' => now()
            ]);
            $this->command->info("✅ Varyant güncellendi: {$v['sku']}");
        }
    }
}
