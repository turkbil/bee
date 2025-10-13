<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RPL201_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'RPL201')->first();
        if (!$m) {
            return;
        }
        $variants = [
            [
                'sku' => 'RPL201-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF RPL201 - 1150 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal ile EUR paletlerde maksimum manevra ve akış hızı; uzun mesafe iç lojistik için ideal.',
                'body' => "<section><h2>1150 mm ile Standartların Hızlısı</h2><p>1150 mm çatal, EUR palet (1200×800) uyumunda en verimli çevikliği sunar...</p></section>",
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR palet akışlarında standart boy ile hızlı çekme/itme'],
                    ['icon' => 'warehouse', 'text' => '3PL içinde iç hat besleme ve tampon alan yönetimi'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf arası kısa manevralar'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresinde WIP taşıma'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça kitting'],
                    ['icon' => 'flask', 'text' => 'Kimya paketleme hatları besleme'],
                ],
            ],
            [
                'sku' => 'RPL201-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF RPL201 - 1220 mm Çatal',
                'short_description' => '1220 mm uzun çatal ile heterojen yüklerde ek denge ve taşıma kolaylığı; depolar arası transferlerde avantaj.',
                'body' => "<section><h2>1220 mm ile Çok Yönlü Taşıma</h2><p>1220 mm varyant, uzun yükler ve karışık koli dağılımlarında esneklik sağlar...</p></section>",
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Karışık koli ve blok yük taşımaları'],
                    ['icon' => 'warehouse', 'text' => 'Uzun paletler için rampaya yaklaşma ve yükleme'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depoda geniş ürün yelpazesi'],
                    ['icon' => 'pills', 'text' => 'İlaç paletlerinde çeşitliliğe uyum'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil rulolarında denge avantajı'],
                    ['icon' => 'industry', 'text' => 'Ağır hizmet ambalaj hatlarında ara taşıma'],
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
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
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
