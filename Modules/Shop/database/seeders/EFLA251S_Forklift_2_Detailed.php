<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFLA251S_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFLA251S')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFLA251S');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF EFLA251S: İç Mekânda Hız, Sessizlik ve Çeviklik</h2><p>Gün, kapıların açılışıyla birlikte mal kabulde başlar; sevkiyat planı dolup taşarken dar koridorlar en yoğun saatlerine hazırlanır. İşte bu ritimde, İXTİF EFLA251S kompakt gövdesi, yastık lastikleri ve çevik dönüş yarıçapıyla devreye girer. 1485 mm dingil açıklığı ve 1092 mm toplam genişlik, operatöre koridor kenarlarına sürtmeden ilerleme özgürlüğü verir. Sürüşte 17 km/s hız, kaldırmada 0.61 m/s ve indirmede 0.5 m/s değerleri tempoyu belirlerken, operatör süspansiyon koltuk ve geniş görüş alanı sayesinde gün boyu konforla çalışır.</p></section><section><h3>Teknik Güç ve Verimlilik</h3><p>EFLA251S, 2500 kg nominal kapasiteyi 500 mm yük merkeziyle sunar; kompakt yapıya rağmen taşıyıcı sistem ve direk tasarımı yüksek rijitlik sağlar. 80V Li-ion platformu, 230Ah standart kapasiteyle hafiflik ve enerji yoğunluğunu dengeler; yoğun vardiyalarda ise 460Ah opsiyon ile uzun vardiya sürelerine cevap verir. AC sürücü kontrolü ve 15 kW tahrik motoru, kayıpsız ivmelenme ve stabil hız kontrolü sunarken 26 kW kaldırma motoru, optimize hidrolik valf ve geniş kesitli yağ hortumu sayesinde daha yüksek debiyle hızlı kaldırma/indirme sağlar. 4800 mm standart kaldırma yüksekliği ve 5838 mm açık direk yüksekliği değerleri, 6.55 metre raf seviyelerine kadar yüksek kullanım verimini destekler; dört kademeli direk seçeneğinde 7134 mm tepe noktasıyla bile 1000 kg residual kapasite korunur. 1990 mm dönüş yarıçapı ve 3664/3864 mm koridor gereksinimi, karma depo yerleşimlerinde bile kesintisiz akışı mümkün kılar.</p><p>Bakım tarafında cıvatasız ön zemin ve sökülebilir paspas ile ana bileşenlere erişim dakikalara iner; cıvatalı üst koruma (OHG) tasarımı, servis veya parça değişimlerinde sök-tak kolaylığı sağlar. Motor kontrol cihazının ağırlık karşısında konumlandırılması doğal ısı regülasyonu yaratır; ek fan gereksinimi ortadan kalktığı için gürültü azalır ve enerji tüketimi düşer. Güvenlikte standart hız kontrol sistemi virajlarda otomatik sınırlama uygularken, opsiyonel mast buffer direk tepesine yaklaşırken kaldırma hızını yumuşatıp sarsıntısız duruş sağlar. Kaymaz taban paspası ise ıslak zeminlerde ekstra tutuş sunar.</p></section><section><h3>Sonuç ve İletişim</h3><p>EFLA251S, iç mekân operasyonlarında kompakt boyut, yüksek enerji verimliliği ve operatör konforunu bir araya getirir. Dar koridorlarda raf besleme, yoğun istasyon geçişleri ve yüksek raf seviyesi uygulamalarında çeviklik ve hız kazandırır. Proje bazlı konfigürasyonlar, mast seçenekleri ve Li-ion kapasite alternatifleriyle farklı vardiya senaryolarına esnek biçimde uyum sağlar. Detaylı teknik değerlendirme ve teklif için 0216 755 3 555 numaralı hattan bize ulaşın.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2500 kg (c=500 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 230Ah (opsiyon 460Ah)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '17 / 17 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1990 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'compress', 'title' => 'Kompakt Şasi', 'description' => '1485 mm dingil mesafesi ve 1092 mm genişlik ile çeviklik'],
                ['icon' => 'battery-full', 'title' => 'Li-Ion Platform', 'description' => '80V sistemde 230Ah standart, 460Ah opsiyon kapasite'],
                ['icon' => 'bolt', 'title' => 'Hızlı Hidrolik', 'description' => 'Optimize valf ve geniş hortumla hızlı kaldırma'],
                ['icon' => 'weight-hanging', 'title' => 'Yüksek Residual', 'description' => '6.55 m’de 1000 kg taşıma ile yüksek raf kullanımı'],
                ['icon' => 'shield-alt', 'title' => 'Aktif Güvenlik', 'description' => 'Virajlarda hız kontrolü, opsiyonel mast yastıklama'],
                ['icon' => 'cog', 'title' => 'Kolay Servis', 'description' => 'Sökülebilir paspas ve erişilebilir OHG yapısı']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Dar koridorlu depolarda raf besleme ve toplama operasyonları'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment alanlarında hızlı palet akışı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde inbound–outbound geçişleri'],
                ['icon' => 'industry', 'text' => 'İç üretim alanlarında WIP taşıma ve hat besleme'],
                ['icon' => 'snowflake', 'text' => 'İyi zemine sahip soğuk oda girişlerinde sessiz lojistik'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında düşük emisyonlu iç mekân taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa yaklaşımı'],
                ['icon' => 'flask', 'text' => 'Kimya depolama alanlarında kokusuz, gazsız çalışma']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'arrows-alt', 'text' => '1990 mm dönüş yarıçapı ile dar alanlarda üstün manevra'],
                ['icon' => 'battery-full', 'text' => 'Li-Ion teknolojisiyle sıfır emisyon ve hızlı fırsat şarjı'],
                ['icon' => 'bolt', 'text' => '0.61/0.64 m/s kaldırma hızıyla yüksek vardiya verimi'],
                ['icon' => 'cog', 'text' => 'Cıvatasız zemin ve erişilebilir OHG ile düşük bakım süresi'],
                ['icon' => 'shield-alt', 'text' => 'Standart hız kontrolü ve opsiyonel mast buffer ile güven'],
                ['icon' => 'star', 'text' => '68 dB(A) düşük gürültü ile konforlu iç mekân çalışma']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimya ve Tehlikesiz Kimyasallar'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım Ekipmanları Lojistiği'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alma tarihinden itibaren 12 ay üretim hatalarına karşı garanti kapsamındadır. Li-Ion batarya modülleri ise satın alma tarihinden itibaren 24 ay boyunca garanti altındadır. Kapsam, normal kullanım koşullarını içerir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '80V Endüstriyel Şarj Cihazı', 'description' => 'Yüksek verimli endüstriyel şarj ünitesi (trifaze).', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => '80V / 230Ah Li-Ion Batarya', 'description' => 'Standart kapasite modül yapısı.', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => '80V / 460Ah Li-Ion Batarya', 'description' => 'Uzun vardiya için yüksek kapasite seçenek.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Cushion Lastik Seti', 'description' => 'İç mekân beton zeminler için düşük gürültü lastikler.', 'is_standard' => true, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Dar koridorlarda minimum dönüş çapı kaç milimetreye iniyor?', 'answer' => 'Makine 1990 mm dönüş yarıçapına sahiptir; dar koridorlarda palet yerleştirme ve çıkarmada yüksek çeviklik sağlar.'],
                ['question' => 'Standart kaldırma ve indirme hızları operasyonu nasıl etkiler?', 'answer' => 'Optimize hidrolik ile 0.61 m/s kaldırma ve 0.5 m/s indirme hızları, vardiya başına daha fazla çevrim ve stabilite sunar.'],
                ['question' => '6.55 metre raf seviyesinde ne kadar yük taşıyabiliyor?', 'answer' => 'Dört kademeli direk seçeneğiyle 6.55 m yükseklikte 1000 kg residual kapasite korunur; yüksek raf kullanımını güvenli kılar.'],
                ['question' => 'Hangi batarya seçenekleri mevcut ve hangi senaryoya uygun?', 'answer' => '80V/230Ah standarttır; tek vardiya ve orta yoğunlukta idealdir. 80V/460Ah opsiyon çok vardiyalı, yüksek yoğunluklu operasyonlar için önerilir.'],
                ['question' => 'Sürüşte hız sınırlama sistemi nasıl çalışır?', 'answer' => 'Viraj yaklaşımında hız kontrol sistemi devreye girerek güvenli dönüş sağlar, devrilme riskini azaltır ve yük stabilitesini artırır.'],
                ['question' => 'Gürültü seviyesi operatör konforunu nasıl etkiler?', 'answer' => '68 dB(A) seviyesindeki düşük gürültü ve motor kontrol cihazının pasif ısı yönetimi, uzun vardiyalarda konforu artırır.'],
                ['question' => 'Bakım erişimi açısından sunulan kolaylıklar nelerdir?', 'answer' => 'Cıvatasız ön zemin, sökülebilir paspas ve cıvatalı OHG hızlı erişim sağlar; planlı bakım sürelerini kısaltır.'],
                ['question' => 'Zemin tipi olarak hangi yüzeylerde en iyi performans alınır?', 'answer' => 'Cushion lastikler düz, kuru ve düzgün beton zeminlerde en iyi tutuşu ve manevrayı sunar; dar alanda avantaj sağlar.'],
                ['question' => 'Standart çatal ölçüsü hangisidir ve hangi paletlerle uyumludur?', 'answer' => '40×122×1070 mm çatal, yaygın endüstri paletleri ile uyumludur; yük merkezi 500 mm’dir.'],
                ['question' => 'Koridor genişliği gereksinimi nedir, planlamada neye dikkat edilmeli?', 'answer' => 'Palet yönüne göre 3664 mm (1000×1200 enine) ve 3864 mm (800×1200 boyuna) koridor ihtiyacı hesaplanmalıdır.'],
                ['question' => 'İç mekân güvenliği için hangi ek özellikler önerilir?', 'answer' => 'Mast buffer ve kelepçe ataşmanı için güvenlik kilidi gibi opsiyonlar, yük stabilitesi ve kullanıcı güvenliğini artırır.'],
                ['question' => 'Satış ve teknik destek için kiminle iletişime geçebilirim?', 'answer' => 'Teklif, demo ve servis için İXTİF satış ekibiyle iletişime geçin: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed içerik güncellendi: EFLA251S');
    }
}
