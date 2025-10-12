<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_15ET_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EPT20-15ET')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İXTİF EPT20-15ET: Günlük Palet Akışında Güvenli Ritm</h2><p>Lojistik operasyonlarda aradığınız şey çoğu zaman gösterişli rakamlardan çok, her vardiyada aynı istikrarı sunan bir iş ortağıdır. İXTİF EPT20-15ET tam da bu noktada devreye girer: 1.5 ton kapasiteyi kompakt bir gövdeyle buluşturur, dar koridorlarda çevik manevralar yapar ve kullanıcıyı yormayan yalın ergonomisiyle iş akışına uyum sağlar. 2×12V/85Ah akü mimarisi ve 24V-15A dahili şarj cihazı sayesinde enerji yönetimi sahada pratikleşir; gece boyunca dışarıda bekleyen ayrı bir şarj ünitesine ihtiyaç duymazsınız. Sessiz çalışan PU tekerlekler ve elektromanyetik fren, hem güvenliği hem de operatör konforunu aynı anda destekler. Kısacası EPT20-15ET, karmaşık çözümlerle değil, doğru boyutlandırılmış tekniklerle verimliliği kalıcı hale getirir.</p></section><section><h3>Teknik</h3><p>Ekipman, yaya kumandalı elektrikli sürüş yapısıyla 4/4.5 km/sa hızlara ulaşır; vardiya içi kısa mesafe taşımalarında ideal ritmi yakalar. 1485 mm dönüş yarıçapı, 1632 mm toplam uzunluk ve 560/685 mm şasi genişliği seçenekleriyle depo raf aralarında güvenli mesafe bırakır. 50/150/1150 mm çatal ölçüsü standart olarak gelir ve 1000 ile 1220 mm uzunluk alternatifleri mevcuttur. 600 mm yük merkezinde 1500 kg taşıma kapasitesi sunarken, 0.022/0.025 m/sn kaldırma ve 0.034/0.023 m/sn indirme hızları ile ürün alma-bırakma döngülerini hızlandırır. Elektriksel tarafta DC sürüş kontrolü ve 0.65 kW sürüş motoru bulunur; 0.84 kW kaldırma motoru ile paletleri kontrollü şekilde yükseltir. 2×12V/85Ah aküler toplam 24V gerilim sağlar; akü modülleri 25.5×2 kg ağırlığıyla gövde dengesi korunarak konumlandırılmıştır. Standart konfigürasyonda çift yük tekeri ve PU malzeme kullanılır; isteğe bağlı iz bırakmayan tahrik tekeri, yardımcı caster tekerleri ve dikey taşıma kolu çalışma modu sahaya göre özelleştirme imkânı verir. Güvenlikte elektromanyetik servis freni öne çıkarken, 74 dB(A) ses seviyesi operatör için konforlu bir çalışma ortamı oluşturur. %5/%16 tırmanma kabiliyeti, rampa geçişleri ve yükleme alanlarında kontrollü hareket sağlar. Dahili 24V-15A şarj cihazı makine üzerinde korumalı konumlandırılmıştır; bu sayede tesis içinde farklı priz noktalarında hızlı ara şarj yapılabilir. Zaman sayacı içermeyen standart batarya göstergesi, istenirse zamanlı seçenekle yükseltilebilir.</p></section><section><h3>Sonuç</h3><p>Eğer amacınız, bakım yükü düşük, yedek parça ihtiyacı basit ve kullanımı sezgisel bir transpaletle vardiya verimini artırmaksa, İXTİF EPT20-15ET dengeli teknikleriyle doğru tercih olur. Kompakt şasi, DC kontrol ve PU tekerlek kombinasyonu; paketleme, cross-dock ve besleme hatlarında akıcı bir malzeme akışı sunar. Teknik danışmanlık ve teklife hemen ulaşmak için 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '2×12V/85Ah (24V) + 24V-15A dahili şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4 / 4.5 km/sa (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => 'Wa=1485 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Dahili Şarj', 'description' => '24V-15A entegre şarj cihazı ile esnek ara şarj imkânı'],
                ['icon' => 'weight-scale', 'title' => '1.5 Ton Kapasite', 'description' => '600 mm yük merkezinde güvenli taşıma performansı'],
                ['icon' => 'compress', 'title' => 'Kompakt Ölçüler', 'description' => '1632 mm uzunluk ve 1485 mm dönüş yarıçapı'],
                ['icon' => 'circle-notch', 'title' => 'PU Tekerler', 'description' => 'Sessiz ve titreşimi azaltan poliüretan yapı'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Hızlı ve dengeli duruş karakteri'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'DC kontrol ve mekanik direksiyonla basit servis']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Fulfillment alanlarında kısa mesafe palet transferi ve sipariş besleme'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında cross-dock ve rampa yaklaşımı'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde raf arası ürün toplama ve taşıma'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında giriş-çıkış hattında sessiz taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik hatlarında hassas koli paletleme'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça lojistiğinde WIP besleme'],
                ['icon' => 'tshirt', 'text' => 'Tekstil paketleme istasyonlarında koli akışı'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında yarı mamul taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'DC sürüş ve 0.65 kW motor ile dengeli güç–verim optimizasyonu'],
                ['icon' => 'battery-full', 'text' => '2×12V/85Ah akü ve entegre 24V-15A şarj ile esnek enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => '1485 mm dönüş yarıçapıyla dar koridor uyumu'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve PU tekerlerde güvenli yol tutuş'],
                ['icon' => 'layer-group', 'text' => '560/685 mm çatal genişliği ve 3 farklı çatal uzunluğu seçeneği'],
                ['icon' => 'shipping-fast', 'text' => 'Hızlı kurulum, basit eğitim ve düşük toplam sahip olma maliyeti']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Batarya modülleri normal kullanım koşullarında 24 ay garanti kapsamındadır. Garanti, kullanıcı hatası dışındaki üretim kusurlarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V-15A Dahili Şarj Cihazı', 'description' => 'Makine üzerinde entegre şarj; farklı priz noktalarında hızlı ara şarj olanağı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Zamanlı Batarya Göstergesi', 'description' => 'Çalışma süresini takip eden gösterge ile vardiya planlamasını kolaylaştırır.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Yardımcı Caster Teker Seti', 'description' => 'Zemin düzensizliğinde yön stabilitesini artıran ek teker çözümü.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'İz Bırakmayan Tahrik Teker', 'description' => 'Trace PU malzeme ile kapalı alanlarda zemin lekelenmesini azaltır.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Maksimum kapasite hangi yük merkezinde geçerlidir ve sınırları nedir?', 'answer' => '1500 kg kapasite 600 mm yük merkezinde geçerlidir. Yük merkezi uzadıkça taşıma dayanımı düşer; güvenli çalışma için palet dağılımını dengeleyin.'],
                ['question' => 'Makinenin yürüyüş hızları operasyon akışını nasıl etkiler?', 'answer' => '4/4.5 km/sa hız profili kısa mesafe iç lojistik için yeterlidir. Operatör kontrolü önceliklidir; güvenlik alanları içinde hız korumalıdır.'],
                ['question' => 'Dönüş yarıçapı ve şasi ölçüleri dar koridorlarda ne sağlar?', 'answer' => '1485 mm dönüş yarıçapı ve 1632 mm toplam uzunluk, 2.2–2.3 m koridorlarda emniyetli manevra olanağı sunar. Raf ve köşe yaklaşımında görüşe dikkat edin.'],
                ['question' => 'Akü sistemi ve şarj altyapısı nasıl yapılandırılmıştır?', 'answer' => '2×12V/85Ah kurulum makine üzerinde bulunur. 24V-15A dahili şarj, tesis genelindeki prizlerle ara şarjı destekler; bakım gereksinimi düşüktür.'],
                ['question' => 'Zemin türüne göre hangi teker kombinasyonu önerilir?', 'answer' => 'PU tekerler standarttır ve iç mekân için idealdir. İz bırakmayan tahrik tekeri kapalı alan hijyeninde avantaj sağlar; düzensiz zeminlerde caster opsiyonu düşünülür.'],
                ['question' => 'Tırmanma kabiliyeti rampa ve yükleme alanlarında yeterli midir?', 'answer' => '%5 yüklü ve %16 yüksüz kabiliyet, tipik rampa geçişlerini karşılar. Süreklilikte hız düşüşü normaldir; güvenlik için hızlanmayı kademeli yapın.'],
                ['question' => 'Frenleme sistemi operatör güvenliğini nasıl destekler?', 'answer' => 'Elektromanyetik servis freni bırakma anında etkinleşerek kontrollü duruş sağlar. Eğimli zeminlerde paleti bırakmadan önce konum sabitlemesi yapın.'],
                ['question' => 'Standart ve opsiyonel çatal ölçü seçenekleri nelerdir?', 'answer' => 'Uzunlukta 1000/1150/1220 mm, genişlikte 560/685 mm seçenekleri mevcuttur. Palet tipine göre seçim operasyon verimini artırır.'],
                ['question' => 'Ses seviyesi ve titreşim değerleri operatör konforunu nasıl etkiler?', 'answer' => '74 dB(A) değer, iç mekân kabul seviyelerindedir. PU tekerler titreşimi sönümler; doğru zemin bakımı konforu artırır.'],
                ['question' => 'Bakım aralıkları ve yedek parça erişimi açısından neler öne çıkar?', 'answer' => 'Mekanik direksiyon ve DC kontrol mimarisiyle bakım döngüleri basittir. Sarf kalemlerine erişim kolaydır; planlı kontrollerle arıza riski azalır.'],
                ['question' => 'Opsiyonel tartı sistemi hangi durumlarda fayda sağlar?', 'answer' => 'Sevkiyat öncesi hızlı doğrulama ve aşırı yükün önlenmesi için uygundur. Süreç kalite kayıtlarını desteklemek üzere kalibrasyon disiplini önerilir.'],
                ['question' => 'Garanti kapsamı ve satış sonrası destek nasıl sağlanır?', 'answer' => 'Makine 12 ay, batarya modülleri 24 ay garantilidir. İXTİF satış, servis, kiralama ve parça desteği için 0216 755 3 555 üzerinden bize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("✅ Detailed: EPT20-15ET");
    }
}
