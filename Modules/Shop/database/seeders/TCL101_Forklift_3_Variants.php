<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TCL101_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'TCL101')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: TCL101'); return; }

        $variants = [
            [
                'sku' => 'TCL101-80V50Ah',
                'variant_type' => 'batarya-kapasitesi',
                'title' => 'İXTİF TCL101 - 80V 50Ah Li-Ion',
                'short_description' => 'Standart 80V/50Ah Li-Ion batarya, entegre 80V-35A şarj cihazı ile molalarda fırsat şarjına uygundur. Kompakt şasi, h6<2000 mm ve 1422 mm dönüş ile dar alanlara çözüm sunar.',
                'long_description' => '<section><h2>80V/50Ah ile kompakt verim</h2><p>TCL101’in standart 80V/50Ah konfigürasyonu, dar alanlı tesislerde çeviklik ve yeterli çalışma süresini dengeler. Entegre tek fazlı şarj cihazı sayesinde vardiya içi molalarda kısa fırsat şarjlarıyla tempoyu korur. 1422 mm dönüş yarıçapı ve h6&lt;2000 mm üst koruma yüksekliği, asma kat ve yük asansörü geçişlerinde işi kolaylaştırır.</p></section><section><h3>Teknik karakter</h3><p>Çift 2.0 kW sürüş motoru ve 7 kW kaldırma motoru, 11/13 km/sa hız, 280/350 mm/sn kaldırma ve 350/350 mm/sn indirme değerlerini sunar. 1000 kg @ 500 mm kapasite, 1200 mm dingil mesafesi ve 1020 mm genişlik ile tipik depo görevlerinde optimal denge sağlar. PMSM mimarisi %10-15 enerji tasarrufu getirir.</p></section><section><h3>Kullanım alanları</h3><p>Perakende DC’lerde raf içi taşıma, HGV treyler içi palet pozisyonlama ve üretim hatları arasında WIP akışı gibi görevlerde öne çıkar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Asma kat geçişleri ve yük asansörü operasyonları'],
                    ['icon' => 'box-open', 'text' => 'Treyler içi palet yerleştirme ve çekme-itme hareketleri'],
                    ['icon' => 'store', 'text' => 'Raf arası dar koridor iç lojistik'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresi besleme ve WIP transferi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depo içi hat besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça akışı']
                ]
            ],
            [
                'sku' => 'TCL101-80V100Ah',
                'variant_type' => 'batarya-kapasitesi',
                'title' => 'İXTİF TCL101 - 80V 100Ah Li-Ion (Uzun Çalışma)',
                'short_description' => '80V/100Ah batarya ile daha uzun çalışma süresi ve daha az şarj molası. PMSM verimliliği ve otomatik park freni ile yoğun vardiya akışlarında yüksek süreklilik sağlar.',
                'long_description' => '<section><h2>80V/100Ah ile uzun vardiya sürekliliği</h2><p>Opsiyonel 80V/100Ah Li-Ion paket, uzun vardiyalarda daha az şarj molası ile hat besleme akışını kesintisiz kılar. Entegre şarj donanımı altyapıyı basitleştirir; fırsat şarjı ile vardiya planları esner. H6 değeri 2000 mm altında olan gövde, katlar arası erişimde esneklik sunar.</p></section><section><h3>Performans ve güvenlik</h3><p>13 km/sa azami hız, elektromanyetik servis/park frenleri, dönüş hız kontrolü ve %13/%15 eğim kabiliyeti ile hem güvenli hem de hızlıdır. PMSM mimarisi enerji tüketimini düşürerek çalışma süresini uzatır.</p></section><section><h3>Uygulama senaryoları</h3><p>3PL merkezlerinde yoğun toplama-besleme döngüleri, soğuk oda giriş-çıkış noktaları ve rampa çevresindeki sirkülasyonlarda yüksek süreklilik sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL toplama-besleme ve cross-dock alanları'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış lojistiği'],
                    ['icon' => 'box-open', 'text' => 'Yüksek hacimli e-ticaret palet akışı'],
                    ['icon' => 'pills', 'text' => 'Hassas ürün depolarında kesintisiz akış'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG yoğun vardiya operasyonları'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı besleme ve tampon alan transferi']
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
