<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFS151_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFS151')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            [
                'sku' => 'EFS151-3M',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFS151 - 3.0 m Standart Direk',
                'short_description' => '3000 mm kaldırma yüksekliğine sahip standart mast, 1980 mm kapalı direk yüksekliği ve 4054 mm açık yükseklik ile alçak tavanlı depolarda optimum denge sağlar. Dar koridorlar için 1535 mm dönüş yarıçapı avantajını korur.',
                'long_description' => '<section><h2>3.0 m standart direk: alçak tavan uyumu ve günlük istifleme verimi</h2><p>3000 mm kaldırma yüksekliği, katlar arası malzeme transferi ve mağaza arkası stok yönetimi için ideal bir eşik sunar. 1980 mm kapalı direk yüksekliği sayesinde kapı geçişleri ve düşük katlı alanlarda giriş-çıkış sorunsuzdur. 1.5 ton nominal kapasite ve 500 mm yük merkezi ile dengeli yük tutuşu korunur; 1060 mm gövde genişliği ve 1535 mm dönüş yarıçapı manevrayı kolaylaştırır.</p></section><section><h3>Teknik parametreler ve operasyonel etkisi</h3><p>8/9 km/s sürüş hızı ve 0.25/0.30 m/s kaldırma hızları, şehir içi yoğun vardiyalarda bile akıcı akış sağlar. 48V/150Ah Li-Ion akü paketi entegre 48V/30A şarj cihazı ile fırsat şarjını destekler. AC sürüş ve hidrolik fren kombinasyonu rampalarda kontrollü ivmelenme ve güvenli duruş getirir. Solid lastikler, pürüzlü zeminlerde darbe emilimi sunarken iz bırakmayan lastik seçeneği zemin koruması isterler için uygundur. Bluetooth servis uygulaması, parametre ayarı ve arıza kodu okumayı mobil cihazdan yönetmenize olanak tanır.</p></section><section><h3>Sonuç</h3><p>Alçak tavan, yük asansörü veya mezanin gibi kısıtlı yükseklikli alanlarda 3.0 m mast konfigürasyonu pratik bir çözümdür. Stok sahalarında günlük istifleme ve hat besleme için dengeli performans verir.</p></section>',
                'use_cases' => [
                    ['icon' => 'store', 'text' => 'Perakende arka depolarında katlar arası ürün akışı'],
                    ['icon' => 'warehouse', 'text' => 'Mezanin ve alçak tavanlı alanlarda palet istifleme'],
                    ['icon' => 'box-open', 'text' => 'Kargo liftleri ile mağaza içi dağıtım'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı besleme ve WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Kentsel mikrodepo ve otopark sevkiyat operasyonları'],
                    ['icon' => 'building', 'text' => 'Eski fabrika binalarında dar koridor transferi']
                ]
            ],
            [
                'sku' => 'EFS151-3_3M',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFS151 - 3.3 m Standart Direk',
                'short_description' => '3300 mm kaldırma ile raf yüksekliği biraz daha fazla olan depolara uyum sağlar. 1995 mm tavan ve 1060 mm genişlik, kısıtlı alanlarda konforlu sürüş ve kontrollü istiflemeyi destekler.',
                'long_description' => '<section><h2>3.3 m mast: daha yüksek raflar için esnek erişim</h2><p>3300 mm kaldırma yüksekliği, mağaza arkası ve 3PL raf dizilimlerinde ek esneklik sunar. 1.5 ton kapasite sınırları içinde, ağırlık merkezi 500 mm olan yüklerde dengeli kaldırma korunur. Gövde genişliği 1060 mm ve dönüş yarıçapı 1535 mm ile dar koridorlarda yön değişimleri hızlı ve güvenlidir.</p></section><section><h3>Enerji ve sürüş</h3><p>48V Li-Ion enerji mimarisi, 150Ah kapasite ve entegre tek faz 48V/30A şarj cihazı ile gün boyu kullanılabilirlik sağlar. 6 kW AC sürüş ve 5.5 kW kaldırma motoru, hızlanma ve hassas konumlandırmada tutarlı bir his verir. Hidrolik servis freni ve mekanik park freni kombinasyonu, rampalarda dur-kalklarda emniyeti güçlendirir. Opsiyonel side-shifter ile hassas konumlama kolaylaşır; bu durumda nominal kapasiteden 200 kg düşülmesi önerilir.</p></section><section><h3>Sonuç</h3><p>Raf yüksekliği 3 metreyi aşan, ancak tavan kısıtları devam eden alanlar için 3.3 m mast pratik bir orta çözüm sunar. Kapı ve yük asansörü geçişlerinde kompakt yapıyı korur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL raf dizilimlerinde katlar arası toplama'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim dağıtım merkezlerinde replenishment'],
                    ['icon' => 'building', 'text' => 'Düşük kapı eşikleri olan depolarda giriş-çıkış'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret mikro-fulfillment alanlarında istifleme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal hammadde depolarında palet transferi'],
                    ['icon' => 'pills', 'text' => 'İlaç lojistiğinde raf önü konumlandırma']
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
            $this->command->info("✅ Variant eklendi: {$v['sku']}");
        }
    }
}
