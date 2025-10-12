<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RSB202_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'RSB202')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: RSB202'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İXTİF RSB202: Platformlu Li-Ion istifte çeviklik ve hassasiyet</h2><p>Orta ölçekli depoların ritmi yüksektir; palet akışı, rampa çevrimi ve raf arası sirkülasyon hiç bitmez. İXTİF RSB202, 2000 kg kapasitesi ve kompakt şasisiyle bu temponun içine zahmetsizce uyum sağlar. Elektronik güç direksiyonu sayesinde kumanda koluna çok az kuvvet uygulayarak ani manevra yapabilir, katlanır amortisörlü platform ve kol korumalarıyla gün boyu yorgunluk hissetmeden çalışabilirsiniz. Entegre şarj cihazına sahip 24V/100Ah Li-Ion akü, fırsat şarjı sayesinde vardiya arasında bekleme süresini kısaltır; oransal kaldırma fonksiyonu ise cam, kozmetik veya elektronik gibi hassas yükleri raf seviyesinde milimetrik olarak konumlandırmanıza imkan tanır.</p></section><section><h3>Teknik</h3><p>RSB202, 600 mm yük merkezinde 2000 kg’ı güvenle taşır ve 1.6 kW AC sürüş motoru ile yüksüz 6 km/s, yüklü 5.5 km/s seyir hızına ulaşır. 3.0 kW kaldırma motoru, yüklü 0.12 m/s ve yüksüz 0.2 m/s kaldırma hızları sunar. 850 mm toplam genişlik ve 1620 mm dönüş yarıçapı dar koridorlar için idealdir; 65×185×1150 mm çatallar ve 685 mm çatal dıştan dışa ölçüsü EUR palet uyumluluğu sağlar. 1900 mm kapalı direk yüksekliği ve 3470 mm açık direk yüksekliğiyle operatör görüş alanı korunurken, 25 mm yerden yükseklik eşik ve rampa geçişlerinde denge sağlar. 24V/100Ah Li-Ion akü (yaklaşık 40 kg) entegre 30A şarj cihazı ile gelir; AC sürüş kontrolü, elektromanyetik servis freni ve 74 dB(A) ses seviyesi ile güvenli ve konforlu bir çalışma sunar. Standart PU tekerlekler, 8/16% eğim performansı ve EPS ile yük altında dahi hafif direksiyon tepkisi, istif ve taşımanın aynı makinede verimli bir şekilde birleşmesini sağlar.</p></section><section><h3>Sonuç</h3><p>Hızlı, hassas ve düşük bakım ihtiyacı olan bir istif makinesi arıyorsanız, RSB202 depoda akışı hızlandırır, operatör konforunu artırır ve toplam sahip olma maliyetini düşürür. Teknik bilgi ve uygun konfigürasyon için 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 100Ah Li-Ion, entegre şarj'],
                ['icon' => 'gauge', 'label' => 'Seyir Hızı', 'value' => '5.5 / 6 km/s (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1620 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'layer-group', 'title' => 'Rijit H-profil direk', 'description' => 'Yüksekte bile dengeli istifleme ve titreşim kontrolü'],
                ['icon' => 'hand', 'title' => 'Elektronik direksiyon', 'description' => 'EPS ile hafif, hassas ve tekrarlanabilir manevra'],
                ['icon' => 'arrows-alt', 'title' => 'Oransal kaldırma', 'description' => 'Hassas yüklerde milimetrik raf hizalama'],
                ['icon' => 'battery-full', 'title' => 'Li-Ion + entegre şarj', 'description' => 'Fırsat şarjı ile düşük kesinti ve sıfır bakım'],
                ['icon' => 'couch', 'title' => 'Konforlu platform', 'description' => 'Katlanır, darbe emici platform ve kol korumaları'],
                ['icon' => 'shield-alt', 'title' => 'Güvenlik sistemi', 'description' => 'Elektromanyetik fren ve AC sürüş kontrolü']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Orta ölçekli depolarda vardiya içi besleme ve raf arası taşıma'],
                ['icon' => 'box-open', 'text' => 'E-ticaret outbound alanında hızlı yük konsolidasyonu'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerde mağaza bazlı ayrıştırma ve istifleme'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış işlemleri'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik operasyonlarında hassas koli istifi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında dar koridor dönüşleri'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP ve yarı mamul akışı'],
                ['icon' => 'flask', 'text' => 'Kimyasal ürün depolarında güvenli elleçleme']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '1.6 kW AC sürüş + 3 kW kaldırma motoru ile sınıfında güçlü performans'],
                ['icon' => 'battery-full', 'text' => '24V/100Ah Li-Ion paket ve entegre şarj ile düşük işletme maliyeti'],
                ['icon' => 'arrows-alt', 'text' => '850 mm genişlik ve 1620 mm dönüş yarıçapı ile çevik şasi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve EPS ile yüksek güvenlik ve kontrol'],
                ['icon' => 'star', 'text' => 'Oransal kaldırma ile hassas ürün koruması ve kaliteli istif']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal ve Tehlikesiz Kimya'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım, Bahçe ve Zirai Lojistik'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti normal kullanım koşullarında üretim kaynaklı hataları kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı', 'description' => '24V Li-Ion akü için 30A çıkışlı dahili şarj ünitesi; kablo yönetimi kolaydır.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'circle-notch', 'name' => 'PU Tekerlek Seti', 'description' => 'Sessiz ve iz bırakmayan standart poliüretan tekerlek konfigürasyonu.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Uzatılmış Akü Paketi', 'description' => 'Daha uzun vardiyalar için artırılmış kapasite Li-Ion modül konfigürasyonu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Tandem Rulolu Çatal Tekerleri', 'description' => 'Pürüzlü zeminlerde daha dengeli giriş/çıkış için tandem uç rulolar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'RSB202 günlük yoğun operasyonda kaç saat kesintisiz çalışabilir?', 'answer' => '24V/100Ah Li-Ion akü fırsat şarjı ile vardiya aralarında hızlıca takviye edilebilir. Tipik iki vardiya düzeninde molalarda şarjla tüm gün operasyon sağlanır.'],
                ['question' => 'Oransal kaldırma hangi durumlarda avantaj sağlar ve nasıl çalışır?', 'answer' => 'Hassas ürünlerde raf hizasında hız kademeli şekilde düşer; kumanda hassasiyeti artar. Cam, kozmetik ve elektronik gibi dar toleranslı istiflerde çarpmayı önler.'],
                ['question' => 'Dar koridorlarda dönüş kabiliyeti nedir, en küçük dönüş alanı?', 'answer' => 'Makinenin dönüş yarıçapı 1620 mm’dir. 850 mm gövde genişliği ile 2430–2530 mm koridorlarda güvenle manevra edilir.'],
                ['question' => 'Yüklü ve yüksüz seyir hızları kaçtır; rampada davranış nasıldır?', 'answer' => 'Seyir hızları yüklü 5.5 km/s, yüksüz 6 km/s; eğim performansı yüklü %8, yüksüz %16’dır. Elektromanyetik fren rampa duruşlarında güven verir.'],
                ['question' => 'Akü ve şarj sistemi bakım gerektirir mi; şarj akımı nedir?', 'answer' => 'Li-Ion sistem bakım gerektirmez; entegre şarj cihazı 30A çıkış verir. Fırsat şarjı desteğiyle ara molalarda enerji takviyesi sağlanır.'],
                ['question' => 'Standart çatal ölçülerine hangi paletler uygundur?', 'answer' => '65×185×1150 mm çatal ve 685 mm dıştan dışa ölçü, EUR palet ve yaygın 800×1200–1000×1200 formatlarıyla uyumludur.'],
                ['question' => 'Gürültü seviyesi ve operatör konfor özellikleri nelerdir?', 'answer' => 'Kulak seviyesinde 74 dB(A) ölçülür. Katlanır, darbe emici platform ve yan kol korumaları uzun vardiyalarda konfor sağlar.'],
                ['question' => 'Yedek parça ve sarf tüketimi açısından TCO nasıl etkilenir?', 'answer' => 'AC sürüş ve Li-Ion akü yapısı fırça, asit ve bakım ihtiyacını azaltır. Daha az duruş süresi toplam sahip olma maliyetini düşürür.'],
                ['question' => 'Kaldırma ve indirme hızları hangi operasyonlara uygundur?', 'answer' => 'Yüklü 0.12 m/s kaldırma ve 0.3 m/s indirme hızları; güvenli ve kontrollü istifleme gerektiren raf sistemleri için dengeli ayarlanmıştır.'],
                ['question' => 'Makine ağırlığı ve dingil yükleri zemin kaplamasına etkiler mi?', 'answer' => '940 kg servis ağırlığı ve dağıtılmış dingil yükleri, endüstriyel zemin standardına uygundur; zemin izinleri kontrol edilmelidir.'],
                ['question' => 'Opsiyonel mast yükseklikleri ve görüş alanına etkileri nelerdir?', 'answer' => '2500–3900 mm aralığında seçenekler mevcuttur. Kapalı/açık direk boyları ve serbest kaldırma değerleri raf sisteminize göre seçilir.'],
                ['question' => 'Garanti kapsamı ve satış-sonrası destek nasıl sağlanır?', 'answer' => 'Makine 12 ay, Li-Ion akü 24 ay garantilidir. Montaj, eğitim, bakım ve yedek parça için İXTİF 0216 755 3 555 üzerinden destek alabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: RSB202');
    }
}
