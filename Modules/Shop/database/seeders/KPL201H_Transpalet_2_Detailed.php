<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KPL201H_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'KPL201H')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF KPL201H: Hız, kontrol ve dayanıklılık bir arada</h2>
  <p>Depo kapıları açıldığında ilk hareketi başlatan ekipman güven verir. KPL201H, 2.0 ton sınıfında Li-Ion enerjiyle çalışan ağır hizmet tipi sürücülü transpalet olarak, yoğun vardiyalarda bile ivmesini kaybetmeyen bir akış sağlar. Yükte 9 km/s, yüksüz 12 km/s hıza ulaşan makine; kısa şasi, düşük ağırlık merkezi ve güç destekli direksiyon sayesinde dar koridorlarda dahi dengeli ve öngörülebilir bir sürüş sunar. Yarı kapalı operatör bölmesi, dolgulu sırt dayama ve süspansiyonlu platform, operatör yorgunluğunu azaltarak vardiya sonunda bile tutarlı performans sağlar.</p>
</section>
<section>
  <h3>Teknik</h3>
  <p>24V/205Ah Li-Ion batarya, harici 100A şarj cihazıyla fırsat şarjına uygundur ve gaz emisyonu olmadığından kapalı alanlarda güvenli çalışmayı destekler. 3.0 kW AC dikey tahrik motoru ve optimize edilmiş kontrolör, hızlı ivmelenme ve yük altında bile kararlı hız sunar. 2000 kg kapasite ve 600 mm yük merkez mesafesiyle sınıf standartlarını karşılayan KPL201H; 734 mm gövde genişliği, 2195 mm toplam uzunluk ve 1045 mm yük yüzüne kadar uzunluk (kısaltılmış şasi ile -184 mm) ile manevra avantajı sağlar. 55×170×1150 mm çatal ölçüleri ve 540/685 mm çatal aralığı, yaygın palet tipleriyle tam uyumludur. 2034 mm dönüş yarıçapı ve dönüşte otomatik hız azaltma fonksiyonu, sık kavşaklarda güvenliği artırır. Poliüretan tekerlekler sessiz ve çevik hareketi desteklerken elektromanyetik servis freni hassas duruş sağlar.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>Yoğun sevkiyat ritmi, operatör konforu ve enerji verimliliğini aynı platformda buluşturan İXTİF KPL201H, hızlı işletmeler için güvenilir bir çekirdek ekipmandır. Doğru konfigürasyon ve aksesuarlarla, çapraz sevkiyat ve hat besleme gibi görevlerde temposunu korur. Daha fazla bilgi ve yerinde demo için 0216 755 3 555 numarasından bize ulaşın.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 205Ah Li-Ion (harici 100A şarj)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '9 / 12 km/s (yük / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '2034 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion teknoloji', 'description' => 'Fırsat şarjı, sıfır bakım ve emisyonsuz çalışma sağlar.'],
                ['icon' => 'bolt', 'title' => 'Güçlü tahrik', 'description' => '3.0 kW AC motor hızlı ivmelenme ve kararlı hız verir.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt şasi', 'description' => 'Kısalan şasi dar alanlarda manevra üstünlüğü sunar.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenlik odaklı', 'description' => 'Dönüşte otomatik hız azaltma ve elektromanyetik fren.'],
                ['icon' => 'steering-wheel', 'title' => 'Güç destekli direksiyon', 'description' => 'Hassas kontrol ve düşük eforla yönlendirme.'],
                ['icon' => 'layer-group', 'title' => 'Operatör konforu', 'description' => 'Süspansiyonlu platform ve dolgulu sırt dayama.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yoğun çapraz sevkiyat hatlarında palet akışının sürdürülmesi'],
                ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde sipariş konsolidasyonu ve hat besleme'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında raf arası transfer'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında yüksek tempolu mal kabul ve çıkış'],
                ['icon' => 'snowflake', 'text' => 'Gıda lojistiğinde soğuk oda giriş-çıkış hareketleri'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ürünlerin güvenli taşınması'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça akışında rampa yaklaşımı'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP taşıma ve hat besleme']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Daha güçlü kontrolör ve tahrik motoru ile hızlı ivmelenme'],
                ['icon' => 'battery-full', 'text' => '205Ah Li-Ion batarya ile uzun dayanım ve fırsat şarjı'],
                ['icon' => 'arrows-alt', 'text' => 'Kısa şasi ve düşük ağırlık merkezi ile gelişmiş stabilite'],
                ['icon' => 'shield-alt', 'text' => 'Dönüşte hız azaltma ile yüksek hızlarda güvenlik'],
                ['icon' => 'steering-wheel', 'text' => 'Güç destekli direksiyon ile zahmetsiz manevra']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Dayanıklı Tüketim'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri ve Yemek']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay fabrika garantisi kapsamındadır. Li-Ion batarya modülleri satın alım tarihinden itibaren 24 ay garantiye tabidir. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Harici 100A Şarj Cihazı', 'description' => 'Standart teslimat kapsamında yüksek akımlı harici şarj cihazı.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Yan Koruma Barı', 'description' => 'Operatör bölmesi yan koruma geliştirmesi.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Ek Batarya Modülü', 'description' => 'Yüksek yoğunluk senaryoları için ek Li-Ion modül opsiyonu.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Tekerlek Seti', 'description' => 'Düşük gürültü ve zemin dostu poliüretan tekerlekler.', 'is_standard' => true, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'KPL201H hangi hızlara ulaşır ve yükte performansı nasıldır?', 'answer' => 'Yükte 9 km/s, yüksüz 12 km/s hızlara çıkar. 3.0 kW AC tahrik motoru sayesinde yük altında da ivmesini korur.'],
                ['question' => 'Li-Ion batarya şarj stratejisi ve bakım gereksinimi nedir?', 'answer' => '24V/205Ah Li-Ion batarya fırsat şarjına uygundur, bakım gerektirmez ve gaz emisyonu yoktur. Harici 100A şarj cihazı standarttır.'],
                ['question' => 'Manevra kabiliyeti ve dönüş güvenliği nasıl sağlanır?', 'answer' => 'Kısa şasi, düşük ağırlık merkezi ve dönüşte otomatik hız azaltma ile dar koridorlarda dengeli, güvenli manevra sağlar.'],
                ['question' => 'Operatör konforunu artıran unsurlar nelerdir?', 'answer' => 'Süspansiyonlu platform, dolgulu sırt dayama ve yarı kapalı bölme uzun vardiyalarda yorgunluğu azaltır.'],
                ['question' => 'Hangi palet ölçüleri ile uyumludur?', 'answer' => '55×170×1150 mm çatal ve 540/685 mm çatal aralığı, EUR ve yaygın endüstriyel paletlerle uyumludur.'],
                ['question' => 'Eğim performansı ve frenleme sistemi nasıldır?', 'answer' => 'Maksimum eğim yükte %8, yüksüz %16’dır. Elektromanyetik fren hassas ve güvenilirdir.'],
                ['question' => 'Direksiyon sistemi ne türdür ve eforu azaltır mı?', 'answer' => 'Elektronik güç destekli direksiyon, hassas kontrol sağlar ve operatör eforunu önemli ölçüde düşürür.'],
                ['question' => 'Ses seviyesi ve iç mekân kullanımı uygun mudur?', 'answer' => '74 dB(A) ses düzeyi ve emisyonsuz Li-Ion enerji ile kapalı alan kullanımı güvenlidir.'],
                ['question' => 'Bakım aralıkları ve toplam sahip olma maliyeti nasıldır?', 'answer' => 'AC motor ve Li-Ion sistem düşük bakım ihtiyacı ve yüksek çalışma süresiyle TCO’yu düşürür.'],
                ['question' => 'Standart teslimatta hangi ekipmanlar vardır?', 'answer' => 'Harici 100A şarj cihazı, PU tekerlekler ve yarı kapalı operatör bölmesi standarttır.'],
                ['question' => 'Opsiyon ve konfigürasyon seçenekleri sunuluyor mu?', 'answer' => 'Koruma barları, ek batarya modülü ve farklı tekerlek seçimleri gibi opsiyonlar mevcuttur.'],
                ['question' => 'Garanti kapsamı ve servis desteği nasıldır?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garantilidir. İXTİF satış-sonrası destek ve yedek parça için 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: KPL201H');
    }
}
