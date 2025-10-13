<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EXP15_Otonom_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EXP15')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EXP15');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF EXP15: “Sadelikle Otomasyon” felsefesinin somut hali</h2>
  <p>İXTİF EXP15, dünyadaki ilk otomatik palet transpaleti sınıfının öncüsü olarak, malzeme akışını karmaşıklıktan arındırır. Tek bir çalışanın beş dakikada öğrenebileceği beş tuşlu basit arayüz, otomasyonu herkesin erişimine açar. WiFi ya da karmaşık BT altyapısı gerektirmeyen kurulumuyla, başlangıç ve bitiş noktalarına yerleştirilen reflektörler üzerinden 50 metreye kadar hatlar hızla devreye alınır. İş gücü yoğun ve tekrarlı taşıma görevleri otomatikleşirken, personel daha değerli işlere odaklanır. 1500 kg kapasiteli şasi, 1.1/1.25 m/s sürüş hızları ve 1400 mm dönüş yarıçapı ile depo koridorlarında çeviklik ve süreklilik sunar.</p>
</section>
<section>
  <h3>Teknik güç ve görev zekâsı</h3>
  <p>EXP15’in kalbinde 24V/60Ah Li-İon tak-çıkar enerji paketi ve AC sürüş mimarisi yer alır. Elektromanyetik fren sistemi, yüklü/yüksüz 1.1/1.25 m/s hızlarda kontrollü duruş sağlar. 55/150/1150 mm çatal ölçüleri ve 540/600/685 mm çatallar arası seçenekleri, farklı palet tipleriyle uyumu artırır. 636 (650) (700) mm gövde genişliği ve 1620 mm toplam uzunluk, 1860 mm koridor genişliğinde EUR paletlerle güvenli manevra olanağı tanır. ±20 mm park ve navigasyon hassasiyetine sahip 2D görsel navigasyon, 180° lidar ile birlikte çevre algıyı güçlendirir; önde ve altta konumlanan lazer kapsama, ayak ve engel algılama alanlarını bütünler. Sistem, dokun-çalış iş akışını destekleyerek 10 farklı rota/görevi hafızada tutar; operatör yalnızca “+” ve “−” tuşlarıyla görev değiştirir.</p>
  <p>Kaldırma/indirme hızları (0.020/0.035 m/s ve 0.058/0.046 m/s) istifleme gerektirmeyen zemin taşıma senaryolarında akıcı akışı destekler. 5/5 % eğim kabiliyeti, rampa geçişlerini standart depo koşullarında güvenle yönetir. <em>&lt;74 dB(A)</em> ses basıncı değeri, gürültü hassasiyeti yüksek vardiyalarda konfor sağlar.</p>
