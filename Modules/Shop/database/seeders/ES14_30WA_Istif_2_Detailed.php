<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES14_30WA_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ES14-30WA')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (ES14-30WA)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section>
  <h2>Hassas İstifin Güvenilir Standardı: ES14-30WA</h2>
  <p>Depoda hız, güven ve esneklik tek bir makinede buluştuğunda, operatörün tüm odağı akışa kayar. İXTİF ES14-30WA bu akış için üretildi: 1400 kg taşıma kapasitesi, 600 mm yük merkezi ve ayarlanabilir geniş bacak (straddle) mimarisi ile farklı palet ölçülerine uyum sağlar. 1545 mm dönüş yarıçapı, kaplumbağa hız moduyla birleştiğinde, kalabalık alanlarda bile hatasız manevra yapılır. Standart oransal kaldırma sistemi, raf seviyesinde milimetrik hassasiyet sunarken elektromanyetik fren, eğimli rampalarda bile güven hissini korur. 24V/210Ah akü sistemi gün boyunca istikrar sağlarken Li-ion alternatif, hızlı şarj ve bakım kolaylığıyla toplam sahip olma maliyetini aşağı çeker. Günün sonunda ES14-30WA, istif operasyonlarının en çok ihtiyaç duyduğu şeyi verir: öngörülebilir, tekrar edilebilir performans.</p>
</section>
<section>
  <h3>Teknik Güç ve Operasyonel Uyum</h3>
  <p>Makine omurgası, dikey AC tahrik motoru (1.1 kW) ve 3.0 kW kaldırma motoru üzerine kuruludur. Yüklü/boş 5.5/6.0 km/s seyir hızları ve 0.127/0.23 m/s kaldırma hızlarıyla vardiya içi çevrim zamanlarını kısaltır. 0.26/0.20 m/s indirme hızları kontrollü bırakma yaparak ürün hasarını önlemeye yardım eder. 2118 mm kapalı mast ve 4115 mm açık mast yüksekliği; 3140 mm efektif kaldırma (h3) ile 3200 mm nominal direk yüksekliği kombinasyonu, standart depo raf yüksekliklerinin tamamını kapsar. 1270/1370/1470 mm şasi genişliği seçenekleri, farklı yük genişliklerinde bacak açıklığının ayarlanmasına imkân verir; b5 çatallar arası 200–760 mm aralığı, ürün çeşitliliğine yanıt verir. 1987 mm toplam uzunluk ve 917 mm yüze kadar uzunluk, yükün içine rahat giriş sağlar; PU (poliüretan) tekerlek seti (230×75 tahrik, 102×73 yük, 85×48 denge) sessiz çalışma ve zemin dostu sürüş sunar. Ast 2460 mm koridor değerleri ve 50 mm dingil merkezi yerden yükseklik ile dar alanlarda raf içi dönüş güvenle tamamlanır. 24V/210Ah akü (190 kg) standart olup, 205Ah Li-ion opsiyonu hızlı fırsat şarjlarıyla kesintisiz vardiya kurgularına uygundur. Mekanik direksiyon, ergonomik timon kolu ve kaplumbağa modu, yeni başlayan operatörlerde dahi güvenli kullanım sağlar.</p>
</section>
<section>
  <h3>Sonuç: Dayanıklı Platform, Düşük TCO</h3>
  <p>ES14-30WA; ayarlanabilir straddle yapısı, oransal kaldırma ve güçlü AC güç aktarımıyla karma depo senaryolarında evrensel bir çözüm sunar. Bakım kolaylığı, sessiz sürüş ve standardize edilmiş bileşenler, stok maliyetlerini ve plan dışı duruşları düşürür. Raf düzeniniz değişse bile, ayarlanabilir bacak açıklığı sayesinde aynı makineyle çalışmaya devam edersiniz. Doğru mast seçimi ve çatal uzunluğu varyantları ile ürün, süreçlerinize tam entegre olur. Teknik detay, fiyat ve en uygun konfigürasyon için 0216 755 3 555 numaralı hattan bize ulaşın.</p>
