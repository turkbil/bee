<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES12_25WA_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'ES12-25WA')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }
        $variants = json_decode(
            <<<'JSON'
            [
                {
                    "sku": "ES12-25WA-920",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF ES12-25WA 920 mm Çatal",
                    "short_description": "920 mm çatal uzunluğu ile palet denge noktasına daha iyi erişim ve çalışma alanında ihtiyaca uygun manevra sunar. ES12-25WA şasi ve iki kademeli indirme sayesinde raf hizasında hassas kontrol korunur, 1.200 kg kapasite verimli biçimde kullanılır.",
                    "body": "<section><h2>İXTİF ES12-25WA - 920 mm Çatal</h2><p>920 mm çatal uzunluğu, farklı palet boyutlarına uyum sağlayarak istifte esneklik kazandırır. Kısa koridorlarda dengeli yaklaşım ve yük merkezine yakın taşıma, operatörün daha az düzeltme ile paleti rafa yerleştirmesine yardım eder. 24V/210Ah enerji sistemi ve AC sürüş ile hızlanma akıcıdır; elektromanyetik fren eğimde güven sağlar.</p></section><section><h3>Teknik Etkiler</h3><p>Çift kademeli indirme mekanizması, paletin rafla buluştuğu son aşamada hızı düşürerek hassasiyeti artırır. 920 mm çatallar, 200–760 mm aralığında ayarlanabilir çatallar arası ile farklı kasa ve palet formatlarını destekler. 1490 mm dönüş yarıçapı ve 1947 mm toplam uzunluk dar alanlarda çevikliği korur. PU tekerlekler zemini korur, 74 dB(A) gürültü seviyesi konforludur.</p></section><section><h3>Operasyonel Kazanımlar</h3><p>Hızlı toplama, tampon alan yönetimi ve üretim hattı beslemesi gibi süreçlerde operasyon başına süreyi kısaltır. Bakım kolaylığı ve modüler aksesuar seçenekleri ile toplam sahip olma maliyetini düşürür. Doğru çatal uzunluğu, yanlış manevra ve düzeltmeleri azaltarak operatör verimliliğini yükseltir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Kısa paletlerle dar alan manevrası ve sık raf değişimi"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "Hızlı toplama hatlarında çevik yaklaşma"
                        },
                        {
                            "icon": "store",
                            "text": "Mağaza arkası dar depo uygulamaları"
                        },
                        {
                            "icon": "box-open",
                            "text": "Ambalaj atölyelerinde hızlı çevrimli istif"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hücresinde hat içi ara stok"
                        },
                        {
                            "icon": "tshirt",
                            "text": "Tekstil raflarında dar dönüş"
                        }
                    ]
                },
                {
                    "sku": "ES12-25WA-1070",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF ES12-25WA 1070 mm Çatal",
                    "short_description": "1070 mm çatal uzunluğu ile palet denge noktasına daha iyi erişim ve çalışma alanında ihtiyaca uygun manevra sunar. ES12-25WA şasi ve iki kademeli indirme sayesinde raf hizasında hassas kontrol korunur, 1.200 kg kapasite verimli biçimde kullanılır.",
                    "body": "<section><h2>İXTİF ES12-25WA - 1070 mm Çatal</h2><p>1070 mm çatal uzunluğu, farklı palet boyutlarına uyum sağlayarak istifte esneklik kazandırır. Kısa koridorlarda dengeli yaklaşım ve yük merkezine yakın taşıma, operatörün daha az düzeltme ile paleti rafa yerleştirmesine yardım eder. 24V/210Ah enerji sistemi ve AC sürüş ile hızlanma akıcıdır; elektromanyetik fren eğimde güven sağlar.</p></section><section><h3>Teknik Etkiler</h3><p>Çift kademeli indirme mekanizması, paletin rafla buluştuğu son aşamada hızı düşürerek hassasiyeti artırır. 1070 mm çatallar, 200–760 mm aralığında ayarlanabilir çatallar arası ile farklı kasa ve palet formatlarını destekler. 1490 mm dönüş yarıçapı ve 1947 mm toplam uzunluk dar alanlarda çevikliği korur. PU tekerlekler zemini korur, 74 dB(A) gürültü seviyesi konforludur.</p></section><section><h3>Operasyonel Kazanımlar</h3><p>Hızlı toplama, tampon alan yönetimi ve üretim hattı beslemesi gibi süreçlerde operasyon başına süreyi kısaltır. Bakım kolaylığı ve modüler aksesuar seçenekleri ile toplam sahip olma maliyetini düşürür. Doğru çatal uzunluğu, yanlış manevra ve düzeltmeleri azaltarak operatör verimliliğini yükseltir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "EUR paletlerde standarda uygun denge ve etkin manevra"
                        },
                        {
                            "icon": "box-open",
                            "text": "Fulfillment istasyonlarında giriş-çıkış akışı"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv parça kasalarında dengeli taşıma"
                        },
                        {
                            "icon": "pills",
                            "text": "Hassas koli ve kasalarda kontrollü istif"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyasal variller için güvenli yaklaşım"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk odalarda ergonomik kullanım"
                        }
                    ]
                },
                {
                    "sku": "ES12-25WA-1150",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF ES12-25WA 1150 mm Çatal",
                    "short_description": "1150 mm çatal uzunluğu ile palet denge noktasına daha iyi erişim ve çalışma alanında ihtiyaca uygun manevra sunar. ES12-25WA şasi ve iki kademeli indirme sayesinde raf hizasında hassas kontrol korunur, 1.200 kg kapasite verimli biçimde kullanılır.",
                    "body": "<section><h2>İXTİF ES12-25WA - 1150 mm Çatal</h2><p>1150 mm çatal uzunluğu, farklı palet boyutlarına uyum sağlayarak istifte esneklik kazandırır. Kısa koridorlarda dengeli yaklaşım ve yük merkezine yakın taşıma, operatörün daha az düzeltme ile paleti rafa yerleştirmesine yardım eder. 24V/210Ah enerji sistemi ve AC sürüş ile hızlanma akıcıdır; elektromanyetik fren eğimde güven sağlar.</p></section><section><h3>Teknik Etkiler</h3><p>Çift kademeli indirme mekanizması, paletin rafla buluştuğu son aşamada hızı düşürerek hassasiyeti artırır. 1150 mm çatallar, 200–760 mm aralığında ayarlanabilir çatallar arası ile farklı kasa ve palet formatlarını destekler. 1490 mm dönüş yarıçapı ve 1947 mm toplam uzunluk dar alanlarda çevikliği korur. PU tekerlekler zemini korur, 74 dB(A) gürültü seviyesi konforludur.</p></section><section><h3>Operasyonel Kazanımlar</h3><p>Hızlı toplama, tampon alan yönetimi ve üretim hattı beslemesi gibi süreçlerde operasyon başına süreyi kısaltır. Bakım kolaylığı ve modüler aksesuar seçenekleri ile toplam sahip olma maliyetini düşürür. Doğru çatal uzunluğu, yanlış manevra ve düzeltmeleri azaltarak operatör verimliliğini yükseltir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "box-open",
                            "text": "Karma yüklerde daha esnek denge ve erişim"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Yoğun koridorlarda standart palet istifi"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "Hızlı toplama ve cross-dock operasyonları"
                        },
                        {
                            "icon": "industry",
                            "text": "Yarı mamul transferinde kontrollü taşıma"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende dağıtımında raf önü besleme"
                        },
                        {
                            "icon": "bolt",
                            "text": "Hızlı giriş-çıkışta stabil hızlanma"
                        }
                    ]
                },
                {
                    "sku": "ES12-25WA-1220",
                    "variant_type": "catal-uzunlugu",
                    "title": "İXTİF ES12-25WA 1220 mm Çatal",
                    "short_description": "1220 mm çatal uzunluğu ile palet denge noktasına daha iyi erişim ve çalışma alanında ihtiyaca uygun manevra sunar. ES12-25WA şasi ve iki kademeli indirme sayesinde raf hizasında hassas kontrol korunur, 1.200 kg kapasite verimli biçimde kullanılır.",
                    "body": "<section><h2>İXTİF ES12-25WA - 1220 mm Çatal</h2><p>1220 mm çatal uzunluğu, farklı palet boyutlarına uyum sağlayarak istifte esneklik kazandırır. Kısa koridorlarda dengeli yaklaşım ve yük merkezine yakın taşıma, operatörün daha az düzeltme ile paleti rafa yerleştirmesine yardım eder. 24V/210Ah enerji sistemi ve AC sürüş ile hızlanma akıcıdır; elektromanyetik fren eğimde güven sağlar.</p></section><section><h3>Teknik Etkiler</h3><p>Çift kademeli indirme mekanizması, paletin rafla buluştuğu son aşamada hızı düşürerek hassasiyeti artırır. 1220 mm çatallar, 200–760 mm aralığında ayarlanabilir çatallar arası ile farklı kasa ve palet formatlarını destekler. 1490 mm dönüş yarıçapı ve 1947 mm toplam uzunluk dar alanlarda çevikliği korur. PU tekerlekler zemini korur, 74 dB(A) gürültü seviyesi konforludur.</p></section><section><h3>Operasyonel Kazanımlar</h3><p>Hızlı toplama, tampon alan yönetimi ve üretim hattı beslemesi gibi süreçlerde operasyon başına süreyi kısaltır. Bakım kolaylığı ve modüler aksesuar seçenekleri ile toplam sahip olma maliyetini düşürür. Doğru çatal uzunluğu, yanlış manevra ve düzeltmeleri azaltarak operatör verimliliğini yükseltir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Uzun yüklerde daha iyi palet kavrama ve denge"
                        },
                        {
                            "icon": "box-open",
                            "text": "Hacimli koli paletlerinde dengeli destek"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv kasalarında uzatılmış erişim"
                        },
                        {
                            "icon": "couch",
                            "text": "Mobilya paketlerinde daha geniş temas"
                        },
                        {
                            "icon": "print",
                            "text": "Büyük ambalaj ve matbaa paletleri"
                        },
                        {
                            "icon": "hammer",
                            "text": "DIY ürün raflarında uzun yük yönetimi"
                        }
                    ]
                }
            ]
JSON,
            true
        );
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
            $this->command->info("✅ Varyant işlendi: " . $v['sku']);
        }
    }
}
