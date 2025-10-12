<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL503_HV_6_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL503-HV-6')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İXTİF EFL503 HV-6 - 5.0 Ton Yüksek Voltaj Elektrikli Forklift: Yüksek Voltajla Ağır İşlerin Yeni Standardı</h2>
<p>İXTİF EFL503 HV-6 - 5.0 Ton Yüksek Voltaj Elektrikli Forklift, 5000 kg ton sınıfında yüksek voltajlı Li-ion mimariyi PMSM sürüş ile birleştirerek, ağır hizmet operasyonlarında hızlanma, dur kalk tepkisi ve rampa performansını yeniden tanımlar. 309V mimari ve gelişmiş BMS ile akü ömrü uzun, 1C hızlı şarj desteğiyle kesintiler kısadır. 600 mm mm yük merkeziyle dengeli taşıma sunarken, tek ön teker mimarisi ve kompakt şasi dar alan manevralarını kolaylaştırır.</p></section>
<section><h3>Teknik</h3>
<p>Gövde ve şasi tasarımında sağlamlık önceliklenmiştir; motor ve batarya için ayrı su soğutma devreleri, hidrolik için yağ soğutma ile sıcaklıklar optimumda tutulur. Yüklü/boş 24/25 km/s seyir hızına ulaşabilen sistem, mast amortisörü sayesinde yükleri sarsmadan kaldırır/indirir. VCU kontrollü dönüş hızı ve aşırı hız uyarısı, operatör güvenliği ve ekipman stabilitesini artırır. IPX4 genel koruma ve yüksek voltaj bileşenlerinde IP67, yağmur, çamur ve dış saha sıçramalarına karşı dayanım sağlar. Şarj tarafında araç tipi istasyonlara uyum ve 1-1.2 saat aralığında tam dolum vardiyalar arası verimi yükseltir.</p></section>
<section><h3>Sonuç</h3>
<p>Toplam sahip olma maliyetini düşüren düşük enerji tüketimi ve minimal bakım gereksinimi ile İXTİF EFL503 HV-6 - 5.0 Ton Yüksek Voltaj Elektrikli Forklift, şirketlerin yüksek hacimli taşıma gereksinimlerine temiz, hızlı ve güvenli bir çözüm sunar. Detaylı fiyatlama ve demo için 0216 755 3 555.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '5000 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '309V/173Ah'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '24/25 km/s'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş Yarıçapı', 'value' => '2730 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Yüksek Voltaj LFP Akü', 'description' => '309V/173Ah kapasite, 1C hızlı şarj uyumu ve düşük işletme maliyeti.'],
                ['icon' => 'bolt', 'title' => 'PMSM Sürüş Teknolojisi', 'description' => 'Yüksek verimli mıknatıslı senkron motor ile hızlı tepki ve çekiş.'],
                ['icon' => 'water', 'title' => 'Çift Su Soğutma', 'description' => 'Motor ve batarya için ayrı su soğutma; yaz sıcağında termal kararlılık.'],
                ['icon' => 'oil-can', 'title' => 'Hidrolik Yağ Soğutma', 'description' => 'Hidrolik devrede sıcaklık kontrolüyle uzun ömür ve kararlı performans.'],
                ['icon' => 'shield-alt', 'title' => 'VCU Güvenlik Denetimi', 'description' => 'Dönüş hız kontrolü ve aşırı hız uyarısı ile stabil sürüş.'],
                ['icon' => 'gauge', 'title' => '25 km/saate Kadar Hız', 'description' => 'Yüklü/boş 24/25 km/s seyir hızı ile yüksek verimlilik.'],
                ['icon' => 'mountain', 'title' => '%20-25 Tırmanma', 'description' => 'Rampa ve bozuk zeminde güvenli tırmanma kabiliyeti.'],
                ['icon' => 'leaf', 'title' => 'Sıfır Emisyon', 'description' => 'İç mekânda temiz hava, düşük gürültü ve sürdürülebilir operasyon.']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'industry', 'text' => 'Ağır sanayi üretim hücrelerinde yüksek kapasiteli palet hareketi'],
                ['icon' => 'car', 'text' => 'Otomotiv montaj hatlarında forklift besleme ve rampa çıkışları'],
                ['icon' => 'warehouse', 'text' => 'Açık alan stok sahalarında rampalı ve bozuk zemin sürüşü'],
                ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde hacimli yüklerin çapraz sevkiyatı'],
                ['icon' => 'snowflake', 'text' => 'Düşük sıcaklık koşullarında akü ısıtma ile kesintisiz çalışma'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde düşük gürültü ve sıfır emisyonla iç mekân operasyonları'],
                ['icon' => 'building', 'text' => 'İnşaat malzemelerinde ağır paletlerin saha içi transferi'],
                ['icon' => 'briefcase', 'text' => 'B2B dağıtım depolarında yoğun vardiya taşıma operasyonları']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Yüksek voltaj + PMSM mimari ile hızlanma ve tırmanma performansı'],
                ['icon' => 'battery-full', 'text' => '309V/173Ah LFP batarya ile 1-1.2 saatte tam şarj ve uzun çevrim ömrü (4000+)'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt gövde ve tek ön teker tasarımıyla dar alan manevrası'],
                ['icon' => 'shield-alt', 'text' => 'VCU denetimi, dönüşte hız kontrolü ve aşırı hız uyarısı ile güvenlik'],
                ['icon' => 'star', 'text' => 'IPX4 genel, HV bileşenlerde IP67 sızdırmazlık ile zorlu hava şartlarına uyum']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve fulfillment merkezleri'],
                ['icon' => 'warehouse', 'text' => '3PL ve sözleşmeli lojistik operasyonları'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim ürünleri (FMCG) depoları'],
                ['icon' => 'snowflake', 'text' => 'Gıda, içecek ve soğuk zincir depoları'],
                ['icon' => 'pills', 'text' => 'İlaç, medikal ve kozmetik lojistiği'],
                ['icon' => 'car', 'text' => 'Otomotiv ve yan sanayi üretim hatları'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim üretim/logistik'],
                ['icon' => 'industry', 'text' => 'Ağır sanayi ve metal işleme tesisleri'],
                ['icon' => 'flask', 'text' => 'Kimya ve petrokimya depolama alanları'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve beyaz eşya üretimi'],
                ['icon' => 'building', 'text' => 'İnşaat malzemeleri ve yapı market depoları'],
                ['icon' => 'briefcase', 'text' => 'B2B toptan dağıtım merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'İçecek ve şişe dolum depoları'],
                ['icon' => 'paw', 'text' => 'Evcil hayvan mama ve yem depoları'],
                ['icon' => 'print', 'text' => 'Ambalaj, baskı ve kağıt üretim alanları'],
                ['icon' => 'couch', 'text' => 'Mobilya ve marangozhane lojistiği'],
                ['icon' => 'hammer', 'text' => 'Makine ve ekipman imalatı'],
                ['icon' => 'seedling', 'text' => 'Tarım ve gübre depoları'],
                ['icon' => 'tv', 'text' => 'Media/e-ticaret hub ve çapraz sevkiyat merkezleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Araç tipi hızlı şarj uyumu (1C)', 'description' => '309V mimari ile 1C hızında DC şarj istasyonlarına uyum.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Poliüretan dolgu lastikler', 'description' => 'Zorlu zeminlerde dayanım ve düşük yuvarlanma direnci.', 'is_standard' => true, 'price' => null],
                ['icon' => 'wrench', 'name' => 'Kabin klima ve ısıtma', 'description' => 'AC ve heater paketi ile 4 mevsim konfor.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'charging-station', 'name' => 'Harici hızlı şarj istasyonu', 'description' => 'Yüksek voltaj uyumlu harici DC şarj ünitesi.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Yüksek voltaj mimarisi günlük operasyonda bana hangi somut faydaları sağlar?', 'answer' => 'Daha yüksek motor beslemesi sayesinde kalkışlar daha atak, rampa çıkışları daha güvenli ve seyir hızı daha yüksek olur; bu da çevrim sürelerinde ölçülebilir düşüş getirir.'],
                ['question' => '1C hızlı şarj desteği vardiya planlamasını nasıl etkiler, akü soğukken performans düşer mi?', 'answer' => '309V LFP batarya 1C hızında 1-1.2 saatte tam dolabilir; şarj sırasında ısıtma fonksiyonu düşük sıcaklıkta akünün ideale yakın sıcaklık aralığında kalmasını sağlar.'],
                ['question' => 'PMSM motor ile gelen verimlilik avantajı bakım aralıklarına nasıl yansır?', 'answer' => 'Fırçasız ve basit rotor tasarımında sürtünme düşük, karbon parça yoktur; periyodik bakım daha seyrektir ve duruş süreleri azalır.'],
                ['question' => 'Su soğutma ve yağ soğutma sistemleri sıcak iklimlerde kapasite düşüşünü önler mi?', 'answer' => 'Motor/batarya su soğutması ve hidrolik yağ soğutma, yazın ısı yükünü kontrol altında tutar; performans düşüşünü ve termal limitlere takılmayı azaltır.'],
                ['question' => 'IPX4 ve IP67 koruma sınıfları dış saha uygulamalarında hangi koşulları kapsar?', 'answer' => 'IPX4 şasi yağmur sıçramalarına dirençlidir; yüksek voltaj bileşenlerinde IP67 suya/toza karşı yüksek sızdırmazlık sağlar.'],
                ['question' => 'Mast amortisörü kırılgan yüklerde hasar riskini somut olarak nasıl düşürür?', 'answer' => 'Kaldırma/indirme sonunda kontrollü yavaşlatma uygulayarak ani duruşları önler; yük ve ekipman üzerindeki şokları azaltır.'],
                ['question' => 'Aynı sınıftaki düşük voltajlı modellere göre enerji tüketimi farkı nedir?', 'answer' => 'Yüksek voltaj + PMSM kombinasyonu, geleneksel AC sistemlere kıyasla yaklaşık %15 elektrik tasarrufu sağlayabilir.'],
                ['question' => 'Telemetri ve uzaktan destek arıza yönetiminde bana ne kazandırır?', 'answer' => 'Batarya ve performans parametreleri izlenir; uzman ekip çevrimiçi rehberlik ile arızayı hızla teşhis edip duruş süresini kısaltır.'],
                ['question' => 'Soğuk hava depolarında şarj sırasında batarya ısınması operasyonu etkiler mi?', 'answer' => 'Şarj esnasında devreye giren ısıtma, bataryayı verimli sıcaklıkta tutar; güvenli ve hızlı şarj devam eder.'],
                ['question' => 'Garanti kapsamında hangi parçalar bulunur ve süreler nelerdir?', 'answer' => 'Standart kapsamda makine 12 ay, Li-Ion batarya modülleri 24 ay garanti altındadır; kapsam üretim hatalarını ve normal kullanım şartlarını kapsar.'],
                ['question' => 'Satış sonrası yedek parça ve servis erişimi nasıldır?', 'answer' => 'Global stok ve yerel depo ile parça tedariği hızlandırılır; teknik dökümanlar ve çevrimiçi destekle kesinti riski azalır.'],
                ['question' => 'Fiyat teklifi ve demo için doğrudan kiminle iletişime geçmeliyim?', 'answer' => 'Teknik ekip ve satış birimiyle hızlıca eşleşmeniz için İXTİF hattı 0216 755 3 555 üzerinden bize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("ℹ️ Detailed güncellendi: EFL503-HV-6");
    }
}
