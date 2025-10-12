<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL181_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL181')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL181'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF EFL181: Kompakt gücün akıllı enerjisi</h2>
  <p>Günün ilk sevkiyatında dar kapı eşiğinden raf aralarına, rampadan açık sahaya geçişleriniz hızlı ve kesintisiz olmalı. EFL181 tam da bu senaryolar için tasarlandı: I/C şasi esinli kompakt gövde, 48V Li-Ion enerji mimarisi ve entegre tek faz şarj cihazı sayesinde esnek kullanım sunar. 1.8 ton nominal kapasite, 3000 mm standart kaldırma ve 8.5/9 km/s seyir hızı ile düşük vardiya sürelerinde dahi yüksek verim elde edersiniz.</p>
</section>
<section>
  <h3>Teknik yapı ve performans</h3>
  <p>48V/150Ah Li-Ion batarya modülü, bakım gerektirmeyen AC çekiş sistemiyle eşleşir. 6 kW çekiş ve 7.5 kW kaldırma motoru, 0.25/0.30 m/s kaldırma ve 0.43/0.45 m/s indirme hızlarıyla yük döngülerini hızlandırır. 1920 mm dönüş yarıçapı ve 2015 mm çatala kadar uzunluk ile dar koridorlara emin giriş yapılır; 1080 mm toplam genişlik, çoğu koridor standardıyla uyumludur. 905/920 mm iz genişlikleri dengeyi artırırken, 6.5-10 ön ve 5.00-8 arka katı lastikler farklı zeminlerde tutuş sağlar. Yağmura dayanıklı bileşenler ve 2080 mm üst koruma yüksekliği, açık alan operasyonlarında operatör güvenliğini destekler. Entegre 16A Plug&Play şarj, mola anlarında fırsat şarjına izin vererek batarya değişimini gereksiz kılar.</p>
