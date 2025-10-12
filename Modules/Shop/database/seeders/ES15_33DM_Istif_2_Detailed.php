<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES15_33DM_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ES15-33DM')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: ES15-33DM'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF ES15-33DM: Geniş şase esnekliği ile 1.5 ton sınıfında çok yönlülük</h2>
  <p>Palet standartlarının değişken olduğu depolarda, ekipmanın uyum kabiliyeti verimliliğin anahtarıdır. İXTİF ES15-33DM; 1270–1470 mm teker kolu genişliği, 200–780 mm ayarlanabilir çatal aralığı ve 1070 mm çatal boyu ile farklı palet genişliklerine kolay uyum sağlar. 24V 125Ah enerji sistemi, 5 km/s seyir hızı ve 1400 mm dönüş yarıçapı sayesinde dar alanlardan geniş alanlara kadar tutarlı bir performans sunar. Geniş taban yapısı ve dengeli ağırlık dağılımı, uzun vardiya operasyonlarında operatöre güven verir.</p>
</section>
<section>
  <h3>Teknik Mimari ve Performans</h3>
  <p>ES15-33DM, 2128 mm kapalı direk yüksekliği ile düşük tavanlı bölgelerde rahat hareket ederken, 3220 mm’ye kadar kaldırma ve 4210 mm direk açık yüksekliği sunar. 0.14/0.20 m/s kaldırma ve 0.13/0.11 m/s indirme hızları, farklı yük senaryolarında dengeli bir tempo sağlar. 1.27 kW sürüş motoru ve 3 kW kaldırma motoru, AC kontrol mimarisiyle enerji verimli ve bakımı kolay bir yapı oluşturur. 915 kg servis ağırlığı ve geniş teker izi; rampalarda %8 (dolu), %16 (boş) eğim performansını stabilize eder. 1650 mm toplam uzunluk ve 580 mm çatala kadar uzunluk, yük yaklaşmalarını hassas hale getirir.</p>
  <p>Poliüretan teker seti (sürüş Ø230×75, arka Ø102×73, ek teker Ø100×50) sessiz çalışma ve zemin dostu sürtünme katsayısı sunar. 40 mm çatal kalınlığı ve 100 mm genişliği, hem dayanım hem de palete giriş kolaylığı açısından optimaldir. 200–780 mm çatal aralığı, tekli veya çiftli kaset tiplerine pratik uyum sağlar. Elektromanyetik frenleme sistemi sürüş güvenliğini pekiştirir; 30–40 mm taban boşlukları ve 60 mm alt yükseklik, palet girişini hızlandırır.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>İXTİF ES15-33DM, esnek şasi geometrisi ile geniş palet yelpazesine uyum sağlayan güvenilir bir istif çözümüdür. Tutarlı hızlar, AC tahrik yapısı ve düşük bakım ihtiyacı ile toplam sahip olma maliyetini düşürür. Saha keşfi ve konfigürasyon danışmanlığı için 0216 755 3 555 üzerinden ekibimizle iletişime geçebilirsiniz.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg @ 600 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V 125Ah'],
                ['icon' => 'gauge', 'label' => 'Seyir Hızı', 'value' => '5/5 km/s (dolu/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1400 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'Geniş Taban', 'description' => '1270–1470 mm teker kolları ile çoklu palet uyumu.'],
                ['icon' => 'battery-full', 'title' => '24V Enerji', 'description' => '125Ah kapasiteyle vardiya boyunca kararlı güç sağlar.'],
                ['icon' => 'weight-hanging', 'title' => '1.5 Ton Kapasite', 'description' => '600 mm yük merkezinde güvenli kaldırma.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Fren', 'description' => 'Elektromanyetik fren ile kontrollü duruş.'],
                ['icon' => 'circle-notch', 'title' => 'PU Tekerler', 'description' => 'Sessiz yuvarlanma ve zemin koruması.'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'AC kontrol ve basit mekanik altyapı.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Geniş palet çeşitliliği olan depolarda raf besleme'],
                ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde yoğun istif döngüleri'],
                ['icon' => 'store', 'text' => 'Perakende ana depo ve bölge dağıtım merkezleri'],
                ['icon' => 'car', 'text' => 'Otomotiv kaset ve kasa paletlerinde yükleme-boşaltma'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış noktalarında kontrollü taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında dar toleranslı istifleme görevleri'],
                ['icon' => 'industry', 'text' => 'Üretim hatlarında ara stok (WIP) yönetimi'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında çeşitli palet genişliklerine uyum']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Geniş teker kolu alternatifleri ile yüksek uyumluluk'],
                ['icon' => 'bolt', 'text' => 'Hızlı kaldırma/indirme değerleri ile çevik döngü süreleri'],
                ['icon' => 'battery-full', 'text' => '24V platform ile kolay bakım ve enerji verimliliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve stabil şasi ile güven'],
                ['icon' => 'star', 'text' => 'Kararlı 5 km/s seyir ile öngörülebilir akış'],
                ['icon' => 'briefcase', 'text' => 'Yedek parça erişimi ve servis kolaylığı']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Depolama'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Üretim ve Lojistik'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Tedarik'],
                ['icon' => 'flask', 'text' => 'Kimyasallar ve Bileşikler'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik Bileşenler'],
                ['icon' => 'tv', 'text' => 'Tüketici Elektroniği ve Beyaz Eşya'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Konfeksiyon'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Gereçleri'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve Endüstriyel Malzeme'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Kırtasiye ve Yayıncılık'],
                ['icon' => 'seedling', 'text' => 'Tarım Lojistiği'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri Depoları'],
                ['icon' => 'building', 'text' => 'Tesis Yönetimi Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay, enerji modülleri 24 ay boyunca üretim kaynaklı hatalara karşı garanti altındadır. Kapsam, normal kullanım şartlarında geçerlidir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Endüstriyel Şarj Ünitesi', 'description' => 'Akü ömrünü koruyan akıllı şarj profilleri ve güvenlik denetimleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'circle-notch', 'name' => 'PU Teker Konfigürasyonu', 'description' => 'Düşük gürültü, zemini koruyan poliüretan bileşikler.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Geniş Çatal Seti', 'description' => 'Farklı yükler için alternatif çatal aralığı ve kilitleme seçenekleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Bakım Kiti', 'description' => 'Planlı bakım için fitiller, gres ve ayar aparatlarını içeren set.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ES15-33DM farklı palet genişliklerine nasıl uyum sağlar?', 'answer' => 'Teker kolları 1170–1370 mm iz genişliğine ve 200–780 mm çatal aralığına sahiptir; bu sayede çoklu palet tiplerine uyum sağlar.'],
                ['question' => 'Dönüş yarıçapı ve koridor gereksinimleri nelerdir?', 'answer' => '1400 mm dönüş yarıçapı ile 2200–2250 mm koridor değerlerinde etkin manevra mümkündür.'],
                ['question' => 'Kaldırma ve indirme hızları operasyon süresini nasıl etkiler?', 'answer' => '0.14/0.20 m/s kaldırma ve 0.13/0.11 m/s indirme, dengeli ve hızlı iş döngüleri sunar.'],
                ['question' => 'Eğim performansı hangi durumlarda yeterlidir?', 'answer' => 'Boşta %16, doluyken %8 eğim tırmanışı sağlar; rampa geçişlerinde süreklilik sunar.'],
                ['question' => 'Enerji sistemi ve bakım açısından avantajı nedir?', 'answer' => '24V platform, AC kontrol ile birleştiğinde düşük bakım ve kararlı enerji verimliliği sunar.'],
                ['question' => 'Direk ölçüleri hangi raf yüksekliklerine uygundur?', 'answer' => 'Kapalı 2128 mm, kaldırma 3220 mm, açık 4210 mm değerlere uygundur.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler?', 'answer' => '74 dB(A) seviyesinde çalışır; uzun vardiyalarda konforu artırır.'],
                ['question' => 'Çatal ölçüsü ve aralığı hangi yükler için idealdir?', 'answer' => '40/100/1070 mm çatal ve 200–780 mm aralık, kaset ve varyant paletlerde ideal tutuş sağlar.'],
                ['question' => 'Şasi boşlukları palet girişini nasıl etkiler?', 'answer' => '60 mm alt yükseklik ve 30–40 mm boşluk değerleri hızlı palet girişine uygundur.'],
                ['question' => 'Fren tipi güvenliği nasıl artırır?', 'answer' => 'Elektromanyetik fren, sürüş ve duruşlarda kontrol sağlar ve yokuşlarda kaymayı engeller.'],
                ['question' => 'Servis ve yedek parça erişimi konusunda durum nedir?', 'answer' => 'Modüler yapı ve yaygın komponentlerle hızlı servis ve parça erişimi mümkündür.'],
                ['question' => 'Garanti kapsamı ve iletişim bilgileri nelerdir?', 'answer' => 'Makine 12 ay, akü 24 ay garanti altındadır. İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: ES15-33DM');
    }
}
