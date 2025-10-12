<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL252X3_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL252X3')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL252X3'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF EFL252X3: İç ve Dış Mekânda Tek Gövdede Çok Yönlülük</h2>
  <p>İXTİF EFL252X3; 2.5 ton nominal kapasite, 80V Li‑Ion enerji mimarisi ve modern PMSM sürüş sistemiyle, depo içi raf koridorlarından açık saha yüklemelerine kadar geniş bir kullanım bandını tek platformda birleştirir. Yüksek yerden açıklık ve büyük lastik kombinasyonu engebeli zeminlerde stabil tutunma sağlarken, 2250 mm dönüş yarıçapı dar alanlarda operatöre gerçek çeviklik sunar. Tek faz entegre şarj cihazı 16A prizle çalışır; bu sayede sahada özel altyapı gerekmeksizin fırsat şarjı yapılabilir. Ergonomik çalışma bölgesi; sadeleştirilmiş gösterge paneli, ayarlanabilir direksiyon ve konfor pedalıyla uzun vardiyalarda yorgunluğu azaltır.</p>
</section>
<section>
  <h3>Teknik Güç ve Dayanıklılık</h3>
  <p>Platformun kalbinde, yüksek verim katsayısıyla öne çıkan kalıcı mıknatıslı senkron motor (PMSM) bulunur. Sürüş motoru S2 60 dakikada 8 kW, kaldırma motoru ise S3 %15 görev çevriminde 16 kW güç üreterek 2.5 tonluk yükleri 0.28/0.36 m/sn hızlarında kaldırır ve 0.40/0.43 m/sn hızlarıyla kontrollü şekilde indirir. 80V/150Ah Li‑Ion batarya standarttır; 80V/280Ah opsiyonu çok vardiyalı çalışmalarda menzili artırır. Li‑Ion kimya doğası gereği sıfır bakım, hızlı şarj ve yüksek çevrim ömrü sunar. 11/12 km/sa yürüyüş hızları, %15/%15 eğim kabiliyeti ve 74 dB(A) operatör kulak seviyesi, yoğun operasyonlarda hem performans hem de konfor dengesi sağlar.</p>
  <p>Şasi ve direk geometrisi kararlılığa odaklanır: 1650 mm dingil mesafesi, 3611 mm toplam uzunluk ve 1154 mm gövde genişliği manevra alanını optimize eder. 2070 mm kapalı direk yüksekliği ve 3000 mm kaldırma yüksekliği standart konfigürasyondur; 4095 mm açık direk yüksekliğiyle istif sonlarında görüş korunur. 1040 mm çatal taşıyıcı genişliği (sınıf 2A) ve 50×122×1070 mm çatal ölçüleri, palet standardizasyonuna uygunluk sunar. Katı lastikler (7.00‑12 ön, 18×7‑8 arka) delik riskini ortadan kaldırırken, su sıçramasına dayanıklı yapı sayesinde yağmur altında dahi operasyon kesintisiz devam eder.</p>
