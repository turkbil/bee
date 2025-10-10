<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Shop\App\Enums\ProductCondition;
use Modules\Shop\App\Enums\ProductType;
use Modules\Shop\App\Models\ShopBrand;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopProduct;

/**
 * @extends Factory<ShopProduct>
 */
class ShopProductFactory extends Factory
{
    protected $model = ShopProduct::class;

    public function definition(): array
    {
        $titleTr = $this->faker->words(3, true);
        $titleEn = $this->faker->words(3, true);

        $productType = Arr::random(ProductType::cases())->value;
        $condition = Arr::random(ProductCondition::cases())->value;

        $basePrice = $this->faker->randomFloat(2, 100, 5000);
        $compareAt = $this->faker->boolean(40)
            ? $basePrice + $this->faker->randomFloat(2, 50, 500)
            : null;

        return [
            'category_id' => ShopCategory::factory(),
            'brand_id' => ShopBrand::factory(),
            'sku' => strtoupper(Str::random(10)),
            'model_number' => strtoupper(Str::random(6)),
            'barcode' => $this->faker->ean13(),
            'title' => [
                'tr' => $titleTr,
                'en' => $titleEn,
            ],
            'slug' => [
                'tr' => Str::slug($titleTr),
                'en' => Str::slug($titleEn),
            ],
            'short_description' => [
                'tr' => $this->faker->sentence(),
                'en' => $this->faker->sentence(),
            ],
            'long_description' => [
                'tr' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
                'en' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            ],
            'product_type' => $productType,
            'condition' => $condition,
            'price_on_request' => false,
            'base_price' => $basePrice,
            'compare_at_price' => $compareAt,
            'cost_price' => $this->faker->randomFloat(2, 50, $basePrice - 10),
            'currency' => 'TRY',
            'deposit_required' => $this->faker->boolean(20),
            'deposit_amount' => $this->faker->randomFloat(2, 0, 500),
            'deposit_percentage' => $this->faker->optional()->numberBetween(10, 50),
            'installment_available' => $this->faker->boolean(60),
            'max_installments' => $this->faker->randomElement([6, 9, 12, 18]),
            'stock_tracking' => true,
            'current_stock' => $this->faker->numberBetween(0, 250),
            'low_stock_threshold' => $this->faker->numberBetween(3, 15),
            'allow_backorder' => $this->faker->boolean(10),
            'lead_time_days' => $this->faker->optional()->numberBetween(1, 14),
            'weight' => $this->faker->randomFloat(2, 0.1, 15),
            'dimensions' => [
                'length' => $this->faker->numberBetween(10, 150),
                'width' => $this->faker->numberBetween(10, 80),
                'height' => $this->faker->numberBetween(5, 60),
                'unit' => 'cm',
            ],
            'technical_specs' => [
                'power' => $this->faker->numberBetween(100, 1000) . 'W',
                'voltage' => '220V',
            ],
            'features' => $this->faker->words(5),
            'highlighted_features' => [
                [
                    'icon' => 'flash',
                    'title' => $this->faker->sentence(3),
                    'description' => $this->faker->sentence(),
                ],
                [
                    'icon' => 'shield',
                    'title' => $this->faker->sentence(3),
                    'description' => $this->faker->sentence(),
                ],
            ],
            'media_gallery' => [
                [
                    'type' => 'image',
                    'url' => $this->faker->imageUrl(800, 600, 'technics', true),
                    'is_primary' => true,
                ],
            ],
            'video_url' => $this->faker->optional()->url(),
            'manual_pdf_url' => $this->faker->optional()->url(),
            'is_active' => $this->faker->boolean(85),
            'is_featured' => $this->faker->boolean(20),
            'is_bestseller' => $this->faker->boolean(10),
            'view_count' => $this->faker->numberBetween(0, 1500),
            'sales_count' => $this->faker->numberBetween(0, 500),
            'published_at' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'warranty_info' => [
                'period' => 24,
                'unit' => 'month',
                'details' => $this->faker->sentence(),
            ],
            'shipping_info' => [
                'weight_limit' => 50,
                'size_limit' => 'large',
                'free_shipping' => $this->faker->boolean(30),
            ],
            'tags' => $this->faker->words(3),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes): array => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function withoutBrand(): static
    {
        return $this->state(fn(array $attributes): array => [
            'brand_id' => null,
        ]);
    }

    public function priceOnRequest(): static
    {
        return $this->state(fn(array $attributes): array => [
            'price_on_request' => true,
            'base_price' => null,
            'compare_at_price' => null,
        ]);
    }
}
