<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EXP15_Otonom_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EXP15')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EXP15');
            return;
        }

        $variants = [
            [
                'sku' => 'EXP15-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EXP15 - 1150 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal, 540/600/685 mm çatallar arası genişlik ve 1500 kg kapasite ile genel amaçlı EUR palet taşıma için optimize edilmiştir; 1.1/1.25 m/s hız ve ±20 mm park doğruluğu sunar.',
                'body' => '<section><h2>1150 mm ile evrensel palet uyumu</h2><p>EXP15\'in 1150 mm çatal versiyonu, Avrupa standart palet ölçülerine odaklanan genel amaçlı iç lojistik için en dengeli konfigürasyondur. 540/600/685 mm çatallar arası genişlik seçenekleriyle, karma ürün akışına sahip depolarda birden çok palet tipine esnek uyum sağlar. 24V/60Ah Li-İon tak-çıkar batarya hızlı değişimle vardiya sürekliliği sunar; AC sürüş ve elektromanyetik fren, hassas hız kontrolü ile güvenli duruş sağlar.</p></section><section><h3>Teknik ve operasyon</h3><p>1.1/1.25 m/s sürüş hızları ve 1400 mm dönüş yarıçapı, 1860 mm koridor gereksiniminde akıcı manevra sağlar. 180° lidar ve alt lazer kapsaması, 2D görsel navigasyonun ±20 mm park doğruluğunu destekleyerek tekrarlanabilir bırakma/alış sağlar. Görev belleğinde 10 ayrı rota saklanabilir; operatör +/− tuşlarıyla rota seçimleri arasında hızla dolaşır. 5/5 % eğim kabiliyeti ile rampa ve eşik geçişleri güven altındadır.</p></section><section><h3>Sonuç</h3><p>Standart palet akışının çoğunlukta olduğu senaryolarda ideal dengeyi sunar. Teknik danışmanlık için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Evrensel EUR palet transferi (tek yönlü hatlar)'],
                    ['icon' => 'warehouse', 'text' => '3PL depolarında giriş-çıkış bant beslemesi'],
                    ['icon' => 'store', 'text' => 'Perakende DC sipariş konsolidasyon bölgeleri'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresinden tampon stoğa taşıma'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda eşiği yakınında düşük hız geçişleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent alanları arasında nokta-nokta akış']
                ]
            ],
            [
                'sku' => 'EXP15-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EXP15 - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yüklerde daha iyi palet dengesi sağlar. 1500 kg kapasite, 1.1/1.25 m/s hız ve 180° lidar ile tekrarlı taşımalarda yüksek stabilite ve güvenlik sunar.',
                'body' => '<section><h2>Uzun yüklerde denge ve temas</h2><p>1220 mm çatal seçeneği, daha uzun kutu/kasalar veya tam dolu içecek paletlerinde daha geniş temas yüzeyi sağlayarak dengeyi artırır. 2D görsel navigasyonun ±20 mm hassasiyeti ve lidar tabanlı güvenlik, uzun yüklerde bile merkezlemeyi kolaylaştırır. AC sürüş mimarisi ve elektromanyetik frenleme, düşük hız torkunu kontrollü duruşla birleştirir.</p></section><section><h3>Teknik ve operasyon</h3><p>636–700 mm gövde genişliği ve 1400 mm dönüş yarıçapı, uzun paletlerle raf arası dönüşleri yönetilebilir kılar. 24V/60Ah Li-İon enerji paketi vardiya içinde tak-çıkar ile hızlıca yenilenir. 10 görev hafızası, tekrarlı toplama ve bırakma istasyonları arasında esnek görev planlaması sağlar.</p></section><section><h3>Sonuç</h3><p>Uzun ve ağır yük yoğunluklu hatlarda süreklilik ve süreç standardizasyonu getirir. Bilgi için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'İçecek kasalarında tek hatlı transfer'],
                    ['icon' => 'warehouse', 'text' => 'Yüksek hacimli tampon stok alanı besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG yoğun bölgelerde toplama-bırakma'],
                    ['icon' => 'flask', 'text' => 'Kimyada uzun konteyner palet hareketleri'],
                    ['icon' => 'pills', 'text' => 'İlaçta raf önü-arka depo arası shuttle'],
                    ['icon' => 'couch', 'text' => 'Mobilya parça kutularında uzun palet taşıma']
                ]
            ],
            [
                'sku' => 'EXP15-1000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EXP15 - 1000 mm Çatal',
                'short_description' => '1000 mm çatal, dar koridorlu alanlarda minimum dönüş çapı hedefleyen tesisler için çeviklik sağlar; ±20 mm park doğruluğu ve 1860 mm koridor ihtiyacıyla hızlı çevrimler elde edilir.',
                'body' => '<section><h2>Dar koridorlarda çeviklik önceliği</h2><p>1000 mm çatal, kompakt yükler ve mini paletlerde çevik manevra isteyen hatlar için optimize edilmiştir. Daha kısa çıkıntı, dönüşlerde sürtünmeyi azaltır; lidar ve alt lazer kapsamasıyla güvenli alanlar korunur. 2D görsel navigasyon, kısa çatalın hizalama esnekliğini ±20 mm park doğruluğu ile güvence altına alır.</p></section><section><h3>Teknik ve operasyon</h3><p>AC sürüş mimarisi, 1.1/1.25 m/s hızlarda enerji verimini yukarı taşır. 24V/60Ah tak-çıkar batarya, çok vardiyalı operasyonlarda hızlı değişimle süreklilik sağlar. 10 görev belleği, dar koridor İç-Dış hatlar arasında esnek görev planlamasına imkân verir.</p></section><section><h3>Sonuç</h3><p>Dar alanlı depolarda çevrim süresini kısaltır ve hat içi akışı standartlaştırır. Detaylı keşif için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Üretim hattı yan besleme ve geri dönüş'],
                    ['icon' => 'warehouse', 'text' => 'Dar koridor raf arası mikro akışlar'],
                    ['icon' => 'store', 'text' => 'Perakende mağaza arkası kompakt depolar'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk odada kısa mesafe ring taşıma'],
                    ['icon' => 'microchip', 'text' => 'Elektronik montaj hücresine WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Otomotiv küçük parça kitting bölgeleri']
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
            $this->command->info("✅ Variant eklendi: {$v['sku']}");
        }
    }
}
