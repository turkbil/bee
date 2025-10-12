<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * F4-202 MASTER SEEDER
 *
 * Sorumluluğu: Temel ürün bilgilerini ekler
 * - SKU, title, slug, short_description
 * - Price, stock, weight, dimensions
 * - Category, brand, status
 */
class F4_202_Transpalet_Master extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. TEMEL BİLGİLER
        // ========================================

        $sku = 'F4-202';
        $title = 'F4 202 Li-Ion Akülü Transpalet 2.5 Ton';
        $slug = 'f4-202-2-5-ton-48v-li-ion-transpalet';
        $shortDescription = '48V Li-Ion güç platformu ile 2.5 ton taşıma kapasitesi sunan F4 202, yüksek performans motoru ve dayanıklı yapısıyla ağır yük operasyonlarında güvenilir çözüm sunar.';

        // ========================================
        // 2. KATEGORİ ve MARKA
        // ========================================

        $categoryId = DB::table('shop_categories')
            ->where('slug->tr', 'transpalet')
            ->value('category_id');

        if (!$categoryId) {
            echo "❌ Kategori bulunamadı! Önce ShopCategorySeeder'ı çalıştırın.\n";
            return;
        }

        $brandId = DB::table('shop_brands')
            ->where('name', 'İXTİF')
            ->value('brand_id');

        if (!$brandId) {
            echo "❌ Marka bulunamadı! Önce ShopBrandSeeder'ı çalıştırın.\n";
            return;
        }

        // ========================================
        // 3. MASTER PRODUCT INSERT
        // ========================================

        $productId = DB::table('shop_products')->insertGetId([
            // Identifiers
            'sku' => $sku,

            // Basic Info (JSON multi-language)
            'title' => json_encode(['tr' => $title], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => $slug], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortDescription], JSON_UNESCAPED_UNICODE),

            // Relations
            'category_id' => $categoryId,
            'brand_id' => $brandId,

            // Variant System
            'parent_product_id' => null,
            'is_master_product' => true,
            'variant_type' => null,

            // Type & Condition
            'product_type' => 'physical',
            'condition' => 'new',

            // Pricing
            'price_on_request' => false,
            'base_price' => 145000.00,
            'compare_at_price' => null,
            'cost_price' => 105000.00,
            'currency' => 'TRY',

            // Deposit & Installment
            'deposit_required' => false,
            'deposit_amount' => null,
            'deposit_percentage' => null,
            'installment_available' => true,
            'max_installments' => 12,

            // Stock Management
            'stock_tracking' => true,
            'current_stock' => 12,
            'low_stock_threshold' => 5,
            'allow_backorder' => false,
            'lead_time_days' => 14,

            // Physical Properties
            'weight' => 180.00,
            'dimensions' => json_encode([
                'length' => 1650,
                'width' => 690,
                'height' => 1950,
                'unit' => 'mm',
            ], JSON_UNESCAPED_UNICODE),

            // Warranty
            'warranty_info' => json_encode([
                'period' => 24,
                'unit' => 'month',
                'details' => '2 yıl üretici garantisi. Batarya 1 yıl garanti kapsamındadır.',
            ], JSON_UNESCAPED_UNICODE),

            // Display & Status
            'is_active' => true,
            'is_featured' => true,
            'is_bestseller' => false,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),

            // Timestamps
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Master Product eklendi: {$sku} (ID: {$productId})\n";
        echo "📦 Ürün: {$title}\n";
        echo "💰 Fiyat: ₺145,000.00\n";
        echo "📊 Stok: 12 adet\n";
        echo "\n";
    }
}
