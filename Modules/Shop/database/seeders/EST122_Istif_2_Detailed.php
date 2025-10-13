<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EST122_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EST122')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EST122');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF EST122: Dar Koridorların Güvenilir İstif Gücü</h2>
  <p>Depo trafiğinin yoğunlaştığı vardiya başlangıçlarında, <strong>İXTİF EST122</strong> operatöre yüksek görüş ve hassas kontrol sunarak istiflemenin ritmini yakalar. 1.200 kg nominal kapasite ve 600 mm yük merkezi ile sınıfının temel gerekliliklerini karşılayan model, <em>kompakt duplex mast</em> ve rijit şasi kurgusu sayesinde dar raf aralarında güvenli kaldırma ve konumlandırma yapar. Uzatılmış ve offset tasarımlı tiller kolu, operatörün gövde dışına konumlanmasını sağlayarak palet uçlarını net görmesine yardım eder; kaplumbağa (crawl) butonu ise hızı sınırlayıp manevrayı inceltir. Entegre şarj cihazı, priz erişimi olan her noktayı potansiyel bir enerji istasyonuna dönüştürür; bu sayede filo sahada kalır, cihazlar gereksizce şarj odasına taşınmaz. Sade, endüstride kanıtlanmış bileşen anlayışı bakım süreçlerini kısaltır, parça tedarikini kolaylaştırır ve sahip olma maliyetini düşürür.</p>
