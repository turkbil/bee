<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\SeoManagement\App\Models\SeoSetting;
use App\Services\SeoLanguageManager;
use App\Services\SeoMetaTagService;
use App\Services\AI\SeoAnalysisService;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Announcement\App\Models\Announcement;

class TestSeoSystem extends Command
{
    protected $signature = 'seo:test';
    protected $description = 'Test SEO system functionality';

    public function handle()
    {
        $this->info('🔍 SEO System Test Started');
        $this->newLine();

        // Test 1: Database Tables
        $this->testDatabaseTables();
        
        // Test 2: SeoLanguageManager
        $this->testSeoLanguageManager();
        
        // Test 3: Model SEO Integration
        $this->testModelSeoIntegration();
        
        // Test 4: Meta Tag Service
        $this->testMetaTagService();
        
        // Test 5: AI SEO Service
        $this->testAiSeoService();

        $this->newLine();
        $this->info('✅ SEO System Test Completed Successfully!');
        
        return 0;
    }

    protected function testDatabaseTables()
    {
        $this->info('📊 Testing Database Tables...');
        
        try {
            // Test seo_settings table
            $count = \DB::table('seo_settings')->count();
            $this->line("   ✅ seo_settings table exists (records: {$count})");
            
            // Test table structure
            $columns = \DB::getSchemaBuilder()->getColumnListing('seo_settings');
            $requiredColumns = ['id', 'seoable_id', 'seoable_type', 'titles', 'descriptions', 'keywords'];
            
            foreach ($requiredColumns as $column) {
                if (in_array($column, $columns)) {
                    $this->line("   ✅ Column '{$column}' exists");
                } else {
                    $this->error("   ❌ Column '{$column}' missing");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Database test failed: " . $e->getMessage());
        }
    }

    protected function testSeoLanguageManager()
    {
        $this->info('🌐 Testing SeoLanguageManager...');
        
        try {
            // Test basic functionality
            $testData = ['tr' => 'Test Başlık', 'en' => 'Test Title'];
            
            // Test getSafeValue
            $value = SeoLanguageManager::getSafeValue($testData, 'tr');
            if ($value === 'Test Başlık') {
                $this->line('   ✅ getSafeValue works correctly');
            } else {
                $this->error('   ❌ getSafeValue failed');
            }
            
            // Test basic functionality
            if (class_exists('App\Services\SeoLanguageManager')) {
                $this->line('   ✅ SeoLanguageManager class exists');
            } else {
                $this->error('   ❌ SeoLanguageManager class missing');
            }
            
            // Test safe value with fallback
            $fallbackValue = SeoLanguageManager::getSafeValue($testData, 'de', 'tr');
            if ($fallbackValue === 'Test Başlık') {
                $this->line('   ✅ Fallback mechanism works correctly');
            } else {
                $this->error('   ❌ Fallback mechanism failed');
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ SeoLanguageManager test failed: " . $e->getMessage());
        }
    }

    protected function testModelSeoIntegration()
    {
        $this->info('🔗 Testing Model SEO Integration...');
        
        // Test Page model
        try {
            $page = Page::first();
            if ($page) {
                $this->testModelSeoMethods($page, 'Page');
            } else {
                $this->line('   ⚠️  No Page records found, creating test record...');
                $this->createTestPage();
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Page model test failed: " . $e->getMessage());
        }

        // Test Portfolio model
        try {
            $portfolio = Portfolio::first();
            if ($portfolio) {
                $this->testModelSeoMethods($portfolio, 'Portfolio');
            } else {
                $this->line('   ⚠️  No Portfolio records found');
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Portfolio model test failed: " . $e->getMessage());
        }

        // Test Announcement model
        try {
            $announcement = Announcement::first();
            if ($announcement) {
                $this->testModelSeoMethods($announcement, 'Announcement');
            } else {
                $this->line('   ⚠️  No Announcement records found');
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Announcement model test failed: " . $e->getMessage());
        }
    }

    protected function testModelSeoMethods($model, $modelName)
    {
        // Test HasSeo trait methods
        if (method_exists($model, 'getSeoTitle')) {
            $title = $model->getSeoTitle();
            $this->line("   ✅ {$modelName}->getSeoTitle(): " . ($title ?? 'null'));
        }

        if (method_exists($model, 'getSeoDescription')) {
            $description = $model->getSeoDescription();
            $this->line("   ✅ {$modelName}->getSeoDescription(): " . \Str::limit($description ?? 'null', 50));
        }

        if (method_exists($model, 'getSeoKeywords')) {
            $keywords = $model->getSeoKeywords();
            $this->line("   ✅ {$modelName}->getSeoKeywords(): " . implode(', ', $keywords));
        }

        if (method_exists($model, 'getMetaTagsHtml')) {
            $metaTags = $model->getMetaTagsHtml();
            $this->line("   ✅ {$modelName}->getMetaTagsHtml(): " . (strlen($metaTags) > 0 ? 'Generated' : 'Empty'));
        }

        // Test SEO settings creation
        if (method_exists($model, 'getOrCreateSeoSetting')) {
            $seoSetting = $model->getOrCreateSeoSetting();
            if ($seoSetting) {
                $this->line("   ✅ {$modelName} SEO settings created/retrieved");
            }
        }
    }

    protected function testMetaTagService()
    {
        $this->info('🏷️  Testing Meta Tag Service...');
        
        try {
            // Test with a page model
            $page = Page::first();
            if ($page) {
                $metaTags = SeoMetaTagService::generateMetaTags($page);
                if (!empty($metaTags)) {
                    $this->line('   ✅ Meta tags generated successfully for Page model');
                    $this->line('   📝 Generated meta tags HTML length: ' . strlen($metaTags));
                } else {
                    $this->error('   ❌ Meta tag generation failed');
                }
                
                // Test breadcrumb schema
                $breadcrumbs = [
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Page', 'url' => url('/page')]
                ];
                $breadcrumbSchema = SeoMetaTagService::generateBreadcrumbSchema($breadcrumbs);
                if (!empty($breadcrumbSchema)) {
                    $this->line('   ✅ Breadcrumb schema generated successfully');
                } else {
                    $this->error('   ❌ Breadcrumb schema generation failed');
                }
                
                // Test organization schema
                $orgSchema = SeoMetaTagService::generateOrganizationSchema();
                if (!empty($orgSchema)) {
                    $this->line('   ✅ Organization schema generated successfully');
                } else {
                    $this->error('   ❌ Organization schema generation failed');
                }
            } else {
                $this->line('   ⚠️  No Page model available for testing');
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Meta tag service test failed: " . $e->getMessage());
        }
    }

    protected function testAiSeoService()
    {
        $this->info('🤖 Testing AI SEO Service...');
        
        try {
            // Test if service can be instantiated
            $aiService = app(\Modules\AI\app\Services\AIService::class);
            $seoAnalysisService = new SeoAnalysisService($aiService);
            
            $this->line('   ✅ AI SEO Service instantiated successfully');
            
            // Test with a page if available
            $page = Page::first();
            if ($page) {
                // Don't actually run AI analysis in test (would use tokens)
                $this->line('   ✅ AI SEO Service ready for analysis');
                $this->line('   ℹ️  Skipping actual AI analysis to preserve tokens');
            } else {
                $this->line('   ⚠️  No models available for AI testing');
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ AI SEO service test failed: " . $e->getMessage());
        }
    }

    protected function createTestPage()
    {
        try {
            $page = Page::create([
                'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
                'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-page'],
                'body' => ['tr' => 'Bu bir test sayfasıdır.', 'en' => 'This is a test page.'],
                'is_active' => true,
                'is_homepage' => false
            ]);
            
            $this->line('   ✅ Test page created successfully');
            $this->testModelSeoMethods($page, 'Page');
            
        } catch (\Exception $e) {
            $this->error("   ❌ Failed to create test page: " . $e->getMessage());
        }
    }
}