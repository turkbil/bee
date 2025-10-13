<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TDL162_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'TDL162')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: TDL162');
            return;
        }

        $variants = [
            [
                'sku' => 'TDL162-3000-STD',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL162 - 3000 mm Standart Mast',
                'short_description' => 'Standart 3.0 m mast ile 1995 mm kapalı yükseklik ve 4058 mm açık yükseklik; dar girişli depolarda maksimum görüş ve 1.6 ton nominal kapasite ile dengeli operasyon.',
                'body' => '<section><h3>Standart 3.0 m Mast: Günlük Operasyonların Güvencesi</h3><p>3.0 metre kaldırma yüksekliği, geniş bir operasyon yelpazesinde ideal denge sunar. 1995 mm kapalı yükseklik, düşük kapı ve tavan geçişlerinde rahat manevra sağlar; 4058 mm açık yükseklik ise standart raf seviyelerine güvenli erişim verir. 80V çift sürüş mimarisi, 16/17 km/s seyir hızı ve 20/25% tırmanma kabiliyeti ile rampa geçişleri, yükleme-boşaltma ve ara depo transferleri akıcı bir ritimde yürütülür. Operatörün konforu için geniş bacak alanı ve düşük gürültü seviyesine ek olarak LED ekran ve performans modu seçici, her vardiyada tekrarlanabilir sonuçlar elde etmeye yardımcı olur.</p></section><section><h3>Teknik Uyum ve Verimlilik</h3><p>Standart mast seçeneği, 40×100×920 mm çatal ölçüleri ve 1050 mm gövde genişliği ile palet standardizasyonunun yüksek olduğu operasyonlarda verimli bir akış sağlar. 3339 mm koridor gereksinimi (1000×1200 enine palet) ve 1639 mm dönüş yarıçapı, dar alanlarda bile çevik hareket etme imkânı verir. Elektromanyetik frenler ve AC sürüş kontrolü, yükle 0.50 m/s kaldırma ve 0.55 m/s indirme hızlarında güvenli, kontrollü bir sürüş karakteri ortaya koyar.</p></section><section><h3>Kullanım Senaryoları</h3><p>E-ticaret sipariş toplama, perakende dağıtım, 3PL raf besleme hatları ve otomotiv yedek parça depolarında gündelik operasyonların büyük kısmını üstlenecek kadar esnektir. Çok vardiyalı programlarda harici hızlı şarj opsiyonu ile kesintisiz çalışma akışı korunur.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Sipariş toplama ve paketleme alanlarında palet akışı'],
                    ['icon' => 'warehouse', 'text' => '3PL raf besleme ve transfer istasyonları'],
                    ['icon' => 'store', 'text' => 'Perakende DC giriş-çıkış rampa operasyonları'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent ara depo taşımaları'],
                    ['icon' => 'industry', 'text' => 'Üretim hücreleri arası WIP akışı'],
                    ['icon' => 'flask', 'text' => 'Kimyasal malzeme raf erişimi ve transferi']
                ]
            ],
            [
                'sku' => 'TDL162-4500-TPL',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL162 - 4500 mm Triplex Serbest Kaldırma',
                'short_description' => '4.5 m triplex serbest kaldırma ile 1600 kg’a yakın residual kapasite; düşük raf önlerinde kapı yüksekliğini aşmadan çalışma ve yoğun istifleme için ideal çözüm.',
                'body' => '<section><h3>4.5 m Triplex: Kapasiteyi Yukarı Taşırken Kontrolü Korumak</h3><p>Triplex serbest kaldırma direk, kapalı alanlarda yükü tavana vurmadan ilk metreleri kaldırabilmeyi sağlar. TDL162, 80V çift sürüş tahriki ve optimize şasi dengesiyle 4.5 metre seviyesine kadar nominal kapasiteyi korumaya odaklanır; bu sayede hızlı istif döngülerinde zaman tasarrufu elde edilir. Entegre şarj cihazı ve harici hızlı şarj girişi, çok vardiyalı senaryolarda şarj planını esnek biçimde düzenlemenize olanak tanır.</p></section><section><h3>Performans Ayrıntıları</h3><p>16/17 km/s seyir hızları, 0.50/0.52 m/s kaldırma ve 0.55 m/s indirme hızları, yoğun vardiya pencerelerinde bile sabit verim sağlar. 18x7-8 ön ve 140/55-9 arka lastikler; elektromanyetik frenlerle birlikte tutarlı duruş ve kalkış karakteri üretir. LED ekran, performans modları ve joystick hidrolik kontrol opsiyonu, operatörün iş akışını iyileştirir.</p></section><section><h3>Kullanım Senaryoları</h3><p>Dar koridor raflarında hızlı istif, hızlı dönüş gerektiren cross-dock hatları ve üretime malzeme besleme istasyonlarında 4.5 m erişim yüksekliği fark yaratır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Dar koridorda yüksek raf istifi'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock alanlarında hızlı palet devirleri'],
                    ['icon' => 'industry', 'text' => 'Üretim hat sonu mamul toplama'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtımında hızlı toplama'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda önlerinde düşük tavanla istif'],
                    ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas raf erişimleri']
                ]
            ],
            [
                'sku' => 'TDL162-6000-TPL',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF TDL162 - 6000 mm Triplex Yüksek Erişim',
                'short_description' => '6.0 m’ye kadar erişim sunan triplex mast; AC sürüş ve elektromanyetik frenlerle birlikte güvenli yavaşlatma ve hızlanma, yüksek raf operasyonlarında güven verir.',
                'body' => '<section><h3>6.0 m Yüksek Erişim: Depo Hacmini Üçe Katlayın</h3><p>Depo tavan yüksekliğinin her santimetresini verime çeviren 6.0 m triplex mast, TDL162’nin 80V mimarisiyle birleşince yüksek raflarda güvenli hız ve denge sağlar. Residual kapasite eğrisi optimize edilerek çalışma aralığında kontrollü hızlanma ve yavaşlatma sağlanır. Entegre şarj cihazı, ara molalarda fırsat şarjını kolaylaştırırken harici hızlı şarj, çok vardiyalı tesislerde kesintisiz akışı mümkün kılar.</p></section><section><h3>Denge ve Operatör Deneyimi</h3><p>1050 mm gövde genişliği ve geliştirilmiş görüş alanı, yüksek seviyelerde çatal konumlandırmayı kolaylaştırır. 65 dB(A) gürültü seviyesi, uzun süreli konsantrasyonu destekler. LED ekran ve performans modu seçici, yük tipine göre davranışı kişiselleştirir. Joystick hidrolik kontrol opsiyonu, hassas ve tekrarlanabilir istif hareketleri sağlar.</p></section><section><h3>Kullanım Senaryoları</h3><p>Yüksek raflı 3PL depoları, elektronik ve yedek parça istif alanları ya da içecek lojistiğinde yüksek istif gereksinimleri için uygundur. Güvenlik ve hız arasında doğru dengeyi kurar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı 3PL depo istifleri'],
                    ['icon' => 'microchip', 'text' => 'Elektronik ve teknoloji stok alanları'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek lojistiğinde yüksek katlı raflar'],
                    ['icon' => 'briefcase', 'text' => 'B2B toptan stok yönetimi'],
                    ['icon' => 'industry', 'text' => 'Yarı mamul ve mamul ara depo istifleri'],
                    ['icon' => 'cart-shopping', 'text' => 'Market zinciri bölge depolarında üst seviye erişim']
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
            $this->command->info('✅ Varyant eklendi: ' . $v['sku']);
        }
    }
}
