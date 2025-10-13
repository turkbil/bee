<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET2_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EPT20-15ET2')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        $variants = [
            [
                'sku' => 'EPT20-15ET2-0800',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-15ET2 - 800 mm Çatal',
                'short_description' => '800 mm çatal; mikro alanlarda düşük kuyruk salınımı ve hızlı hizalama sağlar, paketleme ve sıralama istasyonlarında çevik akış sunar.',
                'body' => '<section><h2>800 mm: Mikro Koridorların Hızlı Oyuncusu</h2><p>800 mm çatal uzunluğu, palet burun mesafesini kısaltarak dar dönüşlerde operatöre kontrol avantajı verir. Cross-dock ve paketleme hatlarında palete yaklaşma–çekilme döngüleri hızlanır. Hafif-orta yüklerde sessiz PU tekerler titreşimi sönümler, elektromanyetik fren ise rampa ve eşik geçişlerinde tutarlı duruş sağlar.</p></section><section><h3>Teknik Odak</h3><p>EPT20-15ET2’nin 1.5 ton kapasitesi ve DC sürüş mimarisi, kısa çatalın getirdiği çeviklikle birleşir. 1475 mm dönüş yarıçapı ve 1638 mm toplam uzunluk, 2.2–2.3 m koridorlarda güvenli manevra alanı yaratır. 24V/65Ah Li-ion batarya ve 24V-10A dahili şarj ile ara şarjlar kolaylaşır; vardiya içi molalarda kapasite geri kazanılır.</p></section><section><h3>Kullanım Önerileri</h3><p>Fulfillment istasyonları, mağaza arkası depo ve üretim hücresi içi beslemelerde yüksek manevra kabiliyeti. Hijyen hassas iç mekânlarda Trace PU opsiyonu önerilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Mikro fulfillment istasyonlarında kısa mesafe transfer'],
                    ['icon' => 'warehouse', 'text' => 'Cross-dock alanlarında hızlı yön değiştirme'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası raf içi akış'],
                    ['icon' => 'cart-shopping', 'text' => 'Toplama ve tampon stok besleme'],
                    ['icon' => 'industry', 'text' => 'Hücre içi WIP transferleri'],
                    ['icon' => 'box-open', 'text' => 'Koli ayrıştırma ve paketleme hatları']
                ]
            ],
            [
                'sku' => 'EPT20-15ET2-2000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPT20-15ET2 - 2000 mm Çatal',
                'short_description' => '2000 mm çatal; hacimli paletlerde taban temasını artırır, uzun ürünlerde denge sağlayarak yükleme alanında daha güvenli ilerleme sunar.',
                'body' => '<section><h2>2000 mm: Hacimli Yüklerde Geniş Destek</h2><p>Uzun çatal, taban teması ve moment kolunu genişleterek hacimli paletlerde stabiliteyi artırır. Otomotiv komponentleri veya içecek kasaları gibi kombinasyonlarda sallantı eğilimi azalır. Elektromanyetik fren eğim geçişlerinde öngörülebilir duruş sağlar.</p></section><section><h3>Teknik Odak</h3><p>EPT20-15ET2’nin 0.75 kW sürüş ve 0.84 kW kaldırma motorları, uzun çatalın ihtiyaç duyduğu kontrollü ivmeyi sunar. 24V/65Ah Li-ion enerji ve dahili şarj ile yoğun hatlarda ara şarj stratejisi uygulanabilir. Dar alanlarda dönüş planlaması yapılmalı; görüş ve tampon alanları önceliklendirilmelidir.</p></section><section><h3>Kullanım Önerileri</h3><p>Yükleme alanları, kasa paletleri ve geniş tabanlı karışık paletlerde güvenli taşıma. Düzensiz zeminlerde opsiyonel caster teker ile yön stabilitesi artırılabilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'car', 'text' => 'Otomotiv komponent ve hacimli koli transferi'],
                    ['icon' => 'snowflake', 'text' => 'Gıda/içecek kasası paletlerinde geniş temas'],
                    ['icon' => 'warehouse', 'text' => 'Yükleme alanı tampon stok yönetimi'],
                    ['icon' => 'industry', 'text' => 'Üretim sahasında uzun parça taşıma'],
                    ['icon' => 'cart-shopping', 'text' => 'Büyük hacimli e-ticaret sevkiyat paletleri'],
                    ['icon' => 'box-open', 'text' => 'Karışık palet kombinasyonlarında stabilizasyon']
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

        $this->command->info("✅ Variants: EPT20-15ET2 → " . count($variants) . " adet");
    }
}
