<?php

namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Portfolio Category Seeder for Tenant4 Database
 * Languages: en
 */
class PortfolioCategorySeederTenant4 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT4 portfolio categories (en)...');
        
        // Duplicate kontrolü
        $existingCount = PortfolioCategory::count();
        if ($existingCount > 0) {
            $this->command->info("Portfolio categories already exist in TENANT4 database ({$existingCount} categories), skipping seeder...");
            return;
        }
        
        // Mevcut kategorileri sil (sadece boşsa)
        PortfolioCategory::truncate();
        
        
        $this->createWebDevelopmentCategory();
        $this->createMobileAppCategory();
    }
    
    private function createWebDevelopmentCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'en' => 'Web Development'
            ],
            'slug' => [
                'en' => 'web-development'
            ],
            'description' => [
                'en' => 'Modern website and web application development services'
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->createSeoSetting(
            $category,
            'Web Development Category - Tenant4',
            'Modern website and web application development services.'
        );
    }
    
    private function createMobileAppCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'en' => 'Mobile Application'
            ],
            'slug' => [
                'en' => 'mobile-application'
            ],
            'description' => [
                'en' => 'iOS and Android mobile application development projects'
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->createSeoSetting(
            $category,
            'Mobile Application Category - Tenant4',
            'iOS and Android mobile application development projects.'
        );
    }

    private function createSeoSetting($category, $titleEn, $descriptionEn): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for category {$category->category_id}");
        $this->command->info("Title EN: {$titleEn}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($category->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $category->seoSetting()->delete();
        }
        
        $category->seoSetting()->create([
            'titles' => [
                'en' => $titleEn
            ],
            'descriptions' => [
                'en' => $descriptionEn
            ],
            'keywords' => [
                'en' => ['portfolio', 'category', 'project']
            ],
            'og_titles' => [
                'en' => $titleEn
            ],
            'og_descriptions' => [
                'en' => $descriptionEn
            ],
            'seo_score' => rand(80, 95),
        ]);
    }
}