<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JX2_4_SiparisToplama_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'JX2-4')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (JX2-4)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section class="intro">
  <h2>İXTİF JX2-4: Dar Koridorlarda Hızı ve Güveni Yeniden Tanımlayın</h2>
  <p>Depo tavanına doğru yükselirken operatörünüzün her hareketi güvenle desteklensin: İXTİF JX2-4, 192” maksimum çatal yüksekliği, 4.5 mph seyir hızı ve yalnızca iç mekâna özel şasisiyle modern sipariş toplama operasyonlarını akıcı hale getirir. 42” çatal, 21.3” çatal aralığı ve 117.5” sağ açılı istif koridoru değeri; raf araları sıkışık, palet akışı yoğun işletmeler için ölçülebilir verimlilik sağlar. Poly tekerlek yapısı gürültüyü düşürürken, rejeneratif fren ve elektromanyetik park sistemi iniş-çıkışlarda kararlılık sunar. Çeşitli batarya seçenekleri—AGM, lityum ve kurşun-asit—farklı vardiya ve şarj politikalarına uyumlu bir enerji mimarisi oluşturur.</p>
</section>
<section class="technical">
  <h3>Teknik Güç ve Boyutsal Uyum</h3>
  <p>İXTİF JX2-4’ün platform mimarisi, 2,200 lb çatal kapasitesi ve 300 lb operatör taşıma değeriyle ciddi istif yoğunluklarında dahi kararlı performans üretir. 53.7” dingil mesafesi ile 65” dönüş yarıçapının birleşimi, 36” toplam genişlik ve 108.3” toplam uzunlukla desteklenerek dar koridorlarda kusursuz manevra kabiliyeti oluşturur. 95.5” kapalı direk yüksekliğinden başlayıp 295” tam yükseltilmiş mast seviyesine kadar uzanan dikey geometri, 192” maksimum çatal yüksekliğine güvenle erişir. 25.6/31.5 fpm kaldırma ve 31.5/35.4 fpm indirme hızları, farklı yük profillerinde tutarlı zamanlamalar sağlar. AC sürüş kontrolü ve 4 kW sürüş motoru, 3 kW kaldırma motoruyla birlikte hızlanma ve duruşlarda akıcı bir his sunar. 70 dB(A) sürücü kulak seviyesi, yoğun vardiyalarda operatör yorgunluğunu azaltmaya yardımcı olur.</p>
  <p>Enerji tarafında JX2-4; 24V mimaride 340Ah kurşun-asit veya 224Ah AGM batarya konfigürasyonlarını destekler. Çeşitli şarj cihazı seçenekleri (35A/40A) ile eşleştirildiğinde, farklı vardiya düzenlerinde sürdürülebilir kullanılabilirlik sağlanır. Poly tekerlekler (10.25 x 5 in sürüş, 6.5 x 4.7 in yük ve 3 x 2 in denge) zeminle yüksek temas kalitesi ve titreşim kontrolü sunarak ürün ve operatör konforunu iyileştirir. Stand alanındaki ergonomi, 89.4” bölüm yüksekliği ve 198” yükseltilmiş ayakta yükseklik değeriyle geniş bir çalışma çevresi sağlar.</p>
</section>
<section class="operations">
  <h3>Operasyonel Verimlilik ve Güvenlik</h3>
  <p>JX2-4, yalnızca düz ve pürüzsüz iç mekân zeminlerinde işletilmek üzere optimize edilmiştir. Bu kısıtlı operasyon alanı sayesinde şasi, direksiyon ve fren sistemi sipariş toplamanın ritmine uyacak şekilde dengelenmiştir. Rejeneratif servis freni inişlerde enerjiyi geri kazanırken, elektromanyetik park freni duruşlarda stabilite sağlar. 117.5” sağ açılı istif koridoru değeri, tipik 48” paletle rahat yanal yaklaşmayı mümkün kılar. 42” çatal uzunluğu ve 21.3” çatal aralığı; standart kasa ve kutu ölçüleriyle uyumlu, tekrarlanabilir toplama hareketleri üretir.</p>
