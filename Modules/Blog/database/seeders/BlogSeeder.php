<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogCategory;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Services\TenantLanguageProvider;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Blog SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('📦 Blog: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, blog tables do not exist in central!');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for Blog!');
            return;
        }

        $faker = Faker::create('tr_TR');
        $languages = TenantLanguageProvider::getActiveLanguageCodes();
        if (empty($languages)) {
            $languages = ['tr', 'en'];
        }

        // 5 Kategori
        $categoryNames = [
            'Web Tasarım' => 'Web Design',
            'Mobil Uygulama' => 'Mobile App',
            'E-Ticaret' => 'E-Commerce',
            'Kurumsal' => 'Corporate',
            'Dijital Pazarlama' => 'Digital Marketing'
        ];

        foreach ($categoryNames as $nameTr => $nameEn) {
            $slugTr = Str::slug($nameTr);

            $category = BlogCategory::where('slug->tr', $slugTr)->first();

            if (!$category) {
                $category = BlogCategory::create([
                    'title' => $this->buildLocalizedArray($languages, fn (string $locale) => $locale === 'en' ? $nameEn : $nameTr),
                    'slug' => $this->buildLocalizedArray($languages, fn (string $locale) => Str::slug($locale === 'en' ? $nameEn : $nameTr)),
                    'description' => $this->buildLocalizedArray($languages, fn ($localeCode) => $faker->paragraph(3)),
                    'is_active' => true,
                    'sort_order' => 0,
                    'parent_id' => null,
                ]);
            }

            // Her kategori için 10 blog
            if (Blog::count() === 0) {
                for ($i = 1; $i <= 10; $i++) {
                    $title = ucwords($faker->words(rand(3, 6), true));
                    $baseSlug = Str::slug($title);
                    $uniqueSuffix = $faker->unique()->numberBetween(1000, 9999);

                    // İçerikleri tüm diller için üret
                $contentByLocale = [];
                foreach ($languages as $localeCode) {
                    $contentByLocale[$localeCode] = $this->generateContent($faker);
                }

                // Excerpt ve reading time hesapla
                $excerptByLocale = [];
                foreach ($contentByLocale as $localeCode => $content) {
                    $excerptByLocale[$localeCode] = Str::limit(strip_tags($content), 180);
                }

                // Yayın durumu belirle
                $statusRoll = $faker->numberBetween(1, 100);
                if ($statusRoll <= 60) {
                    $status = 'published';
                    $publishedAt = $faker->dateTimeBetween('-6 months', '-1 day');
                } elseif ($statusRoll <= 80) {
                    $status = 'scheduled';
                    $publishedAt = $faker->dateTimeBetween('now', '+1 month');
                } else {
                    $status = 'draft';
                    $publishedAt = null;
                }

                $tagNames = $faker->randomElements([
                    'teknoloji', 'web', 'mobil', 'tasarım', 'geliştirme',
                    'seo', 'pazarlama', 'sosyal medya', 'e-ticaret', 'startup'
                ], rand(1, 4));

                $blog = Blog::create([
                    'title' => $this->buildLocalizedArray($languages, fn ($localeCode) => $title),
                    'slug' => $this->buildLocalizedArray($languages, fn ($localeCode) => $baseSlug . '-' . $uniqueSuffix),
                    'body' => $contentByLocale,
                    'excerpt' => $excerptByLocale,
                    'published_at' => $publishedAt,
                    'is_featured' => $faker->boolean(20),
                    'status' => $status,
                    'blog_category_id' => $category->category_id,
                    'is_active' => $status === 'published' ? true : $faker->boolean(60),
                ]);

                $blog->syncTagsByName($tagNames, 'blog');
                }
            }
        }
    }

    private function generateContent($faker): string
    {
        $sections = rand(2, 3);
        $content = '';

        for ($s = 0; $s < $sections; $s++) {
            $content .= '<h2>' . $faker->sentence(6) . '</h2>';
            $content .= '<p>' . $faker->paragraph(3) . '</p>';

            $content .= '<h3>' . $faker->sentence(5) . '</h3>';
            $content .= '<p>' . $faker->paragraph(2) . '</p>';

            $content .= '<h4>' . $faker->sentence(4) . '</h4>';
            $content .= '<p>' . $faker->paragraph(2) . '</p>';

            $content .= $this->generateListBlock($faker);
        }

        $content .= '<blockquote><p>' . $faker->sentence(8) . '</p></blockquote>';

        return $content;
    }

    private function generateListBlock($faker): string
    {
        $html = '<ul>';
        for ($i = 0; $i < rand(3, 5); $i++) {
            $html .= '<li>' . $faker->sentence(rand(8, 12)) . '</li>';
        }
        $html .= '</ul>';

        $html .= '<ol>';
        for ($i = 0; $i < rand(2, 4); $i++) {
            $html .= '<li>' . $faker->sentence(rand(6, 10)) . '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    private function buildLocalizedArray(array $languages, callable $callback): array
    {
        $localized = [];

        foreach ($languages as $localeCode) {
            $localized[$localeCode] = $callback($localeCode);
        }

        return $localized;
    }
}
