<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RSC202_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'RSC202')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (RSC202)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF RSC202: Dar Koridorların Güçlü ve Hassas İstifçisi</h2>
  <p>İXTİF RSC202, 2.0 tonluk nominal kapasiteyi kompakt bir gövdede birleştirerek yoğun depo operasyonlarının ritmine uyum sağlar. 1915 mm dönüş yarıçapı, 900 mm şasi genişliği ve 116 mm yerden yükseklik; rampalarda, bozuk zemin geçişlerinde ve dar raf aralarında güvenle hareket etmenizi sağlar. Oransal kaldırma sistemi ve hassas mast eğim kabiliyeti sayesinde paletleri çok katlı raflarda nazikçe konumlandırır, ürün zararını en aza indirir ve operatörün işini kolaylaştırır. 24V/280Ah enerji sistemi ve AC tahrik mimarisi, hızlı tepki ve dengeli çekiş üretirken, elektromanyetik fren güvenliği standart olarak sunulur.</p>
</section>
<section>
  <h3>Teknik Güç ve Kontrol</h3>
  <p>RSC202, 500 mm yük merkezinde 2000 kg yük taşır. 3.3 kW AC sürüş motoru ve 3.0 kW kaldırma motoru, yük altında bile akıcı hızlanma ve stabil kaldırma performansı sunar. 5.5/6 km/s seyir hızı aralığı ve 0.10/0.16 m/s kaldırma hızları, vardiya içi çevrim sürelerini kısaltır; 0.19/0.16 m/s indirme hızları ise hassas yerleştirmede optimum kontrol sağlar. Direk eğimi 1.5°/7° ile palete yaklaşım ve konumlama kolaylaşır. Standart 3000 mm kaldırma ile 2118 mm kapalı direk yüksekliği ve 3915 mm açık yükseklik dengeli bir görüş alanı sunar; 2600–5000 mm seçenekleri operasyonunuza uygun direk kombinasyonunu kurmanıza imkân verir. 40×122×1070 mm çatal ölçüsü, 2A sınıfı 800 mm taşıyıcı ile birlikte geniş palet uyumluluğu sağlar. 787 mm arka iz genişliği, 1487 mm dingil mesafesi ve 215 mm yük mesafesi (x) ile şasi dengesi korunur.</p>
  <p>Poliüretan tekerlekler sessiz ve titreşimi düşük bir sürüş sağlarken, 116 mm merkezde yer açıklığı ve 80 mm direk altı yükseklik, eşikler ve düzensiz zeminlerde takılmaları azaltır. Elektronik direksiyon tasarımı, yumuşak tepki ve tekrarlanabilir manevra kabiliyeti sunar; operatör kulak seviyesinde 74 dB(A) ses düzeyiyle konforlu bir çalışma ortamı elde eder. Akü sistemi 24V/280Ah kapasite ve 190 kg modül ağırlığı ile uzun vardiyaları destekler; yerleşik 30 A şarj cihazı seçeneği günlük planlara uyum sağlar. Li-ion akü opsiyonu, hızlı şarj ve yüksek çevrim ömrü ile TCO&#39;yu düşürür.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>RSC202, kompakt ölçüler, hassas kontrol ve ölçeklenebilir direk seçenekleri ile depo akışını güvenle hızlandırır. Ekiplerinize zaman kazandıran, ürün zedelenmesini azaltan ve dar alan verimliliğini artıran bir istif çözümü arıyorsanız doğru yerdesiniz. Teknik detay, fiyat ve yerinde demonstrasyon için hemen arayın: <strong>0216 755 3 555</strong>.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 280Ah (Li-ion opsiyon)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '5.5 / 6 km/s (yük/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1915 mm yarıçap']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'microchip', 'title' => 'Oransal Kaldırma', 'description' => 'Raf seviyelerinde hassas ve nazik yerleştirme sağlar.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Şasi', 'description' => '1915 mm dönüş ve 900 mm genişlikle dar alan başarısı.'],
                ['icon' => 'industry', 'title' => 'Yerden Yükseklik', 'description' => '116 mm açıklık ile eşik ve zemin geçişlerinde akıcılık.'],
                ['icon' => 'bolt', 'title' => 'Güçlü Aktarma', 'description' => '3.3 kW AC sürüş ve 3.0 kW kaldırma motoru.'],
                ['icon' => 'shield-alt', 'title' => 'Emniyet Freni', 'description' => 'Elektromanyetik servis/park freni standarttır.'],
                ['icon' => 'hand', 'title' => 'Elektronik Direksiyon', 'description' => 'Yumuşak tepki ve tekrarlanabilir manevra.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yoğun 3PL depolarında çok seviyeli raf istifleme'],
                ['icon' => 'box-open', 'text' => 'E-ticaret merkezlerinde çapraz sevkiyat ve toplama'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde rampa besleme ve replenishment'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış hatlarında kontrollü taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ve nazik istif'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında ağır kasaların konumlandırılması'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim koli paletleme operasyonları'],
                ['icon' => 'industry', 'text' => 'WIP alanlarında yarı mamul akışı ve hat besleme']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC sürüş ve güçlü kaldırma motorlarıyla yük altında stabil performans'],
                ['icon' => 'battery-full', 'text' => '24V/280Ah kapasite ve Li-ion opsiyonu ile uzun vardiya erişimi'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 1915 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve elektronik direksiyon ile güvenli kullanım'],
                ['icon' => 'layer-group', 'text' => '2600–5000 mm direk alternatifleriyle esnek yapılandırma'],
                ['icon' => 'star', 'text' => 'Düşük gürültü seviyesi ve konforlu operatör deneyimi']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Ürün Depoları'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve Hırdavat (DIY)'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Dahili Şarj Cihazı 30A', 'description' => 'Günlük vardiya planına uygun, cihaz üzerinde güvenli şarj kolaylığı sağlar.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Katlanır Platform ve Kollar', 'description' => 'Uzun mesafelerde operatör konforu ve hız için katlanır sürüş platformu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'certificate', 'name' => 'Uyarı ve Mavi Işık Paketi', 'description' => 'Yaya güvenliği için uyarı lambası ve mavi ikaz ışığı seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'microchip', 'name' => 'Telematik Modülü', 'description' => 'Kullanım saatleri ve filo takibi için uzaktan izleme modülü.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'bolt', 'name' => 'Li-ion Akü Paketi', 'description' => 'Hızlı şarj ve uzun çevrim ömrü ile kesintisiz kullanım sağlar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Teker Seti', 'description' => 'Sessiz ve düşük titreşimli sürüş için yüksek kalite poliüretan tekerler.', 'is_standard' => true, 'is_optional' => false, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '2000 kg kapasite hangi yük merkezinde garanti edilir?', 'answer' => 'Nominal kapasite 500 mm yük merkezi içindir. Merkez uzaklığı arttıkça taşıma kapasitesi doğal olarak düşer; direk grafiklerine göre değerlendirilmelidir.'],
                ['question' => 'Dar alanlarda manevra performansı nasıldır, minimum dönüş yarıçapı nedir?', 'answer' => 'RSC202, 1915 mm dönüş yarıçapına sahiptir. 900 mm genişlik ve elektronik direksiyon kombinasyonu dar raf aralarında akıcı manevra sunar.'],
                ['question' => 'Standart direk ve serbest kaldırma değerleri hangi kombinasyonlarda sunulur?', 'answer' => 'Standart direklerde 2600–3900 mm arası 150 mm serbest kaldırma bulunur. Serbest direkli (FEM) seçeneklerde 4000–5000 mm aralığında daha yüksek serbest kaldırma sağlanır.'],
                ['question' => 'Yerden yükseklik ve rampa geçişlerinde alt vurma riski var mı?', 'answer' => 'Merkezde 116 mm ve direk altında 80 mm açıklık ile eşik ve rampa geçişlerinde alt vurma riski azaltılır; hız eşiklerinde temkinli yaklaşım önerilir.'],
                ['question' => 'Akü kapasitesi ve şarj altyapısı vardiya planlarını nasıl etkiler?', 'answer' => '24V/280Ah kurşun-asit veya Li-ion opsiyonlarıyla tipik bir vardiya desteklenir. 30A dahili şarj cihazı günlük takvime entegre çalışır, Li-ion hızlı şarja uygundur.'],
                ['question' => 'Fren sistemi ve duruş güvenliği hakkında bilgi verir misiniz?', 'answer' => 'Elektromanyetik servis ve park freni standarttır. Eğimsiz zeminlerde güvenli park ve yük altında kontrollü yavaşlama sağlar.'],
                ['question' => 'Çatal ölçüleri ve taşıyıcı sınıfı hangi paletlerle uyumludur?', 'answer' => '40×122×1070 mm çatal ve 2A sınıfı 800 mm taşıyıcı; EUR, ISO ve özel uzunluk opsiyonlarıyla geniş palet uyumu sunar.'],
                ['question' => 'Gürültü ve operatör konforu seviyeleri nedir?', 'answer' => 'Kulak seviyesinde 74 dB(A) ses basıncı ile konforlu çalışma sağlar. Elektronik direksiyon ve PU tekerlekler titreşimi düşürür.'],
                ['question' => 'Eğim kabiliyeti ve hız değerleri yük altında nasıl değişir?', 'answer' => 'Maksimum eğim %5 (yük) / %8 (boş), hız ise 5.5/6 km/s değerlerindedir. Yük ve zemine göre kontrol koluyla kademeli ayarlanır.'],
                ['question' => 'Bakım periyotları ve yedek parça erişimi nasıl planlanır?', 'answer' => 'Elektrikli tahrik ve PU teker yapısı düşük bakım ihtiyacı doğurur. Periyodik kontrollerde teker, fren ve hidrolik sistem gözden geçirilmelidir.'],
                ['question' => 'Opsiyonel güvenlik ekipmanları hangi durumlarda önerilir?', 'answer' => 'Yaya trafiği yoğun sahalarda mavi ikaz, alan uyarı ve buzzer paketleri önerilir. Telemetri ile çarpışma ve kullanım kayıtları izlenebilir.'],
                ['question' => 'Garanti süresi ve servis desteği detayları nelerdir?', 'answer' => 'Makine 12 ay, akü 24 ay. İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: RSC202');
    }
}
