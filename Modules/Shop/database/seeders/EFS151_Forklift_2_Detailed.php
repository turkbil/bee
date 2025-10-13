<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFS151_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFS151')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([

            'body' => json_encode(['tr' => '
<section><h2>İXTİF EFS151: Kompakt gövdede 1.5 ton gücü, şehir içi depolara uygun çeviklik</h2>
<p>Alçak tavanlı eski depo katları, dar kapılar, sıkışık rampa sahaları... EFS151 tam da bu koşullarda fark yaratmak için geliştirildi. 3 tekerli karşı dengeli mimarisi ve 1995 mm koruyucu tavan yüksekliği ile düşük kat yüksekliklerine uyum sağlar; 1535 mm dönüş yarıçapı sayesinde dar koridorlu mağaza arkaları, yük asansörleri ve mezanin alanlarında rahatça manevra eder. 1500 kg nominal kapasite ve 500 mm yük merkezi ile günlük taşıma işlerinizde dengeli performans sunar; kompakt şasi ve düşük ağırlık (2200 kg) ise binaya binen yükleri azaltır. Operatör için ayarlanabilir direksiyon ve kova tipi koltuk konfor sağlar; AC sürüş kontrolü ve tepkisel hidrolikler ise işi akıcı hale getirir.</p></section>
<section><h3>Teknik güç: 48V Li-Ion altyapı, AC tahrik ve dengeli mast geometrisi</h3>
<p>İXTİF EFS151, 48V/150Ah Li-Ion akü paketi ve entegre 48V/30A şarj cihazı ile fırsat şarjı yapabilen, gün boyu verim sunan bir enerji mimarisi kullanır. İhtiyaca göre 48V/180Ah AGM akü ve harici 48V/30A şarj cihazı seçeneği mevcuttur. 6 kW AC sürüş motoru ve 5.5 kW kaldırma motoru, 8/9 km/s sürüş hızı ve 0.25/0.30 m/s kaldırma hızlarıyla çevik ve kontrollü bir çalışma sağlar. 3°/5° mast eğimi, 3000 mm standart kaldırma yüksekliği (h3) ve 4054 mm açık mast yüksekliği (h4) ile günlük istiflemeye uygundur; 100 mm serbest kaldırma ve 1980 mm kapalı mast yüksekliği alçak alanlarda kullanım kolaylığı sunar. Solid lastik tekerler (330×145 ön, 16×6-8 arka) dayanıklıdır; 905 mm ön iz genişliği ve 1060 mm toplam genişlik, dar alan uyumunu pekiştirir. Aşağı mast boşluğu 90 mm ve aks ortası boşluğu 78 mm ile rampalara yumuşak giriş-çıkış sağlanır. Hidrolik servis ve mekanik park freni güven verir; Bluetooth servis uygulaması ise parametre ayarı ile arıza kodlarını el terminali gerektirmeden okuma imkânı sağlar.</p></section>
<section><h3>Sonuç</h3><p>Alçak tavanlı mimariler, şehir içi mağaza ve depolar, yer kısıtlı tesisler için EFS151 dengeli bir çözüm sunar: kompakt yapı, güçlü AC tahrik ve Li-Ion esnekliği. Proje ihtiyaçlarınıza göre mast ve enerji seçenekleriyle yapılandırılır. Teklif ve demo için arayın: 0216 755 3 555</p></section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V/150Ah Li-Ion (48V/30A entegre şarj)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '8 / 9 km/s (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş Yarıçapı', 'value' => '1535 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '48V Li-Ion enerji sistemi', 'description' => '150Ah kapasite ve fırsat şarjı ile vardiya boyunca yüksek erişilebilirlik'],
                ['icon' => 'bolt', 'title' => 'AC sürüş ve kaldırma', 'description' => '6 kW sürüş ve 5.5 kW kaldırma ile akıcı ivmelenme ve hassas kontrol'],
                ['icon' => 'building', 'title' => 'Alçak tavan uyumu', 'description' => '1995 mm tavan yüksekliği ile kapılardan ve düşük katlardan rahat geçiş'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt manevra', 'description' => '1535 mm dönüş yarıçapı ve 1060 mm genişlik ile dar koridor çevikliği'],
                ['icon' => 'shield-alt', 'title' => 'Güven veren frenler', 'description' => 'Hidrolik servis freni ve mekanik park freni ile emniyetli duruş'],
                ['icon' => 'microchip', 'title' => 'Bluetooth servis uygulaması', 'description' => 'Parametre ayarı ve arıza kodu okuma ile hızlı bakım']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Mezanin ve düşük kat depolarda giriş-çıkış ve iç lojistik akışı'],
                ['icon' => 'store', 'text' => 'Perakende zincir mağaza arkalarında dar koridor istifleme'],
                ['icon' => 'building', 'text' => 'Eski fabrika binalarında alçak tavanlı hat besleme işleri'],
                ['icon' => 'car', 'text' => 'Yeraltı otoparkı ve yük asansörü erişimli kentsel dağıtım depoları'],
                ['icon' => 'box-open', 'text' => 'Kargo liftleri ile katlar arası palet transferi ve stoklama'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP taşıma ve malzeme besleme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk olmayan ancak serin antrepolarda dar alan operasyonları'],
                ['icon' => 'briefcase', 'text' => '3PL operasyonlarında çapraz sevkiyat alanı besleme']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Sınıfında düşük dönüş yarıçapı ile kapı ve dar koridor uyumu'],
                ['icon' => 'battery-full', 'text' => 'Li-Ion akü ve entegre şarj ile yüksek uptime ve fırsat şarjı'],
                ['icon' => 'bolt', 'text' => 'AC tahrik, güçlü kaldırma ve dengeli hız değerleri'],
                ['icon' => 'shield-alt', 'text' => 'Dayanıklı solid lastikler ve güvenli fren mimarisi'],
                ['icon' => 'microchip', 'text' => 'Bluetooth servis uygulaması ile hızlı teşhis ve bakım']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende ve Dağıtım Merkezleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda Depoları (soğuk olmayan alanlar)'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça Lojistiği'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'industry', 'text' => 'Genel Üretim Tesisleri'],
                ['icon' => 'flask', 'text' => 'Kimyasal Hammaddeler Deposu'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen Depoları'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye Depoları'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj Sanayi'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon Deposu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY Lojistiği'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG) Dağıtımı'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek ve Şişeleme Depoları'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ekipmanları'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri Depoları'],
                ['icon' => 'briefcase', 'text' => 'Kargo & Kurye Aktarma Merkezleri'],
                ['icon' => 'building', 'text' => 'Şehir İçi Mağaza Arka Depoları'],
                ['icon' => 'award', 'text' => 'Belediye ve Kamu Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion akü modülleri satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 48V/30A şarj cihazı', 'description' => 'Li-Ion sürümde gövde içine entegre tek faz şarj (16A fiş) ile kolay şarj.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Yan Kaydırma Ataçmanı (2A)', 'description' => 'Hassas istifleme için hidrolik side-shifter; kapasitede 200 kg düşüş notu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Harici 48V/30A AGM şarj cihazı', 'description' => 'AGM akü seçeneği ile uyumlu, endüstriyel sınıf harici şarj cihazı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'award', 'name' => 'İz bırakmayan katı lastik seti', 'description' => 'Zemin koruması gereken alanlar için non-marking solid lastik konfigürasyonu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Düşük tavanlı alanlarda forklift rahatça çalışabilir mi, kapı geçişlerinde sorun olur mu?', 'answer' => '1995 mm koruyucu tavan yüksekliği sayesinde düşük kat ve standart kapılardan geçiş mümkündür. 1980 mm kapalı mast yüksekliği de alçak raf aralarında avantaj sağlar.'],
                ['question' => 'Günlük vardiyada akü ömrü yeterli mi, fırsat şarjı destekleniyor mu?', 'answer' => '48V/150Ah Li-Ion paket fırsat şarjı ile gün içinde kısa molalarda takviye edilebilir; entegre 48V/30A şarj cihazı tek faz 16A priz ile çalışır.'],
                ['question' => 'AGM akü seçeneği hangi durumlar için uygun olur?', 'answer' => 'Yüksek kapasite ihtiyacı veya belirli operasyon alışkanlıklarında 48V/180Ah AGM ve harici 48V/30A şarj seçeneği tercih edilebilir; batarya değişimi kolaydır.'],
                ['question' => 'Dönüş yarıçapı ve koridor gereksinimleri nelerdir?', 'answer' => 'Dönüş yarıçapı 1535 mm’dir. 1000×1200 çapraz palet için 3000 mm, 800×1200 boyuna palet için 3200 mm koridor genişliği önerilir.'],
                ['question' => 'Yokuş performansı ve rampada kalkış nasıldır?', 'answer' => 'Maksimum eğim kabiliyeti yüklü %10, boş %12’dir. Solid lastikler ve AC tahrik kontrollü kalkış sağlar.'],
                ['question' => 'Yan kaydırma ataçmanı kapasiteyi etkiler mi, güvenli midir?', 'answer' => 'Side-shifter ile nominal kapasiteden 200 kg düşülmesi önerilir. Hidrolik sistem güvenlik valfleri ile desteklenir.'],
                ['question' => 'Mast seçenekleri neler, alçak tavan için hangi direk uygun?', 'answer' => 'Standart mastta 3000/3300/3600 mm seçenekleri, serbest kaldırmalı direklerde de alternatifler mevcuttur; alçak tavanlar için 3000 veya 3300 mm önerilir.'],
                ['question' => 'Fren sistemi ve acil durdurma donanımları neler?', 'answer' => 'Hidrolik servis freni ve mekanik park freni standarttır. Elektriksel kesme ve acil durdurma butonu ile ekipman güvenliği artırılır.'],
                ['question' => 'Operatör ergonomisi açısından koltuk ve direksiyon ayarı var mı?', 'answer' => 'Kova tipi koltuk ve ayarlanabilir direksiyon düzeni ile farklı boyutlardaki operatörler için konforlu sürüş sağlanır.'],
                ['question' => 'Bakım ve arıza teşhisinde nasıl zaman kazanılır?', 'answer' => 'Bluetooth servis uygulaması ile parametreler ayarlanır, arıza kodları hızlıca okunur; harici el terminaline ihtiyaç azaltılır.'],
                ['question' => 'Çevresel koşullarda toz ve hafif nemde dayanım nasıldır?', 'answer' => 'Endüstriyel ortamlar için tasarlanmış elektrik ve mekanik komponentler toza karşı korumalıdır; düzenli bakım ile uzun ömürlüdür.'],
                ['question' => 'Garanti süresi ve servis desteğine nasıl ulaşırım?', 'answer' => 'Makine 12 ay, Li-Ion akü 24 ay garantilidir. Servis ve teklif için İXTİF 0216 755 3 555 numarasından bize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("✅ Detailed güncellendi: EFS151");
    }
}
