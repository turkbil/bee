<?php
// F4_201_Transpalet_3_Variants.php
namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $master = DB::table('shop_products')->where('sku', 'F4-201')->first();
        if (!$master) return;

        $variants = [
            [
                'sku' => 'F4-201-1150x560',
                'title' => 'F4 201 – 1150×560 mm',
                'attributes' => [
                    'variant_type' => 'catal-uzunlugu',
                    'catal_uzunlugu_mm' => 1150,
                    'catal_genisligi_mm' => 560,
                    'catal_boyutlari_mm' => '50×150×1150',
                ],
                'long_description' => <<<HTML
<section class="variant-intro">
  <h2>F4 201 – 1150×560 mm</h2>
  <p><strong>Standart Euro paletin tartışmasız şampiyonu.</strong></p>
  <p>1150×560 mm ölçü, Avrupa paleti üzerinde optimum giriş-çıkış, rampa ve körüklerde hızlı hizalama sağlar. 48V sistem, 2.0 ton kapasite ve kompakt l2=400 mm ile dar koridorlarda ritmi korur.</p>
  <ul>
    <li>Euro palete tam uyum, hızlı çatala giriş</li>
    <li>Kompakt gövde ile minimum dönüş yarıçapı</li>
    <li>Tak-çıkar Li-iyon batarya ile kesintisiz vardiya</li>
  </ul>
</section>
<section class="variant-body">
  <h3>Neden 1150×560?</h3>
  <p>Avrupa palet standartları için 1150×560 konfigürasyonu endüstri normu kabul edilir. Çatal uç geometrisi ve 560 mm dış çatal aralığı, palete girişte rehberlik ederek tırnakların paletin alt tablasına zarar vermeden konumlanmasını sağlar. Bu ölçüyle 1360 mm dönüş yarıçapı ve 2025–2160 mm koridor ihtiyacı, dar alanlarda bile tutarlı akış üretir. 48V sürüş mimarisi ve elektromanyetik fren sistemi, yük altında kontrollü hızlanma ve güvenli duruş sağlar.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Yoğun e-ticaret DC’lerinde sipariş birleştirme ve cross-dock operasyonları için dakikalar değil saniyeler kritiktir. 1150×560 ölçü, palet kabul alanından sevkiyat kapısına kadar aynı ritmi korur. Poliüretan tekerler düşük gürültü ve vibrasyon üretir, böylece vardiya boyu operatör yorgunluğu azalır. Li-iyon batarya ile fırsat şarjı, öğle aralarında bile çevrimleri uzatır. Stabilize teker opsiyonu, yük merkezi önden kaçan ağır paletlerde burna bindirmeyi kontrol eder. İXTİF’in ikinci el alım-satımı, kiralık seçenekler, orijinal yedek parça ve 7/24 teknik servis ekosistemiyle toplam sahip olma maliyeti düşer.</p>
  <h3>Diğer Varyantlardan Farkı</h3>
  <p>Geniş paletli 685 mm varyanta göre dar raf açıklıklarında daha akıcıdır. Uzun çatal 1350–1500 mm çözümlerine kıyasla manevra alanı kısıtlı sahalarda avantaj sağlar ve kör nokta riskini düşürür. Daha geniş çatal aralığı gerektiren endüstriyel paletlerde 685 mm varyantı tercih edilirken, standart FMCG ve perakende lojistiğinde 1150×560 daha evrensel bir çözümdür.</p>
  <h3>Hangi Durumlarda?</h3>
  <p>Perakende arka alan beslemeleri, kurye hub’ları, gıda dağıtım merkezleri ve e-ticaret fulfilmant operasyonları. Raf önü manevrası, dar koridor ve yüksek çevrim gerektiren hatlar için idealdir.</p>
  <h3>Teknik Detaylar</h3>
  <p>Çatal boyutları 50×150×1150 mm, dıştan dışa 560 mm. Nominal kapasite 2000 kg, yük merkezi 600 mm, dönüş yarıçapı 1360 mm, hız 4.5/5 km/s. Batarya 24V/20Ah ×2 Li-iyon; hızlı şarj ve tak-çıkar yapı desteklenir.</p>
</section>
HTML
            ],
            [
                'sku' => 'F4-201-1220x685',
                'title' => 'F4 201 – 1220×685 mm',
                'attributes' => [
                    'variant_type' => 'catal-uzunlugu',
                    'catal_uzunlugu_mm' => 1220,
                    'catal_genisligi_mm' => 685,
                    'catal_boyutlari_mm' => '50×150×1220',
                ],
                'long_description' => <<<HTML
<section class="variant-intro">
  <h2>F4 201 – 1220×685 mm</h2>
  <p><strong>Geniş palet, yüksek stabilite.</strong></p>
  <p>685 mm dış genişlik, endüstriyel ve geniş tabanlı paletlerde denge ve yük oturuşunu iyileştirir; 1220 mm uzunluk ise palet boyu artarken manevrayı kontrollü tutar.</p>
  <ul>
    <li>Geniş paletlerde denge artışı</li>
    <li>Uzun yüklerde merkezleme kolaylığı</li>
    <li>İXTİF 7/24 teknik servis ve orijinal yedek parça</li>
  </ul>
</section>
<section class="variant-body">
  <h3>Neden 1220×685?</h3>
  <p>Geniş palet ve endüstriyel kasalarda 685 mm aralık, kütlenin ağırlık merkezini çatal tabanına daha iyi yayar. Bu sayede eğimli rampalarda ve zemin bozukluklarında salınım azalır. 1220 mm uzunluk, 1150 mm’ye göre daha iyi uç desteği sağlayarak palet kanat sarkmasını azaltır.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Ağır içecek kasaları, kimya bidonları ve endüstriyel üretim paletlerinde yük geometrisi çeşitlidir. Bu varyant, farklı palet tipleri arasında geçişte operatörün hizalama hatalarını tolere eder. İXTİF’in kiralık seçenekleri ve ikinci el alım-satımı ile filonuz bu varyantla pik dönemlerde esnek büyür; orijinal yedek parça ve 7/24 teknik servis operasyonu kesintisiz kılar.</p>
  <h3>Diğer Varyantlardan Farkı</h3>
  <p>1150×560’a göre daha geniş taban, yüksek ağırlık merkezli yüklerde stabilite sağlar. 1350–1500 mm uzun varyantlara göre dar koridorda daha çeviktir.</p>
  <h3>Hangi Durumlarda?</h3>
  <p>İçecek, kimya ve ağır paketleme hatları. Rampalı sevkiyat noktalarında ve zemini pürüzlü depolarda önerilir.</p>
  <h3>Teknik Detaylar</h3>
  <p>Çatal 50×150×1220 mm, dıştan dışa 685 mm. Kapasite 2000 kg, hız 4.5/5 km/s. Batarya 24V/20Ah ×2 Li-iyon.</p>
</section>
HTML
            ],
            [
                'sku' => 'F4-201-1350x560',
                'title' => 'F4 201 – 1350×560 mm',
                'attributes' => [
                    'variant_type' => 'catal-uzunlugu',
                    'catal_uzunlugu_mm' => 1350,
                    'catal_genisligi_mm' => 560,
                    'catal_boyutlari_mm' => '50×150×1350',
                ],
                'long_description' => <<<HTML
<section class="variant-intro">
  <h2>F4 201 – 1350×560 mm</h2>
  <p><strong>Uzun yüklerde daha fazla temas.</strong></p>
  <p>1350 mm çatal, uzun palet ve kasalarda uç desteğini artırır. 560 mm genişlik ile raf geçişlerinde çeviklik korunur.</p>
  <ul>
    <li>Uzun paletlerde sarkma kontrolü</li>
    <li>Raf geçişlerinde çeviklik</li>
    <li>İXTİF ekosistemi: kiralık, ikinci el, yedek parça, 7/24 servis</li>
  </ul>
</section>
<section class="variant-body">
  <h3>Neden 1350×560?</h3>
  <p>Uzatılmış çatal boyu, paletin kanat uçlarına daha fazla destek vererek yük salınımını azaltır. Dar raf açıklıklarında 560 mm genişlik, manevrayı kolay tutar.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Uzun kutu ve kasalarda merkezleme kolaylaşır; yükün ağırlık merkezi daha dengeli dağılır. İXTİF 7/24 teknik servis ve orijinal yedek parça tedariki ile bakım kolaydır.</p>
  <h3>Diğer Varyantlardan Farkı</h3>
  <p>1150×560’a göre uç desteği daha iyidir; 1220×685’e göre dar koridorda daha çeviktir.</p>
  <h3>Hangi Durumlarda?</h3>
  <p>Uzun ambalajlı ürünler, mobilya bileşenleri, beyaz eşya alt paletleri.</p>
  <h3>Teknik Detaylar</h3>
  <p>Çatal 50×150×1350 mm, dıştan dışa 560 mm. Nominal kapasite 2000 kg, dönüş yarıçapı 1360 mm.</p>
</section>
HTML
            ],
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->insert([
                'sku' => $v['sku'],
                'parent_sku' => 'F4-201',
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug('İXTİF ' . $v['title'])], JSON_UNESCAPED_UNICODE),
                'category_id' => $master->category_id,
                'brand_id' => $master->brand_id,
                'variant_type' => 'catal-uzunlugu',
                'attributes' => json_encode($v['attributes'], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode([
                    'Genişletilmiş çatal ile uzun paletlerde uç desteği gereken transferler',
                    'Rampa ve körüklerde hizalama toleransı isteyen sevkiyatlar',
                    'Dar koridorda çeviklik gerektiren raf önü akışları',
                    'Soğuk depo veya düşük gürültü gereksinimi olan alanlar',
                    'Karma palet konsolidasyonu ve cross-dock operasyonları',
                    'Yüksek çevrimli e-ticaret sipariş birleştirme hatları',
                    'Kimya ve içecek kasalarında merkezleme kritik uygulamalar',
                    'Pik sezonlarda kiralık kapasite ile filoyu ölçeklemek',
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
