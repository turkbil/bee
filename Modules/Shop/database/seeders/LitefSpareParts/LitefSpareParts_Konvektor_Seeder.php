<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_Konvektor_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 120)
        $category = ShopCategory::where('slug->tr', 'konvektor')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Konvektör - GİRİŞ 36/72V ÇIKIŞ 24V 3A
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-545'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Konvektör - GİRİŞ 36/72V ÇIKIŞ 24V 3A']),
                'slug' => json_encode(['tr' => 'konvektor-giris-36-72v-cikis-24v-3a']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Konvektör - GİRİŞ 36/72V ÇIKIŞ 24V 3A</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-545')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4900.webp');
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

        // Ürün: Konvektör - GİRİŞ 36/72V ÇIKIŞ 12V 27.5A
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-546'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Konvektör - GİRİŞ 36/72V ÇIKIŞ 12V 27.5A']),
                'slug' => json_encode(['tr' => 'konvektor-giris-36-72v-cikis-12v-27-5a']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Konvektör - GİRİŞ 36/72V ÇIKIŞ 12V 27,5A</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-546')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4894.webp');
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

        // Ürün: Konvektör - GİRİŞ 24V ÇIKIŞ 12V 5A
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-547'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Konvektör - GİRİŞ 24V ÇIKIŞ 12V 5A']),
                'slug' => json_encode(['tr' => 'konvektor-giris-24v-cikis-12v-5a']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Konvektör - GİRİŞ 24V ÇIKIŞ 12V 5A</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-547')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_3887.webp');
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

        // Ürün: Konvektör - GİRİŞ 36/72 ÇIKIŞ 24V 4.2A
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-548'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Konvektör - GİRİŞ 36/72 ÇIKIŞ 24V 4.2A']),
                'slug' => json_encode(['tr' => 'konvektor-giris-36-72-cikis-24v-4-2a']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Konvektör - GİRİŞ 36/72 ÇIKIŞ 24V 4,2A</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-548')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_4896.webp');
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
