<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RSi201_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'RSi201')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: RSi201'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF RSi201: Çift Katlı İstifte Maksimum Akış</h2>
  <p>Depo içinde iki paleti tek seferde hareket ettirebilmek, sipariş toplama ve hat besleme akışlarını dramatik biçimde hızlandırır. RSi201 tam da bunu yapar: çift katlı istif mimarisi sayesinde eşzamanlı taşıma, 3 kW kaldırma motorunun sağladığı 0.18 m/sn kaldırma ve 0.36 m/sn indirme hızlarıyla birleşir. Süspansiyonlu platform operatör konforu sunarken, elektrikli direksiyon ve otomatik viraj yavaşlatma yorgunluğu azaltır. 24V/205Ah Li-ion batarya fırsat şarjına uygundur; entegre 24V/30A şarj cihazı günlük rutine esneklik katarken, opsiyonel 24V/100A harici şarj cihazı iki saatte tam doluma imkan tanır.</p>
</section>
<section>
  <h3>Teknik Yetkinlik ve Operasyonel Uyum</h3>
  <p>RSi201, 2000 kg kapasiteyi 600 mm yük merkezinde taşır ve 1600 mm kaldırma yüksekliği ile tipik depo raf yüksekliğini etkin biçimde karşılar. 1316 mm retracted direk yüksekliği ve 2112 mm açık direk yüksekliği, şeffaf mast kalkanıyla birlikte geniş görüş sağlar. 2120 mm toplam uzunluk, 734 mm genişlik ve 1920 mm dönüş yarıçapı; dar koridorlarda güvenli manevra ve raf arası çalışma kolaylığı sunar. 120 mm ilk kaldırma, rampalarda ve bozuk zeminlerde palet altından ekstra açıklık kazandırarak sürtünmeyi ve palet hasarlarını azaltır. Poliüretan tekerlekler sessiz ve titreşimi düşük bir sürüş sağlar; elektromanyetik servis freni yüksek yükte dahi tutarlı duruş performansı verir.</p>
  <p>Güç aktarım tarafında 2.5 kW S2-60dk sürüş motoru ve 3.0 kW S3-15% kaldırma motoru kullanılır. 8/8 km/sa seyir hızı, 8%/16% yüklü/boş tırmanma kabiliyetiyle bir araya geldiğinde, vardiya boyunca çevrim sürelerini aşağı çeker. 24V/205Ah Li-ion batarya modülü 70 kg ağırlığındadır ve hızlı ara şarjlar ile tek batarya üzerinde yüksek vardiya sürdürülebilirliği sağlar.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>İki paletli taşıma kabiliyeti, hassas oransal kaldırma, kompakt şasi ve verimli Li-ion enerji sistemi; RSi201’i yoğun operasyonlar için çok yönlü bir istif çözümü yapar. Teknik detay ve teklif için 0216 755 3 555.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (600 mm LC)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 205Ah Li-ion'],
                ['icon' => 'gauge', 'label' => 'Hız (yüklü/boş)', 'value' => '8 / 8 km/sa'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1920 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'layer-group', 'title' => 'Çift Katlı Taşıma', 'description' => 'Aynı anda iki palet ile tek seferde iki kat throughput.'],
                ['icon' => 'bolt', 'title' => 'Güçlü Kaldırma', 'description' => '3 kW motor ve oransal kaldırma ile hızlı ve hassas hareket.'],
                ['icon' => 'arrows-alt', 'title' => 'İlk Kaldırma', 'description' => '120 mm ekstra açıklıkla rampa ve engebede rahat geçiş.'],
                ['icon' => 'warehouse', 'title' => 'Kompakt Şasi', 'description' => 'Dar alanlarda güvenli manevra ve kolay kontrol.'],
                ['icon' => 'hand', 'title' => 'Elektrikli Direksiyon', 'description' => 'Daha az efor, uzun vardiyada düşük yorgunluk.'],
                ['icon' => 'battery-full', 'title' => 'Li-ion Güç', 'description' => 'Fırsat şarjı ve yüksek erişilebilirlik ile kesintisiz akış.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Yoğun sipariş toplama hatlarında iki paleti aynı anda besleme'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde çapraz sevkiyat ve hub konsolidasyonu'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde palet içi/arası transfer ve toplama beslemesi'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış noktalarında hızlı geçiş operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç depolarında hassas, düşük titreşimli ürün hareketi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında rampa yaklaşımı ve ara stok taşımaları'],
                ['icon' => 'tshirt', 'text' => 'Tekstil kolilerinde dar koridorlar arasında çevik dolaşım'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP paletlerinin çift katlı transferi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '0.18 m/sn kaldırma ve 0.36 m/sn indirme ile çevrim zamanı üstünlüğü'],
                ['icon' => 'battery-full', 'text' => '24V/205Ah Li-ion ve entegre 24V/30A şarj ile yüksek erişilebilirlik'],
                ['icon' => 'arrows-alt', 'text' => '120 mm ilk kaldırma ile rampalarda palet sürtünmesini azaltma'],
                ['icon' => 'shield-alt', 'text' => 'Şeffaf mast kalkanı ve elektromanyetik frenle güvenli kullanım'],
                ['icon' => 'warehouse', 'text' => 'Kompakt boyutlar ve 1920 mm dönüş yarıçapı ile dar alanda verim'],
                ['icon' => 'star', 'text' => 'Çift katlı konsept ile sipariş başına maliyeti aşağı çekme']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşenler'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Dayanıklı Tüketim'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri ve Yem']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre 24V/30A Şarj Cihazı', 'description' => 'Makine üzerinde yer alan şarj ile vardiya içinde esnek fırsat şarjı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'charging-station', 'name' => 'Harici 24V/100A Hızlı Şarj', 'description' => 'İki saate kadar tam dolum için yüksek akımlı harici şarj ünitesi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tandem Yük Teker Seti', 'description' => 'Ağır hizmet kullanımında daha pürüzsüz geçiş için tandem konfigürasyon.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yedek 24V/205Ah Li-ion Modül', 'description' => 'Kesintisiz vardiya akışı için değiştirilebilir ek batarya paketi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Çift katlı istif hangi senaryolarda en fazla verim sağlar?', 'answer' => 'İki paletin aynı anda taşındığı sipariş toplama ve hat besleme akışlarında çevrim sürelerini yarıya indirmek için idealdir; kapı başına throughput artar.'],
                ['question' => 'İlk kaldırma yüksekliği rampalarda ne kazandırır?', 'answer' => '120 mm ek yerden açıklık, palet zemine sürtmeden rampadan geçişi kolaylaştırır ve palet tabanı hasarlarını önemli ölçüde azaltır.'],
                ['question' => 'Operatör konforunu artıran platform özellikleri nelerdir?', 'answer' => 'Süspansiyonlu, kaymaz yüzeyli platform titreşimi sönümler; katlanır yapı dar alanlarda yaya moda geçişi hızlandırır, manevrayı kolaylaştırır.'],
                ['question' => 'Elektrikli direksiyon uzun vardiyada nasıl fayda sağlar?', 'answer' => 'Direksiyon torkunu düşürerek koldaki eforu azaltır; otomatik viraj yavaşlatma ile denge korunur, yorgunluk ve hata riski düşer.'],
                ['question' => 'Batarya fırsat şarjı için önerilen aralıklar nelerdir?', 'answer' => 'Vardiya içinde kısa molalarda %20-80 bandında şarj, Li-ion kimya ile çevrim ömrünü korur ve günlük erişilebilirliği en üst düzeye taşır.'],
                ['question' => 'Maksimum sürüş hızı ve emniyet nasıl dengelenir?', 'answer' => '8 km/sa hıza rağmen virajlarda otomatik yavaşlama ve elektromanyetik fren, stabilite ve duruş mesafesi açısından güvenli sınırlar sağlar.'],
                ['question' => 'Hangi tekerlek konfigürasyonu önerilir?', 'answer' => 'Standart poliüretan sessiz ve iz bırakmaz; eşik/bozuk zeminde tandem yük tekeri, geçişleri yumuşatır ve yük salınımını azaltır.'],
                ['question' => 'Servis ağırlığı ve dengesi taşıma güvenliğini nasıl etkiler?', 'answer' => '860 kg servis ağırlığı ve uygun aks yük dağılımı, çift katlı taşımalarda dahi dengeyi koruyarak devrilme riskini düşürür.'],
                ['question' => 'RSi201’in tırmanma kabiliyeti operasyonu nasıl etkiler?', 'answer' => 'Yüklü %8, boş %16 eğim değerleri; rampa ve seviye farklılıklarında akışı kesmeden malzeme hareketine olanak verir.'],
                ['question' => 'Bakım aralıkları ve tipik sarf kalemleri nelerdir?', 'answer' => 'Li-ion sistemde asit bakımı yoktur; düzenli kontrol noktaları tekerler, fren sistemi ve hidrolik elemanlardır; planlı duruşlar kısalır.'],
                ['question' => 'Şeffaf mast kalkanının katkısı nedir?', 'answer' => 'İleri görüşü genişleterek palet giriş-çıkışında hassas hizalamayı kolaylaştırır; çarpışma ve palet hasar oranlarını azaltır.'],
                ['question' => 'Garanti kapsamı ve destek kanallarınız nelerdir?', 'answer' => 'Makine 12 ay, Li-ion akü 24 ay garanti kapsamındadır. Satış, servis ve yedek parça için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: RSi201');
    }
}
