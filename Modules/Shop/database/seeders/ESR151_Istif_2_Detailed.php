<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESR151_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ESR151')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: ESR151'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>ESR151: Sürüş Platformlu Kompakt İstifte Güven ve Verim</h2>
  <p>İXTİF ESR151, depo operasyonlarında hız, güven ve ergonomiyi aynı gövdede toplayan 1.5 ton kapasiteli sürücülü istif makinesidir. 500 mm yük merkezine optimize edilen şasi dengesi, 1488 mm dönüş yarıçapı ve 850 mm gövde genişliğiyle dar koridorlarda bile akıcı bir akış sağlar. Günün yoğun saatlerinde operatörün ihtiyacı olan konforu katlanır ride-on pedal ile sunar; pedal kapandığında ise kaplumbağa modunda hassas ve kontrollü manevra mümkündür. 24V/15A entegre şarj cihazı saha içinde mobil şarj esnekliği vererek altyapı bağımlılığını azaltır.</p>
</section>
<section>
  <h3>Teknik Güç ve Güvenlik</h3>
  <p>ESR151, 0.75 kW tahrik ve 2.2 kW kaldırma motoru kombinasyonuyla dengeli performans sunar. 4.0/4.5 km/s seyir hızları, 0.10/0.14 m/s kaldırma ve 0.10/0.10 m/s indirme hızları ile palet akışını ritmik tutar. 2×12V/105Ah akü düzeni (24V toplam) operasyonların gün boyu verimli sürmesini sağlar; DC sürüş kontrolü basitlik ve servis kolaylığı kazandırır. Güvenlik tarafında, mast arkasındaki metal ağ düşebilecek yüklere karşı operatörü korur. Çatallar 720 mm üzerine çıktığında otomatik düşük hız moduna geçilerek istifleme sırasında denge ve kontrol artırılır. GB/T26949.1–2012 endüstriyel araç stabilite doğrulamasını geçen şasi; 1215 mm dingil mesafesi ve optimize edilmiş teker izi ile ağırlık dağılımını ideal seviyede tutar.</p>
  <p>Poliüretan tahrik ve yük tekerleri zemine dosttur; düşük titreşim ve sessiz çalışma (74 dB(A)) sağlar. 1832 mm toplam uzunluk ve 682 mm yük yüzüne kadar uzunluk (l2) raf önü yaklaşmalarını kolaylaştırır. 60/170/1150 mm çatal ölçüleri, 570 mm çatallar arası mesafe ile standart EUR palet uyumu sunar. Standart direk seçenekleri 2516/2716/3016/3316 mm aralığında farklı raf yüksekliklerine uygun konfigürasyon sağlar.</p>
</section>
<section>
  <h3>Sonuç: Akıcı Akış, Kolay Bakım</h3>
  <p>ESR151, elektromanyetik servis freni, mekanik direksiyon ve erişilebilir bakım noktalarıyla sahip olma maliyetini düşük tutar. Entegre şarj cihazı ve dayanıklı PU tekerler, mobilite ve süreklilik gerektiren modern depo operasyonlarında pratiklik sağlar. Yüksek güvenlik seviyeleri, operatör konforu ve kompakt şasi sayesinde, yoğun vardiya akışları için etkili bir çözüm sunar. Teklif ve demo için arayın: 0216 755 3 555</p>
