<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_RulmanTasiyici_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 113)
        $category = ShopCategory::where('slug->tr', 'rulman-tasiyici')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Rulman Taşıyıcı - TCM - TC-FD30T3Z
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-482'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Rulman Taşıyıcı - TCM - TC-FD30T3Z']),
                'slug' => json_encode(['tr' => 'rulman-tasiyici-tcm-tc-fd30t3z']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Rulman Taşıyıcı - TCM - TC-FD30T3Z</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-482')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4884.webp');
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

        // Ürün: Rulman Taşıyıcı - KOMATSU - KO-FD30T16
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-483'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Rulman Taşıyıcı - KOMATSU - KO-FD30T16']),
                'slug' => json_encode(['tr' => 'rulman-tasiyici-komatsu-ko-fd30t16']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Rulman Taşıyıcı - KOMATSU - KO-FD30T16</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-483')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_2616.webp');
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

        // Ürün: Rulman Taşıyıcı - T201
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-484'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Rulman Taşıyıcı - T201']),
                'slug' => json_encode(['tr' => 'rulman-tasiyici-t201']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Rulman Taşıyıcı - T201</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-484')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4882.webp');
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

        // Ürün: Rulman Taşıyıcı - KOMATSU - KO-FD50AT-8
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-485'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Rulman Taşıyıcı - KOMATSU - KO-FD50AT-8']),
                'slug' => json_encode(['tr' => 'rulman-tasiyici-komatsu-ko-fd50at-8']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Rulman Taşıyıcı - KOMATSU - KO-FD50AT-8</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-485')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3021.webp');
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
