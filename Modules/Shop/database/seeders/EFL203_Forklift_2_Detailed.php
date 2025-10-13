<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL203_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFL203')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFL203');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section><h2>İXTİF EFL203: Dış sahada sağlam, içerde çevik</h2>
<p>2000 kg kapasiteli İXTİF EFL203, içten yanmalı görünümlü fakat tamamen elektrikli altyapısıyla depo içi ve açık alan operasyonlarınızda güvenilir bir omurga sağlar. 80V 230Ah Li-Ion güç paketi kısa şarj molalarıyla vardiya sürelerini kesintisiz kılar; 14/15 km/s seyir hızı, %15/%20 tırmanma kabiliyeti ve 0.29/0.36 m/s kaldırma hızı yoğun yükleme-boşaltma döngülerinde akışı korur. Pnömatik lastikler, yüksek şasi açıklığı ve suya dayanıklı yapı ile rampalar, ıslak zeminler ve bozuk satıhlarda istikrarlı ilerler. Geniş ayak alanı, ayarlanabilir direksiyon ve kol dayamalı koltuk, operatörün tüm vardiya boyunca konforunu destekler.</p></section>
<section><h3>Teknik güç ve görünürlük</h3>
<p>Yeni geliştirilen direğin hortum düzeni ve mukavemeti iyileştirilmiş, cıvatalı üst koruma (OHG) ile birleştirildiğinde çatala ve yüke kesintisiz bir görüş açısı sağlar. Standart 3000 mm kaldırma yüksekliğinde direk, 2090 mm kapalı yükseklik ve 4025 mm açık yükseklik değerleriyle kapı geçişlerini ve raf operasyonlarını optimize eder. 40×122×1070 mm çatallar ve 2A sınıf taşıyıcı, 2356 mm yüke kadar uzunluk ve 1154 mm gövde genişliğiyle palet çeşitliliğine uyumludur. 10 kW S2 sürüş motoru ve 16 kW S3 kaldırma motoru birlikte, düşük gürültülü 74 dB(A) iş ortamında tutarlı performans üretir. AC sürüş kontrolü, hidrolik servis freni ve mekanik park freni; hızlanma, duruş ve milimetrik yük kontrolünü güvenli şekilde destekler.</p></section>
<section><h3>Sonuç</h3>
<p>İXTİF EFL203, Li-Ion teknolojisinin sağladığı düşük bakım ve yüksek kullanım oranı ile TCO’yu düşürürken, açık alan uyumluluğu ve kompakt şasiyle depo verimini artırır. Detaylı teknik bilgi ve keşif için 0216 755 3 555.</p></section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (c=500 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V 230Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '14/15 km/s (yük/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '2110 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion enerji verimi', 'description' => 'Hızlı fırsat şarjı ve uzun çevrim ömrü ile kesintisiz çalışma'],
                ['icon' => 'bolt', 'title' => 'Güçlü aktarma', 'description' => '10 kW sürüş ve 16 kW kaldırma motoru ile dengeli performans'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt şasi', 'description' => 'Dar alanlarda çevik hareket ve düşük Ast değerleri'],
                ['icon' => 'shield-alt', 'title' => 'Geliştirilmiş görüş', 'description' => 'Optimize hortum yerleşimi ve cıvatalı OHG ile net görüş'],
                ['icon' => 'building', 'title' => 'Dış saha uyumu', 'description' => 'Yüksek şasi açıklığı ve pnömatik lastikler ile açık alan konforu'],
                ['icon' => 'store', 'title' => 'Operatör konforu', 'description' => 'Geniş ayak alanı, ayarlı direksiyon ve kol dayama']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Rampa yükleme-boşaltma ve çapraz sevkiyat hatları'],
                ['icon' => 'box-open', 'text' => 'Fulfillment merkezlerinde inbound/outbound palet akışı'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında yarı mamul (WIP) transferi'],
                ['icon' => 'building', 'text' => 'Açık stok sahalarında hava koşullarında malzeme taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv tedarikinde konteyner boşaltma ve hat besleme'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli ve kontrollü taşıma'],
                ['icon' => 'snowflake', 'text' => 'Gıda lojistiğinde soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas ve sessiz çalışma gerektiren işler']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'IC gövde ergonomisi ile elektrik verimliliğini birleştiren tasarım'],
                ['icon' => 'battery-full', 'text' => '80V Li-Ion: hızlı şarj, sıfıra yakın bakım, yüksek uptime'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt ölçüler ve 2110 mm dönüş ile dar alan çevikliği'],
                ['icon' => 'shield-alt', 'text' => 'İyileştirilmiş direk/OGH görünürlüğü ile güvenli istif'],
                ['icon' => 'star', 'text' => 'Büyük LED ekran ve güçlü farlarla sezgisel kullanım']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Soğuk Zincir Lojistiği'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Dağıtımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama ve Üretim'],
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
                'coverage' => 'Satın alım tarihinden itibaren makine için 12 ay, Li-Ion batarya modülleri için 24 ay fabrika garantisi sağlanır. Garanti, normal kullanım koşullarındaki üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'charging-station', 'name' => 'Harici hızlı şarj cihazı', 'description' => 'Üç faz hızlı şarj çözümü, çoklu vardiyada esneklik', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Entegre şarj kablosu', 'description' => 'Ara molalarda pratik şarj için gövdeye entegre', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Geniş tabanlı pnömatik lastik', 'description' => 'Dış sahada kavrama ve konfor artırımı', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => '80V 460Ah Li-Ion paket', 'description' => 'Çok vardiyalı operasyon için yüksek kapasiteli enerji', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EFL203 hangi kaldırma ve indirme hızlarını sağlar?', 'answer' => 'Kaldırma hızı yükte 0.29 m/s, yüksüz 0.36 m/s; indirme hızı yükte 0.43 m/s, yüksüz 0.44 m/s değerindedir.'],
                ['question' => 'Maksimum eğim kabiliyeti operasyonu nasıl etkiler?', 'answer' => 'Yükte %15, yüksüz %20 tırmanma kabiliyeti rampalı sahalarda güvenli ve akıcı lojistiği destekler.'],
                ['question' => 'Standart akü paketi kaç volttur ve kapasitesi nedir?', 'answer' => '80V 230Ah Li-Ion paketi standarttır; hızlı fırsat şarjıyla çoklu vardiyada yüksek kullanım oranı sağlar.'],
                ['question' => 'Dış mekânda yağmurda çalışmaya uygun mu?', 'answer' => 'Suya dayanıklı tasarım ve pnömatik lastikler yağışlı günlerde kontrollü ve güvenli sürüş imkânı verir.'],
                ['question' => 'Operatör konforu için hangi iyileştirmeler var?', 'answer' => 'Geniş ayak boşluğu, ayarlanabilir direksiyon ve kol dayamalı koltuk uzun vardiyada konfor sunar.'],
                ['question' => 'Hangi mast ölçüleri destekleniyor?', 'answer' => 'Standartta 3000 mm; farklı kaldırma yükseklikleri opsiyon listesinde mevcuttur.'],
                ['question' => 'Gövde boyutları hangi koridor genişliklerini gerektirir?', 'answer' => 'Ast (1000×1200 enine) 3805 mm ve Ast (800×1200 boyuna) 4005 mm olarak ölçülmüştür.'],
                ['question' => 'Gürültü seviyesi çalışma güvenliğini nasıl etkiler?', 'answer' => '74 dB(A) düşük gürültü seviyesi kapalı alanlarda iletişimi ve iş güvenliğini destekler.'],
                ['question' => 'Sürüş ve kaldırma motor güçleri nelerdir?', 'answer' => 'Sürüş motoru 10 kW (S2 60 dk), kaldırma motoru 16 kW (S3 15%) değerindedir.'],
                ['question' => 'Entegre şarj ile harici şarj arasında fark nedir?', 'answer' => 'Gömülü şarj pratik ara molalarda idealdir; harici üç faz şarj ise daha kısa şarj süresi ve yüksek vardiya temposu sağlar.'],
                ['question' => 'Yan kaydırıcı (side shifter) kapasiteyi etkiler mi?', 'answer' => 'Evet, yan kaydırıcı ile nominal kapasiteden 100 kg düşüm dikkate alınmalıdır.'],
                ['question' => 'Satın alma ve yerinde keşif için nasıl iletişim kurarım?', 'answer' => 'İXTİF satış ekibine 0216 755 3 555 numarasından ulaşabilir, keşif ve demo talep edebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('🧩 Detailed güncellendi: EFL203');
    }
}
