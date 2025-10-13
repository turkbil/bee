<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL302_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFL302')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFL302');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section>
    <h2>İXTİF EFL302: Dizel Alışkanlığını Li-Ion Gücüyle Değiştirin</h2>
    <p>Depo kapıları sabah açıldığında, hız ve dayanıklılık kadar süreklilik de kritik hale gelir. İXTİF EFL302, içten yanmalı forkliftlerden alışık olduğunuz sağlam sürüş hissini, LFP Li-Ion batarya mimarisi ve IPX4 su korumalı gövde ile birleştirir. Büyük lastikler ve yüksek yerden yükseklik, açık sahada çukurları tolere ederken, dar alanlarda 2500 mm dönüş yarıçapı ile atik manevralar sağlar. Fırsat şarj kabiliyeti sayesinde operasyon aralarında dakikalarla ölçülen kısa şarjlar tüm vardiyanızı besler; böylece plan dışı duruşlar tarihe karışır.</p>
</section>
<section>
    <h3>Teknik Güç ve Verimlilik</h3>
    <p>3000 kg nominal kapasite ve AC sürüş kontrolü ile operatör, yük altında bile akıcı hızlanma ve kontrollü yavaşlama elde eder. 11/12 km/sa seyir hızı günlük taşıma akışlarını hızlandırırken, 0.29/0.36 m/s (yük/yüksüz) kaldırma değerleri istif döngüsünü kısaltır. LFP kimya yapısı, termal kaçak riskini düşürürken ömür boyu bakım gerektiren filtre ve yağ değişimlerini ortadan kaldırır. IPX4 koruma sınıfı, yağmur altında dahi güven veren bir yapı sunar; mekanik park freni ve hidrolik servis freni ise operatöre güvenli, öngörülebilir duruş mesafeleri sağlar. Basit komponent mimarisi ve iyi erişilebilen servis noktaları bakım süresini kısaltır, toplam sahip olma maliyetini düşürür.</p>
</section>
<section>
    <h3>Sonuç: Bugün Planlayın, Yarın Kazanın</h3>
    <p>Telematik paketi, araç konumu, kullanım ve akü sağlığına ilişkin verileri görünür kılarak filonuzu ölçülebilir kılar. Böylece bakım randevularını önceden planlar, kullanım dışı süreleri minimumda tutarsınız. Projelerinize uygun direk ve çatal alternatifleriyle EFL302, saha gerçeklerine uyum sağlayan modüler bir çözümdür. Bilgi ve teklif için 0216 755 3 555.</p>
