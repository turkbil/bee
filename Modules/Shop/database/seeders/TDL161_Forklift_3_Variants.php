<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL161_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'TDL-161')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: TDL-161');
            return;
        }

        $variants = [
            [
                'sku' => 'TDL-161-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL161 - 3.0 m Direk (Standart Mast)',
                'short_description' => '3.0 m kaldırma, 100 mm serbest kaldırma, kompakt şasi ve 3 teker çeviklik ile dar koridorlarda dengeli yükleme. 48V Li-Ion ile fırsat şarjı ve düşük bakım.',
                'body' => '<section><h2>3.0 m Standart Mast</h2><p>Standart 3000 mm kaldırma, günlük depolama ve sevkiyat süreçlerinin çoğu için ideal kapsama alanı sunar. 100 mm serbest kaldırma ile rampa ve kapı eşiklerinden geçişlerde hassas hareket mümkündür. 3 teker şasi ve 1605 mm dönüş yarıçapı, dar alanlarda raf önü hizalamayı kolaylaştırır.</p></section><section><h3>Teknik ve Operasyon</h3><p>48V/280Ah Li-Ion batarya ve 5.4kW×2 AC tahrik motorları, kesintisiz çekiş ve hızlanma sağlar. 11 kW kaldırma motoru, yüklü/boş 0.35/0.43 m/s değerleriyle dengeli kaldırma sunar. Entegre 48V-50A şarj ünitesi ile mola aralarında fırsat şarjı yapılabilir.</p></section><section><h3>Kullanım</h3><p>Genel depo, perakende DC ve 3PL merkezlerinde palet kabulü, cross-dock ve sevkiyat hazırlığında optimum çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo raf önü yükleme'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock palet aktarımı'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası sevkiyat hazırlığı'],
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında giriş-çıkış'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında hassas taşıma'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı besleme']
                ]
            ],
            [
                'sku' => 'TDL-161-4500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL161 - 4.5 m Direk (Serbest Kaldırma)',
                'short_description' => '4.5 m kaldırma, 1430 mm’ye kadar serbest kaldırma ile kapalı alan raf içi çalışmalarda üst seviye esneklik. LCD ekran ve hidrolik direksiyon ile hassas kontrol.',
                'body' => '<section><h2>4.5 m Serbest Kaldırma</h2><p>4.5 m mast ile tavan yüksekliği sınırlı alanlarda dahi etkili istifleme sağlanır. Serbest kaldırma sayesinde çatallar, mast yükseltmeden ürünleri raf seviyesine taşır ve kapı geçişlerinde toplam yüksekliği kontrol altında tutar.</p></section><section><h3>Performans</h3><p>Çift AC tahrik (5.4kW×2) rampalarda tutarlı çekiş sunar. 48V Li-Ion batarya fırsat şarjı ile vardiya boyu hazırdır. Elektromanyetik frenler güvenli ve öngörülebilir duruş sağlar.</p></section><section><h3>Uygulama</h3><p>Perakende, FMCG ve ilaç depolarında üst raf seviyelerine erişimde verimli çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Raf içi istifleme'],
                    ['icon' => 'box-open', 'text' => 'Toplama ve sevkiyat hazırlığı'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası depolama'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda operasyonları'],
                    ['icon' => 'pills', 'text' => 'Hassas ürün raflama'],
                    ['icon' => 'industry', 'text' => 'WIP alan yönetimi']
                ]
            ],
            [
                'sku' => 'TDL-161-6000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL161 - 6.0 m Direk (Serbest Kaldırma)',
                'short_description' => '6.0 m’e kadar kaldırma ile yüksek raf sistemlerine erişim. 3 teker kompakt şasi dar koridorlarda çevik; Li-Ion sistemle planlı duruşlar azalır.',
                'body' => '<section><h2>6.0 m Yüksek Erişim</h2><p>6000 mm mast, yüksek raflı depolarda maksimum alan verimliliğini sağlar. Serbest kaldırma ile kapı ve sprinkler kısıtlarında kontrollü yükselme mümkündür. Katı lastikler ve optimize şasi, yük altında dengeyi korur.</p></section><section><h3>Teknik</h3><p>0.35/0.43 m/s kaldırma, 15/16 km/s sürüş ve 15/17% eğim kabiliyeti, yoğun vardiya akışlarında süreklilik sunar. Entegre 48V-50A şarj ünitesi çalışma planını esnekleştirir.</p></section><section><h3>Senaryolar</h3><p>3PL yüksek raflı depolar, elektronik ve FMCG dağıtım merkezleri.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raf erişimi'],
                    ['icon' => 'box-open', 'text' => 'Yoğun vardiya palet akışı'],
                    ['icon' => 'store', 'text' => 'DC konsolidasyon alanı'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtım hatları'],
                    ['icon' => 'microchip', 'text' => 'Elektronik kutu-palet istifi'],
                    ['icon' => 'industry', 'text' => 'Üretim sonu depolama']
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
        $this->command->info('✅ Variants: TDL-161 (3 adet)');
    }
}
