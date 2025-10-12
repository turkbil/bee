<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL302X4_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL302X4')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL302X4'); return; }

        $variants = [
            [
                'sku' => 'EFL302X4-STD100',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL302X4 - Standart Batarya (80V/100Ah, 3 Modül)',
                'short_description' => 'Standart 80V/100Ah Li-Ion seti (3×26 kg modül) ile 11/12 km/s hız ve 0.29/0.36 m/s kaldırma değerlerini sunar. Hızlı modül değişimi ve geniş görüşlü direkle yoğun vardiyalarda güven ve verim sağlar.',
                'long_description' => '<section><h3>Standart Batarya ile Dengeli Performans</h3><p>Standart 3 modüllü (80V/100Ah) konfigürasyon, çoğu depo ve üretim sahasının günlük mesaisine fazlasıyla yeter; modüller yalnızca 26 kg olduğundan vardiya içinde güvenli ve hızlı değişim yapılır. Operatör, yan kapıdan bataryalara erişerek şarj için modülleri ayırabilir; araç sahada kalır, enerji yönetimi uzaktan gerçekleşir. Bu yaklaşım özellikle mobil şarjın sınırlı olduğu, priz erişiminin problem olduğu sahalarda operasyonu kesintisiz kılar.</p><p>3.0 ton kapasite ve 500 mm yük merkezinde dengeli ağırlık dağılımı, 1760 mm dingil mesafesi ile birleşir. 2428 mm dönüş yarıçapı ve 1228 mm genişlik, sıkışık alanlarda kontrollü manevra sağlar. 11/12 km/s hız ile akış hızlanır; 0.29/0.36 m/s kaldırma ve 0.4/0.4 m/s indirme hızları ritmi sabitler. Hidrolik servis freni ve mekanik park freni, PN lastiklerle birlikte rampa ve açık alanlarda güven verir.</p></section><section><h3>Uygulama Senaryoları</h3><p>Standart batarya, tek vardiya veya orta yoğunlukta iki vardiya çalışan depolar için ekonomik ve esnek çözümdür. E-ticaret akışlarında çapraz sevkiyat, otomotiv tedarikinde hat besleme, kimya ve gıda depolarında iç mekân taşımaları gibi geniş bir spektruma yayılır.</p></section><section><h3>İş Sonu</h3><p>Günün sonunda modüller sökülerek şarj alanına taşınır; ertesi güne hazır, dengeli bir enerji planı elde edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde tek vardiya palet akışı'],
                    ['icon' => 'warehouse', 'text' => '3PL’de rampa yaklaşımı ve yükleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv tedarikinde hat besleme'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında iç mekân lojistiği'],
                    ['icon' => 'snowflake', 'text' => 'Gıda soğuk oda giriş-çıkış destekleri'],
                    ['icon' => 'store', 'text' => 'Perakende DC konsolidasyon alanları']
                ]
            ],
            [
                'sku' => 'EFL302X4-EXT200',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL302X4 - Genişletilmiş Batarya (80V/200Ah, 6 Modül)',
                'short_description' => 'İki set halinde 6 modüllü 80V/200Ah konfigürasyonla uzun vardiyalarda minimum duruş. Bir set şarjdayken diğeri çalışır; kiralama ve çok lokasyonlu operasyonlar için ideal esneklik ve süreklilik sağlar.',
                'long_description' => '<section><h3>Kesintisiz Vardiya için Çift Set Çözümü</h3><p>Genişletilmiş batarya seçeneği, standart 3 modülün yanına ikinci bir 3’lü set ekleyerek toplam 6 modüle ulaşır. Operasyon esnasında üç modül araçta çalışırken diğer üç modül haricide şarj edilir; setler arasında dönüşümlü geçiş ile duruş süreleri minimize edilir. Bu düzen, yoğun vardiya temposu olan 3PL merkezleri, 7/24 çalışan üretim tesisleri ve çok lokasyonlu saha ekipleri için belirgin bir avantaj yaratır.</p><p>Akıllı BMS her iki sette de hücre koruması, sıcaklık ve akım izleme sağlar. PMS sürüş mimarisi yüksek verimli güç dönüşümü ile tüketimi düşük tutar. 11/12 km/s hız, 0.29/0.36 m/s kaldırma ve %15 eğim değerleri korunurken, enerji planı tamamen operasyona göre esnetilir.</p></section><section><h3>Verimlilik ve Maliyet</h3><p>Daha az şarj-bağlantı ziyareti, daha az planlı duruş ve daha iyi kapasite kullanım oranı (CU). Kiralama iş modellerinde, şarj edilmiş modüllerin sahalara dağıtılması kolaylaşır; enerji “tüketim başına” faturalanabilir.</p></section><section><h3>Güvenli Altyapı</h3><p>Opsiyonel çoklu şarj istasyonu ergonomik yükseklikte, altı modüle kadar eşzamanlı şarj sunar; kablo karmaşası ve sahada izinsiz hareket riskini azaltır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '7/24 depolarda kesintisiz palet akışı'],
                    ['icon' => 'industry', 'text' => 'Üretim hatlarında vardiya içi malzeme besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtımında yoğun slot saatleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv final montaj alanı beslemesi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret pik dönemleri ve kampanyalar'],
                    ['icon' => 'building', 'text' => 'Çok lokasyonlu saha operasyonları']
                ]
            ],
            [
                'sku' => 'EFL302X4-MAST3300',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL302X4 - 3300 mm Direk Seçeneği',
                'short_description' => '3300 mm kaldırma yüksekliği, 2210 mm kapalı ve 4395 mm açık direk boyutlarıyla raf erişimi artarken tavan kısıtı olan depolarda denge korunur. Görüş alanı geniş tasarım, yük kontrolünü kolaylaştırır.',
                'long_description' => '<section><h3>Depo Tavanına Uyumlu Kaldırma Çözümü</h3><p>3300 mm direk, standart konfigürasyona göre raf erişimini artırırken kapalı yükseklik ve görüş optimizasyonu ile tavan kısıtı olan tesislerde manevra güvenliğini korur. Operatörün yük üstü görüşü iyileştirilmiş kesit sayesinde çatal konumlama hataları azalır, ayırma ve istif döngüleri hızlanır.</p><p>Boyutlar PDF’de verilen değerlere göre 2210 mm kapalı ve yaklaşık 4395 mm açık yüksekliktir; serbest kaldırma ve eğim açıları raflı/rafsız konfigürasyonlara uyacak şekilde tasarlanır. Pnömatik lastik yapısı, rampa ve düzensiz zeminlerde konforu sürdürür.</p></section><section><h3>Operasyonel Etki</h3><p>Orta yüksek raflı depolar, paketleme ve sevkiyat alanları için ideal bir kompromi sunar. 3.0 ton kapasite, 11/12 km/s hız ve %15 eğim değerleri ile birlikte hem iç hem dış alanlarda dengeli performans verir.</p></section><section><h3>Uygulama Senaryoları</h3><p>Raf dizaynı sık değişen veya farklı tavan yüksekliklerine sahip çoklu lokasyonlu tesisler bu seçeneği tercih eder; ekipman filosunun standardizasyonu kolaylaşır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Orta yükseklik raflı depolarda raf içi çalışma'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf beslemeleri'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve istif'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret çıkış alanı palet hazırla'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent tampon stok alanları'],
                    ['icon' => 'flask', 'text' => 'Kimya ve deterjan raf stok alanları']
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
            $this->command->info('✅ Variant: ' . $v['sku']);
        }
    }
}
