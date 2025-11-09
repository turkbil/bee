<?php

namespace Modules\Payment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payment\App\Models\PaymentCategory;
use Illuminate\Support\Str;

/**
 * PaymentCategory Factory
 *
 * Gerçekçi kategori test verileri oluşturur.
 * Çoklu dil desteği içerir.
 *
 * @extends Factory<PaymentCategory>
 */
class PaymentCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentCategory::class;

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
                'tr' => $this->faker->sentence(10),
                'en' => $this->faker->sentence(10),
            ],
            'is_active' => $this->faker->boolean(90), // %90 aktif
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
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Inactive state
     * Pasif kategori
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Web Development category
     * Web Geliştirme kategorisi
     */
    public function webDevelopment(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => [
                'tr' => 'Web Geliştirme',
                'en' => 'Web Development',
            ],
            'slug' => [
                'tr' => 'web-gelistirme',
                'en' => 'web-development',
            ],
            'description' => [
                'tr' => 'Modern web uygulamaları ve web siteleri geliştirme projeleri',
                'en' => 'Modern web application and website development projects',
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * Mobile Development category
     * Mobil Geliştirme kategorisi
     */
    public function mobileDevelopment(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => [
                'tr' => 'Mobil Geliştirme',
                'en' => 'Mobile Development',
            ],
            'slug' => [
                'tr' => 'mobil-gelistirme',
                'en' => 'mobile-development',
            ],
            'description' => [
                'tr' => 'iOS ve Android mobil uygulama geliştirme projeleri',
                'en' => 'iOS and Android mobile application development projects',
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    /**
     * UI/UX Design category
     * UI/UX Tasarım kategorisi
     */
    public function uiuxDesign(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => [
                'tr' => 'UI/UX Tasarım',
                'en' => 'UI/UX Design',
            ],
            'slug' => [
                'tr' => 'uiux-tasarim',
                'en' => 'uiux-design',
            ],
            'description' => [
                'tr' => 'Kullanıcı deneyimi ve arayüz tasarım projeleri',
                'en' => 'User experience and interface design projects',
            ],
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    /**
     * Brand Identity category
     * Kurumsal Kimlik kategorisi
     */
    public function brandIdentity(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => [
                'tr' => 'Kurumsal Kimlik',
                'en' => 'Brand Identity',
            ],
            'slug' => [
                'tr' => 'kurumsal-kimlik',
                'en' => 'brand-identity',
            ],
            'description' => [
                'tr' => 'Logo, kurumsal kimlik ve marka tasarım projeleri',
                'en' => 'Logo, corporate identity and brand design projects',
            ],
            'is_active' => true,
            'sort_order' => 4,
        ]);
    }

    /**
     * E-Commerce category
     * E-Ticaret kategorisi
     */
    public function ecommerce(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => [
                'tr' => 'E-Ticaret',
                'en' => 'E-Commerce',
            ],
            'slug' => [
                'tr' => 'e-ticaret',
                'en' => 'e-commerce',
            ],
            'description' => [
                'tr' => 'Online alışveriş platformları ve e-ticaret çözümleri',
                'en' => 'Online shopping platforms and e-commerce solutions',
            ],
            'is_active' => true,
            'sort_order' => 5,
        ]);
    }
}
