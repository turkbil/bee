<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_TranspaletTekeri_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 61)
        $category = ShopCategory::where('slug->tr', 'transpalet-tekeri')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Alüminyum Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 200x50
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-152'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Alüminyum Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 200x50']),
                'slug' => json_encode(['tr' => 'aluminyum-uzeri-poliuretan-transpalet-tekerlegi-bilya-rulmanli-200x50']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Alüminyum Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 200x50</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-152')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/bilya-rulmanli-poliuretan-aluminyum-uzeri-poliuretan-kapli-tekerlekler-c.webp');
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

        // Ürün: Alüminyum Üzeri Kauçuk Transpalet Tekerleği - Bilya Rulmanlı - 160x45
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-153'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Alüminyum Üzeri Kauçuk Transpalet Tekerleği - Bilya Rulmanlı - 160x45']),
                'slug' => json_encode(['tr' => 'aluminyum-uzeri-kaucuk-transpalet-tekerlegi-bilya-rulmanli-160x45']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Alüminyum Üzeri Kauçuk Transpalet Tekerleği - Bilya Rulmanlı - 160x45</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-153')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/abr160x45-aluminyum-uzeri-kaucuk-transpalet-tekerlegi-cap16.webp');
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

        // Ürün: Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 170x40
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-154'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 170x40']),
                'slug' => json_encode(['tr' => 'poliamid-transpalet-tekerlegi-bilya-rulmanli-170x40']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 170x40</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-154')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/BilyalY_RulmanlY_170_mm_500_Kg.webp');
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

        // Ürün: Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 85x70
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-155'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 85x70']),
                'slug' => json_encode(['tr' => 'poliamid-transpalet-tekerlegi-bilya-rulmanli-85x70']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Poliamid Transpalet Tekerleği - Bilya Rulmanlı - 85x70</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-155')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/BilyalY_RulmanlY_Beyaz_85x70_mm_600_Kg.webp');
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

        // Ürün: Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 85x90
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-156'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 85x90']),
                'slug' => json_encode(['tr' => 'dokum-uzeri-poliuretan-transpalet-tekerlegi-bilya-rulmanli-85x90']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 85x90</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-156')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/vbp-85x90x20-poliuretan-transpalet-tekerlekleri-emes-emes-2180-16-O.webp');
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

        // Ürün: Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 150x50
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-157'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 150x50']),
                'slug' => json_encode(['tr' => 'dokum-uzeri-poliuretan-transpalet-tekerlegi-bilya-rulmanli-150x50']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Döküm Üzeri Poliüretan Transpalet Tekerleği - Bilya Rulmanlı - 150x50</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-157')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/bilya-rulmanli-poliuretan-dokum-uzeri-poliuretan-kapli-tekerlekler-c.webp');
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

        // Ürün: Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 200x50
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-158'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 200x50']),
                'slug' => json_encode(['tr' => 'poliamid-uzeri-poliuretan-transpalet-tekeri-bilya-rulmanli-200x50']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 200x50</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-158')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/1.webp');
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

        // Ürün: Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 82x72
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-159'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 82x72']),
                'slug' => json_encode(['tr' => 'poliamid-uzeri-poliuretan-transpalet-tekeri-bilya-rulmanli-82x72']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>Poliamid Üzeri Poliüretan Transpalet Tekeri - Bilya Rulmanlı - 82x72</p>']),
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
        $productModel = ShopProduct::where('sku', 'LITEF-159')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/poliretan_bilyalY_82x72x20_rulmanlY.webp');
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
