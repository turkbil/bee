<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TCL101_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'TCL101')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: TCL101'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>Kompakt çeviklik, 80V verimlilik: TCL101</h2><p>Yoğun depolarda, rampa ağızlarında ve asma kat geçişlerinde her santimetre değerlidir. İXTİF TCL101, h6&lt;2000 mm üst koruma yüksekliği ve 1422 mm dönüş yarıçapı ile en dar alanlarda dahi rahatça manevra yapar. 80V Li-Ion batarya mimarisi, entegre şarj ile kesintileri azaltır; kalıcı mıknatıslı senkron motor (PMSM) ise %10-15 enerji tasarrufu sağlayarak vardiya boyunca verimliliği artırır. 464 mm geniş bacak payı ve kolçaklı koltuk, operatör yorgunluğunu düşürür; 13 km/sa azami hız ise palet akışını hızlandırır.</p></section><section><h3>Teknik güç ve kontrol</h3><p>TCL101, 1000 kg nominal kapasite ve 500 mm yük merkezi ile tipik tek paletli taşıma senaryolarını güvenle karşılar. 2×2.0 kW çift sürüş motoru ve 7 kW kaldırma motoru; 11/13 km/sa (yüklü/boş) hız, 280/350 mm/sn kaldırma, 350/350 mm/sn indirme performansı sunar. 1200 mm dingil mesafesine sahip kompakt şasi, 2604 mm toplam uzunluk ve 1020 mm genişlik ile dar koridorlarda etkin çalışır. 3000 mm kaldırma yüksekliği, 1990 mm kapalı direk ve 3919 mm açık direk ölçüleriyle raf arası operasyonlarda optimum denge kurar. Otomatik park freni, dönüş hız kontrolü ve elektromanyetik servis/park frenleri güvenliği artırır. Hidrolik yönlendirme, hassas ve öngörülebilir direksiyon hissi sağlar.</p></section><section><h3>Operasyonel verim ve ergonomi</h3><p>Li-Ion batarya, bakım gerektirmeyen yapısı ve fırsat şarjı desteği ile molalarda hızla enerji alır; yerleşik 80V-35A tek fazlı şarj cihazı altyapı gereksinimlerini sadeleştirir. 65 kg hafif batarya ve &lt;2 ton servis ağırlığı, katlar arası taşımada avantaj sağlar. 464 mm bacak payı, ergonomik koltuk ve kol dayama; sık dur-kalk yapılan görevlerde operatör konforunu destekler. 13/15% eğim kabiliyeti ve 1422 mm dönüş yarıçapı; rampalı, dar ve yoğun alanlarda akıcı sürüş sunar. Düşük 68 dB(A) kabin içi ses seviyesi de operatör yorgunluğunu azaltır.</p></section><section><h3>Sonuç</h3><p>Asma katlar, yük asansörleri ve dar koridorlar için geliştirilen İXTİF TCL101; kompakt boyut, 80V verimlilik ve ileri güvenlik fonksiyonlarıyla depo akışını hızlandırır. Projenize uygun direk, aksesuar ve batarya seçenekleri için hemen arayın: 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1000 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V/50Ah (opsiyon 80V/100Ah)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11/13 km/sa (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1422 mm Wa']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '80V Li-Ion sistem', 'description' => 'Bakım gerektirmez, fırsat şarjı destekli entegre şarj ile kesintileri azaltır.'],
                ['icon' => 'bolt', 'title' => 'PMSM verimliliği', 'description' => '%10-15 enerji tasarrufu ve uzatılmış çalışma süresi sunar.'],
                ['icon' => 'arrows-alt', 'title' => 'Üç teker çeviklik', 'description' => 'Çift sürüş motoru ile dar alanda anlık yön değiştirme imkânı.'],
                ['icon' => 'warehouse', 'title' => 'Alçak üst koruma', 'description' => 'h6<2000 mm ile asma kat ve yük asansörlerinde çalışma.'],
                ['icon' => 'shield-alt', 'title' => 'Aktif güvenlik', 'description' => 'Otomatik park freni ve dönüş hız kontrolü standarttır.'],
                ['icon' => 'star', 'title' => 'Ergonomi', 'description' => '464 mm geniş bacak payı ve kolçaklı koltuk ile konfor.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Asma katlı depolarda ve yük asansörlerinde malzeme akışı'],
                ['icon' => 'box-open', 'text' => 'HGV treyler içi palet hareketleri ve rampa yaklaşımı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde dar koridor raf içi operasyonlar'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP ve yarı mamul transferi'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG hat besleme ve hat sonu istifleme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış noktalarında hızlı palet devri'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ve temiz operasyonlar'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında yoğun vardiya içi taşımalar']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'arrows-alt', 'text' => '1422 mm dönüş yarıçapı ve üç teker tasarımı ile benzersiz manevra.'],
                ['icon' => 'battery-full', 'text' => '80V Li-Ion + entegre şarj ile altyapı kolaylığı ve fırsat şarjı.'],
                ['icon' => 'bolt', 'text' => 'PMSM motor ile %10-15 enerji tasarrufu, daha uzun çalışma süresi.'],
                ['icon' => 'shield-alt', 'text' => 'Otomatik park freni ve dönüş hız kontrolü standart güvenlik.'],
                ['icon' => 'cog', 'text' => 'Servis ağırlığı <2 ton: taşıma, kurulum ve kat geçişlerinde avantaj.']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG (Hızlı Tüketim)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Dağıtımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistiği'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Donanım'],
                ['icon' => 'tv', 'text' => 'Dayanıklı Tüketim ve Beyaz Eşya'],
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
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay fabrika garantisi kapsamındadır. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garantiye tabidir. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '80V-35A Dahili Tek Faz Şarj Cihazı', 'description' => 'Standart entegre şarj cihazı ile fırsat şarjı ve pratik enerji yönetimi.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => '80V/100Ah Li-Ion Batarya', 'description' => 'Daha uzun çalışma süresi için artırılmış kapasite batarya paketi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'bolt', 'name' => 'LED Mavi Uyarı Lambası', 'description' => 'Ön/arka mavi ışık ile yaya güvenliği ve görünürlük artışı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'award', 'name' => 'Konforlu Koltuk (Emniyet Switch\'li)', 'description' => 'Bel destekli, ayarlanabilir koltuk ile uzun vardiyalarda konfor.', 'is_standard' => true, 'is_optional' => false, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'TCL101 modelinin nominal kapasitesi ve yük merkezi nedir?', 'answer' => 'Nominal kapasite 1000 kg, yük merkezi 500 mm’dir. Tek paletli standart uygulamalar için idealdir.'],
                ['question' => 'Dönüş yarıçapı ve dar alan performansı nasıldır?', 'answer' => 'Üç teker dizaynı sayesinde 1422 mm dönüş yarıçapına ulaşır ve noktasal dönüşe çok yakındır.'],
                ['question' => 'Azami sürüş hızı ve eğim kabiliyeti değerleri nelerdir?', 'answer' => 'Yüklü 11 km/sa, boş 13 km/sa hıza çıkar; eğim kabiliyeti yüklü %13, boş %15 seviyesindedir.'],
                ['question' => 'Direk ölçüleri ve maksimum kaldırma yüksekliği kaç mm’dir?', 'answer' => 'Kapalı direk 1990 mm, serbest kaldırma 120 mm, kaldırma 3000 mm, açık direk 3919 mm’dir.'],
                ['question' => 'Batarya tipi, kapasitesi ve şarj altyapısı nasıldır?', 'answer' => '80V Li-Ion, standart 50Ah; entegre 80V-35A tek faz şarj cihazı ile fırsat şarjı desteklenir.'],
                ['question' => 'Enerji verimliliğini artıran PMSM teknolojisi ne sağlar?', 'answer' => 'Kalıcı mıknatıslı senkron motor %10-15 enerji tasarrufu ve yaklaşık %10 daha uzun çalışma süresi verir.'],
                ['question' => 'Operatör konforu için sunulan ergonomik özellikler nelerdir?', 'answer' => '464 mm geniş bacak payı, kolçaklı koltuk ve düşük 68 dB(A) ses seviyesi ile konforludur.'],
                ['question' => 'Güvenlik işlevleri standart mı geliyor?', 'answer' => 'Evet. Otomatik park freni, elektromanyetik servis/park frenleri ve dönüş hız kontrolü standarttır.'],
                ['question' => 'Servis ağırlığı ve katlar arası kullanım avantajı nedir?', 'answer' => 'Servis ağırlığı yaklaşık 1950 kg olduğundan yük asansörleri ve asma katlarda kullanım kolaydır.'],
                ['question' => 'Bakım gereksinimi ve planlı duruşlar nasıl etkilenir?', 'answer' => 'Li-Ion batarya ve kapalı aktarma yapısı düşük bakım ister; planlı duruş süreleri minimize edilir.'],
                ['question' => 'Opsiyonel batarya ve aydınlatma seçenekleri mevcut mu?', 'answer' => 'Evet. 80V/100Ah batarya, LED arka lamba ve mavi uyarı lambası gibi seçenekler bulunur.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek nasıl ilerler?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. Yedek parça ve servis için İXTİF ile iletişim: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: TCL101');
    }
}
