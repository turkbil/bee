<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPL154_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPL154')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı (EPL154)');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF EPL154: Kompakt gövde, Li‑Ion enerji, maksimum çeviklik</h2>
  <p>İXTİF EPL154, 1.5 ton kapasiteyi yalnızca 160 kg servis ağırlığı ve <strong>l2=400 mm</strong> gövde uzunluğu ile birleştirerek dar alan verimliliğini üst seviyeye taşır. <strong>24V/30Ah</strong> çıkarılabilir Li‑Ion batarya, metal koruma kapağı ve entegre şarj cihazı sayesinde esnek enerji yönetimi sağlar. Standart endüstriyel yüzer denge tekerleri yüksek stabilite sunar; <strong>1330 mm</strong> dönüş yarıçapı ve <strong>4.5/5 km/s</strong> sürüş hızı ile raf aralarında akıcı manevra elde edilir. Kaplumbağa (creep) modu, kol dikey durumdayken hassas ve güvenli ilerleme sağlar; böylece kapı girişleri, rampalar ve sıkışık koridor geçişleri daha kontrollü hale gelir.</p>
</section>
<section>
  <h3>Teknik güç ve güvenilir tahrik</h3>
  <p>Olgun ve güvenilir tahrik mimarisi ile bilinen EPT20‑15ET platformundan türeyen tahrik sistemi, <strong>0.75 kW</strong> sürüş motoru ve <strong>0.8 kW</strong> kaldırma motoru ile dengeli performans verir. <strong>PU sürüş/yük tekerlekleri</strong> sessiz ve zemine dost çalışma sunarken, standart şasi üzerinde <strong>Ø210x70 mm</strong> sürüş ve <strong>Ø80x60 mm</strong> (opsiyonel <strong>Ø74x88 mm</strong>) yük tekeri seçenekleri mevcuttur. <strong>115 mm</strong> kaldırma yüksekliği, <strong>80 mm</strong> alçaltılmış çatal yüksekliği ve <strong>50/150/1150 mm</strong> çatal boyutları, palet giriş çıkışlarında optimum açı ve açıklık sağlar. <strong>30 mm</strong> merkez yerden yükseklik, eşik ve genleşme derzlerinde geçişi kolaylaştırır. Tam yükte <strong>%6</strong>, yüksüzde <strong>%16</strong> eğim tırmanma kabiliyeti; ayrıca 1000 kg yük ile <strong>%10</strong> seviyesini hedefleyen tasarım yaklaşımı, rampa ve kısa mesafe iç lojistikte süreklilik sağlar.</p>
  <p>Enerji tarafında, <strong>24V/30Ah</strong> Li‑Ion batarya <em>Battery Management System</em> (BMS) ile korunur; hücre dengesi, aşırı akım ve sıcaklık gözetimi gibi fonksiyonlar kullanım güvenliğini artırır. <strong>Metal koruma kapağı</strong> çabuk açılır‑kapanır yapısıyla servis erişimini hızlandırır ve fişin kazara çıkmasını önlemeye yardımcı olur. Entegre (on‑board) şarj cihazı, vardiya aralarında enerjiyi tazeleme imkânı verir; işletmeler harici şarj altyapısına bağımlı kalmadan operasyonu sürdürebilir.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>EPL154; hafif şasi, yüzer denge tekerleri, Li‑Ion enerji paketi ve güvenilir tahrik sistemiyle KOBİ depolarından 3PL merkezlerine kadar geniş bir yelpazede değer üretir. Kompakt ölçüler, düşük gürültü düzeyi (<strong>&lt;74 dB(A)</strong>) ve mekanik direksiyonun sezgisel hissi; yeni kullanıcıların hızla adapte olmasını sağlar. Dar alanlarda çeviklik kadar, toplam sahip olma maliyetini düşüren bakım dostu yapı da öne çıkar. Detaylı bilgi ve teklif için arayın: <strong>0216 755 3 555</strong>.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 30Ah Li‑Ion, çıkarılabilir'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 / 5 km/s (yüklü / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1330 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li‑Ion Enerji', 'description' => '24V-30Ah batarya BMS ile güvenli ve hızlı şarj.'],
                ['icon' => 'compress', 'title' => 'Kompakt Şasi', 'description' => 'l2=400 mm ile dar koridorlarda rahat hareket.'],
                ['icon' => 'layer-group', 'title' => 'Denge Tekerleri', 'description' => 'Endüstriyel yüzer tekerler ile stabilite.'],
                ['icon' => 'shield-alt', 'title' => 'Metal Kapak', 'description' => 'Batarya fişinin kazara çıkmasını önlemeye yardımcı.'],
                ['icon' => 'hand', 'title' => 'Creep Modu', 'description' => 'Dikey kolda yavaş ve kontrollü ilerleme.'],
                ['icon' => 'cog', 'title' => 'Olgun Tahrik', 'description' => 'EPT20‑15ET temelli güvenilir sistem.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret sipariş toplama alanlarında yakın mesafe palet transferleri'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında dar koridor içi besleme ve sevkiyat öncesi konsolidasyon'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde raf arası ürün hareketi ve cross‑dock'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş‑çıkış ve yükleme rampası yaklaşımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik lojistiğinde hassas ürün palet akışı'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında montaj hattına hat besleme'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim kolilerinin WIP alanlarına taşınması'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında yarı mamul (WIP) taşıma operasyonları']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Kompakt gövde ve 1330 mm dönüş yarıçapı ile üstün manevra'],
                ['icon' => 'battery-full', 'text' => 'BMS’li Li‑Ion batarya, çıkarılabilir yapı ve entegre şarj kolaylığı'],
                ['icon' => 'bolt', 'text' => 'Olgun tahrik sistemi ve PU tekerler ile tutarlı performans'],
                ['icon' => 'shield-alt', 'text' => 'Metal batarya kapağı ve düşük gürültü <74 dB(A) çalışma']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincir Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Dağıtımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'building', 'text' => 'Tesis Yönetimi ve Kampüs Lojistiği'],
                ['icon' => 'briefcase', 'text' => 'B2B Depo ve Kurumsal Lojistik'],
                ['icon' => 'cart-shopping', 'text' => 'Toptan Ticaret Merkezleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alma tarihinden itibaren 12 ay fabrika garantisindedir. Li‑Ion batarya modülü 24 ay garanti kapsamındadır. Garanti üretim hatalarını ve normal kullanım koşullarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı 24V‑10A', 'description' => 'Makine üzerinde yerleşik, vardiya aralarında fırsat şarjına imkân veren standart şarj çözümü.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'charging-station', 'name' => 'Harici Şarj Cihazı 24V‑10A', 'description' => 'Şarj alanında kullanım için bağımsız ünite; filo yönetiminde esneklik sağlar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tandem PU Yük Teker Seti', 'description' => 'Zemin koşullarına göre daha iyi yük dağılımı ve aşınma performansı sunar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yedek Li‑Ion Batarya 24V‑30Ah', 'description' => 'Hızlı değişim için ikinci modül; vardiya sürekliliğini artırır.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO'],
                ['icon' => 'shield-alt', 'name' => 'EN 1175 Güvenlik', 'year' => '2024', 'authority' => 'CEN']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EPL154 hangi maksimum palet ağırlığını güvenle taşıyabilir?', 'answer' => 'Nominal kapasite 1500 kg’dır. 600 mm yük merkezinde bu değer sağlanır; farklı yük merkezlerinde kapasite değişebilir.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti hangi ölçü ile ifade edilir?', 'answer' => 'Dönüş yarıçapı 1330 mm’dir. Ayrıca l2=400 mm gövde uzunluğu dar alanlarda yaklaşımı kolaylaştırır.'],
                ['question' => 'Standart çatal ölçüleri ve palet uyumu nasıldır?', 'answer' => '50/150/1150 mm çatal ölçüleri ve 540 mm çatal aralığı EUR paletlerle uyumludur; farklı uzunluk/genişlik opsiyonları bulunur.'],
                ['question' => 'Enerji sistemi kaç volt ve kaç amper-saat kapasitededir?', 'answer' => '24V/30Ah Li‑Ion modül kullanılır. BMS denetimiyle güvenlik ve hücre dengesi sağlanır.'],
                ['question' => 'Şarj mimarisi işletme içinde nasıl esneklik sunar?', 'answer' => 'Makine üzerinde entegre 24V‑10A şarj cihazı bulunur. İstenirse harici şarj çözümleri tercih edilebilir.'],
                ['question' => 'Eğimlerde performans verisi nedir?', 'answer' => 'Maksimum yüzde eğim yüklü %6, yüksüz %16’dır. Kısa rampalarda güvenli ilerleme için hız kontrolü önerilir.'],
                ['question' => 'Gürültü seviyesi kullanıcı konforu açısından ne düzeydedir?', 'answer' => 'Sürücü kulağında ölçülen ses basınç seviyesi 74 dB(A) altındadır; kapalı alanlarda konforlu çalışma sağlar.'],
                ['question' => 'Direksiyon tipi ve kullanım hissi nasıldır?', 'answer' => 'Mekanik direksiyon yapı basit ve sezgiseldir. Creep modu ile milimetrik manevra mümkündür.'],
                ['question' => 'Bakım aralıkları ve servis erişimi nasıl kolaylaştırıldı?', 'answer' => 'Metal kapak sayesinde batarya bölmesine hızlı erişim mümkündür. Modüler yapı temel kontrolleri hızlandırır.'],
                ['question' => 'Hangi tekerlek ve malzeme seçenekleri mevcuttur?', 'answer' => 'Sürüş ve yük tekerleri PU’dur; yük tekerinde tandem seçeneği, sürüşte oyuklu PU seçeneği bulunur.'],
                ['question' => 'Soğuk oda veya düşük sıcaklıkta kullanım için tavsiyeler nelerdir?', 'answer' => 'PU tekerler için zeminin temiz olması önerilir. Kısa fırsat şarjlarıyla Li‑Ion sıcaklık aralığı içinde verimli çalışır.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek nasıl sağlanıyor?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. Satış, servis ve yedek parça için İXTİF destek hattı: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EPL154');
    }
}
