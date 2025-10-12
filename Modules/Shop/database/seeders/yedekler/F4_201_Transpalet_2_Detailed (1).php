<?php
<?php
namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_2_Detailed extends Seeder
{
    public function run()
    {
        $product = DB::table('shop_products')->where('sku', 'F4-201')->first();
        if (!$product) return;

        $update = [
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
  <p><strong>Türkiye'nin dağıtım zincirlerini turbo hızda akıtan, sınıfında rakipsiz verimlilik: İXTİF F4 201 Li-Ion Transpalet!</strong></p>
  <p>Modern depolar artık hız, güvenlik ve esneklik istiyor. İXTİF bu ihtiyacı tek gövdede çözüyor: F4 201, 48V elektrik mimarisi ve çift 24V/20Ah Li-Ion batarya modülü ile “tak-çıkar” esnekliği sunarken, <em>ikinci el alım-satımı</em>, <em>kiralık seçenekler</em>, <em>orijinal yedek parça</em> ve <em>7/24 teknik servis</em> güvencesiyle operasyonlarınızı sürekli açık tutar.</p>
  <ul>
    <li>2.0 ton net kapasiteyle ağır yükte bile kontrollü, akıcı ve güvenli taşıma</li>
    <li>400 mm l2 ve 1360 mm dönüş yarıçapıyla dar koridorlarda üstün manevra</li>
    <li>Çift 24V/20Ah Li-Ion modül, hızlı şarj ve sıfır bakım ile kesintisiz operasyon</li>
    <li>Platform F tabanlı tasarım ile ölçeklenebilir konfigürasyon ve hızlı parça tedariği</li>
  </ul>
</section>
<section class="marketing-body">
  <h3>Neden Bu Ürün?</h3>
  <p>F4 201, 48V sistem mimarisi sayesinde torku yüksek, verimliliği tutarlı bir sürüş sunar. 2 ton (2000 kg) kapasite ve 600 mm yük merkezi ile endüstriyel paletlerde güvenli denge sağlar. 590/695 mm gövde genişliği seçenekleri ile EUR ve endüstriyel paletlerin çoğunda rahat giriş sunar. 50×150×1150 mm standart çatal, 900–1500 mm arası uzunluk ve 560/685 mm arası çatal aralığı opsiyonlarıyla farklı sektör senaryolarına uyarlanabilir. Stabilize tekerlek opsiyonu düzensiz zeminlerde yük salınımını azaltır, elektromanyetik servis frenleme ise yük altında kontrollü duruş sağlar.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Depo giriş-çıkış noktaları, cross-dock hatları ve mağaza arkası stok alanlarında eşya akışını hızlandırır. 4.5/5 km/s hızlarıyla boş-dolu dengesi korunurken, 0.016/0.020 m/s kaldırma, 0.058/0.046 m/s indirme hız parametreleri hassas yük yerleştirmeleri için yeterlidir. 85 mm minimum çatal yüksekliği, 105 mm kaldırma ile rampalarda ve eşiklerde takılmayı azaltır. 30 mm yerden açıklık orta tekerlek bölgesinde zemine uyum sağlar. BLDC sürüş ve mekanik yönlendirme kombinasyonu bakım maliyetlerini düşürür. Li-Ion kimyası ara şarjı tolere eder; bu da yemek arasında 15–30 dakikalık şarjlarla vardiya sürekliliği demektir. İXTİF’in <em>kiralık seçenekler</em> portföyü kampanya sezonlarında kapasiteyi artırmanızı sağlar; operasyon sakinleştiğinde iade ederek maliyeti değişkenleştirirsiniz.</p>
  <h3>İXTİF Farkı</h3>
  <p>Ürünle birlikte hizmet ekosistemi gelir: <strong>ikinci el alım-satımı</strong> ile filonuzu yenilerken sermaye kilidini azaltın; <strong>kiralık seçenekler</strong> ile pik dönemleri yönetin; <strong>orijinal yedek parça</strong> tedarikiyle arıza riskini düşürün; <strong>7/24 teknik servis</strong> ile gece ve hafta sonu dahil kesintileri en aza indirin. Bu bütünleşik yaklaşım, TCO hesaplarında somut avantaj üretir.</p>
  <h3>Teknik Üstünlükler</h3>
  <p>48/20 V/Ah nominal paket, iki adet 24V/20Ah modülden oluşur; tak-çıkar tasarım batarya değişimini dakikalara indirir. 140 kg servis ağırlığı ve 1360 mm dönüş yarıçapı, perakende depo koridorlarında güvenli dönüş sağlar. 2160 mm (1000×1200 yan) ve 2025 mm (800×1200 uzun) koridor değerleri standarda uygundur. 8/16% eğim performansı rampa geçişlerinde destek olur. Poliüretan sürüş ve yük tekerlekleri düşük yuvarlanma direnci ve zemini koruma sağlar. Tüm bu veriler dürüst parametrelerle beyan edilmiştir; doğru kullanım koşullarında ömür, batarya bakım gerektirmeyen tasarım ve hızlı şarj alışkanlığı ile uzar.</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'title' => 'Kapasite', 'value' => '2.0 Ton (2000 kg)'],
                ['icon' => 'arrows-left-right', 'title' => 'Çatal', 'value' => '50×150×1150 mm | 560/685 mm aralık'],
                ['icon' => 'battery-full', 'title' => 'Batarya', 'value' => '48V / 20Ah (2×24V/20Ah, Li-Ion, tak-çıkar)'],
                ['icon' => 'gauge', 'title' => 'Hız', 'value' => '4.5/5 km/s (yüklü/boş)'],
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'charging-station', 'title' => 'Tak-Çıkar Li-Ion', 'description' => 'İki adet 24V/20Ah modül ile hızlı değişim, ara şarj uyumlu yapı.'],
                ['icon' => 'gauge-high', 'title' => '48V Güç Mimarisi', 'description' => 'Yüksek tork ve verimlilik ile yoğun vardiya temposuna uygun.'],
                ['icon' => 'ruler-combined', 'title' => 'Kompakt Gövde', 'description' => '400 mm l2 ve 1360 mm dönüş yarıçapıyla dar koridor kabiliyeti.'],
                ['icon' => 'shield', 'title' => 'Güvenli Frenleme', 'description' => 'Elektromanyetik servis freni; eğimde kontrollü duruş.'],
                ['icon' => 'screwdriver-wrench', 'title' => 'İXTİF Servis Ekosistemi', 'description' => '7/24 teknik servis, orijinal yedek parça ve kiralık seçenekler.'],
                ['icon' => 'boxes-stacked', 'title' => 'Toplu Tedarik Avantajı', 'description' => '4 ünite/kutu ve 40’ konteynerde yüksek istifleme verimi.'],
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                '48V BLDC sürüş sistemi ve 2.0 ton kapasite ile güçlü performans',
                'Tak-çıkar iki adet 24V/20Ah Li-Ion modül ile hızlı batarya değişimi',
                'Polyurethane tekerlekler ile düşük yuvarlanma direnci ve sessiz çalışma',
                '400 mm l2 ve 590/695 mm genişlik seçenekleriyle kompakt tasarım',
                '1360 mm dönüş yarıçapı ile dar koridor manevrası',
                'Stabilize tekerlek opsiyonu ile düzensiz zeminlerde yük kontrolü',
                'Elektromanyetik servis freni ile güvenli duruş',
                'Platform F tabanlı tasarım ile ortak parça ve kolay bakım',
                'İXTİF ikinci el alım-satımı ile filonuzu kolayca yenileyin',
                'İXTİF kiralık seçenekler ile pik dönem yönetimi',
                'İXTİF orijinal yedek parça temini ile kesintisiz operasyon',
                'İXTİF 7/24 teknik servis ile gece-gündüz destek',
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Li-Ion bataryaların avantajı nedir?', 'answer' => 'Li-Ion bataryalar hızlı şarj, ara şarja uygunluk ve düşük bakım ihtiyacı sunar. İXTİF, orijinal yedek parça ve 7/24 teknik servis ile batarya ömrünü maksimize edecek bakım protokollerini uygular. Kiralık seçenekler ve ikinci el alım-satımı sayesinde finansal esneklik sağlanır.'],
                ['question' => 'Hangi paletlerle uyumlu?', 'answer' => 'EUR (1200×800) ve endüstriyel (1200×1000) paletlerle uyumludur. 560 ve 685 mm çatal aralığı ile çoğu palet standardına girer.'],
                ['question' => 'Eğim performansı ne kadar?', 'answer' => 'Maksimum eğim kapasitesi yüklü %8, yüksüz %16’dır. Rampa kullanımlarında hız ve fren yönetimi için operatör eğitimleri önerilir.'],
                ['question' => 'Bakım aralıkları nasıl?', 'answer' => 'BLDC sürüş ve Li-Ion batarya ile bakım ihtiyacı düşüktür. İXTİF’in 7/24 teknik servis ağı ve orijinal yedek parça stoğu ile planlı bakım duruşları minimuma iner.'],
                ['question' => 'Kiralama mümkün mü?', 'answer' => 'Evet. İXTİF kiralık seçenekler sunar. Pik dönemlerde kapasite artırımı için esnektir; dönem bitiminde iade ile masrafı değişkene çevirirsiniz.'],
                ['question' => 'İkinci el alım-satımı var mı?', 'answer' => 'Evet. İXTİF ikinci el alım-satımı ile eski ekipmanı değerinde alıp yeni F4 201’e geçişinizi hızlandırır.'],
                ['question' => 'Orijinal yedek parça temin ediliyor mu?', 'answer' => 'Evet. İXTİF orijinal yedek parça tedariki ile yüksek uyumluluk ve güvenlik standartları korunur.'],
                ['question' => 'Gece arızasında destek alabilir miyim?', 'answer' => 'Evet. 7/24 teknik servis hattımızla vardiya dışı saatlerde de müdahale edilir.'],
                ['question' => 'Soğuk hava deposunda kullanabilir miyim?', 'answer' => 'Poliüretan tekerlek ve Li-Ion kimya ara şarja uygundur. Ortam koşullarına göre lastik ve yağ önerileri için İXTİF teknik servisinden destek alın.'],
                ['question' => 'Operatör eğitimi sağlanıyor mu?', 'answer' => 'Evet. İXTİF, devreye alma sırasında güvenli kullanım ve enerji yönetimi eğitimleri sağlar.'],
                ['question' => 'Yedek batarya modülü alabilir miyim?', 'answer' => 'Evet. Tak-çıkar yapı sayesinde ek 24V/20Ah modüller stoklanabilir. İXTİF orijinal yedek parça politikası geçerlidir.'],
                ['question' => 'Garanti kapsamı nedir?', 'answer' => 'Transpalet kategorisinde 12 ay standart garanti + 24 ay destek kapsamı bulunur. Batarya için ayrı koşullar uygulanabilir. Detaylar İXTİF sözleşmesinde yer alır.'],
            ], JSON_UNESCAPED_UNICODE),

            'technical_specs' => json_encode([
                'üretici' => 'İXTİF',
                'model' => 'F4 201',
                'tahrik' => 'Elektrikli (BLDC)',
                'operatör_tipi' => 'Yaya (pedestrian)',
                'kapasite_kg' => 2000,
                'yuk_merkezi_mm' => 600,
                'servis_agirligi_kg' => 140,
                'lastik_tipi' => 'Poliüretan (PU)',
                'on_lastik' => '210×70 mm',
                'arka_lastik' => '80×60 mm (tandem)',
                'ek_teker' => '74×30 mm (opsiyonel stabilize)',
                'cekis_teker_sayisi' => '1x / 4 yük tekeri',
                'kaldirma_yuksekligi_mm' => 105,
                'tiller_yuksekligi_min_max_mm' => '750 / 1190',
                'indirilmis_catal_yuksekligi_mm' => 85,
                'toplam_uzunluk_mm' => 1550,
                'yuk_yuzune_uzunluk_l2_mm' => 400,
                'toplam_genislik_mm' => '590 / 695',
                'catal_boyutu_mm' => '50×150×1150',
                'catal_araligi_mm' => '560 / 685',
                'yerden_aciklik_mm' => 30,
                'koridor_genisligi_1000x1200_yan_mm' => 2160,
                'koridor_genisligi_800x1200_uzun_mm' => 2025,
                'donus_yaricapi_mm' => 1360,
                'hiz_km_s_yuklu_bos' => '4.5 / 5',
                'kaldirma_hizi_m_s_yuklu_bos' => '0.016 / 0.020',
                'indirme_hizi_m_s_yuklu_bos' => '0.058 / 0.046',
                'max_egim_%_yuklu_bos' => '8 / 16',
                'fren' => 'Elektromanyetik',
                'surus_motoru_kw' => 0.9,
                'kaldirma_motoru_kw' => 0.7,
                'batarya_V_Ah' => '48 / 20 (2×24V/20Ah modül)',
                'batarya_agirligi_kg' => 10,
                'surus_kontrol' => 'BLDC',
                'direksiyon' => 'Mekanik',
                'ses_basinci_dbA' => 74
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                'E-ticaret fulfillment merkezlerinde EUR paletlerin yoğun vardiyada raflar arası transferi.',
                'Perakende bölge depolarında kamyondan rafa hızlı mal indirme-yükleme akışı.',
                'Gıda lojistiğinde soğuk odalar ile hazırlama alanları arasında taşıma.',
                'İlaç ve kozmetik depolarında hassas ürünlerin düşük darbe ile taşınması.',
                'Otomotiv yedek parça depolarında dar koridorlarda toplama beslemesi.',
                'Hızlı tüketim ürünleri cross-dock hatlarında saatlik yüksek turn-over.',
                'Mağaza arkası stok alanlarında sessiz ve zemini yıpratmayan hareket.',
                '3PL hub’larında pik sezonda kiralık havuzla kapasite ölçekleme.',
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '48V mimari ile yüksek verim ve tork.'],
                ['icon' => 'battery-half', 'text' => 'Tak-çıkar Li-Ion modüllerle kesintisiz vardiya.'],
                ['icon' => 'arrows-to-circle', 'text' => 'Kompakt 400 mm l2 ile dar koridor manevrası.'],
                ['icon' => 'shield-halved', 'text' => 'Elektromanyetik fren ve sabit hız kontrolü ile güvenlik.'],
                ['icon' => 'warehouse', 'text' => 'Platform F ile ortak parça ve hızlı servis erişimi.'],
                ['icon' => 'truck-ramp-box', 'text' => 'Toplu tedarikte lojistik maliyet avantajı.'],
                ['icon' => 'headset', 'text' => 'İXTİF ekosistemi: ikinci el alım-satımı, kiralık seçenekler, orijinal yedek parça, 7/24 teknik servis.'],
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon'=>'box-open','name'=>'E-ticaret'],
                ['icon'=>'shop','name'=>'Perakende'],
                ['icon'=>'warehouse','name'=>'Depolama'],
                ['icon'=>'truck-fast','name'=>'Lojistik'],
                ['icon'=>'cart-flatbed','name'=>'3PL'],
                ['icon'=>'pills','name'=>'İlaç'],
                ['icon'=>'flask','name'=>'Kozmetik'],
                ['icon'=>'car','name'=>'Otomotiv'],
                ['icon'=>'bread-slice','name'=>'Gıda'],
                ['icon'=>'snowflake','name'=>'Soğuk Zincir'],
                ['icon'=>'shirt','name'=>'Tekstil'],
                ['icon'=>'microchip','name'=>'Elektronik'],
                ['icon'=>'industry','name'=>'Üretim'],
                ['icon'=>'leaf','name'=>'Tarım'],
                ['icon'=>'kit-medical','name'=>'Sağlık Lojistiği'],
                ['icon'=>'wine-bottle','name'=>'İçecek'],
                ['icon'=>'oil-can','name'=>'Kimya'],
                ['icon'=>'seedling','name'=>'Fidancılık'],
                ['icon'=>'chair','name'=>'Mobilya'],
                ['icon'=>'box','name'=>'Ambalaj'],
                ['icon'=>'plug','name'=>'Beyaz Eşya'],
                ['icon'=>'hammer','name'=>'Yapı Market'],
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon'=>'battery','name'=>'Yedek Li-Ion Modül (24V/20Ah)','description'=>'Tak-çıkar hızlı değişim için ek modül','is_standard'=>false,'is_optional'=>true],
                ['icon'=>'gauge','name'=>'Harici Çift Şarj Cihazı (2×24V-10A)','description'=>'İki modülü eşzamanlı hızlı şarj','is_standard'=>false,'is_optional'=>true],
                ['icon'=>'circle-nodes','name'=>'Stabilize Teker Seti','description'=>'Düzensiz zeminlerde kararlılık','is_standard'=>false,'is_optional'=>true],
                ['icon'=>'arrows-left-right','name'=>'Özel Çatal Boyu','description'=>'900–1500 mm uzunluk seçenekleri','is_standard'=>false,'is_optional'=>true],
                ['icon'=>'screwdriver-wrench','name'=>'Bakım Kiti','description'=>'Planlı bakım sarf malzemeleri','is_standard'=>false,'is_optional'=>true],
                ['icon'=>'shield','name'=>'Koruyucu Kapak Seti','description'=>'Batarya flip kapak yedek','is_standard'=>false,'is_optional'=>true],
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon'=>'certificate','name'=>'CE','year'=>2024,'authority'=>null]
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'kategori' => 'transpalet',
                'garanti_standart_ay' => 12,
                'destek_ekstra_ay' => 24,
                'detay' => 'Transpalet kategorisinde 12 ay standart garanti + 24 ay ek destek. Batarya için özel koşullar geçerlidir.',
            ], JSON_UNESCAPED_UNICODE),
        ];

        DB::table('shop_products')->where('product_id', $product->product_id)->update($update);
    }
}