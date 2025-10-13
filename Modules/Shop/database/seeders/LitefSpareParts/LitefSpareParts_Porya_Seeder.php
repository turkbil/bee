<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_Porya_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 133)
        $category = ShopCategory::where('slug->tr', 'porya')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Porya - 1490
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-685'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Porya - 1490']),
                'slug' => json_encode(['tr' => 'porya-1490']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Porya - 1490</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-685')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3587.webp');
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

        // Ürün: Porya - 1499
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-686'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Porya - 1499']),
                'slug' => json_encode(['tr' => 'porya-1499']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Porya - 1499</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-686')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3627.webp');
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

        // Ürün: Porya - 1496
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-687'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Porya - 1496']),
                'slug' => json_encode(['tr' => 'porya-1496']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Porya - 1496</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-687')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3585.webp');
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

        // Ürün: Porya - 1502
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-688'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Porya - 1502']),
                'slug' => json_encode(['tr' => 'porya-1502']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Porya - 1502</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-688')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3571.webp');
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

        // Ürün: Porya Arka - JUNGHEINRICH
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-689'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Porya Arka - JUNGHEINRICH']),
                'slug' => json_encode(['tr' => 'porya-arka-jungheinrich']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Porya Arka - JUNGHEINRICH</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-689')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3635.webp');
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
