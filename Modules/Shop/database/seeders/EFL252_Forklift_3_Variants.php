<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL252')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL252'); return; }

        $variants = [
            [
                'sku' => 'EFL252-3000SM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL252 - 3.0 m Standart Mast',
                'short_description' => '3000 mm kaldırma yüksekliğine sahip standart mast, 2060 mm kapalı yükseklik ve 4050 mm açık mast değeri ile depo kapıları ve standart raf yükseklikleri için ideal esnekliği sağlar.',
                'long_description' => '<section><h3>3.0 m Standart Mast: Depo İçinde Hız ve Netlik</h3><p>EFL252-3000SM, 3000 mm kaldırma yüksekliği gerektiren yaygın depo operasyonları için optimize edilmiştir. 2060 mm kapalı mast, sık kullanılan kapı ve tavan yüksekliklerine uyumludur; 4050 mm açık mast, yüklerin kamyon rampa seviyesine emniyetle istiflenmesini sağlar. 6°/10° mast eğimi ile palet hizalama hızlı ve kontrollüdür. 2A sınıfı karet ve 40×122×1070 mm çatal ölçüsü, EUR ve benzeri paletlere tam oturur. 2290 mm dönüş yarıçapı, dar koridorlarda hassas dönüşler sunar. LFP Li-ion 80V 205Ah batarya fırsat şarjı ile vardiya esnekliğini artırırken IPX4 su koruması yağmurlu günlerde dahi aralıksız süreklilik sağlar.</p></section><section><h3>Teknik Uyum ve Güvenlik</h3><p>10 kW sürüş ve 16 kW kaldırma motoru; 11/12 km/s sürüş, 0.28/0.37 m/s kaldırma hızlarını sağlar. Hidrolik servis freni, mekanik park freni ve AC sürüş kontrolü, operatörün güvenli hızlanma ve hassas konumlandırma yapmasına yardımcı olur. Telematics; gerçek zamanlı konum, kullanım raporları ve kart erişim takibini destekleyerek filo yöneticisine şeffaflık sunar. Yan kaydırıcı tercih edildiğinde nominal kapasiteden yaklaşık 150 kg düşüş kural olarak hesaba katılmalıdır.</p></section><section><h3>Kullanım Senaryoları</h3><p>3.0 m mast; rampa yükleme, cross-dock ve hat besleme gibi düşük-orta raf yükseklikleri için uygundur. Kapalı depolarda hızlı döngüler, yoğun sipariş toplama ve araç içi yükleme işlerinde optimum denge kurar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Kapalı depoda araç yükleme/boşaltma'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock hatlarında palet aktarma'],
                    ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde düşük raf istifleme'],
                    ['icon' => 'industry', 'text' => 'Üretim hatlarına yarı mamul besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv kutu palet transferi'],
                    ['icon' => 'flask', 'text' => 'Kimyasal depolarda kapalı alan operasyonu']
                ]
            ],
            [
                'sku' => 'EFL252-4500TFM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL252 - 4.5 m Üçlü Serbest Kaldırmalı Mast',
                'short_description' => '4.5 m üçlü serbest kaldırmalı mast; giriş yüksekliği sınırlı alanlarda raf üstüne erişim ve kapalı kapılardan sorunsuz geçiş için serbest kaldırma avantajı sunar.',
                'long_description' => '<section><h3>4.5 m Triplex Serbest Kaldırma: Kısıtlı Girişlerde Maximum Erişim</h3><p>EFL252-4500TFM, kapalı girişlerden geçip yüksek raflara erişim gereken depolar için tasarlanmıştır. Serbest kaldırma, mast yükselmeden çatalı yukarı taşıyarak tavan kısıtlarına takılmadan yük hareketi sağlar. 2115 mm civarı kapalı mast ve 5525 mm açık mast yüksekliği (broşür seçenek aralığına uygun) ile geniş bir uygulama yelpazesi desteklenir. LFP Li-ion 80V 205Ah batarya, vardiya boyunca fırsat şarjını destekleyerek performans düşmeden operasyonu sürdürür.</p></section><section><h3>Kontrol ve Verim</h3><p>AC sürüş kontrolü ve hidrolik direksiyon, hassas yaklaşma ile güvenli istiflemeyi kolaylaştırır. 11/12 km/s sürüş hızı ve 0.28/0.37 m/s kaldırma hızı, yoğun trafik saatlerinde dahi tempoyu korur. IPX4 koruma ile yağmurlu hava koşullarında açık saha rampaları ve konteyner içi operasyonlar güvenle yürütülür. Telematics ile konum ve kullanım verileri, preventif bakım planlarını besleyerek TCO’yu düşürür.</p></section><section><h3>Kullanım Senaryoları</h3><p>Yüksek raflı depolama, kapalı girişli alanlar, mevsimsel yüksek hacim dönemlerinde yoğun vardiya ve çoklu vardiya çalışma düzenleri için uygundur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı depolama ve dar koridor erişimi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret tepe sezon dalgalanmalarında hızlı istif'],
                    ['icon' => 'snowflake', 'text' => 'Yağışlı havada açık saha rampa operasyonu'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG akışında yoğun vardiya palet hareketi'],
                    ['icon' => 'building', 'text' => 'Çok katlı depo girişlerinde serbest kaldırma avantajı'],
                    ['icon' => 'industry', 'text' => 'Üretim sonu stok alanlarında yüksek konumlandırma']
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
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant eklendi: {$v['sku']}");
        }
    }
}
