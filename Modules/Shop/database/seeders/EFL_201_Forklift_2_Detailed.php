<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL_201_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFL201')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFL201');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>EFL201 ile 80V Sınıfında Yeni Standart</h2><p>İXTİF EFL201, ağır hizmet depolardan açık alan sevkiyat noktalarına uzanan geniş bir operasyon yelpazesi için tasarlanmış 2.0 ton kapasiteli elektrikli karşı dengeli bir forkliftir. 80V/150Ah Li‑Ion batarya mimarisi ve entegre 80V/35A şarj cihazı sayesinde fırsat şarjı mümkün olur; bu da çok vardiyalı sahalarda makinenin gün boyunca ritmini kaybetmeden çalışmasını sağlar. Yeni tasarımlı direk yapısı, hidroliğin görüş hattını kapatan borularını azaltır; operatör yük uçlarını ve çevreyi daha net izleyerek hızlı ve güvenli manevralar yapar. 120 mm yer açıklığı ve katı lastikler, rampalı sahalarda ya da bozuk zeminlerde tutunmayı artırır, suya dayanıklı gövde ise dış mekânda her hava koşulunda güven verir.</p></section><section><h3>Teknik Güç ve Verimlilik</h3><p>EFL201 inşa itibarıyla verim odaklıdır: 6 kW S2 sürüş motoru ve 11 kW S3 kaldırma motoru, 11/14 km/s sürüş hızları ve 0.25/0.30 m/s kaldırma değerleri sunar. 2000 kg (Q) kapasite ve 500 mm yük merkezi ile dengeli taşıma sağlanır. 2020 mm kapalı direk yüksekliği ve 4028 mm açık yükseklik, 3000 mm standart kaldırma ile palet operasyonlarının çoğunu kapsar. 2100 mm dönüş yarıçapı, 3342 mm toplam uzunluk ve 1080 mm genişlik; kapalı alanlarda çeviklik sunarken açık alanda stabiliteyi korur. 3137 kg servis ağırlığı ve 4604/533 kg yüklü aks dağılımı, sağlam gövde geometrisinin göstergesidir. Hidrolik servis freni ve mekanik park freni ile yokuş başlangıçları kontrol altındadır; 12/15% tırmanma kabiliyeti ve 10000 N maks. çekiş kuvveti, rampa yaklaşımı ve çekiş gerektiren işler için güven verir.</p></section><section><h3>Sonuç</h3><p>EFL201; ergonomi, bakım kolaylığı ve enerji verimliliğini birleştirerek B2B operasyonlarda toplam sahip olma maliyetini düşürür. Fırsat şarjı ve 80V altyapı ile yoğun vardiyalarda ritmi tutar, yeni görüş alanlı direk tasarımı ise iş güvenliği kültürünü destekler. Doğru konfigürasyon ve uygulama eşleşmesi için uzman ekibimizle konuşun: 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (Q), 500 mm yük merkezi'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 150Ah Li‑Ion, 80V/35A entegre şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11/14 km/s sürüş, 0.25/0.30 m/s kaldırma'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '2100 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '80V Li‑Ion mimari', 'description' => 'Fırsat şarjı ve sıfır bakım ile kesintisiz vardiyalar'],
                ['icon' => 'bolt', 'title' => 'Güçlü tahrik', 'description' => '6 kW sürüş, 11 kW kaldırma ile dengeli performans'],
                ['icon' => 'arrows-alt', 'title' => 'Açık görüşlü direk', 'description' => 'Azaltılmış hatlar ile uç ve çevreyi net görme'],
                ['icon' => 'shield-alt', 'title' => 'Dış saha dayanımı', 'description' => 'Suya dayanıklılık ve 120 mm yer açıklığı'],
                ['icon' => 'briefcase', 'title' => 'Operatör ergonomisi', 'description' => 'Geniş bacak alanı ve ayarlanabilir direksiyon'],
                ['icon' => 'cog', 'title' => 'Kolay bakım', 'description' => 'Basit düzen ile ana bileşenlere hızlı erişim']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarda rampalı alanlarda palet yükleme ve boşaltma'],
                ['icon' => 'box-open', 'text' => 'E‑ticaret fulfillment alanlarında yüksek devirli besleme'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde raf arası malzeme transferi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça lojistiğinde hat besleme ve iç transfer'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında giriş‑çıkış ve kısa mesafe sevkiyat'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde güvenli palet hareketi ve ara stok taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ürünlerin güvenli taşınması'],
                ['icon' => 'industry', 'text' => 'Ağır sanayide WIP taşıma ve forklift rıhtım operasyonları']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '80V platform ile 48V sistemlere kıyasla daha yüksek verim ve çekiş'],
                ['icon' => 'battery-full', 'text' => '150Ah Li‑Ion modül ile hızlı şarj ve bakım gerektirmeyen enerji'],
                ['icon' => 'arrows-alt', 'text' => 'Yeni direk tasarımı ile hızlanan yük konumlandırma ve güvenlik'],
                ['icon' => 'shield-alt', 'text' => 'Suya dayanım ve 120 mm yer açıklığı ile dış mekâna uyum'],
                ['icon' => 'cog', 'text' => 'Basit bileşen yerleşimi ile duruş süresi ve servis maliyetinde azalma']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda'],
                ['icon' => 'pills', 'text' => 'İlaç'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'building', 'text' => 'İnşaat malzemeleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim (FMCG)'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal dağıtım merkezleri'],
                ['icon' => 'award', 'text' => 'Etkinlik ve fuar lojistiği'],
                ['icon' => 'star', 'text' => 'Beyaz eşya dağıtımı'],
                ['icon' => 'bolt', 'text' => 'Enerji ekipmanı depoları'],
                ['icon' => 'cog', 'text' => 'Makine yedek parça'],
                ['icon' => 'box-open', 'text' => 'Mobilya lojistiği'],
                ['icon' => 'warehouse', 'text' => 'Araç yedek parça hubları'],
                ['icon' => 'store', 'text' => 'DIY/Yapı market dağıtımı'],
                ['icon' => 'industry', 'text' => 'Metal işleme ve dökümhaneler'],
                ['icon' => 'flask', 'text' => 'Boya ve kimyasal ardiye'],
                ['icon' => 'microchip', 'text' => 'Telekom ve ağ donanımı']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay garanti kapsamındadır. Li‑Ion batarya modülleri ise 24 ay üretim hatalarına karşı garanti altındadır. Garanti, normal kullanım şartlarında geçerlidir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 80V Şarj Ünitesi', 'description' => 'Standart 80V/35A entegre şarj cihazı ile pratik fırsat şarjı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Hızlı Şarj Ünitesi', 'description' => 'Daha yoğun vardiyalar için yüksek akımlı harici şarj çözümü.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Geniş Kapasiteli Batarya', 'description' => 'Uzun süreli operasyonlar için artırılmış nominal kapasite seçeneği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Dış Mekân Paketleri', 'description' => 'Zorlu şartlara karşı ek sızdırmazlık ve koruma setleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EFL201 modelinde standart kaldırma yüksekliği ve direk ölçüleri nedir?', 'answer' => 'Standart kaldırma 3000 mm, direk kapalı 2020 mm ve açık 4028 mm’dir. 100 mm serbest kaldırma ile çoğu palet operasyonu kapsanır.'],
                ['question' => '80V sistemin 48V sistemlere göre performans farkı ne sağlar?', 'answer' => '80V mimari daha yüksek güç verimi ve daha düşük akım ile ısınmayı azaltır; çekiş ve operasyon sürekliliğinde hissedilir iyileşme sunar.'],
                ['question' => 'Yerden açıklık ve lastik tipi dış mekânda nasıl avantaj sağlar?', 'answer' => '120 mm yer açıklığı ve katı lastikler bozuk zeminlerde temas ve sızdırmazlık sağlar; yağmur ve ıslak koşullarda güvenli ilerleme desteklenir.'],
                ['question' => 'Operatör ergonomisinde öne çıkan güncellemeler nelerdir?', 'answer' => 'İleri alınmış gaz pedalı, ayarlanabilir direksiyon ve konforlu koltuk uzun vardiyalarda yorgunluğu azaltır ve manevra kontrolünü artırır.'],
                ['question' => 'Bakım erişimi ve component yerleşimi bakım sürelerini nasıl etkiler?', 'answer' => 'Basitleştirilmiş düzen ana bileşenlere hızlı erişim sağlar, planlı bakım ve arıza müdahalesi sürelerini kısaltır, duruş maliyetini düşürür.'],
                ['question' => 'Maksimum sürüş ve kaldırma hızları hangi aralıklardadır?', 'answer' => 'Sürüşte 11/14 km/s (yüklü/boş) ve kaldırmada 0.25/0.30 m/s değerleri sunulur; indirmede 0.43/0.45 m/s hız sağlanır.'],
                ['question' => 'Tırmanma kabiliyeti ve çekiş kuvveti hangi uygulamalar için yeterlidir?', 'answer' => '12/15% tırmanma kabiliyeti ve 10000 N çekiş kuvveti rampa yaklaşımı, yükleme istasyonları ve açık alan geçişleri için uygundur.'],
                ['question' => 'Direk görünürlüğündeki iyileştirme operasyon güvenliğini nasıl etkiler?', 'answer' => 'Hidrolik hatların görüş hattından uzaklaştırılması uç ve çevre görünürlüğünü artırır; dar alan manevralarında hız ve güvenlik sağlar.'],
                ['question' => 'Sürüş gürültü seviyesi operatör konforuna etkisi nedir?', 'answer' => '70 dB(A) seviyesinde ölçülen kabin içi gürültü, uzun vardiyalarda konforu artırır ve operatör yorgunluğunu azaltır.'],
                ['question' => 'Standart çatal ölçüleri ve kanca sınıfı hangi tip paletleri kapsar?', 'answer' => '40×122×1070 mm çatal ve 2A sınıfı kanca ile yaygın EUR ve ISO palet ölçüleri güvenle ele alınır.'],
                ['question' => 'Enerji tüketimi ve vardiya planlaması için fırsat şarjı nasıl kurgulanır?', 'answer' => 'Kısa molalarda entegre 80V/35A şarj ile SOC üstte tutulur; vardiya sonu derin şarj ihtiyacı minimize edilir, akü ömrü korunur.'],
                ['question' => 'Garanti kapsamı ve servis desteği nasıl ilerler?', 'answer' => 'Makine 12 ay, akü 24 ay kapsamındadır. İXTİF satış ve servis ağı için 0216 755 3 555 ile iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EFL201');
    }
}
