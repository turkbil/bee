<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_Ampul_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 118)
        $category = ShopCategory::where('slug->tr', 'ampul')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Ampul 12V DUYSUZ
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-525'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 12V DUYSUZ']),
                'slug' => json_encode(['tr' => 'ampul-12v-duysuz']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 12V DUYSUZ</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-525')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3806.webp');
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

        // Ürün: Ampul 12V 45-40W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-526'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 12V 45-40W']),
                'slug' => json_encode(['tr' => 'ampul-12v-45-40w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 12V 45-40W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-526')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3801.webp');
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

        // Ürün: Ampul 12V H4 60-55W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-527'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 12V H4 60-55W']),
                'slug' => json_encode(['tr' => 'ampul-12v-h4-60-55w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<div class="urunTanim">Ampul 12V H4 60-55W</div>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-527')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3798.webp');
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

        // Ürün: Ampul 12V 15W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-528'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 12V 15W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-12v-15w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 12V 15W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-528')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3787.webp');
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

        // Ürün: Ampul 12V H7 55W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-529'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 12V H7 55W']),
                'slug' => json_encode(['tr' => 'ampul-12v-h7-55w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 12V H7 55W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-529')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3809.webp');
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

        // Ürün: Ampul H3 12V 55W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-530'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul H3 12V 55W']),
                'slug' => json_encode(['tr' => 'ampul-h3-12v-55w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul H3 12V 55W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-530')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3761.webp');
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

        // Ürün: Ampul 24V 5W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-531'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 24V 5W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-24v-5w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 24V 5W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-531')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3761_1.webp');
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

        // Ürün: Ampul H3 24V 70W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-532'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul H3 24V 70W']),
                'slug' => json_encode(['tr' => 'ampul-h3-24v-70w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul H3 24V 70W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-532')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3765.webp');
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

        // Ürün: Ampul 24V 21W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-533'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul 24V 21W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-24v-21w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul 24V 21W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-533')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3776.webp');
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

        // Ürün: Ampul - 24V 21/5W 93 ÇİFT DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-534'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - 24V 21/5W 93 ÇİFT DUY']),
                'slug' => json_encode(['tr' => 'ampul-24v-21-5w-93-cift-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - 24V 21/5W 93 ÇİFT DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-534')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3781.webp');
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

        // Ürün: Ampul - 48V 25/10W ÇİFT DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-535'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - 48V 25/10W ÇİFT DUY']),
                'slug' => json_encode(['tr' => 'ampul-48v-25-10w-cift-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - 48V 25/10W ÇİFT DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-535')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3783.webp');
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

        // Ürün: Ampul - 48V 5W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-536'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - 48V 5W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-48v-5w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - 48V 5W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-536')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3768.webp');
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

        // Ürün: Ampul - 48V 8W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-537'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - 48V 8W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-48v-8w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - 48V 8W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-537')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3771.webp');
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

        // Ürün: Ampul - H3 48V 60W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-538'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - H3 48V 60W']),
                'slug' => json_encode(['tr' => 'ampul-h3-48v-60w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - H3 48V 60W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-538')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3763.webp');
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

        // Ürün: Ampul - 48V 40W
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-539'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - 48V 40W']),
                'slug' => json_encode(['tr' => 'ampul-48v-40w']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - 48V 40W</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-539')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3789.webp');
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

        // Ürün: Ampul - TCM - 12V 27W TEK DUY
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-540'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Ampul - TCM - 12V 27W TEK DUY']),
                'slug' => json_encode(['tr' => 'ampul-tcm-12v-27w-tek-duy']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Ampul - TCM - 12V 27W TEK DUY</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-540')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3804.webp');
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
