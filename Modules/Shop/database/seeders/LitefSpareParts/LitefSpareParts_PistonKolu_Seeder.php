<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_PistonKolu_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 82)
        $category = ShopCategory::where('slug->tr', 'piston-kolu')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Piston Kolu - Daewoo - DB33
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-209'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Piston Kolu - Daewoo - DB33']),
                'slug' => json_encode(['tr' => 'piston-kolu-daewoo-db33']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Piston Kolu - Daewoo - DB33</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-209')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/daewoo_db33.webp');
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

        // Ürün: Piston Kolu - İsuzu - 6BG1 - 6BD1 - 6BB1
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-210'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Piston Kolu - İsuzu - 6BG1 - 6BD1 - 6BB1']),
                'slug' => json_encode(['tr' => 'piston-kolu-isuzu-6bg1-6bd1-6bb1']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Piston Kolu - İsuzu - 6BG1 - 6BD1 - 6BB1</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-210')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/isuzu_6bg16bb16bd1-gigapixel.webp');
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

        // Ürün: Piston Kolu - Komatsu - 4D95S
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-211'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Piston Kolu - Komatsu - 4D95S']),
                'slug' => json_encode(['tr' => 'piston-kolu-komatsu-4d95s']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Piston Kolu - Komatsu - 4D95S</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-211')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/komatsu_4d95s6d95l.webp');
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

        // Ürün: Piston Kolu - Toyota - 1Z - 2Z - 13Z
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-212'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Piston Kolu - Toyota - 1Z - 2Z - 13Z']),
                'slug' => json_encode(['tr' => 'piston-kolu-toyota-1z-2z-13z']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Piston Kolu - Toyota - 1Z - 2Z - 13Z</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-212')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/toyota_1z2z13z-gigapixel.webp');
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

        // Ürün: Piston Kolu - Yanmar - 4D92E
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-213'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Piston Kolu - Yanmar - 4D92E']),
                'slug' => json_encode(['tr' => 'piston-kolu-yanmar-4d92e']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Piston Kolu - Yanmar - 4D92E</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-213')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/yanmar_4d92e-gigapixel.webp');
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
