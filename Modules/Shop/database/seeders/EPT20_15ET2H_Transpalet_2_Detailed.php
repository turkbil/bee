<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_15ET2H_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPT20-15ET2H')->first();
        if (!$p) {
            echo "❌ Master bulunamadı";
            return;
        }
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section class="hero-intro">
  <h2>İXTİF EPT20-15ET2H: Zor Zeminlerin Güvenilir Transpaleti</h2>
  <p><strong>Günün ilk sevkiyatı.</strong> Rampalar ıslak, saha engebeli ve süre kısıtlı. Standart bir transpalet kaygan zeminde zorlanırken, EPT20-15ET2H yüksek şasi boşluğu, kauçuk tahrik tekeri ve sızdırmaz gövdesiyle güven vererek işe başlar. 1.500 kg kapasiteyi kompakt ölçülerle birleştirir; DC sürüş kontrolü ve elektromanyetik fren ile hassas manevra sağlar. Entegre şarj cihazı sahada priz bulur bulmaz tak-çalıştır esnekliği sunarken, optimize tahrik sistemi bakım aralıklarını uzatır.</p>
</section>
<section class="technical-power">
  <h3>Teknik Güç</h3>
  <p>Model 24V/65Ah AGM batarya ile beslenir ve standart 24V-10A şarj cihazı içerir. 0.75 kW S2 sürüş motoru ve 0.8 kW S3 kaldırma motoru, 4.0/4.5 km/s seyir hızlarına ulaşır. 1500 kg nominal kapasite, 600 mm yük merkezinde sağlanır. 1704 mm toplam uzunluk ve 685 mm genişlik sayesinde dar alanlarda çeviklik sunar; 1505 mm dönüş yarıçapı raf aralarında avantaj yaratır. 115 mm kaldırma, 80 mm minimum çatal yüksekliği ve 30 mm şasi altı boşluk; 1000×1200 (enine) için 2307 mm ve 800×1200 (boyuna) için 2179 mm koridor gereksinimi ile birlikte değerlendirildiğinde, depo içi ve dış saha geçişlerinde dengeli bir profil sunar. Kauçuk tahrik ve PU yük tekerleri çekiş ile zemin korumasını birleştirir; mekanik direksiyon ve elektromanyetik fren, operatör güvenliği için kararlı bir his verir. DC kontrol mimarisi bakım kolaylığını artırır.</p>
</section>
<section class="operations">
  <h3>Operasyonel Avantajlar</h3>
  <p>Yüksek şasi boşluğu ve yatay yerleşimli motor/fren, dişli kutusunu yer seviyesindeki darbe ve kirden uzak tutar; bu yapı toz ve suya karşı sızdırmazlığı güçlendirir. İnşaat sahası, açık yükleme alanı veya bozuk beton yüzeylerde paletin takılma riskini azaltır. Kauçuk tahrik tekeri kaygan zeminlerde tutuş sağlar; yük tekerlerinde PU malzeme darbe yayılımını azaltır. Entegre şarj cihazı filo yönetiminde ek donanım ihtiyacını düşürür, vardiya arasında ara şarjı kolaylaştırır. Optimize teker yapısı aşınmada hızlı değişime izin vererek servis süresini kısaltır ve toplam sahip olma maliyetini düşürür.</p>
</section>
<section class="battery-system">
  <h3>Enerji Sistemi</h3>
  <p>65Ah AGM batarya, günlük kullanımda düşük bakım gereksinimi ve öngörülebilir performans sunar. Batarya göstergesi ve saat sayacı opsiyonları ile filo takibi kolaylaşır. Şarj altyapısı basittir: standart 24V-10A entegre şarj cihazı sayesinde ayrı bir harici şarj ünitesi taşımaya gerek kalmaz. Opsiyonel 85Ah AGM ile daha uzun süreli döngüler mümkündür; DC kontrol yapısı enerji verimliliğini korur.</p>
