<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopProductVariant;

/**
 * @extends Factory<ShopProductVariant>
 */
class ShopProductVariantFactory extends Factory
{
    protected $model = ShopProductVariant::class;

    public function definition(): array
    {
        $titleTr = $this->faker->words(2, true);
        $titleEn = $this->faker->words(2, true);

        return [
            'product_id' => ShopProduct::factory(),
            'sku' => strtoupper(Str::random(12)),
            'barcode' => $this->faker->optional()->ean13(),
            'title' => [
                'tr' => $titleTr,
                'en' => $titleEn,
            ],
            'option_values' => [
                'color' => $this->faker->safeColorName(),
                'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            ],
            'price_modifier' => $this->faker->randomFloat(2, -100, 250),
            'cost_price' => $this->faker->optional()->randomFloat(2, 10, 150),
            'stock_quantity' => $this->faker->numberBetween(0, 150),
            'reserved_quantity' => $this->faker->numberBetween(0, 25),
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 5),
            'dimensions' => $this->faker->optional()->randomElement([
                [
                    'length' => $this->faker->numberBetween(10, 60),
                    'width' => $this->faker->numberBetween(10, 40),
                    'height' => $this->faker->numberBetween(5, 30),
                    'unit' => 'cm',
                ],
            ]),
            'image_url' => $this->faker->optional()->imageUrl(600, 600, 'products', true),
            'images' => $this->faker->optional()->randomElements([
                $this->faker->imageUrl(800, 800, 'products', true),
                $this->faker->imageUrl(800, 800, 'fashion', true),
                $this->faker->imageUrl(800, 800, 'technics', true),
            ], $this->faker->numberBetween(0, 3)),
            'is_default' => false,
            'is_active' => $this->faker->boolean(85),
            'sort_order' => $this->faker->numberBetween(0, 50),
        ];
    }

    public function default(): self
    {
        return $this->state(fn(array $attributes): array => [
            'is_default' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn(array $attributes): array => [
            'is_active' => false,
        ]);
    }
}

