<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESL122_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ESL122')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ESL122');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF ESL122: Dar Koridorların Akıllı İstif Uzmanı</h2>
  <p>Depo kapıları açıldığında ilk iş güvenle başlar. İXTİF ESL122, 1.200 kg nominal kapasitesi, 24V/100Ah Li-Ion bataryası ve kompakt şasisiyle dar koridorlarda akışkan bir operasyon sunmak için tasarlandı. Kirişli rijit direk mimarisi, gövdeyi çevreleyen yan darbe kirişleri ve poliüretan teker seti; titreşimi azaltırken sessizliği artırır. Kaplumbağa (crawl) fonksiyonlu uzun ve ofset tiller kolu, operatöre yük etrafında doğal bir görüş hattı ve güvenli yaklaşım sağlar. Entegre şarj cihazı, saha içinde prize yakın her noktayı bir istasyona çevirerek kesintisiz vardiya düzenine destek verir.</p>
</section>
<section>
  <h3>Teknik Güç ve Verimlilik</h3>
  <p>ESL122, elektromekanik frenle desteklenen DC sürüş kontrolü ve 0.75 kW tahrik motoru sayesinde 4.2/4.5 km/s seyir hızlarına ulaşır. 2.2 kW kaldırma motoru, kaliteli hidrolik pompa ile eşleşerek yüklü durumda 0.09 m/s, boşta 0.13 m/s kaldırma hızları üretir; indirme hızları ise sırasıyla 0.10/0.085 m/s değerlerindedir. 2067 mm kapalı direk yüksekliği ve 2930 mm nominal kaldırma yüksekliği, 3532 mm tam açık direk yüksekliğiyle birlikte hafif hizmet raf çözümlerinin büyük bölümünü kapsar. 1464 mm dönüş yarıçapı ile 2230–2296 mm koridor gereksinimleri; EUR paletlerin hem uzunlamasına hem enine akışına olanak verir. 560 kg servis ağırlığı ve 1212 mm dingil açıklığı, manevra anında dengeyi destekler. 24V/100Ah Li-Ion batarya 28 kg ağırlığa sahip olup EN 16796’ya göre 0.62 kWh/saat enerji tüketimi ve VDI 2198’e göre 55.12 t/kWh verimlilik sağlar. Poliüretan teker malzemesi ve mekanik direksiyon, dar yüzeylerde sessiz ilerleme sunar; 3%/10% (yüklü/boş) tırmanma kabiliyeti rampalı alanlarda güvenli yaklaşım sağlar.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>Günlük hafif istif görevlerinde güvenilir ve ekonomik bir çözüm arayan işletmeler için İXTİF ESL122; güçlü şasi, rijit direk ve hızlı şarj özellikleriyle toplam sahip olma maliyetini aşağı çeker. Operatör dostu ergonomi, dar alanlarda bile kontrollü hızlanma ve güvenli frenleme ile birleşince, hem vardiya verimini hem ürün güvenliğini artırır. Teknik detayları yerinde görmek ve optimizasyon çalışması planlamak için ekiplerimizle iletişime geçin: 0216 755 3 555.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1200 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 100Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.2 / 4.5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1464 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion Enerji', 'description' => '24V/100Ah, hızlı şarj, sıfır bakım ve uzun çevrim ömrü.'],
                ['icon' => 'bolt', 'title' => 'Entegre Şarj', 'description' => 'Depo içinde priz olan her noktada hızlı tak-çalıştır şarj.'],
                ['icon' => 'arrows-alt', 'title' => 'Dar Alan Manevrası', 'description' => 'Uzun ofset tiller ve kaplumbağa modu ile güvenli yaklaşım.'],
                ['icon' => 'industry', 'title' => 'Rijit Direk', 'description' => 'Kiriş yapısı ile pürüzsüz kaldırma ve daha az deformasyon.'],
                ['icon' => 'cog', 'title' => 'Sessiz Hidrolik', 'description' => 'Kaliteli pompa ile düşük gürültü ve hızlı kaldırma çevrimi.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Fren', 'description' => 'Elektromanyetik frenleme ve kontrollü yokuş hâkimiyeti.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret depolarında hafif raf içi istif ve sipariş konsolidasyonu'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde back-of-store malzeme akışı'],
                ['icon' => 'warehouse', 'text' => '3PL cross-dock hatlarında hızlı palet yerleştirme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk depo giriş-çıkış alanlarında sessiz taşıma (uygun zemin)'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ürünlerin kontrollü istifi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında kutu ve kasaların istiflenmesi'],
                ['icon' => 'flask', 'text' => 'Kimyasal ambalaj depolarında palet bazlı yerleştirme'],
                ['icon' => 'industry', 'text' => 'Hafif üretim hücrelerinde yarı mamul (WIP) besleme ve toplama']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Entegre şarj ile altyapı yatırımı olmadan vardiya sürekliliği'],
                ['icon' => 'battery-full', 'text' => 'Li-Ion batarya ile hızlı şarj, sıfır bakım ve tutarlı performans'],
                ['icon' => 'arrows-alt', 'text' => '1464 mm dönüş yarıçapı ve ofset tiller ile dar alan çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve DC sürüş ile öngörülebilir kontrol'],
                ['icon' => 'cog', 'text' => 'Modüler, servis-dostu tasarım ve düşük gürültü seviyeleri']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama ve Dağıtım'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen Depoları'],
                ['icon' => 'building', 'text' => 'Beyaz Eşya Dağıtım Depoları'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv ve Kırtasiye'],
                ['icon' => 'industry', 'text' => 'Hafif Sanayi Üretim Tesisleri'],
                ['icon' => 'cart-shopping', 'text' => 'DIY/Yapı Market Arka Depoları'],
                ['icon' => 'warehouse', 'text' => 'İçecek ve Ambalaj Lojistiği'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Mikro Dağıtım Merkezleri'],
                ['icon' => 'building', 'text' => 'Mobilya ve Ev-Yaşam Depoları'],
                ['icon' => 'flask', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'briefcase', 'text' => 'Ofis Malzemeleri ve Hırdavat'],
                ['icon' => 'industry', 'text' => 'Yerel Üretici ve Tedarikçi Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'cog',  'name' => 'Harici hızlı şarj ünitesi', 'description' => 'Vardiya arasında hızlı enerji takviyesi için yüksek akım destekli harici şarj çözümü.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Entegre şarj cihazı',       'description' => 'Standart entegre şarj cihazı ile pratik tak-çalıştır şarj imkânı.', 'is_standard' => true,  'is_optional' => false, 'price' => null],
                ['icon' => 'cog',  'name' => 'Tandem arka teker seti',    'description' => 'Zemin koşullarına uygun, aşınmaya dayanıklı poliüretan teker alternatifleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog',  'name' => 'Uzun süreli şarj kablosu',  'description' => 'Şarj alanı esnekliği için endüstriyel fiş uyumlu uzatma kablosu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award',       'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS'],
                ['icon' => 'certificate', 'name' => 'EN 16796', 'year' => '2024', 'authority' => 'CEN']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Dar koridorda minimum dönüş yarıçapı nedir ve manevra güvenliği nasıl sağlanır?', 'answer' => 'Dönüş yarıçapı 1464 mm’dir. Uzun ofset tiller ve kaplumbağa modu, operatör ile yük arası güvenli mesafeyi koruyarak dar alan manevralarında kontrollü yaklaşım sağlar.'],
                ['question' => 'Nominal kaldırma yüksekliği ve tam açık direk ölçüsü hangi raf yüksekliklerine uygundur?', 'answer' => '2930 mm kaldırma yüksekliği ve 3532 mm tam açık direk, hafif hizmet raf sistemlerinin çoğunu kapsar. Uygun raf düzeni ile 2-3 seviye kullanımda verimlidir.'],
                ['question' => 'Yüklü ve boş seyir hızları hangi değerlerdedir ve hız kontrolü nasıl çalışır?', 'answer' => 'Yüklü/boş 4.2/4.5 km/s hız sunar. DC sürüş kontrolü ve elektromanyetik frenleme ile hızlanma ve duruşlar öngörülebilir şekilde yönetilir.'],
                ['question' => 'Kaldırma ve indirme hızları operasyon çevrimine nasıl etki eder?', 'answer' => 'Kaldırma 0.09/0.13 m/s, indirme 0.10/0.085 m/s’dir. Kısa çevrimlerde zaman kaybını azaltır; özellikle konsolidasyon ve yerleştirme görevlerinde akışı hızlandırır.'],
                ['question' => 'Akü kapasitesi, enerji tüketimi ve tipik vardiyada beklenen çalışma süresi nedir?', 'answer' => '24V/100Ah Li-Ion ve 0.62 kWh/s tüketimle verimlidir. İş yüküne bağlı olmakla birlikte tek vardiyada molalarla birlikte tüm görevleri karşılayacak sürdürülebilirlik sağlar.'],
                ['question' => 'Rampalarda performans nasıl ve maksimum eğim kapasitesi kaçtır?', 'answer' => 'Maksimum eğim kabiliyeti yüklü/boş %3/%10’dur. Elektromanyetik fren ve kontrollü sürüş yaklaşımı rampa çıkış ve inişlerde stabilite sağlar.'],
                ['question' => 'Teker ve zemin etkileşimi nasıldır, zemin seçimi için önerileriniz nelerdir?', 'answer' => 'Poliüretan tekerler düz ve temiz iç zeminlerde sessiz ve iz bırakmayan performans sunar. Kaba zeminlerde aşınma artabileceğinden zemin uygunluğu kontrol edilmelidir.'],
                ['question' => 'Direk rijitliği ve şasi dayanımı yük altında stabiliteyi nasıl etkiler?', 'answer' => 'Kirişli direk ve yan darbe kirişleri deformasyonu azaltır, yük altında titreşimi düşürerek operatör güven ve hızını artırır.'],
                ['question' => 'Bakım aralıkları ve servis erişimi açısından tasarımın avantajları nelerdir?', 'answer' => 'Modüler Li-Ion ve erişilebilir hidrolik bileşenler ile kontrol paneli, periyodik bakım ve kontrol süreçlerini hızlandırır, iş duruşlarını kısaltır.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler ve ölçüm değeri nedir?', 'answer' => 'Ses basınç seviyesi 74 dB(A) düzeyindedir. İç lojistikte gece vardiyalarında dahi iletişimi zorlamayacak konforda çalışır.'],
                ['question' => 'Standart ve opsiyonel şarj çözümleri arasında operasyonel fark nedir?', 'answer' => 'Standart entegre şarj pratik tak-çalıştır sağlar. Opsiyonel hızlı şarj ile mola sürelerinde daha yüksek akımla kısa sürede enerji takviyesi yapılabilir.'],
                ['question' => 'Garanti kapsamı ve satış-sonrası destek için kiminle görüşebilirim?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. İXTİF satış ve servis ekiplerine 0216 755 3 555 hattından ulaşabilir, yerinde demo ve eğitim planlayabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: ESL122');
    }
}
