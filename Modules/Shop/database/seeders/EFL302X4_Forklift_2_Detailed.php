<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL302X4_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFL302X4')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFL302X4');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF EFL302X4: Sahada Elektriğin En Esnek Hali</h2>
  <p>İXTİF EFL302X4; 3.0 ton kapasiteli, karşı denge sınıfında modüler Li-Ion batarya konseptini gerçek operasyona indiren yeni nesil bir forklift. Her biri yalnızca 26 kg olan batarya modülleri operatör tarafından el ile sökülüp takılabildiği için, şarj altyapısının kısıtlı olduğu inşaat, tarım veya saha çalışmalarında araç yerinde kalırken enerji uzaktan yönetilebilir. Geniş görüşlü direk, ferah kabin, genişletilmiş fren pedalı ve büyük LED ekran; güvenlik ve ergonomiyi günlük iş akışının doğal parçası yapar. 80V/100Ah standart batarya paketi, akıllı BMS ve yüksek verimli PMS sürüş sistemi ile birleşerek düşük bakım ve yüksek çevrim ömrü sağlar.</p>
</section>
<section>
  <h3>Teknik Güç ve Verimlilik</h3>
  <p>EFL302X4; 500 mm yük merkezinde 3000 kg taşıma kapasitesi, 1760 mm dingil mesafesi ve 2428 mm dönüş yarıçapı ile dengeli manevra kabiliyeti sunar. 11/12 km/s sürüş hızı, 0.29/0.36 m/s kaldırma ve 0.4/0.4 m/s indirme hızları üretken vardiyalarda akışı hızlandırır. 15/15% eğim kabiliyeti, rampalı saha ve yükleme alanlarında kararlılık sağlar. Hidrolik servis freni ve mekanik park freni, 60 dB(A) kulak seviyesi ile desteklenerek uzun vardiyalarda konforu artırır. 8 kW PMS sürüş motoru ve 16 kW kaldırma motoru, yüksek güç yoğunluğunu minimize edilmiş enerji tüketimiyle birleştirir. 80V/100Ah Li-Ion batarya seti (üç modül) akıllı BMS ile hücre dengelemesi, sıcaklık takibi ve güvenli şarj yönetimi sağlar; opsiyonel ikinci üçlü setle toplam altı pakete genişletilerek vardiya içi çalışmayı kesintisiz kılmak mümkündür.</p>
  <p>Boyutsal olarak 3735 mm toplam uzunluk, 2665 mm yüke kadar uzunluk ve 1228 mm genişlik; 1100 mm çatal tablası ve 45×122×1070 mm çatal ölçüleri ile Avrupa paletleri için ideal geometri sunar. 2265 mm direk kapalı yüksekliği ve 4096 mm açık yükseklik; 2700–3500 mm arası direk seçenekleriyle farklı depo tavan yüksekliklerine uyum sağlar. Pnömatik lastikler (28×9-15-14PR ön, 6.5F-10-10PR arka) değişken zemin koşullarında konfor ve çekişi birlikte getirir.</p>
