<?php

namespace Database\Factories;

use Modules\Page\App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $titleTr = $this->faker->sentence(3);
        $titleEn = $this->faker->sentence(3);

        return [
            'title' => json_encode([
                'tr' => $titleTr,
                'en' => $titleEn
            ]),
            'slug' => json_encode([
                'tr' => \Illuminate\Support\Str::slug($titleTr),
                'en' => \Illuminate\Support\Str::slug($titleEn)
            ]),
            'body' => json_encode([
                'tr' => $this->faker->paragraphs(3, true),
                'en' => $this->faker->paragraphs(3, true)
            ]),
            'css' => json_encode([
                'tr' => '',
                'en' => ''
            ]),
            'js' => json_encode([
                'tr' => '',
                'en' => ''
            ]),
            'seo' => json_encode([
                'tr' => [
                    'meta_title' => $titleTr,
                    'meta_description' => $this->faker->sentence(10),
                    'keywords' => [$this->faker->word(), $this->faker->word()],
                    'og_image' => null
                ],
                'en' => [
                    'meta_title' => $titleEn,
                    'meta_description' => $this->faker->sentence(10),
                    'keywords' => [$this->faker->word(), $this->faker->word()],
                    'og_image' => null
                ]
            ]),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'is_homepage' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function homepage(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_homepage' => true,
            'is_active' => true,
        ]);
    }
}