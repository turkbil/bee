<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_AsansorAyarTeflonu_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 101)
        $category = ShopCategory::where('slug->tr', 'asansor-ayar-teflonu')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Asansör Ayar Teflonu - TCM - TC-FD30T3Z
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-405'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Asansör Ayar Teflonu - TCM - TC-FD30T3Z']),
                'slug' => json_encode(['tr' => 'asansor-ayar-teflonu-tcm-tc-fd30t3z']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Asansör Ayar Teflonu - TCM - TC-FD30T3Z</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-405')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3107.webp');
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

        // Ürün: Asansör Ayar Teflonu - OM PİMESPO
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-406'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Asansör Ayar Teflonu - OM PİMESPO']),
                'slug' => json_encode(['tr' => 'asansor-ayar-teflonu-om-pimespo']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Asansör Ayar Teflonu - OM PİMESPO</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-406')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3118.webp');
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

        // Ürün: Asansör Ayar Teflonu - TCM - TC-FD35T3S
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-407'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Asansör Ayar Teflonu - TCM - TC-FD35T3S']),
                'slug' => json_encode(['tr' => 'asansor-ayar-teflonu-tcm-tc-fd35t3s']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Asansör Ayar Teflonu - TCM - TC-FD35T3S</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-407')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3120.webp');
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

        // Ürün: Asansör Ayar Teflonu - CLARK - CL-3TON
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-408'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Asansör Ayar Teflonu - CLARK - CL-3TON']),
                'slug' => json_encode(['tr' => 'asansor-ayar-teflonu-clark-cl-3ton']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Asansör Ayar Teflonu - CLARK - CL-3TON</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-408')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3112.webp');
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
