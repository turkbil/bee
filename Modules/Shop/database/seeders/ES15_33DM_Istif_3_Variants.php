<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES15_33DM_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ES15-33DM')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: ES15-33DM'); return; }

        $variants = [
            [
                'sku' => 'ES15-33DM-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES15-33DM - 1220 mm Çatal',
                'short_description' => '1220 mm çatal uzunluğu, uzun yüklerde daha iyi ağırlık dağıtımı ve daha az dışa taşma ile güvenli merkezleme sağlar.',
                'long_description' => '<section><h3>Uzun Yükler İçin Doğru Denge</h3><p>1220 mm çatal, standart 1070 mm’e kıyasla uzun yüklerin ağırlık merkezini daha geniş bir yüzeye yayar. Bu, özellikle içecek kasaları, uzun koliler ve endüstriyel ambalajlarda dengeyi artırır. Geniş şase ve ayarlanabilir çatal aralığı ile birlikte çalıştığında, palet dışı sarkmaları azaltır ve operatörün güvenli ilerlemesini destekler.</p></section><section><h3>Teknik Tutarlılık</h3><p>Platform; 24V 125Ah enerji, 5 km/s seyir hızı, elektromanyetik fren ve AC sürüş mimarisini korur. Kaldırma ve indirme değerleri ana modelle aynıdır. Çatalın uzaması yük yaklaşma mesafesinde hassasiyeti artırırken, düzgün yerleştirme kabiliyetini güçlendirir.</p></section><section><h3>Uygulama Örnekleri</h3><p>Uzun kaset paletleri, gıda-içecek sektöründe kasa kuleleri, mobilya ve ambalaj gibi uzun paketli ürün grupları için önerilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasa kulelerinde dengeli kavrama'],
                    ['icon' => 'couch', 'text' => 'Mobilya ve uzun paketli ürünlerin taşınması'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment depolarında uzun koli istiflemesi'],
                    ['icon' => 'warehouse', 'text' => 'Geniş koridorlarda uzun yük döngüleri'],
                    ['icon' => 'industry', 'text' => 'Üretimden sevkiyata uzun malzeme akışı'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parçada uzun kasetler']
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
        $this->command->info('✅ Variants eklendi: ES15-33DM');
    }
}