</section>
<section>
  <h3>Sonuç ve teklif</h3>
  <p>EFL181, günde birkaç saatlik yoğunluklarda giriş seviyesi bütçeyle güvenilir bir çözüm sunar. Ergonomik bucket koltuk ve ayarlanabilir direksiyon uzun vardiyalarda konfor sağlar; kompakt yapısı ise karma geçişlerde operasyona esneklik katar. Doğru mast seçenekleriyle raf yüksekliğinize uyarlanır ve toplam sahip olma maliyetini aşağı çeker. Teknik değerlendirme ve en uygun konfigürasyon için bizi arayın: 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1800 kg (Q)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V / 150Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '8.5 / 9 km/s (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1920 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Fırsat şarjı', 'description' => 'Tek faz entegre şarj ile mola anlarında takviye enerji'],
                ['icon' => 'bolt', 'title' => 'AC tahrik', 'description' => '6 kW çekiş, 7.5 kW kaldırma ile çevik hızlanma'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt manevra', 'description' => '2015 mm L2 ve 1920 mm Wa ile dar koridor uyumu'],
                ['icon' => 'shield-alt', 'title' => 'Dayanıklı yapı', 'description' => 'Yağmura dayanıklı komponentlerle açık alan kullanımı'],
                ['icon' => 'star', 'title' => 'Ergonomi', 'description' => 'Ayarlanabilir direksiyon ve rahat bucket koltuk'],
                ['icon' => 'industry', 'title' => 'Güvenilirlik', 'description' => 'Olgun, piyasada kanıtlanmış komponent ekosistemi']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarda raf besleme ve cross-dock aktarımları'],
                ['icon' => 'box-open', 'text' => 'E-ticaret sipariş konsolidasyonu ve çıkış rampası sevkiyatı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde palet iç lojistiği'],
                ['icon' => 'snowflake', 'text' => 'Gıda merkezlerinde giriş-çıkış ve kısa mesafe transfer'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında hassas yük taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında rampa yaklaşımı'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP malzeme akışı'],
                ['icon' => 'building', 'text' => 'Tesis içi tedarik ve bakım lojistiği']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC tahrik ve Li-Ion enerji ile yüksek verim ve düşük bakım'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve dar dönüş ile sınırlı alanlarda çeviklik'],
                ['icon' => 'battery-full', 'text' => 'Tek faz entegre şarjla altyapı kolaylığı ve fırsat şarjı'],
                ['icon' => 'shield-alt', 'text' => 'Yağmura dayanıklı bileşenlerle iç/dış mekân kullanım esnekliği'],
                ['icon' => 'star', 'text' => 'Ergonomik sürüş pozisyonu ile operatör verimliliği'],
                ['icon' => 'briefcase', 'text' => 'Giriş seviyesi toplam sahip olma maliyeti (TCO)']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'car', 'text' => 'Otomotiv ve Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel İmalat'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri Deposu'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Ticaret'],
                ['icon' => 'award', 'text' => 'Lüks Ürün Lojistiği'],
                ['icon' => 'certificate', 'text' => 'Kamu ve Belediyeler'],
                ['icon' => 'box-open', 'text' => 'Ambalaj ve Matbaa Tedariki'],
                ['icon' => 'warehouse', 'text' => 'Yedek Parça Dağıtım Merkezleri'],
                ['icon' => 'store', 'text' => 'DIY & Hırdavat Depoları'],
                ['icon' => 'flask', 'text' => 'Boya ve Kaplama Lojistiği'],
                ['icon' => 'microchip', 'text' => 'Telekom ve Donanım Depoları'],
                ['icon' => 'industry', 'text' => 'Enerji ve Endüstriyel Tesis Servisi']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay fabrika garantisine tabidir. Li-Ion batarya modülleri 24 ay garanti kapsamındadır; garanti normal kullanım koşullarındaki üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Tek Faz Şarj Kablosu', 'description' => '16A priz ile hızlı bağlantı; dahili şarj cihazı için uygun kablo ve fiş takımı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Harici Şarj İstasyonu', 'description' => 'Depo dışında da şarj imkânı sağlayan ek istasyon; filo esnekliğini artırır.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Yan Kaydırma Ataçmanı', 'description' => 'Hassas konumlandırma için hidrolik yan kaydırma aparatı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Uzun Çatal Seti', 'description' => '1070–1220 mm arası farklı çatal uzunluğu seçenekleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Günlük 2-4 saat kullanımda batarya ömrü ne kadar etkilenir?', 'answer' => 'Li-Ion kimya kısmi şarjı tolere eder; 150Ah kapasiteyle fırsat şarjı sayesinde vardiya boyunca stabil voltaj korunur ve ömür döngüsü uzar.'],
                ['question' => 'Açık alanda yağmur altında çalıştırmak güvenli midir?', 'answer' => 'Ana komponentler yağmura dayanıklı tasarlanmıştır. Doğrudan su püskürtme ve sel koşullarından kaçınılmalı, planlı bakım periyodu korunmalıdır.'],
                ['question' => 'Dar koridorlarda minimum dönüş mesafesi nedir?', 'answer' => 'Dönüş yarıçapı 1920 mm’dir. 1000×1200 ve 800×1200 paletler için önerilen koridor genişlikleri sırasıyla 3525 ve 3725 mm’dir.'],
                ['question' => 'Standart mast ile maksimum kaldırma yüksekliği nedir?', 'answer' => 'Standart direk ile 3000 mm kaldırma ve 4028 mm tam açılmış yükseklik elde edilir; farklı mast seçenekleri ile bu değer yükseltilebilir.'],
                ['question' => 'Hangi zeminde hangi lastikler tavsiye edilir?', 'answer' => 'Katı (solid) lastikler çok amaçlıdır. Pürüzsüz zeminlerde titreşim azaltır; dış mekânda dayanıklılık ve delinmeye direnç sağlar.'],
                ['question' => 'Eğimli rampalarda çekiş performansı nasıldır?', 'answer' => 'Maksimum tırmanma kabiliyeti yüklü/yüksüz %10.5/%15’tir. Rampa yaklaşımında yük dağılımına ve hız yönetimine dikkat edilmelidir.'],
                ['question' => 'Operatör konforunu artıran unsurlar nelerdir?', 'answer' => 'Ayarlanabilir direksiyon, bucket tip koltuk ve kompakt gövde; görüş ve vücut mekaniğini destekleyerek yorgunluğu azaltır.'],
                ['question' => 'Bakım gereksinimleri dizel modellere göre nasıldır?', 'answer' => 'Yağ değişimi, filtre ve kayış gibi kalemler bulunmadığından planlı bakım süreleri kısadır ve toplam maliyet düşer.'],
                ['question' => 'Şarj altyapısı için özel bir istasyon gerekli mi?', 'answer' => 'Hayır. Entegre tek faz şarj cihazı 16A standart priz ile çalışır. Endüstriyel kullanımda hat sigortası ve kablo kesiti uygun seçilmelidir.'],
                ['question' => 'Gürültü ve titreşim seviyeleri operatörü nasıl etkiler?', 'answer' => 'Sürüş esnasında 70 dB(A) seviyesinde çalışır; elektrikli aktarma organları düşük titreşim ve sessiz operasyon sunar.'],
                ['question' => 'Yan kaydırma kullanıldığında kapasite etkilenir mi?', 'answer' => 'Evet. İç yan kaydırmada yaklaşık 150 kg, haricide 200 kg kapasite düşümü kabul edilir; yük diyagramına uygun çalışılmalıdır.'],
                ['question' => 'Garanti kapsamı nedir ve uzatılmış destek alabilir miyim?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garantilidir. Satış, servis ve yerinde eğitim için İXTİF ile iletişim: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EFL181');
    }
}
