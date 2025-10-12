<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL302X2_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL-302X2')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL-302X2'); return; }
        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "EFL-302X2-standart-mast-3000",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF EFL302X2 - 3000 mm Direk",
                    "short_description": "3000 mm (h3) direk konfigürasyonu ile raf yüksekliğine uygun çalışır; 2258 mm dönüş yarıçapı ve 11/12 km/h hız ile dar koridorlarda çevik, PMSM tahrik ve Li-ion enerji ile verimli operasyon sağlar.",
                    "long_description": "<section><h2>Operasyonunuza Uyan Direk Konfigürasyonu</h2><p>Çalışma yüksekliği ihtiyaçları değiştikçe forkliftinizin de buna ayak uydurması gerekir. Bu varyant, 3000 mm (h3) kapasitesiyle raf erişimini genişletirken, şasi ölçülerini ve dönüş kabiliyetini koruyarak koridor verimliliğini düşürmez. Serbest kaldırma seçeneği, alçak kapalı geçişlerde mast uzatmadan pallet kaldırma olanağı sunar.</p></section><section><h3>Teknik Etkiler</h3><p>Nominal 3000 kg kapasite 500 mm yük merkezinde korunur; yan kaydırıcı kullanımıyla tipik 100 kg net düşüm beklenir. Direk kapalı yüksekliği, serbest kaldırma miktarı ve mast açık ölçüsü raf ve tavan limitlerine göre belirlenmelidir. 80V/100Ah Li-ion paket, PMSM tahrik ile düşük tüketim ve uzun çevrim sunar; hidrolik direksiyon ve ≤74 dB(A) gürültü seviyesi operatör konforunu destekler.</p></section><section><h3>Uygulama Senaryoları</h3><p>Bu direk konfigürasyonu, kapı açıklıklarına takılmadan iç-dış saha geçişleri, mezanin altı operasyonlar ve yüksek raflı depolarda istikrarlı istifleme için idealdir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda yüksek raf erişimi ve hassas istifleme"
                        },
                        {
                            "icon": "building",
                            "text": "Mezanin altı kapalı alanlarda serbest kaldırma ile çalışma"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "Hızlı sipariş toplama alanlarında çevik dönüşler"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattı beslemede değişken yükseklik gereksinimleri"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Açık saha geçişlerinde suya dayanıklı şasi ile operasyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyasal depolarda kontrollü hız ve güvenli manevra"
                        }
                    ]
                },
                {
                    "sku": "EFL-302X2-serbest-mast-4800",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF EFL302X2 - 4800 mm Serbest Kaldırma",
                    "short_description": "4800 mm (h3) direk konfigürasyonu ile raf yüksekliğine uygun çalışır; 2258 mm dönüş yarıçapı ve 11/12 km/h hız ile dar koridorlarda çevik, PMSM tahrik ve Li-ion enerji ile verimli operasyon sağlar.",
                    "long_description": "<section><h2>Operasyonunuza Uyan Direk Konfigürasyonu</h2><p>Çalışma yüksekliği ihtiyaçları değiştikçe forkliftinizin de buna ayak uydurması gerekir. Bu varyant, 4800 mm (h3) kapasitesiyle raf erişimini genişletirken, şasi ölçülerini ve dönüş kabiliyetini koruyarak koridor verimliliğini düşürmez. Serbest kaldırma seçeneği, alçak kapalı geçişlerde mast uzatmadan pallet kaldırma olanağı sunar.</p></section><section><h3>Teknik Etkiler</h3><p>Nominal 3000 kg kapasite 500 mm yük merkezinde korunur; yan kaydırıcı kullanımıyla tipik 100 kg net düşüm beklenir. Direk kapalı yüksekliği, serbest kaldırma miktarı ve mast açık ölçüsü raf ve tavan limitlerine göre belirlenmelidir. 80V/100Ah Li-ion paket, PMSM tahrik ile düşük tüketim ve uzun çevrim sunar; hidrolik direksiyon ve ≤74 dB(A) gürültü seviyesi operatör konforunu destekler.</p></section><section><h3>Uygulama Senaryoları</h3><p>Bu direk konfigürasyonu, kapı açıklıklarına takılmadan iç-dış saha geçişleri, mezanin altı operasyonlar ve yüksek raflı depolarda istikrarlı istifleme için idealdir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda yüksek raf erişimi ve hassas istifleme"
                        },
                        {
                            "icon": "building",
                            "text": "Mezanin altı kapalı alanlarda serbest kaldırma ile çalışma"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "Hızlı sipariş toplama alanlarında çevik dönüşler"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattı beslemede değişken yükseklik gereksinimleri"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Açık saha geçişlerinde suya dayanıklı şasi ile operasyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyasal depolarda kontrollü hız ve güvenli manevra"
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
            $this->command->info("➕ Varyant: {$v['sku']}");
        }
    }
}
