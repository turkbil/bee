<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CPD20FVL_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'CPD20FVL')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (CPD20FVL)'); return; }

        $variants = [
            
            [
                'sku' => 'CPD20FVL-920',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD20FVL - 920 mm Çatal Forklift',
                'short_description' => '920 mm çatal uzunluğu ile farklı palet ve kutu ebatlarında daha iyi denge ve yerleştirme kontrolü sağlar; dar koridorlarda palet giriş-çıkışlarını hızlandırır.',
                'long_description' => '<section><h2>920 mm Çatal: Esneklik ve erişim</h2><p>Operasyonlarda palet ölçülerinin ve rampa koşullarının farklılaştığı alanlarda 920 mm çatal, yükün ağırlık merkezine daha doğru erişim ve daha güvenli kavrama sağlar. Sürücüler raf derinliğine yaklaşırken palet ucunu daha rahat hizalar; iade ve çapraz sevkiyat gibi hızlı değişen akışlarda süreklilik artar.</p></section><section><h3>Teknik denge</h3><p>Uygun çatal boyu, yükün ön aksa binen momentini optimize eder. Palet çıkıntılarında şasiye çarpmayı azaltır, raf ayağına yaklaşırken daha öngörülebilir duruş sağlar. Farklı ataşman ve lastik seçenekleriyle birlikte, ürün bütün gün boyunca dengeli ve hızlı çalışır.</p></section><section><h3>Operasyonel sonuç</h3><p>Doğru konfigürasyonla yerden kazanım, daha az manevra ve daha düşük hasar oranı elde edilir. Entegre şarj ve Li‑Ion mimari, vardiya arasında fırsat şarjı ile süreklilik sağlar.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "box-open",
                            "text": "Koli yoğun hatlarda palet içi yerleşimi hızlandırır"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda raf derinliğine güvenli erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de farklı ambalaj boylarına uyum"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattında WIP transferlerinde stabil kavrama"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv kasalarında çıkıntılı yüklerde hassas pozisyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya varillerinde merkez kaçmayı azaltan denge"
                        }
                    ]
JSON
                , true)
            ]
        ,
            
            [
                'sku' => 'CPD20FVL-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD20FVL - 1070 mm Çatal Forklift',
                'short_description' => '1070 mm çatal uzunluğu ile farklı palet ve kutu ebatlarında daha iyi denge ve yerleştirme kontrolü sağlar; dar koridorlarda palet giriş-çıkışlarını hızlandırır.',
                'long_description' => '<section><h2>1070 mm Çatal: Esneklik ve erişim</h2><p>Operasyonlarda palet ölçülerinin ve rampa koşullarının farklılaştığı alanlarda 1070 mm çatal, yükün ağırlık merkezine daha doğru erişim ve daha güvenli kavrama sağlar. Sürücüler raf derinliğine yaklaşırken palet ucunu daha rahat hizalar; iade ve çapraz sevkiyat gibi hızlı değişen akışlarda süreklilik artar.</p></section><section><h3>Teknik denge</h3><p>Uygun çatal boyu, yükün ön aksa binen momentini optimize eder. Palet çıkıntılarında şasiye çarpmayı azaltır, raf ayağına yaklaşırken daha öngörülebilir duruş sağlar. Farklı ataşman ve lastik seçenekleriyle birlikte, ürün bütün gün boyunca dengeli ve hızlı çalışır.</p></section><section><h3>Operasyonel sonuç</h3><p>Doğru konfigürasyonla yerden kazanım, daha az manevra ve daha düşük hasar oranı elde edilir. Entegre şarj ve Li‑Ion mimari, vardiya arasında fırsat şarjı ile süreklilik sağlar.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "box-open",
                            "text": "Koli yoğun hatlarda palet içi yerleşimi hızlandırır"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda raf derinliğine güvenli erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de farklı ambalaj boylarına uyum"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattında WIP transferlerinde stabil kavrama"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv kasalarında çıkıntılı yüklerde hassas pozisyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya varillerinde merkez kaçmayı azaltan denge"
                        }
                    ]