</section>
<section>
  <h3>Teknik Güç ve Bileşen Mimarisi</h3>
  <p>EST122’nin DC tahrik mimarisi, <strong>0.75 kW</strong> çekiş motoru ve <strong>2.2 kW</strong> hidrolik pompa kombinasyonu ile dengelenir. Bu yapı, yüklü/boşta <strong>4.2/4.5 km/s</strong> sürüş hızları ve <strong>0.10/0.14 m/s</strong> kaldırma hızlarını mümkün kılar. Elektromanyetik servis freni yokuş başlarında ve rampa çevrelerinde güvenli duruş sağlar; maksimum eğim kabiliyeti <strong>%3/%10</strong> (yüklü/boş) değerindedir. 24V besleme, sahada yaygın bulunan <strong>2×12V/85Ah</strong> modüllerle sağlanır; operasyon yoğunluğuna göre <em>105Ah</em> opsiyonu düşünülebilir. Batarya modüllerinin <strong>2×24 kg</strong> olması servis sırasında ergonomiyi destekler. Gövde genişliği <strong>792 mm</strong>, toplam uzunluk <strong>1713 mm</strong> ve dönüş yarıçapı <strong>1458 mm</strong> değerleri dar koridor uyumu için optimize edilmiştir.  <strong>1856 mm</strong> kapalı mast, <strong>2430 mm</strong> nominal kaldırma ve <strong>3071 mm</strong> açık mast yüksekliği, tek kat ve orta seviye raf yüksekliklerini kapsar. Standart çatal ölçüsü <strong>60/170/1150 mm</strong> olup taşıyıcı genişliği <strong>680 mm</strong>’dir; çatallar arası mesafe <strong>570 mm</strong> (opsiyonel <strong>685 mm</strong>) olarak sunulur. Poliüretan teker seti (Ø210×70 / Ø74×72 ve Ø130×55 destek) düşük yuvarlanma direnci ve titreşim sönümleme açısından hafif hizmet uygulamalara uygundur.</p>
  <p>Operasyonel görünürlük ve ergonomi, offset tiller ve <em>crawl</em> butonunun birlikteliğiyle güçlenir. Sürüş kontrolünün DC mimaride olması, bakım ve servis süreçlerinde sadeliği korurken; sade bileşen filozofisi stokta kritik yedekleri az sayıda referansla yönetmenize imkân verir. Entegre şarj cihazı, vardiya arasında veya öğle molalarında hızlı enerji takviyesi yapılmasına olanak tanır; bu da makinenin daha çok üretken zaman geçirmesi anlamına gelir.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>EST122, depo içi hafif hizmet istif işlerinde güvenlik, görünürlük ve çevikliği dengeli biçimde sunar. Kompakt ölçüler, rijit mast kurgusu ve entegre şarjın getirdiği esneklikle, dar alanlı işletmeler için yüksek değer yaratır. Profesyonel uygulama analizi ve teklif için bizi arayın: <strong>0216 755 3 555</strong>.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1200 kg @ 600 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V (2×12V) / 85Ah; opsiyon 105Ah'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '4.2 / 4.5 km/s (yük/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1458 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'layer-group', 'title' => 'Kompakt duplex mast', 'description' => 'Hafif hizmet istifleme için optimize kiriş yapısı'],
                ['icon' => 'arrows-alt', 'title' => 'Yanal tahrik stabilitesi', 'description' => 'Yük altında gövde salınımını azaltan düzen'],
                ['icon' => 'hand', 'title' => 'Uzatılmış tiller ergonomisi', 'description' => 'Dar alanlarda yüksek görüş ve kontrol'],
                ['icon' => 'gauge', 'title' => 'Kaplumbağa modu', 'description' => 'Hassas manevra için hız sınırlama butonu'],
                ['icon' => 'battery-full', 'title' => 'Entegre şarj', 'description' => 'Priz olan her yerde kolay şarj imkânı'],
                ['icon' => 'cog', 'title' => 'Verimli hidrolik', 'description' => 'Düşük gürültü ve kısa kaldırma süresi']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Dar raf aralarında paletli ürünlerin katlar arası istiflenmesi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında sipariş hazırlama sonrası istifleme'],
                ['icon' => 'box-open', 'text' => 'E-ticaret operasyonlarında iade ve stok yerleştirme süreçleri'],
                ['icon' => 'industry', 'text' => 'Hafif üretim hücrelerinde WIP paletlerinin ara istasyonu'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda girişine yakın alanlarda kısa mesafe istif görevleri'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında hassas ürün raflama'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında kompakt alan yönetimi'],
                ['icon' => 'flask', 'text' => 'Kimyasal ambalajların güvenli ve düzenli stoklanması']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Endüstride kanıtlanmış bileşenlerle yüksek çalışma sürekliliği'],
                ['icon' => 'battery-full', 'text' => 'Entegre şarj ve 24V sistemle esnek enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => 'Yanal tahrik ve rijit şasi ile yük altında stabilite'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve sağlam mast ile güvenli kullanım'],
                ['icon' => 'gauge', 'text' => 'Kaplumbağa modu ile dar alanlarda kontrollü manevra']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincir Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım Merkezleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Hammadde Depoları'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj Depoları'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv ve Dosyalama Depoları'],
                ['icon' => 'building', 'text' => 'Tedarik Merkezleri ve Konsolidasyon Noktaları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı', 'description' => 'Gövde içine entegre akıllı şarj ünitesi; priz olan her noktada kolay şarj.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Saatmetreli Batarya Göstergesi', 'description' => 'Enerji seviyesi ve toplam çalışma saatini tek ekranda izleme.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Mast Koruma Filesi', 'description' => 'Görüşü korurken küçük parçaların mast içine girmesini önleyen demir ağ.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'Desenli PU Tahrik Tekerleği', 'description' => 'Daha yüksek tutuş için desenli poliüretan sürüş tekeri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EST122 ile hangi raf yüksekliklerine güvenle ulaşabilirim?', 'answer' => 'Nominal kaldırma 2430 mm ve açık mast 3071 mm’dir. Tek kat ve orta raf seviyeleri için idealdir; üst seviye gereksinimler için daha yüksek mastlı modeller düşünülmelidir.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti nasıl sağlanıyor?', 'answer' => '792 mm gövde genişliği, 1458 mm dönüş yarıçapı ve kaplumbağa modu dar alanlarda kontrollü, güvenli ve görünür manevra olanağı verir.'],
                ['question' => 'Batarya kapasitesini artırmak mümkün mü, kaç seçenek var?', 'answer' => 'Standart 24V 85Ah modüldür. Operasyon yoğunluğuna göre 105Ah seçeneği sunulur; aralıklı fırsat şarjlarıyla vardiya sürekliliği desteklenir.'],
                ['question' => 'Eğimli rampalarda güvenlik nasıl sağlanıyor?', 'answer' => 'Elektromanyetik fren sistemi ve %3/%10 eğim kabiliyeti (yük/boş) rampalarda kontrollü duruş ve kalkış sağlar.'],
                ['question' => 'Hidrolik sistemin bakım aralığı ve gürültü seviyesi nasıldır?', 'answer' => 'Verimli pompa düşük gürültü üretir; üretici bakım planına bağlı filtre ve yağ kontrolleriyle istikrarlı performans korunur.'],
                ['question' => 'Standart çatal ölçüleri değiştirilebilir mi?', 'answer' => '60/170/1150 mm çatal ölçüsü standarttır; çatallar arası mesafe 570 mm’dir ve 685 mm opsiyonu vardır.'],
                ['question' => 'Sürüş kontrol sistemi ve direksiyon yapısı nedir?', 'answer' => 'DC sürüş kontrolü basit ve güvenilirdir; mekanik direksiyonla bir araya geldiğinde servis kolaylaşır ve maliyetler düşer.'],
                ['question' => 'Ses seviyesi düzenlemelere uygun mu?', 'answer' => 'Sürücü kulağında ölçülen ses seviyesi 74 dB(A) civarındadır; iç mekân iş sağlığı sınırlarını karşılamak üzere tasarlanmıştır.'],
                ['question' => 'Koridor ve rampa yaklaşımı için ölçüleri nasıl yorumlamalıyım?', 'answer' => 'Ast 2290/2225 mm değerleri tipik EUR/ISO palet akışını gösterir; 1713 mm toplam uzunluk dar koridorlarda yaklaşım kolaylığı sağlar.'],
                ['question' => 'Parça ve servis erişimi nasıldır, ortak bileşenler kullanılıyor mu?', 'answer' => 'Sade, endüstride kanıtlanmış bileşenler tercih edilmiştir. Bu yaklaşım parça tedarikini hızlandırır ve servis sürelerini kısaltır.'],
                ['question' => 'Operatör güvenliği için hangi önlemler bulunuyor?', 'answer' => 'Rijit mast kiriş yapısı, elektromanyetik fren ve düşük hız modu; operatörün yük çevresinde kontrollü kalmasına yardımcı olur.'],
                ['question' => 'Garanti süresi ve destek hattı bilgileri nelerdir?', 'answer' => 'Makine 12 ay, Li-Ion akü modülleri 24 ay garantilidir. Satış, servis ve kiralama desteği için İXTİF: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EST122');
    }
}