</section>
<section>
  <h3>Operasyonel Verimlilik ve Ergonomi</h3>
  <p>EFL252X3; basitleştirilmiş gösterge paneliyle kritik bilgileri net sunar, ayarlanabilir direksiyon farklı operatör boylarına uyum sağlar, geniş kumanda alanı ve rahat pedal grubu uzun süreli kullanımda bile konfor sağlar. 2250 mm dönüş yarıçapı, 3946/4146 mm koridor gereksinimleri (1000×1200 enine / 800×1200 boyuna) ve yüksek yerden açıklık; hem dar koridorlu raf sahalarında hem de bozuk zeminli yükleme alanlarında verimli hareket sağlar. Tek faz 16A entegre şarj cihazı, vardiya aralarında kısa fırsat şarjlarıyla gün boyu operasyonu destekler. PMSM teknolojisi, klasik motorlara kıyasla yaklaşık %10 enerji tasarrufu sağlayarak toplam sahip olma maliyetini düşürür.</p>
  <p>Bakım gereksinimleri Li‑Ion mimari sayesinde minimaldir. Lateral çekilip kolayca değiştirilebilen batarya, şarj altyapısının kısıtlı olduğu sahalarda bile operasyon sürekliliği sağlar. Hidrolik hizmet freni ve mekanik park freni eğimli rampalarda güven hissini pekiştirir. Yüksek dayanımlı direk tasarımı ve 6°/10° eğim açıları, hassas istif ve boşaltma hareketlerinde operatöre esneklik kazandırır.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>İXTİF EFL252X3; çok yönlü zemin kabiliyeti, dar alan çevikliği ve Li‑Ion/PMSM verimliliği ile karma operasyonları tek bir makinede çözmek isteyen işletmeler için ideal bir seçenektir. Teknik danışmanlık ve teklif için bizi arayın: <strong>0216 755 3 555</strong>.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2500 kg (Q), c=500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 150Ah Li‑Ion (opsiyon 280Ah)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11 / 12 km/sa (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '2250 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li‑Ion Enerji', 'description' => 'Bakım gerektirmeyen 80V mimari, fırsat şarjı ve uzun çevrim ömrü.'],
                ['icon' => 'bolt', 'title' => 'PMSM Verimliliği', 'description' => '%10’a kadar enerji tasarrufu ve daha uzun vardiya süresi.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Manevra', 'description' => '2250 mm dönüş yarıçapı ile dar alanlara rahat erişim.'],
                ['icon' => 'shield-alt', 'title' => 'Dış Mekân Koruması', 'description' => 'Su sıçramasına dayanıklı yapı ile yağmurda çalışma.'],
                ['icon' => 'cog', 'title' => 'Engebeli Zemin Kabiliyeti', 'description' => 'Yüksek yerden açıklık ve büyük lastikler ile stabil sürüş.'],
                ['icon' => 'briefcase', 'title' => 'Ergonomik Kokpit', 'description' => 'Ayarlanabilir direksiyon ve sade panel ile konfor.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Dar koridorlu depo içi raf sahalarında palet istifleme ve alma'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve hat besleme operasyonları'],
                ['icon' => 'car', 'text' => 'Otomotiv tedarik depolarında rampa yükleme ve boşaltma'],
                ['icon' => 'snowflake', 'text' => 'Açık saha ve değişken hava koşullarında yük transferi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde çapraz sevkiyat'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolarda kontrollü ve güvenli taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik lojistiğinde hassas palet akışı'],
                ['icon' => 'box-open', 'text' => 'E‑ticaret fulfillment merkezlerinde yoğun vardiya içi akış']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'PMSM motor mimarisi ile daha düşük enerji tüketimi ve yüksek tork'],
                ['icon' => 'battery-full', 'text' => '80V Li‑Ion sistem: hızlı şarj, sıfır bakım, opsiyonel 280Ah kapasite'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve 2250 mm dönüş yarıçapı ile çevik operasyon'],
                ['icon' => 'shield-alt', 'text' => 'Su korumalı yapı ve katı lastikler; zorlu zeminde süreklilik'],
                ['icon' => 'star', 'text' => 'Ergonomik kokpit tasarımı ile operatör veriminde artış']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek ve Şişeleme'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistiği'],
                ['icon' => 'flask', 'text' => 'Kimya ve Petrokimya'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Teknolojisi'],
                ['icon' => 'car', 'text' => 'Otomotiv ve Yan Sanayi'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım, Bahçe ve Peyzaj'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri ve Yem'],
                ['icon' => 'briefcase', 'text' => 'B2B Endüstriyel Tedarik'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi sağlanır. Li‑Ion batarya modülleri satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'cog', 'name' => '80V/280Ah Batarya Paketi', 'description' => 'Standart 150Ah yerine artırılmış kapasite ile çok vardiyalı çalışma için menzil artışı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Tek Faz Entegre Şarj Cihazı', 'description' => '16A priz uyumlu entegre şarj cihazı ile kolay fırsat şarjı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'grip-lines-vertical', 'name' => 'İzsiz Katı Lastik Seti', 'description' => 'Zemin izi bırakmayan katı lastik seçeneği ile temiz alan uyumluluğu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'tachometer-alt', 'name' => 'Saatmetreli Batarya Göstergesi', 'description' => 'Çalışma saati ve şarj seviyesini aynı anda gösteren gösterge modülü.', 'is_standard' => true, 'is_optional' => false, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '80V Li‑Ion batarya günlük fırsat şarjına uygun mudur ve kaç döngü dayanır?', 'answer' => 'Li‑Ion kimya bellek etkisi olmadan kısa molalarda şarj edilebilir. Tipik kullanımda binlerce döngü ömrü sunar ve yüksek kullanılabilirlik sağlar.'],
                ['question' => 'PMSM motoru klasik AC motora kıyasla nasıl enerji tasarrufu sağlar?', 'answer' => 'Kalıcı mıknatıslı senkron yapı rotor kayıplarını azaltır. Bu sayede yaklaşık %10 verim artışı ve daha düşük tüketim elde edilir.'],
                ['question' => 'Dar koridor performansı nasıldır ve minimum dönüş alanı nedir?', 'answer' => 'Şasi 2250 mm dönüş yarıçapına sahiptir. 1000×1200 enine palette ~3946 mm, 800×1200 boyuna palette ~4146 mm koridor gereksinimi bulunur.'],
                ['question' => 'Dış mekânda yağmur altında kullanımda koruma seviyesi nedir?', 'answer' => 'Su sıçramasına karşı korumalı bileşenler sayesinde yağmur altında da operasyonlar güvenle sürdürülebilir. Elektrik sistemi açık suya maruz bırakılmamalıdır.'],
                ['question' => 'Engebeli zeminde lastik ve yerden açıklık avantajı nedir?', 'answer' => 'Büyük katı lastikler ve yüksek yerden açıklık, pürüzlü yüzeylerde titreşimi azaltır ve şasi korumasını artırır.'],
                ['question' => 'Standart çatal ölçüsü ve taşıyıcı sınıfı hangi paletleri destekler?', 'answer' => '50×122×1070 mm çatal ve sınıf 2A taşıyıcı, yaygın EUR ve ISO paletlerle uyumlu genel amaçlı kullanım sağlar.'],
                ['question' => 'Kaldırma ve indirme hızları yükleme döngüsünü nasıl etkiler?', 'answer' => '0.28/0.36 m/sn kaldırma ve 0.40/0.43 m/sn indirme hızları, hassas istif ve hızlı çevrim süreleri için dengelenmiştir.'],
                ['question' => 'Eğim performansı nedir, rampalarda nasıl davranır?', 'answer' => 'Maksimum %15/%15 eğim kabiliyeti sunar. Hidrolik hizmet freni ve mekanik park freni rampalarda güven sağlar.'],
                ['question' => 'Bataryanın lateral çıkarılabilir yapısı sahada ne kazandırır?', 'answer' => 'Yedek batarya ile hızlı değişim sağlanır; şarj altyapısı zayıf sahalarda bile vardiya sürekliliği korunur.'],
                ['question' => 'Operatör konforu için kokpitte hangi iyileştirmeler var?', 'answer' => 'Ayarlanabilir direksiyon, sade panel, ergonomik pedal ve geniş çalışma alanı; yorgunluğu düşürür, hata riskini azaltır.'],
                ['question' => 'Kurumsal güvenlik ve bakım açısından hangi standartlar desteklenir?', 'answer' => 'CE uygunluğu, düşük gürültü seviyesi ve basitleştirilmiş elektronik mimari; bakım süreçlerini kısaltır, emniyet kültürünü destekler.'],
                ['question' => 'Garanti süresi ve satış sonrası desteğe nasıl ulaşırım?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. İXTİF satış ve teknik destek için 0216 755 3 555 numarasından ekibimize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EFL252X3');
    }
}
