<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL203P_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL203P')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL203P'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section><h2>İXTİF EFL203P: Premium sınıfta hız, güç ve görüş</h2>
<p>EFL203P, iki versiyonlu EFL203 ailesinin en çevik ve hızlı üyesidir. 19/20 km/s sürüş, 0.48/0.54 m/s kaldırma ve %22/%28 tırmanma değerleri; yoğun rampalı, dur-kalkın sık olduğu operasyonlarda bile akışı hızlandırır. Büyük LED ekran, güçlü LED aydınlatma ve optimize hortum düzenine sahip yeni direk, yük ve çevreyi daha net görerek güvenli hızda çalışmayı mümkün kılar. Yüksek şasi açıklığı ve geniş pnömatik lastikler, dış sahada yağmurlu zeminde dahi kontrolü elinizde tutmanızı sağlar.</p></section>
<section><h3>Teknik üstünlüklerin toplamı</h3>
<p>2000 kg kapasite ve 500 mm yük merkezi ile EFL203P, 3540 kg servis ağırlığı, 2356 mm yüke kadar uzunluk ve 1154 mm genişlik ile palet çeşitliliğine uyumludur. 2110 mm dönüş yarıçapı dar koridor çevikliğini destekler. 15 kW S2 sürüş motoru ve 18 kW S3 kaldırma motoru; AC sürüş kontrolü, hidrolik servis freni ve mekanik park freni ile birlikte tutarlı güç aktarımı ve kontrollü yavaşlamayı garanti eder.</p></section>
<section><h3>Sonuç</h3>
<p>Çok vardiyalı, yüksek yoğunluklu uygulamalarda aradığınız hız ve süreklilik EFL203P’de birleşir. Premium performans ile TCO’yu aşağı çeker; verimi yukarı taşır. Detay ve keşif için 0216 755 3 555.</p></section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (c=500 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V 230Ah (opsiyon 460Ah)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '19/20 km/s (yük/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '2110 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'bolt', 'title' => 'Premium hız', 'description' => 'Daha yüksek sürüş ve kaldırma hızlarıyla çevik operasyon'],
                ['icon' => 'battery-full', 'title' => 'Li-Ion güç', 'description' => 'Yüksek kullanım oranı ve fırsat şarjı uyumu'],
                ['icon' => 'shield-alt', 'title' => 'Gelişmiş görüş', 'description' => 'Yeni direk ve bolted OHG ile açık görüş alanı'],
                ['icon' => 'building', 'title' => 'Dış saha hakimiyeti', 'description' => 'Yüksek açıklık ve pnömatik lastik kombinasyonu'],
                ['icon' => 'store', 'title' => 'Ergonomi', 'description' => 'Geniş ayak alanı ve ayarlanabilir direksiyon'],
                ['icon' => 'star', 'title' => 'LED arayüz', 'description' => 'Büyük ekran ve güçlü farlarla sezgisel kontrol']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yoğun vardiyada rampalı yükleme-boşaltma'],
                ['icon' => 'box-open', 'text' => 'Hız öncelikli cross-docking operasyonları'],
                ['icon' => 'industry', 'text' => 'Yüksek tempolu hat besleme ve WIP taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv tedarikinde saatlik çekme planı'],
                ['icon' => 'snowflake', 'text' => 'Soğukta hızdan ödün vermeden malzeme akışı'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında hızlı ve güvenli ürün hareketi'],
                ['icon' => 'store', 'text' => 'Perakende DC yoğun toplama/sürüş turu'],
                ['icon' => 'pills', 'text' => 'İlaçta dar zaman pencereli sevkiyat'] 
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '19/20 km/s sürüş ile çevrim süresi kısaltma'],
                ['icon' => 'battery-full', 'text' => '80V Li-Ion ve 460Ah seçenekle uzun otonomi'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt boyut ve 2110 mm dönüş ile verimli rota'],
                ['icon' => 'shield-alt', 'text' => 'Direk ve OHG tasarımıyla güvenli hızda çalışma'],
                ['icon' => 'star', 'text' => 'LED gösterge ve farlarla daha sezgisel kullanım']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Soğuk Zincir Lojistiği'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Dağıtımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama ve Üretim'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Satın alım tarihinden itibaren makine için 12 ay, Li-Ion batarya modülleri için 24 ay fabrika garantisi sağlanır. Garanti normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'charging-station', 'name' => 'Harici hızlı şarj cihazı', 'description' => 'Üç faz premium hızlı şarj', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Entegre şarj kablosu', 'description' => 'Gövdeye entegre şarj soketi', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Ağır hizmet pnömatik lastik', 'description' => 'Dış sahada kavrama ve dayanıklılık', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => '80V 460Ah Li-Ion paket', 'description' => 'Çok vardiya için yüksek kapasite', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EFL203P ile EFL203 arasındaki temel performans farkı nedir?', 'answer' => 'EFL203P daha yüksek sürüş (19/20 km/s) ve kaldırma hızları (0.48/0.54 m/s) ile daha güçlü tırmanma (%22/%28) sunar.'],
                ['question' => 'Premium versiyonun tırmanma kabiliyeti saha planlamasını nasıl etkiler?', 'answer' => 'Daha yüksek eğim kabiliyeti, rampalı güzergâhlarda taşıma kapasitesinin gün boyu korunmasını destekler.'],
                ['question' => 'Standart batarya ile opsiyon 460Ah arasında seçim nasıl yapılır?', 'answer' => 'Vardiya süresi, rampa yoğunluğu ve tur frekansına göre 230Ah tek veya 460Ah çok vardiya senaryoları için uygundur.'],
                ['question' => 'Operatör ekranı hangi bilgileri gösterir?', 'answer' => 'Büyük LED ekran hız, akü durumu, arıza kodları ve çalışma saatini net biçimde sunar.'],
                ['question' => 'Yağışlı koşullarda fren ve çekiş kararlılığı nasıldır?', 'answer' => 'Pnömatik lastikler ve hidrolik servis freni, ıslak zeminde dengeli frenleme ve çekiş sağlar.'],
                ['question' => 'Hangi mast konfigürasyonları mevcut?', 'answer' => '3000 mm standart, farklı yükseklikler ve serbest kaldırma seçenekleri opsiyon olarak sunulur.'],
                ['question' => 'Kabin ergonomisinde hangi ayar seçenekleri bulunur?', 'answer' => 'Direksiyon açısı ayarı, kol dayama ve geniş ayak boşluğu farklı operatörlere hızla uyum sağlar.'],
                ['question' => 'Bakım aralıkları Li-Ion teknolojisinde nasıl değişir?', 'answer' => 'Su ekleme ve eşitleme şarjı gerekmediğinden periyodik bakım duruşları azalır.'],
                ['question' => 'Yan kaydırıcı kullanımı kapasiteye etkide bulunur mu?', 'answer' => 'Yan kaydırıcı kullanılan konfigürasyonlarda nominal kapasiteden 100 kg düşüm hesaplanmalıdır.'],
                ['question' => 'Koridor gereksinimleri nelerdir ve rota planlaması nasıl yapılır?', 'answer' => 'Ast 3805/4005 mm (enine/boyuna) değerleri raf aralığı ve dönüş planı için referanstır.'],
                ['question' => 'Enerji tüketimini düşürmek için hangi sürüş pratikleri önerilir?', 'answer' => 'Fırsat şarjı, hız modlarının doğru seçimi ve yumuşak ivmelenme enerji verimini artırır.'],
                ['question' => 'Teklif ve demo talebi için kiminle iletişime geçebilirim?', 'answer' => 'İXTİF ile 0216 755 3 555 üzerinden iletişime geçerek keşif, demo ve fiyatlandırma talep edebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('🧩 Detailed güncellendi: EFL203P');
    }
}
