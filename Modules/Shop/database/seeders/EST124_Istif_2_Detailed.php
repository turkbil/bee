<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EST124_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EST124')->first();
        if (!$p) {
            echo "❌ Master bulunamadı: EST124\n";
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF EST124: Kompakt istifte güven, pratiklik ve sürdürülebilir performans</h2>
  <p>İXTİF EST124, günümüz depolarının pratik istifleme ihtiyaçlarına odaklanmış yürüyüş tipi bir çözümdür. 1.200 kg kapasite, 600 mm yük merkezi ve 24V/80Ah AGM aküyle birleşen 24V/10A entegre şarj cihazı; gün içi şarj kolaylığı ve esnek vardiya planlaması sağlar. Metal kapak, sağlam şasi ve pazarca kanıtlanmış sürüş-hidrolik bileşenler uzun ömür ve düşük toplam sahip olma maliyeti sunar. 2.5 metreden 3.6 metreye kadar farklı direk seçenekleriyle orta seviye raf sistemlerinde düzenli istifleme ve yerden kazanç elde edersiniz. 4.0/4.5 km/s sürüş hızları, dar koridorda 1415 mm dönüş yarıçapı ve yalnızca 925 mm gövde genişliği ile çevik operasyonlar mümkün olur.</p>
</section>
<section>
  <h3>Teknik gücün özeti: Şasi, hidrolik ve enerji sistemi</h3>
  <p>EST124, 1195 mm aks mesafesi ve dengeli tekerlek yerleşimiyle yük altında stabil kalır. PU tekerlek kombinasyonu (Ø210×70 tahrik, Ø74×72 yük, Ø130×55 destek) sessiz ve zemin dostu hareket sağlar. 2.2 kW kaldırma motoru, 0.10/0.15 m/s kaldırma hızları ile paletleri raf yüksekliğine hızlıca taşır; elektromanyetik fren güvenli duruş sağlar. Direk opsiyonları 2513/2713/3013/3313/3613 mm kaldırma yüksekliği sunar; kapalı yükseklik değerleriyle kapı ve rampa geçişlerinde rahat manevra elde edilir. 24V/80Ah AGM akü günlük kullanım için idealdir; 24V/10A entegre şarj cihazı sayesinde cihazı fişe takmak yeterlidir. EN 16796’ya göre 0.57 kWh/h enerji tüketimi, 74 dB(A) ses seviyesi ve DC sürüş kontrolüyle verimlilik/konfor dengelenir.</p>
</section>
<section>
  <h3>Sonuç ve iletişim</h3>
  <p>İXTİF EST124; sade, dayanıklı ve ekonomik bir istif makinesi arayan depolar için güvenilir bir seçenektir. E-ticaretten 3PL’e, perakendeden hafif üretime kadar pek çok senaryoda hem yeni başlayan operasyonlara hem de yedek makine ihtiyacına cevap verir. Doğru direk seçimi ve periyodik bakım ile yıllarca istikrarlı performans sunar. Bilgi ve teklif için 0216 755 3 555 numaralı hattan bize ulaşın.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1200 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 80Ah AGM, entegre 24V/10A şarj'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '4.0 / 4.5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1415 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'shield-alt', 'title' => 'Sağlam şasi', 'description' => 'Metal kapak ve güçlü şasi yük altında deformasyonu azaltır.'],
                ['icon' => 'plug', 'title' => 'Entegre şarj', 'description' => '24V/10A şarj cihazı ile fişe tak-şarj et pratikliği.'],
                ['icon' => 'battery-full', 'title' => 'AGM akü', 'description' => '24V/80Ah kapasite ile günlük vardiyada esnek kullanım.'],
                ['icon' => 'warehouse', 'title' => '3.6 m direk', 'description' => 'Orta yükseklikte raf sistemleri için yeterli kaldırma.'],
                ['icon' => 'gauge', 'title' => 'Verimli hız', 'description' => '4.5 km/s boş hız ile akışkan hat besleme.'],
                ['icon' => 'cog', 'title' => 'Kanıtlı parçalar', 'description' => 'Pazarda kendini kanıtlamış sürüş ve hidrolik modüller.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret depolarında EUR palet istifleme ve replenishment'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde cross-dock sonrası raf içi yerleştirme'],
                ['icon' => 'store', 'text' => 'Perakende dağıtımda şube sevkiyat öncesi stok hazırlığı'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında orta yükseklikli soğuk oda giriş-çıkışı'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ürün palet hareketi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça rafları arasında kompakt istifleme'],
                ['icon' => 'tshirt', 'text' => 'Tekstil koli paletlerinin üretimden depoya transferi'],
                ['icon' => 'industry', 'text' => 'Hafif üretim hücrelerinde WIP palet ara stok yönetimi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '2.2 kW kaldırma motoru ile hızlı ve kontrollü kaldırma performansı'],
                ['icon' => 'battery-full', 'text' => 'AGM akü ve entegre şarj ile düşük altyapı ihtiyacı, kolay kurulum'],
                ['icon' => 'arrows-alt', 'text' => '925 mm genişlik ve 1415 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve mekanik direksiyonla güven veren kullanım'],
                ['icon' => 'star', 'text' => 'Pazarca kanıtlanmış bileşenlerle yüksek parça bulunabilirliği']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil'],
                ['icon' => 'book', 'text' => 'Kırtasiye ve Yayıncılık'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'couch', 'text' => 'Mobilya'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv Depoları'],
                ['icon' => 'building', 'text' => 'Belediye ve Kamu Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion/AGM batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V/10A Şarj Cihazı', 'description' => 'Makine üzerinde dahili, günlük kullanımda fişe takarak kolay şarj imkânı sağlar.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Uzun Çatal Seçenekleri', 'description' => '1150 mm standart çatalın yanında 1000 veya 1220 mm opsiyonlarını ekleyin.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Geniş Çatal Taşıyıcı', 'description' => '680 mm yerine 770 mm taşıyıcı ile farklı palet formlarına uyum.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'Harici Hızlı Şarj Ünitesi', 'description' => 'Daha yoğun vardiyalar için harici şarj altyapısı ile esnek planlama.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Günde tek vardiyada ne kadar kullanım süresi elde ederim?', 'answer' => '24V/80Ah AGM akü ve 0.57 kWh/h tüketimle tipik depo akışında vardiya içi mesailer rahatlıkla desteklenir; entegre şarj ile molalarda takviye mümkündür.'],
                ['question' => 'Hangi raf yüksekliklerine kadar istifleme yapabilirim?', 'answer' => 'Direk seçenekleri 2.513 m ile 3.613 m arasında değişir; operasyonunuzun raf yüksekliğine göre uygun direk seçimi yapılır.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti nasıldır?', 'answer' => '925 mm gövde genişliği ve 1415 mm dönüş yarıçapı sayesinde 2180 mm koridorlarda 800×1200 paletle efektif dönüş sağlar.'],
                ['question' => 'Zemin aşınması ve gürültü seviyesi hangi seviyededir?', 'answer' => 'PU tekerlekler sessiz çalışır ve zemin dostudur; ölçülen ses seviyesi 74 dB(A) olup kapalı alanlarda konfor sunar.'],
                ['question' => 'Yokuş performansı ve rampa geçişleri için veriler nedir?', 'answer' => 'Azami eğim kabiliyeti yüklü %3, yüksüz %10’dur; rampa ve seviye farklılıklarında kontrollü geçiş sağlar.'],
                ['question' => 'Bakım aralıkları ve parça bulunabilirliği nasıldır?', 'answer' => 'Pazarca kanıtlanmış modüller kullanıldığı için bakım kolaydır; yaygın parça tedariki ve hızlı servis planlanabilir.'],
                ['question' => 'Operatör güvenliği için frenleme sistemi nedir?', 'answer' => 'Elektromanyetik servis freni ve mekanik direksiyonla birlikte güvenli ve öngörülebilir duruş sağlar.'],
                ['question' => 'Hangi palet ölçüleriyle uyumludur?', 'answer' => 'Standart 60/170/1150 mm çatal ve 680 mm taşıyıcı EUR paletlerle uyumludur; geniş taşıyıcı opsiyonu mevcuttur.'],
                ['question' => 'Enerji maliyetlerini nasıl optimize ederim?', 'answer' => 'EN 16796’ya göre 0.57 kWh/h tüketim, mola şarjlarıyla birleştirildiğinde enerji maliyetlerini düşük tutar.'],
                ['question' => 'Hangi ortam sıcaklıklarında çalıştırılabilir?', 'answer' => 'AGM kimyası geniş bir sıcaklık aralığında çalışır; soğuk alanlarda performans için düzenli şarj planı önerilir.'],
                ['question' => 'Teslimat ve kurulumda ek elektrik altyapısı gerekir mi?', 'answer' => 'Dahili şarj cihazı standart priz çözümleriyle çalışır; devre kapasitesi ve topraklama kontrolü yeterlidir.'],
                ['question' => 'Garanti kapsamı ve iletişim bilgileri nedir?', 'answer' => 'Makine 12 ay, akü 24 ay garanti kapsamındadır. Satış ve servis için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        echo "✅ Detailed güncellendi: EST124\n";
    }
}
