<?php

namespace Modules\Portfolio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Illuminate\Support\Str;

/**
 * PortfolioCategory Factory
 *
 * Gerçekçi test verileri oluşturur.
 * Çoklu dil desteği ve SEO ayarları içerir.
 *
 * @extends Factory<PortfolioCategory>
 */
class PortfolioCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PortfolioCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameTr = $this->faker->words(2, true);
        $nameEn = $this->faker->words(2, true);

        $slugTr = Str::slug($nameTr);
        $slugEn = Str::slug($nameEn);

        return [
            'name' => [
                'tr' => ucfirst($nameTr),
                'en' => ucfirst($nameEn),
            ],
            'slug' => [
                'tr' => $slugTr,
                'en' => $slugEn,
            ],
            'description' => [
                'tr' => $this->faker->sentence(8),
                'en' => $this->faker->sentence(8),
            ],
            'is_active' => $this->faker->boolean(85), // %85 aktif
            'sort_order' => $this->faker->numberBetween(0, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Active state
     * Aktif kategori
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Inactive state
     * Pasif kategori
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * With specific sort order
     * Belirli sıra ile kategori
     */
    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }

    /**
     * Web Design category state
     */
    public function webDesign(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => [
                'tr' => 'Web Tasarım',
                'en' => 'Web Design',
            ],
            'slug' => [
                'tr' => 'web-tasarim',
                'en' => 'web-design',
            ],
            'description' => [
                'tr' => 'Modern ve responsive web tasarım projeleri',
                'en' => 'Modern and responsive web design projects',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Mobile App category state
     */
    public function mobileApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => [
                'tr' => 'Mobil Uygulama',
                'en' => 'Mobile Application',
            ],
            'slug' => [
                'tr' => 'mobil-uygulama',
                'en' => 'mobile-application',
            ],
            'description' => [
                'tr' => 'iOS ve Android mobil uygulama geliştirme projeleri',
                'en' => 'iOS and Android mobile application development projects',
            ],
            'is_active' => true,
        ]);
    }
}