</section>
<section class="closing">
  <h3>Sonuç</h3>
  <p>Engebeli zeminlerde dahi akıcı hareket, düşük bakım ve sağlamlık arayan işletmeler için EPT20-15ET2H dengeli bir seçenektir. Çalışma koşullarınıza uygun çatal uzunlukları ve batarya seçenekleri için bizi arayın: <strong>0216 755 3 555</strong>.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 65Ah AGM (entegre 24V-10A şarj)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yüklü / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1505 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'Yüksek Şasi Boşluğu', 'description' => 'Bozuk/engebeli zeminlerde takılma riskini düşürür.'],
                ['icon' => 'shield-alt', 'title' => 'Sızdırmaz Şanzıman', 'description' => 'Toz ve su girişine karşı korumalı yapı.'],
                ['icon' => 'circle-notch', 'title' => 'Kauçuk Tahrik', 'description' => 'Kayma direnci ve aşınma dayanımı sağlar.'],
                ['icon' => 'cog', 'title' => 'Optimize Tahrik Sistemi', 'description' => 'Yatay motor/fren ile daha iyi koruma.'],
                ['icon' => 'battery-full', 'title' => 'AGM Enerji', 'description' => '65Ah AGM ve entegre şarj cihazı standart.'],
                ['icon' => 'wrench', 'title' => 'Kolay Bakım', 'description' => 'Tahrik tekeri hızlı değişim tasarımı.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Açık yükleme alanlarında bozuk zemin üstünde palet transferi'],
                ['icon' => 'hammer', 'text' => 'İnşaat sahalarında engebeli rota üzerinde malzeme besleme'],
                ['icon' => 'box-open', 'text' => 'Depo ile saha arasında kısa mesafe cross-dock akışları'],
                ['icon' => 'industry', 'text' => 'Ağır üretim tesislerinde WIP palet hareketi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde rampa yaklaşımı'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında raf arası sevk'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında nemli zeminlerde güvenli hareket'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde dış saha palet taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Yüksek şasi boşluğu ve yatay motor/fren ile üst düzey koruma'],
                ['icon' => 'battery-full', 'text' => 'AGM batarya + entegre şarj ile basit enerji altyapısı'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 1505 mm dönüş ile çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'Toz/su korumalı şanzımanla uzun ömür'],
                ['icon' => 'wrench', 'text' => 'Tahrik tekerinde hızlı değişim, düşük servis süresi']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya ve Boya'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Dayanıklı Tüketim'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve İnşaat Malzemeleri'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'B2B Endüstriyel Tedarik']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay fabrika garantisi kapsamındadır. AGM batarya modülleri satın alım tarihinden itibaren 24 ay garanti altındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V-10A Şarj Cihazı', 'description' => 'Cihaz üzerinde yerleşik, sahada kolay tak-çalıştır şarj imkanı sağlar.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'battery-full', 'name' => '85Ah AGM Batarya', 'description' => 'Standart 65Ah yerine daha uzun çevrimler için artırılmış kapasite.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Yük Teker Seti (Tandem)', 'description' => 'Dayanıklı PU malzeme ile sessiz ve yüzey dostu hareket.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Kauçuk Tahrik Teker (Yedek)', 'description' => 'Kayma direncini koruyan, hızlı değişime uygun orijinal teker.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Engebeli zeminlerde kayma riski nasıl azaltılır ve hangi tekerlek kullanılır?', 'answer' => 'Kauçuk tahrik tekeri kayma direncini artırır, PU yük tekerleri yüzey uyumu sağlar. Yüksek şasi boşluğu takılmayı azaltır ve çekiş korunur.'],
                ['question' => 'Kapasite değeri hangi yük merkezinde sağlanır ve bu neyi ifade eder?', 'answer' => 'Nominal 1500 kg kapasite 600 mm yük merkezinde geçerlidir. Yük merkezi arttıkça efektif kapasite düşebilir; palet boyutuna dikkat edilmelidir.'],
                ['question' => 'Şarj altyapısı nedir ve harici cihaz gerektirir mi?', 'answer' => 'Standart entegre 24V-10A şarj cihazı bulunur. Şebekeye bağlayıp ara şarj yapılabilir; harici şarj cihazı taşımaya gerek kalmaz.'],
                ['question' => 'Seyir ve kaldırma hızları operasyonu nasıl etkiler?', 'answer' => '4.0/4.5 km/s seyir ve 0.027/0.038 m/s kaldırma hızları, kısa mesafe içi akışlarda dengeli performans ve güvenli hızlanma sunar.'],
                ['question' => 'Tırmanma kabiliyeti rampa performansını nasıl etkiler?', 'answer' => 'Yüklü %5, yüksüz %16 tırmanma değerleri düşük eğimli rampalarda güvenli hareket sağlar. Daha dik rampalarda hız ve menzil azalabilir.'],
                ['question' => 'Zorlu ortamlarda dayanıklılığı artıran tasarım detayı nedir?', 'answer' => 'Motor ve frenin teker üzerinde yatay konumlandırılması şasi altı boşluğu artırır; sızdırmaz şanzıman toz ve su girişine bariyer oluşturur.'],
                ['question' => 'Direksiyon ve fren sistemi operatör güvenliğine nasıl katkı sağlar?', 'answer' => 'Mekanik direksiyon kararlı geri bildirim sunar; elektromanyetik servis freni eğim ve duruşlarda tutuşu destekler.'],
                ['question' => 'Hangi çatal uzunlukları ve genişlik seçenekleri mevcuttur?', 'answer' => '560×1150 mm standarttır; 560 veya 685 mm genişliklerde 800–2000 mm aralığında farklı çatal uzunlukları opsiyondur.'],
                ['question' => 'Batarya seçenekleri ve çalışma süresine etkisi nedir?', 'answer' => '65Ah AGM standarttır; 85Ah AGM opsiyonu daha uzun çevrim sağlar. Çalışma süresi görev profilinize bağlıdır.'],
                ['question' => 'Bakım erişimi ve yedek parça değişimi ne kadar hızlıdır?', 'answer' => 'Optimize tahrik tekeri tasarımı sayesinde aşınmada hızlı sök-tak yapılabilir; planlı duruş süreleri kısalır.'],
                ['question' => 'Hangi koridor genişliğinde çalışabilir ve dar alan kabiliyeti nasıldır?', 'answer' => '1000×1200 enine için 2307 mm, 800×1200 boyuna için 2179 mm koridor gereksinimi vardır; 1505 mm dönüş yarıçapı dar alanlarda çeviklik sağlar.'],
                ['question' => 'Garanti koşulları ve satış-sonrası destek kanalları nelerdir?', 'answer' => 'Makine 12 ay, AGM batarya 24 ay garanti kapsamındadır. İXTİF satış, servis ve yedek parça için 0216 755 3 555 ile iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        echo "✅ Detailed: EPT20-15ET2H";
    }
}
