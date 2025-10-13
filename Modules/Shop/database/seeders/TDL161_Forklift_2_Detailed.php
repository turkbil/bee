<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TDL161_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'TDL-161')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: TDL-161');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF TDL161: Gücü Kompaktlıkla Buluşturan Li-Ion Forklift</h2><p>Günün ilk paleti rampaya geldiğinde operatör bir şey ister: güven, görünürlük ve kesintisiz güç. İXTİF TDL161, 3 tekerli çevik şasi ile dar koridorlarda milimetre hassasiyetinde manevra sağlarken, Li-Ion mimarisi sayesinde planlı molalara bağımlı kalmadan gün boyu aynı performansı korur. Ayarlanabilir direksiyon ve konforlu koltuk operatörün yorgunluğunu azaltır; geniş bacak alanı ve sağ üst köşedeki yüksek çözünürlüklü ekranla kritik bilgiler tek bakışta okunur.  Sonuç: daha güvenli, daha hızlı ve daha öngörülebilir operasyon.</p></section><section><h3>Teknik Yetkinlik</h3><p>Şasi altında çift AC tahrik motoru (5.4 kW × 2) ve 48V/280Ah batarya birlikte çalışarak yük altında dahi ivmelenmeyi kararlı tutar. 11 kW kaldırma motoru yükleri akıcı bir hızda kaldırır; yüklü/boş 0.35/0.43 m/s kaldırma ve 0.45/0.37 m/s indirme değerleri operasyon akışını tahmin edilebilir kılar. 15/16 km/s seyir hızı ve 15/17% eğim tırmanma kabiliyeti, rampalı yük akışlarında süreklilik sağlar. 3.0 m standart kaldırma yüksekliği, 2A sınıfı çatal taşıyıcı ve sağlam katı lastikler ile forklift farklı zemin koşullarında dengeli kalır. Hidrolik direksiyon sistemi mekanik basitliği korurken daha duyarlı bir hissiyat verir. Entegre 48V/50A şarj cihazı sayesinde gece boyunca dış ünite arayışına gerek kalmadan uygun prizle fırsat şarjı yapılabilir. Opsiyonel elektromanyetik joystick, tüm ana fonksiyonları avuç içinde birleştirir; daha hassas ve hızlı çalışma sunar.</p></section><section><h3>Sonuç</h3><p>Günün sonunda önemli olan toplam çevrim, enerji verimliliği ve operatör konforudur. Bu model, dar alan çevikliği ile saha üretkenliğini artırırken Li-Ion mimarisi sayesinde bakım ihtiyacını azaltır ve planlanmayan duruşları minimuma indirir. Doğru mast ve ataşman kombinasyonuyla intralojistiğinizi bir üst seviyeye taşıyın. Teknik danışmanlık ve demo için 0216 755 3 555 numarasından bize ulaşın.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1600 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V / 280Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '15/16 km/s (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => 'Wa 1605 mm, 3 teker çeviklik']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion Süreklilik', 'description' => 'Fırsat şarjı ile vardiya boyu sabit güç ve düşük bakım'],
                ['icon' => 'bolt', 'title' => 'Çift Tahrik', 'description' => '5.4kW×2 AC motorlar ile dengeli çekiş ve ivmelenme'],
                ['icon' => 'microchip', 'title' => 'LCD Gösterge', 'description' => 'Hız, saat, batarya ve park bilgisi tek ekranda'],
                ['icon' => 'cog', 'title' => 'Hidrolik Direksiyon', 'description' => 'Basit, güvenilir mimari ve hassas hissiyat'],
                ['icon' => 'building', 'title' => 'Optimize Şasi', 'description' => 'Kabin montaj ve sökümü kolaylaştıran gövde'],
                ['icon' => 'cart-shopping', 'title' => 'Joystick Seçeneği', 'description' => 'Avuç içi tüm fonksiyonlarla yüksek verim']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Dar koridorlu depolarda raf önü toplama ve besleme'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment akışında palet çapraz aktarımı'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde rampa yükleme/boşaltma'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış iş akışları'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas ürünlerin güvenli taşıması'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında hat besleme'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP taşıma'],
                ['icon' => 'flask', 'text' => 'Kimya ambalajlarında sızdırmaz palet hareketi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Çift AC tahrik ile yüksek çekiş ve yokuş kabiliyeti'],
                ['icon' => 'battery-full', 'text' => '48V Li-Ion ile hızlı fırsat şarjı ve düşük bakım'],
                ['icon' => 'arrows-alt', 'text' => '3 teker kompakt şasi ile dar alan manevrası'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik frenler ile güvenli duruş ve kontrol']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda'],
                ['icon' => 'pills', 'text' => 'İlaç'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Ağır Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG'],
                ['icon' => 'box-open', 'text' => 'Kargo/Ara Depo'],
                ['icon' => 'warehouse', 'text' => 'Soğuk Zincir Depo'],
                ['icon' => 'store', 'text' => 'DIY/Yapı Market'],
                ['icon' => 'industry', 'text' => 'Metal İşleme'],
                ['icon' => 'flask', 'text' => 'Boya-Kaplama'],
                ['icon' => 'microchip', 'text' => 'Beyaz Eşya Tedariki'],
                ['icon' => 'car', 'text' => 'Aftermarket Lojistiği'],
                ['icon' => 'building', 'text' => 'Belediye Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'Toptan Dağıtım']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 24 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 60 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 24,
                'battery_warranty_months' => 60
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '48V-50A Entegre Şarj Cihazı', 'description' => 'Standart dahili şarj ünitesi (16A priz uyumlu).', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => '48V-150A Harici Şarj', 'description' => 'Yüksek akım dış ünite ile hızlı şarj opsiyonu.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Yan Kaydırıcı (Side Shifter)', 'description' => 'Yan hareketle hızlı konumlama; kapasiteden 200 kg düşüm notu geçerlidir.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cart-shopping', 'name' => 'Non-marking Lastik Seti', 'description' => 'İz bırakmayan katı lastik opsiyonu.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Dar koridorlarda dönüş yarıçapı operasyonu nasıl kolaylaştırır?', 'answer' => '1605 mm dönüş yarıçapı ve 3 teker düzeni, raf önlerinde daha küçük manevra alanıyla palet konumlandırmayı hızlandırır.'],
                ['question' => 'Li-Ion batarya ile fırsat şarjı gerçekten vardiyayı nasıl etkiler?', 'answer' => '48V Li-Ion yapı hızlı ara şarjı destekler; planlı molalarda %20-40 dolumla vardiya sonunda performans düşmeden devam edilir.'],
                ['question' => 'Çift AC tahrikli yapı yokuş performansında ne sağlar?', 'answer' => '5.4kW×2 motor yokuşta çekişi korur; 15/17% eğim kabiliyeti rampalı hatlarda süreklilik sunar.'],
                ['question' => 'Operatör ergonomisi günlük verimliliği hangi yönüyle artırır?', 'answer' => 'Ayarlanabilir direksiyon ve konforlu koltuk, yorgunluğu azaltır; geniş bacak alanı pedal ulaşımını kolaylaştırır.'],
                ['question' => 'LCD ekran hangi bilgileri gösterir ve konumu neden önemlidir?', 'answer' => 'Hız, saat, batarya ve park bilgisini sunar; sağ üst köşe konumu görüş alanını kapatmadan okunabilirlik sağlar.'],
                ['question' => 'Standart mast ile maksimum kaldırma ve serbest kaldırma değerleri nedir?', 'answer' => 'Standartta 3000 mm kaldırma ve 100 mm serbest kaldırma bulunur; opsiyonla 3600–6000 mm aralığı mümkündür.'],
                ['question' => 'Yan kaydırıcı kullanıldığında kapasite neden düşer?', 'answer' => 'Ataşman ağırlığı ve ağırlık merkezi etkisi nedeniyle nominal kapasiteden 200 kg düşüm kurallar gereği uygulanır.'],
                ['question' => 'Katı lastik ve iz bırakmayan lastik seçiminde kriter nedir?', 'answer' => 'Standart katı lastikler uzun ömürlüdür; gıda/ilaç gibi zemin hassasiyetinde iz bırakmayan lastikler önerilir.'],
                ['question' => 'Bakım maliyetlerinde Li-Ion ile nasıl avantaj elde edilir?', 'answer' => 'Sulama, gaz çıkışı ve dengeleme gerektirmeyen yapı ile periyodik bakım süreleri azalır, toplam sahip olma maliyeti düşer.'],
                ['question' => 'Sürüş ve kaldırma motoru güçleri operasyon profilini nasıl etkiler?', 'answer' => '5.4kW×2 sürüş ve 11kW kaldırma kombinasyonu, hem hız hem kaldırma döngülerinde dengeli performans sağlar.'],
                ['question' => 'Harici hızlı şarj çözümü hangi durumlarda mantıklıdır?', 'answer' => 'Yoğun çok vardiyalı senaryolarda 48V-150A veya 200A harici şarj ile kısa duraklarda yüksek dolum hedeflenir.'],
                ['question' => 'Satış sonrası destek ve yedek parça temini için nasıl ulaşabilirim?', 'answer' => 'Satış, servis ve yedek parça talepleriniz için İXTİF müşteri hattı 0216 755 3 555 üzerinden destek alabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: TDL-161');
    }
}
