<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_DengeTekeri_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 57)
        $category = ShopCategory::where('slug->tr', 'denge-tekeri')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Denge Tekeri - 1529
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-133'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - 1529']),
                'slug' => json_encode(['tr' => 'denge-tekeri-1529']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - 1529</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-133')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4318.webp');
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

        // Ürün: Denge Tekeri - 1525
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-134'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - 1525']),
                'slug' => json_encode(['tr' => 'denge-tekeri-1525']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - 1525</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-134')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4321.webp');
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

        // Ürün: Denge Tekeri - 1522
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-135'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - 1522']),
                'slug' => json_encode(['tr' => 'denge-tekeri-1522']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - 1522</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-135')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4324.webp');
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

        // Ürün: Denge Tekeri - 1526
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-136'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - 1526']),
                'slug' => json_encode(['tr' => 'denge-tekeri-1526']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - 1526</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-136')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4327.webp');
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

        // Ürün: Denge Tekeri - 1953
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-137'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - 1953']),
                'slug' => json_encode(['tr' => 'denge-tekeri-1953']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - 1953</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-137')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4331.webp');
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

        // Ürün: Denge Tekeri - STILL - ST-EXUSF20
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-138'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - STILL - ST-EXUSF20']),
                'slug' => json_encode(['tr' => 'denge-tekeri-still-st-exusf20']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - STILL - ST-EXUSF20</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-138')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4336.webp');
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

        // Ürün: Denge Tekeri - STILL - ST-EXU16
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-139'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Denge Tekeri - STILL - ST-EXU16']),
                'slug' => json_encode(['tr' => 'denge-tekeri-still-st-exu16']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Denge Tekeri - STILL - ST-EXU16</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-139')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4361.webp');
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
