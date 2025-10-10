<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopCategory;

/**
 * @extends Factory<ShopCategory>
 */
class ShopCategoryFactory extends Factory
{
    protected $model = ShopCategory::class;

    public function definition(): array
    {
        $nameTr = $this->faker->words(2, true);
        $nameEn = $this->faker->words(2, true);

        return [
            'parent_id' => null,
            'title' => [
                'tr' => Str::title($nameTr),
                'en' => Str::title($nameEn),
            ],
            'slug' => [
                'tr' => Str::slug($nameTr),
                'en' => Str::slug($nameEn),
            ],
            'description' => [
                'tr' => $this->faker->sentence(12),
                'en' => $this->faker->sentence(12),
            ],
            'image_url' => $this->faker->imageUrl(640, 480, 'technics', true),
            'icon_class' => $this->faker->randomElement(['fa-laptop', 'fa-mobile', 'fa-plug', 'fa-tv']),
            'level' => 1,
            'path' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean(90),
            'show_in_menu' => $this->faker->boolean(80),
            'show_in_homepage' => $this->faker->boolean(40),
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

    public function childOf(ShopCategory $parent): static
    {
        return $this->state(fn(array $attributes): array => [
            'parent_id' => $parent->category_id,
            'level' => $parent->level + 1,
            'path' => trim(($parent->path ? $parent->path . '.' : '') . $parent->category_id, '.'),
        ]);
    }
}