</section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1400 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 210Ah (Li-ion opsiyon 205Ah)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '5.5/6.0 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1545 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'Ayarlanabilir geniş bacak', 'description' => 'Farklı palet ölçülerine ve yük genişliklerine hızlı uyum sağlar'],
                ['icon' => 'battery-full', 'title' => '24V enerji mimarisi', 'description' => '210Ah kurşun-asit veya 205Ah Li-ion ile esnek enerji yönetimi'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik fren', 'description' => 'Eğimli rampalarda dahi güvenli duruş ve park kilidi'],
                ['icon' => 'warehouse', 'title' => 'Dar koridor çevikliği', 'description' => '1545 mm dönüş yarıçapı ve kaplumbağa hız ile güvenli manevra'],
                ['icon' => 'star', 'title' => 'Oransal kaldırma', 'description' => 'Hassas raf yerleştirme için milimetrik kontrol ve yumuşak bırakma'],
                ['icon' => 'bolt', 'title' => '3.0 kW kaldırma gücü', 'description' => 'Ağır paletlerde bile stabil ve seri kaldırma performansı']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Karma palet yapısına sahip depolarda bacak açıklığıyla uyumlu istifleme'],
                ['icon' => 'box-open', 'text' => 'Fulfillment alanlarında sık döngülü toplama/istif akışı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde dar koridor raf içi hareket'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkışlarında sessiz ve kontrollü operasyonlar'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında hassas ürünler için yumuşak kaldırma-indirme'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında hızlı replenishment'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve hat besleme'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolamada kapalı alanlarda düşük titreşimli sürüş']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC sürüş + 3.0 kW kaldırma ile çevrim süresini kısaltan güçlü aktarım'],
                ['icon' => 'battery-full', 'text' => 'Li-ion opsiyon ile hızlı fırsat şarjı ve düşük bakım ihtiyacı'],
                ['icon' => 'arrows-alt', 'text' => 'Ayarlanabilir straddle tasarım ile farklı palet ve yük tiplerine tek çözüm'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve oransal kontrol ile hasar ve risk azaltımı'],
                ['icon' => 'warehouse', 'text' => 'Dar koridorlarda 1545 mm dönüş ve 1987 mm toplam uzunluk ile manevra üstünlüğü']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfilment'],
                ['icon' => 'warehouse', 'text' => '3PL ve lojistik hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim ürünleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve soğuk zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve medikal'],
                ['icon' => 'flask', 'text' => 'Kimya ve boya'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve komponent'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça'],
                ['icon' => 'industry', 'text' => 'Genel imalat'],
                ['icon' => 'building', 'text' => 'Depolama ve şehir içi depolar'],
                ['icon' => 'briefcase', 'text' => 'B2B toptan ticaret'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtım merkezleri'],
                ['icon' => 'warehouse', 'text' => 'Bölgesel konsolidasyon hub’ları'],
                ['icon' => 'box-open', 'text' => 'Kargo ve paket ayrıştırma'],
                ['icon' => 'industry', 'text' => 'Montaj hatları ve WIP alanları'],
                ['icon' => 'flask', 'text' => 'Laboratuvar malzemeleri depoları'],
                ['icon' => 'pills', 'text' => 'Kozmetik ve kişisel bakım'],
                ['icon' => 'building', 'text' => 'Belediye depo ve bakım atölyeleri'],
                ['icon' => 'briefcase', 'text' => 'Sözleşmeli depo işletmeleri'],
                ['icon' => 'car', 'text' => 'Lastik ve jant lojistiği'],
                ['icon' => 'box-open', 'text' => 'Geri dönüşüm ve ambalaj depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Harici Şarj Cihazı (30A)', 'description' => 'Standart kullanım için optimize edilmiş şarj çözümü; güvenli ve stabil şarj eğrisi.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Poliüretan Yük Tekerleri (Çift)', 'description' => 'Düşük gürültü ve zemin dostu yapı; ağır hizmette uzun ömür.', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => '205Ah Li-ion Akü Kiti', 'description' => 'Hızlı fırsat şarjı ve bakım gerektirmeyen kullanım için Li-ion modül.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Ayarlanabilir Sırt Dayama', 'description' => 'Farklı koliler ve istif yükseklikleri için optimize edilmiş yük dayama aparatı.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Kaplumbağa hız modu hangi durumlarda öne çıkar ve nasıl açılır?', 'answer' => 'Yoğun yaya trafiği ve dar koridorlarda hassas manevra için idealdir. Timon kolu üzerindeki hız sınırlama düğmesiyle devreye alınır ve kalkış torkunu yumuşatır.'],
                ['question' => 'Ayarlanabilir bacak açıklığı hangi palet tipleriyle uyumlu çalışır?', 'answer' => 'EUR, CHEP ve endüstriyel paletlerde b4 1070/1170/1270 mm ve b5 200–760 mm aralığı sayesinde farklı taban genişlikleri güvenle kavranır.'],
                ['question' => 'Maksimum direk yüksekliği ve etkili kaldırma değerleri nelerdir?', 'answer' => 'Standart konfigürasyonda H 3200 mm, h3 3140 mm ve h4 4115 mm değerleri sağlanır. Raf yüksekliği planlamasında bu değerler referans alınmalıdır.'],
                ['question' => 'Yüklü ve yüksüz seyir hızları operasyonu nasıl etkiler?', 'answer' => '5.5/6.0 km/s hızlar çevrim zamanlarını kısaltır, operatör güvenliğini gözetir. Zemin ve yoğunluğa göre kaplumbağa modu tercih edilmelidir.'],
                ['question' => 'Li-ion akü tercih edilirse bakım ve şarj süreçleri değişir mi?', 'answer' => 'Li-ion modül bakım gerektirmez, fırsat şarjını destekler. Isı yönetimi iyidir; vardiya arasında kısa şarjlarda performans düşüşü yaşanmaz.'],
                ['question' => 'Manevra alanı dar depolar için dönüş yarıçapı yeterli mi?', 'answer' => '1545 mm dönüş yarıçapı ve 1987 mm toplam uzunluk ile 2460 mm koridorlarda güvenli dönüş mümkündür. Raf yerleşimine göre test sürüşü önerilir.'],
                ['question' => 'Fren sistemi eğimli rampalarda geri kaymayı engeller mi?', 'answer' => 'Elektromanyetik servis freni park konumunda tekerlekleri kilitler, eğimde geri kayma riskini azaltır. Operatör eğitimine dikkat edilmelidir.'],
                ['question' => 'Yük altındaki kaldırma hızı ne kadar ve hasarı nasıl önler?', 'answer' => '0.127 m/s kaldırma hızı ve oransal kontrol, hassas bırakma ile ürün köşe ezilmelerini azaltır; kırılgan yüklerde avantaj sağlar.'],
                ['question' => 'Tekerlek malzemesi zemin aşınmasını ve gürültüyü nasıl etkiler?', 'answer' => 'Poliüretan bileşim düşük gürültü ve zemin dostu sürtünme sunar. Pürüzlü zeminlerde düzenli kontrol ömrü uzatır.'],
                ['question' => 'Bakım erişimi ve arıza tespitinde hangi kolaylıklar var?', 'answer' => 'Dikey motor yerleşimi ve erişilebilir servis kapakları rutin kontrolleri hızlandırır. Standart bileşenler yedek parça bulunabilirliğini artırır.'],
                ['question' => 'Hangi mast seçenekleriyle farklı raf yüksekliklerine ulaşılabilir?', 'answer' => '3000, 3200, 3590, 4000, 4500, 4800 ve 5000 mm h3 seçenekleri mevcuttur. Kapalı/açık yükseklikler projeye göre seçilir.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek nasıl ilerler?', 'answer' => 'Makine için 12 ay, Li-Ion akü modülleri için 24 ay garanti sağlanır. Tüm satış, servis ve yedek parça talepleriniz için İXTİF 0216 755 3 555 hattından ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: ES14-30WA');
    }
}