JSON
                , true)
            ]
        ,
            
            [
                'sku' => 'CPD20FVL-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD20FVL - 1150 mm Çatal Forklift',
                'short_description' => '1150 mm çatal uzunluğu ile farklı palet ve kutu ebatlarında daha iyi denge ve yerleştirme kontrolü sağlar; dar koridorlarda palet giriş-çıkışlarını hızlandırır.',
                'long_description' => '<section><h2>1150 mm Çatal: Esneklik ve erişim</h2><p>Operasyonlarda palet ölçülerinin ve rampa koşullarının farklılaştığı alanlarda 1150 mm çatal, yükün ağırlık merkezine daha doğru erişim ve daha güvenli kavrama sağlar. Sürücüler raf derinliğine yaklaşırken palet ucunu daha rahat hizalar; iade ve çapraz sevkiyat gibi hızlı değişen akışlarda süreklilik artar.</p></section><section><h3>Teknik denge</h3><p>Uygun çatal boyu, yükün ön aksa binen momentini optimize eder. Palet çıkıntılarında şasiye çarpmayı azaltır, raf ayağına yaklaşırken daha öngörülebilir duruş sağlar. Farklı ataşman ve lastik seçenekleriyle birlikte, ürün bütün gün boyunca dengeli ve hızlı çalışır.</p></section><section><h3>Operasyonel sonuç</h3><p>Doğru konfigürasyonla yerden kazanım, daha az manevra ve daha düşük hasar oranı elde edilir. Entegre şarj ve Li‑Ion mimari, vardiya arasında fırsat şarjı ile süreklilik sağlar.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "box-open",
                            "text": "Koli yoğun hatlarda palet içi yerleşimi hızlandırır"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda raf derinliğine güvenli erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de farklı ambalaj boylarına uyum"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattında WIP transferlerinde stabil kavrama"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv kasalarında çıkıntılı yüklerde hassas pozisyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya varillerinde merkez kaçmayı azaltan denge"
                        }
                    ]
JSON
                , true)
            ]
        ,
            
            [
                'sku' => 'CPD20FVL-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD20FVL - 1220 mm Çatal Forklift',
                'short_description' => '1220 mm çatal uzunluğu ile farklı palet ve kutu ebatlarında daha iyi denge ve yerleştirme kontrolü sağlar; dar koridorlarda palet giriş-çıkışlarını hızlandırır.',
                'long_description' => '<section><h2>1220 mm Çatal: Esneklik ve erişim</h2><p>Operasyonlarda palet ölçülerinin ve rampa koşullarının farklılaştığı alanlarda 1220 mm çatal, yükün ağırlık merkezine daha doğru erişim ve daha güvenli kavrama sağlar. Sürücüler raf derinliğine yaklaşırken palet ucunu daha rahat hizalar; iade ve çapraz sevkiyat gibi hızlı değişen akışlarda süreklilik artar.</p></section><section><h3>Teknik denge</h3><p>Uygun çatal boyu, yükün ön aksa binen momentini optimize eder. Palet çıkıntılarında şasiye çarpmayı azaltır, raf ayağına yaklaşırken daha öngörülebilir duruş sağlar. Farklı ataşman ve lastik seçenekleriyle birlikte, ürün bütün gün boyunca dengeli ve hızlı çalışır.</p></section><section><h3>Operasyonel sonuç</h3><p>Doğru konfigürasyonla yerden kazanım, daha az manevra ve daha düşük hasar oranı elde edilir. Entegre şarj ve Li‑Ion mimari, vardiya arasında fırsat şarjı ile süreklilik sağlar.</p></section>',
                'use_cases' => json_decode(<<<'JSON'
                    [
                        {
                            "icon": "box-open",
                            "text": "Koli yoğun hatlarda palet içi yerleşimi hızlandırır"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Dar koridorlarda raf derinliğine güvenli erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de farklı ambalaj boylarına uyum"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hattında WIP transferlerinde stabil kavrama"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv kasalarında çıkıntılı yüklerde hassas pozisyon"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya varillerinde merkez kaçmayı azaltan denge"
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
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }
        $this->command->info("✅ Variants: CPD20FVL (" . count($variants) . " adet)");
    }
}
