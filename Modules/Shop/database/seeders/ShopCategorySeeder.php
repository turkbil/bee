<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopCategory;
use Modules\SeoManagement\App\Models\SeoSetting;
use Faker\Factory as Faker;

/**
 * Shop Category Seeder
 *
 * Temel shop kategorilerini oluşturur.
 * Tüm kategoriler JSON çoklu dil desteği ile gelir.
 *
 * @package Modules\Shop\Database\Seeders
 */
class ShopCategorySeeder extends Seeder
{
    private $fakers = [];
    private $languages = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Shop kategorileri SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('📁 Shop categories: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, shop tables do not exist in central!');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for Shop Categories!');
            return;
        }

        // Duplicate check
        if (ShopCategory::count() > 3) {
            $this->command->warn("⚠️  Shop categories exist (>3). Skipping...");
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

        $this->command->info("📁 Creating shop categories for languages: " . implode(', ', $this->languages));

        $categories = $this->getCategoryData();

        foreach ($categories as $categoryData) {
            $seoMeta = $categoryData['seo_meta'];
            unset($categoryData['seo_meta']);

            $category = ShopCategory::create($categoryData);
            $this->createSeoSettings($category, $seoMeta);

            $this->command->info("  ✓ Category created: {$categoryData['title'][$this->languages[0]]}");
        }

        $this->command->info("✅ Total " . count($categories) . " categories created");
    }

    /**
     * Get category data with translations
     * İXTİF - Türkiye'nin İstif Pazarı Kategorileri
     */
    private function getCategoryData(): array
    {
        $baseCategories = [
            [
                'tr' => 'Forklift',
                'en' => 'Forklift',
                'ar' => 'رافعة شوكية',
                'icon' => 'fa-solid fa-truck-moving',
                'description_tr' => 'Elektrikli, dizel ve LPG forklift çeşitlerimiz ile her ihtiyaca uygun çözümler sunuyoruz.',
                'description_en' => 'We offer solutions for every need with our electric, diesel and LPG forklift varieties.'
            ],
            [
                'tr' => 'Transpalet',
                'en' => 'Pallet Truck',
                'ar' => 'عربة منصات',
                'icon' => 'fa-solid fa-dolly',
                'description_tr' => 'Manuel, akülü ve tartılı transpalet seçenekleri ile yük taşıma işlemlerinizi kolaylaştırın.',
                'description_en' => 'Simplify your load handling operations with manual, electric and weighing pallet truck options.'
            ],
            [
                'tr' => 'İstif Makinesi',
                'en' => 'Stacker',
                'ar' => 'رافعة تخزين',
                'icon' => 'fa-solid fa-layer-group',
                'description_tr' => 'Akülü ve manuel istif makineleri ile depo raflarınıza güvenli ve hızlı yükleme yapın.',
                'description_en' => 'Load your warehouse shelves safely and quickly with electric and manual stackers.'
            ],
            [
                'tr' => 'Sipariş Toplama Makinesi',
                'en' => 'Order Picker',
                'ar' => 'آلة جمع الطلبات',
                'icon' => 'fa-solid fa-boxes-stacked',
                'description_tr' => 'Yüksek raflarda sipariş toplama işlemlerinizi güvenli ve verimli hale getirin.',
                'description_en' => 'Make your high-shelf order picking operations safe and efficient.'
            ],
            [
                'tr' => 'Otonom Sistemler',
                'en' => 'Autonomous Systems',
                'ar' => 'أنظمة مستقلة',
                'icon' => 'fa-solid fa-robot',
                'description_tr' => 'AGV ve AMR teknolojileri ile deponuzu geleceğe taşıyın. Akıllı ve otonom çözümler.',
                'description_en' => 'Take your warehouse to the future with AGV and AMR technologies. Smart and autonomous solutions.'
            ],
            [
                'tr' => 'Reach Truck',
                'en' => 'Reach Truck',
                'ar' => 'شاحنة الوصول',
                'icon' => 'fa-solid fa-truck-ramp-box',
                'description_tr' => 'Dar koridorlu depolarda yüksek raflara erişim için özel olarak tasarlanmış reach truck\'lar.',
                'description_en' => 'Reach trucks specially designed for high shelf access in narrow aisle warehouses.'
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

                // Kategori description (icon ile birlikte kaydet)
                if ($lang === 'tr') {
                    $descriptions[$lang] = $baseCategory['description_tr'] ?? $faker->sentence(rand(12, 18));
                } elseif ($lang === 'en') {
                    $descriptions[$lang] = $baseCategory['description_en'] ?? $faker->sentence(rand(12, 18));
                } else {
                    $descriptions[$lang] = $faker->sentence(rand(12, 18));
                }

                $seoMeta[$lang] = [
                    'title' => $title . ' | İXTİF - Türkiye\'nin İstif Pazarı',
                    'description' => $descriptions[$lang],
                ];
            }

            // Icon class ekle
            $iconClass = $baseCategory['icon'] ?? 'fa-solid fa-box';

            $categories[] = [
                'title' => $titles,
                'slug' => $slugs,
                'description' => $descriptions,
                'icon_class' => $iconClass,
                'sort_order' => $order++,
                'is_active' => true,
                'show_in_menu' => true,
                'show_in_homepage' => true,
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
                ['seoable_id' => $category->category_id, 'seoable_type' => ShopCategory::class],
                [
                    'titles' => [$lang => $data['title']],
                    'descriptions' => [$lang => $data['description']],
                    'status' => 'active',
                ]
            );
        }
    }
}
