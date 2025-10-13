<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES15_15ES_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES15-15ES')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ES15-15ES');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF ES15-15ES: Kompakt yapıda güvenilir 1.5 ton sınıfı istif gücü</h2>
  <p>Depo kapıları açılır açılmaz yoğun iş akışı başlar. Paletler rampa başından kabul alanına, oradan depolama raflarına akarken, her saniye ve her manevra önemlidir. İXTİF ES15-15ES, 1.5 ton kapasiteli yaya tipi elektrikli istif makinesi olarak bu akışın nabzını tutar. 24V 125Ah batarya, 5 km/s seyir hızı ve 1500 mm dönüş yarıçapı sayesinde dar koridorlarda hızlı ve kontrollü ilerler. Operatörün kol boyu tasarımlı sürüş kolu ile ergonomi, elektromanyetik fren ile güvenlik birleşir. Yoğun vardiyalarda istikrarlı kaldırma performansı sunan 3 kW kaldırma motoru ve AC sürüş sistemi, özellikle 600 mm yük merkezindeki 1500 kg yükleri güvenle istiflemenize yardımcı olur.</p>
</section>
<section>
  <h3>Teknik Güç ve Verimlilik</h3>
  <p>ES15-15ES, 2128 mm kapalı direk yüksekliği ve 3227 mm standart kaldırma yüksekliği ile, 3743 mm açık direk boyuna kadar uzanan dengeli bir yapı sunar. 60×170×1150 mm çatal ölçüsü depo standardı paletler için idealdir. 800 mm gövde genişliği ve 1740 mm toplam uzunluk, koridor içi hareketlerde manevrayı kolaylaştırır; 575 mm çatala kadar uzunluk ise yüke yaklaşmada avantaj sağlar. 0.13 m/s dolu kaldırma ve 0.20 m/s boş kaldırma hızları operasyon sürelerini öngörülebilir kılar; 0.13/0.13 m/s indirme hızları ise yük emniyetini artırır. 8/16% eğim kabiliyeti, rampa geçişlerinde iş akışını kesintiye uğratmadan devam ettirir. PU teker seti (sürüş Ø230×75, yük Ø80×60, destek Ø130×55) sessiz ve titreşimsiz yuvarlanma sağlar. Elektrik mimarisinde 1.27 kW S2 60 dk sürüş motoru ve 3 kW S3 15% kaldırma motoru birlikte çalışır; AC kontrol altyapısı düşük bakım ve yüksek enerji verimliliği sunar. 24V 125Ah akü, 330×190×240 mm boyut sınırına sahiptir ve 60 kg ağırlığı ile şasi dengesini korur.</p>
  <p>Güvenlik tarafında elektromanyetik servis freni, yük altında ve rampalarda kontrolü artırır. 28 mm şase altı açıklığı ve 88 mm indirilmiş yükseklik, palet giriş-çıkışını sorunsuz kılar. 1150/1480 mm aralığında ayarlanabilir sürüş kolu yüksekliği, farklı boydaki operatörler için ergonomiyi destekler. 2340 mm (1000×1200 çapraz) ve 2260 mm (800×1200 uzunlamasına) koridor genişliği gereksinimleri, depo planlamasında net bir referans sağlar. Tüm bu değerler, ağır iş yükleri altında dahi öngörülebilir, güvenli ve verimli bir istif deneyimi oluşturur.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>İXTİF ES15-15ES, kompakt ölçülerde 1.5 ton sınıfında dengeli güç, emniyet ve ergonomi sağlar. Dar alanlarda yüksek çeviklik, tahmin edilebilir performans ve düşük bakım ihtiyacı ile toplam sahip olma maliyetini aşağı çeker. Doğru ataşman ve çatal seçimiyle tek vardiyadan çok vardiyalı düzene kolay geçiş yapabilirsiniz. Detaylar ve yerinde demo için 0216 755 3 555 numaralı hattımızı arayın.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg @ 600 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V 125Ah (2×12V/125Ah)'],
                ['icon' => 'gauge', 'label' => 'Seyir Hızı', 'value' => '5/5 km/s (dolu/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1500 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Verimli 24V Enerji', 'description' => '125Ah kapasite ile vardiya boyunca istikrarlı güç sağlar.'],
                ['icon' => 'weight-hanging', 'title' => '1.5 Ton Sınıfı', 'description' => '600 mm yük merkezinde emniyetli kaldırma ve taşıma.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Şasi', 'description' => '800 mm genişlik ve 1740 mm uzunluk ile dar koridor uyumu.'],
                ['icon' => 'shield-alt', 'title' => 'Emniyetli Fren', 'description' => 'Elektromanyetik frenle kontrollü duruş ve rampa güveni.'],
                ['icon' => 'circle-notch', 'title' => 'PU Teker Seti', 'description' => 'Sessiz, düşük titreşimli ve zemini koruyan yapı.'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'AC kontrol, basit mekanik direksiyon ve sağlam tasarım.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Depolama raflarına 3.2 m seviyesine kadar palet istiflemesi'],
                ['icon' => 'store', 'text' => 'Perakende arka depo alanlarında şube içi malzeme transferi'],
                ['icon' => 'box-open', 'text' => 'E-ticaret kutulama hatlarında WIP ve bitmiş ürün akışı'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında manevra kabiliyeti yüksek istif'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında düşük sıcaklık alanlarına giriş-çıkış'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında hassas yüklerin güvenli konumlandırılması'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde yarı mamul taşıma ve besleme'],
                ['icon' => 'flask', 'text' => 'Kimya depolama alanlarında kontrollü yük yönetimi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '1.27 kW AC sürüş + 3 kW kaldırma ile sınıfında güçlü tahrik'],
                ['icon' => 'arrows-alt', 'text' => '800 mm gövde genişliğiyle dar koridorlarda çeviklik'],
                ['icon' => 'battery-full', 'text' => '24V 125Ah batarya ile vardiya kararlılığı'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve dengeli şasi ile güvenli kullanım'],
                ['icon' => 'star', 'text' => 'Öngörülebilir hız ve kaldırma değerleriyle standart operasyon'],
                ['icon' => 'briefcase', 'text' => 'Düşük bakım ihtiyacı ve yaygın yedek parça temini']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Operasyonları'],
                ['icon' => 'store', 'text' => 'Perakende Depolama ve Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Ürünler'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşenler'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Konfeksiyon'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçıvanlık'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'building', 'text' => 'Tesis Yönetimi ve Bakım Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine, satın alım tarihinden itibaren 12 ay üretim hatalarına karşı garanti altındadır. Li-Ion veya kurşun-asit batarya modülleri ise satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti normal kullanım koşullarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Akü Şarj Cihazı', 'description' => 'Uygun voltaj ve akım değerlerinde güvenli şarj sağlayan endüstriyel şarj cihazı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'circle-notch', 'name' => 'PU Teker Seti', 'description' => 'Sessiz ve zemini koruyan poliüretan tekerlek konfigürasyonu.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Genişletilmiş Akü Paketi', 'description' => 'Uzun vardiyalar için artırılmış amper-saat kapasitesi opsiyonu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Yan Yük Kılavuzları', 'description' => 'Dar paletlerde yük merkezini sabitlemek için mekanik yönlendirme seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ES15-15ES hangi yük merkezinde 1.5 ton kapasite sunar?', 'answer' => 'Makine 600 mm yük merkezinde 1500 kg nominal kapasite sunar; bu değer palet tipine ve ağırlık dağılımına bağlıdır.'],
                ['question' => 'Dar koridorlarda minimum dönüş yeterliliği nedir?', 'answer' => '1500 mm dönüş yarıçapı ve 800 mm gövde genişliği ile 2.3 m seviyesindeki koridorlarda çevik manevra sağlar.'],
                ['question' => 'Kaldırma ve indirme hızları operasyonu nasıl etkiler?', 'answer' => '0.13/0.20 m/s kaldırma ve 0.13/0.13 m/s indirme hızları, yük emniyetini korurken çevik bir tempo sağlar.'],
                ['question' => 'Rampa ve eğim performansı hangi seviyededir?', 'answer' => 'Maksimum eğim kabiliyeti dolu %8, boş %16 seviyesindedir; rampa geçişlerinde yeterli tırmanma sunar.'],
                ['question' => 'Akü kapasitesi ve ağırlığı makinenin dengesini nasıl etkiler?', 'answer' => '24V 125Ah akü 60 kg ağırlıktadır; gövde dengesini korur ve titreşimi azaltır.'],
                ['question' => 'Hangi teker malzemesi kullanılıyor?', 'answer' => 'Sürüş ve yük tekerlerinde poliüretan (PU) kullanılır; sessiz ve zemin dostudur.'],
                ['question' => 'Direk kapalı ve açık yükseklikleri nelerdir?', 'answer' => 'Kapalı 2128 mm, kaldırma 3227 mm, açık 3743 mm; depo raf yüksekliğine uygunluk planlaması kolaydır.'],
                ['question' => 'Servis freni tipi nedir ve avantajı ne sağlar?', 'answer' => 'Elektromanyetik fren kullanılır; rampa ve duruşlarda ek güvenlik ve kontrol sağlar.'],
                ['question' => 'Koridor genişliği gereksinimleri planlamada nasıl hesaplanır?', 'answer' => '1000×1200 çaprazda 2340 mm, 800×1200 uzunlamasına 2260 mm referans alınmalıdır.'],
                ['question' => 'Gövde koruması ve operatör konforu için hangi detaylar var?', 'answer' => 'Mekanik direksiyon, PU tekerler ve dengeli şasi titreşimi azaltır ve kullanım konforu sağlar.'],
                ['question' => 'Bakım periyotları ve parça erişimi nasıldır?', 'answer' => 'AC sürüş mimarisi ve basit mekanik tasarım düşük bakım aralığı ve hızlı parça erişimi sunar.'],
                ['question' => 'Garanti kapsamı ve iletişim bilgileri nelerdir?', 'answer' => 'Makine 12 ay, akü 24 ay garanti altındadır. İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: ES15-15ES');
    }
}
