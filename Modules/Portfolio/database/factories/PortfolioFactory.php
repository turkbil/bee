<?php
namespace Modules\Portfolio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\Portfolio;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition()
    {
        $title = $this->faker->sentence(3);

        return [
            'tenant_id' => $this->faker->randomElement(\DB::table('tenants')->pluck('id')->toArray()), // tenant_id
            'title'     => $title,
            'slug'      => Str::slug($title),
            'body'      => $this->faker->paragraphs(3, true), // Paragraf
            'is_active' => 1,                                 // 'active' yerine 'is_active' kullanılmalı
            'css' => null,
            'js'        => null,
            'metakey'   => null,
            'metadesc'  => null,
        ];
    }
}
