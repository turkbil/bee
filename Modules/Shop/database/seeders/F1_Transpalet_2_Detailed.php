<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F1_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'F1')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (F1)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section class="intro">
  <h2>İXTİF F1: Dağıtım döngüsünde net tasarruf, sahada gerçek dayanıklılık</h2>
  <p><strong>Depo vardiyası başlıyor.</strong> İlk palet rampa başına yanaşırken, F1 kompakt gövdesi ve dengeli ağırlık dağılımıyla operatörün kol hareketini anında torka çevirir. Platform tabanlı F mimarisi, filoda aynı kumanda kolu ve ortak bakım parçaları ile yönetimi kolaylaştırır. Entegre 24V/10A şarj cihazı ve 24V/65Ah AGM akü, fişe tak & şarj et pratikliği sunar; gün içinde dağınık şarjlarla 5–6 saatlik gerçek çalışma süresi yakalanır. Standart kasalama ile tek kolide 3 ünite sevkiyat, 40’ konteynerde 132 üniteye kadar optimizasyon sağlayarak toplam nakliye maliyetini düşürür.</p>
</section>
<section class="technical">
  <h3>Teknik güç ve ölçüler</h3>
  <p>F1, 1500 kg kapasiteli yaya kumandalı bir akülü transpalettir. 600 mm yük merkezinde çalışır; dönüş yarıçapı 1426 mm seviyesindedir. Poliüretan tahrik tekeri (210×70 mm) ve tandem yük tekerleri (Φ80×60 / Φ74×88) standarttır; opsiyonel destek tekerleri (Φ74×30) ilave stabilite sağlar. 1604 mm toplam uzunluk ve 695/620 mm gövde genişliği dar koridorlarda çeviklik sunar; 55/150/1150 mm çatal kesiti ve 685/560 mm çatallar arası ölçülerle yaygın EUR paletlerini sorunsuz kavrar. Yüksekliği 105 mm kaldırma, 82 mm düşük fork seviyesi ve 25 mm yer yüksekliği ile rampa eşiklerinde kontrollü hareket sağlanır. 4.0/4.5 km/s yürüyüş hızı, 0.020/0.026 m/s kaldırma ve 0.069/0.055 m/s indirme hızları akıcı akış yaratır. 0.75 kW DC sürüş motoru ve 0.5 kW kaldırma motoru elektromanyetik fren ile eşleşir; 5%/16% eğim kabiliyeti tipik depo rampaları için yeterlidir.</p>
