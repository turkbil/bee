<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES16_RS_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES16-RS')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ES16-RS');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>İXTİF ES16-RS: Dar Koridorlarda Güç, Hız ve Hassasiyet</h2>
  <p>İXTİF ES16-RS, modern depoların ritmine ayak uydurmak için tasarlanmış ayakta kullanım istif makinesidir. Sabah vardiyasından gece kapanışına kadar artmayan yorgunluk, azalan çevrim süresi ve yüksek raflarda güven veren stabilite vadeder. 1.6 ton nominal kapasite, 600 mm yük merkezi ve elektronik direksiyonla birlikte gelen çevik manevra kabiliyeti, operatör yorgunluğunu azaltırken istif kalitesini standartlaştırır. 24V/210Ah akü altyapısı, yan çekme özelliği sayesinde vardiyalar arasında hızlı akü değişimi sağlar; iki kademeli alçaltma fonksiyonu ise palet inişlerinde ürün bütünlüğünü korur ve raf hasarlarını önler.</p>
</section>
<section>
  <h3>Teknik Güç ve Operasyonel Verimlilik</h3>
  <p>ES16-RS, 3.0 kW kaldırma motoru ve 1.6 kW sürüş motorundan aldığı güçle 5.5/6.0 km/s hızlara ulaşır. 0.13/0.16 m/s kaldırma hızı ve 0.30/0.22 m/s indirme hızı, yoğun vardiyalarda dakikalar içinde fark yaratır. 1375 mm dingil mesafesi ve 850 mm şasi genişliği, 1730/2090 mm dönüş yarıçapı ile birleşerek sıkışık alanlarda akıcı hareket sağlar. Poliüretan (PU) tekerlekler, düşük yuvarlanma direnci ve sessiz çalışma sunar. Direk kapalı yüksekliği 2020 mm ve 3000 mm’e kadar kaldırma, standart uygulamalarda optimum aralığı yakalar; üç kademeli serbest kaldırmalı direk seçenekleri 4500–5500 mm’e kadar uzanarak karma operasyonlara uyum sağlar. Elektromanyetik fren sistemi ve 74 dB(A) ses seviyesi, güvenlik ve konfor dengesini korur. 28 mm şasi altı boşluk ve 88 mm çatal altı yüksekliği, palete giriş-çıkışta nazik temas sağlar.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>ES16-RS, standartlaştırılmış kalite, tahmin edilebilir çevrim süreleri ve düşük toplam sahip olma maliyeti için tasarlanmış güvenilir bir platformdur. Yan çekmeli akü yapısı, harici şarj cihazı desteği ve elektronik direksiyon; hem ekipman kullanılabilirliğini hem de operatör güvenini artırır. Projenize uygun direk, çatal ölçüsü ve akü varyantlarıyla yapılandırılabilen bu model, büyüyen depo hatlarına hızlıca entegre olur. Detaylı teklif ve teknik danışmanlık için <strong>0216 755 3 555</strong> numarasından bize ulaşın.</p>
