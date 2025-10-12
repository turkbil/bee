<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES18_40WA_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ES18-40WA')->first();
        if (!$m) {$this->command->error('❌ Master ürün bulunamadı: ES18-40WA'); return; }
        $created = 0;

        DB::table('shop_products')->updateOrInsert(['sku' => 'ES18-40WA-920'], [
            'sku' => 'ES18-40WA-920',
            'parent_product_id' => $m->product_id,
            'variant_type' => 'catal-uzunlugu',
            'category_id' => $m->category_id,
            'brand_id' => $m->brand_id,
            'title' => json_encode(['tr' => 'İXTİF ES18-40WA - 920 mm Çatal'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug('İXTİF ES18-40WA - 920 mm Çatal')], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '920 mm çatal, kısa paletler ve dar raf ağızlarında minimum çıkıntı ile hassas manevra sunar. Hafif-orta yüklerde çeviklik artırır, taşıma sırasında dönüş alanını küçültür.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => "<section><h2>920 mm Çatal: Dar Alanların Hızlı Ustası</h2>\n<p>920 mm çatal konfigürasyonu, dar koridor ve kısa paletlerin yoğun olduğu hatlarda minimum çıkıntı ile güvenli manevra sağlar. Çıkıntının azalması, forklift trafiğiyle paylaşılan alanlarda çarpışma riskini düşürür; operatör, kaplumbağa hızı modunda raf ağzına yaklaşırken yükün iz düşümünü daha rahat yönetir.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>ES18-40WA’nın AC çekiş sistemi ve oransal hidrolik yapısı, kısa çatalın dahi dengeli ağırlık transferiyle çalışmasına olanak tanır. 1645 mm dönüş yarıçapı, 4.5/5.0 km/s hız ve 0.127/0.23 m/s kaldırma değerleri, kısa çatalın çeviklik avantajıyla birleşir. 200–760 mm ayarlı çatallar arası mesafe, küçük paketlerin merkezlenmesini kolaylaştırır.</p></section>\n<section><h3>Sonuç</h3>\n<p>Hızlı toplama, ayrıştırma ve kısa palet akışlarında 920 mm çatal, akışı hızlandırır ve hata riskini azaltır. Detaylı konfigürasyon için 0216 755 3 555.</p></section>"], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "Kısa paletli koli istasyonlarında giriş-çıkış"
                    },
                    {
                        "icon": "warehouse",
                        "text": "Dar raf ağızlarında düşük çıkıntı ile konumlandırma"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende arka oda mal kabul ve ayrıştırma"
                    },
                    {
                        "icon": "industry",
                        "text": "Montaj hücresinde WIP taşıma ve geri besleme"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv küçük parça sepet/palet operasyonları"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya laboratuvar depolarında güvenli yönlendirme"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'is_master_product' => false,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
        ]);
        $created++;
    
        DB::table('shop_products')->updateOrInsert(['sku' => 'ES18-40WA-1070'], [
            'sku' => 'ES18-40WA-1070',
            'parent_product_id' => $m->product_id,
            'variant_type' => 'catal-uzunlugu',
            'category_id' => $m->category_id,
            'brand_id' => $m->brand_id,
            'title' => json_encode(['tr' => 'İXTİF ES18-40WA - 1070 mm Çatal (Standart)'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug('İXTİF ES18-40WA - 1070 mm Çatal (Standart)')], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1070 mm standart çatal, EUR/ISO paletlerde dengeli ağırlık dağılımı ve çok yönlü kullanım sunar. Çoğu depolama senaryosunda optimum erişim ile verimli istifleme sağlar.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => "<section><h2>1070 mm Çatal: Günlük İşlerin Çok Yönlü Standardı</h2>\n<p>1070 mm, ES18-40WA’nın fabrika standardıdır ve geniş bir uygulama yelpazesine hizmet eder. Hem raf içi istifleme hem de kısa mesafe transferde yükü dengeli taşır; operatörün hat başına yaklaşırken yük izdüşümünü rahatça kontrol etmesine yardımcı olur.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>Standart çatal, 600 mm yük merkezindeki 1800 kg kapasiteyi verimli kullanır. 2560 mm koridor gereksinimi ve 1645 mm dönüş yarıçapı, çoğu depo planıyla uyumludur. Oransal hidrolik, kırılgan içeriklerde sarsıntısız indirmeye yardımcı olur.</p></section>\n<section><h3>Sonuç</h3>\n<p>Çoğu depo için en dengeli seçenek: Mal kabulden sevkiyata kadar aynı makineyle verimli akış. Detay için 0216 755 3 555.</p></section>"], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "Genel depo operasyonlarında EUR palet istifleme"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL akışlarında kampanya/stok yönetimi"
                    },
                    {
                        "icon": "pills",
                        "text": "Kırılgan ürün hatlarında kontrollü indirme"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda transferlerinde stabil kullanım"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Koli yoğun tekstil depolarında standart operasyon"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Kurumsal arşiv/evrak paletleme uygulamaları"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'is_master_product' => false,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
        ]);
        $created++;
    
        DB::table('shop_products')->updateOrInsert(['sku' => 'ES18-40WA-1150'], [
            'sku' => 'ES18-40WA-1150',
            'parent_product_id' => $m->product_id,
            'variant_type' => 'catal-uzunlugu',
            'category_id' => $m->category_id,
            'brand_id' => $m->brand_id,
            'title' => json_encode(['tr' => 'İXTİF ES18-40WA - 1150 mm Çatal'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug('İXTİF ES18-40WA - 1150 mm Çatal')], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1150 mm çatal, uzun paketli yüklerde daha iyi taban desteği ve yanal stabilite sunar. Raftan alma ve yerleştirmede yük salınımını azaltmaya yardımcı olur.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => "<section><h2>1150 mm Çatal: Uzun Yüklerde Dengeli Destek</h2>\n<p>1150 mm çatal, uzun ya da dengesiz ağırlık dağılımına sahip paletlerde daha stabil bir taban alanı sağlar. Bu sayede raf ağzında konumlandırma sırasında yük salınımı azalır ve operatörün hassas düzeltmeleri daha etkili olur.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>1.8 ton nominal kapasite, 600 mm yük merkezi ve 45/100 mm kesit ile çatal rijitliği korunur. AC çekiş ve elektromanyetik fren, yüksüz dönüşlerde dahi kontrolü elinizde tutmanızı sağlar. Ayarlanabilir b5 aralığı (200–760 mm) ile farklı kasalara uyum sağlanır.</p></section>\n<section><h3>Sonuç</h3>\n<p>Uzun paketler, mobilya ve otomotiv yan sanayide güvenli istif için 1150 mm çatal doğru tercihtir. Bilgi için 0216 755 3 555.</p></section>"], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "industry",
                        "text": "Endüstriyel komponent kasalarında daha iyi taban desteği"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv tampon/uzun parça paletlerinde stabil taşıma"
                    },
                    {
                        "icon": "print",
                        "text": "Baskı/ambalaj rulolarında güvenli kavrama"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya yarı mamullerinde geniş taban gereksinimi"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "FMCG’de karışık paletlerin yatay dengesi"
                    },
                    {
                        "icon": "building",
                        "text": "Tesis içi uzun yük transferi uygulamaları"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'is_master_product' => false,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
        ]);
        $created++;
    
        DB::table('shop_products')->updateOrInsert(['sku' => 'ES18-40WA-1220'], [
            'sku' => 'ES18-40WA-1220',
            'parent_product_id' => $m->product_id,
            'variant_type' => 'catal-uzunlugu',
            'category_id' => $m->category_id,
            'brand_id' => $m->brand_id,
            'title' => json_encode(['tr' => 'İXTİF ES18-40WA - 1220 mm Çatal'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug('İXTİF ES18-40WA - 1220 mm Çatal')], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1220 mm çatal, ekstra uzun paletlerde maksimum destek sağlayarak sarkma ve esnemeyi azaltır. Yüksek raflara yerleştirmede güven ve doğruluk kazandırır.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => "<section><h2>1220 mm Çatal: En Zorlu Uzun Yükler İçin</h2>\n<p>1220 mm çatal, ekstra uzun paletlerde taban temas alanını büyüterek yük sarkmasını sınırlar. Özellikle üst seviye raf yerleştirmelerinde operatöre daha fazla tolerans penceresi sunar.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>ES18-40WA’nın 3.0 kW kaldırma motoru ve oransal hidrolik kombinasyonu, uzun çatal kullanımında dahi kontrollü hız eğrisi sağlar. Dönüş yarıçapı 1645 mm olup, koridor planlamasında palet çıkıntıları hesaba katılmalıdır.</p></section>\n<section><h3>Sonuç</h3>\n<p>Hacimli ve uzun yüklerde en yüksek stabilite için 1220 mm çatal konfigürasyonu önerilir. Planlama desteği ve uygulama analizi için 0216 755 3 555.</p></section>"], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "warehouse",
                        "text": "Uzun paletli raflarda yüksek seviye istifleme"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır sanayide uzun kasaların transferi"
                    },
                    {
                        "icon": "wine-bottle",
                        "text": "İçecek paletlerinde karışık kasa desteklemesi"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım girdileri için uzun paket taşıma"
                    },
                    {
                        "icon": "hammer",
                        "text": "Yapı market ürünlerinde hacimli paketler"
                    },
                    {
                        "icon": "star",
                        "text": "Kalın shrinkli karışık yüklerde yatay stabilite"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'is_master_product' => false,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
        ]);
        $created++;
    
        $this->command->info("✅ Variants oluşturuldu: {$created} adet");
    }
}
