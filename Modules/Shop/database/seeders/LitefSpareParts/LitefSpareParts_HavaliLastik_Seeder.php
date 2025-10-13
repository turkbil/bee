<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_HavaliLastik_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 54)
        $category = ShopCategory::where('slug->tr', 'havali-lastik')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: 200x50-10 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-106'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '200x50-10 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '200x50-10-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>200x50-10 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-106')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik.webp');
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

        // Ürün: 6.50-10 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-107'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6.50-10 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6-50-10-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6.50-10 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-107')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_1.webp');
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

        // Ürün: 21x8-9 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-108'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '21x8-9 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '21x8-9-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>21x8-9 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-108')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_2.webp');
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

        // Ürün: 23x10-12 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-109'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '23x10-12 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '23x10-12-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>23x10-12 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-109')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_3.webp');
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

        // Ürün: 27x10-12 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-110'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '27x10-12 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '27x10-12-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>27x10-12 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-110')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_4.webp');
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

        // Ürün: 7.00-12 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-111'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-12 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-12-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-12 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-111')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_5.webp');
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

        // Ürün: 7.00-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-112'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.00-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-00-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.00-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-112')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_6.webp');
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

        // Ürün: 7.50-16 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-113'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '7.50-16 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '7-50-16-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>7.50-16 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-113')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_7.webp');
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

        // Ürün: 28x9-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-114'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '28x9-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '28x9-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>28x9-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-114')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_8.webp');
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

        // Ürün: 8x25-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-117'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '8x25-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '8x25-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>8x25-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-117')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_11.webp');
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

        // Ürün: 300-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-118'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '300-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '300-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>300-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-118')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_12.webp');
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

        // Ürün: 355-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-119'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-119')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_13.webp');
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

        // Ürün: 355x65-15 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-120'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '355x65-15 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '355x65-15-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>355x65-15 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-120')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_14.webp');
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

        // Ürün: 9x00-20 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-121'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '9x00-20 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '9x00-20-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>9x00-20 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-121')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_15.webp');
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

        // Ürün: 12x00-20 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-122'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '12x00-20 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '12x00-20-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>12x00-20 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-122')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_16.webp');
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

        // Ürün: 6.00-9 Havalı Forklift Lastiği
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-123'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => '6.00-9 Havalı Forklift Lastiği']),
                'slug' => json_encode(['tr' => '6-00-9-havali-forklift-lastigi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>6.00-9 Havalı Forklift Lastiği</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-123')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/HavalY_Lastik_17.webp');
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
