<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_BeyazDolguLastik_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 53)
        $category = ShopCategory::where('slug->tr', 'beyaz-dolgu-lastik')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: 15x4-1/2-8 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-85'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '15x4-1/2-8 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '15x4-1-2-8-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>15x4-1/2-8 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-85')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu.webp');
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

        // Ürün: 18x7-8 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-86'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '18x7-8 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '18x7-8-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>18x7-8 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-86')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_1.webp');
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

        // Ürün: 200x50-10 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-88'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '200x50-10 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '200x50-10-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>200x50-10 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-88')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_3.webp');
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

        // Ürün: 6x00-9 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-89'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6x00-9 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6x00-9-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6x00-9 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-89')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_4.webp');
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

        // Ürün: 6x50-10 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-90'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6x50-10 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6x50-10-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6x50-10 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-90')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_5.webp');
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

        // Ürün: 21x8-9 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-91'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '21x8-9 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '21x8-9-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>21x8-9 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-91')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_6.webp');
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

        // Ürün: 23x10-12 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-92'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '23x10-12 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '23x10-12-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>23x10-12 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-92')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_7.webp');
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

        // Ürün: 27x10-12 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-93'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '27x10-12 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '27x10-12-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>27x10-12 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-93')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_8.webp');
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

        // Ürün: 7.00-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-94'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-94')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_9.webp');
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

        // Ürün: 7.00-12 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-95'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-12 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-12-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-12 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-95')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_10.webp');
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

        // Ürün: 7.50-16 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-96'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.50-16 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-50-16-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.50-16 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-96')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_11.webp');
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

        // Ürün: 28x9-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-97'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '28x9-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '28x9-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>28x9-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-97')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_12.webp');
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

        // Ürün: 250-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-98'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '250-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '250-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>250-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-98')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_13.webp');
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

        // Ürün: 8x25-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-99'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '8x25-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '8x25-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>8x25-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-99')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_14.webp');
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

        // Ürün: 300-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-100'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '300-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '300-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>300-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-100')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_15.webp');
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

        // Ürün: 355-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-101'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-101')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_16.webp');
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

        // Ürün: 355x65-15 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-102'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355x65-15 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355x65-15-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355x65-15 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-102')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_17.webp');
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

        // Ürün: 9x00-20 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-103'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '9x00-20 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '9x00-20-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>9x00-20 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-103')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_18.webp');
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

        // Ürün: 12x00-20 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-104'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '12x00-20 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '12x00-20-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>12x00-20 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-104')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_19.webp');
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

        // Ürün: 16x6-8 Beyaz Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-105'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '16x6-8 Beyaz Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '16x6-8-beyaz-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>16x6-8 Beyaz Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-105')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/beyal_dolgu_20.webp');
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
