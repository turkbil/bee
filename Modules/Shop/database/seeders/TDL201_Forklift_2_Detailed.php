<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TDL201_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'TDL-201')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: TDL-201');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF TDL201: Gücü Kompaktlıkla Buluşturan Li-Ion Forklift</h2><p>Günün ilk paleti rampaya geldiğinde operatör bir şey ister: güven, görünürlük ve kesintisiz güç. İXTİF TDL201, 3 tekerli çevik şasi ile dar koridorlarda milimetre hassasiyetinde manevra sağlarken, Li-Ion mimarisi sayesinde planlı molalara bağımlı kalmadan gün boyu aynı performansı korur. Ayarlanabilir direksiyon ve konforlu koltuk operatörün yorgunluğunu azaltır; geniş bacak alanı ve sağ üst köşedeki yüksek çözünürlüklü ekranla kritik bilgiler tek bakışta okunur.  Sonuç: daha güvenli, daha hızlı ve daha öngörülebilir operasyon.</p></section><section><h3>Teknik Yetkinlik</h3><p>Şasi altında çift AC tahrik motoru (5.4 kW × 2) ve 48V/405Ah batarya birlikte çalışarak yük altında dahi ivmelenmeyi kararlı tutar. 11 kW kaldırma motoru yükleri akıcı bir hızda kaldırır; yüklü/boş 0.35/0.43 m/s kaldırma ve 0.45/0.37 m/s indirme değerleri operasyon akışını tahmin edilebilir kılar. 15/16 km/s seyir hızı ve 15/17% eğim tırmanma kabiliyeti, rampalı yük akışlarında süreklilik sağlar. 3.0 m standart kaldırma yüksekliği, 2A sınıfı çatal taşıyıcı ve sağlam katı lastikler ile forklift farklı zemin koşullarında dengeli kalır. Hidrolik direksiyon sistemi mekanik basitliği korurken daha duyarlı bir hissiyat verir. Entegre 48V/50A şarj cihazı sayesinde gece boyunca dış ünite arayışına gerek kalmadan uygun prizle fırsat şarjı yapılabilir. Optimize gövde ve kabin yapısı, montaj ve sökme sürelerini kısaltarak filo yönetiminde esneklik sağlar.</p></section><section><h3>Sonuç</h3><p>Günün sonunda önemli olan toplam çevrim, enerji verimliliği ve operatör konforudur. Bu model, dar alan çevikliği ile saha üretkenliğini artırırken Li-Ion mimarisi sayesinde bakım ihtiyacını azaltır ve planlanmayan duruşları minimuma indirir. Doğru mast ve ataşman kombinasyonuyla intralojistiğinizi bir üst seviyeye taşıyın. Teknik danışmanlık ve demo için 0216 755 3 555 numarasından bize ulaşın.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg @ 500 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V / 405Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '15/16 km/s (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => 'Wa 1605 mm, 3 teker çeviklik']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Yüksek Kapasite Akü', 'description' => '405Ah seçenek ile uzun vardiyada yüksek dayanım'],
                ['icon' => 'bolt', 'title' => 'Dual AC Tahrik', 'description' => '5.4kW×2 sürüş ile süreklilik ve verimlilik'],
                ['icon' => 'microchip', 'title' => 'Bilgi Odaklı LCD', 'description' => 'Hız, saat, pil ve park göstergeleri net görüntü'],
                ['icon' => 'cog', 'title' => 'Hidrolik Direksiyon', 'description' => 'Basit ve güvenilir tasarım, düşük bakım'],
                ['icon' => 'building', 'title' => 'Tek Parça Şasi', 'description' => 'Kabin montajı ve sökümü için optimize yapı'],
                ['icon' => 'cart-shopping', 'title' => 'Joystick Opsiyonu', 'description' => 'Avuç içi kontrolle hassas kumanda']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yüksek hacimli DC’lerde rampa besleme ve boşaltma'],
                ['icon' => 'box-open', 'text' => 'Cross-dock hatlarında palet transferi'],
                ['icon' => 'store', 'text' => 'Perakende mağaza arkası sevkiyat hazırlığı'],
                ['icon' => 'snowflake', 'text' => 'Soğuk zincir giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolamada hassas taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv komponent akışında hat besleme'],
                ['icon' => 'industry', 'text' => 'Üretim alanları arası yarı mamul hareketi'],
                ['icon' => 'flask', 'text' => 'Kimyasal varil ve IBC palet akışı']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '5.4kW×2 tahrik ile ivmelenme ve yokuş performansı'],
                ['icon' => 'battery-full', 'text' => '405Ah akü ile uzun vardiyalarda süreklilik'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ile dar koridor yetkinliği'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik frenleme ile güvenli duruş']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve İçecek'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Sanayi Üretimi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'building', 'text' => 'Kamu ve Belediye Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Dağıtım'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Hızlı Tüketim'],
                ['icon' => 'warehouse', 'text' => 'Geniş Alan Depolama'],
                ['icon' => 'store', 'text' => 'DIY/Yapı Market'],
                ['icon' => 'industry', 'text' => 'Metal ve Dökümhane'],
                ['icon' => 'flask', 'text' => 'Boya ve Kimyasal Lojistiği'],
                ['icon' => 'microchip', 'text' => 'Beyaz Eşya Dağıtımı'],
                ['icon' => 'car', 'text' => 'Aftermarket Parça'],
                ['icon' => 'building', 'text' => 'Kampüs ve Üniversite Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'Toptan Ticaret']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 24 ay fabrika garantisi verilir. Li-Ion batarya modülleri için 60 aya kadar koruma sağlanır. Garanti genel kullanım şartlarında üretim kaynaklı hataları kapsar.',
                'duration_months' => 24,
                'battery_warranty_months' => 60
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '48V-50A Entegre Şarj Cihazı', 'description' => 'Standart dahili şarj; 16A priz ile uyumlu.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => '48V-200A Harici Şarj', 'description' => 'Çok vardiyalı kullanım için yüksek akım harici ünite.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Yan Kaydırıcı (Side Shifter)', 'description' => 'Hızlı çatal hizalama; kapasiteden 200 kg düşüm kuralı geçerli.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cart-shopping', 'name' => 'Non-marking Lastik Seti', 'description' => 'Gıda/ilaç zeminleri için iz bırakmayan lastik.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '2000 kg nominal kapasite hangi ataşmanlarda korunur?', 'answer' => 'Standart çatal ve taşıyıcı ile nominal kapasite geçerlidir; yan kaydırıcı gibi ataşmanlar eklendiğinde 200 kg düşüm uygulanır.'],
                ['question' => '405Ah batarya ile bir vardiyada kaç saat çalışılabilir?', 'answer' => 'Operasyon yoğunluğuna bağlıdır; fırsat şarjı uygulandığında vardiya boyunca sabit performans korunur.'],
                ['question' => 'LCD ekranın üst köşe konumu görüş güvenliğini nasıl etkiler?', 'answer' => 'Görüş alanını kapatmadan bilgi verir; çatal uçlarının ve yaya trafiğinin izlenmesini kolaylaştırır.'],
                ['question' => 'Hidrolik direksiyonun bakım avantajı nedir?', 'answer' => 'Basit mekanik yapı ve daha az parça sayesinde periyodik bakım süreleri kısalır, toplam maliyet düşer.'],
                ['question' => 'Dar koridorlarda 3 teker yapısının artısı nedir?', 'answer' => 'Merkezlenmiş tek arka teker ile daha dar dönüş yayı elde edilir, raf önünde hizalama kolaylaşır.'],
                ['question' => 'Eğim performansı rampalı tesislerde yeterli mi?', 'answer' => '15/17% değerleri tipik rampa geçişleri için yeterlidir; yük ağırlığı ve zemin şartları göz önüne alınmalıdır.'],
                ['question' => 'Kaldırma motoru gücü operasyon hızına nasıl yansır?', 'answer' => '11 kW kaldırma motoru, yüklü/boş 0.35/0.43 m/s değerleriyle dengeli döngü süreleri sağlar.'],
                ['question' => 'Harici şarj cihazı ne zaman tercih edilir?', 'answer' => 'Çok yoğun vardiya düzeninde 150A/200A harici şarj ile kısa molalarda yüksek dolum hedeflenir.'],
                ['question' => 'Lastik malzemesini seçerken nelere bakmalıyım?', 'answer' => 'Zemin hassasiyeti, nem ve sıcaklık koşulları; katı veya non-marking lastikler buna göre seçilir.'],
                ['question' => 'Çatal ölçüsü ve sınıfı hangi aksesuarlarla uyumludur?', 'answer' => '40×122×1070 mm çatal ve 2A sınıfı taşıyıcı pek çok standart ataşmanla uyumludur.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler?', 'answer' => '79 dB(A) seviyesinde düzenli çalışma sağlar; operatör yorgunluğu azalır.'],
                ['question' => 'İXTİF satış ve servis desteğine nasıl erişirim?', 'answer' => 'Tüm satış, kiralama ve servis talepleriniz için İXTİF müşteri hattı 0216 755 3 555 ile iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: TDL-201');
    }
}
