<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WPL202_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'WPL202')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı (WPL202)');
            return;
        }

        $variants = [
            [
                'sku' => 'WPL202-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF WPL202 - 1150 mm Çatal',
                'short_description' => '1150 mm çatal uzunluğu; EUR paletlerde hızlı giriş-çıkış, kamyon içi manevrada maksimum çeviklik ve kısa koridorlarda yüksek verim sağlar.',
                'body' => '<section><h2>1150 mm Çatal: Standart EUR Paletler İçin Hız</h2><p>WPL202’nin 1150 mm çatalı, Avrupa standardı paletlerde en rahat kullanımı sunar. Kısa çıkıntı, rampa ve kamyon içi dönüşlerde operatöre ekstra alan kazandırır. 470 mm l2 ve 1320 mm dönüş yarıçapı kombinasyonu, yükleme kapılarında bekleme sürelerini azaltır. Li‑Ion batarya sistemi fırsat şarjını desteklediğinden yoğun vardiyalarda bile akışın kesilmesi önlenir.</p></section><section><h3>Operasyonel Etki</h3><p>Perakende ve e-ticaret merkezlerinde sipariş konsolidasyonu, çapraz sevkiyat ve kısa mesafe transferlerinde 1150 mm çatal optimum denge sağlar. Çift PU yük tekerleri ve senkron kastor sistemi bozuk zeminlerde dengeyi korurken, elektromanyetik fren rampalarda güven verir.</p></section><section><h3>Neden 1150 mm?</h3><p>Standart palet derinliği ve raf aralıklarına uyumla hatasız yaklaşım, daha az düzeltme manevrası ve daha düşük ekipman yıpranması elde edilir. Soğuk depo kitiyle 0 ~ -20°C aralığında da aynı performans sürer.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletli hızlı yükleme/boşaltma'],
                    ['icon' => 'warehouse', 'text' => 'Cross-dock istasyonlarında hat besleme'],
                    ['icon' => 'store', 'text' => 'Perakende mağaza arkalarında dar alan taşıma'],
                    ['icon' => 'car', 'text' => 'Şehir içi dağıtım merkezlerinde ara depo hareketi'],
                    ['icon' => 'industry', 'text' => 'Üretim hücreleri arası kısa mesafe akış'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış operasyonları']
                ]
            ],
            [
                'sku' => 'WPL202-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF WPL202 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, daha uzun yüklerde ekstra destek ve uzun paletlerde denge sağlayarak hat besleme ve istasyonlar arası transferi hızlandırır.',
                'body' => '<section><h2>1220 mm Çatal: Uzun Paletlerde Dengeli Taşıma</h2><p>1220 mm’lik çatal, uzun kutular ve derin paletlerde yük desteğini artırır. WPL202’nin kompakt şasisi ve 1320 mm dönüş yarıçapı korunurken, daha uzun çatal sayesinde paletin uç kısmında sarkma riski azalır. Operatörler dar alanlarda kaplumbağa modu ile hassas hareket ederken, elektromanyetik fren yokuş başında güven verir.</p></section><section><h3>Operasyonel Etki</h3><p>Ambalaj, tekstil ve elektronik dağıtım merkezlerinde istasyonlar arası taşımada daha az yeniden konumlandırma gerektirir. Senkron kastor tekerleri ve PU yük tekerleri düzensiz yüzeylerde darbeleri sönümler, taşıma kalitesini yükseltir.</p></section><section><h3>Neden 1220 mm?</h3><p>Daha uzun paletler ve kutularda yük dağılımı iyileşir; özellikle yüksek yoğunluklu raf alanlarında palete tam destek, operatör güvenini artırır. Soğuk depo seçeneği ile düşük sıcaklıklarda da aynı denge korunur.</p></section>',
                'use_cases' => [
                    ['icon' => 'cart-shopping', 'text' => 'Ambalaj ve koli hatlarında istasyon besleme'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil depolarında uzun palet taşıma'],
                    ['icon' => 'microchip', 'text' => 'Elektronik dağıtımda cihaz kasaları'],
                    ['icon' => 'warehouse', 'text' => '3PL depolarında uzun koridor transferi'],
                    ['icon' => 'industry', 'text' => 'Montaj öncesi malzeme sevki'],
                    ['icon' => 'flask', 'text' => 'Kimyasal varil ve konteyner paletleri']
                ]
            ],
            [
                'sku' => 'WPL202-1450',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF WPL202 - 1450 mm Çatal',
                'short_description' => '1450 mm çatal, özel palet ve uzun paketlerde tek seferde güvenli destek sunar; rampa ve eşiklerde stabiliteyi artırır.',
                'body' => '<section><h2>1450 mm Çatal: Özel Paletlerde Tek Seferde Destek</h2><p>Standart dışı uzunluklardaki paletler ve paketler için 1450 mm çatal, yük altına daha geniş destek vererek esneme ve sarkmayı azaltır. Kompakt şasi ve senkron kastor sistemi, artan çatal boyuna rağmen çevikliği korur. Li‑Ion sistemle birlikte fırsat şarjı, uzun vardiyalarda kesintisiz akış sağlar.</p></section><section><h3>Operasyonel Etki</h3><p>Mobilya, beyaz eşya ve proje lojistiği uygulamalarında tek parça taşımalarda yeniden konumlandırma ihtiyacını düşürür. Elektromanyetik fren ve kauçuk tahrik tekeri (soğuk depo kiti) yüzey tutuşunu artırır.</p></section><section><h3>Neden 1450 mm?</h3><p>Daha uzun ürünlerde yük dengesini iyileştirir, palet kenarında ağırlık yoğunlaşmasını azaltır. Operatörler, kaplumbağa modunda hassas yaklaşım ile dar alanlarda dahi kontrollü çalışır.</p></section>',
                'use_cases' => [
                    ['icon' => 'couch', 'text' => 'Mobilya paketlerinde tek seferde taşıma'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya ve büyük kutu taşıma'],
                    ['icon' => 'car', 'text' => 'Otomotivde uzun komponent paletleri'],
                    ['icon' => 'warehouse', 'text' => 'Uzun raf hatlarında besleme'],
                    ['icon' => 'building', 'text' => 'Proje depolarında özel yükler'],
                    ['icon' => 'box-open', 'text' => 'Hacimli e-ticaret iadeleri']
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
            $this->command->info('✅ Varyant eklendi: ' . $v['sku']);
        }
    }
}
