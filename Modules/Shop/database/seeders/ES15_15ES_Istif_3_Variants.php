<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES15_15ES_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'ES15-15ES')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: ES15-15ES');
            return;
        }

        $variants = [
            [
                'sku' => 'ES15-15ES-1000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF ES15-15ES - 1000 mm Çatal',
                'short_description' => '1000 mm çatal uzunluğu ile dar palet ve ara stok istasyonlarına yaklaşmada daha çevik; manevra alanı sınırlı raf sokakları için optimize edilir.',
                'body' => '<section><h3>Operasyonel Fark</h3><p>1000 mm çatal konfigürasyonu, özellikle dar paletli ürün gruplarında ve ara stok istasyonlarına yakın çalışmalarda manevra üstünlüğü sağlar. Daha kısa çıkıntı, raflar arası yaklaşmayı ve yükü taşıyıcıya ortalamayı kolaylaştırır. 1.5 ton nominal kapasite korunurken, çatal boyunun kısalması dönüş yarıçapı içinde kalan payı artırır; bu da başta çapraz geçişler olmak üzere sık hamleli işlerde hız kazandırır.</p></section><section><h3>Teknik Uyum</h3><p>Şasi, direk ve tahrik sistemleri ana model ile aynıdır: 24V 125Ah enerji mimarisi, 5 km/s seyir hızı ve elektromanyetik fren sistemi standarttır. PU teker dizilimi titreşimi düşürür. Kaldırma-İndirme hızları (0.13/0.20 ve 0.13/0.13 m/s) yük emniyetini korur. 1000 mm çatal, ürüne yaklaşma mesafesini azaltarak dar alanlarda regale hızlı yerleştirme sağlar.</p></section><section><h3>Kullanım Senaryoları</h3><p>Dar paletlerde, e-ticaret iade ayrıştırma noktalarında, üretim hücrelerinde WIP akışında ve mağaza arkası depolarda hızlı manevra gerektiren görevlerde öne çıkar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticaret iade ve ayrıştırma masalarında kısa mesafe manevra'],
                    ['icon' => 'store', 'text' => 'Mağaza arkası kompakt depolarda raf besleme'],
                    ['icon' => 'warehouse', 'text' => '3PL mikro-fulfillment alanlarında sık dönüş'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP akışı'],
                    ['icon' => 'car', 'text' => 'Otomotiv küçük parça kasalarında dar palet kullanımı'],
                    ['icon' => 'flask', 'text' => 'Kimya ve kozmetikte küçük koli paletleme']
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
        $this->command->info('✅ Variants eklendi: ES15-15ES');
    }
}
