<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES16_RSi_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ES16-RSi')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: ES16-RSi'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section><h2>İXTİF ES16 RSi: Dar Koridorların Kontrolünü Elinize Alın</h2>
<p>Sabah vardiyası başlarken depo kapıları açılır, rampa üzerindeki yoğunluk hızla artar. Operatörünüz ayakta konumdan geniş görüş alanıyla paletleri seçer, <em>ilk kaldırma</em> sayesinde destek kollarını yükseltir ve çift kat taşımayı devreye alır. İXTİF ES16 RSi, 2000 kg toplam yük kapasitesi ve 5,5/6 km/s seyir hızları ile kesintisiz akış sağlar. Elektronik direksiyon ve elektromanyetik fren, her manevrada güveni artırır; poliüretan tekerlekler ise zemin titreşimini sönümleyerek sessiz ve konforlu bir çalışma sağlar. Günün sonunda fark edilen şey basittir: Aynı alanda daha fazla iş, daha az efor ve daha az bekleme süresi.</p></section>
<section><h3>Teknik Güç ve Verimlilik</h3>
<p>ES16 RSi, 600 mm yük merkezinde 2000 kg toplam kapasite sunar; mast ile kaldırmada 1600 kg’a kadar güvenli istifleme sağlar. 24V 280Ah kurşun-asit aküsü (opsiyon 24V 205Ah Li-ion) yoğun operasyonlarda uzun çalışma süreleri verir. AC sürüş kontrolü ve 2.5 kW sürüş motoru hızlı tepki ve enerji verimliliği üretirken, 3.0 kW kaldırma motoru 0,11/0,16 m/s kaldırma hızlarıyla hedef yüksekliğe hızlı erişim sağlar. 1765/2192 mm dönüş yarıçapı ve 2195 mm toplam uzunluk, dar koridorlarda raf arası manevraları kolaylaştırır. 3000 mm standart kaldırma yüksekliğine ek olarak 3600, 4500 ve 5000 mm’ye kadar direk seçenekleri mevcuttur. Elektromanyetik servis freni ve viraj yavaşlatma fonksiyonu, operatörün ani yön değişimlerinde stabil kalmasına yardımcı olur. 74 dB(A) seviyesindeki ses basıncı, vardiya boyunca konforlu çalışma ortamı yaratır.</p>
<p>Şasi altı 20 mm yer açıklığı, rampalarda ve eşiklerde sürpriz çarpmaları önler. 60×190×1150 mm çatallar ve 800 mm taşıyıcı genişliği, yaygın EUR paletlerde maksimum uyum sunar. 850 mm toplam genişlik ile 2597/3024 mm koridor gereksinimlerini karşılayan yapı, 3PL ve perakende depolarının değişken akışlarına rahatça uyum sağlar. Elektronik direksiyon, düşük hızlarda mikrometre hassasiyeti ile konumlama yaparken yüksek hızda dengeli bir direksiyon hissi verir.</p></section>
<section><h3>Sonuç</h3>
<p>ES16 RSi; verimliliği, güvenliği ve operatör konforunu aynı gövdede birleştirir. Yoğun vardiyalarda akışı hızlandırmak, dar alanlarda güvenle istiflemek ve toplam sahip olma maliyetini düşürmek isteyen ekipler için güçlü bir çözümdür. Tüm teknik detaylar, proje uyumluluğu ve teklif için hemen arayın: <strong>0216 755 3 555</strong>.</p></section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (mast ile 1600 kg)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V 280Ah (opsiyon 24V 205Ah Li-ion)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '5.5 / 6 km/s (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş Yarıçapı', 'value' => '1765 / 2192 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Güçlü Enerji Altyapısı', 'description' => '24V 280Ah akü ile uzun çalışma; Li-ion 205Ah seçeneğiyle hızlı şarj.'],
                ['icon' => 'bolt', 'title' => 'AC Sürüş Kontrolü', 'description' => 'Yük altında dahi akıcı hızlanma ve hassas dozaj.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenlik Donanımları', 'description' => 'Elektromanyetik fren ve viraj yavaşlatma stabilite sağlar.'],
                ['icon' => 'warehouse', 'title' => 'Direk Seçenekleri', 'description' => '3000–5000 mm arası farklı istif yükseklikleri.'],
                ['icon' => 'store', 'title' => 'Dar Koridor Performansı', 'description' => '850 mm genişlik ve kısa dönüş yarıçapıyla çeviklik.'],
                ['icon' => 'cog', 'title' => 'Elektronik Direksiyon', 'description' => 'Düşük efor, yüksek hassasiyet; gün boyu konfor.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment akışlarında raf arası istifleme ve besleme'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında yükleme rampası öncesi tampon alan yönetimi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde sipariş konsolidasyon bölgeleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve içecek depolarında soğuk oda giriş-çıkışları'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik lojistiğinde hassas ürün konumlama'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında yoğun SKU yönetimi'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve hat besleme'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolarda güvenli istif ve izlenebilirlik']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC tahrik ve güçlü 2.5 kW motor ile yüksek verim ve düşük tüketim'],
                ['icon' => 'battery-full', 'text' => 'Esnek enerji: 24V 280Ah kurşun-asit, 205Ah Li-ion opsiyonu'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve kısa dönüş yarıçapı ile dar koridor üstünlüğü'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve viraj yavaşlatma ile güvenli operasyon'],
                ['icon' => 'warehouse', 'text' => '3000–5000 mm direk seçenekleriyle farklı raf yüksekliklerine uyum']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya ve Tehlikesiz Kimyasallar'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri Depoları'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Ticaret'],
                ['icon' => 'cart-shopping', 'text' => 'Dağıtım ve Lojistik Ağları'],
                ['icon' => 'warehouse', 'text' => 'Serbest Depo ve Antrepolar'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Paketleme'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'store', 'text' => 'DIY ve Yapı Market'],
                ['icon' => 'building', 'text' => 'Mobilya Depolama'],
                ['icon' => 'industry', 'text' => 'Makine ve Yedek Parça'],
                ['icon' => 'box-open', 'text' => 'Ambalaj ve Matbaa Lojistiği'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Tedarik Zinciri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V 30A Harici Şarj Cihazı', 'description' => 'Kurşun-asit aküler için harici tip, sağlam ve endüstriyel sınıf şarj çözümü.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => '24V 100A Li-ion Şarj Cihazı', 'description' => 'Li-ion aküler için hızlı şarj; yoğun vardiya döngülerinde minimum bekleme.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'PU Tekerlek Sistemi', 'description' => 'Düşük gürültü ve zemin dostu sürüş için poliüretan sürüş/yük tekerlekleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Batarya Su Otomatik Dolum', 'description' => 'Kurşun-asit akü bakımını kolaylaştıran otomatik su dolum sistemi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO'],
                ['icon' => 'certificate', 'name' => 'EN 1175', 'year' => '2024', 'authority' => 'CEN']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Mast ile ve destek kolları ile kapasite farkı günlük operasyonda ne anlama gelir?', 'answer' => 'Mastla kaldırmada 1600 kg güvenli istiflenir; destek kollarıyla 2000 kg taşınır. Çift kat akışlarda toplam verimi artırır, raf içi istifte mast değerleri esas alınır.'],
                ['question' => 'Dar koridorda dönüş yarıçapı ve toplam uzunluk nasıl avantaj sağlar?', 'answer' => '1765/2192 mm dönüş yarıçapı ve 2195 mm toplam uzunluk, 2600–3100 mm koridorlarda palet çevikliği ve raf sonu U dönüşlerini kolaylaştırır.'],
                ['question' => 'Akü seçenekleri nelerdir ve hangisini hangi vardiyada tercih etmeliyim?', 'answer' => 'Standart 24V 280Ah kurşun-asit maliyet avantajı sunar; 24V 205Ah Li-ion hızlı şarj ve ara şarj kabiliyetiyle çok vardiyalı işletmeler için idealdir.'],
                ['question' => 'Sürüş hızı ve kaldırma hızı performansı hangi işlerde belirgin fark yaratır?', 'answer' => '5,5/6 km/s sürüş ve 0,11/0,16 m/s kaldırma hızları, tampon alan boşaltma ve giriş-çıkış operasyonlarında gözle görülür çeviklik sağlar.'],
                ['question' => 'Viraj yavaşlatma sistemi hangi durumlarda devreye girer?', 'answer' => 'Elektronik kontrol, dönüş açısını algılayarak hızlanmayı düşürür; yük stabilitesini korur ve devrilme riskini azaltır.'],
                ['question' => 'Operatör konforu için hangi özellikler öne çıkar?', 'answer' => 'Elektronik direksiyon düşük efor sağlar, PU tekerlekler titreşimi azaltır, 74 dB(A) ses seviyesi uzun vardiyalarda konfor sunar.'],
                ['question' => 'Soğuk depo koşullarında hangi hazırlık önerilir?', 'answer' => 'PU tekerlek kullanımı ve akü yönetiminin dikkatle yapılması önerilir; Li-ion için ısıtma/şarj stratejileri planlanmalıdır.'],
                ['question' => 'Bakım periyotları ve kritik kontrol noktaları nelerdir?', 'answer' => 'Günlük görsel kontroller, haftalık tekerlek ve fren testi, akü seviyesi ve kablo denetimi; aylık cıvata sıkılık ve yağlama.'],
                ['question' => 'Rampa ve eşik geçişlerinde alt açıklık ve yaklaşım açıları yeterli mi?', 'answer' => '20 mm alt açıklık ve ilk kaldırma fonksiyonu, rampalara girişte şasiyi korur; ani çarpmaları azaltır.'],
                ['question' => 'Hangi palet ölçülerinde en iyi uyumu sağlar?', 'answer' => 'EUR paletlerle 60×190×1150 mm çatallar uyumludur; 800 mm taşıyıcı genişliği, yaygın raf açıklıklarına uygunluk sağlar.'],
                ['question' => 'Elektronik direksiyonun arıza halinde emniyet senaryosu nasıl işler?', 'answer' => 'Kontrol ünitesi arızalarında güvenli duruş ve frenleme önceliklidir; operatör ikazlarıyla cihaz emniyet moduna alınır.'],
                ['question' => 'Garanti kapsamı ve satış-sonrası destek nasıl ilerler?', 'answer' => 'Makine 12 ay, akü 24 ay garantilidir. Yedek parça, servis ve kiralama seçenekleri için İXTİF 0216 755 3 555 hattından bilgi alabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: ES16-RSi');
    }
}
