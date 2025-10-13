<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Shop\App\Models\ShopProductFieldTemplate;

class ShopProductFieldTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu tablo sadece tenant veritabanında var
        // Central seeding sırasında çalışırsa atla
        if (!Schema::hasTable('shop_product_field_templates')) {
            $this->command->warn('⚠️  ShopProductFieldTemplateSeeder sadece tenant database için, atlanıyor...');
            return;
        }
        $templates = [
            [
                'name' => 'Kitap Ürünü',
                'description' => 'Kitap satışı için gerekli alanlar',
                'fields' => [
                    ['name' => 'author', 'type' => 'input', 'order' => 0],
                    ['name' => 'publisher', 'type' => 'input', 'order' => 1],
                    ['name' => 'isbn', 'type' => 'input', 'order' => 2],
                    ['name' => 'page_count', 'type' => 'input', 'order' => 3],
                    ['name' => 'publication_year', 'type' => 'input', 'order' => 4],
                    ['name' => 'language', 'type' => 'input', 'order' => 5],
                    ['name' => 'summary', 'type' => 'textarea', 'order' => 6],
                    ['name' => 'is_bestseller', 'type' => 'checkbox', 'order' => 7],
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Elektronik Cihaz',
                'description' => 'Elektronik ürünler için teknik özellikler',
                'fields' => [
                    ['name' => 'brand', 'type' => 'input', 'order' => 0],
                    ['name' => 'model', 'type' => 'input', 'order' => 1],
                    ['name' => 'warranty_period', 'type' => 'input', 'order' => 2],
                    ['name' => 'power', 'type' => 'input', 'order' => 3],
                    ['name' => 'dimensions', 'type' => 'input', 'order' => 4],
                    ['name' => 'weight', 'type' => 'input', 'order' => 5],
                    ['name' => 'technical_specs', 'type' => 'textarea', 'order' => 6],
                    ['name' => 'has_warranty', 'type' => 'checkbox', 'order' => 7],
                    ['name' => 'is_waterproof', 'type' => 'checkbox', 'order' => 8],
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Giyim Ürünü',
                'description' => 'Tekstil ve giyim ürünleri için',
                'fields' => [
                    ['name' => 'size', 'type' => 'input', 'order' => 0],
                    ['name' => 'color', 'type' => 'input', 'order' => 1],
                    ['name' => 'fabric', 'type' => 'input', 'order' => 2],
                    ['name' => 'care_instructions', 'type' => 'textarea', 'order' => 3],
                    ['name' => 'is_unisex', 'type' => 'checkbox', 'order' => 4],
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Endüstriyel Makine',
                'description' => 'Mevcut verileriniz için (Technical Specifications vs.)',
                'fields' => [
                    ['name' => 'technical_specs_key', 'type' => 'input', 'order' => 0],
                    ['name' => 'technical_specs_value', 'type' => 'input', 'order' => 1],
                    ['name' => 'feature_icon', 'type' => 'input', 'order' => 2],
                    ['name' => 'feature_text', 'type' => 'input', 'order' => 3],
                    ['name' => 'certification_name', 'type' => 'input', 'order' => 4],
                    ['name' => 'certification_year', 'type' => 'input', 'order' => 5],
                    ['name' => 'use_case_description', 'type' => 'textarea', 'order' => 6],
                    ['name' => 'has_ce_certificate', 'type' => 'checkbox', 'order' => 7],
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($templates as $template) {
            ShopProductFieldTemplate::updateOrCreate(
                ['name' => $template['name']], // Unique anahtar
                $template // Güncellenecek/oluşturulacak veriler
            );
        }
    }
}
