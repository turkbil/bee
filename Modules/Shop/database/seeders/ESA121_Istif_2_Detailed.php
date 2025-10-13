<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESA121_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ESA121')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ESA121');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF ESA121: Kompakt gövdede sınıfının en dengeli istifi</h2>
  <p>Depoda ilk vardiya başlarken, kapılar açılır açılmaz rafların ritmi başlar. Paletler, siparişler, tedarik ve sevkiyat arasında akış kesintisiz olmalıdır. İXTİF ESA121 işte bu akışı sağlamlaştırmak için tasarlanmış, 1.2 ton kapasiteli elektrikli yaya tipi istif makinesidir. H-kesit direk mimarisi ve iki yandan konumlandırılan kaldırma silindirleri, özellikle yüksek kaldırma seviyelerinde şasiyi burulmaya karşı dirençli kılar; operatöre geniş görüş açısı sunarken yük altında stabiliteyi hissedilir biçimde artırır. 24V/105Ah batarya mimarisi AGM veya Li-ion seçenekleriyle gelir; entegre şarj cihazı sayesinde makine her prizde kolayca şarj olur. 3.0 kW\'a yükseltilen kaldırma motoru, daha hızlı kaldırma ve indirme hızlarıyla çevrim süresini kısaltır; elektromanyetik servis freni ise güvenliği otomatik hale getirir.</p>
</section>
<section>
  <h3>Teknik güç ve ölçüler: Veriye dayalı performans</h3>
  <p>ESA121, 1200 kg nominal kapasiteyi 600 mm yük merkezinde karşılar ve 2930 mm kaldırma yüksekliğine ulaşır. Direk kapalıyken 1995 mm, açık konumda 3460 mm yüksekliğe sahiptir. Kompakt gövde 1760 mm toplam uzunluk ve 826 mm toplam genişlik ile dar koridorlarda manevrayı kolaylaştırır; 1480 mm dönüş yarıçapı ve 4.0/4.5 km/s seyir hızı, sık dur-kalk yapılan operasyonlarda akışı hızlandırır. Güç ünitesinde 0.65 kW tahrik motoru ve S3 %15 göreve oranında 3.0 kW kaldırma motoru beraber çalışır. Kaldırma hızları yüklü/boş 0.15/0.24 m/s, indirme hızları 0.21/0.20 m/s seviyesindedir. Polüretan tekerlek seti standarttır; DC sürüş kontrolü ve elektromanyetik fren sistemi operatörün yükle meşgulken bile güvenliği korur. 24V/105Ah batarya AGM veya Li-ion olarak seçilebilir; her iki seçenek de bakım gerektirmeyen yapıdadır ve entegre şarj cihazı standart sunulur. Şasi orta noktası yerden yüksekliği (m2) 23 mm, çatal ölçüleri 60/170/1150 mm’dir. Bu değerler, 1000×1200 ve 800×1200 palet akışlarında raf içi giriş-çıkışlarda hassas konumlamayı kolaylaştırır.</p>
