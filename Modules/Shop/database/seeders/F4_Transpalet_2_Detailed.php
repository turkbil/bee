<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F4_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'F4-1500')->first();
        if (!$p) {
            echo "❌ Master bulunamadı\n";
            return;
        }
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF F4: Kompakt gövdede esneklik ve verim</h2><p>İXTİF F4, modern depo ve dağıtım operasyonlarının ihtiyaç duyduğu çevikliği, düşük ağırlık ve yüksek dayanıklılıkla birleştirir. 1.5 ton kaldırma kapasitesi, l2=400 mm kompakt şasi ve yalnızca 120 kg servis ağırlığıyla dar koridorlardan rampa yaklaşımlarına kadar pek çok senaryoda hızlı, güvenli ve ekonomik taşıma sağlar. Platform tabanlı tasarım; ara sıra kullanım ile yoğun vardiya arasında ihtiyaca göre yapılandırma olanağı tanırken, iki güç yuvalı mimari tek veya çift 24V 20Ah Li‑Ion akü ile kesintisiz çalışma sunar. Depo içi küçük ekipmanlar için sökülebilir saklama gözü, operatörlerin günlük akışını hızlandırır; stabilite tekeri opsiyonu ise bozuk zeminlerde dengeyi artırarak hasar ve düşme riskini azaltır.</p></section><section><h3>Teknik güç ve ölçüler</h3><p>F4, DC sürüş kontrolü ve elektromanyetik servis freni ile güvenli duruş ve yumuşak kalkış sağlar. 0.75 kW sürüş motoru ve 0.5 kW kaldırma motoru, 4.0/4.5 km/s (yüklü/yüksüz) yürüyüş hızlarına ulaşırken 0.017/0.020 m/s kaldırma ve 0.058/0.046 m/s indirme hızlarıyla kontrollü malzeme akışı sunar. 6%/16% eğim performansı rampalarda yeterli çekiş sağlar. 210×70 mm tahrik, 80×60 mm yük tekerleri ve 74×30 mm destek tekerleri poliüretan kaplamasıyla sessiz ve zemin dostudur. Toplam genişlik 590 veya 695 mm, standart çatal ölçüsü 55×150×1150 mm’dir; çatal açıklığı 560/685 mm seçenekleri ile farklı palet tiplerini destekler. Tiller yüksekliği 750–1190 mm aralığındadır. 1360 mm dönüş yarıçapı ve 2025–2160 mm koridor genişliği değerleri dar alanlarda yön değiştirmeyi kolaylaştırır. 24V/20Ah Li‑Ion modül (opsiyonel ikinci modülle 2×24V/20Ah) hızlı değişim ve fırsat şarjı ile vardiya devamlılığı sağlar; modül başına yaklaşık 5 kg ağırlık ergonomiyi korur. Enerji tüketimi 0.18 kWh/h seviyesinde ölçülmüştür ve VDI 2198’e göre 60 t/h iş çevrimi ile 333.33 t/kWh verimlilik sunar.</p></section><section><h3>Sonuç</h3><p>F4; esnek konfigürasyonları, kompakt ölçüleri ve maliyet düşüren toptan paketleme avantajlarıyla e-ticaret, 3PL ve perakende dağıtım merkezleri için ideal bir çözüm sunar. Teknik bilgi ve teklif için 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg (Q)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V 20Ah Li‑Ion (2×20Ah opsiyon)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1360 mm (Wa)']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Çift güç yuvası', 'description' => '2×24V 20Ah ile vardiya boyunca yüksek kullanım'],
                ['icon' => 'compress', 'title' => 'Kompakt şasi', 'description' => 'l2=400 mm ile dar koridor çevikliği'],
                ['icon' => 'layer-group', 'title' => 'Platform mimarisi', 'description' => 'Dört şasi seçeneğiyle pazara hızlı uyum'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli fren', 'description' => 'Elektromanyetik frenle kontrollü duruş'],
                ['icon' => 'cog', 'title' => 'Bakım kolaylığı', 'description' => 'Modüler yapı ve hızlı akü değişimi'],
                ['icon' => 'star', 'title' => 'Toptan avantaj', 'description' => 'Kutu başına 4 ünite ile lojistik tasarruf']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret depolarında çapraz sevkiyat ve sipariş konsolidasyonu'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde vardiya içi besleme ve palet transferi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtımda raf arası ürün akışı ve mağaza hazırlığı'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ürün hareketi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa yaklaşımı'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve hat besleme'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyimde koli/paket taşıma']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '0.75 kW sürüş ve 0.5 kW kaldırma ile dengeli performans'],
                ['icon' => 'battery-full', 'text' => 'Li‑Ion modüllerle hızlı şarj ve yüksek kullanılabilirlik'],
                ['icon' => 'arrows-alt', 'text' => '590/695 mm genişlik ve farklı çatal ölçüleriyle esneklik'],
                ['icon' => 'layer-group', 'text' => 'Platform F şasisiyle hızlı konfigürasyon ve ölçeklenebilirlik'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve stabilite tekeri opsiyonu ile güvenlik']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolar'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market / DIY'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'building', 'text' => 'Tesis Yönetimi ve Facility']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay süreyle üretim hatalarına karşı garanti kapsamındadır. Li‑Ion batarya modülleri satın alım tarihinden itibaren 24 ay ayrı garanti altındadır. Garanti, normal kullanım koşullarında geçerlidir.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Harici Akü Şarj Cihazı', 'description' => '24V Li‑Ion modüller için hızlı ve güvenli şarj çözümü.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Stabilite Teker Seti', 'description' => 'Dengesiz zeminlerde yük stabilitesi ve güvenlik sağlar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'İkinci Akü Modülü', 'description' => '2×24V 20Ah yapılandırma ile kesintisiz vardiya.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Tekerlek Opsiyonları', 'description' => 'Farklı zeminler için poliüretan/tandem teker alternatifleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Tek akü ile tipik çalışma süresi ne kadar sağlanır?', 'answer' => '24V 20Ah Li‑Ion modül fırsat şarjı ile vardiya içi kullanım sunar; yoğunluk ve koridor mesafesine bağlı süre değişir.'],
                ['question' => 'Çift akü seçeneği nasıl takılır ve değişim süresi nedir?', 'answer' => 'İkinci akü yuvası hazırdır; modüler yapı sayesinde akü değişimi aletsiz, dakikalar içinde tamamlanır.'],
                ['question' => 'Dar koridorlarda minimum dönüş alanı nedir?', 'answer' => '1360 mm dönüş yarıçapı ve 2025–2160 mm koridor gereksinimi ile yön değiştirme kolaydır.'],
                ['question' => 'Eğim performansı ve rampa kullanımı nasıldır?', 'answer' => 'Maksimum eğim yeteneği yüklü %6, yüksüz %16’dır; rampa yaklaşımlarında güvenli çekiş sağlar.'],
                ['question' => 'Hangi palet ölçüleri ile uyumludur?', 'answer' => 'Standart 1150×560 mm çatal ile EUR palet uyumludur; 685 mm genişlik ve farklı uzunluk opsiyonları mevcuttur.'],
                ['question' => 'Bakım aralıkları ve tipik sarf malzemeleri nelerdir?', 'answer' => 'Li‑Ion teknoloji bakım ihtiyacını azaltır; periyodik kontroller, fren ve tekerlek aşınmaları takip edilmelidir.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler?', 'answer' => '74 dB(A) seviyesinde ölçülen gürültü, kapalı alan operasyonlarında konforlu çalışma sağlar.'],
                ['question' => 'F4\'ün ağırlığı ve taşıma kolaylığı avantaj sağlar mı?', 'answer' => '120 kg servis ağırlığı, araç içi transfer ve katlar arası taşıma senaryolarında avantaj sunar.'],
                ['question' => 'Fren sistemi hangi durumlarda devreye girer?', 'answer' => 'Elektromanyetik fren eğimde ve acil durumda güvenli duruş sağlar; kumanda kolu konumu ile entegredir.'],
                ['question' => 'Enerji verimliliği ile ilgili ölçüm değerleri nedir?', 'answer' => 'DIN EN 16796’ya göre 0.18 kWh/saat tüketim, VDI 2198’e göre 60 t/sa iş çevrimi ve 333.33 t/kWh verimlilik elde edilmiştir.'],
                ['question' => 'Opsiyonel stabilite tekeri hangi zeminlerde önerilir?', 'answer' => 'Bozuk, pürüzlü veya eğimli zeminlerde yük salınımını azaltmak ve dengeyi artırmak için önerilir.'],
                ['question' => 'Garanti koşulları ve servis desteği nasıl sağlanır?', 'answer' => 'Makine 12 ay, akü 24 ay garanti kapsamındadır. İXTİF satış ve servis desteği için 0216 755 3 555 numarasından ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        echo "✅ Detailed: F4-1500\n";
    }
}
