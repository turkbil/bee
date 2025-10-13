<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES20_WA_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES20-WA')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı (ES20-WA)');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF ES20-WA: 2 Ton sınıfında güven, hassasiyet ve verimlilik</h2>
  <p>ES20-WA, yaya operatörler için tasarlanmış, depo içi istifleme ve transfer görevlerinde yüksek güvenlik ve tekrarlanabilir hassasiyet sağlayan bir çözümdür. 2000 kg kapasite, 600 mm yük merkezi ve kompakt gövde geometrisi, dar koridorlarda bile akıcı hareketi mümkün kılar. Operatör, iki kademeli indirme fonksiyonu sayesinde raf seviyesine yaklaşırken hızın otomatik ve yumuşak biçimde düşmesini deneyimler; bu, özellikle kırılgan ambalajlı ürünlerde hasar riskini azaltır. 1.1 kW AC sürüş motoru ve 3.0 kW kaldırma motoru birlikte çalışarak 4.5/5.0 km/s sürüş hızlarını ve 0.11/0.16 m/s kaldırma hızlarını sunar. Tüm bu değerler, yoğun vardiya saatlerinde bile yük akışının sürekli ve öngörülebilir olmasını sağlar.</p>
  <p>ES20-WA’nın tasarımı, operatörün gün boyu konforunu gözetir. 800 mm toplam genişlik ve 1589 mm dönüş yarıçapı, 2440–2465 mm koridorlarda paletlerle rahat hareket alanı yaratır. Tiller kolunun 715–1200 mm aralığındaki çalışma yüksekliği farklı boylardaki kullanıcılar için ergonomik bir kontrol noktası sunar. Mekanik direksiyon, bakım kolaylığı ve basitlik ile öne çıkar; elektromanyetik frenleme ise hem ani duruşlarda hem de yokuşlarda güvenli park sağlayarak yaya güvenliğini destekler.</p>
</section>
<section>
  <h3>Teknik Güç ve Şasi Dengesi</h3>
  <p>Şasi yerleşimi, 1170 kg servis ağırlığının sağladığı kütle avantajını poliüretan tekerlek seti ile dengeler. Ön tahrik tekeri ⌀230×75 mm, yük tekerleri ⌀85×70 mm ve denge tekerleri ⌀130×55 mm ölçülerindedir. Bu kombinasyon, titreşimi sınırlı seviyede tutarken zemin ile sürekli temas sağlayarak hassas sürüşü destekler. 18 mm yerden açıklık ve 88 mm çatal alt yüksekliği rampalarda kontrollü yaklaşımı güçlendirir. Standart çatal konfigürasyonu 60×190×1150 mm ölçülerinde olup 600 mm çatal aralığıyla yaygın EUR paletlerle tam uyumludur.</p>
  <p>Direk yapılandırmasında 3000 mm kaldırma yüksekliği, 2020 mm kapalı ve 3465 mm açık direk yüksekliği değerleriyle dengeli bir çözüm sunar; 100 mm serbest kaldırma sayesinde düşük yükseklikte yük yer değiştirme işlemleri kolaylaşır. Performans tarafında 0.32/0.23 m/s indirme değerleri, iki kademeli kontrol sayesinde raf yaklaşımında yumuşak bir profil yaratır. Tırmanma kapasitesi yüklü/boşta %6/%12 seviyesindedir; bu sayede yükleme rampaları ve iç hat geçişleri güvenli ve akıcı şekilde tamamlanır. S2 60 dk çevriminde 1.1 kW sürüş motoru ve S3 %15 çevriminde 3.0 kW kaldırma motoru, farklı görev profillerinde ısıl dengeyi koruyacak şekilde boyutlandırılmıştır.</p>
  <p>Enerji sistemi 24V mimarisi üzerine kuruludur. Standart 280Ah akü ile uzun çalışma penceresi hedeflenirken, 205Ah ve 150Ah Li-ion seçenekleriyle bakım ihtiyacı azaltılır ve ara şarj stratejileri desteklenir. Harici 24V-30A, 24V-50A veya 24V-100A şarj üniteleriyle tesisin altyapısına göre esneklik sağlanır. İsteğe bağlı telematik ve otomatik su dolum seçenekleri, filo yönetimi ve bakım periyotlarının dijital takibini mümkün kılar.</p>