</section>
<section class="closing">
  <h3>Sonuç: Basit, verimli, hesaplı</h3>
  <p>F1; endüstriyel dayanıklılığı, ortak parça yönetimi ve sevkiyatta 3 ünite/kolİ avantajıyla toplam sahip olma maliyetini aşağı çeker. Bakım erişimi sade, kullanımı hızlıdır. Uygulamanız için doğru çatalları ve teker setini birlikte seçelim: <strong>0216 755 3 555</strong></p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 65Ah AGM, entegre şarj 24V/10A'],
                ['icon' => 'gauge', 'label' => 'Yürüyüş Hızı', 'value' => '4.0 / 4.5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1426 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'layer-group', 'title' => 'Platform F mimarisi', 'description' => 'Ortak parçalar ve tek kumanda kolları ile kolay filo bakımı'],
                ['icon' => 'battery-full', 'title' => 'AGM enerji', 'description' => '24V/65Ah kapasite ile gün içi 5–6 saat çalışma'],
                ['icon' => 'plug', 'title' => 'Entegre şarj', 'description' => '24V/10A dahili şarj; harici cihaza gerek yok'],
                ['icon' => 'shipping-fast', 'title' => '3 ünite/kolİ', 'description' => 'Sevkiyatta kutu başına üç ürün ile navlun tasarrufu'],
                ['icon' => 'shield-alt', 'title' => 'Endüstriyel dayanıklılık', 'description' => 'Gövde ve sürüş takımı zorlu alanlara uygundur'],
                ['icon' => 'cog', 'title' => 'Kanıtlanmış tahrik', 'description' => 'Stabil sürüş için pazarca doğrulanmış ünite']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Depo içi yükleme/boşaltma alanlarında kısa mesafe palet taşıma'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde vardiya içi besleme ve çapraz sevkiyat'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde dar koridor reyon arası transfer'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa yaklaşımı'],
                ['icon' => 'industry', 'text' => 'Ağır sanayi alanlarında zemin geçişlerinde kontrollü hareket'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında kapalı alanda güvenli manevra'],
                ['icon' => 'snowflake', 'text' => 'Gıda zincirinde soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas koli paletlerinin taşınması']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Piyasada kanıtlanmış tahrik ve elektromanyetik fren ile güvenilir performans'],
                ['icon' => 'battery-full', 'text' => '24V/65Ah AGM ve entegre şarj ile fişe tak & çalıştır işletme kolaylığı'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 1426 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'layer-group', 'text' => 'Platform tabanlı tasarım sayesinde stok ve servis parçası ortaklığı'],
                ['icon' => 'shield-alt', 'text' => 'Endüstriyel şasi, PU teker seti ve düşük gürültü seviyesi'],
                ['icon' => 'shipping-fast', 'text' => '3 ünite/kolİ sevkiyatla konteyner başına daha fazla ürün']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşenler'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım, Bahçe ve Seracılık'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay fabrika garantisi altındadır. AGM batarya modülleri satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim kaynaklı arızaları kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı 24V/10A', 'description' => 'Cihaz üzerinde dahili şarj; harici ünite ihtiyacı olmadan kolay şarj.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Destek Teker Seti', 'description' => 'Ek stabilite için opsiyonel Φ74×30 mm destek tekerleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Tekli Yük Tekerleri', 'description' => 'Tandem yerine tekli yük teker konfigürasyonu seçeneği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'AGM Batarya Seti 24V/65Ah', 'description' => 'Yedek batarya modülleri ile vardiya sürekliliği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'F1 günde kaç saat aralıksız çalışmayı destekler?', 'answer' => 'AGM 24V/65Ah batarya ile tipik koşullarda 5–6 saat gerçek çalışma elde edilir; kısmi şarjlarla kullanım süresi dağıtım döngüsüne yayılabilir.'],
                ['question' => 'Dar koridorlarda manevra kabiliyeti hangi ölçüde?', 'answer' => '1426 mm dönüş yarıçapı ve 1604 mm toplam uzunluk sayesinde 800×1200 palet koridoru 2244 mm değerinde verimli dönüş sağlar.'],
                ['question' => 'Standart çatal ölçüleri ve palet uyumu nedir?', 'answer' => '55/150/1150 mm çatal kesiti ve 685/560 mm çatallar arası ile yaygın EUR/UK palet tiplerine uygun kavrama ve denge sunar.'],
                ['question' => 'Hız ve kaldırma/indirme performansı nasıldır?', 'answer' => 'Yürüyüş hızı 4.0/4.5 km/s; kaldırma 0.020/0.026 m/s ve indirme 0.069/0.055 m/s değerleriyle akıcı operasyon sağlar.'],
                ['question' => 'Rampa ve eğimlerde performansı ne seviyede?', 'answer' => 'Yüklü/yüksüz %5/%16 eğim kabiliyeti, yükleme alanı rampaları ve kısa eğimli geçişler için yeterli performans sunar.'],
                ['question' => 'Hangi fren ve sürüş kontrol sistemi kullanılıyor?', 'answer' => 'Elektromanyetik servis freni ve DC sürüş kontrolü; 0.75 kW motor ile kararlı hızlanma sağlar.'],
                ['question' => 'Gövde ve teker materyali dayanımı nasıl etkiler?', 'answer' => 'Poliüretan tahrik ve tandem yük tekerleri titreşimi azaltır, zeminde sessiz ve düzgün ilerleme sağlar.'],
                ['question' => 'Bakım erişimi ve parça yönetiminde avantaj nedir?', 'answer' => 'Platform F mimarisi, ortak kumanda kolu ve modüler bileşenlerle parça stoklamayı ve servisi kolaylaştırır.'],
                ['question' => 'Sevkiyatta maliyet avantajı nasıl sağlanır?', 'answer' => 'Koli başına 3 ünite ve 40’ konteynerde 132 üniteye kadar yükleme ile navlun ve dağıtım maliyetleri düşürülür.'],
                ['question' => 'Gürültü seviyesi ve ergonomi kullanıcıyı nasıl etkiler?', 'answer' => '<74 dB(A) gürültü düzeyi ve dengeli kol geometri siyle uzun vardiyalarda yorgunluk azaltılır.'],
                ['question' => 'Opsiyonel destek tekerleri ve tekli yük tekeri ne sağlar?', 'answer' => 'Zemin koşullarına göre stabilite ve dönüş davranışı iyileşir; dar alanlarda istenen sürüş tepkisi yakalanır.'],
                ['question' => 'Garanti ve satış sonrası desteği nasıl alabilirim?', 'answer' => 'Makine 12 ay, AGM batarya 24 ay garanti kapsamındadır. Satış ve servis için İXTİF ile iletişim: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed içerik güncellendi: F1');
    }
}
