<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_DingilAskiBurcu_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 131)
        $category = ShopCategory::where('slug->tr', 'dingil-aski-burcu')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Dingil Askı Burcu - TCM - TC-FD1.8
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-677'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Dingil Askı Burcu - TCM - TC-FD1.8']),
                'slug' => json_encode(['tr' => 'dingil-aski-burcu-tcm-tc-fd1-8']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Dingil Askı Burcu - TCM - TC-FD1,8</p>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-677')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3043.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }

        // Ürün: Dingil Askı Burcu - TCM - TC-FD30T3Z
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-678'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Dingil Askı Burcu - TCM - TC-FD30T3Z']),
                'slug' => json_encode(['tr' => 'dingil-aski-burcu-tcm-tc-fd30t3z']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Dingil Askı Burcu - TCM - TC-FD30T3Z</p>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-678')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3041.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }

        // Ürün: Dingil Askı Burcu - 305
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-679'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Dingil Askı Burcu - 305']),
                'slug' => json_encode(['tr' => 'dingil-aski-burcu-305']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Dingil Askı Burcu - 305</p>

<p></p>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-679')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3082.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }
    }
}
