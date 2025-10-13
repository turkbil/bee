<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WSA161i_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'WSA161i')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>WSA161i ile Yoğun Depolarda Hızlı ve Güvenli İstif</h2>
  <p>Günün ilk sevkiyatından gece vardiyasının son satırına kadar, WSA161i depoların ritmine ayak uydurmak için tasarlandı. Başlangıç kaldırma ve destek kolu yapısı sayesinde zemindeki eşitsizlikleri rahatça aşar, rampalardan akıcı geçiş sağlar. Çift kat palet taşıma özelliği yoğun saatlerde hattı beslerken çeviklik kazandırır; kompakt şasi ve kaplumbağa modu dar koridorlarda kontrollü manevralar sunar. Operatör, üst kapaktaki saklama bölmesi ve USB çıkış ile belgelerini düzenli tutar, mobil cihazlarını kolayca güçlendirir.</p>
</section>
<section>
  <h3>Teknik Güç ve Oransal Kontrol</h3>
  <p>WSA161i, 24V/100Ah Li-ion batarya ve standart 24V/30A entegre şarj cihazıyla vardiya aralarında fırsat şarjı yaparak kesintisiz operasyon sağlar. Li-ion teknoloji sıfır bakım ve şarj sırasında sıfır emisyon avantajı getirir; uzun vardiyalar için 24V/205Ah seçenek mevcuttur. 1.6 kW AC tahrik motoru ve 4.5 kW kaldırma motoru, 5.0/5.5 km/s sürüş ve 0.23/0.30 m/s kaldırma hızlarıyla sınıfının iddialı performansını sunar. 1600 kg mast kaldırma (Q1) ve 2000 kg destek kolu kaldırma (Q2) kapasitesi, 600 mm yük merkezinde ağır paletlerde güven verir. 2015 mm kapalı direk yüksekliği ve 2915 mm kaldırma (3.0 m direk) standarttır; 5.5 metreye kadar çok kademeli mast seçenekleri ile yüksek raflarda da etkindir. 1826 mm dönüş yarıçapı, 878 mm yüke kadar uzunluk ve 810 mm genişlik sayesinde 1000×1200 çapraz (Ast 2646 mm) ve 800×1200 boyuna (Ast 2560 mm) koridorlarda rahat çalışır. Oransal kaldırma/indirme kontrolü paleti raf seviyesinde milimetrik yerleştirir, elektromanyetik servis freni inişlerde güveni artırır.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>WSA161i, yoğun 3PL merkezlerinden perakende dağıtım depolarına kadar birçok senaryoda verimlilik artışı sağlar. Çift kat taşıma, hızlı hidrolik tepkiler ve Li-ion sistemin esnek şarj kabiliyeti ile çevrim sürelerini kısaltır, toplam sahip olma maliyetini düşürür. Projenize uygun mast ve batarya konfigürasyonları için ekibimizle görüşün: 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => 'Q1: 1600 kg (mast), Q2: 2000 kg (destek kolu)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 100Ah Li-ion (opsiyon 205Ah), entegre 24V/30A şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => 'Sürüş 5.0/5.5 km/s, kaldırma 0.23/0.30 m/s'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => 'Dönüş yarıçapı 1826 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-ion teknoloji ve entegre şarj', 'description' => 'Fırsat şarjı, sıfır bakım ve şarj sırasında sıfır emisyon ile kesintisiz vardiyalar.'],
                ['icon' => 'bolt', 'title' => 'Çift kat palet taşıma', 'description' => 'Başlangıç kaldırma ve güçlü destek kolları ile throughput’u artırır.'],
                ['icon' => 'shield-alt', 'title' => 'Oransal hidrolik kontrol', 'description' => 'Kaldırma/indirme hareketlerinde hassasiyet ve raf başında güven.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt şasi ve kaplumbağa modu', 'description' => 'Dar koridorlarda düşük hızda kontrollü manevra ve konumlama.'],
                ['icon' => 'cog', 'title' => 'Dikey AC tahrik', 'description' => 'Güçlü çekiş, düşük bakım gereksinimi ve istikrarlı hızlanma.'],
                ['icon' => 'briefcase', 'title' => 'Operatör odaklı detaylar', 'description' => 'Kapakta saklama gözü ve USB çıkış ile günlük kullanım kolaylığı.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde inbound-outbound hatlarında hızlı istifleme ve çift kat taşıma'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında raf arası replenishment ve toplama sonrası geri besleme'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment operasyonlarında yoğun saatlerde sipariş konsolidasyonu'],
                ['icon' => 'snowflake', 'text' => 'Soğuk depo giriş-çıkış rampalarında başlangıç kaldırma ile engel aşma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa ve eşiksiz geçişlerin akıcı yönetimi'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik lojistiğinde hassas ürünlerin yumuşak yerleştirilmesi'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolamada kontrollü ve güvenli raflama süreçleri'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve ara stok istiflemesi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Sınıfında yüksek kaldırma/indirme hızları ile çevrim süresini azaltır'],
                ['icon' => 'battery-full', 'text' => 'Li-ion + entegre şarj kombinasyonu ile esnek enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt boyutlar ve 1826 mm dönüş yarıçapı ile dar koridor kabiliyeti'],
                ['icon' => 'shield-alt', 'text' => 'Oransal kontrol ve elektromanyetik fren ile güvenli operasyon'],
                ['icon' => 'layer-group', 'text' => '3.0–5.5 m arasında geniş mast seçenekleri ve konfigürasyon esnekliği']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Dayanıklı Tüketim ve Beyaz Eşya'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe'],
                ['icon' => 'paw', 'text' => 'Evcil Ürünleri ve Pet Lojistiği']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V/30A Şarj Cihazı', 'description' => 'Şase içine entegre yapı sayesinde harici şarj gerektirmeden hızlı fırsat şarjı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => '205Ah Li-ion Batarya Paketi', 'description' => 'Uzun vardiyalar için artırılmış kapasite ile çalışma süresini genişletir.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Tekerlek Seti (tandem)', 'description' => 'Sessiz çalışma ve düşük zemin aşınması için poliüretan teker seti.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'wrench', 'name' => 'Ek Güvenlik Paketi', 'description' => 'Uyarı etiketi ve ek koruma aksesuarları ile güvenli kullanım seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Çift kat palet taşıma hangi operasyonlarda en çok fayda sağlar?', 'answer' => 'Yoğun sevkiyat saatlerinde hat besleme ve yükleme sırasında iki paleti aynı anda hareket ettirerek çevrim süresini kısaltır ve verimi artırır.'],
                ['question' => 'Başlangıç kaldırma zemindeki eşitsizliklerde nasıl avantaj sağlar?', 'answer' => 'Destek kollarını yükselterek şasiye ek yerden yükseklik kazandırır; rampalar, eşikler ve bozuk zeminlerde takılmayı azaltır.'],
                ['question' => 'Oransal kaldırma sistemi raf başında hassasiyeti nasıl etkiler?', 'answer' => 'Kaldırma ve indirme hareketleri kademeli ve kontrollü ilerler; operatör paleti raf seviyesinde milimetrik konumlandırabilir.'],
                ['question' => 'Standart batarya ve şarj sistemi vardiya planlamasına nasıl uyum sağlar?', 'answer' => '24V/100Ah Li-ion batarya ve 24V/30A entegre şarj fırsat anlarında hızlıca doldurularak gün boyunca kesintisiz çalışmayı destekler.'],
                ['question' => 'Maksimum kaldırma yüksekliği hangi mast seçenekleri ile elde edilir?', 'answer' => '3.0 m standarttır; 2-/3-kademeli mastlarla 4.0, 4.5, 5.0 ve 5.5 metre seçenekleri mevcuttur.'],
                ['question' => 'Dönüş yarıçapı ve gövde ölçüleri dar koridorlarda yeterli midir?', 'answer' => '1826 mm dönüş yarıçapı, 878 mm yük yüzüne uzunluk ve 810 mm genişlik ile 2.5–2.7 m koridorlarda rahat manevra olanağı sunar.'],
                ['question' => 'Sürüş ve kaldırma motor değerleri operasyon hızını nasıl etkiler?', 'answer' => '1.6 kW AC sürüş ve 4.5 kW kaldırma motoru hızlı ivmelenme, akıcı sürüş ve yüksek kaldırma hızları sağlar.'],
                ['question' => 'Gürültü seviyesi ve fren sistemi operatör konforuna katkı sağlar mı?', 'answer' => '74 dB(A) ses seviyesi ve elektromanyetik servis freni daha sessiz ve güvenli bir çalışma ortamı sağlar.'],
                ['question' => 'Bakım periyotları ve tüketim parçaları açısından cihazın avantajı nedir?', 'answer' => 'Li-ion batarya sıfır bakım ister; dikey AC motor ve PU teker seti uzun ömür ve düşük bakım maliyeti sunar.'],
                ['question' => 'Hangi palet boyutları için koridor genişliği önerilir?', 'answer' => '1000×1200 çapraz için Ast 2646 mm, 800×1200 boyuna için Ast 2560 mm ile verimli manevra sağlanır.'],
                ['question' => 'Opsiyonel 205Ah batarya hangi kullanım profilleri için uygundur?', 'answer' => 'Yoğun vardiya, uzun hat besleme ve fazla kaldırma çevrimi olan tesislerde tek vardiyada daha uzun çalışma sağlar.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek nasıl işliyor?', 'answer' => 'Makine 12 ay, batarya 24 ay garanti kapsamındadır. İXTİF satış ve servis ekibine 0216 755 3 555 üzerinden ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: WSA161i');
    }
}
