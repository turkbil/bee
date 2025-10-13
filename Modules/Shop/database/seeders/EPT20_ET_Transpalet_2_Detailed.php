<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_ET_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPT20-ET')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EPT20-ET');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF EPT20 ET: Şehrin temposuna ayak uyduran 2.0 tonluk güç</h2><p>Kalabalık bir şehir deposunda gün henüz aydınlanmadan operasyon başlar. Rampalar, yol tümsekleri, dar yükleme alanları ve ardı ardına gelen teslimatlar arasında, ekipler sizden hem çeviklik hem de dayanıklılık bekler. İXTİF EPT20 ET tam da bu sahneler için tasarlandı: 48V elektronik mimari ve fırçasız kalıcı mıknatıslı DC motoru ile enerjiyi verimli kullanır, 2.0 tonluk yükleri güvenle taşırken 80 mm alçaltılmış yükseklik ve 140 mm kaldırma mesafesi sayesinde farklı palet tiplerine esnek uyum sağlar. Pimli sökülebilir tiller, nakliyeden montaja kadar süreyi kısaltır; tek dokunuşla açılan kapak ve tek somunla değişen tahrik tekeri ise bakım duruşlarını dakikalara indirir.</p></section><section><h3>Teknik</h3><p>EPT20 ET, 2000 kg kapasite ve 600 mm yük merkeziyle sınıfında güçlü bir denge sunar. 48V (12V×4) 30Ah AGM batarya paketi, iç entegre 48V-5A şarj seçeneği ve süre göstergeli batarya indikatörüyle vardiya planlamasını kolaylaştırır. 0.75 kW S2 sürüş motoru ve 0.84 kW S3 kaldırma motoru, 4/5.5 km/s yürüyüş hızına, 0.018/0.037 m/s kaldırma ve 0.032/0.038 m/s indirme hızlarına ulaşır. Şok emici bağlantı sistemi bozuk zeminde titreşimi sönümler, büyük tahrik tekeri ve opsiyonel döner yük tekeri ise 100 mm engelleri aşmayı kolaylaştırır. 1550 mm dönüş yarıçapı, 1685 mm toplam uzunluk ve 560/685 mm genişlik değerleri dar koridorlarda manevrayı mümkün kılar. 80 mm alçaltılmış yükseklik ile 140 mm kaldırma, EUR 1150 mm çatal ölçüleri ve 560/685 mm çatallar arası mesafe ile yaygın paletlerde sorunsuz giriş-çıkış sağlar. Elektromanyetik servis freni ve DC sürüş kontrolü güvenliği artırırken, mekanik direksiyon basit ve güvenilir bir hissiyat sunar. 74 dB(A) ses seviyesi gürültü duyarlılığı olan alanlarda konforlu çalışmayı destekler.</p></section><section><h3>Sonuç</h3><p>Şehir içi lojistikten 3PL merkezlerine, perakende arka depolardan dağıtım hub\'larına kadar EPT20 ET; düşük servis ağırlığı (220 kg), yüksek geçiş kabiliyeti ve modüler bakım kolaylığıyla operasyon maliyetlerini düşürür, vardiya sürekliliğini artırır. Projenize en doğru çatal genişliği ve teker konfigürasyonu ile sahaya hızlı devreye almak için bize ulaşın: 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (Q)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V 30Ah AGM (12V×4)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4 / 5.5 km/s (yüklü / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1550 mm Wa']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '48V elektronik mimari', 'description' => 'Verimlilik ve güvenilirlik artışıyla uzun vardiya performansı'],
                ['icon' => 'cog', 'title' => 'Fırçasız DC PM motor', 'description' => 'Ömür boyu bakım gerektirmeyen sürüş motoru ile düşük TCO'],
                ['icon' => 'arrows-alt', 'title' => 'Yüksek geçiş kabiliyeti', 'description' => '100 mm engelleri aşmaya yardımcı döner yük tekeri seçeneği'],
                ['icon' => 'circle-notch', 'title' => 'Şok emici şasi', 'description' => 'Bağlantı kinematiği ile bozuk zeminde titreşim azaltımı'],
                ['icon' => 'plug', 'title' => 'Sökülebilir tiller tasarımı', 'description' => 'Pimli yapı ile hızlı sevkiyat ve kolay montaj'],
                ['icon' => 'toolbox', 'title' => 'Hızlı servis erişimi', 'description' => 'Tek dokunuş kilit ve tek somunla tahrik tekeri değişimi']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Şehir içi dağıtım depolarında dar koridor içi palet akışı'],
                ['icon' => 'industry', 'text' => 'Bozuk zeminli üretim alanlarında ara stok (WIP) taşıma'],
                ['icon' => 'road', 'text' => 'Tümsek ve yüksek eşikli girişlerde engel aşma gerektiren operasyonlar'],
                ['icon' => 'store', 'text' => 'Perakende arka depolarda rampa yaklaşımı ve kamyon boşaltma'],
                ['icon' => 'cart-shopping', 'text' => '3PL cross-dock merkezlerinde hızlı yük aktarımı'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında düşük gürültü ile iç lojistik'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetikte hassas ürün palet hareketi'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında dar alan manevrası']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '48V sistem ve güçlü motorlarla sınıfına göre üstün verimlilik'],
                ['icon' => 'battery-full', 'text' => 'AGM batarya ve dahili şarj ile pratik enerji yönetimi'],
                ['icon' => 'arrows-alt', 'text' => '100 mm engel aşımı ve 1550 mm dönüş yarıçapıyla çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve DC kontrol ile güvenli kullanım'],
                ['icon' => 'cog', 'text' => 'Fırçasız PM motor sayesinde bakım aralıklarının uzaması'],
                ['icon' => 'layer-group', 'text' => 'Silindir gövde tasarımı ile alçak ağırlık merkezi']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincirler'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya Dağıtım'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri ve Yem'],
                ['icon' => 'ship', 'text' => 'Liman içi depolama ve antrepo'],
                ['icon' => 'building', 'text' => 'B2B toptan dağıtım merkezleri']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion/AGM batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '48V-5A Dahili Şarj Cihazı', 'description' => 'Makine üzerinde entegre şarj ile priz erişimi olan her noktada kolay şarj imkânı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'gauge', 'name' => 'Süre Göstergeli Batarya İndikatörü', 'description' => 'Kalan kapasite ve çalışma süresini göstererek vardiya planlamasını kolaylaştırır.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'grip-lines-vertical', 'name' => 'Döner Yük Teker Takımı', 'description' => 'Yüksek eşik ve 100 mm engelleri aşmada ilave destek sağlayan yapı.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tekli Yük Teker Seçeneği', 'description' => 'Standart çiftli yerine tekli düzenek ile belirli zemin koşullarına uyum.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EPT20 ET hangi zemin türlerinde verimli performans sağlar?', 'answer' => 'PU teker ve şok emici şasi ile endüstriyel beton, epoksi kaplı alanlar ve orta pürüzlü yüzeylerde titreşim azalır; opsiyonel döner yük tekeri yüksek eşiklerde geçişi kolaylaştırır.'],
                ['question' => '2.0 ton kapasite hangi yük merkez mesafesinde geçerlidir?', 'answer' => 'Nominal 2000 kg kapasite, 600 mm yük merkezi (c) kabulüyle verilmiştir. Palet boyu ve ağırlık dağılımı değişirse güvenli kapasite de değişebilir.'],
                ['question' => 'Dönüş yarıçapı ve koridor ölçüsü dar alanlarda yeterli mi?', 'answer' => '1550 mm dönüş yarıçapı ve 2304/2371 mm Ast değerleri dar koridorlarda EUR paletle rahat manevra sağlar; l1=1685 mm toplam uzunluk çevikliği destekler.'],
                ['question' => 'Yürüyüş ve kaldırma hızları operasyon verimini nasıl etkiler?', 'answer' => '4/5.5 km/s yürüyüş, 0.018/0.037 m/s kaldırma ve 0.032/0.038 m/s indirme hızları, yük trafiğine bağlı olarak hat içi akışı hızlandırır ve çevrim süresini düşürür.'],
                ['question' => 'Batarya kapasitesi ve şarj çözümü vardiyaya uygun mu?', 'answer' => '48V 30Ah AGM paket ve dahili 48V-5A şarj ile fırsat şarjı yapılabilir. Süre göstergesi kalan kapasiteyi izlemeye yardım eder.'],
                ['question' => 'Motor tipi ve bakım aralıkları hakkında bilgi verir misiniz?', 'answer' => 'Fırçasız kalıcı mıknatıslı DC sürüş motoru, kömür değişimi gerektirmez. Tek somunla tahrik tekeri değişimi ve tek dokunuş kilit, servis süresini azaltır.'],
                ['question' => 'Eğim kabiliyeti rampalarda yeterli midir?', 'answer' => 'Azami eğim %8 (yüklü) ve %16 (yüksüz) değerleri ile rampa ve yükleme alanlarında güvenli kalkış ve duruş sağlar.'],
                ['question' => 'Gürültü seviyesi gıda ve ilaç depolarına uygun mu?', 'answer' => '74 dB(A) ses basıncı değeri, gürültü duyarlı ortamlarda kullanıcı konforunu artırır ve vardiya boyunca yorgunluğu azaltır.'],
                ['question' => 'Çatal ve palet uyumu için hangi ölçüler mevcuttur?', 'answer' => 'Standart çatal 50/150/1150 mm’dir. Çatal aralığı 560 veya 685 mm; ayrıca 475×1150 seçeneği mevcuttur.'],
                ['question' => 'Güvenlik sistemleri nelerdir?', 'answer' => 'Elektromanyetik servis freni ve DC sürüş kontrolü ani duruşlarda stabilite sağlar; alçak ağırlık merkezi devrilme riskini azaltır.'],
                ['question' => 'Dış saha kullanımında engel aşma performansı nasıldır?', 'answer' => 'Büyük tahrik tekeri ve opsiyonel döner yük tekeri 100 mm yüksekliğe kadar eşik ve bariyerlerde geçişi destekler, dış sahada kesintisiz akış sağlar.'],
                ['question' => 'Garanti koşulları ve satış sonrası destek nasıl işler?', 'answer' => 'Makine 12 ay, akü 24 ay garanti kapsamındadır. İXTİF satış, servis ve yedek parça desteği için 0216 755 3 555 numarasından bize ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: EPT20-ET');
    }
}