</section>
<section>
  <h3>Sonuç ve çağrı</h3>
  <p>EXP15, otomasyona ilk adımı atmak isteyen işletmeler için güçlü bir başlangıç noktasıdır. Kurulumun sadeliği, düşük bakım gereksinimi ve çok görevlilik sayesinde ilk günden verim artışı sağlayan bir çözüm sunar. Mevcut elektrikli transpalet düzeyinde servis kolaylığı, toplam sahip olma maliyetini aşağı çeker. Teknik detay ve keşif için hemen arayın: <strong>0216 755 3 555</strong>.</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 60Ah Li-İon (tak-çıkar)'],
                ['icon' => 'gauge', 'label' => 'Hız (yüklü/yüksüz)', 'value' => '1.1 / 1.25 m/s'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1400 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Tak-çıkar Li-İon enerji', 'description' => '24V/60Ah modül dakikalar içinde değişir ve vardiya sürekliliği sağlar.'],
                ['icon' => 'shield-alt', 'title' => 'Çift lazer koruma', 'description' => 'Yukarı/aşağı kapsama ile engel ve ayak bölgesi algılama güvenliğini artırır.'],
                ['icon' => 'microchip', 'title' => '2D görsel navigasyon', 'description' => '±20 mm hassasiyetle nokta atışı park ve bırakma yapar.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt gövde', 'description' => 'Dar koridorda 1860 mm koridor gereksinimiyle akıcı manevra.'],
                ['icon' => 'bolt', 'title' => 'AC sürüş + e-fren', 'description' => 'Düşük hızda kontrollü, yüksek tekrarda tutarlı duruş sağlar.'],
                ['icon' => 'cart-shopping', 'title' => 'Çoklu görev belleği', 'description' => '10 rota/görev kaydı ve +/− ile anında görev değiştirme.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Tekrarlı nokta-noktaya malzeme transferinde otomatik taşıma'],
                ['icon' => 'warehouse', 'text' => 'Üretim hücresinden ara depoya WIP akış beslemesi'],
                ['icon' => 'store', 'text' => 'Perakende DC içinde hatlar arası palet akışı'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış tünellerinde düşük hız güvenliği'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetikte hassas palet bırakma gerektiren alanlar'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça besleme ve geri toplama hatları'],
                ['icon' => 'industry', 'text' => 'Ağır iş ortamlarında operatör yorgunluğunu sıfırlama'],
                ['icon' => 'flask', 'text' => 'Kimyada güvenli erişim bölgeleriyle çarpışma önleme']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Dünyanın ilk otomatik palet transpaleti sınıfında öncü çözüm'],
                ['icon' => 'battery-full', 'text' => 'Plug-in Li-İon batarya ile kesintisiz vardiya ve hızlı değişim'],
                ['icon' => 'arrows-alt', 'text' => '636–700 mm gövde genişliğiyle dar alan uyumu ve çeviklik'],
                ['icon' => 'shield-alt', 'text' => '180° lidar ve lazer kombinasyonuyla gelişmiş güvenlik'],
                ['icon' => 'cart-shopping', 'text' => '10 göreve kadar hafıza ve +/− ile anında görev geçişi']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım Operasyonları'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir Depoları'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği ve Depolama'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama ve İşleme'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yüksek Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça Depoları'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan ve Veteriner Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-İon batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Akıllı Şarj Cihazı', 'description' => 'Tak-çıkar batarya için optimize edilmiş şarj profili, hücre dengeleme desteği.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Reflektör Kiti', 'description' => 'Başlangıç-bitiş noktaları için montaj braketi ve yüksek yansıtıcılık seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'Yedek Li-İon Batarya (60Ah)', 'description' => 'Kesintisiz vardiya için ikinci modül, güvenli tak-çıkar mekanizması.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Tandem Tekerlek Seti', 'description' => 'Zemin düzensizliklerinde daha stabil geçiş sağlayan tandem rulolar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Kurulum için WiFi veya ağ altyapısı gerekir mi, ilk devreye alma nasıl yapılır?', 'answer' => 'Hayır, WiFi gerekmez. Başlangıç ve bitiş noktalarına reflektörler yerleştirilir ve güzergâh öğretilir. Sonrasında dokun-çalış mantığıyla görev başlatılır.'],
                ['question' => 'Çoklu görev değiştirme operatör tarafından nasıl ve ne kadar hızlı yapılabilir?', 'answer' => 'Cihaz 10 göreve kadar rota saklar. Operatör sadece “+” ve “−” tuşlarıyla saniyeler içinde görev değiştirir ve yeni park alanına yönlendirme yapılır.'],
                ['question' => 'Navigasyon hassasiyeti ve park doğruluğu hangi seviyededir?', 'answer' => '2D görsel navigasyon ile ±20 mm hassasiyet sağlanır. Park noktaları reflektörle işaretlendiğinde palet bırakma ve alma tekrarlanabilir olur.'],
                ['question' => 'Güvenlik sensörleri neleri kapsar ve çarpışma önleme nasıl çalışır?', 'answer' => '180° lidar kapsaması üst bölgeyi, alt lazerler ise ayak/engel bölgelerini tarar. Algılama olduğunda hız düşürme ve güvenli duruş devreye girer.'],
                ['question' => 'Eğim performansı ve rampa geçişlerinde tavsiye edilen sınırlar nelerdir?', 'answer' => 'Maksimum eğim yüklü/yüksüz %5’tir. Yükseklik değişimlerinde hız sınırları ve frenleme logiği güvenliği korur.'],
                ['question' => 'Enerji sistemi vardiya içinde nasıl yönetilir, batarya değişimi ne kadar sürer?', 'answer' => 'Tak-çıkar 24V/60Ah modül, dakikalar içinde değiştirilebilir. İkinci batarya ile vardiya sürekliliği sağlanır ve kesinti minimuma iner.'],
                ['question' => 'Ses seviyesi ve iç mekân kullanımında konfor açısından ölçümler nedir?', 'answer' => 'Sürücü kulağında <74 dB(A) ölçülür. İç mekân kullanımına uygundur ve gürültü limitlerine uyumludur.'],
                ['question' => 'Çatal ve gövde ölçüleri hangi palet tipleriyle uyumludur?', 'answer' => '55/150/1150 mm çatal ve 540/600/685 mm çatallar arası genişlik, yaygın EUR paletler ile tam uyumluluk sağlar.'],
                ['question' => 'Bakım gereksinimleri ve servis planı nasıldır, özel mühendislik ister mi?', 'answer' => 'Elektrikli transpalet düzeyinde basit bakımla çalışır. Özel robotik mühendislik gerekmez; periyodik kontrol listeleriyle sürdürülebilir.'],
                ['question' => 'Hangi çevresel koşullarda çalışması önerilir, dış ortam kullanımı mümkün mü?', 'answer' => 'İç mekân önerilir. Toz ve nem kontrolü yapılan depolarda en iyi performansı verir; dış ortam için korumalı alanlar tercih edilmelidir.'],
                ['question' => 'Yazılım güncellemeleri ve görev öğretimi sırasında veri güvenliği nasıl sağlanır?', 'answer' => 'Görev öğretimi cihaz üstünden yapılır, haricî ağ bağlantısı zorunlu değildir. Bu sayede veri yüzeyi sınırlanır ve güvenlik artar.'],
                ['question' => 'Garanti kapsamı nedir ve satış-sonrası destek için kime başvurmalıyım?', 'answer' => 'Makine 12 ay, Li-İon batarya 24 ay garanti kapsamındadır. Satış, servis ve teknik destek için İXTİF çağrı merkezine 0216 755 3 555 üzerinden ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EXP15');
    }
}