</section>
',], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg (Q), 500 mm yük merkezi'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V (2×12V) / 105Ah, entegre 24V/15A şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yükle / yüksüz)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => '1488 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Entegre Şarj Esnekliği', 'description' => '24V/15A dahili şarj ile park alanında veya rampada pratik dolum'],
                ['icon' => 'shield-alt', 'title' => 'Otomatik Düşük Hız', 'description' => '720 mm üstü istifte hız kısıtlaması ile dengeli ve güvenli kaldırma'],
                ['icon' => 'star', 'title' => 'Stabilite Doğrulaması', 'description' => 'GB/T26949.1–2012 testlerini geçen şasi mimarisi'],
                ['icon' => 'briefcase', 'title' => 'Katlanır Pedal', 'description' => 'Uzun mesafede konfor, dar alanda kapatma ile hassas sürüş'],
                ['icon' => 'cart-shopping', 'title' => 'PU Tekerler', 'description' => 'Düşük titreşim ve zemine zarar vermeyen sessiz hareket'],
                ['icon' => 'bolt', 'title' => '2.2 kW Kaldırma Motoru', 'description' => 'Sürekli istif akışı ve dengeli performans']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarında dar koridor raf arası istif ve transfer'],
                ['icon' => 'box-open', 'text' => 'E-ticaret sipariş çıkış alanında palet besleme ve ara stok'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezinde raf önü replenishment işleri'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda antrepolarına düşük titreşimli giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ürün istifi ve güvenli taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında dar alan yaklaşmaları'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP paletleri kısa mesafe taşımaları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG yükleme öncesi tampon alan yönetimi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Kompakt şasi ve 1488 mm dönüş yarıçapı ile yüksek manevra'],
                ['icon' => 'battery-full', 'text' => 'Entegre şarj ve 24V/105Ah akü ile yüksek kullanım süresi'],
                ['icon' => 'arrows-alt', 'text' => 'Standart direk seçenekleri ile farklı raf yüksekliklerine uyum'],
                ['icon' => 'shield-alt', 'text' => '720 mm üstünde otomatik düşük hız ve elektromanyetik fren'],
                ['icon' => 'star', 'text' => 'GB/T26949.1–2012 stabilite testi ile doğrulanmış güven'],
                ['icon' => 'briefcase', 'text' => 'Katlanır pedal sayesinde operatör konforu ve güvenli kontrol']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel Üretim Tesisleri'],
                ['icon' => 'briefcase', 'text' => 'Posta & Kargo Aktarma'],
                ['icon' => 'building', 'text' => 'İthalat/İhracat Depoları'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen Lojistiği'],
                ['icon' => 'box-open', 'text' => 'Ambalaj & Materyal Depoları'],
                ['icon' => 'warehouse', 'text' => 'Yarı Mamul Stok Alanları'],
                ['icon' => 'store', 'text' => 'DIY/Yapı Market Dağıtımı'],
                ['icon' => 'cart-shopping', 'text' => 'İçecek ve Şişeleme Lojistiği'],
                ['icon' => 'industry', 'text' => 'Ağır Sanayi Yardımcı Depolar'],
                ['icon' => 'briefcase', 'text' => '3. Parti Yedek Parça Merkezleri'],
                ['icon' => 'building', 'text' => 'Organize Sanayi Bölgesi Depoları'],
                ['icon' => 'box-open', 'text' => 'Kargo Konsolidasyon Merkezleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion veya kurşun asit batarya modülleri satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Dahili 24V/15A Şarj Cihazı', 'description' => 'Makine üzerinde entegre şarj ile sahada priz bulunan her noktada dolum imkânı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cart-shopping', 'name' => 'PU Teker Seti (Standart)', 'description' => 'Zemine dost, sessiz ve düşük titreşimli poliüretan tahrik/yük tekerleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Yüksek Kapasiteli Akü', 'description' => 'Daha uzun vardiyalar için artırılmış kapasite akü paketi (uyumlu konfigürasyon).', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Yan Destekler', 'description' => 'İstif stabilitesi için opsiyonel yan destek donanımı (bazı konfigürasyonlarda).', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ESR151 hangi yük kapasitesini ve yük merkezini destekler?', 'answer' => 'Makine 1500 kg kapasite ve 500 mm yük merkezine göre derecelendirilmiştir; bu değerler stabilite ve performans açısından optimum noktayı sunar.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti nasıldır, minimum dönüş yarıçapı nedir?', 'answer' => '1488 mm dönüş yarıçapı ve 850 mm gövde genişliği ile tipik depo koridorlarında rahat dönüş sağlar; yükleme alanlarında yaklaşma mesafesi kısalır.'],
                ['question' => 'Hız ve kaldırma/indirme performans değerleri operasyonlara yeterli mi?', 'answer' => '4.0/4.5 km/s sürüş, 0.10/0.14 m/s kaldırma ve 0.10/0.10 m/s indirme hızlarıyla palet akışı temposunu dengeler; ürün hasar riskini düşürür.'],
                ['question' => 'Batarya teknik özellikleri ve saha içi şarj seçenekleri nelerdir?', 'answer' => '2×12V/105Ah akü (24V) dahili 24V/15A şarj cihazı ile desteklenir; altyapıya bağımlılığı azaltarak vardiya aralarında esnek şarj sağlar.'],
                ['question' => 'Güvenlik için otomatik düşük hız nasıl devreye girer ve faydası nedir?', 'answer' => 'Çatallar 720 mm yüksekliği aştığında hız otomatik düşer; istif sırasında devrilme ve yük salınımı riskini azaltır, hassas konumlandırma kolaylaşır.'],
                ['question' => 'Hangi tekerlek malzemesi kullanılır ve zemin dostu mudur?', 'answer' => 'Poliüretan sürüş ve yük tekerleri sessiz, düşük titreşimli çalışma sunar ve endüstriyel zeminlerde aşınmayı minimuma indirir.'],
                ['question' => 'Sürüş kontrol tipi ve fren sistemi bakım gereksinimini nasıl etkiler?', 'answer' => 'DC sürüş kontrolü ve elektromanyetik servis freni basit, güvenilir ve düşük bakım gerektiren bir yapı sağlar.'],
                ['question' => 'Direk yükseklik seçenekleri raf yapılarına nasıl uyum sağlar?', 'answer' => 'Standart direklerde 2516/2716/3016/3316 mm aralıkları mevcuttur; tesis raf yüksekliklerine göre uygun konfigürasyon seçilebilir.'],
                ['question' => 'Operatör konforu için hangi ergonomik unsurlar öne çıkıyor?', 'answer' => 'Katlanır sürüş pedalı, ayakta kullanım ergonomisi ve düşük titreşimli tekerler uzun vardiyalarda yorgunluğu azaltır, kontrolü artırır.'],
                ['question' => 'Gürültü seviyesi kaç dB(A) ve kapalı alanlarda kullanım uygun mu?', 'answer' => '74 dB(A) seviyesinde çalışır; kapalı depolarda iletişimi bozmayacak, iş güvenliği sınırları içinde sessiz bir operasyon sağlar.'],
                ['question' => 'Bakım erişilebilirliği ve yedek parça sürekliliği açısından durum nedir?', 'answer' => 'Basit mekanik direksiyon, erişilebilir komponent yerleşimi ve yaygın sarf malzemeleriyle planlı bakım süreçleri kısa sürer.'],
                ['question' => 'Satış sonrası destek, garanti koşulları ve iletişim bilgileri nelerdir?', 'answer' => 'Makine için 12 ay, batarya için 24 ay garanti sunulur. Destek ve teklif için İXTİF 0216 755 3 555 numarasından bize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: ESR151');
    }
}