</section>
'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1600 kg (Q)'],
                ['icon' => 'battery-full', 'label' => 'Akü', 'value' => '24V / 210Ah (opsiyonlar mevcut)'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '5.5 / 6.0 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1730 / 2090 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'İki Kademeli Alçaltma', 'description' => 'Palet inişinde sarsıntıyı azaltır, raf ve ürün hasarını önler.'],
                ['icon' => 'battery-full', 'title' => 'Yan Çekmeli Akü', 'description' => 'Vardiya aralarında hızlı akü değişimiyle kesintisiz operasyon.'],
                ['icon' => 'bolt', 'title' => 'Güçlü Tahrik', 'description' => '3.0 kW kaldırma + 1.6 kW sürüş ile seri çevrim süreleri.'],
                ['icon' => 'warehouse', 'title' => 'Dar Koridor Uyumlu', 'description' => '850 mm şasi ve kompakt iz genişliği ile çevik manevra.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenlik Standartları', 'description' => 'Elektromanyetik fren ve elektronik direksiyon.'],
                ['icon' => 'star', 'title' => 'PU Tekerler', 'description' => 'Sessiz sürüş ve düşük yuvarlanma direnci.']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Yoğun hacimli depolarda raf arası istif ve toplama beslemesi'],
                ['icon' => 'box-open', 'text' => 'E-ticaret sipariş merkezlerinde giriş-çıkış palet akışı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında mağaza sevkiyat hazırlığı'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda girişi-çıkışı arasında kısa mesafe transfer'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas ürünlerin nazik istifi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça hatlarında WIP ve rampa yaklaşımı'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında yarı mamul taşıma ve tampon stok'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim koli paletleme operasyonları']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '5.5/6.0 km/s hız ve güçlü motorlar ile daha kısa çevrim süresi'],
                ['icon' => 'battery-full', 'text' => 'Yan çekmeli akü ve harici şarj ile yüksek kullanılabilirlik'],
                ['icon' => 'arrows-alt', 'text' => 'İki kademeli alçaltma ile raf ve ürün koruması'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve elektronik direksiyon güvenliği'],
                ['icon' => 'warehouse', 'text' => '850 mm şasi genişliği ile dar alanlarda çeviklik']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG ve Hızlı Tüketim'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Depolama ve Dağıtım'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Hammadde Depoları'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion veya kurşun-asit akü modülleri, üretim hatalarına karşı 24 ay garanti kapsamındadır. Garanti normal kullanım koşullarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V-30A Harici Şarj Cihazı', 'description' => 'Standart şarj altyapısı; vardiyalar arasında güvenli ve düzenli şarj döngüsü.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'PU Teker Seti (Tandem)', 'description' => 'Sessiz çalışma ve düşük yuvarlanma direnci için poliüretan yük tekerleri.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'grip-lines-vertical', 'name' => 'Akü Yan Çekme Arabası', 'description' => 'Yan çekmeli aküyü hızlı ve emniyetli biçimde çıkarmaya yardımcı aparat.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Oransal Kaldırma Sistemi', 'description' => 'Hassas hız kontrolü ile yükü milimetrik konumlandırma imkânı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'ES16-RS’nin nominal kapasitesi ve yük merkezi kaçtır, hangi paletlerde idealdir?', 'answer' => 'Nominal kapasite 1600 kg, yük merkezi 600 mm’dir. Standart EUR ve ISO paletler için uygundur; 60×190×1150 mm çatal ölçüsü standarttır.'],
                ['question' => 'Maksimum kaldırma yüksekliği ve direk seçenekleri nelerdir?', 'answer' => 'Standartta 3000 mm’dir. İki ve üç kademeli direklerle 3300–5500 mm aralığında seçenekler mevcuttur; serbest kaldırma değerleri uygulamaya göre değişir.'],
                ['question' => 'Sürüş ve kaldırma hızları yoğun depolarda nasıl bir verim sağlar?', 'answer' => '5.5/6.0 km/s sürüş, 0.13/0.16 m/s kaldırma; çevrim sürelerini düşürerek daha fazla paletin güvenle hareket etmesini sağlar.'],
                ['question' => 'Elektronik direksiyon ve elektromanyetik fren hangi güvenlik faydalarını sağlar?', 'answer' => 'Elektronik direksiyon hassas manevra, elektromanyetik fren ise tutarlı duruş mesafesi ve eğimde güvenli park sağlar.'],
                ['question' => 'Akü sistemi neler sunar, vardiya değişimlerinde süreç nasıldır?', 'answer' => '24V/210Ah akü yan çekme yapısıyla hızlı değişir. Harici şarj cihazı düzenli şarj akışını destekler; opsiyonel daha yüksek kapasiteler mevcuttur.'],
                ['question' => 'Şasi ve iz genişliği dar koridorlarda ne avantaj sağlar?', 'answer' => '850 mm şasi, 574 mm ön iz ile raf aralarında çevik hareket; 1730/2090 mm dönüş yarıçapıyla sıkışık noktalarda hız kazandırır.'],
                ['question' => 'Yokuş performansı ve rampa yaklaşımı değerleri nelerdir?', 'answer' => 'Maksimum eğim %8 (yüklü) ve %16 (boş). Kısa rampalarda güvenli yaklaşım ve kontrollü iniş/çıkış sağlar.'],
                ['question' => 'Gürültü seviyesi ve titreşim açısından operatör konforu nasıldır?', 'answer' => '74 dB(A) ses seviyesi ve PU tekerler ile düşük titreşim; vardiya boyunca konforlu kullanım sağlar.'],
                ['question' => 'Bakım periyotları ve sarf kalemleri açısından beklenti nedir?', 'answer' => 'AC sürüş kontrolü ve kapalı motor yapıları düşük bakım ihtiyacı sunar. Teker ve fren sarfları düzenli kontrolde uzun ömürlüdür.'],
                ['question' => 'Standart çatal ölçüsü dışında hangi seçenekler bulunur?', 'answer' => '570×1220 mm çatal uzunluğu ve 685 mm çatallar arası mesafe seçenekleri mevcuttur; uygulamaya göre özelleştirme yapılabilir.'],
                ['question' => 'Uzaktan izleme ve aksesuar seçenekleri nelerdir?', 'answer' => 'Uzaktan izleme, oransal kaldırma gibi opsiyonlar eklenebilir. Akü arabası gibi yardımcı ekipmanlar operasyonu hızlandırır.'],
                ['question' => 'Garanti kapsamı ve satış-sonrası destek nasıl işler?', 'answer' => 'Makine 12 ay, akü 24 ay garantilidir. Satış, kiralama ve servis için İXTİF destek hattı: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: ES16-RS');
    }
}
