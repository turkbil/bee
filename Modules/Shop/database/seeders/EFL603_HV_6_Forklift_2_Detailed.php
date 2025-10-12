<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL603_HV_6_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL603-HV-6')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL603-HV-6'); return; }
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İXTİF EFL603 HV: Ağır Hizmette Sessiz Güç</h2><p>Günün ilk vardiyası daha mesaiye başlamadan kapı önünde bekleyen ağır paletler, açık alanda yağmurla parlayan zemin ve sıkışık zaman pencereleri… Bu sahnede İXTİF EFL603 HV sahneye çıktığında, 6 tonluk yükleri yüksek hız ve sakinlikle taşırken, dizel gürültüsünün yerini düşük sesli, titreşimsiz bir elektrikli performans alır. Yüksek voltajlı Li-ion mimarisi, hızlı tepki veren PMSM motoruyla birleşerek operatörün gazı açtığı anda akıcı bir ivmelenme sunar. Büyük kütleleri düşük manevra alanlarında güvenle döndürmek, rampaları hız kesmeden tırmanmak ve dar koridorları tek seferde dönmek için tasarlanmıştır.</p></section><section><h3>Teknik Güç ve Süreklilik</h3><p>EFL603 HV; 309V 228Ah LFP batarya, 60 kW PMSM sürüş motoru ve 2×27.8 kW kaldırma motoru ile endüstriyel tempoya ayarlanmış bir güç paketidir. 25/26 km/s seyir hızıyla sahada hızlı akış sağlar; %30/%34 eğim kabiliyeti, rampalarda ve engebeli zeminlerde ivmeyi korur. Su soğutmalı motor ve batarya ile yağ soğutmalı hidrolik sistem, yaz sıcağında bile termal kararlılığı koruyarak verimi düşüren limitlere yaklaşmayı engeller. IPX4 genel koruma ve yüksek voltaj komponentlerinde IP67 seviyesinde sızdırmazlık, sağanak yağmur, sıçrayan su ve zorlu dış ortam koşullarında kesintisiz çalışmayı destekler. 1C hızlı şarj sayesinde tam dolum yaklaşık 1–1.2 saat sürer; bu da planlı molalarla çok vardiyalı işletmelerde batarya değişimi ihtiyacını ortadan kaldırır ve toplam sahip olma maliyetini düşürür. VCU (Araç Kontrol Ünitesi), hız/kalkış eğrilerini optimize eder, dönüş hızını açıya göre sınırlar ve aşırı hız uyarısı ile güvenliği artırır. Mast üzerindeki hidrolik tamponlama ise yükü sarsmadan kaldırıp indirerek hem operatör konforunu hem de ekipman ömrünü uzatır.</p><p>1220 mm standart çatal, 1845 mm geniş çatal taşıyıcı ve 2028 mm gövde genişliği, iri yüklerin stabil şekilde kavranmasını destekler. 3235 mm dönüş yarıçapı ve yüksek yer açıklığı (m2=265 mm) sahada pratik manevra sağlar. Pnömatik lastikler (8.25-15-14PR) dış mekânda tutuşu artırır. Li-ion teknolojisi; 4000 döngüye varan hücre ömrü, kurşun-asit akülere kıyasla daha düşük enerji kaybı ve bakım ihtiyacı ile filo verimliliğinde sürdürülebilir bir zemin sunar. PMSM’nin fırçasız tasarımı, mekanik aşınmayı azaltır; servis aralıklarını seyrelterek plan dışı duruş riskini minimize eder.</p></section><section><h3>Sonuç ve İletişim</h3><p>İXTİF EFL603 HV; metal işleme, inşaat, dökümhane, geri dönüşüm ve liman sahaları gibi ağır uygulamalarda yüksek hız, dayanıklılık ve düşük işletme maliyetini aynı gövdede birleştirir. Yağmur, çamur, yüksek ısı ve eğimli zeminler gibi zorlayıcı şartlarda bile akıcı hızlanma, güçlü kaldırma ve kontrollü frenleme ile iş akışını hızlandırır. Projenizin kapasite, ataşman ve direk yapılandırmaları için uzman ekibimizle birlikte doğru kombinasyonu belirleyelim. Detaylı bilgi ve keşif için 0216 755 3 555.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '6000 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '309V / 228Ah LFP, 1C hızlı şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '25/26 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '3235 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Yüksek Voltaj Li‑ion', 'description' => '309V LFP batarya ile uzun süreli çalışma ve düşük enerji maliyeti.'],
                ['icon' => 'microchip', 'title' => 'PMSM + VCU Kontrol', 'description' => 'Hızlı tepki, hassas çekiş ve dönüş hız sınırlandırma.'],
                ['icon' => 'cog', 'title' => 'Gelişmiş Soğutma', 'description' => 'Motor/batarya su, hidrolik yağ soğutma ile termal kararlılık.'],
                ['icon' => 'bolt', 'title' => '1C Hızlı Şarj', 'description' => 'Planlı molalarda ~1–1.2 saatte tam dolum.'],
                ['icon' => 'shield-alt', 'title' => 'Çok Katmanlı Güvenlik', 'description' => 'Aşırı akım/sıcaklık ve kısa devre korumaları.'],
                ['icon' => 'star', 'title' => 'Mast Tamponlama', 'description' => 'Yükü sarsmadan kaldırma ve indirme konforu.']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'industry', 'text' => 'Ağır sanayi sahalarında 6 tona kadar bobin, kalıp ve kalın levha taşıma'],
                ['icon' => 'building', 'text' => 'Şantiye ve prefabri̇k üretimde dış mekân palet hareketleri'],
                ['icon' => 'warehouse', 'text' => 'Depo rampalarında yoğun yükleme-boşaltma operasyonları'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde yüksek ısıya yakın bölgelerde malzeme akışı'],
                ['icon' => 'car', 'text' => 'Otomotiv yan sanayide ağır parça ve ekipman lojistiği'],
                ['icon' => 'snowflake', 'text' => 'Değişken hava koşullarında dış ortam operasyonları'],
                ['icon' => 'box-open', 'text' => 'Büyük hacimli paletlerin hızlı iç/dış transferi'],
                ['icon' => 'briefcase', 'text' => 'Kamu altyapı ve hizmet sahalarında ağır yük taşımaları']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Dizel eşdeğerlerini yakalayan hızlanma ve tırmanma performansı'],
                ['icon' => 'battery-full', 'text' => '1C hızlı şarj ve 15%’e varan enerji tasarrufu ile düşük TCO'],
                ['icon' => 'arrows-alt', 'text' => 'Yüksek yer açıklığı ve pnömatik lastik ile dış mekânda üstün çekiş'],
                ['icon' => 'shield-alt', 'text' => 'IPX4/IP67 koruma ve kapsamlı elektronik güvenlik katmanları'],
                ['icon' => 'star', 'text' => 'Mast tamponlama ile yük ve ekipman ömrünün korunması']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'industry', 'text' => 'Ağır Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya ve Petrokimya'],
                ['icon' => 'car', 'text' => 'Otomotiv ve Yan Sanayi'],
                ['icon' => 'warehouse', 'text' => '3PL Lojistik ve Depolama'],
                ['icon' => 'box-open', 'text' => 'E-ticaret Megadepoları'],
                ['icon' => 'building', 'text' => 'İnşaat ve Şantiye Lojistiği'],
                ['icon' => 'briefcase', 'text' => 'Kamu ve Altyapı Projeleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Dağıtım Merkezleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir Dışı Alanlar'],
                ['icon' => 'flask', 'text' => 'Geri Dönüşüm ve Atık İşleme'],
                ['icon' => 'industry', 'text' => 'Dökümhane ve Metal İşleme'],
                ['icon' => 'building', 'text' => 'Prefabrik ve Ağır Modüler Üretim'],
                ['icon' => 'car', 'text' => 'Ağır Servis ve Bakım Tesisleri'],
                ['icon' => 'warehouse', 'text' => 'Liman Saha Operasyonları'],
                ['icon' => 'box-open', 'text' => 'Büyük Hacimli Ambalaj ve Paletleme'],
                ['icon' => 'building', 'text' => 'Enerji Santrali Sahaları'],
                ['icon' => 'industry', 'text' => 'Kâğıt ve Selüloz Tesisleri'],
                ['icon' => 'flask', 'text' => 'Maden Zenginleştirme Yan Tesisleri'],
                ['icon' => 'warehouse', 'text' => 'Demir-Çelik Depoları'],
                ['icon' => 'briefcase', 'text' => 'Belediye ve Kamu Depoları']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay boyunca üretim hatalarına karşı garanti kapsamındadır. Li‑Ion batarya modülleri ise 24 ay garanti altındadır. Garanti, kullanım kılavuzuna uygun şartlarda geçerlidir.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Araç Tipi Hızlı Şarj Soketi', 'description' => '1C şarja uygun araç tipi bağlantı donanımı', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Akü Isıtma Modu', 'description' => '0°C altındaki şarjlarda otomatik ısıtma fonksiyonu', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Fork Pozisyonlayıcı', 'description' => 'Pim tip çatal ile farklı yük genişliklerine hızlı uyum', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Geri Görüş Kamera ve Radar', 'description' => 'Operatör güvenliği için ekranlı geri görüş paketi', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Günlük iki vardiyada şarj planı nasıl olmalı, batarya ömrünü etkiler mi?', 'answer' => '1C hızlı şarjla planlı mola aralarında takviye şarj yapılabilir. LFP kimya ve gelişmiş BMS, döngü ömrünü korurken fırsat şarjını destekler.'],
                ['question' => 'Yüksek ısıya yakın alanlarda termal yönetim yeterli güvenliği sağlar mı?', 'answer' => 'Motor ve batarya su soğutma, hidrolikte yağ soğutma ile ısıyı hızlı uzaklaştırır. VCU sıcaklıkları izler, limitlerde gücü güvenli şekilde sınırlar.'],
                ['question' => 'Dış mekânda yağmur altında çalışırken elektronik aksam korunur mu?', 'answer' => 'Araç genelinde IPX4, yüksek voltaj bileşenlerinde IP67 koruma bulunur. Yağış ve sıçrayan suya karşı sızdırmazlık sahada sürekliliği destekler.'],
                ['question' => 'Dizel muadillere göre çekiş ve hızlanma performansı nasıldır?', 'answer' => 'Yüksek voltaj + PMSM kombinasyonu, yüksek tork ve hızlı cevap ile dizel eşdeğerlerine yakın hızlanma ve sürat değerleri sunar.'],
                ['question' => 'Rampa ve eğimli zeminlerde hız düşümü yaşanır mı, maksimum eğim nedir?', 'answer' => 'Yüklü %30, boşta %34 tırmanma kabiliyeti mevcuttur. PMSM torku ve çekiş, eğimde hızın korunmasına yardımcı olur.'],
                ['question' => 'Mast hareketleri yük güvenliği açısından ne gibi korumalar içeriyor?', 'answer' => 'Hidrolik tamponlama ile kaldırma/indirme sonlarında sarsıntı azalır. Bu, yük hasarını ve ekipman aşınmasını azaltır.'],
                ['question' => 'Bakım periyotları ve tipik bakım kalemleri nelerdir?', 'answer' => 'Fırçasız PMSM motor ve kapalı Li‑Ion paket düşük bakım gerektirir. Periyodik kontroller, hidrolik yağ/filtre ve soğutma devrelerinin kontrolünü içerir.'],
                ['question' => 'Kurşun-asit aküden geçişte enerji ve maliyet kazanımları nedir?', 'answer' => 'PMSM ve HV mimari toplamda yaklaşık %15 elektrik tasarrufu sunar. Fırsat şarjla üretkenlik artar, batarya değişimi ihtiyacı kalkar.'],
                ['question' => 'Operatör güvenliği açısından hız ve dönüş yönetimi nasıl işliyor?', 'answer' => 'VCU dönüş açısına bağlı hız sınırlaması ve aşırı hız uyarısı sağlar. Parametreler fabrika ayarlı olup güvenli çalışma aralığı korunur.'],
                ['question' => 'Ataşman entegrasyonu (pozisyonlayıcı vb.) kapasiteyi nasıl etkiler?', 'answer' => 'Ataşman ağırlığı ve öne uzaması efektif kapasiteyi düşürebilir. Doğru mast/ataşman kombinasyonu için uygulama analizi önerilir.'],
                ['question' => 'Soğuk hava ve don koşullarında şarj ve kullanımda neye dikkat etmeli?', 'answer' => 'Şarj sırasında otomatik akü ısıtma devreye girer. Hücreler optimum sıcaklığa geldiğinde şarj güvenli hızda devam eder.'],
                ['question' => 'Satın alma ve satış sonrası destek kanalları neler, kimle iletişim kurarım?', 'answer' => 'Satış, yedek parça ve teknik destek için İXTİF ekibine 0216 755 3 555 üzerinden ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: EFL603-HV-6');
    }
}
