<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPL185_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPL185')->first();
        DB::table('shop_products')->where('product_id', $p->product_id)->update([

            'body' => json_encode(['tr' => '<section><h2>İXTİF EPL185: 1.8 ton sınıfında kompakt güç ve Li‑Ion verimliliği</h2><p>İXTİF EPL185, 1.8 ton nominal kapasiteyi yalnızca 170 kg servis ağırlığı ve <strong>l2=400 mm</strong> kompakt gövde ile birleştirir. <strong>48V Li‑Ion</strong> enerji sistemi (20Ah, opsiyonel 30Ah) metal korumalı çıkarılabilir batarya kartuşu ve <strong>entegre 48V‑10A şarj cihazı</strong> ile esnek çalışma sağlar. Yüzer stabilite tekerleri eşik ve genleşme derzlerinden sarsıntısız geçişe destek olurken, <strong>1330 mm</strong> dönüş yarıçapı ve mekanik direksiyon dar koridorlarda kontrollü manevra sunar. <em>Creep</em> modu, kol dikey konumdayken palet hizalamada milimetrik ilerlemeyi mümkün kılar.</p></section><section><h3>Teknik altyapı ve performans</h3><p>İkinci nesil sürüş teknolojisi ve <strong>fırçasız DC</strong> motor, karbon fırça değişimi ihtiyacını ortadan kaldırarak bakım sürekliliğini artırır. <strong>0.9 kW</strong> sürüş ve <strong>0.8 kW</strong> kaldırma motoru dengeli hızlanma ve kaldırma hızı (<strong>0.020/0.025 m/s</strong>) üretir. PU sürüş ve yük tekerleri zemin dostu çalışma ile düşük <strong>74 dB(A)</strong> gürültü seviyesini destekler. <strong>0.065/0.030 m/s</strong> indirme hızı, ürün emniyeti açısından öngörülebilir bir profil sunar. Tam yükte <strong>%6</strong>, yüksüz <strong>%16</strong> eğim kabiliyeti; 1000–1300 kg yüklerde <strong>%10</strong> hedef eğim performansını mümkün kılar. Batarya kartuşunun yüzer yerleşimi, temas pimlerini korur ve uzun ömür sağlar.</p><p>Boyutlarda <strong>1550 mm</strong> toplam uzunluk, <strong>610/695 mm</strong> genişlik seçenekleri, <strong>50/150/1150 mm</strong> çatal ölçüsü ve <strong>540/685 mm</strong> çatal aralığı; EUR palet ve geniş tabanlı paletlerde uyumluluğu artırır. <strong>80 mm</strong> alçaltılmış çatal yüksekliği palet kanallarına pürüzsüz giriş çıkış sağlar; <strong>115 mm</strong> kaldırma yüksekliği ise çekme sırasında yeterli yer açıklığı sunar.</p></section><section><h3>Sonuç</h3><p>EPL185; e‑ticaret, 3PL, perakende dağıtım ve endüstriyel ara taşımalarda verim, çeviklik ve düşük sahip olma maliyeti sunar. Li‑Ion enerji ile fırsat şarjı, dar alan manevrası ve bakım dostu mimari birleşerek işletmelerin toplam çalışma süresini artırır. Detay ve teklif için arayın: <strong>0216 755 3 555</strong>.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1800 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V Li-Ion, 20Ah (ops. 30Ah)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '5.0 / 5.5 km/s (yüklü / yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1330 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '48V Li‑Ion Enerji', 'description' => '20/30Ah modül, BMS ile güvenlik ve hızlı fırsat şarjı.'],
                ['icon' => 'plug', 'title' => 'Entegre Şarj', 'description' => '48V‑10A on-board şarj ile altyapı bağımsız kullanım.'],
                ['icon' => 'industry', 'title' => 'Yüzer Stabilite', 'description' => 'Endüstriyel yüzer tekerler ile sarsıntısız geçiş.'],
                ['icon' => 'bolt', 'title' => 'Fırçasız DC Tahrik', 'description' => 'Bakım ihtiyacını azaltan verimli motor mimarisi.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Manevra', 'description' => 'l2=400 mm gövde ve 1330 mm dönüş ile dar alanda çeviklik.'],
                ['icon' => 'shield-alt', 'title' => 'Metal Batarya Kapağı', 'description' => 'Bağlantının kazara çıkmasını önlemeye yardımcı yapı.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Raf arası kısa mesafe palet transferi ve sipariş konsolidasyonu'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında cross-dock besleme ve sevkiyat hazırlığı'],
                ['icon' => 'store', 'text' => 'Perakende DC içinde mağaza bazlı sıralama ve yükleme öncesi hareket'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG koli paletlerinin hat başı beslemesi'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında yarı mamul (WIP) taşımaları'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında hat besleme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış yaklaşımında hassas konumlandırma'],
                ['icon' => 'flask', 'text' => 'Kimyasal depolarda güvenli ve sessiz iç lojistik akışı']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Fırçasız DC sürüş ile daha düşük bakım ve tutarlı çekiş'],
                ['icon' => 'battery-full', 'text' => '48V Li‑Ion ve BMS ile yüksek enerji verimliliği'],
                ['icon' => 'arrows-alt', 'text' => 'l2=400 mm kompakt yapı ile dar koridorda çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'Metal batarya kapağı ve yüzer kartuş ile güvenilirlik'],
                ['icon' => 'plug', 'text' => 'Entegre şarj ile altyapı esnekliği']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG'],
                ['icon' => 'snowflake', 'text' => 'Gıda & Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç & Medikal'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Depolar'],
                ['icon' => 'building', 'text' => 'Tesis Yönetimi'],
                ['icon' => 'star', 'text' => 'Fulfillment Merkezleri'],
                ['icon' => 'bolt', 'text' => 'Yedek Parça Lojistiği'],
                ['icon' => 'battery-full', 'text' => 'Enerji Ekipman Depoları'],
                ['icon' => 'shield-alt', 'text' => 'Savunma Yan Sanayi Depoları'],
                ['icon' => 'award', 'text' => 'Proje Bazlı Lojistik'],
                ['icon' => 'certificate', 'text' => 'Sertifikalı Depolama'],
                ['icon' => 'cog', 'text' => 'Bakım & Yedek Parça Depoları'],
                ['icon' => 'plug', 'text' => 'Teknik Servis Lojistiği']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay garanti kapsamındadır. Li-Ion batarya modülü 24 ay garanti altındadır. Üretim hataları ve normal kullanım koşulları geçerlidir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Entegre Şarj Cihazı 48V-10A', 'description' => 'Makine üzerinde yerleşik on-board şarj (standart).', 'is_standard' => true, 'price' => null],
                ['icon' => 'plug', 'name' => 'Harici Şarj Cihazı 48V-10A', 'description' => 'Şarj alanı için bağımsız ünite (opsiyonel).', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tandem PU Yük Teker Seti', 'description' => 'Zemin koşullarına göre daha iyi yük dağılımı sağlar (opsiyonel).', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yedek Li-Ion Batarya 48V-20Ah/30Ah', 'description' => 'Hızlı değişim için ikinci modül (opsiyonel).', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EPL185’in nominal kapasitesi nedir?', 'answer' => 'Nominal kapasite 1800 kg’dır ve 600 mm yük merkezinde sağlanır.'],
                ['question' => 'Dar alanlarda manevra değeri nedir?', 'answer' => 'Dönüş yarıçapı 1330 mm’dir; l2=400 mm kompakt gövde yaklaşımı kolaylaştırır.'],
                ['question' => 'Enerji sistemi ve şarj mimarisi nasıldır?', 'answer' => '48V Li-Ion batarya 20Ah (opsiyon 30Ah) kapasitededir. Entegre 48V-10A şarj cihazı standarttır.'],
                ['question' => 'Sürüş/kaldırma hızı değerleri nelerdir?', 'answer' => 'Sürüş hızı 5/5.5 km/s, kaldırma hızı 0.020/0.025 m/s’dir.'],
                ['question' => 'Eğimlerde performansı nedir?', 'answer' => 'Maksimum eğim yüklü %6, yüksüz %16’dır. 1000–1300 kg yüklerde %10 hedef eğim performansı mümkündür.'],
                ['question' => 'Gürültü düzeyi ne kadardır?', 'answer' => 'Sürücü kulağında 74 dB(A) seviyesindedir.'],
                ['question' => 'Teker ve lastik seçenekleri nelerdir?', 'answer' => 'Sürüş ve yük tekerleri PU’dur; yük tekerinde tandem seçeneği mevcuttur.'],
                ['question' => 'Direksiyon ve kontrol tipi nedir?', 'answer' => 'Mekanik direksiyon ve DC sürüş kontrolü kullanılır.'],
                ['question' => 'Batarya güvenliği nasıl sağlanır?', 'answer' => 'Metal korumalı kapak ve BMS, bağlantı güvenliği ve hücre sağlığına katkı sağlar.'],
                ['question' => 'Çatal ve aralık seçenekleri var mı?', 'answer' => 'Uzunluk 900–1500 mm, aralık 540/685 mm opsiyonları mevcuttur.'],
                ['question' => 'Bakım erişimi ve modülerlik nasıldır?', 'answer' => 'Metal kapak hızlı erişim sağlar; modüler yapı kontrolleri kolaylaştırır.'],
                ['question' => 'Garanti kapsamı ve servis?', 'answer' => 'Makine 12 ay, batarya 24 ay garantilidir. Satış & servis için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
    }
}
