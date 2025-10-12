<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL252_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL252')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL252'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF EFL252: 2.5 Ton Sınıfında Li-Ion Gücü ve Kompakt Çeviklik</h2>
  <p>İXTİF EFL252, geleneksel dizel forkliftlerin alışıldık gücünü modern LFP Li-ion teknolojisinin verimliliğiyle birleştirir. 2.5 ton nominal kapasite, 500 mm yük merkezi ve yalnızca 3573 mm toplam uzunluk ile dar koridorlarda güven ve çeviklik sunar. Fırsat şarjına uygun 80V 205Ah Li-ion batarya gün içinde kısa molalarda doldurularak vardiya planlarını aksatmadan üretkenliği yüksek tutar. IPX4 su koruması dış mekânda yağmur altında bile sürekliliği destekler; 74 dB(A) düşük gürültü seviyesi operatör konforuna katkı sağlar.</p>
</section>
<section>
  <h3>Teknik Güç ve Operasyonel Verim</h3>
  <p>Şasi ve direk mimarisi yüksek dayanımlı bileşenlere dayanır. 10 kW S2 (60 dk) sürüş motoru ve 16 kW S3 (15%) kaldırma motoru, 11/12 km/s sürüş hızı ve 0.28/0.37 m/s kaldırma hızlarını sağlar. 2290 mm dönüş yarıçapı ve 1154 mm toplam genişlik, 3985 mm (1000×1200 çapraz) koridor gereksinimiyle birlikte dar alanlarda dahi akıcı manevra sunar. 6°/10° mast eğimi, 3000 mm standart kaldırma ve 4050 mm açık mast yüksekliği ile yaygın kapılardan geçiş kolaydır. 40×122×1070 mm çatal, 2A sınıfı karet ve 1090 mm karet genişliği palet aralığına uyumludur. Katı (solid) lastikler dış kullanımda darbe dayanımı sağlar; 970/975 mm iz genişlikleri stabiliteyi artırır. AC sürüş kontrolü pürüzsüz hızlanma ve geri kazanımla enerji verimliliğini destekler; hidrolik servis freni ve mekanik park freni güvenli duruş sağlar.</p>
  <p>Batarya LFP kimyasıyla kendi kendine tutuşmayı önlemeye odaklanır ve hızlı ara şarjlarla arıza süresini azaltır. IPX4 su koruması elektronik ve tahrik aksamını farklı açılardan sıçrayan suya karşı korur; bu sayede yağmurlu havalarda rampa besleme, açık saha yükleme ve konteyner içi operasyonlar kesintisiz devam eder. Telematics paketi; araç konum izleme, kullanım ve teşhis raporları, Li-ion batarya koşul analitiği ve kart erişim güncellemeleriyle filo yönetiminin verisini görünür kılar.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>İXTİF EFL252; düşük toplam sahip olma maliyeti, fırsat şarjı ile vardiya esnekliği ve kompakt gövdesiyle depo içi ve dış ortam işlerinde güçlü bir standart kurar. Sahada test edilerek kanıtlanan operasyonel süreklilik, bakım ihtiyacını azaltan tasarımla birleşir. Detaylar ve keşif için bizi arayın: 0216 755 3 555.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2500 kg @ 500 mm yük merkezi'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V 205Ah (LFP) – fırsat şarjı'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '11 / 12 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '2290 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'LFP Li-Ion Güvenliği', 'description' => 'Kendi kendine tutuşmayı önleyen kimya ve hızlı ara şarj uyumu.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Şasi', 'description' => '3573 mm toplam uzunlukla dar alanlarda manevra.'],
                ['icon' => 'shield-alt', 'title' => 'IPX4 Su Koruması', 'description' => 'Yağmurda dış mekânda güvenli operasyon.'],
                ['icon' => 'microchip', 'title' => 'Telematics', 'description' => 'Konum, kullanım raporu, teşhis ve erişim yönetimi.'],
                ['icon' => 'bolt', 'title' => 'Düşük TCO', 'description' => 'Filtre ve yağ yok; bakım maliyeti azalır.'],
                ['icon' => 'star', 'title' => 'Konforlu Çalışma', 'description' => '74 dB(A) düşük gürültü ile ergonomi.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarda yükleme-boşaltma ve hat besleme operasyonları'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment merkezlerinde palet giriş-çıkış akışı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde cross-dock ve sevkiyat hazırlığı'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında ağır kutu palet taşıma'],
                ['icon' => 'snowflake', 'text' => 'Açık saha rampa operasyonlarında yağmurlu koşullarda çalışma'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP ve yarı mamul transferi'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolamada güvenli ve kontrollü taşıma'],
                ['icon' => 'microchip', 'text' => 'Elektronik ürün depolarında hassas yüklerin düzenli akışı']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'battery-full', 'text' => 'Fırsat şarjı ile vardiya planlarını esnekleştirir, şarj sürelerini gündüz molalarına taşır.'],
                ['icon' => 'bolt', 'text' => 'Dizel muadillere göre %30–%50 daha düşük enerji maliyeti ve bakım kalemi azalması.'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt gövde ve 2290 mm dönüş yarıçapı ile dar koridor çevikliği.'],
                ['icon' => 'shield-alt', 'text' => 'IPX4 koruma ile dış mekân yağmur koşullarında süreklilik.'],
                ['icon' => 'microchip', 'text' => 'Telematics ile filo görünürlüğü: konum, kullanım ve teşhis raporları.']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Ağır Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Lojistik'],
                ['icon' => 'warehouse', 'text' => 'Depolama ve Antrepo'],
                ['icon' => 'box-open', 'text' => 'Kargo & Kurye Hub'],
                ['icon' => 'industry', 'text' => 'Metal İşleme'],
                ['icon' => 'flask', 'text' => 'Boya & Kaplama'],
                ['icon' => 'car', 'text' => 'Lastik & Jant Deposu'],
                ['icon' => 'store', 'text' => 'DIY & Yapı Market'],
                ['icon' => 'building', 'text' => 'Mobilya Lojistiği'],
                ['icon' => 'cart-shopping', 'text' => 'İçecek Dağıtımı']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '80V 35A Harici Şarj Cihazı', 'description' => 'Standart şarj cihazı ile güvenilir ve sürdürülebilir şarj döngüsü.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Süspansiyonlu Operatör Koltuğu', 'description' => 'Daha az titreşim, daha çok konfor için ayarlanabilir süspansiyon sistemi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Dahili Yan Kaydırıcı', 'description' => 'Yük hizalamayı hızlandıran entegre sideshifter ataşmanı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => '80V 280Ah Li-Ion Paket', 'description' => 'Yoğun vardiyalar için artırılmış nominal kapasite seçeneği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EFL252 hangi enerji teknolojisini kullanır ve neden güvenlidir?', 'answer' => 'LFP kimyaya sahip 80V Li-ion batarya kullanır. Termal stabilitesi yüksektir, kendi kendine tutuşmayı önlemeye odaklanır ve fırsat şarjını destekler.'],
                ['question' => 'Gün içinde fırsat şarjı operasyonu nasıl etkiler?', 'answer' => 'Kısa molalarda şarj edilebildiği için vardiya akışı bölünmez. Batarya yönetimi süreklidir ve toplam verimlilik artar.'],
                ['question' => 'Kompakt gövdenin saha avantajı nedir?', 'answer' => '3573 mm uzunluk ve 2290 mm dönüş yarıçapı dar alanlarda manevrayı kolaylaştırır, raf arası ve kapı geçişlerinde hız kazandırır.'],
                ['question' => 'Dış mekân kullanımında koruma seviyesi nedir?', 'answer' => 'IPX4 su koruması, farklı açılardan sıçrayan suya karşı bileşenleri korur. Yağmur altında rampa ve saha operasyonları güvenle sürer.'],
                ['question' => 'Telematics neleri raporlar ve nasıl fayda sağlar?', 'answer' => 'Gerçek zamanlı konum, kullanım ve teşhis raporları ile kart erişim güncellemeleri sunar; filo görünürlüğünü ve güvenliği artırır.'],
                ['question' => 'Standart mast ve serbest kaldırmalı mast seçenekleri var mı?', 'answer' => '3000–6000 mm aralığında farklı mast tipleri ve serbest kaldırma seçenekleri mevcuttur; kapı yüksekliği ve raf senaryonuza göre seçilir.'],
                ['question' => 'Yan kaydırıcı ataşmanı kapasiteyi etkiler mi?', 'answer' => 'Evet. Entegre veya harici yan kaydırıcı kullanıldığında nominal kapasiteden yaklaşık 150 kg düşüş dikkate alınmalıdır.'],
                ['question' => 'Bakım kalemlerinde ne tür azalmalar sağlanır?', 'answer' => 'Dizeldeki hava/yağ filtresi, motor yağı ve marş aküsü gibi sarf kalemleri yoktur. Planlı duruş ve maliyetler azalır.'],
                ['question' => 'Sürüş ve kaldırma motor güçleri nedir?', 'answer' => 'Sürüşte 10 kW (S2 60 dk), kaldırmada 16 kW (S3 15%) motorlar bulunur; pürüzsüz hızlanma ve verimlilik sunar.'],
                ['question' => 'Gürültü seviyesi ve operatör konforu nasıldır?', 'answer' => 'Sürücü kulak seviyesinde 74 dB(A) ölçülür. Daha sessiz çalışma yorgunluğu azaltır ve iç-dış ortam uyumu sağlar.'],
                ['question' => 'Enerji maliyeti avantajı ne düzeydedir?', 'answer' => 'Dizel forkliftlere kıyasla genel enerji maliyetinde yaklaşık %30–%50 tasarruf sağlanabilir; kullanım senaryosuna bağlıdır.'],
                ['question' => 'Garanti kapsamı ve iletişim numarası nedir?', 'answer' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti altındadır. Satış ve servis için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EFL252');
    }
}
