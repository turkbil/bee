<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_KizakTakimi_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 99)
        $category = ShopCategory::where('slug->tr', 'kizak-takimi')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Kızak Takımı - TCM - TC-FD30T-6
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-374'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - TCM - TC-FD30T-6']),
                'slug' => json_encode(['tr' => 'kizak-takimi-tcm-tc-fd30t-6']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - TCM - TC-FD30T-6</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-374')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3060.webp');
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

        // Ürün: Kızak Takımı - ÇİN - Çİ-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-375'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - ÇİN - Çİ-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-cin-ci-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<div class="urunTanim">Kızak Takımı - ÇİN - Çİ-3TON</div>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-375')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3049.webp');
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

        // Ürün: Kızak Takımı - KOMATSU - KO-FD30NT17
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-376'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - KOMATSU - KO-FD30NT17']),
                'slug' => json_encode(['tr' => 'kizak-takimi-komatsu-ko-fd30nt17']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - KOMATSU - KO-FD30NT17</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-376')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3155.webp');
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

        // Ürün: Kızak Takımı - KOMATSU - KO-FD25NT17
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-377'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - KOMATSU - KO-FD25NT17']),
                'slug' => json_encode(['tr' => 'kizak-takimi-komatsu-ko-fd25nt17']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - KOMATSU - KO-FD25NT17</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-377')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3122.webp');
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

        // Ürün: Kızak Takımı - KOMATSU - KO-FD30NT16
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-378'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - KOMATSU - KO-FD30NT16']),
                'slug' => json_encode(['tr' => 'kizak-takimi-komatsu-ko-fd30nt16']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - KOMATSU - KO-FD30NT16</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-378')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3138.webp');
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

        // Ürün: Kızak Takımı - BAOLİ - BA-KB30D
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-379'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - BAOLİ - BA-KB30D']),
                'slug' => json_encode(['tr' => 'kizak-takimi-baoli-ba-kb30d']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - BAOLİ - BA-KB30D</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-379')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3155_1.webp');
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

        // Ürün: Kızak Takımı - BAOLİ - BA-2.5TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-380'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - BAOLİ - BA-2.5TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-baoli-ba-2-5ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - BAOLİ - BA-2,5TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-380')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3090.webp');
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

        // Ürün: Kızak Takımı - YALE - YA-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-381'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - YALE - YA-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-yale-ya-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - YALE - YA-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-381')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3110.webp');
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

        // Ürün: Kızak Takımı - ÇUKUROVA - CU-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-382'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - ÇUKUROVA - CU-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-cukurova-cu-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - ÇUKUROVA - CU-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-382')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3046.webp');
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

        // Ürün: Kızak Takımı - LİNDE - Lİ-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-383'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - LİNDE - Lİ-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-linde-li-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - LİNDE - Lİ-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-383')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3140.webp');
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

        // Ürün: Kızak Takımı - FEDERAL POWER - FE-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-384'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - FEDERAL POWER - FE-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-federal-power-fe-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - FEDERAL POWER - FE-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-384')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3161.webp');
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

        // Ürün: Kızak Takımı - STILL - ST-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-385'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - STILL - ST-3TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-still-st-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - STILL - ST-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-385')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3158.webp');
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

        // Ürün: Kızak Takımı - KOMATSU - KO-4.5TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-386'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Kızak Takımı - KOMATSU - KO-4.5TON']),
                'slug' => json_encode(['tr' => 'kizak-takimi-komatsu-ko-4-5ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Kızak Takımı - KOMATSU - KO-4,5TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-386')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3079.webp');
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
