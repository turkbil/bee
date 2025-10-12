<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES16_RS_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ES16-RS')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ES16-RS'); return; }

        $variants = [
            [
                'sku' => 'ES16-RS-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES16-RS - 1150 mm Çatal',
                'short_description' => '1150 mm çatal uzunluğu, 1.6 ton kapasite ve iki kademeli alçaltma ile standart EUR paletlerde hızlı ve emniyetli istif; dar koridorlarda yüksek çeviklik.',
                'long_description' => '<section><h3>1150 mm Çatalın Standardı</h3><p>1150 mm çatal konfigürasyonu, EUR paletlerle tam uyum sağlayarak depolama ve sevkiyat arasında hızlı akış kurar. 60×190 mm kesit ve 88 mm alçak çatal yüksekliği, palete nazik giriş ve hasarsız çıkış sağlar. 24V/210Ah akü, 3.0 kW kaldırma motoru ile birleştiğinde 5.5/6.0 km/s sürüş hızları ve 0.13/0.16 m/s kaldırma hızları sayesinde yoğun vardiyalarda standart çevrim sürelerini düşürür.</p></section><section><h3>Teknik Uyum</h3><p>850 mm şasi, 574 mm ön iz ve 1730/2090 mm dönüş yarıçapı dar koridorlarda çeviklik sunar. Elektronik direksiyon, elektromanyetik fren ve PU teker seti; tutarlı yönlendirme ve güvenli duruş sağlar. 3000 mm’e kadar kaldırma yüksekliği ve iki kademeli alçaltma fonksiyonu, raf temaslarını yumuşatır ve ürün bütünlüğünü korur.</p></section><section><h3>Operasyonel Değer</h3><p>Bu varyant; toplama, besleme ve sevkiyat hazırlığında minimum manevra, maksimum verim hedefleyen operasyonlar için optimize edilmiştir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletlerle standart giriş-çıkış istif döngüleri'],
                    ['icon' => 'warehouse', 'text' => 'Raf arası hat besleme ve tampon stok yönetimi'],
                    ['icon' => 'store', 'text' => 'Mağaza sevkiyatı için sipariş konsolidasyonu'],
                    ['icon' => 'industry', 'text' => 'Üretim alanında WIP taşıma ve ara stoklama'],
                    ['icon' => 'car', 'text' => 'Otomotiv yan sanayide komponent akışı'],
                    ['icon' => 'pills', 'text' => 'Hassas ürünlerin kontrollü ve nazik istifi']
                ]
            ],
            [
                'sku' => 'ES16-RS-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES16-RS - 1220 mm Çatal',
                'short_description' => '1220 mm çatal seçeneği, uzun palet ve özel yüklerde daha iyi denge sunar; 600 mm yük merkezi korunurken raf girişlerinde iki kademeli alçaltma hassasiyet sağlar.',
                'long_description' => '<section><h3>Uzun Paletlere Uyum</h3><p>1220 mm çatal, uzun paletler ve uzayan yük geometrilerinde ağırlık dağılımını iyileştirir. 60×190 mm kesit sertliği ve 24V/210Ah akü altyapısı; 1.6 ton nominal kapasiteyi güvenli şekilde sahaya taşır. 0.30/0.22 m/s indirme ve iki kademeli alçaltma, raflara yaklaşımda titreşimi azaltır.</p></section><section><h3>Hassas Manevra</h3><p>Elektronik direksiyon ve elektromanyetik fren sistemi; dar koridorlarda kontrollü hızlanma ve yumuşak duruş sağlar. 1730/2090 mm dönüş yarıçapı ile 850 mm şasi genişliği kombinasyonu, sıkışık zeminlerde çeviklik demektir.</p></section><section><h3>Uygulama Senaryoları</h3><p>İçecek kasaları, mobilya parçaları ve geniş yüzeyli kolilerde tek seferde güvenli taşıma ve düzenli istif sağlanır.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasalarında uzun palet kullanımı'],
                    ['icon' => 'couch', 'text' => 'Mobilya ve büyük koli istifi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG’de miks palet konsolidasyonu'],
                    ['icon' => 'warehouse', 'text' => 'Karma raflarda derinlik uyumu'],
                    ['icon' => 'hammer', 'text' => 'DIY ürünlerinde uzun paketlerin taşınması'],
                    ['icon' => 'print', 'text' => 'Ambalaj rulolarında stabil taşıma']
                ]
            ],
            [
                'sku' => 'ES16-RS-685W',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF ES16-RS - 685 mm Çatallar Arası',
                'short_description' => '685 mm çatallar arası mesafe, geniş palet ve kafes kasalarda daha iyi taban teması sağlar; PU tekerler ve AC sürüş ile sessiz ve kontrollü ilerleme.',
                'long_description' => '<section><h3>Geniş Paletlerde Temas ve Denge</h3><p>685 mm çatallar arası mesafe, özel palet ve kasalarda geniş taban temasına imkan tanır. Bu sayede yükün salınımı azalır, direğe yaklaşım kontrollü olur. 574 mm ön iz ve 380/495 mm arka iz ile şasi dengesi korunur.</p></section><section><h3>Kontrol ve Güvenlik</h3><p>Elektronik direksiyon, elektromanyetik fren ve iki kademeli alçaltma birlikte çalışarak ürün bütünlüğünü ve raf güvenliğini artırır. 5.5/6.0 km/s sürüş hızları ve 8/16% eğim kabiliyeti, tesisteki akışa esneklik katar.</p></section><section><h3>Uygulama Odakları</h3><p>Geniş taban gerektiren paletlerde ve dengesiz yüklerde operatörün güvenle hızlanıp yavaşlamasını destekleyen dengeli yapı sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Ağır sanayide geniş tabanlı palet taşımaları'],
                    ['icon' => 'box-open', 'text' => 'Kafes kasalarda stabil giriş ve çıkış'],
                    ['icon' => 'warehouse', 'text' => 'Karma ürün paletlerinde denge artırma'],
                    ['icon' => 'car', 'text' => 'Otomotiv alt parça kasalarında güvenli konumlandırma'],
                    ['icon' => 'flask', 'text' => 'Kimya varillerinde taban temasını artırma'],
                    ['icon' => 'pills', 'text' => 'Hassas ürün yüklerinde salınım kontrolü']
                ]
            ],
            [
                'sku' => 'ES16-RS-4500M',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES16-RS - 4500 mm Direk (Free Lift)',
                'short_description' => '4500 mm’e uzanan serbest kaldırmalı direk; kapı geçişlerinde düşük kapalı yükseklik, raflarda yüksek erişim ve iki kademeli alçaltma ile hassas yerleştirme.',
                'long_description' => '<section><h3>Yüksek Erişim, Düşük Kapalı Yükseklik</h3><p>Serbest kaldırmalı 4500 mm direk seçeneği, kapı ve tünel geçişlerinde düşük kapalı yükseklik avantajını korurken yüksek raflara erişim sağlar. Serbest kaldırma; yükün direk yükselmeden çatallarla yukarı alınmasına imkân tanıyarak tavan kısıtlarının olduğu bölgelerde güvenli çalışmayı destekler.</p></section><section><h3>Stabilite ve Görüş</h3><p>İki kademeli alçaltma, yükün inişini yumuşatır; elektronik direksiyon ve elektromanyetik fren, platform stabilitesini artırır. PU teker seti, titreşimi düşürerek operatör görüşünü net tutar.</p></section><section><h3>Performans</h3><p>3.0 kW kaldırma motoru ve AC sürüş kontrolü; yoğun iş yükünde dahi tutarlı hız ve kaldırma karakteri sağlar. 24V enerji mimarisi, akü yan çekme ve harici şarj ile yüksek vardiya kullanılabilirliği sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı bölgelerde yerleştirme'],
                    ['icon' => 'building', 'text' => 'Tavan kısıtlı kapalı alanlarda serbest kaldırma'],
                    ['icon' => 'cart-shopping', 'text' => 'Miks palet istifleme ve ayrıştırma'],
                    ['icon' => 'store', 'text' => 'Sevkiyat öncesi katmanlı yerleştirme'],
                    ['icon' => 'box-open', 'text' => 'Giriş-çıkış rampası yakınında tampon stok'],
                    ['icon' => 'industry', 'text' => 'Üretimde yarı mamulün üst raflara taşınması']
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
        }

        $this->command->info('✅ Variants güncellendi: ES16-RS');
    }
}
