<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KSi201_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'KSi201')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: KSi201');
            return;
        }

        $variants = [
            [
                'sku' => 'KSi201-1600',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF KSi201 - 1600 mm Direk',
                'short_description' => '1600 mm kaldırma yüksekliğiyle kapı ve rampa yüksekliklerine optimum uyum sunan bu konfigürasyon, 2236 mm dönüş yarıçapı ve 8.5/10 km/s hızla dar alanlarda hızlı, güvenli ve çift katlı taşıma yapar. Uzun vardiyalarda Li‑Ion enerjiyle kesintisiz çalışır.',
                'body' => '<section><h2>1600 mm Direk ile Maksimum Uyum</h2><p>İXTİF KSi201’in 1600 mm mast seçeneği, kapalı alan geçişleri ve standart raf yükseklikleri için ideal bir denge sunar. 1316 mm indirgenmiş direk yüksekliği sayesinde rampa kapıları ve alçak kapı geçişleri sorunsuz aşılırken, 2112 mm uzatılmış mast yüksekliği operasyonda gerekli görünürlüğü ve istif mesafesini sağlar. Çift katlı taşıma mimarisi, destek kollarıyla paleti yerden kaldırırken direk ile ikinci paleti taşımanıza olanak vererek iki paleti aynı anda hareket ettirir ve çevrim başına verimi artırır.</p></section><section><h3>Performans ve Ergonomi</h3><p>2.5 kW AC tahrik ve 3.0 kW kaldırma motoru 8.5/10 km/s seyir hızlarını kolaylıkla üretir; elektromanyetik fren ve elektronik direksiyon kontrollü duruş ve hassas manevra sağlar. 55×185×1150 mm çatallar ve 570 mm çatallar arası mesafe, EUR paletlerle nokta atışı uyumludur. 24V/205Ah Li‑Ion akü, entegre 24V/30A şarj cihazıyla fırsat şarjını destekler; 24V/100A harici şarj seçeneği yoğun kullanımda hızlı toparlanma sağlar. 2236 mm dönüş yarıçapı ve 2920 mm tipik koridor ihtiyacı, dar alanlarda kesintisiz akış yaratır.</p></section><section><h3>Kullanım Senaryoları</h3><p>İki paleti tek hamlede çekmek, hat beslemeyi hızlandırır. Rampa üstü yükleme, çapraz sevkiyat ve uzun mesafeli iç taşıma hatlarında operatör, süspansiyonlu platform ve yastıklı sırt dayanağıyla konforlu bir çalışma alanına sahiptir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletli siparişleri iki katlı taşıyarak hat besleme döngülerini hızlandırma'],
                    ['icon' => 'warehouse', 'text' => 'Dar koridorlarda 2236 mm dönüş yarıçapı ile raf arası transfer'],
                    ['icon' => 'store', 'text' => 'Perakende DC içinde cross-dock hattı besleme'],
                    ['icon' => 'car', 'text' => 'Rampa üstü yükleme ve araç besleme operasyonları'],
                    ['icon' => 'industry', 'text' => 'Üretim hücreleri arası WIP akışı (tek/çift palet)'],
                    ['icon' => 'snowflake', 'text' => 'Sıcaklık kontrollü alanlarda sessiz ve sarsıntısız taşıma']
                ]
            ],
            [
                'sku' => 'KSi201-2100',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF KSi201 - 2100 mm Direk (Opsiyon)',
                'short_description' => '2100 mm mast, daha yüksek raf noktalarına erişim ve çekme-itme hatlarında esneklik sağlar. Li‑Ion batarya ve entegre şarj ile uzun vardiyalarda yüksek up‑time, çift katlı mimari ile iki palette çevrim süresi avantajı sunar.',
                'body' => '<section><h2>2100 mm Mast ile Esnek İstifleme</h2><p>Opsiyonel 2100 mm direk, KSi201’i daha yüksek istif yüksekliklerine taşıyarak çekme-itme ve buffer bölgelerinde esnekliği artırır. Direk geometri optimizasyonu, görüşü korurken yükün stabilitesini sağlar. Çift katlı tasarım, destek kolu kaldırması ile zemin paletini yükseltirken direk ile ikinci paleti kaldırarak iki paletin eşzamanlı taşınmasına olanak verir; özellikle cross-dock ve konsolidasyon alanlarında çevrim başına iş miktarını yükseltir.</p></section><section><h3>Enerji, Hız ve Kontrol</h3><p>24V/205Ah Li‑Ion enerji paketi bakım gerektirmeden uzun ömür sunar; entegre 24V/30A şarj cihazı fırsat şarjını kolaylaştırır, 24V/100A harici şarj ile hızlı toparlanma mümkündür. 2.5 kW AC sürüş motoru 10 km/s’ye kadar hız sağlar; elektromanyetik frenleme ve elektronik direksiyon dar alanda güvenli manevra kabiliyeti yaratır. Poliüretan tekerlek seti, zemin düzensizliklerinde titreşimi minimize eder ve gürültüyü düşük tutar.</p></section><section><h3>Operasyonel Uygulamalar</h3><p>Yüksek raflı tampon alanlar, uzun mesafeli hat besleme, yükleme rampaları ve yoğun sevkiyat saatleri için uygundur. 3026/2920 mm koridor değerleri planlamada referans olur; 2236 mm dönüş yarıçapı ve 2456 mm toplam uzunluk ile dar alanlarda dahi çevik kalır.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Konsolidasyon alanında iki paleti eşzamanlı çekip sevk hazırlığı'],
                    ['icon' => 'warehouse', 'text' => 'Yüksek raf tampon bölgelerinde tampon istifleme'],
                    ['icon' => 'store', 'text' => 'Perakende iade toplama ve ayrıştırma hatları'],
                    ['icon' => 'flask', 'text' => 'Kimya ve hassas ürün koridorlarında kontrollü istif'],
                    ['icon' => 'car', 'text' => 'Araç yükleme rampalarında çift katlı taşıma verimliliği'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası bitmiş ürün buffer alanları']
                ]
            ]
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->updateOrInsert(['sku' => $v['sku']], [
                'sku' => $v['sku'],
                'parent_product_id' => $m->product_id,
                'variant_type' => $v['variant_type'],
                'category_id' => $m->category_id,
                'brand_id' => $m->brand_id,
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }
        $this->command->info('✅ Variants eklendi: KSi201-1600, KSi201-2100');
    }
}
