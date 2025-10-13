<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KSi201_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'KSi201')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: KSi201');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İki Palet, Tek Hamle: KSi201 ile Uzun Mesafede Hız ve Konfor</h2><p>Gün boyu yoğun yük akışının olduğu depolarda gerçek verimlilik; hız, güvenlik ve operatör konforunun aynı gövdede buluşmasıyla mümkün olur. İXTİF KSi201, sürücülü platformu, çelik koruma gövdesi ve ayarlanabilir tiller başı ile operatör için güvenli bir çalışma alanı oluşturur. Çift kaldırma mimarisi sayesinde aynı anda iki paleti kaldırıp hareket ettirebilir; destek kolları 2000 kg’a kadar yükleri taşırken direkli kaldırmada 1000 kg yükle istifleme operasyonlarını güvenle sürdürür. 24V/205Ah Li‑Ion batarya ve entegre 24V/30A şarj cihazı, vardiya aralarında fırsat şarjı ile operasyonu ayakta tutar; opsiyonel 24V/100A harici şarj cihazı ise yaklaşık 2 saatlik hızlı şarj olanağıyla yoğun dönemlerde kapasiteyi artırır. Kompakt şasi ve 2236 mm dönüş yarıçapı dar koridorlarda çevik manevralar sağlar; 8.5/10 km/s seyir hızı ve 8/16% tırmanma kabiliyeti ise rampalı ve uzun mesafeli hatlarda akışı hızlandırır.</p></section><section><h3>Teknik Altyapı ve Performans</h3><p>KSi201, AC sürüş kontrolüyle 2.5 kW tahrik motoru ve 3.0 kW kaldırma motorunu verimli şekilde yönetir. Elektromanyetik servis freni ve elektronik direksiyon, hızlanma ve duruşlarda tutarlı kontrol sunar. 1316 mm düşük direk yüksekliği (1600 mm mast), 2112 mm uzatılmış yükseklik ve 100 mm serbest kaldırma değerleri kapı ve raf girişlerine uyumu artırır. 55×185×1150 mm çatal ölçüleri ve 570 mm çatallar arası genişlik, EUR palet uyumluluğu sağlar. 16 mm şasi altı yerden yükseklik ve 3026/2920 mm koridor genişliği değerleri (1000×1200 ve 800×1200 paletler için) dar alanlarda planlama yapmayı kolaylaştırır. 920 kg servis ağırlığı ile denge korunurken, ön/arka iz genişlikleri (514/385 mm) ve 1x,2/4 teker konfigürasyonu, poliüretan tekerleklerle sessiz ve titreşimsiz sürüş sunar. Yük altında 0.18 m/s, yüksüz 0.23 m/s kaldırma hızları; 0.36/0.18 m/s indirme hızlarıyla birlikte istifleme çevrimlerini hızlandırır.</p><p>Enerji tarafında 24V/205Ah Li‑Ion batarya yaklaşık 70 kg ağırlığıyla kompakt bir pakette uzun ömür ve bakım gerektirmeyen işletme sunar. Entegre 24V/30A şarj cihazı standarttır; fişe tak-şarj et yaklaşımı ile harici altyapı ihtiyacını azaltır. Opsiyonel 24V/100A harici şarj cihazı, yoğun vardiyalarda hızlı toparlanma sağlar. 8% yüklü ve 16% yüksüz tırmanma kabiliyeti rampalı yükleme alanlarında çift katlı taşımanın avantajını artırır. 74 dB(A) operatör kulak seviyesi ile gürültü kontrollüdür.</p></section><section><h3>Sonuç</h3><p>İXTİF KSi201, iki paleti tek seferde hareket ettirerek hat besleme hızını yükseltir ve operatör ergonomisini merkeze alır. Dar alanda çeviklik, Li‑Ion enerji ile yüksek up‑time ve markette kanıtlanmış komponentler ile düşük bakım maliyeti; KSi201’i yoğun depoların güvenilir çözümü yapar. Teknik detaylar ve teklif için 0216 755 3 555.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (destek kolu), 1000 kg (direk)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 205Ah Li‑Ion (entegr. 24V/30A şarj)'],
                ['icon' => 'star', 'label' => 'Hız', 'value' => '8.5 / 10 km/s (yüklü / boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş Yarıçapı', 'value' => '2236 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li‑Ion Enerji Sistemi', 'description' => 'Bakım gerektirmez, fırsat şarjı ile yüksek çalışma süresi sağlar.'],
                ['icon' => 'bolt', 'title' => 'Güçlü Tahrik', 'description' => '2.5 kW AC sürüş motoru ve 3.0 kW kaldırma motoru.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Manevra', 'description' => '2236 mm dönüş yarıçapı dar koridorlarda çeviklik sağlar.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Platform', 'description' => 'Çelik koruma kabuğu ve elektromanyetik fren.'],
                ['icon' => 'star', 'title' => 'Yüksek Seyir Hızı', 'description' => 'Uzun mesafe operasyonlarında 10 km/s hıza çıkar.'],
                ['icon' => 'cog', 'title' => 'Kanıtlanmış Parçalar', 'description' => 'Düşük arıza oranı ve kolay parça yönetimi.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'İki paleti aynı anda hareket ettirerek hat besleme hızını artırma'],
                ['icon' => 'warehouse', 'text' => 'Yoğun 3PL depolarında uzun mesafeli iç taşıma'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezinde vardiya içi sipariş toplama destekleri'],
                ['icon' => 'snowflake', 'text' => 'Soğutmalı alan giriş-çıkışlarında kontrollü taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas kolilerde sarsıntısız sürüş'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça rampalarında çift katlı taşıma'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP hareketi'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli ve temiz enerji ile operasyon']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Çift katlı taşıma ile çevrim süresini kısaltan yüksek verimlilik'],
                ['icon' => 'battery-full', 'text' => '24V/205Ah Li‑Ion ve entegre şarj ile kesintisiz çalışma'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 2236 mm dönüş yarıçapı ile dar alan uyumu'],
                ['icon' => 'shield-alt', 'text' => 'Çelik koruma gövdesi ve elektromanyetik fren ile güvenlik'],
                ['icon' => 'star', 'text' => '10 km/s hıza varan sürüş ile uzun mesafede hız'],
                ['icon' => 'cog', 'text' => 'Pazarda kanıtlanmış komponentlerle düşük bakım maliyeti']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşenler'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Endüstriyel Üretim'],
                ['icon' => 'briefcase', 'text' => 'Ofis Malzemeleri Dağıtımı'],
                ['icon' => 'building', 'text' => 'Yapı Market & DIY Lojistiği'],
                ['icon' => 'box-open', 'text' => 'Kargo & Kurye Aktarma'],
                ['icon' => 'warehouse', 'text' => 'Havaalanı Kargo Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'Zincir Mağaza Geri İadeleri'],
                ['icon' => 'star', 'text' => 'Promosyon & Mevsimsel Lojistik'],
                ['icon' => 'award', 'text' => 'Etkinlik & Fuar Lojistiği'],
                ['icon' => 'box-open', 'text' => 'Ambalaj & Matbaa Sevkiyatları'],
                ['icon' => 'industry', 'text' => 'Metal/Plastik Parça Depoları'],
                ['icon' => 'building', 'text' => 'Belediye & Kamu Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li‑Ion batarya modülleri satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim ve işçilik hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V/30A Şarj Cihazı', 'description' => 'Standart dahili şarj cihazı ile fişe tak-şarj et kolaylığı.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => '24V/100A Harici Hızlı Şarj', 'description' => 'Opsiyonel harici şarj ile yaklaşık 2 saatte hızlı dolum.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'PU Teker Seti', 'description' => 'Sessiz ve titreşimsiz sürüş için poliüretan tekerlek konfigürasyonu.', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Yedek Li‑Ion Batarya Modülü', 'description' => 'Yoğun vardiyalar için ikinci 24V/205Ah paket.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'İki paleti aynı anda taşırken kaldırma limitleri nelerdir?', 'answer' => 'Destek kolu kaldırmasında toplam 2000 kg, direk ile yük kaldırmada 1000 kg kapasite sağlanır; palet dağılımı ve zemin durumuna dikkat edilmelidir.'],
                ['question' => 'Dar koridorlarda minimum dönüş alanı ne kadar gerektirir?', 'answer' => '2236 mm dönüş yarıçapı ve 2456 mm toplam uzunlukla 800×1200 palet için tipik koridor genişliği 2920 mm seviyesindedir.'],
                ['question' => 'Rampalarda performansı nasıl, maksimum eğim değerleri nedir?', 'answer' => 'Yüklü %8, yüksüz %16 tırmanma kabiliyeti ile yükleme rampaları ve kısa eğimli geçişler güvenle aşılır.'],
                ['question' => 'Enerji sistemi bakım gerektirir mi ve şarj stratejisi nasıl olmalı?', 'answer' => 'Li‑Ion batarya bakım gerektirmez; fırsat şarjı için entegre 24V/30A cihazı kullanın, yoğun dönemlerde 24V/100A harici hızlı şarj opsiyonunu tercih edebilirsiniz.'],
                ['question' => 'Seyir ve kaldırma hızları operasyon verimine nasıl yansır?', 'answer' => '8.5/10 km/s seyir ve 0.18/0.23 m/s kaldırma hızları, çift katlı taşıma ile birleşince çevrim sürelerini anlamlı ölçüde düşürür.'],
                ['question' => 'Operatör konforu için hangi ergonomik unsurlar bulunuyor?', 'answer' => 'Ayarlanabilir tiller, süspansiyonlu platform, yastıklı sırt dayanağı ve çelik kabuk, uzun mesafe çalışmada yorgunluğu azaltır.'],
                ['question' => 'Güvenlik sistemleri neler ve fren tipi nedir?', 'answer' => 'Elektromanyetik servis freni, otomatik viraj hızı düşürme ve koruyucu çelik yapı standart güvenlik unsurlarıdır.'],
                ['question' => 'Bakım ve yedek parça yönetiminde hangi avantajları sunar?', 'answer' => 'Pazarda kanıtlanmış komponentler ve olgun tahrik ünitesi ile parça stok optimizasyonu ve kolay servis sağlanır.'],
                ['question' => 'Hangi palet ve yük ölçüleri ile en uyumlu çalışır?', 'answer' => '55×185×1150 mm çatal ve 570 mm çatallar arası mesafe, EUR paletlerle en iyi uyumu sağlar.'],
                ['question' => 'Gürültü seviyesi ve iç mekân kullanımı için uygunluğu nasıldır?', 'answer' => '74 dB(A) sürücü kulak seviyesiyle iç mekânlarda konforlu ve düşük gürültülü çalışma sunar.'],
                ['question' => 'Opsiyonel ekipmanlar ve hızlı şarj seçeneği hakkında bilgi verir misiniz?', 'answer' => '24V/100A harici hızlı şarj opsiyonu ve yedek Li‑Ion modül ile yoğun vardiyalarda kesintisiz operasyon planlanabilir.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek noktalarına nasıl ulaşırım?', 'answer' => 'Makine 12 ay, Li‑Ion batarya 24 ay garantilidir. Satış, servis ve yedek parça için İXTİF çağrı merkezi: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: KSi201');
    }
}