</section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '3000 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V 205Ah Li-Ion (LFP)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11/12 km/sa (yük/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '2500 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'LFP Li-Ion Güvenliği', 'description' => 'Kendiliğinden tutuşmaya dirençli kimya ve dengeli ısı yönetimi'],
                ['icon' => 'bolt', 'title' => 'Düşük TCO', 'description' => 'Yağ, hava ve yakıt filtresi gibi parçalar olmadan daha az bakım'],
                ['icon' => 'arrows-alt', 'title' => 'Açık Alan Uyumlu', 'description' => 'Büyük lastikler ve yüksek yerden yükseklikle konforlu sürüş'],
                ['icon' => 'shield-alt', 'title' => 'IPX4 Koruma', 'description' => 'Yağmur ve sıçramaya karşı test edilmiş gövde tasarımı'],
                ['icon' => 'briefcase', 'title' => 'Telematik', 'description' => 'Konum, kullanım ve akü analitiği ile görünür filo'],
                ['icon' => 'cog', 'title' => 'Basit Servis', 'description' => 'Kolay erişilir bileşenlerle hızlı bakım çevrimi']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Dış saha yükleme rampalarında yoğun giriş-çıkış akışı'],
                ['icon' => 'warehouse', 'text' => '3PL cross-dock hatlarında hızlı palet transferi'],
                ['icon' => 'store', 'text' => 'Perakende DC merkezlerinde raf besleme ve sevkiyat'],
                ['icon' => 'snowflake', 'text' => 'Açık alan yağış koşullarında IPX4 korumalı operasyon'],
                ['icon' => 'pills', 'text' => 'Medikal ve kozmetikte hassas paletlerin dengeli taşınması'],
                ['icon' => 'car', 'text' => 'Otomotiv komponent lojistiğinde üretim hattı besleme'],
                ['icon' => 'industry', 'text' => 'Ağır sanayi sahalarında dayanıklı taşıma döngüleri'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında kontrollü kaldırma ve istif']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC sürüşle yüksek tork ve akıcı hızlanma'],
                ['icon' => 'battery-full', 'text' => 'LFP Li-Ion ile güvenli ve hızlı fırsat şarjı'],
                ['icon' => 'arrows-alt', 'text' => 'Büyük lastikler sayesinde bozuk zeminde konfor'],
                ['icon' => 'shield-alt', 'text' => 'IPX4 şasi koruması ile yağmurda kesintisiz çalışma'],
                ['icon' => 'star', 'text' => 'IC tasarım DNA’sından gelen sağlam şasi'],
                ['icon' => 'cog', 'text' => 'Basitleştirilmiş mimari ile hızlı bakım ve düşük TCO']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG & Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Soğuk Zincir & Gıda Lojistiği'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik & Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya Dağıtımı'],
                ['icon' => 'car', 'text' => 'Otomotiv Yan Sanayi'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı & Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya & Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market & DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık & Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım & Bahçe'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'cog', 'name' => 'Yan Kaydırma Aparatı', 'description' => 'Operatör konforu ve istif hassasiyeti için hidrolik yanal kaydırma bloğu', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Dahili Şarj Ünitesi', 'description' => 'AC prizde ara şarj için entegre şarj altyapısı', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Ek Kapasite Akü Paketi', 'description' => 'Daha uzun çalışma için yüksek Ah modül konfigürasyonu', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Servis Takım Kiti', 'description' => 'Rutin bakım ve ayarlar için saha kiti', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Hangi batarya kimyası kullanılıyor ve termal güvenliği nasıl sağlanıyor?', 'answer' => 'LFP (LiFePO4) kimyası kullanılır; hücre dengesi ve BMS sayesinde termal kaçak riski azaltılır, uzun döngü ömrü sağlanır.'],
                ['question' => 'Fırsat şarjı operasyonu nasıl etkiler ve batarya ömrüne zararlı mı?', 'answer' => 'Ara molalarda kısa şarj desteklenir; LFP kimyasıyla bellek etkisi yoktur, uygun akımla ömür üzerinde olumsuz etki yaratmaz.'],
                ['question' => 'IPX4 su koruması yağış altında çalışmaya yeterli midir?', 'answer' => 'IPX4 sıçrayan suya karşı korur; yoğun basınçlı suya maruz bırakmamak koşuluyla yağmur altında operasyon yapılabilir.'],
                ['question' => 'Mekanik park freni ve hidrolik servis freninin avantajı nedir?', 'answer' => 'Kısa fren mesafesi ve düşük yorgunluk sağlar; rampalarda güvenli park imkânı verir.'],
                ['question' => 'Telematik hangi verileri raporlar ve nasıl kullanılır?', 'answer' => 'Araç konumu, kullanım saatleri, hata kodları ve akü analitiği dashboard üzerinden görüntülenir ve filo planlamasında kullanılır.'],
                ['question' => 'Dış sahada lastik seçimi ve yerden yükseklik ne sağlar?', 'answer' => 'Büyük katı lastikler bozuk zeminlerde titreşimi azaltır; yüksek yerden yükseklik engel aşma kabiliyeti kazandırır.'],
                ['question' => 'Bakım aralıkları ve sarf malzeme ihtiyacı nedir?', 'answer' => 'Yağ ve filtre olmadığından periyodik bakım sadeleşir; kontroller elektriksel ve mekanik bağlantılara odaklanır.'],
                ['question' => 'Direk ve ataşman seçenekleri kapasiteyi nasıl etkiler?', 'answer' => 'Yan kaydırma gibi ataşmanlar tipik olarak ~200 kg kapasite düşümü yaratır; yük grafiğine göre seçim yapılmalıdır.'],
                ['question' => 'Çalışma ses seviyesi operatör konforunu nasıl etkiler?', 'answer' => 'Sürücü kulağında <74 dB(A) gürültü seviyesiyle vardiya boyunca konforlu bir çalışma ortamı oluşur.'],
                ['question' => 'Eğim performansı ve rampa kullanımı nasıldır?', 'answer' => 'Maksimum eğim kabiliyeti %15’e kadardır; rampa operasyonlarında yük dağılımına dikkat edilmelidir.'],
                ['question' => 'Şarj altyapısı için gereken elektriksel gereksinimler nelerdir?', 'answer' => '80V Li-Ion şarj cihazlarıyla uyumlu çalışır; tesis elektriğine göre uygun güçte şarj ünitesi seçilmelidir.'],
                ['question' => 'Garanti süresi ve kapsamı nedir, servis ve yedek parça desteği nasıl sağlanır?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. Satış, servis ve yedek parça için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: EFL302");
    }
}
