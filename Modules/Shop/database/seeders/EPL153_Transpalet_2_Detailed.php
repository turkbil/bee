<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPL153_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPL153')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EPL153');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF EPL153: Kompakt gücün verimli lojistik çözümü</h2><p>İXTİF EPL153, depo içi kısa mesafe taşımalarda hız, güvenlik ve kullanım kolaylığını bir araya getirir. 1500 kg nominal kapasite ve 600 mm yük merkeziyle standart EUR paletleri rahatlıkla taşır. Kompakt şasi (l2=400 mm) ve sadece 120 kg servis ağırlığı sayesinde rampalara yaklaşma ve dar koridor dönüşlerinde operatörü yormadan çalışır. Çıkarılabilir 24V 20Ah Li-Ion batarya gün içi hızlı değişim ve harici şarj ile vardiya sürekliliği sağlar.</p></section><section><h3>Teknik</h3><p>Sürüş DC kontrol yapısı ve 0.75 kW tahrik motoru ile stabildir; 0.7 kW kaldırma motoru kontrollü kaldırma sunar. 4.5/5.0 km/s hız değerleri, 1330 mm dönüş yarıçapı ve 2050–2145 mm koridor gereksinimleri dar alan performansını belirler. Poliüretan tekerlekler düşük gürültü ve zemin koruması sağlar. 50×150×1150 mm çatal ölçüsü standarttır; 560 veya 685 mm çatal aralığı seçenekleri farklı palet tipleriyle uyumu güçlendirir. Yüklü/yüksüz tırmanma kabiliyeti %6/%16 olup elektromanyetik servis freni eğimli alanlarda güvenli durmayı destekler. İndirme işlemi manuel kontrollüdür; hassas yüklerde zarar riskini düşürür. Batarya 24V/20Ah modüldür ve yaklaşık 7 kg’dır; harici 24V-10A şarj cihazı standarttır.</p></section><section><h3>Sonuç</h3><p>Hafif ve orta yoğunluklu operasyonlar için tasarlanan EPL153, bakım kolaylığı, esnek varyant seçenekleri ve güvenilir performansı ile toplam sahip olma maliyetini düşürür. Çözüme geçmek için 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 20Ah Li-Ion, çıkarılabilir'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 / 5.0 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1330 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Çıkarılabilir Li-Ion modül', 'description' => '24V 20Ah modül harici şarj ile vardiya değişimlerini hızlandırır.'],
                ['icon' => 'bolt', 'title' => 'Verimli sürüş sistemi', 'description' => 'DC kontrol ile 0.75 kW motor akıcı hızlanma ve frenleme sağlar.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt şasi', 'description' => '400 mm yük ucuna kadar uzunluk dar koridorlarda çeviklik sunar.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli frenleme', 'description' => 'Elektromanyetik fren yokuşlarda güvenli durmayı destekler.'],
                ['icon' => 'cart-shopping', 'title' => 'Çatal seçenekleri', 'description' => '900–1500 mm arası çatal uzunlukları ve 560/685 mm genişlik.'],
                ['icon' => 'cog', 'title' => 'Basit bakım', 'description' => 'Piyasada kanıtlanmış bileşenlerle düşük bakım ihtiyacı.']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret depo içi kısa mesafe palet transferleri'],
                ['icon' => 'store', 'text' => 'Perakende arka depo raf besleme ve sipariş hazırlama'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında çapraz yükleme alanı taşıma'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında giriş-çıkış rampası yaklaşımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında hassas ürün taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yan sanayi parça akışı ve hat besleme'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP hareketleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim ürünlerinde paletli sevkiyat hazırlığı']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'DC tahrik ile dengeli hızlanma ve düşük enerji tüketimi'],
                ['icon' => 'battery-full', 'text' => 'Çıkarılabilir 24V 20Ah batarya ile minimum duruş süresi'],
                ['icon' => 'arrows-alt', 'text' => '400 mm l2 ile dar koridorlarda üstün manevra'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve kontrollü indirme ile güvenlik'],
                ['icon' => 'cog', 'text' => 'Basit tasarım sayesinde hızlı servis ve düşük bakım maliyeti']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL Lojistik'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel Üretim'],
                ['icon' => 'building', 'text' => 'İnşaat Malzemeleri Deposu'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Toptan Tedarik Depoları'],
                ['icon' => 'warehouse', 'text' => 'Bayi Lojistik Merkezleri'],
                ['icon' => 'box-open', 'text' => 'Paketleme ve Koli İşleme'],
                ['icon' => 'industry', 'text' => 'Makine Yedek Parça Deposu'],
                ['icon' => 'flask', 'text' => 'Boya ve Kimyasal Lojistik'],
                ['icon' => 'pills', 'text' => 'Kozmetik Depolama'],
                ['icon' => 'car', 'text' => 'Lastik ve Jant Depoları'],
                ['icon' => 'building', 'text' => 'Belediye Depoları']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Harici Şarj Cihazı 24V-10A', 'description' => 'Standart harici şarj cihazı ile bataryayı makineden bağımsız şarj edin.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Tandem Yük Teker Seti', 'description' => 'Çift yük tekeri ile eşit ağırlık dağılımı ve zemin koruması.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'İz Yapmayan PU Tahrik Teker', 'description' => 'Zemin izini azaltan bileşik ile sessiz ve temiz çalışma.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yedek Li-Ion Batarya 24V/20Ah', 'description' => 'Vardiya değişimlerinde kesintisiz operasyon için ikinci batarya.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'EPL153 hangi yük kapasitesinde en verimli çalışır?', 'answer' => '600 mm yük merkezinde 1500 kg’a kadar nominal performans sunar. Kısa mesafeli depo içi taşımalarda verimlilik ve güvenlik birlikte sağlanır.'],
                ['question' => 'Kompakt şasi dar koridorlarda ne sağlar?', 'answer' => '400 mm l2 ve 1330 mm dönüş yarıçapı ile 2050–2145 mm koridorlarda rahat manevra ve palet pozisyonlama imkânı verir.'],
                ['question' => 'Sürüş sistemi ve motor gücü nedir?', 'answer' => 'DC kontrol ve 0.75 kW sürüş motoru kullanır. Dengeli hızlanma ve düşük enerji tüketimi hedeflenmiştir.'],
                ['question' => 'Kaldırma mekanizması nasıl çalışır?', 'answer' => 'Kaldırma elektriklidir, indirme manuel kontrollüdür. Hassas yüklerde ani düşüşü önlemeye yardımcı olur.'],
                ['question' => 'Standart çatal ölçüsü ve alternatifler nelerdir?', 'answer' => '50×150×1150 mm standarttır. 900/1000/1220/1500 mm uzunluk ve 560/685 mm genişlik seçenekleri mevcuttur.'],
                ['question' => 'Tırmanma kabiliyeti ve fren türü?', 'answer' => 'Yüklü %6, yüksüz %16 tırmanır. Elektromanyetik servis freni ile güvenli durma sağlar.'],
                ['question' => 'Hız değerleri yüklü ve yüksüz durumda nasıldır?', 'answer' => 'Yüklü 4.5 km/s, yüksüz 5.0 km/s’dir. Operasyon verimliliğini destekler.'],
                ['question' => 'Tekerlek malzemesi ve etkisi nedir?', 'answer' => 'Poliüretan tekerlekler zemin koruması ve düşük gürültü seviyesi sağlar. Depo içi kullanım için uygundur.'],
                ['question' => 'Batarya özellikleri ve değişim pratikliği nasıldır?', 'answer' => '24V/20Ah Li-Ion modül yaklaşık 7 kg’dır. Tak-çıkar yapı sayesinde hızlıca değiştirilebilir ve harici şarj edilebilir.'],
                ['question' => 'Bakım ve yedek parça erişimi kolay mı?', 'answer' => 'Basit tasarım ve yaygın bileşen kullanımı bakım sürelerini kısaltır ve servis işlemlerini kolaylaştırır.'],
                ['question' => 'Opsiyonel teker ve aksesuarlar nelerdir?', 'answer' => 'Tandem yük tekeri, iz yapmayan tahrik tekeri ve ek batarya gibi opsiyonlar farklı zemin ve vardiya senaryolarını destekler.'],
                ['question' => 'Garanti ve servis desteği nasıl sağlanır?', 'answer' => 'Makine 12 ay, Li-Ion batarya modülleri 24 ay garantilidir. İXTİF Türkiye genelinde satış, servis, kiralama ve yedek parça sağlar. 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: EPL153');
    }
}
