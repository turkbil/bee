<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\App\Models\BlogCategory;
use Modules\SeoManagement\App\Models\SeoSetting;
use Faker\Factory as Faker;

/**
 * Blog Category Seeder
 *
 * Temel blog kategorilerini oluşturur.
 * Tüm kategoriler JSON çoklu dil desteği ile gelir.
 *
 * @package Modules\Blog\Database\Seeders
 */
class BlogCategorySeeder extends Seeder
{
    private $fakers = [];
    private $languages = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Blog kategorileri SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('📁 Blog categories: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, blog tables do not exist in central!');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for Blog Categories!');
            return;
        }

        // Duplicate check
        if (BlogCategory::count() > 3) {
            $this->command->warn("⚠️  Blog categories exist (>3). Skipping...");
            return;
        }

        // Tenant dillerini al
        $this->languages = \DB::table('tenant_languages')
            ->where('is_active', 1)
            ->pluck('code')
            ->toArray();

        if (empty($this->languages)) {
            $this->languages = ['tr', 'en']; // Fallback
        }

        // Her dil için Faker instance oluştur
        $localeMap = ['tr' => 'tr_TR', 'en' => 'en_US', 'ar' => 'ar_SA'];
        foreach ($this->languages as $lang) {
            $locale = $localeMap[$lang] ?? 'en_US';
            $this->fakers[$lang] = Faker::create($locale);
        }

        $this->command->info("📁 Creating blog categories for languages: " . implode(', ', $this->languages));

        $categories = $this->getCategoryData();

        foreach ($categories as $categoryData) {
            $seoMeta = $categoryData['seo_meta'];
            unset($categoryData['seo_meta']);

            $category = BlogCategory::create($categoryData);
            $this->createSeoSettings($category, $seoMeta);

            $this->command->info("  ✓ Category created: {$categoryData['title'][$this->languages[0]]}");
        }

        $this->command->info("✅ Total " . count($categories) . " categories created");
    }

    /**
     * Get category data with translations
     */
    private function getCategoryData(): array
    {
        $baseCategories = [
            [
                'tr' => 'Web Geliştirme',
                'en' => 'Web Development',
                'ar' => 'تطوير الويب'
            ],
            [
                'tr' => 'Mobil Uygulamalar',
                'en' => 'Mobile Apps',
                'ar' => 'تطبيقات الجوال'
            ],
            [
                'tr' => 'E-Ticaret',
                'en' => 'E-Commerce',
                'ar' => 'التجارة الإلكترونية'
            ],
            [
                'tr' => 'UI/UX Tasarım',
                'en' => 'UI/UX Design',
                'ar' => 'تصميم واجهة المستخدم'
            ],
            [
                'tr' => 'SEO & Dijital Pazarlama',
                'en' => 'SEO & Digital Marketing',
                'ar' => 'تحسين محركات البحث والتسويق الرقمي'
            ],
        ];

        $categories = [];
        $order = 1;

        foreach ($baseCategories as $baseCategory) {
            $titles = [];
            $slugs = [];
            $descriptions = [];
            $seoMeta = [];

            foreach ($this->languages as $lang) {
                $title = $baseCategory[$lang] ?? $baseCategory['en'];
                $faker = $this->fakers[$lang];

                $titles[$lang] = $title;
                $slugs[$lang] = \Str::slug($title);
                $descriptions[$lang] = $faker->sentence(rand(12, 18));

                $seoMeta[$lang] = [
                    'title' => $title,
                    'description' => $faker->sentence(15),
                ];
            }

            $categories[] = [
                'title' => $titles,
                'slug' => $slugs,
                'description' => $descriptions,
                'sort_order' => $order++,
                'is_active' => true,
                'seo_meta' => $seoMeta
            ];
        }

        return $categories;
    }

    /**
     * Create SEO settings for category
     */
    private function createSeoSettings($category, $seoMeta): void
    {
        foreach ($seoMeta as $lang => $data) {
            $category->seoSetting()->updateOrCreate(
                ['seoable_id' => $category->category_id, 'seoable_type' => BlogCategory::class],
                [
                    'titles' => [$lang => $data['title']],
                    'descriptions' => [$lang => $data['description']],
                    'status' => 'active',
                ]
            );
        }
    }
}
