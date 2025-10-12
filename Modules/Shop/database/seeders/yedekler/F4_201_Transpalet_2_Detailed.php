<?php
// F4_201_Transpalet_2_Detailed.php
namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F4_201_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');
        $brandId = DB::table('shop_brands')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) = 'İXTİF'")
            ->value('brand_id');

        DB::table('shop_products')
            ->where('sku', 'F4-201')
            ->update([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
  <p><strong>Türkiye’nin en çevik 2 ton sınıfı transpaleti: İXTİF F4 201 ile dar koridorlar artık geniş!</strong></p>
  <p>Depoda hız, sevkiyatta verim, bakımda huzur arayanlar için tasarlandı. 48V mimarisi, tak-çıkar Li-iyon bataryaları ve kompakt şasi, F4 201’i lojistikten perakendeye kadar her noktada “rakipsiz akış” mottosuna taşır. İXTİF ekosistemiyle bütünleşik çalışır: <strong>ikinci el alım-satımı</strong>, <strong>kiralık seçenekler</strong>, <strong>orijinal yedek parça</strong> ve <strong>7/24 teknik servis</strong> desteği tek platformda.</p>
  <ul>
    <li>2.0 ton nominal kapasite ile sınıfının güçlü performansı</li>
    <li>48V sistem ve BLDC sürüş ile verimli ivmelenme ve düşük tüketim</li>
    <li>Tak-çıkar Li-iyon modüllerle sıfıra yakın bakım ve hızlı şarj</li>
    <li>l2=400 mm kompakt yapı ile dar koridorda çevik manevra</li>
  </ul>
</section>
<section class="marketing-body">
  <h3>Neden Bu Ürün?</h3>
  <p>İXTİF F4 201, 2.0 ton kapasiteli <em>Li-iyon transpalet</em> segmentinde 48V mimarisiyle fark yaratır. 600 mm yük merkezi, 1360 mm dönüş yarıçapı ve 590/695 mm şasi genişliği kombinasyonu, dar alanlarda raf önlerinde ve çapraz trafik içinde seri manevra sağlar. 4.5/5 km/s seyir hızları, 0.016/0.020 m/s kaldırma ve 0.058/0.046 m/s indirme hızları dengeli bir akış sunar; operatör yorgunluğunu azaltır ve çevrim zamanlarını kısaltır. Tak-çıkar 24V/20Ah ×2 Li-iyon batarya paketi, hızlı şarj ve fırsat şarjı ile kesintisiz operasyonu destekler. Stabilize tekerlek opsiyonu, düzensiz zeminlerde ve iri yüklerde salınımı azaltır; kare borulu yeni tip tiller başı ise sağlamlık ve ergonomiyi birlikte sunar.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Depoda S&OP planına uyum için çevrim sürelerini öngörülebilir kılmak önemlidir. F4 201, standart 1150×560 mm çatal ölçüsüyle Avrupa paleti üzerinde optimum giriş-çıkış açısını sunar. 30 mm yerden açıklık ve poliüretan teker seti, düz zeminlerde sessiz ve titreşimsiz ilerleme sağlar. %8 yüklü, %16 yüksüz tırmanma değeri, rampa ve yükleme körüklerinde akıcı geçişe imkân verir. Li-iyon kimya sayesinde ara şarjla vardiya uzatmak kolaydır; hafıza etkisi yoktur. Platform F tabanlı modüler yapı, farklı çatal uzunluk-genişlik opsiyonları ile filonuzu standartlaştırırken kullanıma göre özelleştirir. Bu esneklik, stok kalemlerini azaltır ve servis süreçlerini kısaltır.</p>
  <h3>İXTİF Farkı</h3>
  <p>İXTİF tedarik ve servis ağı, ürün yaşam döngüsünü baştan sona yönetir: <strong>ikinci el alım-satımı</strong> ile filonuzu yenilerken değer korursunuz. Pik sezonlarda <strong>kiralık seçenekler</strong> ile CAPEX baskısını hafifletirsiniz. Uzun ömür ve güven için <strong>orijinal yedek parça</strong> kullanılır. Arıza ve periyodik bakım süreçlerinde <strong>7/24 teknik servis</strong> ile kesinti süreniz minimuma iner.</p>
  <h3>Teknik Üstünlükler</h3>
  <p>48/20 nominal batarya paketi iki adet 24V/20Ah Li-iyon modül ile oluşturulur; bu tasarım hem kolay değişim hem de güvenli flip kapak koruması sağlar. 590 veya 695 mm dış genişlik, 560 ya da 685 mm çatal aralığı opsiyonlarıyla uyumludur. 1360 mm dönüş yarıçapı ve 1550 mm toplam uzunluk, 2025–2160 mm koridor değerleriyle birlikte dar alanlarda net avantaj verir. Elektromanyetik servis freni ve mekanik direksiyon kombinasyonu, düşük bakım ve yüksek kontrol sağlar. BLDC sürüş, yüksek verimlilik ve 74 dB(A) operatör kulak seviyesi ile sessiz çalışma sunar. Tüm bu avantajlar lojistik, e-ticaret, FMCG, otomotiv yan sanayi ve soğuk zincir benzeri zorlu akışlarda gerçek performansa dönüşür.</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),

                // Primary specs (Transpalet şablonu: Kapasite, Çatal, Batarya, Hız)
                'primary_specs' => json_encode([
                    ['title' => 'Kapasite', 'value' => '2000 kg', 'icon' => 'weight-hanging'],
                    ['title' => 'Çatal', 'value' => '50×150×1150 mm (opsiyonlar mevcut)', 'icon' => 'arrows-left-right'],
                    ['title' => 'Batarya', 'value' => '48V (24V/20Ah ×2) Li-iyon', 'icon' => 'battery-full'],
                    ['title' => 'Hız', 'value' => '4.5/5 km/s (yüklü/boş)', 'icon' => 'gauge'],
                ], JSON_UNESCAPED_UNICODE),

                // Highlighted features (6, ikonlu)
                'highlighted_features' => json_encode([
                    ['icon' => 'plug-circle-bolt', 'title' => 'Tak-Çıkar Li-İyon', 'text' => '24V/20Ah ×2 modül ile hızlı değişim, fırsat şarjı ve sıfıra yakın bakım.'],
                    ['icon' => 'gauge-high', 'title' => '48V Güç', 'text' => '48V sistem ile ivmelenme ve verimlilik artışı.'],
                    ['icon' => 'arrows-left-right', 'title' => 'Çatal Seçenekleri', 'text' => '900–1500 mm uzunluk, 560–685 mm genişlik opsiyonları.'],
                    ['icon' => 'road', 'title' => 'Kompakt Manevra', 'text' => 'l2=400 mm ve Wa=1360 mm ile dar koridorda çeviklik.'],
                    ['icon' => 'shield-halved', 'title' => 'Güvenli Fren', 'text' => 'Elektromanyetik servis freni ile güvenli duruş.'],
                    ['icon' => 'headset', 'title' => 'İXTİF 7/24 Destek', 'text' => '7/24 teknik servis, kiralama, ikinci el ve orijinal yedek parça.'],
                ], JSON_UNESCAPED_UNICODE),

                // Features (list + branding)
                'features' => json_encode([
                    'list' => [
                        '48V BLDC sürüş ile yüksek verim ve düşük enerji tüketimi',
                        'Tak-çıkar Li-iyon paketler ile kesintisiz vardiya',
                        'Stabilize teker opsiyonu ile düzensiz zeminde yük kontrolü',
                        'Kare borulu sağlam tiller başı ile güvenli kullanım',
                        '1150×560 mm standardın yanında geniş çatal opsiyonları',
                        'Elektromanyetik fren ile güvenli duruş mesafeleri',
                        'Poliüretan teker seti ile sessiz ve düşük titreşim',
                        'Modüler platform F ile hızlı konfigürasyon',
                        'İXTİF ekosistemi: ikinci el, kiralama, yedek parça, 7/24 servis',
                        'CE uygunluğu ve endüstriyel standartlarla uyum',
                    ],
                    'branding' => [
                        'slogan' => 'Akışı Hızlandır, Maliyeti Azalt',
                        'motto' => 'Her palette güven, her metrede verim.',
                        'summary' => 'İXTİF F4 201, Li-iyon teknolojiyi kompakt şasiyle birleştirir; İXTİF’in ikinci el alım-satımı, kiralık seçenekler, orijinal yedek parça ve 7/24 teknik servis güvencesiyle toplam sahip olma maliyetini düşürür.',
                    ],
                ], JSON_UNESCAPED_UNICODE),

                // FAQ (10-12)
                'faq_data' => json_encode([
                    ['q' => 'Gerçek taşıma kapasitesi nedir?', 'a' => 'Nominal kapasite 2000 kg’dir. 600 mm yük merkezi ve uygun palet ergonomisi ile tam verim alınır. İXTİF 7/24 teknik servis ekibi sahada doğrulama ve operatör eğitimi sunar.'],
                    ['q' => 'Batarya nasıl değişir ve şarj süresi nedir?', 'a' => 'Tak-çıkar 24V/20Ah ×2 Li-iyon modüller hızlıca değiştirilir. Fırsat şarjı desteklenir. Orijinal yedek parça ve şarj ekipmanı İXTİF tarafından sağlanır.'],
                    ['q' => 'Kiralama seçenekleri mevcut mu?', 'a' => 'Evet. Operasyon pik dönemleri için kısa ve uzun dönem kiralık seçenekler sunuyoruz. İXTİF 7/24 teknik servis ve periyodik bakım dahildir.'],
                    ['q' => 'İkinci el değerlendirme yapılıyor mu?', 'a' => 'Evet. Filonuzdaki eski üniteleri ikinci el alım-satımı hizmetimizle değerlendirir, yeni F4 201’e geçişi hızlandırırız.'],
                    ['q' => 'Çatal ölçülerini özelleştirebilir miyiz?', 'a' => '900–1500 mm uzunluk ve 560–685 mm genişlik aralığında seçenekler vardır. Uygun kombinasyon İXTİF mühendisleri tarafından önerilir.'],
                    ['q' => 'Bakım aralıkları nedir?', 'a' => 'Li-iyon sistemde bakım ihtiyacı düşüktür. Aylık görsel kontroller ve periyodik teknik kontrolleri İXTİF 7/24 teknik servis planlar.'],
                    ['q' => 'Eğim performansı nedir?', 'a' => 'Yüklü %8, yüksüz %16’dır. Rampa operasyonları için güvenli limitlerdir.'],
                    ['q' => 'Gürültü seviyesi ne kadar?', 'a' => 'Yaklaşık 74 dB(A) seviyesindedir. Kapalı alanlarda konforludur.'],
                    ['q' => 'Yedek parça tedariki nasıl?', 'a' => 'İXTİF orijinal yedek parça stoğu ile hızlı tedarik sağlar. Kritik parçalar için SLA yönetimi uygulanır.'],
                    ['q' => 'Garanti kapsamı nedir?', 'a' => 'Transpalet kategorisi için 12 ay garanti ve 24 ay yedek parça tedarik güvencesi sunulur. İXTİF 7/24 teknik servis desteği dahildir.'],
                    ['q' => 'CE belgesi var mı?', 'a' => 'Evet. CE uygunluğu mevcuttur. Belgeler siparişte paylaşılır.'],
                    ['q' => 'Soğuk depo uyumu nasıldır?', 'a' => 'Li-iyon batarya düşük ısılarda iyi performans verir. Uygulama özelinde ısı yönetimi ve teker seçimi İXTİF tarafından önerilir.'],
                ], JSON_UNESCAPED_UNICODE),

                // Technical specs (Türkçe keyler)
                'technical_specs' => json_encode([
                    '_title' => 'Teknik Özellikler',
                    '_icon'  => 'table-list',
                    'uretici' => 'İXTİF',
                    'model' => 'F4 201',
                    'surus' => 'Elektrikli, yaya kumandalı',
                    'kapasite_kg' => 2000,
                    'yuk_merkezi_mm' => 600,
                    'servis_agirligi_kg' => 140,
                    'yuk_mesafesi_x_mm' => 950,
                    'aks_mesafesi_y_mm' => 1180,
                    'genislik_mm' => '590 / 695',
                    'toplam_uzunluk_mm' => 1550,
                    'yukseklik_tiller_min_max_mm' => '410 / 535',
                    'kaldirma_yuksekligi_mm' => 105,
                    'indirgen_yukseklik_mm' => 85,
                    'yuzey_bosluk_mm' => 30,
                    'koridor_1000x1200_mm' => 2160,
                    'koridor_800x1200_mm' => 2025,
                    'donus_yaricapi_mm' => 1360,
                    'catal_boyutlari_mm' => '50×150×1150 (opsiyonlar mevcut)',
                    'catal_araligi_mm' => '560 / 685',
                    'teker_on_arka' => 'PU; ön 210×70 mm, arka 80×60 mm',
                    'ek_teker_mm' => '74×30 mm',
                    'teker_dizilimi' => '1x — / 4',
                    'hiz_km_s' => '4.5 / 5 (yüklü/boş)',
                    'kaldirma_hizi_m_s' => '0.016 / 0.020',
                    'indirme_hizi_m_s' => '0.058 / 0.046',
                    'maks_egim_yuklu_yuksuz' => '%8 / %16',
                    'fren' => 'Elektromanyetik',
                    'surus_motor_kw' => 0.9,
                    'kaldirma_motor_kw' => 0.7,
                    'batarya_v_ah' => '48/20 (24V/20Ah ×2)',
                    'sarj_cihazi' => '24V-5A ×2 (opsiyon 24V-10A ×2)',
                    'direksiyon' => 'Mekanik',
                    'surus_kontrol' => 'BLDC',
                    'ses_basinci_dbA' => 74,
                    'enerji_tuketimi_kWh_h' => 0.18,
                    'devir_cikisi_t_h' => 88,
                    'devir_verimi_t_kWh' => 473.12,
                ], JSON_UNESCAPED_UNICODE),

                // Use cases (8)
                'use_cases' => json_encode([
                    'E-ticaret dağıtım merkezlerinde yoğun hacimli palet hareketleri ve cross-dock operasyonları',
                    'Perakende arka alan raf beslemeleri ve mağaza içi dar koridor sevkiyatları',
                    'Otomotiv yan sanayide atölye içi WIP malzeme akışı ve montaj hattı beslemesi',
                    'FMCG depolarında yüksek çevrimli toplama ve yükleme rampası operasyonları',
                    'Soğuk depo ve gıda lojistiğinde düşük ısılarda kesintisiz palet transferi',
                    '3PL antrepolarda karma palet konsolidasyonu ve sevkiyat hazırlığı',
                    'İçecek endüstrisinde dar alanlarda ağır paletlerin hızlı konumlandırılması',
                    'Kimya paketleme hatlarında titreşim hassas ürünlerin güvenli taşınması',
                ], JSON_UNESCAPED_UNICODE),

                // Competitive advantages (7, ikonlu, İXTİF 4 hizmet içermeli)
                'competitive_advantages' => json_encode([
                    ['icon' => 'bolt', 'title' => '48V Verim', 'text' => 'Daha düşük tüketim, daha güçlü çekiş.'],
                    ['icon' => 'cube', 'title' => 'Modüler Platform', 'text' => 'Platform F ile hızlı konfigürasyon ve yedek parça standardizasyonu.'],
                    ['icon' => 'gears', 'title' => 'Düşük Bakım', 'text' => 'Li-iyon ve elektromanyetik frenle daha az duruş.'],
                    ['icon' => 'cart-flatbed', 'title' => 'Geniş Çatal Seçenekleri', 'text' => '900–1500 mm uzunluk, 560–685 mm genişlik.'],
                    ['icon' => 'arrows-rotate', 'title' => 'İkinci El Alım-Satımı', 'text' => 'Filonuzun değerini koruyun.'],
                    ['icon' => 'handshake', 'title' => 'Kiralık Seçenekler', 'text' => 'Pik sezonda esnek kapasite.'],
                    ['icon' => 'screwdriver-wrench', 'title' => 'Orijinal Yedek Parça ve 7/24 Servis', 'text' => 'Hızlı tedarik ve kesintisiz destek.'],
                ], JSON_UNESCAPED_UNICODE),

                // Target industries (20+)
                'target_industries' => json_encode([
                    ['icon' => 'warehouse', 'title' => 'Lojistik Depolama'],
                    ['icon' => 'truck', 'title' => '3PL ve Nakliye'],
                    ['icon' => 'bag-shopping', 'title' => 'Perakende'],
                    ['icon' => 'industry', 'title' => 'Otomotiv Yan Sanayi'],
                    ['icon' => 'bottle-water', 'title' => 'İçecek'],
                    ['icon' => 'burger', 'title' => 'Gıda ve İçecek'],
                    ['icon' => 'snowflake', 'title' => 'Soğuk Depo'],
                    ['icon' => 'flask', 'title' => 'Kimya'],
                    ['icon' => 'box', 'title' => 'E-ticaret'],
                    ['icon' => 'store', 'title' => 'Mağaza Arkası'],
                    ['icon' => 'pallet', 'title' => 'Ambalaj'],
                    ['icon' => 'kit-medical', 'title' => 'İlaç ve Sağlık'],
                    ['icon' => 'leaf', 'title' => 'Tarım Ürünleri'],
                    ['icon' => 'oil-well', 'title' => 'Petrokimya Lojistiği'],
                    ['icon' => 'helmet-safety', 'title' => 'İnşaat Malzemeleri'],
                    ['icon' => 'solar-panel', 'title' => 'Enerji ve Solar'],
                    ['icon' => 'microchip', 'title' => 'Elektronik'],
                    ['icon' => 'bread-slice', 'title' => 'Fırıncılık'],
                    ['icon' => 'wine-bottle', 'title' => 'Şarap ve İçecek'],
                    ['icon' => 'recycle', 'title' => 'Geri Dönüşüm'],
                    ['icon' => 'cubes', 'title' => 'Seramik ve Cam'],
                ], JSON_UNESCAPED_UNICODE),

                // Accessories (5-6, ikonlu)
                'accessories' => json_encode([
                    ['icon' => 'battery', 'title' => 'Ek Li-İyon Modül', 'key' => 'ek_batarya_modulu'],
                    ['icon' => 'charging-station', 'title' => 'Hızlı Şarj Ünitesi', 'key' => 'hizli_sarj_unitesi'],
                    ['icon' => 'wheelchair-move', 'title' => 'Stabilize Teker Seti', 'key' => 'stabilize_teker'],
                    ['icon' => 'grip-vertical', 'title' => 'Özel Çatal Boyu', 'key' => 'ozel_catal_boyu'],
                    ['icon' => 'screwdriver-wrench', 'title' => 'Bakım Kiti', 'key' => 'bakim_kiti'],
                    ['icon' => 'shield', 'title' => 'Çeki Muhafazası', 'key' => 'ceki_koruma'],
                ], JSON_UNESCAPED_UNICODE),

                // Certifications (Sadece PDF’deki)
                'certifications' => json_encode([
                    ['icon' => 'certificate', 'title' => 'CE'],
                ], JSON_UNESCAPED_UNICODE),

                // Warranty info (Transpalet: 12+24)
                'warranty_info' => json_encode([
                    'garanti_suresi_ay' => 12,
                    'yedek_parca_tedarik_ay' => 24,
                ], JSON_UNESCAPED_UNICODE),

                'updated_at' => now(),
            ]);
    }
}
