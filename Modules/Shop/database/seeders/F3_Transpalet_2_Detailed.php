<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F3_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'F3')->first();
        if (!$p) {
            echo "❌ Master bulunamadı";
            return;
        }
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section class="hero"><h2>İXTİF F3: Lojistik Merkezlerinde Dayanıklı Verim</h2><p>Depo kapıları açıldığında ilk hareketi başlatan araç genellikle transpalettir. İXTİF F3, 1.5 ton kapasiteli Li‑Ion kalbi ve güçlü şasisiyle sabahın yoğunluğunda ilk paleti kaldırıp günün son sevkiyatına kadar ritmi korur. Yeni ergonomik tiller başı, başparmakla düğme sıkıştırma zorunluluğunu azaltıp avuç içiyle akıcı kontrol sağlar; operatörün ellerindeki gereksiz yükü alır. Platform F mimarisi ise ürünün yapı taşlarını sadeleştirerek aynı gövdede 4 farklı şasi konfigürasyonuna zemin hazırlar; böylece farklı uygulama ihtiyaçlarına tek bir aileden cevap verilir.</p></section><section class="technical"><h3>Teknik Güç ve Boyutlar</h3><p>F3, 24V/20Ah Li‑Ion tak‑çıkar bataryasıyla hızlı değişim ve fırsat buldukça ara şarj imkânı verir. 0.75 kW sürüş ve 0.5 kW kaldırma motoru, 4.0/4.5 km/s seyir hızını ve 0.017/0.020 m/s kaldırma hızını sağlar. Sade ve sağlam çelik yapı 120 kg servis ağırlığına, 1360 mm dönüş yarıçapına ve 25 mm zemin açıklığına rağmen 1.5 ton yükü 600 mm yük merkezinde taşır. 55/150/1150 mm çatal ölçüsü, 685/560 mm çatallar arası seçenekleri, 695/590 mm gövde genişliği ve 1550 mm toplam uzunluk dar koridorlarda pratik manevra sunar. PU tekerlek seti (210×70 ön; Ø80×60 veya Ø74×88 arka) sessiz ve zemine dost sürüş sağlar. Elektromanyetik servis freni güvenli duruş sağlarken, mekanik direksiyon ve DC sürüş kontrolü bakım kolaylığı ve düşük toplam sahip olma maliyeti hedefler. Flip kapaklı batarya yuvası su girişine karşı koruma sağlayarak iş güvenliği standartlarını destekler.</p></section><section class="closing"><h3>Sonuç: Maliyetleri Düşüren Platform</h3><p>F3’ün lojistik operasyonlarında asıl farkı, Platform F ile operasyonel esneklik ve tedarik zincirinde maliyet avantajı sunmasıdır. 4 üniteyi tek kutuda sevk edebilme ve 40’ konteynere 176 ünite sığdırma, navlun ve depolama maliyetlerinde %30–40’a varan tasarruf potansiyeli yaratır. Ergonomi, güvenlik ve kompakt manevra kabiliyeti birleştiğinde, yoğun vardiyalarda bile akıcı bir akış ortaya çıkar. Detaylı bilgi ve teklif için 0216 755 3 555 numaralı hattımızdan ulaşabilirsiniz.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 20Ah Li‑Ion (tak‑çıkar)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yüklü / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1360 mm Wa']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Tak‑Çıkar Li‑Ion Batarya', 'description' => '24V/20Ah modül ile hızlı değişim ve vardiya içi esneklik'],
                ['icon' => 'hand', 'title' => 'Ergonomik Tiller Başı', 'description' => 'Avuç içiyle kolay sürüş; başparmak baskısını azaltır'],
                ['icon' => 'shield-alt', 'title' => 'Flip Kapaklı Koruma', 'description' => 'Bataryayı su girişine karşı koruyan kapak tasarımı'],
                ['icon' => 'layer-group', 'title' => 'Platform F Mimarisi', 'description' => 'Basit konfigürasyon, 4 farklı şasi seçeneği'],
                ['icon' => 'industry', 'title' => 'Güçlü Şasi', 'description' => 'Depo trafiğinde dayanıklı ve kararlı yapı'],
                ['icon' => 'box-open', 'title' => 'Toptan Sevk Avantajı', 'description' => '4 ünite/kutu, 40’ konteynerde 176 ünite kapasite']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Lojistik merkezlerinde inbound–outbound palet akışları'],
                ['icon' => 'cart-shopping', 'text' => 'Cross-dock hatlarında hızlı palet transferi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında raf besleme'],
                ['icon' => 'box-open', 'text' => 'E‑ticaret fulfillment istasyonlarında sipariş konsolidasyonu'],
                ['icon' => 'industry', 'text' => 'Üretim hattı WIP taşıma ve hücre içi lojistik'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında kısa mesafe soğuk oda transferleri'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında rampa yaklaşımı'],
                ['icon' => 'flask', 'text' => 'Kimya/kozmetik depolarında hassas ürün taşıma']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '0.75 kW sürüş ve 0.5 kW kaldırma ile dengeli performans'],
                ['icon' => 'battery-full', 'text' => '24V/20Ah Li‑Ion modül sayesinde düşük bakım ve hızlı şarj'],
                ['icon' => 'arrows-alt', 'text' => '1360 mm dönüş yarıçapı ile dar koridor manevrası'],
                ['icon' => 'layer-group', 'text' => 'Platform F ile 4 şasi seçeneği; geniş uygulama uyumu'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve korumalı batarya yapısı'],
                ['icon' => 'shipping-fast', 'text' => '4 ünite/kutu ve 176 ünite/k40’ ile sevkiyatta tasarruf']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya ve Boya'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri ve Mama']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay fabrika garantisi kapsamındadır. Li‑Ion batarya modülü satın alım tarihinden itibaren 24 ay garanti altındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V–5A Harici Şarj Cihazı', 'description' => 'Standart şarj ünitesi; hızlı ve güvenli bağlantı ile günlük kullanım için uygundur.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => '24V–10A Harici Şarj (Opsiyonel)', 'description' => 'Daha hızlı şarj ihtiyacı olan vardiyalar için yüksek akım seçeneği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'circle-notch', 'name' => 'PU Tandem Yük Tekerleri', 'description' => 'Sessiz ve zemini koruyan poliüretan tandem yük tekerleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'gauge', 'name' => 'Batarya Göstergesi', 'description' => 'Enerji seviyesini anlık gösterir; bakım planlamasını kolaylaştırır.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'F3’ün nominal kapasitesi ve yük merkezi değeri nedir?', 'answer' => 'Nominal kapasite 1500 kg’dır ve 600 mm yük merkezinde ölçülmüştür; tipik EUR palet operasyonlarına uygundur.'],
                ['question' => 'Seyir ve kaldırma hızları operasyonu nasıl etkiler?', 'answer' => '4.0/4.5 km/s seyir ve 0.017/0.020 m/s kaldırma hızları, kısa mesafeli depo içi akışta çeviklik sağlar.'],
                ['question' => 'Batarya sistemi hangi türdür ve değişimi nasıldır?', 'answer' => '24V/20Ah Li‑Ion modül tak‑çıkar yapıdadır; ara şarj ve hızlı değişim ile vardiya sürekliliği desteklenir.'],
                ['question' => 'Teker seti ve malzeme türü nasıldır?', 'answer' => 'Ön 210×70, arka Ø80×60 (veya Ø74×88) PU teker seti sessiz ve zemin dostu sürüş sağlar.'],
                ['question' => 'Dönüş yarıçapı ve gövde ebatları dar koridorlarda yeterli mi?', 'answer' => '1360 mm dönüş yarıçapı, 1550 mm uzunluk ve 695/590 mm genişlik dar alan manevrası sağlar.'],
                ['question' => 'Eğim kabiliyeti değerleri yük altında nasıldır?', 'answer' => 'Maksimum eğim %5 (yüklü) ve %16 (yüksüz) olarak verilmiştir; rampa yaklaşımında bu değerler gözetilmelidir.'],
                ['question' => 'Fren sistemi ve güvenlik donanımı neleri içerir?', 'answer' => 'Elektromanyetik servis freni ve flip kapaklı batarya koruması güvenli çalışmayı destekler.'],
                ['question' => 'Platform F mimarisi ne kazandırır?', 'answer' => 'Sade konfigürasyon ve 4 farklı şasi ile farklı uygulamalara uyum ve stok/servis kolaylığı sağlar.'],
                ['question' => 'Standart paket ve sevkiyat avantajı nedir?', 'answer' => '4 ünite/kutu tedarik ve 40’ konteynere 176 ünite sığdırma ile navlun maliyeti düşer.'],
                ['question' => 'Ses seviyesi ve ergonomi açısından operatöre etkisi?', 'answer' => '<74 dB(A) ses seviyesi ve avuç içiyle kullanım sağlayan yeni tiller başı yorgunluğu azaltır.'],
                ['question' => 'Bakım ve toplam sahip olma maliyeti yaklaşımı nasıldır?', 'answer' => 'Basit şasi, DC sürüş kontrolü ve Li‑Ion sistem düşük bakım ve yüksek erişilebilirlik sunar.'],
                ['question' => 'Garanti koşulları ve yetkili hizmet iletişimi nedir?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. İXTİF satış ve servis için 0216 755 3 555 ile iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        echo "✅ Detailed: F3\n";
    }
}
