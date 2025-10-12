<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WPL202_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'WPL202')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (WPL202)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF WPL202: Dar Alanların Ağır İşçi Arısı</h2>
  <p>Depo kapıları açılır açılmaz operasyona hazır bir ekip üyesi gibi davranan İXTİF WPL202, çevikliğiyle sabah vardiyasından gece kapanışına kadar akışı hızlandırır. 470&nbsp;mm’lik kısa şasi ile rampa başında, kamyon içinde ve dar raf aralarında kolayca döner; 1320&nbsp;mm dönüş yarıçapı sayesinde manevralarınız daha az ileri-geri gerektirir. 2.0&nbsp;ton kapasite ve yalnızca 320&nbsp;kg servis ağırlığı, güvenli taşıma ve düşük zemin baskısı dengesini korur. Li‑Ion enerji sistemi ve entegre şarj cihazı, fırsat şarjıyla operasyona ara vermeden devam etmenizi sağlar; akü değişimi, su tamamlama ve bakım işleri gündeminizden çıkar.</p>
</section>
<section>
  <h3>Teknik</h3>
  <p>WPL202, 24V/100Ah Li‑Ion bataryayı standart sunar; 24V/30A entegre şarj cihazı ile şarj altyapınızı sadeleştirir. Dikey AC tahrik motoru (1.6&nbsp;kW) güçlü kalkış ve kontrollü hızlanma sağlar. Elektromanyetik servis freni ve mekanik direksiyon tasarımı, taşıma sırasında güveni artırır. 55/170/1150&nbsp;mm çatal ölçüleri ve 85&nbsp;mm minimum çatal yüksekliği, yaygın EUR paletlerde sorunsuz giriş-çıkış sunar. 714&nbsp;mm toplam genişlik ve 1620&nbsp;mm toplam uzunluk, dar koridorlarda etkin kullanım sağlar; şasi orta noktası 27&nbsp;mm yerden açıklık ile rampalarda sürtünme riski düşüktür. Kaldırma ve indirme hızları sırasıyla 0.022&nbsp;m/s ve 0.039&nbsp;m/s seviyesindedir. Yüklü/yüksüz hız 5.5/6&nbsp;km/s olup, %10/%16 tırmanma kabiliyetiyle rampa ve eşiklerde performansını korur. PU yük tekerleri çift düzenlemede dayanıklıdır; senkron kastor teker sistemi zemin bozukluklarında dengeyi destekler. 74&nbsp;dB(A) ses seviyesi kullanıcı konforunu gözetir. Soğuk depo seçeneği (0&nbsp;~&nbsp;-20&nbsp;°C), su geçirmez kumanda başı, ısıtmalı Li‑Ion batarya, kauçuk tahrik tekeri ve düşük sıcaklık hidrolik yağıyla donatılarak kesintisiz çalışma sağlar.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>WPL202; dar alan kabiliyeti, Li‑Ion verimliliği ve olgun komponentlerin sağladığı güvenilirlikle yoğun depo, perakende dağıtım ve şehir içi teslimat merkezlerinde toplam sahip olma maliyetini düşürür. Hızlı kurulum ve kolay kullanım sayesinde eğitime ayrılan süre kısalır, verimlilik erken devreye girer. Teknik ekipleriniz için bakım listesi sadeleşirken, operatörleriniz ergonomik kumanda kolu ve kaplumbağa modu ile güvenle çalışır. Doğru çatal ölçüsü ve soğuk depo opsiyonlarıyla operasyonunuza tam uyan yapılandırmayı belirlemek için bize ulaşın: 0216 755 3 555.</p>
