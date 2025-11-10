<?php

namespace Modules\Favorite\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Favorite\App\Models\Favorite;
use Modules\SeoManagement\App\Models\SeoSetting;
use Faker\Factory as Faker;

class FavoriteSeederTenant4 extends Seeder
{
    private $faker;

    public function run(): void
    {
        $this->faker = Faker::create('tr_TR');

        if (Favorite::count() > 0) {
            $this->command->warn("⚠️  Favorites exist. Skipping...");
            return;
        }

        $this->command->info('📝 Creating favorites with Faker...');

        for ($i = 1; $i <= 12; $i++) {
            $data = $this->generateFavorite($i);
            $seoMeta = $data['seo_meta'];
            unset($data['seo_meta']);

            $favorite = Favorite::create($data);
            $this->createSeoSettings($favorite, $seoMeta);

            $this->command->info("  ✅ {$data['title']['tr']}");
        }

        $this->command->info("🎉 Created 12 favorites!");
    }

    private function generateFavorite(int $index): array
    {
        $titleTr = $this->faker->sentence(rand(4, 7));
        $titleEn = ucfirst($this->faker->words(rand(4, 7), true));

        // Türkçe body - Uzun paragraflar
        $bodyTr = '<div class="prose dark:prose-invert max-w-none">';
        $bodyTr .= '<h2>' . $titleTr . '</h2>';
        for ($i = 0; $i < rand(5, 8); $i++) {
            $bodyTr .= '<p>' . $this->faker->paragraph(rand(10, 20)) . '</p>';
        }
        $bodyTr .= '</div>';

        // İngilizce body - Uzun paragraflar
        $fakerEn = Faker::create('en_US');
        $bodyEn = '<div class="prose dark:prose-invert max-w-none">';
        $bodyEn .= '<h2>' . $titleEn . '</h2>';
        for ($i = 0; $i < rand(5, 8); $i++) {
            $bodyEn .= '<p>' . $fakerEn->paragraph(rand(10, 20)) . '</p>';
        }
        $bodyEn .= '</div>';

        return [
            'title' => [
                'tr' => $titleTr,
                'en' => $titleEn,
            ],
            'slug' => [
                'tr' => \Str::slug($titleTr),
                'en' => \Str::slug($titleEn),
            ],
            'body' => [
                'tr' => $bodyTr,
                'en' => $bodyEn,
            ],
            'is_active' => rand(0, 10) > 2, // %80 aktif
            'seo_meta' => [
                'tr' => [
                    'title' => $titleTr,
                    'description' => $this->faker->sentence(15),
                ],
                'en' => [
                    'title' => $titleEn,
                    'description' => $fakerEn->sentence(15),
                ],
            ]
        ];
    }

    private function createSeoSettings($favorite, $seoMeta): void
    {
        foreach ($seoMeta as $lang => $data) {
            $favorite->seoSetting()->updateOrCreate(
                ['seoable_id' => $favorite->favorite_id, 'seoable_type' => Favorite::class],
                [
                    'titles' => [$lang => $data['title']],
                    'descriptions' => [$lang => $data['description']],
                    'status' => 'active',
                ]
            );
        }
    }
}
