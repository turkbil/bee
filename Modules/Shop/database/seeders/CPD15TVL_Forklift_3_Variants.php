<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CPD15TVL_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'CPD15TVL')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: CPD15TVL');
            return;
        }

        $variants = [
            [
                'sku' => 'CPD15TVL-CATAL-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD15TVL 1070 mm Çatal',
                'short_description' => 'Kısa çatal ile dar koridorda üstün çeviklik.',
                'long_description' => '<section><h2>İXTİF CPD15TVL 1070 mm Çatal</h2><p>Kısa çatal seçeneği özellikle dar dönüş alanlarında çevik hareket sağlar. Kompakt şasi, dengeli ağırlık dağılımı ve hassas kontrol algoritmaları ile dar alan manevraları güvenle yapılır.</p></section><section><h3>Teknik</h3><p>Kapasite 1500 kg, yük merkezi 500 mm, standart kaldırma yüksekliği 3000 mm. Li-Ion 48 V / 360 Ah ve rejeneratif frenleme ile uzun çalışma süresi elde edilir.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Dar koridorda palet alma/verme'],
                    ['icon' => 'store', 'text' => 'Arka depo raf besleme'],
                    ['icon' => 'warehouse', 'text' => 'Konteyner içi yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Hat besleme ve WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Yan sanayi malzeme akışı'],
                    ['icon' => 'cart-shopping', 'text' => 'Yoğun toplama operasyonlarında çevik kullanım'],
                ],
            ],
            [
                'sku' => 'CPD15TVL-CATAL-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF CPD15TVL 1150 mm Çatal',
                'short_description' => 'Standart Avrupa paleti için ideal uzunluk.',
                'long_description' => '<section><h2>İXTİF CPD15TVL 1150 mm Çatal</h2><p>Standart palet boyutlarında denge ve erişim için optimum uzunluk sunar. Kompakt şasi, dengeli ağırlık dağılımı ve hassas kontrol algoritmaları ile dar alan manevraları güvenle yapılır.</p></section><section><h3>Teknik</h3><p>Kapasite 1500 kg, yük merkezi 500 mm, standart kaldırma yüksekliği 3000 mm. Li-Ion 48 V / 360 Ah ve rejeneratif frenleme ile uzun çalışma süresi elde edilir.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Dar koridorda palet alma/verme'],
                    ['icon' => 'store', 'text' => 'Arka depo raf besleme'],
                    ['icon' => 'warehouse', 'text' => 'Konteyner içi yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Hat besleme ve WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Yan sanayi malzeme akışı'],
                    ['icon' => 'cart-shopping', 'text' => 'Standart paletlerde stabil ve güvenli taşıma'],
                ],
            ],
            [
                'sku' => 'CPD15TVL-MAST-3300',
                'variant_type' => 'mast-yuksekligi',
                'title' => 'İXTİF CPD15TVL 3300 mm Mast',
                'short_description' => 'Orta seviye raflar için dengeli çözüm.',
                'long_description' => '<section><h2>İXTİF CPD15TVL 3300 mm Mast</h2><p>Orta seviye raf erişimi için kaldırma kapasitesi ve stabiliteyi birleştirir. Kompakt şasi, dengeli ağırlık dağılımı ve hassas kontrol algoritmaları ile dar alan manevraları güvenle yapılır.</p></section><section><h3>Teknik</h3><p>Kapasite 1500 kg, yük merkezi 500 mm, standart kaldırma yüksekliği 3000 mm. Li-Ion 48 V / 360 Ah ve rejeneratif frenleme ile uzun çalışma süresi elde edilir.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Dar koridorda palet alma/verme'],
                    ['icon' => 'store', 'text' => 'Arka depo raf besleme'],
                    ['icon' => 'warehouse', 'text' => 'Konteyner içi yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Hat besleme ve WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Yan sanayi malzeme akışı'],
                    ['icon' => 'cart-shopping', 'text' => 'Orta yükseklikte raf düzenlerine uygun'],
                ],
            ],
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
    }
}
