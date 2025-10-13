<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_Catal_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 86)
        $category = ShopCategory::where('slug->tr', 'catal')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: 41 Ayna - 1000x100x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-224'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1000x100x45 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1000x100x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1000x100x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-224')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel.webp');
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

        // Ürün: 41 Ayna - 1200x125x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-225'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1200x125x45 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1200x125x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1200x125x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-225')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_1.webp');
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

        // Ürün: 41 Ayna - 1200x100x40 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-226'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1200x100x40 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1200x100x40-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1200x100x40 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-226')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_2.webp');
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

        // Ürün: 41 Ayna - 1100x100x40 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-227'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1100x100x40 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1100x100x40-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1100x100x40 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-227')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_3.webp');
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

        // Ürün: 41 Ayna - 1300x120x40 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-228'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1300x120x40 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1300x120x40-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1300x120x40 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-228')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_4.webp');
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

        // Ürün: 41 Ayna - 1400x100x40 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-229'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1400x100x40 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1400x100x40-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1400x100x40 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-229')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_5.webp');
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

        // Ürün: 41 Ayna - 1600x120x40 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-230'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1600x120x40 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1600x120x40-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1600x120x40 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-230')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_6.webp');
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

        // Ürün: 41 Ayna - 1800x100x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-231'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '41 Ayna - 1800x100x50 Çatal']),
                'slug' => json_encode(['tr' => '41-ayna-1800x100x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>41 Ayna - 1800x100x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-231')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_7.webp');
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

        // Ürün: 51 Ayna - 1000x100x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-232'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1000x100x45 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1000x100x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1000x100x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-232')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_8.webp');
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

        // Ürün: 51 Ayna - 1100x120x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-233'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1100x120x45 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1100x120x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1100x120x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-233')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_9.webp');
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

        // Ürün: 51 Ayna - 1200x100x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-234'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1200x100x45 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1200x100x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1200x100x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-234')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_10.webp');
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

        // Ürün: 51 Ayna - 1200x120x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-236'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1200x120x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1200x120x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1200x120x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-236')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_12.webp');
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

        // Ürün: 51 Ayna - 1200x150x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-237'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1200x150x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1200x150x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1200x150x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-237')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_13.webp');
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

        // Ürün: 51 Ayna - 1400x120x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-238'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1400x120x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1400x120x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1400x120x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-238')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_14.webp');
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

        // Ürün: 51 Ayna - 1500x120x45 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-239'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1500x120x45 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1500x120x45-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1500x120x45 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-239')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_15.webp');
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

        // Ürün: 51 Ayna - 1600x150x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-240'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1600x150x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1600x150x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1600x150x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-240')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_16.webp');
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

        // Ürün: 51 Ayna - 1800x120x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-241'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1800x120x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1800x120x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1800x120x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-241')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_17.webp');
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

        // Ürün: 51 Ayna - 1800x150x50 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-242'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 1800x150x50 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-1800x150x50-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 1800x150x50 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-242')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_18.webp');
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

        // Ürün: 51 Ayna - 2600x130x60 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-243'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '51 Ayna - 2600x130x60 Çatal']),
                'slug' => json_encode(['tr' => '51-ayna-2600x130x60-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>51 Ayna - 2600x130x60 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-243')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_19.webp');
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

        // Ürün: 63 Ayna - 1200x50x65 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-244'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 1200x50x65 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-1200x50x65-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 1200x50x65 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-244')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_20.webp');
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

        // Ürün: 63 Ayna - 1400x450x70 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-245'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 1400x450x70 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-1400x450x70-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 1400x450x70 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-245')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_21.webp');
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

        // Ürün: 63 Ayna - 1600x150x60 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-246'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 1600x150x60 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-1600x150x60-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 1600x150x60 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-246')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_22.webp');
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

        // Ürün: 63 Ayna - 1700x150x60 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-247'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 1700x150x60 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-1700x150x60-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 1700x150x60 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-247')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_23.webp');
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

        // Ürün: 63 Ayna - 1800x150x65 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-248'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 1800x150x65 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-1800x150x65-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 1800x150x65 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-248')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_24.webp');
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

        // Ürün: 63 Ayna - 2000x150x65 Çatal
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-249'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '63 Ayna - 2000x150x65 Çatal']),
                'slug' => json_encode(['tr' => '63-ayna-2000x150x65-catal']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>63 Ayna - 2000x150x65 Çatal</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-249')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catali-3A150X50X1600-resim-1866-gigapixel_25.webp');
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
