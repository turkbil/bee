<?php

namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Portfolio Category Seeder for Tenant2 Database
 * Languages: tr, en
 */
class PortfolioCategorySeederTenant2 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT2 portfolio categories (tr, en)...');
        
        // Duplicate kontrolü
        $existingCount = PortfolioCategory::count();
        if ($existingCount > 0) {
            $this->command->info("Portfolio categories already exist in TENANT2 database ({$existingCount} categories), skipping seeder...");
            return;
        }
        
        // Mevcut kategorileri sil (sadece boşsa)
        PortfolioCategory::truncate();
        
        
        $this->createWebDevelopmentCategory();
        $this->createDigitalMarketingCategory();
        $this->createMobileAppCategory();
    }
    
    private function createWebDevelopmentCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'tr' => 'Web Geliştirme', 
                'en' => 'Web Development'
            ],
            'slug' => [
                'tr' => 'web-gelistirme', 
                'en' => 'web-development'
            ],
            'description' => [
                'tr' => 'Modern web siteleri ve web uygulamaları geliştirme hizmetleri',
                'en' => 'Modern website and web application development services'
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->createSeoSetting(
            $category,
            'Web Geliştirme Kategorisi - Tenant2',
            'Web Development Category - Tenant2',
            'Modern web siteleri ve web uygulamaları geliştirme hizmetleri.',
            'Modern website and web application development services.'
        );
    }
    
    private function createDigitalMarketingCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'tr' => 'Dijital Pazarlama', 
                'en' => 'Digital Marketing'
            ],
            'slug' => [
                'tr' => 'dijital-pazarlama', 
                'en' => 'digital-marketing'
            ],
            'description' => [
                'tr' => 'SEO, sosyal medya pazarlama ve dijital reklam kampanyaları',
                'en' => 'SEO, social media marketing and digital advertising campaigns'
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->createSeoSetting(
            $category,
            'Dijital Pazarlama Kategorisi - Tenant2',
            'Digital Marketing Category - Tenant2',
            'SEO, sosyal medya pazarlama ve dijital reklam kampanyaları.',
            'SEO, social media marketing and digital advertising campaigns.'
        );
    }
    
    private function createMobileAppCategory(): void
    {
        $category = PortfolioCategory::create([
            'name' => [
                'tr' => 'Mobil Uygulama', 
                'en' => 'Mobile Application'
            ],
            'slug' => [
                'tr' => 'mobil-uygulama', 
                'en' => 'mobile-application'
            ],
            'description' => [
                'tr' => 'iOS ve Android mobil uygulama geliştirme projeleri',
                'en' => 'iOS and Android mobile application development projects'
            ],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->createSeoSetting(
            $category,
            'Mobil Uygulama Kategorisi - Tenant2',
            'Mobile Application Category - Tenant2',
            'iOS ve Android mobil uygulama geliştirme projeleri.',
            'iOS and Android mobile application development projects.'
        );
    }

    private function createSeoSetting($category, $titleTr, $titleEn, $descriptionTr, $descriptionEn): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for category {$category->category_id}");
        $this->command->info("Title TR: {$titleTr}");
        $this->command->info("Title EN: {$titleEn}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($category->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $category->seoSetting()->delete();
        }
        
        $category->seoSetting()->create([
            'titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'keywords' => [
                'tr' => ['portfolio', 'kategori', 'proje'],
                'en' => ['portfolio', 'category', 'project']
            ],
            'og_titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'og_descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'seo_score' => rand(80, 95),
        ]);
    }
}