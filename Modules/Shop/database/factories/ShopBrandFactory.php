<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopBrand;

/**
 * @extends Factory<ShopBrand>
 */
class ShopBrandFactory extends Factory
{
    protected $model = ShopBrand::class;

    public function definition(): array
    {
        $nameTr = $this->faker->company();
        $nameEn = $this->faker->company();

        return [
            'title' => [
                'tr' => $nameTr,
                'en' => $nameEn,
            ],
            'slug' => [
                'tr' => Str::slug($nameTr),
                'en' => Str::slug($nameEn),
            ],
            'description' => [
                'tr' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
            ],
            'logo_url' => $this->faker->imageUrl(300, 150, 'business', true),
            'website_url' => $this->faker->url(),
            'country_code' => $this->faker->countryCode(),
            'founded_year' => $this->faker->numberBetween(1950, date('Y')),
            'headquarters' => $this->faker->city(),
            'certifications' => [
                [
                    'name' => 'ISO 9001',
                    'year' => $this->faker->numberBetween(2000, 2024),
                ],
            ],
            'is_active' => $this->faker->boolean(90),
            'is_featured' => $this->faker->boolean(25),
            'sort_order' => $this->faker->numberBetween(0, 50),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes): array => [
            'is_active' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn(array $attributes): array => [
            'is_featured' => true,
        ]);
    }
}
