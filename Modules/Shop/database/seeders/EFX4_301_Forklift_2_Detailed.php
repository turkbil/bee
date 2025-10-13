<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFX4_301_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFX4-301')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFX4-301');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section><h2>İXTİF EFX4 301: Modüler Enerjiyle Maksimum Uptime</h2><p>İXTİF EFX4 301, 3.0 ton sınıfında modüler ve elle değiştirilebilir Li-Ion batarya mimarisiyle operasyonların ritmini yeniden tanımlar. Her biri yaklaşık 26 kg olan hafif batarya modülleri, özel yan kapıdan kolay erişimle dakikalar içinde değiştirilebilir; bu sayede şarj altyapısının sınırlı olduğu şantiyeler, açık saha projeleri veya kırsal depolar için gerçek bir çözüm sunar. Geniş görüşlü direk, ferah kabin, yeni LED farlar ve geniş fren pedalı gibi sürücü odaklı geliştirmeler konforu artırırken, suya dayanıklı gövde sahada güven verir. Opsiyonel çoklu şarj istasyonu, birden fazla bataryayı aynı anda hazırlayarak hazır güç havuzu oluşturur; kiralama ve paylaşımlı kullanım senaryolarında maliyetleri düşürür.</p></section>
<section><h3>Teknik</h3><p>EFX4 301’in kalbinde yüksek verimliliğiyle bilinen PMS (Permanent Magnet Synchronous) tahrik motoru bulunur ve 8 kW S2-60dk gücüyle hassas, sessiz ve enerji tasarruflu sürüş sunar. 80V/100Ah nominal kapasiteli Li-Ion batarya seti (üç modül) akıllı BMS ile korunur; hızlı şarj ve uzun çevrim ömrüyle planlı duruşları kısaltır. 3.0 ton (Q=3000 kg) kapasite, 500 mm yük merkezinde güvenle sağlanır. 11/12 km/s yürüyüş hızı, 0.29/0.36 m/s kaldırma ve 0.4/0.4 m/s indirme değerleri akıcı malzeme akışını destekler. 2428 mm dönüş yarıçapı ve 1228 mm gövde genişliği dar koridor çevikliğini artırır. 2265 mm alçalmış direk, 3000 mm kaldırma ve 4096 mm yükselmiş direk yüksekliği standart mastta optimum denge sağlar. Pnömatik lastikler; 28x9-15 (ön) ve 6.5F-10 (arka) ölçüleriyle farklı zemin koşullarında güvenli yol tutuş sunar. Hidrolik servis ve mekanik park freni, 60 dB(A) kabin içi gürültü değeri ve hidrolik direksiyon ile gün boyu konfor ve güvenlik birlikte sunulur.</p></section>
<section><h3>Sonuç</h3><p>Modüler, taşınabilir ve kiralanabilir enerji yaklaşımıyla İXTİF EFX4 301; endüstriyel tesislerden tarım sahalarına, inşaat ve uzak lokasyonlara kadar farklı ortamlarda kesintisiz çalışma sağlar. Akü setini saha dışında şarj ederek ekipman kullanımını ayırabilir, planlı vardiya döngülerini sorunsuz sürdürebilirsiniz. Teknik detaylar ve keşif için 0216 755 3 555 numaralı hattımızı arayın.</p></section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '3000 kg (c=500 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V / 100Ah Li-Ion (3 modül, BMS)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '11/12 km/s (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş Yarıçapı', 'value' => '2428 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Elle Değiştirilebilir Modüler Akü', 'description' => '26 kg modüllerle hızlı değişim, minimum duruş süresi.'],
                ['icon' => 'bolt', 'title' => 'PMS Motor Verimliliği', 'description' => 'Yüksek enerji dönüşümü ve hassas tork kontrolü.'],
                ['icon' => 'cog', 'title' => 'Akıllı BMS Entegrasyonu', 'description' => 'Hücre sağlığı, dengeleme ve güvenlik optimizasyonu.'],
                ['icon' => 'plug', 'title' => 'Çoklu Şarj İstasyonu', 'description' => 'Harici 6 bataryaya kadar eşzamanlı şarj, hazır güç havuzu.'],
                ['icon' => 'shield-alt', 'title' => 'Dayanıklı Gövde', 'description' => 'Suya dayanıklı yapı ile zorlu sahalarda güven.'],
                ['icon' => 'building', 'title' => 'Geniş Görüşlü Direk', 'description' => 'Operatör görüşü ve güvenliği artıran yeni tasarım.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'industry', 'text' => 'Ağır sanayi hat besleme ve WIP taşıma operasyonları'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında yoğun vardiya içi yük akışı'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında giriş-çıkış ve rampada hızlı çevrim'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas yüklerin güvenli transferi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça merkezlerinde cross-dock'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde raf arası transfer'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde kontrollü alan lojistiği'],
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment ve toplama-besleme süreçleri']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'battery-full', 'text' => 'Modüler, taşınabilir enerji ile şarj altyapısına bağımlılığı azaltır'],
                ['icon' => 'bolt', 'text' => 'PMS motor ve akıllı BMS ile düşük tüketim ve yüksek verim'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 2428 mm dönüş yarıçapıyla çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'Suya dayanıklı, sağlam gövde ile uzun ömür ve güvenlik'],
                ['icon' => 'star', 'text' => 'Geniş görüş ve ergonomi ile operatör verimliliği artışı']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir Depoları'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolar'],
                ['icon' => 'car', 'text' => 'Otomotiv Yan Sanayi'],
                ['icon' => 'industry', 'text' => 'Genel Üretim Tesisleri'],
                ['icon' => 'flask', 'text' => 'Kimya ve Petrokimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'building', 'text' => 'Kamu Lojistiği ve Belediyeler'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Dağıtım Ağları'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Kurye Hub’ları'],
                ['icon' => 'warehouse', 'text' => 'Limana Yakın Depolama'],
                ['icon' => 'industry', 'text' => 'Metal ve Ağır Sanayi'],
                ['icon' => 'flask', 'text' => 'Boya ve Kimyasal Karışım'],
                ['icon' => 'car', 'text' => 'Yedek Parça Dağıtım Merkezleri'],
                ['icon' => 'store', 'text' => 'DIY/Yapı Market Lojistiği'],
                ['icon' => 'building', 'text' => 'İnşaat Şantiye Lojistiği'],
                ['icon' => 'briefcase', 'text' => 'Kiralama Filoları ve Paylaşımlı Depolar']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay üretim hatalarına karşı garanti altındadır. Li-Ion batarya modülleri ise 24 ay boyunca akıllı BMS kapsamında hücre koruma ve performans kriterleriyle desteklenir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Standart Şarj Kablo Seti', 'description' => 'Günlük harici şarj bağlantıları için temel kablo seti', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Modüler Çoklu Şarj İstasyonu', 'description' => 'Harici 6 bataryaya kadar eşzamanlı ve güvenli şarj', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Ek Li-Ion Batarya Modül Seti', 'description' => 'Vardiya uzatma için ilave 3’lü modül seti', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'building', 'name' => 'LED Aydınlatma Paketi', 'description' => 'Ön LED farlar ve kabin içi aydınlatma yükseltmesi', 'is_standard' => true, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Modüler batarya paketleri tek kişi tarafından güvenle değiştirilebilir mi?', 'answer' => 'Evet. Her modül yaklaşık 26 kg olup yan servis kapısından erişimle ergonomik şekilde değiştirilebilir; ek ekipman gerektirmez.'],
                ['question' => 'Standart batarya kapasitesi hangi çalışma süresini hedefliyor?', 'answer' => '80V/100Ah set, tipik tek vardiyada yoğunluğa bağlı olarak ara şarj ile kesintisiz akış sağlar; çoklu set ile süre uzatılır.'],
                ['question' => 'Opsiyonel çoklu şarj istasyonu kaç bataryayı aynı anda şarj eder?', 'answer' => 'İstasyon kompakt ve modülerdir; aynı anda altı bataryaya kadar güvenli şarj imkânı sunar ve sahadan bağımsız konumlandırılır.'],
                ['question' => 'PMS tahrik sistemi enerji verimliliğine nasıl katkı sağlar?', 'answer' => 'Sabit mıknatıslı senkron motor yüksek verim ve hassas kontrol sunar; daha düşük tüketimle daha uzun çalışma sağlar.'],
                ['question' => 'Mekanik ve hidrolik fren kombinasyonu bakım maliyetini etkiler mi?', 'answer' => 'Hidrolik servis freni ve mekanik park freni dayanıklıdır; düzenli kontrol ve fren hidroliği bakımı ile düşük TCO hedeflenir.'],
                ['question' => 'Gürültü seviyesi ve operatör konforu için sunulan geliştirmeler neler?', 'answer' => '60 dB(A) seviyesinde sessiz kabin, yeni koltuk/kolçak, geniş pedallar ve geniş görüşlü direk ile konfor artırılır.'],
                ['question' => 'Hangi zemin koşulları için pnömatik lastikler tercih ediliyor?', 'answer' => '28x9-15 ön ve 6.5F-10 arka ölçüler, açık sahalar ve pürüzlü zeminlerde daha iyi sönümleme ve yol tutuş sunar.'],
                ['question' => 'Dönüş yarıçapı ve koridor genişliği dar alanlarda yeterli mi?', 'answer' => '2428 mm dönüş yarıçapı ve 4115/4315 mm koridor değerleri, çoğu depo layout’unda raf arası manevrayı kolaylaştırır.'],
                ['question' => 'Bataryalar sahada şarj edilemediğinde operasyon nasıl sürer?', 'answer' => 'Taşınabilir modüller farklı noktada şarj edilirken araç kalan modüllerle çalışmaya devam eder; çalınma riski de azalır.'],
                ['question' => 'Mast seçenekleri ve standart kaldırma yüksekliği nedir?', 'answer' => 'Standart mast 3000 mm’dir; 2700/3300/3500 mm seçenekleri ile proje ihtiyacına göre konfigürasyon yapılır.'],
                ['question' => 'Garanti süresince akü performansı nasıl izlenir?', 'answer' => 'Akıllı BMS; hücre gerilimleri, sıcaklık ve döngüleri izler; bakım planlamasını kolaylaştırır.'],
                ['question' => 'Garanti koşulları ve servis desteği nasıl sağlanır?', 'answer' => 'Makine 12 ay, akü modülleri 24 ay garanti kapsamındadır. Satış ve yetkili servis için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed: EFX4-301 güncellendi");
    }
}