</section>
<section class="energy">
  <h3>Enerji Seçenekleri ve Vardiya Planlaması</h3>
  <p>Farklı depolarda enerji politikaları değişkenlik gösterir. AGM, hızlı şarjla kesintisizliği gözeten operasyonlar için bakım gerektirmeyen bir profile sahiptir. Kurşun-asit, 340Ah kapasitesiyle uzun süreli kullanımda yaygın ve ekonomiktir. Lityum alternatifi, fırsat şarjıyla çevrim içi kalmayı hedefleyen işletmeler için düşünülmelidir. 35A ve 40A şarj seçenekleriyle birlikte; molalarda dolum, vardiya bitişinde tam şarj veya ara şarj stratejileri planlanabilir. 700 lb batarya kütlesi, şasinin denge dağılımına katkı sağlayarak yüksek kaldırma seviyelerinde platform hissini dengeler.</p>
</section>
<section class="closing">
  <h3>Neden JX2-4?</h3>
  <p>Dar koridorlar, ağırlık limitleri ve yoğun sipariş hacimleri JX2-4’ün uzmanlık alanıdır. İç mekân kullanımıyla çelişmeyen bir hız (4.5 mph), güvenli fren mimarisi ve raf derinliklerine erişen 192” kaldırma yüksekliği, özellikle e-ticaret ve 3PL dünyasında tutarlı çevrim süreleri sunar. Teknik detaylar ve uygun konfigürasyon için bize ulaşın: 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2,200 lb (çatal) + 300 lb (operatör)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 340Ah LA veya 24V / 224Ah AGM'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 / 4.5 mph (yük/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '65 in dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => '192” erişim', 'description' => 'Yüksek raflarda güvenli ve tekrarlanabilir sipariş toplama'],
                ['icon' => 'cart-shopping', 'title' => '42” çatal', 'description' => 'Standart 48” palet ve kutu formatlarına uyum sağlar'],
                ['icon' => 'battery-full', 'title' => '24V enerji', 'description' => 'AGM ve kurşun-asit seçenekleri; lityum alternatifi'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli fren', 'description' => 'Rejeneratif servis ve elektromanyetik park sistemi'],
                ['icon' => 'industry', 'title' => 'İç mekân optimizasyonu', 'description' => 'Poly tekerlek ve düz zemin performansı'],
                ['icon' => 'gauge', 'title' => 'Sabit hız', 'description' => '4.5 mph ile dengeli, öngörülebilir çalışma']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret raflarında tekli ürün toplama ve bölge bazlı sevk hazırlığı'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde dar koridorda toplama-teslim rotaları'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde kampanya dönemlerinde yoğun sipariş akışı'],
                ['icon' => 'snowflake', 'text' => 'Soğuk zincir stok alanlarında düşük gürültüyle hareket'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında kırılgan kutu yönetimi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında yüksek lokasyon erişimi'],
                ['icon' => 'flask', 'text' => 'Kimya ambalajlanmış ürünlerinde güvenli üst raf erişimi'],
                ['icon' => 'microchip', 'text' => 'Elektronik küçük parça raflarında seçici toplama']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC sürüş ve rejeneratif fren ile akıcı hız kontrolü'],
                ['icon' => 'arrows-alt', 'text' => '192” erişim ve 95.5” kapalı yükseklik kombinasyonu'],
                ['icon' => 'arrows-turn-right', 'text' => '65” dönüş yarıçapı ve 36” genişlik ile koridor uyumu'],
                ['icon' => 'battery-full', 'text' => '24V platformda çoklu batarya ve şarj opsiyonu'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik park freniyle güvenli duruş'],
                ['icon' => 'star', 'text' => '70 dB(A) seviyesinde konforlu çalışma']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL Lojistik'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya Depoları'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik Parça'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya Depoları'],
                ['icon' => 'hammer', 'text' => 'Yapı Market/DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık/Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım Lojistiği'],
                ['icon' => 'paw', 'text' => 'Evcil Ürünleri Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay fabrika garantisine tabidir. Li-Ion batarya modülleri ise satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim kaynaklı hataları kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'charging-station', 'name' => '35A/40A Şarj Ünitesi', 'description' => '24V mimari ile uyumlu akıllı şarj; vardiya aralarında hızlı dolum stratejileri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'AC Sürüş Kontrol Modülü', 'description' => 'Platform ile uyumlu fabrika çıkışlı sürüş kontrol donanımı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Poly Tekerlek Seti', 'description' => 'İç mekân zeminde sessiz ve düşük yuvarlanma direnci sağlayan set.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'grip-lines-vertical', 'name' => 'Fork Koruma Bariyeri', 'description' => 'Kutu ve paketlerin çatalda stabil kalması için mekanik bariyer.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Dar koridorlarda asgari dönüş yarıçapı kaç inçtir ve hangi raf mesafelerinde idealdir?', 'answer' => 'JX2-4, 65 inç dönüş yarıçapı ve 36 inç genişliğiyle sık raf aralarında manevra sunar. 117.5 inç sağ açılı istif koridoru değeri tipik 48 inç paletle uyumludur.'],
                ['question' => 'Maksimum kaldırma yüksekliği ve kapalı direk yüksekliği birlikte nasıl planlanmalıdır?', 'answer' => '192 inç maksimum çatal yüksekliği ve 95.5 inç kapalı direk yüksekliği, koridor girişleri ve raf üst kotları dikkate alınarak planlanmalıdır. 295 inç mast tam yüksekliktir.'],
                ['question' => 'Enerji mimarisinde hangi batarya seçenekleri mevcuttur ve kullanım senaryoları nelerdir?', 'answer' => '24V platformda 340Ah kurşun-asit, 224Ah AGM ve lityum alternatifi bulunur. Uzun vardiya, fırsat şarjı ve bakım gereksinimine göre tercih yapılır.'],
                ['question' => 'Seyir ve kaldırma- indirme hızları operasyon temposunu nasıl etkiler?', 'answer' => '4.5/4.5 mph seyir ve 25.6/31.5 fpm kaldırma, 31.5/35.4 fpm indirme hızları sabit ve öngörülebilir çevrim süreleri sağlar.'],
                ['question' => 'Frenleme sistemi güvenliği nasıl destekler ve eğimli zeminlerde kullanım nasıldır?', 'answer' => 'Rejeneratif servis freni enerji verimliliği sağlar; elektromanyetik park freni stabil duruş sunar. İç mekân, düz ve pürüzsüz zemin kullanımına yöneliktir.'],
                ['question' => 'Tekerlek ve iz genişliği seçimlerinin ürün ve operatör konforuna etkisi nedir?', 'answer' => 'Poly tekerlek seti, 30.3/22.4 in iz genişliğiyle dengeli ve sessiz hareket sunar; titreşim kontrolü ve raf koruması iyileşir.'],
                ['question' => 'Toplam uzunluk ve çatala kadar uzunluk değerleri palet yaklaşımını nasıl etkiler?', 'answer' => '108.3 in toplam uzunluk ve 66.1 in çatala kadar uzunluk, raf önünde hizalanmayı kolaylaştırır; standart 48 in palet kullanımına uygundur.'],
                ['question' => 'Operatör bölmesi ölçüleri yüksek pozisyonda görüşü nasıl etkiler?', 'answer' => '89.4 in bölüm yüksekliği ve 198 in ayakta yüksekliğin sağladığı çalışma alanı, üst raflarda kontrollü görüş açıları sunar.'],
                ['question' => 'Gürültü seviyesi ve titreşim profili vardiya konforu açısından nasıldır?', 'answer' => '70 dB(A) seviyesinde ve poly tekerleklerle düşük titreşimli yapı, uzun vardiyalarda konforu artırır.'],
                ['question' => 'Bakım gereksinimleri ve batarya değişim/şarj stratejileri nasıl planlanmalı?', 'answer' => 'AGM bakım gerektirmez; kurşun-asit ekonomik ve yaygındır; lityum fırsat şarjıyla esneklik sağlar. Şarj akımları 35A/40A seçeneklidir.'],
                ['question' => 'İç mekân kullanım kısıtı hangi güvenlik standartlarıyla ilişkilidir?', 'answer' => 'Yalnızca düz ve pürüzsüz zeminlerde kullanım tavsiyesi, şasi dengelemesi ve fren mimarisi ile birlikte güvenli istif performansını hedefler.'],
                ['question' => 'Garanti kapsamı nedir ve satış-sonrası destek için kiminle iletişime geçerim?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. Satış, servis ve parça desteği için İXTİF ile iletişim: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("✅ Detailed güncellendi: JX2-4");
    }
}
