<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\ShopCategoryMapper;

/**
 * Shop Product Master Seeder
 *
 * Tüm ürünleri JSON extracts klasöründen okuyarak otomatik olarak ekler.
 * Her ürün için:
 * - Ana ürün kaydı (shop_products)
 * - Varyantlar (shop_product_variants)
 * - Attribute bağlantıları (shop_product_attributes)
 */
class ShopProductMasterSeeder extends Seeder
{
    private string $jsonExtractsPath = '/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/json-extracts';
    private array $stats = [
        'total_json_files' => 0,
        'products_created' => 0,
        'variants_created' => 0,
        'attributes_linked' => 0,
        'errors' => [],
    ];

    public function run(): void
    {
        $this->command->info('🚀 Shop Product Master Seeder başlatılıyor...');
        $this->command->info('📂 JSON Extract klasörü: ' . $this->jsonExtractsPath);

        // JSON dosyalarını tara
        $jsonFiles = File::glob($this->jsonExtractsPath . '/*.json');
        $this->stats['total_json_files'] = count($jsonFiles);

        $this->command->info("📄 {$this->stats['total_json_files']} adet JSON dosyası bulundu");

        if (empty($jsonFiles)) {
            $this->command->warn('⚠️  JSON dosyası bulunamadı! Önce AI ile PDF→JSON dönüşümü yapın.');
            return;
        }

        // Progress bar
        $bar = $this->command->getOutput()->createProgressBar(count($jsonFiles));
        $bar->start();

        foreach ($jsonFiles as $jsonFile) {
            try {
                $this->processProductJson($jsonFile);
                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['errors'][] = [
                    'file' => basename($jsonFile),
                    'error' => $e->getMessage()
                ];
                $this->command->error("\n❌ Hata: " . basename($jsonFile) . " → " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->command->newLine(2);

        // İstatistikler
        $this->printStats();
    }

    /**
     * Tek bir JSON dosyasını işler
     */
    private function processProductJson(string $jsonFilePath): void
    {
        $fileName = basename($jsonFilePath);
        $jsonData = json_decode(File::get($jsonFilePath), true);

        if (!$jsonData) {
            throw new \Exception("JSON parse hatası");
        }

        // Kategori ID'sini bul
        $categoryId = $this->resolveCategoryId($jsonData);
        if (!$categoryId) {
            throw new \Exception("Kategori bulunamadı");
        }

        // Ana ürünü ekle
        $productId = $this->createProduct($jsonData, $categoryId);

        // Varyantları ekle
        if (isset($jsonData['variants']) && is_array($jsonData['variants'])) {
            $this->createVariants($productId, $jsonData['variants']);
        }

        // Attribute'ları bağla
        $this->linkAttributes($productId, $jsonData);

        $this->stats['products_created']++;
    }

    /**
     * Kategori ID'sini JSON'dan çözümler
     */
    private function resolveCategoryId(array $jsonData): ?int
    {
        // 1. Önce JSON'da category_id var mı kontrol et
        if (isset($jsonData['category_brand']['category_id'])) {
            return (int) $jsonData['category_brand']['category_id'];
        }

        // 2. PDF source'dan klasör adını bul
        if (isset($jsonData['metadata']['pdf_source'])) {
            $pdfPath = $jsonData['metadata']['pdf_source'];
            // Örn: "/Users/.../2-Transpalet/F4 201/..."
            preg_match('/(\d+-[^\/]+)/', $pdfPath, $matches);
            if (isset($matches[1])) {
                $folderName = $matches[1]; // "2-Transpalet"
                return ShopCategoryMapper::getCategoryIdFromFolder($folderName);
            }
        }

        // 3. Kategori adından bul
        if (isset($jsonData['category_brand']['category_name'])) {
            return ShopCategoryMapper::getCategoryIdFromTitle($jsonData['category_brand']['category_name']);
        }

        return null;
    }

    /**
     * Ana ürünü oluştur
     */
    private function createProduct(array $jsonData, int $categoryId): int
    {
        $productInfo = $jsonData['product_info'] ?? [];
        $basicData = $jsonData['basic_data'] ?? [];
        $categoryBrand = $jsonData['category_brand'] ?? [];
        $pricing = $jsonData['pricing'] ?? [];
        $inventory = $jsonData['inventory'] ?? $jsonData['stock_info'] ?? [];
        $physicalProps = $jsonData['physical_properties'] ?? [];

        // SKU kontrolü - mevcut ürün varsa güncelle
        $existingProduct = DB::table('shop_products')->where('sku', $productInfo['sku'])->first();

        $productData = [
            'category_id' => $categoryId,
            'brand_id' => $categoryBrand['brand_id'] ?? 1, // Default: İXTİF
            'sku' => $productInfo['sku'],
            'model_number' => $productInfo['model_number'] ?? null,
            'barcode' => $productInfo['barcode'] ?? null,
            'title' => json_encode($basicData['title'] ?? [], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode($basicData['slug'] ?? [], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode($basicData['short_description'] ?? [], JSON_UNESCAPED_UNICODE),
            'body' => json_encode($basicData['body'] ?? [], JSON_UNESCAPED_UNICODE),
            'product_type' => $productInfo['product_type'] ?? 'physical',
            'condition' => $productInfo['condition'] ?? 'new',
            'price_on_request' => $pricing['price_on_request'] ?? true,
            'base_price' => $pricing['base_price'] ?? null,
            'compare_at_price' => $pricing['compare_at_price'] ?? null,
            'cost_price' => $pricing['cost_price'] ?? null,
            'currency' => $pricing['currency'] ?? 'TRY',
            'deposit_required' => $pricing['deposit_required'] ?? true,
            'deposit_amount' => $pricing['deposit_amount'] ?? null,
            'deposit_percentage' => $pricing['deposit_percentage'] ?? 30,
            'installment_available' => $pricing['installment_available'] ?? true,
            'max_installments' => $pricing['max_installments'] ?? 12,
            'stock_tracking' => $inventory['stock_tracking'] ?? true,
            'current_stock' => $inventory['stock_quantity'] ?? $inventory['current_stock'] ?? 0,
            'low_stock_threshold' => $inventory['low_stock_threshold'] ?? 1,
            'allow_backorder' => $inventory['allow_backorder'] ?? false,
            'lead_time_days' => $inventory['lead_time_days'] ?? 45,
            'weight' => $physicalProps['weight'] ?? $physicalProps['service_weight'] ?? null,
            'dimensions' => json_encode($physicalProps['dimensions'] ?? [], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode($jsonData['technical_specs'] ?? [], JSON_UNESCAPED_UNICODE),
            'features' => json_encode($jsonData['features'] ?? [], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode($jsonData['highlighted_features'] ?? [], JSON_UNESCAPED_UNICODE),
            'media_gallery' => json_encode($jsonData['media_gallery'] ?? [], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode($jsonData['primary_specs'] ?? [], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode($jsonData['use_cases'] ?? [], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode($jsonData['competitive_advantages'] ?? [], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode($jsonData['target_industries'] ?? [], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode($jsonData['faq_data'] ?? [], JSON_UNESCAPED_UNICODE),
            'video_url' => $jsonData['video_url'] ?? null,
            'manual_pdf_url' => $jsonData['manual_pdf_url'] ?? null,
            'is_active' => true,
            'is_featured' => $jsonData['is_featured'] ?? false,
            'is_bestseller' => $jsonData['is_bestseller'] ?? false,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),
            'warranty_info' => json_encode($jsonData['warranty_info'] ?? [], JSON_UNESCAPED_UNICODE),
            'shipping_info' => json_encode($jsonData['shipping_info'] ?? [], JSON_UNESCAPED_UNICODE),
            'tags' => json_encode($jsonData['tags'] ?? [], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];

        if ($existingProduct) {
            // Güncelle
            DB::table('shop_products')
                ->where('product_id', $existingProduct->product_id)
                ->update($productData);
            return (int) $existingProduct->product_id;
        } else {
            // Yeni kayıt
            $productData['created_at'] = now();
            $productId = DB::table('shop_products')->insertGetId($productData);
            return (int) $productId;
        }
    }

    /**
     * Varyantları oluştur
     */
    private function createVariants(int $productId, array $variants): void
    {
        foreach ($variants as $variantData) {
            $sku = $variantData['sku'] ?? null;
            if (!$sku) {
                continue;
            }

            // Mevcut varyant kontrolü
            $existingVariant = DB::table('shop_product_variants')
                ->where('product_id', $productId)
                ->where('sku', $sku)
                ->first();

            $variant = [
                'product_id' => $productId,
                'sku' => $sku,
                'barcode' => $variantData['barcode'] ?? null,
                'title' => json_encode($variantData['title'] ?? [], JSON_UNESCAPED_UNICODE),
                'option_values' => json_encode($variantData['option_values'] ?? [], JSON_UNESCAPED_UNICODE),
                'price_modifier' => $variantData['price_modifier'] ?? 0,
                'cost_price' => $variantData['cost_price'] ?? null,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'reserved_quantity' => $variantData['reserved_quantity'] ?? 0,
                'weight' => $variantData['weight'] ?? null,
                'dimensions' => json_encode($variantData['dimensions'] ?? [], JSON_UNESCAPED_UNICODE),
                'image_url' => $variantData['image_url'] ?? null,
                'images' => json_encode($variantData['images'] ?? [], JSON_UNESCAPED_UNICODE),
                'is_default' => $variantData['is_default'] ?? false,
                'is_active' => $variantData['is_active'] ?? true,
                'sort_order' => $variantData['sort_order'] ?? 0,
                'updated_at' => now(),
            ];

            if ($existingVariant) {
                DB::table('shop_product_variants')
                    ->where('variant_id', $existingVariant->variant_id)
                    ->update($variant);
            } else {
                $variant['created_at'] = now();
                DB::table('shop_product_variants')->insert($variant);
                $this->stats['variants_created']++;
            }
        }
    }

    /**
     * Attribute'ları bağla (technical_specs'den otomatik çıkar)
     */
    private function linkAttributes(int $productId, array $jsonData): void
    {
        $technicalSpecs = $jsonData['technical_specs'] ?? [];

        $attributeMappings = [
            // [attribute_id, json_path, multiplier_for_unit]
            [1, 'capacity.load_capacity.value', 1], // Yük Kapasitesi (kg)
            [2, 'electrical.voltage.value', 1], // Voltaj (V)
            [3, 'electrical.type', null], // Batarya Tipi (string)
            [4, 'dimensions_detail.lift_height.value', 1], // Asansör Yüksekliği (mm)
            [5, 'capacity.service_weight.value', 1], // Servis Ağırlığı (kg)
            [6, 'dimensions_detail.fork_length.value', 1], // Çatal Uzunluğu (mm)
        ];

        foreach ($attributeMappings as [$attributeId, $path, $multiplier]) {
            $value = data_get($technicalSpecs, $path);
            if ($value === null) {
                continue;
            }

            // Mevcut bağlantı kontrolü
            $existing = DB::table('shop_product_attributes')
                ->where('product_id', $productId)
                ->where('attribute_id', $attributeId)
                ->first();

            $attributeData = [
                'product_id' => $productId,
                'attribute_id' => $attributeId,
                'value' => json_encode(['tr' => (string)$value, 'en' => (string)$value, 'vs.' => '...'], JSON_UNESCAPED_UNICODE),
                'value_text' => is_string($value) ? $value : null,
                'value_numeric' => is_numeric($value) ? ($multiplier ? $value * $multiplier : $value) : null,
                'sort_order' => 0,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('shop_product_attributes')
                    ->where('product_attribute_id', $existing->product_attribute_id)
                    ->update($attributeData);
            } else {
                $attributeData['created_at'] = now();
                DB::table('shop_product_attributes')->insert($attributeData);
                $this->stats['attributes_linked']++;
            }
        }
    }

    /**
     * İstatistikleri yazdır
     */
    private function printStats(): void
    {
        $this->command->info("📊 İSTATİSTİKLER:");
        $this->command->info("   📄 Toplam JSON dosyası: {$this->stats['total_json_files']}");
        $this->command->info("   ✅ Oluşturulan ürün: {$this->stats['products_created']}");
        $this->command->info("   🔧 Oluşturulan varyant: {$this->stats['variants_created']}");
        $this->command->info("   🏷️  Bağlanan attribute: {$this->stats['attributes_linked']}");

        if (!empty($this->stats['errors'])) {
            $this->command->error("\n❌ HATALAR:");
            foreach ($this->stats['errors'] as $error) {
                $this->command->error("   {$error['file']}: {$error['error']}");
            }
        }
    }
}