</section>
<section>
  <h3>Operasyonel Avantajlar ve Uygulama Senaryoları</h3>
  <p>ES20-WA, cross-dock alanlarından raf içi replenishment akışlarına kadar çok farklı süreçlerde ortak bir amaç için çalışır: hatasız ve hızlı malzeme hareketi. Kompakt gövde, operatörün görüş hattını kapatmayacak bir direk profili ve tahmin edilebilir hız profilleriyle güvenli sürüş sağlar. 74 dB(A) gürültü seviyesi iç hacimlerde rahat iletişim imkânı yaratır; bu da ekip koordinasyonunu güçlendirir. PU tekerlekler, beton ve epoksi kaplamalı zeminlerde sessiz ve iz bırakmayan hareket sunar. Gün sonunda, mekanik direksiyon ve sade hidrolik tasarım bakım aralıklarını uzatarak toplam sahip olma maliyetini düşürür.</p>
  <p>Kullanımda en çok fark yaratan unsur, iki kademeli indirme fonksiyonudur. Operatör, paletin raf önünde hizalanması sırasında sistemin otomatik olarak yavaş indirme moduna geçmesiyle yükü sabit ve sarsıntısız biçimde bırakır. Bu, cam, kimyasal bidonlar, kozmetik ürünler veya hassas elektronik gibi kırılgan içerikli kolilerde hasar riskini belirgin biçimde azaltır. Ayrıca serbest kaldırma ve farklı direk kombinasyonları ile kapı/kapak altlarında çatalı yükseltip direği kapalı tutarak alan kısıtlarını aşmak mümkündür.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>Özetle ES20-WA, 2 ton sınıfında güvenli frenleme, güçlü kaldırma ve akıllı hız profilleriyle raf verimini artırmak isteyen depolar için dengeli bir istif platformudur. Standart 24V/280Ah çözümünden Li‑ion seçeneklerine uzanan enerji esnekliği, farklı vardiya planlarına uyum sağlar. Doğru direk ve akü konfigürasyonu ile hem ilk yatırımınızı hem de işletme maliyetinizi optimize edebilirsiniz. Projenize özel konfigürasyon, demo ve teklif için bizi arayın: 0216 755 3 555.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (600 mm yük merkezi)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 280Ah (opsiyon Li-ion 205Ah/150Ah)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '4.5 / 5.0 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1589 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '24V Enerji Platformu', 'description' => '280Ah ile uzun vardiya, Li-ion seçenekleriyle düşük bakım.'],
                ['icon' => 'bolt', 'title' => '3.0 kW Kaldırma Gücü', 'description' => 'Ağır paletlerde bile hızını ve stabilitesini korur.'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Yaya güvenliği ve eğimde güvenli duruş sağlar.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Manevra', 'description' => '800 mm genişlik ve 1589 mm dönüşle dar koridor uyumu.'],
                ['icon' => 'star', 'title' => 'İki Kademeli İndirme', 'description' => 'Hassas yavaşlatma ile raflarda kontrollü yerleştirme.'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'Mekanik direksiyon ve PU tekerleklerle sürdürülebilirlik.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarında yoğun vardiya içi palet istiflemesi'],
                ['icon' => 'box-open', 'text' => 'E-ticaret merkezlerinde cross-dock ve raf besleme'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde koridor içi transfer'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış hatlarında kontrollü taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç-Kozmetik depolarında hassas ve sabit hızla istif'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa geçişleri'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde yarı mamul (WIP) besleme'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolamada dar alanlarda güvenli taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'İki kademeli indirme ile raf yerleştirmede üstün hassasiyet'],
                ['icon' => 'battery-full', 'text' => '280Ah standart, Li-ion seçenekleriyle esnek enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve kısa koridor dönüşleriyle verimlilik'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve AC sürüş kontrolünde güvenlik'],
                ['icon' => 'cog', 'text' => 'Mekanik direksiyon sayesinde düşük toplam sahip olma maliyeti']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri Deposu'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Dağıtım Merkezleri'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Paketleme'],
                ['icon' => 'warehouse', 'text' => 'Serbest Depo Bölgeleri'],
                ['icon' => 'store', 'text' => 'Cash & Carry'],
                ['icon' => 'industry', 'text' => 'Makine İmalatı'],
                ['icon' => 'flask', 'text' => 'Boya ve Kaplama'],
                ['icon' => 'microchip', 'text' => 'Beyaz Eşya ve Elektrikli Cihazlar'],
                ['icon' => 'car', 'text' => 'Lastik & Jant Depoları'],
                ['icon' => 'building', 'text' => 'Belediye Ambarları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Harici Şarj Ünitesi 24V-30A', 'description' => 'Standart kurulum için güvenilir şarj çözümü, tesis içi kullanım.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Li-ion Akü Paketi 205Ah', 'description' => 'Bakım gerektirmeyen enerji; hızlı şarj ve ara şarj desteği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Li-ion Akü Paketi 150Ah', 'description' => 'Hafif operasyonlar için ekonomik Li-ion alternatif paketi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Proportional Lift Sistemi', 'description' => 'Hassas kaldırma kumandası ile raf yerleştirmede kontrol artışı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ES20-WA için önerilen koridor genişliği nedir ve hangi paletlerle uyumludur?', 'answer' => '1000×1200 ve 800×1200 paletlerde sırasıyla 2465 mm ve 2440 mm koridor genişliği tavsiye edilir. 800 mm gövde ve 1589 mm dönüş yarıçapı dar alanlarda avantaj sağlar.'],
                ['question' => 'İki kademeli indirme işlevi raf istifinde nasıl fayda sağlar?', 'answer' => 'Yükü yaklaşırken hız düşer ve son aşamada yumuşak bırakma sağlanır. Bu, salınımı azaltır ve ürün hasarını minimuma indirir; operatör hatasını telafi eder.'],
                ['question' => 'Standart akü kapasitesi ve alternatif enerji seçenekleri nelerdir?', 'answer' => 'Standart 24V/280Ah kurulum sağlanır. Uygulamaya göre 205Ah veya 150Ah Li-ion akü opsiyonları ile bakım azalır, hızlı ara şarj imkânı doğar.'],
                ['question' => 'Maksimum kaldırma yüksekliği ve direk ölçüleri nasıl yapılandırılır?', 'answer' => '3000 mm kaldırma için 2020 mm kapalı ve 3465 mm açık yükseklik değerleri tipiktir. Farklı serbest kaldırma ve direk kombinasyonları seçenek olarak sunulur.'],
                ['question' => 'Sürüş ve kaldırma hızları hangi seviyededir?', 'answer' => 'Sürüş 4.5/5.0 km/s, kaldırma 0.11/0.16 m/s ve indirme 0.32/0.23 m/s’dir. Bu değerler güvenli ve verimli istifleme temposu sağlar.'],
                ['question' => 'Eğimli rampalarda performans ve frenleme güvenliği nasıldır?', 'answer' => '6/12% tırmanma kabiliyeti vardır. Elektromanyetik fren yokuşta güvenli duruş ve park imkânı verir; AC kontrol akıcı hareket sağlar.'],
                ['question' => 'Direksiyon ve tekerlek sistemi bakım gereksinimleri nelerdir?', 'answer' => 'Mekanik direksiyon ve PU tekerlek kombinasyonu düşük bakım gerektirir. Düzenli kontrol ve yağlama ile uzun servis ömrü elde edilir.'],
                ['question' => 'Gürültü seviyesi kapalı alanlarda çalışan ekipler için uygun mudur?', 'answer' => '74 dB(A) seviyesinde çalışır. Kapalı alanlarda konforu korur ve vardiya boyunca operatör yorgunluğunu azaltır.'],
                ['question' => 'Çatal ölçüleri ve palet alttan giriş uyumu nasıldır?', 'answer' => 'Standart 60×190×1150 mm çatal ve 600 mm aralık, EUR paletlerle uyumludur. 88 mm çatal alt yüksekliği güvenli yaklaşım sunar.'],
                ['question' => 'Hangi şarj çözümleri desteklenir ve vardiya planlaması nasıl yapılır?', 'answer' => '24V-30A standart harici şarj desteklenir; yoğun kullanımda 50A/100A seçenekleriyle ara şarj stratejileri uygulanabilir.'],
                ['question' => 'Cihazda telematik veya su dolum sistemi seçenekleri mevcut mu?', 'answer' => 'Telematik ve otomatik su dolum opsiyonları mevcuttur. Standart kurulumda kapalıdır; talebe göre eklenir ve entegrasyon sağlanır.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek hizmetleri nelerdir?', 'answer' => 'Makine 12 ay, akü 24 ay garanti altındadır. Kurulum, bakım ve yedek parça için İXTİF destek hattı 0216 755 3 555 üzerinden hizmet verir.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: ES20-WA');
    }
}
