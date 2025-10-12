<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESi161_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ESi161')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: ESi161'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF ESi161: Çift Katlı Verim, Kompakt Güç</h2>
  <p>Depo kapıları açılır açılmaz işler hızlanır; ESi161 tam da bu an için tasarlanmıştır. İkili kaldırma mimarisi sayesinde aynı anda iki paleti hareket ettirerek yükleme, boşaltma ve besleme işlemlerini hızlandırır. Mono direk yapısı ve şeffaf görüş paneli, operatörün çatal uçlarını net görmesini sağlar; bu da hasarı azaltır ve ilk denemede doğru konumlandırma sunar. Kompakt gövde, dar koridorlarda ve kasa içlerinde çalışmayı kolaylaştırır. Kaplumbağa (turtle) düğmesi düşük hızda hassas manevra verir; rampalar ve eşiksiz geçişler için merkez tahrik ve denge tekerleri güven veren çekiş sunar. Li-Ion batarya ile vardiya arasında kısa molalarda dahi hızlı şarj mümkün olur; entegre şarj cihazı ise priz olan her yerde şarj imkânı sağlar.</p>
</section>
<section>
  <h3>Teknik</h3>
  <p>ESi161, 24V/100Ah Li-Ion enerji sistemi ve 0.75 kW tahrik motoruyla 4/4.5 km/sa seyir hızlarını sunar. 2.2 kW kaldırma motoru, 1520 mm&apos;ye kadar kaldırma (h3) ve 1608 mm maksimum kaldırma yüksekliği (H) ile raf besleme ve zemin transferi arasında ideal bir denge kurar. 1600 kg toplam kapasite, mast kaldırmada 800 kg ve taşıyıcı kollarla 1600 kg değerleriyle operasyonel esnekliği artırır. 1265 mm dingil mesafesi, 1473 mm dönüş yarıçapı ve 800 mm toplam genişlik ile dar alan performansı öne çıkar. 55×190×1150 mm çatal ölçüsü ve 620 mm yüze kadar uzunluk, standart EUR paletlerle tam uyum sağlar. Polüretan tekerlekler sessiz, titreşimi düşük sürüş sunarken, elektromanyetik fren güvenli duruş sağlar. Enerji tüketimi EN 16796 standardına göre 0.4 kWh/s düzeyindedir; 22.73 t/saat çıktı ve 63.72 t/kWh verim değerleriyle sınıfında iddialıdır. DC sürüş kontrolü, mekanik direksiyon ile birleştirilerek bakım basitliği ve yüksek çalışma sürekliliği sağlar.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>İki paleti aynı anda taşıyabilme, hızlı şarj ve kompakt tasarım; ESi161 ile depo akışı daha öngörülebilir ve maliyetler daha kontrol edilebilir hale gelir. Operatörler güvenle çalışırken ekipman filonuz daha verimli olur. Teknik bilgi, yerinde keşif ve teklif için bizi arayın: 0216 755 3 555.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1600 kg (Q); mast ile 800 kg (Q1)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 100Ah Li-Ion, entegre şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4 / 4.5 km/sa (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => 'Dönüş yarıçapı 1473 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion Enerji', 'description' => '24V 100Ah ile hızlı fırsat şarjı ve uzun çevrim ömrü'],
                ['icon' => 'bolt', 'title' => 'İkili Kaldırma', 'description' => 'Aynı anda iki palet taşıyarak çevrim süresini kısaltır'],
                ['icon' => 'industry', 'title' => 'Merkez Tahrik', 'description' => 'Stabilizasyon tekerlekleriyle dar koridorda kontrol'],
                ['icon' => 'star', 'title' => 'Tam Görüş', 'description' => 'Şeffaf panel ile çatal uçlarında yüksek görünürlük'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Fren', 'description' => 'Elektromanyetik fren ile kontrollü yavaşlama'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'Mekanik direksiyon ve DC kontrol ile kolay servis']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'EUR paletlerle iki katlı (double-deck) taşıma ve rampa yaklaşımı'],
                ['icon' => 'warehouse', 'text' => 'Dar koridor raf arası istifleme ve toplama besleme'],
                ['icon' => 'store', 'text' => 'Mağaza arkası ve dağıtım merkezinde araç yükleme/boşaltma'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında ara stok (WIP) taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında çevik transfer'],
                ['icon' => 'snowflake', 'text' => 'Soğuk giriş-çıkış alanlarında kısa mesafe taşımalar'],
                ['icon' => 'flask', 'text' => 'Kimya ve kozmetik depolarında hassas ürün hareketi'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde zarar riskini azaltan kontrollü istif']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'İkili kaldırma ile tek turda iki palet transferi ve daha az manevra'],
                ['icon' => 'battery-full', 'text' => 'Entegre şarj cihazı ile priz olan her noktada hızlı şarj olanağı'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi + kaplumbağa modu ile sınıfının ötesinde manevra'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve net görüş ile daha güvenli operasyon'],
                ['icon' => 'star', 'text' => 'Şeffaf panel ve mono direkle hassas istifleme'],
                ['icon' => 'cog', 'text' => 'DC sürüş ve mekanik direksiyon ile basit ve ekonomik bakım']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda'],
                ['icon' => 'pills', 'text' => 'İlaç'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'briefcase', 'text' => 'FMCG'],
                ['icon' => 'building', 'text' => 'Depolama Altyapısı'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim dağıtımı'],
                ['icon' => 'award', 'text' => 'Ambalaj ve matbaa'],
                ['icon' => 'certificate', 'text' => 'Kırtasiye ve yayın'],
                ['icon' => 'star', 'text' => 'Mobilya dağıtım merkezleri'],
                ['icon' => 'bolt', 'text' => 'Beyaz eşya lojistiği'],
                ['icon' => 'cog', 'text' => 'Makine yedek parça depoları'],
                ['icon' => 'plug', 'text' => 'Enerji ve ekipman depoları'],
                ['icon' => 'arrows-alt', 'text' => 'Cross-dock merkezleri'],
                ['icon' => 'shield-alt', 'text' => 'Kargo/kurye aktarma']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode(['coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı', 'description' => 'Makine üzerinde dahili 30A şarj cihazı; günlük fırsat şarjı için uygundur.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Ekstra Li-Ion Batarya Modülü', 'description' => '24V 100Ah paketle uyumlu ilave modül ile vardiya süresi artışı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'PU Teker Seti', 'description' => 'Sessiz ve zemin dostu poliüretan tekerlek seti (ön/arka/destek).', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Çatal Seçenekleri', 'description' => '1000/1150/1220 mm çatal uzunluğu seçenekleri ile uygulama uyumu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'İkili kaldırma ile kaç paleti aynı anda taşıyabilirim?', 'answer' => 'ESi161, taşıyıcı kollar ve mast kombinasyonu ile iki paleti aynı anda güvenle taşıyabilir; mast kaldırmada 800 kg, destek kollarında ise 1600 kg kapasite sunar.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti ne düzeyde sağlanır?', 'answer' => '800 mm genişlik ve 1473 mm dönüş yarıçapı ile standart raf aralarında rahat hareket eder; kaplumbağa modu düşük hızda hassas kontrol verir.'],
                ['question' => 'Rampalarda ve eşiksiz geçişlerde performans nasıl?', 'answer' => 'Merkez tahrik ve stabilizasyon tekerlekleri çekişi artırır; 3%/10% eğim değerleri güvenli sürüş sınırlarını belirtir.'],
                ['question' => 'Enerji tüketimi ve çalışma süresi tahmini nedir?', 'answer' => 'EN 16796’a göre 0.4 kWh/s tüketim sunar. 24V 100Ah Li-Ion paket fırsat şarjıyla vardiya boyunca yüksek erişilebilirlik sağlar.'],
                ['question' => 'Şarj altyapısı gereksinimi ve şarj süresi ne kadar?', 'answer' => 'Entegre 30A şarj cihazı standarttır; vardiya aralarında hızlı şarj imkânı vererek kesintileri azaltır.'],
                ['question' => 'Görüş ve güvenlik özellikleri nelerdir?', 'answer' => 'Mono direk ve şeffaf panel çatalları net gösterir; elektromanyetik fren ve düşük hız modu güvenli kullanım sağlar.'],
                ['question' => 'Bakım aralıkları ve servis kolaylığı nasıl?', 'answer' => 'DC sürüş ve mekanik direksiyon, daha az parça ve basit servis prosedürleri ile planlı duruşları kısaltır.'],
                ['question' => 'Hangi palet ölçüleri ile uyumludur?', 'answer' => '55×190×1150 mm çatal ölçüleri ve 560 mm çatallar arası mesafe; EUR palet ile uyumlu sorunsuz kullanım sağlar.'],
                ['question' => 'Ses seviyesi ve operatör konforu nasıldır?', 'answer' => '74 dB(A) ses seviyesi ile gürültü kontrolüne katkı sağlar; yumuşak PU tekerlekler titreşimi azaltır.'],
                ['question' => 'Standart ekipmanlara ek olarak neler seçilebilir?', 'answer' => 'Ek Li-Ion modül, farklı çatal uzunlukları ve teker setleri gibi seçeneklerle uygulamaya göre özelleştirme mümkündür.'],
                ['question' => 'Teslimat ve devreye alma süreçleri nasıl ilerler?', 'answer' => 'Kurulum, operatör eğitimleri ve güvenlik kontrolleri planlanır; saha koşullarına göre test ve validasyon yapılır.'],
                ['question' => 'Garanti koşulları ve satış sonrası destek hangi kapsamda?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. İXTİF satış ve servis için 0216 755 3 555 üzerinden iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: ESi161');
    }
}