</section>
'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 100Ah Li‑Ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '5.5 / 6 km/s (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1320 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li‑Ion Enerji', 'description' => '24V/100Ah batarya ile fırsat şarjı ve sıfır bakım'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Şasi', 'description' => '470 mm l2 ile kamyon içi ve dar koridor çevikliği'],
                ['icon' => 'layer-group', 'title' => 'Stabil Kastor', 'description' => 'Senkron kastor sistemiyle zemin bozukluğunda denge'],
                ['icon' => 'plug', 'title' => 'Entegre Şarj', 'description' => '24V/30A dahili şarj ile kablo kalabalığı yok'],
                ['icon' => 'shield-alt', 'title' => 'Dayanıklı Elektronik', 'description' => 'Suya dayanıklı kumanda başı ve elektronik bileşenler'],
                ['icon' => 'snowflake', 'title' => 'Soğuk Depo Opsiyonu', 'description' => '0 ~ -20°C için ısıtmalı batarya ve uygun yağlar']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Kamyon içinde EUR paletlerin hızlı yükleme/boşaltması'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde dar koridor besleme ve transfer'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde hat besleme ve toplama'],
                ['icon' => 'snowflake', 'text' => 'Soğuk odalarda 0 ~ -20°C aralığında güvenilir taşıma'],
                ['icon' => 'car', 'text' => 'Şehir içi cross-dock merkezlerinde rampa yaklaşımı'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolarda kontrollü ve güvenli malzeme akışı'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas kutu ve kolilerin paletlenmesi'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP ve hammadde akışı']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Dikey AC tahrik motoru ile güçlü ve verimli sürüş'],
                ['icon' => 'battery-full', 'text' => 'Li‑Ion akü ile sıfır bakım ve fırsat şarjı'],
                ['icon' => 'arrows-alt', 'text' => '470 mm l2 ile sınıfında üstün manevra'],
                ['icon' => 'layer-group', 'text' => 'Senkron kastor tekerleriyle yüksek stabilite'],
                ['icon' => 'shield-alt', 'text' => 'Su korumalı elektronikler ile yüksek güvenilirlik']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistiği'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolar'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yedek Parça'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'Ofis Lojistiği ve Arşiv'],
                ['icon' => 'building', 'text' => 'Belediye ve Kamu Depoları']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li‑Ion batarya modülü 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V/30A Şarj Cihazı', 'description' => 'Cihaz üzerinde dahili şarj ile altyapı yatırımını azaltır, fırsat şarjına uygundur.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'circle-notch', 'name' => 'PU Yük Teker Seti (Çift)', 'description' => 'Sessiz çalışma ve düzgün yuvarlanma sağlayan dayanıklı yük tekerleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'snowflake', 'name' => 'Soğuk Depo Kiti', 'description' => 'Isıtmalı Li‑Ion batarya, suya dayanıklı komponentler ve düşük ısı hidrolik yağı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'key', 'name' => 'Tuş Takımı (Pin Kodlu)', 'description' => 'Anahtar yerine keypad ile yetkilendirme ve günlük kullanım kolaylığı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'WPL202 hangi palet ölçülerinde en verimli çalışır?', 'answer' => 'Standart 55/170/1150 mm çatal ölçüsü EUR paletlerde sorunsuz giriş-çıkış sağlar. 540 veya 685 mm çatal aralığı farklı palet tiplerine uyum sunar.'],
                ['question' => 'Yüklü ve yüksüz maksimum hız değerleri nedir?', 'answer' => 'Maksimum sürüş hızı yüklü 5.5 km/s, yüksüz 6 km/s’dir. Kaplumbağa modu dar alanlarda güvenli, düşük hızlı manevra sağlar.'],
                ['question' => 'Maksimum eğim performansı hangi koşullarda korunur?', 'answer' => '%10 yüklü ve %16 yüksüz tırmanma kabiliyeti sağlanır. Kısa şasi ve senkron kastor sistemi rampalarda stabiliteyi destekler.'],
                ['question' => 'Soğuk depoda kullanırken hangi bileşenler farklıdır?', 'answer' => 'Isıtmalı Li‑Ion batarya, su geçirmez kumanda başı, kauçuk tahrik tekeri ve düşük sıcaklık hidrolik yağı kullanılır. Çalışma aralığı 0 ~ -20°C’dir.'],
                ['question' => 'Bataryanın günlük bakım gereksinimi var mı?', 'answer' => 'Li‑Ion sistemde su tamamlama gerekmez, bellek etkisi yoktur. Entegre şarj ile vardiya aralarında fırsat şarjı yapılabilir.'],
                ['question' => 'Kumanda kolu ergonomisi uzun süreli kullanıma uygun mu?', 'answer' => 'Ergonomik tasarım ve düşük kol kuvveti ihtiyacı, yoğun vardiyalarda operatör yorgunluğunu azaltır. Kaplumbağa düğmesi hassas yaklaşım sağlar.'],
                ['question' => 'Dönüş yarıçapı ve şasi ölçüsü hangi alanlarda avantaj sağlar?', 'answer' => '1320 mm dönüş yarıçapı ve 470 mm l2 değeri sayesinde kamyon içi ve dar koridorlarda seri manevra mümkündür.'],
                ['question' => 'Fren sistemi ve emniyet özellikleri nelerdir?', 'answer' => 'Elektromanyetik servis freni standarttır. Suya dayanıklı elektronikler ve sağlam şasi yapısı güvenli çalışmayı destekler.'],
                ['question' => 'Ses seviyesi çalışma konforunu nasıl etkiler?', 'answer' => '74 dB(A) seviyesindeki ses basıncı, kapalı alan operasyonlarında kullanıcı ve çevre konforunu gözetir.'],
                ['question' => 'Bakım ve parça bulunabilirliği konusunda neler bilinmeli?', 'answer' => 'Olgun ve yaygın komponentler kullanıldığı için bakım prosedürleri basittir ve yedek parça tedariki kolaydır.'],
                ['question' => 'Hangi şarj altyapısı gerekir, harici şarj şart mı?', 'answer' => 'Harici şarj şart değildir; 24V/30A entegre şarj cihazı standarttır. Uygun priz altyapısı ile fırsat şarjı yapılabilir.'],
                ['question' => 'Garanti ve satış sonrası destek nasıl sağlanır?', 'answer' => 'Makine 12 ay, akü 24 ay garantilidir. İXTİF satış ve servis desteği için 0216 755 3 555 numarasından ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: WPL202');
    }
}
