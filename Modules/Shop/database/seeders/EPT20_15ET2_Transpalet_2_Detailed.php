<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_15ET2_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPT20-15ET2')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF EPT20-15ET2: Hafif Gövdede Güçlü ve Ekonomik Taşıma</h2><p>İXTİF EPT20-15ET2, 1.5 ton kapasiteyi kompakt ve hafif gövde mimarisiyle sunar. 24V/65Ah Li-ion batarya, DC sürüş kontrolü ve PU teker kombinasyonu; iç lojistikte sessiz, ekonomik ve öngörülebilir bir operasyon sağlar. 4.5/5 km/sa hızlar ve 1475 mm dönüş yarıçapı dar koridor yaklaşımını kolaylaştırırken, 30 mm şase açıklığı eşikler ve pürüzlerde kontrollü geçişler sunar.</p></section><section><h3>Teknik</h3><p>EPT20-15ET2, 600 mm yük merkezinde 1500 kg taşıma kapasitesine sahiptir. 883/946 mm yük mesafesi, 1202/1261 mm dingil mesafesi ve 1638 mm toplam uzunluk ile raf aralarında güvenli manevra yapar. 50/150/1150 mm çatal ölçüsü standarttır; 800–2000 mm uzunluk ve 560/685/520/460/420/600 mm genişlik seçenekleriyle farklı palet tiplerine uyum sağlanır. Kaldırma/indirme hızları 0.027/0.038 ve 0.059/0.039 m/sn değerlerindedir. Sürüşte 0.75 kW, kaldırmada 0.84 kW motor kullanılır; elektromanyetik fren güvenli duruş karakteri sunar. 24V-10A dahili şarj ara şarja uygundur; batarya göstergesi zaman sayacı içerir. Opsiyon olarak iz bırakmayan Trace PU, tek yük tekeri, 75 mm kısa sürüş kolu, dikey kol modu ve elektrikli tartı, depolama kutusu, kargo sırtlığı gibi ataşmanlar mevcuttur.</p></section><section><h3>Sonuç</h3><p>Hızlı devreye alınan, düşük bakımlı ve bütçe dostu bir çözüm arıyorsanız EPT20-15ET2 dengeli mimarisiyle öne çıkar. Paketleme, besleme hatları ve cross-dock akışlarında pratik kullanım sunar. Teknik danışmanlık ve teklif için 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 65Ah Li-ion + 24V-10A dahili şarj'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 / 5 km/sa (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => 'Wa=1475 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-ion Verim', 'description' => '24V/65Ah ile hafif ve uzun ömürlü enerji yönetimi'],
                ['icon' => 'plug', 'title' => 'Dahili Şarj', 'description' => '24V-10A entegre şarj ile esnek ara şarj'],
                ['icon' => 'bolt', 'title' => 'Güç Dengesi', 'description' => '0.75 kW sürüş, 0.84 kW kaldırma gücü'],
                ['icon' => 'arrows-alt', 'title' => 'Dar Alan Çevikliği', 'description' => '1475 mm dönüş yarıçapı ile koridor uyumu'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Fren', 'description' => 'Elektromanyetik frenle öngörülebilir duruş'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'DC kontrol ve mekanik direksiyon']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Fulfillment ve paketleme alanlarında kısa mesafe palet transferi'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında cross-dock ve rampa yaklaşımı'],
                ['icon' => 'store', 'text' => 'Perakende DC raf arası ürün toplama'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında sessiz ve iz bırakmayan taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç üretim ve dağıtım alanlarında hassas akış'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça ve komponent beslemesi'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arası yarı mamul taşıma'],
                ['icon' => 'cart-shopping', 'text' => 'Mağaza arkası depo operasyonları']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Hafif servis ağırlığı (180 kg) ile düşük enerji tüketimi'],
                ['icon' => 'battery-full', 'text' => 'Li-ion batarya ve dahili şarj ile esnek vardiya yönetimi'],
                ['icon' => 'arrows-alt', 'text' => '1475 mm dönüş yarıçapı ve kompakt şasi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve PU tekerlerde güvenli yol tutuş'],
                ['icon' => 'cog', 'text' => 'DC sürüş ve mekanik direksiyonla basit servis'],
                ['icon' => 'cart-shopping', 'text' => 'Geniş çatal ölçü opsiyonları ile farklı palet uyumu']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi ve Üretim'],
                ['icon' => 'building', 'text' => 'Beyaz Eşya ve Tüketici Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Dağıtım'],
                ['icon' => 'box-open', 'text' => 'Ambalaj ve Baskı'],
                ['icon' => 'store', 'text' => 'Ev-Yaşam ve Mobilya Lojistiği'],
                ['icon' => 'cart-shopping', 'text' => 'Zincir Mağaza Depoları'],
                ['icon' => 'industry', 'text' => 'Yapı Market ve Hırdavat'],
                ['icon' => 'box-open', 'text' => 'Kargo, Parsel ve Posta'],
                ['icon' => 'warehouse', 'text' => 'Soğuk Oda Dışı Gıda Deposları'],
                ['icon' => 'microchip', 'text' => 'Telekom ve IT Donanımı'],
                ['icon' => 'flask', 'text' => 'Boyaa ve Kimyasal Yan Ürünler']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-ion batarya modülleri normal kullanım koşullarında 24 ay garanti kapsamındadır. Garanti, kullanıcı hatası dışındaki üretim kusurlarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V-10A Dahili Şarj Cihazı', 'description' => 'Makine üzerinde entegre; farklı priz noktalarında ara şarj olanağı.', 'is_standard' => true, 'price' => null],
                ['icon' => 'battery-full', 'name' => '85Ah Li-ion Batarya Paketi', 'description' => 'Yüksek çevrim ihtiyacı olan hatlar için artırılmış kapasite.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Zamanlı Batarya Göstergesi', 'description' => 'Vardiya planı ve kalan kapasite takibini kolaylaştırır.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cart-shopping', 'name' => 'Elektrikli Tartı Ataşmanı', 'description' => 'Taşıma sırasında tartım yaparak süreci hızlandırır.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'industry', 'name' => 'Kargo Sırtlığı', 'description' => 'Düşmeyi önleyen yük destek çözümü.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'box-open', 'name' => 'Depolama Kutusu', 'description' => 'Eldiven ve küçük ekipmanlar için pratik saklama alanı.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Kapasite hangi yük merkezinde geçerlidir?', 'answer' => 'Nominal 1500 kg kapasite 600 mm yük merkezinde geçerlidir.'],
                ['question' => 'Dönüş yarıçapı dar koridorlarda yeterli mi?', 'answer' => '1475 mm dönüş yarıçapı çoğu 2.2–2.3 m koridorda güvenli manevraya olanak tanır.'],
                ['question' => 'Hız değerleri operasyonu nasıl etkiler?', 'answer' => '4.5/5 km/sa hızlar kısa mesafe iç lojistikte akıcı bir tempo sağlar.'],
                ['question' => 'Hangi çatal ölçüleri mevcut?', 'answer' => 'Uzunluk 800–2000 mm, genişlik 560/685/520/460/420/600 mm seçenekleri vardır (1150×560 mm standarttır).'],
                ['question' => 'Zemin için hangi teker malzemesi önerilir?', 'answer' => 'Standart PU iç mekân için uygundur; iz bırakmayan Trace PU hijyen kritik alanlarda avantaj sağlar.'],
                ['question' => 'Fren sistemi nedir?', 'answer' => 'Elektromanyetik servis freni öngörülebilir ve dengeli duruş sağlar.'],
                ['question' => 'Tırmanma kabiliyeti yeterli mi?', 'answer' => '%5 yüklü, %16 yüksüz değerleri tipik rampa geçişlerini karşılar.'],
                ['question' => 'Sürüş kolu seçenekleri var mı?', 'answer' => 'Evet, standart kola ek olarak 75 mm kısa seçenek ve dikey kol modu mevcuttur.'],
                ['question' => 'Aksesuar opsiyonları nelerdir?', 'answer' => 'Elektrikli tartı, kargo sırtlığı, depolama kutusu, caster teker gibi seçenekler sunulur.'],
                ['question' => 'Ses seviyesi nedir?', 'answer' => 'Operatör kulak seviyesinde 74 dB(A) ölçülür.'],
                ['question' => 'Bakım sıklığı nasıldır?', 'answer' => 'DC sürüş ve mekanik direksiyon mimarisiyle bakım süreçleri basittir.'],
                ['question' => 'Garanti ve satış sonrası destek nasıl sağlanır?', 'answer' => 'Makine 12 ay, Li-ion batarya 24 ay garantilidir. İXTİF satış ve servis desteği için 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("✅ Detailed: EPT20-15ET2");
    }
}