</section>
<section>
  <h3>Sonuç: Esnek Enerji, Sürekli Operasyon</h3>
  <p>Modüler, elle değiştirilebilir batarya mimarisi; saha kiralama modelleri, çok lokasyonlu operasyonlar ve şarj istasyonu erişimi sınırlı işletmeler için gerçek bir oyun değiştirici. Opsiyonel çoklu şarj istasyonu aynı anda altı paketi şarj edebilir; vardiya planını güç durumuna göre değil iş akışına göre kurmanızı sağlar. EFL302X4, bakım ihtiyacını azaltırken üretkenliği artırır; özellikle ağır görev, yoğun vardiya ve karma saha koşullarında ideal bir çözüm sunar. Teknik detaylar ve demo için 0216 755 3 555 üzerinden ekibimize ulaşın.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '3000 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 100Ah Li-Ion (3 modül)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11/12 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => 'Wa 2428 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Manuel değiştirilebilir modüler batarya', 'description' => '26 kg modüllerle arazide güvenli ve hızlı enerji değişimi'],
                ['icon' => 'bolt', 'title' => 'PMS sürüş + akıllı BMS', 'description' => 'Yüksek verim, düşük tüketim ve hücre koruma'],
                ['icon' => 'arrows-alt', 'title' => 'Geniş görüşlü direk', 'description' => 'Yük üstü net görüş ve çarpışma riskinin azalması'],
                ['icon' => 'shield-alt', 'title' => 'Suya dayanıklı tasarım', 'description' => 'Zorlu saha ve dış ortam koşullarına uyum'],
                ['icon' => 'plug', 'title' => 'Çoklu şarj istasyonu', 'description' => 'Aynı anda altı pakete kadar harici şarj'],
                ['icon' => 'star', 'title' => 'Ergonomi ve konfor', 'description' => 'Geniş pedal, yeni koltuk ve büyük LED ekran']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'industry', 'text' => 'Ağır sanayi tesislerinde 3.0 ton sınıfı hat besleme ve mamul transferi'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında yoğun vardiya palet taşımaları ve rampa operasyonları'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça ve CKD hatlarında konteyner boşaltma'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli, düşük emisyonlu iç mekan taşıma'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış alanlarında hızlı çevrim operasyonları'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde çapraz sevkiyat ve konsolidasyon görevleri'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde güvenli ve kontrollü palet transferleri'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment alanlarında yüksek tempolu sipariş akışı']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'PMS motor ve BMS entegrasyonu ile sınıfında üstün enerji verimliliği'],
                ['icon' => 'battery-full', 'text' => 'Modüler, taşıması kolay batarya paketleri ile kesintisiz vardiya'],
                ['icon' => 'arrows-alt', 'text' => 'Geniş görüşlü direk ve kompakt ölçülerle güvenli manevra'],
                ['icon' => 'shield-alt', 'text' => 'Suya dayanıklı ve sağlam şasi ile zorlu koşullara uyum'],
                ['icon' => 'star', 'text' => 'Ergonomik kabin, geniş pedallar ve LED ekran ile operatör konforu'],
                ['icon' => 'layer-group', 'text' => 'Mast ve batarya konfigürasyonlarında ölçeklenebilir esneklik']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv ve Tedarikçileri'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri ve Yem']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Harici Çoklu Şarj İstasyonu', 'description' => 'Aynı anda 6 batarya modülünü güvenli ve ergonomik yükseklikte şarj eder.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Ek Batarya Seti (3 Modül)', 'description' => 'Standart 3’lü setin yanına ikinci 3’lü set ile toplam 6 modül çalışma', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'AC Şarj Kablo ve Konnektör Seti', 'description' => 'Güvenli bağlantı ve kolay taşıma için aksesuar seti', 'is_standard' => true, 'price' => null],
                ['icon' => 'circle-notch', 'name' => 'Pnömatik Lastik Seti', 'description' => 'Standart lastiklerin dönemsel değişim seti', 'is_standard' => true, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Modüler bataryalar kaç kilogram ve tek kişi tarafından değiştirilebilir mi?', 'answer' => 'Her modül yaklaşık 26 kg’dır ve yan kapıdan kolay erişimle tek operatör tarafından güvenli biçimde değiştirilebilir. El arabası gerektirmez, ergonomik tutamaçlar bulunur.'],
                ['question' => 'Standart batarya kapasitesi ve genişletme seçeneği nedir?', 'answer' => 'Standart konfigürasyon 80V/100Ah (3 modül) setidir. İkinci 3’lü set eklenerek 6 modüle çıkabilir, vardiya içi süre belirgin şekilde uzar.'],
                ['question' => 'Sürüş ve kaldırma motor güçleri hangi çalışma performansını sağlar?', 'answer' => '8 kW PMS sürüş ve 16 kW kaldırma motoru 11/12 km/s hız ve 0.29/0.36 m/s kaldırma değerlerini sağlar; enerji verimliliği yüksektir.'],
                ['question' => 'Hangi mast yükseklikleri mevcut ve tavan kısıtı olan depolara uygun mu?', 'answer' => '2700, 3000, 3300 ve 3500 mm mast seçenekleri vardır. 2265 mm kapalı yükseklik ve geniş görüş kesiti tavan kısıtı olan alanlara uyum sağlar.'],
                ['question' => 'Eğim kabiliyeti ve rampa performansı nasıldır?', 'answer' => 'Maksimum %15 (yüklü/boş) tırmanma kabiliyeti sunar. Hidrolik servis freni ve mekanik park freni rampalarda güven verir.'],
                ['question' => 'Operatör konforu için hangi iyileştirmeler yapıldı?', 'answer' => 'Yeni koltuk, geniş fren pedalı, büyük LED ekran ve yüksek görünürlüklü direk standarttır. Düşük 60 dB(A) seviye konforu destekler.'],
                ['question' => 'Bataryalar sahada nasıl şarj edilir; altyapı yoksa ne yapılır?', 'answer' => 'Bataryalar sökülüp uzaktan şarj edilebilir. Opsiyonel çoklu istasyonla aynı anda altı modül şarj edilir; sahada şebeke kısıtı problem olmaktan çıkar.'],
                ['question' => 'Hangi tekerlekler kullanılıyor ve zemine etkisi nedir?', 'answer' => 'Pnömatik lastikler (28×9-15-14PR ön, 6.5F-10-10PR arka) düzensiz zeminlerde konfor, çekiş ve ekipman koruması sağlar.'],
                ['question' => 'Gürültü seviyesi ve iç mekân uygunluğu nasıldır?', 'answer' => 'Sürücü kulak seviyesinde 60 dB(A) ölçülür. Emisyonsuz yapı sayesinde iç mekân operasyonları için uygundur.'],
                ['question' => 'Bakım aralıkları ve işletme maliyeti açısından avantaj nedir?', 'answer' => 'Li-Ion ve PMS mimarisi daha az bakım ihtiyacı doğurur; yağ değişimi ve klasik akü bakımları gerekmez, planlı duruşları azaltır.'],
                ['question' => 'Kiralamada batarya yönetimiyle farklı iş modelleri mümkün mü?', 'answer' => 'Modüller taşınabilir olduğu için batarya kiralama veya abonelik modeli kurgulanabilir; çok lokasyona hazır paket dağıtımı yapılabilir.'],
                ['question' => 'Garanti kapsamı ve servis desteği nasıl işliyor?', 'answer' => 'Makine 12 ay, Li-Ion modüller 24 ay garantilidir. Satış-sonrası kurulum, eğitim ve yedek parça için İXTİF ekibiyle 0216 755 3 555 üzerinden iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: EFL302X4');
    }
}
