<?php
<?php
namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_1_Master extends Seeder
{
    public function run()
    {
        $categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');
        $brandId = DB::table('shop_brands')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) = 'İXTİF'")
            ->value('brand_id');

        $exists = DB::table('shop_products')->where('sku', 'F4-201')->first();

        if (!$exists) {
            DB::table('shop_products')->insert([
                'sku' => 'F4-201',
                'model_number' => 'F4-201-CE',
                'title' => json_encode(['tr' => 'İXTİF F4 201 Li-Ion Elektrikli Transpalet 2.0 Ton'], JSON_UNESCAPED_UNICODE),
                'slug'  => json_encode(['tr' => Str::slug('İXTİF F4 201 Li-Ion Elektrikli Transpalet 2.0 Ton')], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => '48V sistem, iki adet 24V/20Ah Li-Ion modül, 2.0 ton kapasite ve kompakt 400 mm l2 ile dağıtım döngüsünde maksimum verim.'], JSON_UNESCAPED_UNICODE),
                'product_type' => 'physical',
                'condition' => 'new',

                'currency' => 'TRY',
                'price_on_request' => true,
                'base_price' => 0.00,
                'compare_at_price' => null,
                'cost_price' => null,

                'stock_tracking' => true,
                'current_stock' => 8,
                'low_stock_threshold' => 2,
                'allow_backorder' => false,

                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'is_master_product' => true,
                'parent_product_id' => null,

                'is_active' => true,
                'is_featured' => false,
                'is_bestseller' => false,
                'view_count' => 0,
                'sales_count' => 0,
                'published_at' => now(),

                'weight' => 140.00,
                'dimensions' => json_encode(['length' => 1550, 'width' => 695, 'height' => 1190, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
                'tags' => json_encode(['transpalet','li-ion','2-ton','ixtif','elektrikli','48v','kompakt'], JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
}