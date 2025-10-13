<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mevcut jsonInputs verilerini custom_json_fields'e taşır
     */
    public function up(): void
    {
        // Tüm ürünleri al
        $products = DB::table('shop_products')->get();

        foreach ($products as $product) {
            $customJsonFields = [];

            // Mevcut custom_json_fields varsa al (boş array garantisi)
            if ($product->custom_json_fields) {
                $customJsonFields = is_string($product->custom_json_fields)
                    ? json_decode($product->custom_json_fields, true) ?? []
                    : $product->custom_json_fields;
            }

            // Mapping: JSON field → Custom Category
            $mappings = [
                'technical_specs' => [
                    'name' => 'Technical Specifications',
                    'fields' => ['key', 'value'],
                    'converter' => function($data) {
                        // Object {key: value} → Array [{key: 'X', value: 'Y'}]
                        if (!is_array($data)) return [];
                        $items = [];
                        foreach ($data as $key => $value) {
                            $items[] = ['key' => $key, 'value' => $value];
                        }
                        return $items;
                    }
                ],
                'features' => [
                    'name' => 'Features',
                    'fields' => ['icon', 'text'],
                    'converter' => null // Direct array
                ],
                'highlighted_features' => [
                    'name' => 'Highlighted Features',
                    'fields' => ['icon', 'title', 'description'],
                    'converter' => null
                ],
                'primary_specs' => [
                    'name' => 'Primary Specifications',
                    'fields' => ['icon', 'label', 'value'],
                    'converter' => null
                ],
                'use_cases' => [
                    'name' => 'Use Cases',
                    'fields' => ['icon', 'text'],
                    'converter' => null
                ],
                'faq_data' => [
                    'name' => 'FAQ',
                    'fields' => ['question', 'answer'],
                    'converter' => null
                ],
                'competitive_advantages' => [
                    'name' => 'Competitive Advantages',
                    'fields' => ['icon', 'text'],
                    'converter' => null
                ],
                'target_industries' => [
                    'name' => 'Target Industries',
                    'fields' => ['icon', 'text'],
                    'converter' => null
                ],
                'accessories' => [
                    'name' => 'Accessories',
                    'fields' => ['icon', 'name', 'price', 'description', 'is_standard', 'is_optional'],
                    'converter' => null
                ],
                'certifications' => [
                    'name' => 'Certifications',
                    'fields' => ['icon', 'name', 'year', 'authority'],
                    'converter' => null
                ],
            ];

            // Her mapping için veri taşı
            foreach ($mappings as $oldField => $config) {
                $oldData = $product->{$oldField};

                if (!$oldData) continue;

                // JSON decode et
                $data = is_string($oldData) ? json_decode($oldData, true) : $oldData;
                if (empty($data)) continue;

                // Converter varsa uygula (technical_specs için object → array)
                if ($config['converter']) {
                    $items = $config['converter']($data);
                } else {
                    $items = is_array($data) ? $data : [];
                }

                // Custom category oluştur
                $customJsonFields[$config['name']] = [
                    'fields' => $config['fields'],
                    'items' => $items
                ];
            }

            // custom_json_fields'i güncelle (sadece veri varsa)
            if (!empty($customJsonFields)) {
                DB::table('shop_products')
                    ->where('product_id', $product->product_id)
                    ->update([
                        'custom_json_fields' => json_encode($customJsonFields),
                        'updated_at' => now()
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * custom_json_fields → jsonInputs geri taşıma (data loss olabilir!)
     */
    public function down(): void
    {
        $products = DB::table('shop_products')->get();

        foreach ($products as $product) {
            if (!$product->custom_json_fields) continue;

            $customJsonFields = is_string($product->custom_json_fields)
                ? json_decode($product->custom_json_fields, true) ?? []
                : $product->custom_json_fields;

            $updates = [];

            // Reverse mapping
            $reverseMap = [
                'Technical Specifications' => 'technical_specs',
                'Features' => 'features',
                'Highlighted Features' => 'highlighted_features',
                'Primary Specifications' => 'primary_specs',
                'Use Cases' => 'use_cases',
                'FAQ' => 'faq_data',
                'Competitive Advantages' => 'competitive_advantages',
                'Target Industries' => 'target_industries',
                'Accessories' => 'accessories',
                'Certifications' => 'certifications',
            ];

            foreach ($reverseMap as $categoryName => $oldField) {
                if (isset($customJsonFields[$categoryName])) {
                    $items = $customJsonFields[$categoryName]['items'] ?? [];

                    // technical_specs: array → object
                    if ($oldField === 'technical_specs' && is_array($items)) {
                        $obj = [];
                        foreach ($items as $item) {
                            $obj[$item['key'] ?? ''] = $item['value'] ?? '';
                        }
                        $updates[$oldField] = json_encode($obj);
                    } else {
                        $updates[$oldField] = json_encode($items);
                    }
                }
            }

            if (!empty($updates)) {
                $updates['updated_at'] = now();
                DB::table('shop_products')
                    ->where('product_id', $product->product_id)
                    ->update($updates);
            }
        }
    }
};
