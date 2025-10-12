<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JX2_1_Siparis_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'JX2-1')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: JX2-1'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF JX2-1 ile Zemin Seviyesinde Hızlı, Güvenli ve Akıllı Toplama</h2>
  <p><strong>Sabah vardiyası başladığında</strong> ilk paleti raftan almak için saniyeler önemlidir. İXTİF JX2-1, 31.5” kompakt gövdesi ve 58” dönüş yarıçapıyla dar koridorlarda bile akıcı manevra sağlar. Operatör, çift yönlü sürüş kumandası sayesinde çatal önde ya da arkada konumlarla ergonomik çalışır; rejeneratif frenleme ve elektromanyetik park freni, her duruşu güvenli ve kontrollü kılar. 72”e kadar kaldırma yüksekliği, alt seviye raflardan ürün toplamayı hızlandırırken 42” çatal ve 22” çatal aralığı, yaygın palet ölçülerine tam uyum sunar. İç mekân ve düzgün zemin için optimize edilen şasi, gürültüyü 74 dB(A) seviyesinde tutarak vardiya konforunu artırır. Kısacası JX2-1, düşük seviye order picking operasyonlarında ritmi yükselten, güveni artıran ve verimliliği günlük standardınız hâline getiren bir çözümdür.</p>
</section>
<section>
  <h3>Teknik Güç ve Operasyonel Verim</h3>
  <p>JX2-1, 24V elektrik mimarisi üzerinde çalışan <em>AC sürüş kontrolü</em> ile pürüzsüz hızlanma ve hassas düşük hız kontrolü sağlar. 2.5 kW sürüş motoru, 5 mph seyir hızına istikrarlı biçimde ulaşır; 3 kW kaldırma motoru ise 25.6/31.5 fpm kaldırma hızlarıyla yoğun toplama senaryolarında akışı destekler. 3571.5 lb servis ağırlığına sahip makine, poly sürüş, yük ve kaster tekerlek kombinasyonuyla titreşimi azaltır ve zemin aşınmasını minimuma indirir. 60” katlanmış yükseklik ve 101” uzatılmış direk yüksekliği; 99.2” toplam uzunluk ve 63.2” yüze kadar uzunluk değerleriyle raf önlerinde hassas konumlanma sağlar. 2” yerden yükseklik ve 111.5” sağ açı istif koridor genişliği, dar alan verimliliğini ölçülebilir şekilde iyileştirir. 24V/340Ah kurşun-asit veya 24V/224Ah AGM batarya seçenekleri, 25A ya da 40A çıkışlı şarj cihazlarıyla desteklenir; 700 lb batarya kütlesi, dengeye katkı vererek platformda güven duygusunu artırır. İç mekân kullanımına özel olarak tasarlanan JX2-1, düz ve pürüzsüz zeminlerde sıfır eğim kabulü ile en güvenli performansını sergiler.</p>
  <p>Ergonomi tarafında, operatör bölmesi 61.4” yüksekliği ve 48” yükseltilmiş ayakta durma seviyesi ile görüş hattını güçlendirir. Çift taraflı sürüş kumandaları, operatörün <strong>forks forward</strong> veya <strong>forks trailing</strong> duruşlarında doğal ve güvenli bir çalışma ritmi yakalamasını sağlar. Standart poly tekerlek takımı sessiz ve öngörülebilir bir yuvarlanma sunarken elektromanyetik park freni, eğimsiz zeminlerde dahi ekipmanı stabil tutar. AC tahrik ve rejeneratif fren, enerji geri kazanımı ile bakım gereksinimlerini azaltır ve toplam sahip olma maliyetini düşürür.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>İXTİF JX2-1, düşük seviye sipariş toplama süreçlerinde hız, güvenlik ve sadeliği bir araya getirir. 72” kaldırma yüksekliği, 42” çatal, 5 mph hız ve çoklu batarya seçenekleri ile depo standartlarınızı yükseltmek için tasarlanmıştır. Detaylı uygulama analizi ve doğru konfigürasyon seçimi için ekibimizle görüşün: <strong>0216 755 3 555</strong>.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2,200 lb (mini mast, 300 lb operatör)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V/340Ah Kurşun-asit veya 24V/224Ah AGM (Li-Ion opsiyon)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '5 mph (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '58” dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Çoklu Enerji Seçeneği', 'description' => '24V 340Ah kurşun-asit, 224Ah AGM ve Li-Ion opsiyonlarıyla esnek altyapı'],
                ['icon' => 'gauge', 'title' => 'Sabit 5 mph Hız', 'description' => 'AC sürüş ve rejeneratif frenleme ile öngörülebilir sürüş'],
                ['icon' => 'ruler-vertical', 'title' => '72” Kaldırma', 'description' => 'Alt seviye raflarda ergonomik erişim ve güvenli toplama'],
                ['icon' => 'warehouse', 'title' => 'Dar Koridor Çevikliği', 'description' => '31.5” genişlik, 111.5” sağ açı istif koridor değeri'],
                ['icon' => 'hand', 'title' => 'Çift Taraflı Kumanda', 'description' => 'Forks forward veya trailing duruşlarında kontrol kolaylığı'],
                ['icon' => 'shield-alt', 'title' => 'Emniyet ve Konfor', 'description' => 'Elektromanyetik park freni, 74 dB(A) gürültü seviyesi']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret siparişlerinin zemin seviyesinden hızlı toplanması'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında yoğun vardiya içi hat beslemesi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde cross-dock operasyonları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG raf öncesi ürün toplama ve sevk hazırlığı'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkışlarında düşük gürültüyle malzeme akışı'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında hassas koli toplama'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında SKU bazlı toplama'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim kolilerinde bölge bazlı toplama'],
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC sürüş ve rejeneratif fren ile düşük bakım ve yüksek enerji verimi'],
                ['icon' => 'battery-full', 'text' => '340Ah/224Ah seçenekleri ve uyumlu şarj cihazlarıyla esnek enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => 'Çift yönlü kumandalar ile operatörün doğal çalışma ergonomisi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik park freni ve poly tekerlekte güvenli tutuş'],
                ['icon' => 'warehouse', 'text' => '31.5” genişlik ile dar koridorlarda üstün çeviklik'],
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Akıllı Şarj Cihazı (25A/40A)', 'description' => 'Enerji gereksinimine göre seçilebilen iki akım seçeneğiyle dengeli şarj.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'AGM 224Ah Batarya Paketi', 'description' => 'Düşük bakım gerektiren AGM hücrelerle daha hızlı şarj ve temiz operasyon.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'Kurşun-Asit 340Ah Batarya', 'description' => 'Uzun vardiyalar için yüksek kapasite; kanıtlanmış dayanıklılık ve süreklilik.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Poly Teker Seti Yedek', 'description' => 'Sürüş/yük/kaster teker seti; düşük titreşim ve zemin dostu iz.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'JX2-1 hangi zemin ve ortam koşulları için uygundur?', 'answer' => 'Makine yalnızca iç mekân ve düz, pürüzsüz zeminlerde kullanım için tasarlanmıştır. Sıfır eğim kabulü ile güvenli performans sunar ve açık alan önerilmez.'],
                ['question' => 'Maksimum kaldırma yüksekliği nedir ve hangi raf seviyelerini kapsar?', 'answer' => 'Maksimum çatal yüksekliği 72 inçtir. Bu değer düşük seviye raflardan sipariş toplama için idealdir ve operatör görüşünü korur.'],
                ['question' => 'Sürüş hızı ve hız kontrolü nasıldır?', 'answer' => 'Yüklü ve yüksüz 5 mph sabit hız sağlar. AC sürüş ve rejeneratif frenleme yumuşak hızlanma ile güvenli duruş sunar.'],
                ['question' => 'Dönüş yarıçapı ve koridor performansı nedir?', 'answer' => '58 inç dönüş yarıçapı ve 111.5 inç sağ açı istif koridor değeriyle dar alanlarda verimli manevra sağlar.'],
                ['question' => 'Batarya seçenekleri nelerdir ve şarj cihazı uyumu nasıldır?', 'answer' => '24V/340Ah kurşun-asit ve 24V/224Ah AGM seçenekleri mevcuttur; 25A veya 40A şarj cihazlarıyla uyumludur. Li-Ion opsiyon da sunulur.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler?', 'answer' => '74 dB(A) seviyesinde ölçülen ses basıncı, kapalı depo ortamlarında kabul edilebilir konfor düzeyi sağlar.'],
                ['question' => 'Fren sistemi ve park güvenliği hakkında bilgi verebilir misiniz?', 'answer' => 'Rejeneratif servis freni enerji geri kazanımı sağlar; elektromanyetik park freni duruşlarda ekipmanı güvenle sabitler.'],
                ['question' => 'Tekerlek ve lastik yapısı hangi avantajları getirir?', 'answer' => 'Poly sürüş, yük ve kaster tekerleri düşük titreşim, zemin dostu yuvarlanma ve sessiz çalışma sunar.'],
                ['question' => 'Operatör bölmesi ergonomisinde öne çıkan unsurlar nelerdir?', 'answer' => '61.4” bölme yüksekliği, 48” yükseltilmiş ayakta durma seviyesi ve çift yönlü kumanda doğal bir çalışma postürü sağlar.'],
                ['question' => 'Bakım aralıkları ve toplam sahip olma maliyeti açısından ne beklenmeli?', 'answer' => 'AC tahrik ve poly teker kombinasyonu düşük bakım eğilimi yaratır; doğru şarj rejimleriyle batarya ömrü uzar, TCO düşer.'],
                ['question' => 'Opsiyonel aksesuarlarla konfigürasyon nasıl özelleştirilir?', 'answer' => 'AGM veya kurşun-asit paketleri, şarj cihazı akımı ve teker setleri operasyon yoğunluğuna göre seçilerek verim optimize edilir.'],
                ['question' => 'Garanti ve satış sonrası destek kapsamında neler sunuyorsunuz?', 'answer' => 'Makine 12 ay, batarya 24 ay garanti altındadır. Satış, servis, kiralama ve yedek parça için İXTİF ekibine 0216 755 3 555 üzerinden ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: JX2-1');
    }
}
