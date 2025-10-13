<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_SiyahDolguLastik_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 52)
        $category = ShopCategory::where('slug->tr', 'siyah-dolgu-lastik')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: 15x4-1/2-8 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-63'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '15x4-1/2-8 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '15x4-1-2-8-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>15x4-1/2-8 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-63')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah.webp');
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

        // Ürün: 16x6-8 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-64'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '16x6-8 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '16x6-8-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>16x6-8 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-64')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_1.webp');
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

        // Ürün: 18x7-8 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-65'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '18x7-8 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '18x7-8-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>18x7-8 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-65')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_2.webp');
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

        // Ürün: 5x00-8 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-67'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '5x00-8 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '5x00-8-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>5x00-8 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-67')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_4.webp');
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

        // Ürün: 200x50-10 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-68'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '200x50-10 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '200x50-10-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>200x50-10 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-68')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_5.webp');
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

        // Ürün: 6x00-9 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-69'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6x00-9 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6x00-9-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6x00-9 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-69')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_6.webp');
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

        // Ürün: 6x50-10 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-70'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6x50-10 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6x50-10-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6x50-10 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-70')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_7.webp');
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

        // Ürün: 21x8-9 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-71'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '21x8-9 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '21x8-9-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>21x8-9 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-71')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_8.webp');
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

        // Ürün: 23x10-12 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-72'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '23x10-12 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '23x10-12-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>23x10-12 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-72')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_9.webp');
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

        // Ürün: 27x10-12 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-73'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '27x10-12 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '27x10-12-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>27x10-12 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-73')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_10.webp');
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

        // Ürün: 7.00-12 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-74'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-12 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-12-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-12 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-74')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_11.webp');
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

        // Ürün: 7.00-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-75'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-75')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_12.webp');
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

        // Ürün: 7.50-16 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-76'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.50-16 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-50-16-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.50-16 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-76')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_13.webp');
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

        // Ürün: 250-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-78'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '250-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '250-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>250-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-78')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_15.webp');
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

        // Ürün: 8x25-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-79'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '8x25-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '8x25-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>8x25-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-79')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_16.webp');
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

        // Ürün: 300-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-80'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '300-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '300-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>300-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-80')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_17.webp');
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

        // Ürün: 355-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-81'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-81')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_18.webp');
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

        // Ürün: 355x65-15 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-82'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355x65-15 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355x65-15-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355x65-15 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-82')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_19.webp');
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

        // Ürün: 9x00-20 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-83'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '9x00-20 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '9x00-20-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>9x00-20 Siyah Dolgu Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-83')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_20.webp');
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

        // Ürün: 12x00-20 Siyah Dolgu Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-84'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '12x00-20 Siyah Dolgu Forklift Lastiği']),
                'slug' => json_encode(['tr' => '12x00-20-siyah-dolgu-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-84')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Dolgu_Siyah_21.webp');
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
