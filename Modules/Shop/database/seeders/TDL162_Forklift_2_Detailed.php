<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TDL162_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'TDL162')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: TDL162');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF TDL162: 80V Güç, Kompakt Çeviklik</h2><p>Gün ilk sevkiyatla başlar ve zamanla yarış başladığında, TDL162 hızlanır. 80V çift sürüş mimarisi, 3 teker manevra kabiliyeti ve 1.6 ton nominal kapasiteyi 4.5 metreye kadar koruyan denge ile operatörün güvenini ilk dakikadan artırır. Entegre şarj cihazı ve harici hızlı şarj girişi aynı gövdede buluşur; bu sayede tek vardiya ara molalarında fırsat şarjı yapmak da, çok vardiyalı yoğun hatlarda harici hızlı şarj ile ritmi hiç bozmadan devam etmek de mümkündür. Geniş bacak alanı, gelişmiş direksiyon hassasiyeti ve sessiz kabin ortamı, uzun vardiyalarda bile yorgunluğu azaltırken LED ekran ve performans modu seçici operasyonu sezgisel hale getirir. Depo dönüş hızını artırmak, rampa ve dar koridorları güvenle geçmek ve enerji maliyetlerini aşağı çekmek isteyen ekipler için TDL162 yeni bir standart sunar.</p></section><section><h3>Teknik</h3><p>Elektrikli 3 teker forklift mimarisi, 1470 mm dingil mesafesi ve 1639 mm dönüş yarıçapı ile dar alanlarda bile çevik hareket etmeyi sağlar. 16/17 km/s seyir hızı, 0.50/0.52 m/s kaldırma hızı ve 20/25% tırmanma değerleri sınıfına göre belirgin bir ivme sunar. 80V/280Ah Li-Ion batarya modülü, düşük iç direnç ve yüksek şarj-deşarj verimliliğiyle gün boyu kesintisiz performansa destek olur. Standart direk ile 1995 mm kapalı yükseklik ve 3000 mm kaldırma yüksekliği sağlanır; üç kademeli direk konfigürasyonlarında nominal 1600 kg kapasite 4.5 m seviyesine kadar korunur. 18x7-8 ön ve 140/55-9 arka lastikler ile zemin tutuşu geliştirilmiştir; elektromanyetik servis ve park frenleri güvenli duruş sağlar. S2 60 dk’da 5.4 kW x2 sürüş motoru ve S3 15%’te 18 kW kaldırma motoru, talep geldiğinde ani güç ihtiyacını karşılar. 65 dB(A) sürücü kulak seviyesi ise gürültüyü azaltarak vardiya sonunda dahi konforu korur. Entegre şarj cihazı 35 A çıkış verir; harici hızlı şarj girişiyle çok vardiyalı operasyonlar desteklenir. LED gösterge, batarya durumu ve performans modu seçimini net biçimde sunar.</p></section><section><h3>Sonuç</h3><p>TDL162, enerji verimliliği, çeviklik ve ergonomiyi tek platformda birleştirerek depo verimini yükseltir. Dar koridorlar, sık rampa geçişleri ve yoğun vardiya planları için ideal bir çözümdür. Seçilebilir joystick hidrolik kontrol ve tam kabin opsiyonu gibi premium seçeneklerle farklı iklim ve iş koşullarına uyum sağlar. Doğru konfigürasyon, doğru mast ve doğru ataşmanlarla planlandığında toplam sahip olma maliyetini düşürür ve ekiplerin günlük ritmini hızlandırır. Detaylı konfigürasyon ve keşif için 0216 755 3 555.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1600 kg (Q), 500 mm yük merkezi'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 280Ah Li-Ion (entegre + harici şarj girişi)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '16/17 km/s (yükle/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => 'Dönüş yarıçapı 1639 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '80V Li-Ion Güç', 'description' => '80V/280Ah standart batarya uzun menzil ve fırsat şarj esnekliği sağlar.'],
                ['icon' => 'bolt', 'title' => 'Çift Sürüş Tahrik', 'description' => 'İki tahrik motoru ile güçlü çekiş ve yüksek ivmelenme elde edilir.'],
                ['icon' => 'cog', 'title' => 'Optimize Mimari', 'description' => 'Şasi ve denge geliştirmeleri hız ve kaldırma performansını artırır.'],
                ['icon' => 'plug', 'title' => 'İki Şarj Seçeneği', 'description' => 'Entegre şarj + harici hızlı şarj girişi aynı gövdede sunulur.'],
                ['icon' => 'star', 'title' => 'Ergonomi ve Sessizlik', 'description' => 'Geniş alan, düşük gürültü ve gelişmiş direksiyon hissi.'],
                ['icon' => 'microchip', 'title' => 'Akıllı Gösterge', 'description' => 'LED ekran ve performans modu seçiciyle sezgisel kullanım.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yoğun vardiyalı depo içi taşıma ve raf besleme akışları'],
                ['icon' => 'box-open', 'text' => 'E-ticaret sipariş hazırlama ve çapraz sevkiyat süreçleri'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde giriş-çıkış hat yönetimi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa ve ara depo taşımaları'],
                ['icon' => 'snowflake', 'text' => 'Soğuk zincir depolarında düşük gürültüyle kontrollü taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik lojistiğinde hassas ürünlerin güvenli taşınması'],
                ['icon' => 'flask', 'text' => 'Kimyasal malzeme depolarında güvenli ve dengeli kaldırma'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP malzeme akışı yönetimi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '80V çift tahrik ile sınıfının üzerinde hız, eğim tırmanma ve ivmelenme'],
                ['icon' => 'battery-full', 'text' => 'Standart Li-Ion batarya ve iki şarj alternatifi ile yüksek erişilebilirlik'],
                ['icon' => 'arrows-alt', 'text' => '3 teker kompakt yapı ve 1639 mm dönüş yarıçapıyla dar koridor çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik frenler ve optimize dengeyle güvenli kullanım'],
                ['icon' => 'cog', 'text' => 'Düşük bakım gereksinimi ve yüksek enerji verimliliğiyle TCO avantajı']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'building', 'text' => 'Yapı Malzemeleri ve DIY'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Ticaret'],
                ['icon' => 'print', 'text' => 'Ambalaj ve Matbaa'],
                ['icon' => 'industry', 'text' => 'Endüstriyel Üretim'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri Dağıtımı'],
                ['icon' => 'book', 'text' => 'Kırtasiye ve Yayıncılık'],
                ['icon' => 'seedling', 'text' => 'Tarım Ürünleri Deposu'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Ürünleri'],
                ['icon' => 'cart-shopping', 'text' => 'Market Zincir Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay fabrika garantisi kapsamındadır. Li-Ion batarya modülleri satın alımdan itibaren 24 ay garanti altındadır. Garanti normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı', 'description' => 'Makine üzerinde standart tek faz entegre şarj cihazı ile pratik şarj.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'charging-station', 'name' => 'Harici Hızlı Şarj', 'description' => 'Çok vardiyalı operasyonlar için yüksek akımlı harici hızlı şarj ünitesi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tam Kabin Opsiyonu', 'description' => 'Farklı hava koşullarında kesintisiz çalışma için tam kapalı kabin seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'microchip', 'name' => 'Joystick Hidrolik Kontrol', 'description' => 'Kol dayamasında konumlanan joystick ile tüm hidrolik fonksiyonları tek elle yönetim.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '80V çift sürüşün 48V sistemlere göre avantajı nedir?', 'answer' => 'Daha düşük akım ile aynı gücü üretir; ısı kaybı azalır, verim artar ve yüksek ivmelenme ile eğim performansı yükselir. Enerji tüketimi düşer.'],
                ['question' => 'Nominal 1600 kg kapasite hangi yüksekliklere kadar korunur?', 'answer' => 'Üç kademeli mast konfigürasyonlarında 4.5 metre seviyesine kadar 1600 kg’a yakın residual kapasite değerleri korunur.'],
                ['question' => 'Standart mast ve serbest kaldırma seçenekleri nelerdir?', 'answer' => 'Standart mast 3.0 m, ayrıca 3.6 m ve 4.0 m; serbest kaldırmalı triplex seçenekler 4.5 m ile 6.0 m aralığında sunulur.'],
                ['question' => 'Dönüş yarıçapı ve dar koridor performansı nasıldır?', 'answer' => '1639 mm dönüş yarıçapı ve 1050 mm genişlik ile 3339 mm koridorlarda EUR palet operasyonları yönetilebilir.'],
                ['question' => 'Seyir ve kaldırma hızları değerleri nedir?', 'answer' => 'Seyir hızı 16/17 km/s; kaldırma 0.50/0.52 m/s, indirme hızı 0.55/0.55 m/s olup sınıfında üst performans sağlar.'],
                ['question' => 'Eğim tırmanma kabiliyeti hangi değerlerde?', 'answer' => 'Maksimum eğim tırmanma kapasitesi yükle %20, yüksüz %25 olup rampa ve seviye farklarında güven verir.'],
                ['question' => 'Gürültü düzeyi ve ergonomi açısından öne çıkanlar nelerdir?', 'answer' => '65 dB(A) ses seviyesi, geniş bacak alanı, gelişmiş direksiyon hassasiyeti ve LED gösterge ile konforlu, güvenli çalışma sunar.'],
                ['question' => 'Bakım ve işletme maliyetlerine etkisi nasıldır?', 'answer' => 'Li-Ion sistem, fren ve tahrik kontrolünde düşük aşınma sağlar; enerji verimliliği ve az bakım ihtiyacı toplam maliyeti düşürür.'],
                ['question' => 'Hangi ataşmanlarla uyumludur?', 'answer' => 'Standart 2A sınıf çatal taşıyıcı ile çok sayıda klemens, rotator ve çatal konfigürasyonlarıyla uyumludur.'],
                ['question' => 'Operasyon güvenliği için hangi sistemler bulunur?', 'answer' => 'Elektromanyetik servis/park frenleri, optimize denge ve net görüş alanı güvenli kullanım sağlar.'],
                ['question' => 'Çok vardiyalı kullanımda şarj stratejisi nasıl olmalı?', 'answer' => 'Ara molalarda entegre şarj ile fırsat şarjı, yoğun planlarda harici hızlı şarj ile kesintisiz vardiya önerilir.'],
                ['question' => 'Garanti koşulları ve satış sonrası desteğe nasıl ulaşırım?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. Kurulum, eğitim ve servis için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: TDL162');
    }
}