</section>
<section>
  <h3>Operasyonel rahatlık ve güvenlik: Günlük işte somut fark</h3>
  <p>Kullanıcı odaklı kapak tasarımı evrak, küçük nesneler ve bardak gözüyle vardiya içinde küçük ama önemli konforlar sağlar; opsiyonel USB portu cihazları şarj etmek için ek pratiklik getirir. H-kesit direk ve yan silindir düzeni, titreşimi azaltır ve özellikle üst seviye istiflerde yük salınımını sınırlayarak yığın kararlılığını yükseltir. Yumuşak iniş (soft landing) standardı cam, içecek, kozmetik gibi hassas yüklerde sesi ve darbe etkisini düşürür; direğin üst seviyelerinden önce otomatik hız azaltma opsiyonu ise güvenlik katmanı ekler. Pazarda kanıtlanmış tahrik ünitesi ve 74 dB(A) ses seviyesi, yoğun depolarda operatör ergonomisini destekler. ESA121’in mono ve duplex mast seçenekleri, düşük tavanlı iç mekanlardan daha yüksek raflara kadar farklı kullanım senaryolarını kapsar; standart model 2930 mm’ye kadar istiflerken <em>ESA121-M</em> serbest kaldırma avantajı ile 1953 mm’ye, <em>ESA121-D</em> ise ayarlanabilir payandalarıyla çoklu palet yapılarına uyum sağlar.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>İXTİF ESA121, bakım gerektirmeyen enerji seçenekleri, kompakt şasi ve güvenli istif mimarisi ile depo akışını sadeleştirir, eğitim ihtiyacını düşürür ve çevrim sürelerini kısaltır. Projenize uygun mast tipi, istif yüksekliği ve ekipman kombinasyonları için teknik ekibimizle görüşebilirsiniz: 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1200 kg @ 600 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 105Ah AGM veya Li-ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => 'Seyir 4.0/4.5 km/s, kaldırma 0.15/0.24 m/s'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1480 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'shield-alt', 'title' => 'H-kesit direk', 'description' => 'Burulmaya dirençli yapı ve üst seviyede stabil istif'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt şasi', 'description' => 'Dar koridorlarda kolay manevra ve kontrollü dönüş'],
                ['icon' => 'battery-full', 'title' => 'Bakım gerektirmez enerji', 'description' => 'AGM veya Li-ion seçenekleriyle esnek vardiya planı'],
                ['icon' => 'plug', 'title' => 'Entegre şarj', 'description' => 'Harici cihaza gerek kalmadan her prizde şarj'],
                ['icon' => 'bolt', 'title' => '3.0 kW kaldırma', 'description' => 'Hızlı kaldırma/indirme ile çevrim süresini kısaltır'],
                ['icon' => 'hand', 'title' => 'Kullanıcı odaklı kapak', 'description' => 'Evrak, eşya, bardak gözü ve opsiyonel USB portu']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret paketleme hatlarında raf içi istif ve besleme'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında çapraz sevkiyat ve kısa mesafe transfer'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerde palet kırma ve mağaza sevki öncesi staging'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkışlarında hassas ve sessiz istif'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik alanlarında yumuşak inişle kırılgan yükler'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça stok alanlarında raf besleme'],
                ['icon' => 'flask', 'text' => 'Kimya varil ve IBC istifinde kontrollü kaldırma'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde yarı mamul (WIP) taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Güncellenmiş 3.0 kW lift motoru ile daha hızlı kaldırma/indirme'],
                ['icon' => 'battery-full', 'text' => 'AGM ve Li-ion opsiyonlarıyla esnek enerji altyapısı'],
                ['icon' => 'arrows-alt', 'text' => '826 mm genişlik ve 1480 mm dönüş yarıçapı ile çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'H-kesit direk ve yan silindirle daha iyi kalıntı kapasite'],
                ['icon' => 'star', 'text' => 'Pazarda kanıtlı tahrik ünitesi ve 74 dB(A) düşük gürültü'],
                ['icon' => 'briefcase', 'text' => 'Kullanıcı odaklı ergonomi ve pratik günlük kullanım']
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
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarındaki üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı', 'description' => 'Standart, 24V/105Ah batarya için uyumlu', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Harici Hızlı Şarj Ünitesi', 'description' => 'Vardiya arası hızlı şarj için gelişmiş ünite', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Teker Seti', 'description' => 'Düşük gürültü ve zemin dostu kullanım', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Li-ion Batarya Paketi', 'description' => 'Bakım gerektirmeyen enerji, hızlı şarj uyumlu', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ESA121\'in nominal kapasitesi ve yük merkezi değerleri nelerdir?', 'answer' => 'Nominal kapasite 1200 kg olup ölçümler 600 mm yük merkezinde verilir. Bu değer, raf içi standart EUR palet operasyonlarında güvenli çalışma aralığını tanımlar.'],
                ['question' => 'Kaldırma ve indirme hızlarında güncelleme ne sağlıyor?', 'answer' => 'Lift gücü 3.0 kW’a çıkarıldığı için kaldırma 0.15/0.24 m/s, indirme 0.21/0.20 m/s seviyelerine ulaşır. Çevrim süresi kısalır ve istif performansı artar.'],
                ['question' => 'H-kesit direk ve yan silindirlerin pratik faydası nedir?', 'answer' => 'Direk rijitliği artar, yük altında burulma azalır. İki yan silindir konumu görüşü açar, üst seviye istiflerde dengeli hareket sağlar ve kalıntı kapasiteyi iyileştirir.'],
                ['question' => 'Batarya seçenekleri ve bakım gereksinimleri nasıldır?', 'answer' => '24V/105Ah AGM veya Li-ion seçenekleri sunulur. Her ikisi de bakım gerektirmez; entegre şarj cihazı standarttır ve vardiya planlamasını kolaylaştırır.'],
                ['question' => 'Kompakt şasi dar koridorlarda nasıl avantaj sağlar?', 'answer' => '826 mm genişlik ve 1480 mm dönüş yarıçapı, raf aralarında kontrollü manevra ve kısa mesafe transferlerde akıcı hareket kabiliyeti sağlar.'],
                ['question' => 'Soft landing özelliği hangi yüklerde fark yaratır?', 'answer' => 'Cam, içecek, kozmetik gibi kırılgan yüklerde inişin son fazını yumuşatarak gürültüyü ve darbe riskini düşürür, hatalı istif kaynaklı hasarı azaltır.'],
                ['question' => 'Sürüş kontrolü ve fren sistemi güvenliği nasıl destekler?', 'answer' => 'DC sürüş kontrolüyle hız yönetimi hassaslaşır; elektromanyetik servis freni bırakıldığında devreye girerek eğimli zeminlerde güvenliği artırır.'],
                ['question' => 'Direk seçenekleri operasyon tasarımına nasıl uyum sağlar?', 'answer' => 'Duplex direk 2930 mm’ye kadar, mono direk 1953 mm’ye kadar çözümler sunar. Straddle varyantı ayarlanabilir payandalarla çoklu paletlere uyum sağlar.'],
                ['question' => 'Bakım ve servis aralıkları ne şekilde planlanmalı?', 'answer' => 'Bakım gerektirmeyen batarya ve kanıtlı tahrik ünitesi sayesinde periyodik kontroller kısalır. Standart sarf parçaları için stok planı kolaydır.'],
                ['question' => 'Seyir hızı ve eğim kabiliyeti hangi senaryolara uygundur?', 'answer' => '4.0/4.5 km/s seyir ve %3/%10 eğim kabiliyeti, düz iç mekan koridorları ve düşük eğimli rampalarda verimli, güvenli operasyon sağlar.'],
                ['question' => 'Opsiyonel USB portu ve ergonomik kapak neden önemli?', 'answer' => 'Vardiya içi belge takibi, el cihazlarının şarjı ve küçük eşyaların düzeni operatör odağını artırır; operasyonel hataları ve duraklamaları azaltır.'],
                ['question' => 'Satış sonrası destek ve teklif almak için nasıl ilerleriz?', 'answer' => 'Teknik konfigürasyon, demo ve fiyatlandırma için İXTİF ekibi ile iletişime geçebilirsiniz: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed: ESA121 (İstif) güncellendi');
    }
}
