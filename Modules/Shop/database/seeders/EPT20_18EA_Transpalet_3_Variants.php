<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_18EA_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT20-18EA')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            [
                'sku' => 'EPT20-18EA-1150x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-18EA - 1150×540 mm Çatal',
                'short_description' => 'Standart EUR paletler için 1150×540 mm çatal: dar koridor çevikliği, 1.8 ton nominal kapasite ve AC sürüş kontrolüyle akıcı operasyona odaklı.',
                'long_description' => '<section><h2>1150×540 mm: Standart Palet Akışının Güvenli Ritmi</h2><p>Standart EUR palet boyutlarına tam uyum sağlayan 1150×540 mm çatal yapılandırması, EPT20-18EA’nın kompakt gövdesiyle birleştiğinde vardiya boyunca hızlı ve ritmik akış üretir. 1457 mm dönüş yarıçapı, dar raf aralarında minimum düzeltme manevrası ile ilerlemeye imkân tanır. 24V 85Ah akü, harici 15A/20A şarj seçenekleriyle desteklenir; AC sürüş kontrolü, düşük hızda hassas konumlandırma ve rampalarda akıcı kalkış sağlar. 85 mm düşük çatal yüksekliği, yük alma–bırakma süreçlerinde hataları azaltır; 105 mm kaldırma yüksekliği paletin zeminden güvenle ayrılmasını sağlar. PU teker seti, titreşimi düşürerek ürün hasar riskini azaltırken, elektromanyetik fren ani duruşlarda dengeyi korur.</p><p>Bu varyant 540 mm çatal aralığıyla market, perakende ve e-ticaret operasyonlarında en yaygın palet tiplerini hedefler. 1625 mm toplam uzunluk ve 645 mm genişlik, 2120–2258 mm koridor gereksinimlerine uyum gösterir. Yüklü %6 eğim kabiliyeti, rampa ve yükleme istasyonu geçişlerinde yeterli çekiş sunar; yüksüz %16 değeriyse iç lojistikte hızlı yer değiştirmeleri destekler. Operatör için 715–1200 mm aralığında ayarlanabilen kontrol kolu, farklı boy ve çalışma alışkanlıklarına konfor sağlar.</p><section><h3>Sonuç</h3><p>1150×540 mm yapılandırma, depo içi “genel amaçlı” kullanımın sağlam standardıdır. E-Ticaret ve 3PL operasyonlarında güvenilir, sessiz ve ekonomik performans hedefleyenler için ideal çözümdür.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret inbound–outbound akışında standart EUR palet taşıma'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de raf arası replenishment ve toplama besleme'],
                    ['icon' => 'warehouse', 'text' => '3PL çapraz sevkiyat hatlarında kısa mesafe transfer'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parçalarında hat besleme ve ara stok taşıma'],
                    ['icon' => 'snowflake', 'text' => 'Gıda soğuk oda giriş–çıkışlarında kısa süreli taşıma'],
                    ['icon' => 'industry', 'text' => 'Genel sanayide WIP palet hareketleri']
                ]
            ],
            [
                'sku' => 'EPT20-18EA-1220x685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-18EA - 1220×685 mm Çatal (Geniş)',
                'short_description' => '1220×685 mm geniş çatal seçeneği, farklı palet tiplerinde stabilite ve geniş oturma yüzeyi sağlayarak ürün zararını azaltmaya yardımcı olur.',
                'long_description' => '<section><h2>1220×685 mm: Geniş Paletlerde Stabilite Önceliği</h2><p>Daha geniş oturma yüzeyi ve 1220 mm çatal uzunluğu ile bu varyant, karışık palet tiplerinin bulunduğu depolarda stabiliteyi artırır. 685 mm çatal genişliği palet yüzeyini daha iyi kavrayarak kavrama hatalarını azaltır; PU teker seti ve AC sürüş kontrolüyle birlikte düşük titreşimli ve kontrollü ilerleme sağlar. 24V 85Ah enerji altyapısı, harici şarj cihazlarıyla vardiya planına uygun şekilde yönetilir. 1457 mm dönüş yarıçapı ve 645 mm gövde genişliği, geniş çatal setine rağmen raf aralarında dengeli manevrayı sürdürür.</p><p>Operasyonel olarak bu konfigürasyon içecek, beyaz eşya/elektronik ve mobilya gibi daha geniş veya esnek ambalajlı ürünlerin bulunduğu koridorlarda tercih edilir. 6%/16% eğim kabiliyetleri rampalarda güven verir; elektromanyetik fren beklenmedik duruşlarda yük kontrolünü destekler. 85 mm düşük çatal yüksekliği ürünün palet üzerinde bozulmadan alınmasını kolaylaştırır; 105 mm kaldırma yüksekliği kısa iç taşıma mesafelerinde yeterlidir. Mekanik direksiyon ve ayarlanabilir kontrol kolu, gün boyu konforu korur.</p><section><h3>Sonuç</h3><p>1220×685 mm varyantı, karışık palet ve farklı ürün gamı olan depolarda ürün bütünlüğünü korumaya odaklanan, güvenli ve öngörülebilir bir seçenektir.</p></section>',
                'use_cases' => [
                    ['icon' => 'cart-shopping', 'text' => 'FMCG ve içecek depolarında karışık palet taşıma'],
                    ['icon' => 'microchip', 'text' => 'Elektronik ve beyaz eşya lojistiğinde geniş palet akışı'],
                    ['icon' => 'warehouse', 'text' => '3PL içinde değişken müşteri palet standardı yönetimi'],
                    ['icon' => 'store', 'text' => 'Perakende mağaza beslemede kırılgan ürün transferi'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası finished goods ara stok hareketleri'],
                    ['icon' => 'box-open', 'text' => 'Kargo hub’larında farklı palet tipleri arasında dağıtım']
                ]
            ],
            [
                'sku' => 'EPT20-18EA-1000x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-18EA - 1000×540 mm Çatal (Kısa)',
                'short_description' => '1000×540 mm kısa çatal, dar alan ve asansörlü tesislerde çeviklik, hızlı konumlandırma ve düşük dönüş yarıçapı avantajı sunar.',
                'long_description' => '<section><h2>1000×540 mm: Sıkışık Alanlarda Çevik Kullanım</h2><p>1000 mm çatal uzunluğu, asansör, rampa üstü ve dar koridor geçişlerinin yoğun olduğu tesislerde çevik hareket kabiliyeti kazandırır. Kısa çatal, palet alınırken gereken konumlandırma mesafesini kısaltır; böylece hat başı ve sonu noktalarda bekleme süreleri azalır. 24V 85Ah enerji yapısı ve AC sürüş kontrolü hassas hız yönetimi sağlar. PU tekerler titreşimi azaltır; elektromanyetik frenleme özellikle rampa başlarında güvenli duruş sağlar. 85 mm düşük çatal yüksekliği kırılgan ürünlerde zarar riskini azaltır.</p><p>Bu konfigürasyon, şehir içi kentsel depolar, katlı mağaza lojistiği ve mikrodepo konseptlerinde etkin bir çözümdür. 1457 mm dönüş yarıçapı ve 645 mm gövde genişliği, sıkışıksa bile kontrollü manevra sunar. 6%/16% eğim kabiliyeti rampa–yükleme alanı geçişlerini destekler; 105 mm kaldırma yüksekliği kısa mesafe iç transferlerde yeterlidir.</p><section><h3>Sonuç</h3><p>1000×540 mm varyantı, çeviklik ve hızın kritik olduğu dar alan operasyonlarında akıcı, güvenli ve ekonomik bir alternatif sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'building', 'text' => 'Katlı mağaza ve AVM içi lojistikte dar alan hareketi'],
                    ['icon' => 'box-open', 'text' => 'Mikrodepo ve şehir içi fulfillment noktalarında hızlı transfer'],
                    ['icon' => 'warehouse', 'text' => 'Asansörlü tesislerde katlar arası palet taşıma'],
                    ['icon' => 'store', 'text' => 'Küçük format perakendede hat besleme ve toplama'],
                    ['icon' => 'car', 'text' => 'Araç rampa alanlarında kısa mesafe manevra'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde noktasal WIP taşıma']
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
        }

        $this->command->info('✅ Variants eklendi: EPT20-18EA (1150×540, 1220×685, 1000×540)');
    }
}
