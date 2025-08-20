<?php

namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Models\SeoSetting;

/**
 * Portfolio Category Seeder for Tenant3 Database
 * Languages: en, ar
 */
class PortfolioCategorySeederTenant3 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT3 portfolio categories (en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = PortfolioCategory::count();
        if ($existingCount > 0) {
            $this->command->info("Portfolio categories already exist in TENANT3 database ({$existingCount} categories), skipping seeder...");
            return;
        }
        
        // Mevcut kategorileri sil (sadece boşsa)
        PortfolioCategory::truncate();
        SeoSetting::where('seoable_type', 'like', '%PortfolioCategory%')->delete();
        
        $this->createWebDevelopmentCategory();
        $this->createAICategory();
        $this->createDesignCategory();
    }
    
    private function createWebDevelopmentCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'en' => 'Web Development',
                'ar' => 'تطوير الويب'
            ],
            'slug' => [
                'en' => 'web-development',
                'ar' => 'تطوير-الويب'
            ],
            'description' => [
                'en' => 'Modern website and web application development services',
                'ar' => 'خدمات تطوير المواقع والتطبيقات الحديثة'
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->createSeoSetting(
            $category,
            'Web Development Category - Tenant3',
            'فئة تطوير الويب - Tenant3',
            'Modern website and web application development services.',
            'خدمات تطوير المواقع والتطبيقات الحديثة.'
        );
    }
    
    private function createAICategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'en' => 'Artificial Intelligence',
                'ar' => 'الذكاء الاصطناعي'
            ],
            'slug' => [
                'en' => 'artificial-intelligence',
                'ar' => 'الذكاء-الاصطناعي'
            ],
            'description' => [
                'en' => 'Enterprise AI solutions and custom trained AI systems',
                'ar' => 'حلول الذكاء الاصطناعي المؤسسية وأنظمة الذكاء الاصطناعي المدربة خصيصاً'
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->createSeoSetting(
            $category,
            'Artificial Intelligence Category - Tenant3',
            'فئة الذكاء الاصطناعي - Tenant3',
            'Enterprise AI solutions and custom trained AI systems.',
            'حلول الذكاء الاصطناعي المؤسسية وأنظمة الذكاء الاصطناعي المدربة خصيصاً.'
        );
    }
    
    private function createDesignCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'en' => 'Design',
                'ar' => 'التصميم'
            ],
            'slug' => [
                'en' => 'design',
                'ar' => 'التصميم'
            ],
            'description' => [
                'en' => 'UI/UX design, graphic design and brand identity projects',
                'ar' => 'مشاريع تصميم واجهة المستخدم والتصميم الجرافيكي وهوية العلامة التجارية'
            ],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->createSeoSetting(
            $category,
            'Design Category - Tenant3',
            'فئة التصميم - Tenant3',
            'UI/UX design, graphic design and brand identity projects.',
            'مشاريع تصميم واجهة المستخدم والتصميم الجرافيكي وهوية العلامة التجارية.'
        );
    }

    private function createSeoSetting($category, $titleEn, $titleAr, $descriptionEn, $descriptionAr): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for category {$category->category_id}");
        $this->command->info("Title EN: {$titleEn}");
        $this->command->info("Title AR: {$titleAr}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($category->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $category->seoSetting()->delete();
        }
        
        $category->seoSetting()->create([
            'titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'keywords' => [
                'en' => ['portfolio', 'category', 'project'],
                'ar' => ['محفظة', 'فئة', 'مشروع']
            ],
            'og_titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'og_descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'available_languages' => ['en', 'ar'],
            'default_language' => 'en',
            'seo_score' => rand(80, 95),
        ]);
    }
}