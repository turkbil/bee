<?php

namespace Modules\Studio\Tests\Unit;

use Tests\TestCase;
use Modules\Studio\App\Services\StudioWidgetService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudioWidgetServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $widgetService;
    
    /**
     * Test için hazırlık
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Widget modülünün yüklü olduğunu kontrol et
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        $this->widgetService = app(StudioWidgetService::class);
        
        // Temel widget verileri
        $this->createTestWidgets();
    }
    
    /**
     * Test için widget örnekleri oluştur
     */
    protected function createTestWidgets()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return;
        }
        
        \Modules\WidgetManagement\App\Models\Widget::create([
            'name' => 'Test Widget 1',
            'slug' => 'test-widget-1',
            'description' => 'Test widget description 1',
            'type' => 'content',
            'is_active' => true,
            'content_html' => '<div class="test-widget">Test Widget 1 Content</div>',
            'content_css' => '.test-widget { color: red; }',
            'content_js' => 'console.log("Test Widget 1");',
            'data' => json_encode(['category' => 'test'])
        ]);
        
        \Modules\WidgetManagement\App\Models\Widget::create([
            'name' => 'Test Widget 2',
            'slug' => 'test-widget-2',
            'description' => 'Test widget description 2',
            'type' => 'content',
            'is_active' => true,
            'content_html' => '<div class="test-widget">Test Widget 2 Content</div>',
            'content_css' => '.test-widget { color: blue; }',
            'content_js' => 'console.log("Test Widget 2");',
            'data' => json_encode(['category' => 'test'])
        ]);
    }
    
    /**
     * Widgetları alma işlevini test et
     */
    public function test_can_get_widgets()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        // Widgetları al
        $widgets = $this->widgetService->getAllWidgets();
        
        // En az 2 widget döndüğünü doğrula
        $this->assertIsArray($widgets);
        $this->assertGreaterThanOrEqual(2, count($widgets));
        
        // Widget yapısını kontrol et
        $firstWidget = $widgets[0];
        $this->assertArrayHasKey('id', $firstWidget);
        $this->assertArrayHasKey('name', $firstWidget);
        $this->assertArrayHasKey('slug', $firstWidget);
        $this->assertArrayHasKey('content_html', $firstWidget);
    }
    
    /**
     * Widget içeriğini alma işlevini test et
     */
    public function test_can_get_widget_content()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        // Tüm widgetları al
        $widgets = $this->widgetService->getAllWidgets();
        
        // İlk widget ID'sini al
        $widgetId = $widgets[0]['id'];
        
        // Widget içeriğini al
        $content = $this->widgetService->getWidgetContent($widgetId);
        
        // İçeriğin doğru yapıda olduğunu kontrol et
        $this->assertIsArray($content);
        $this->assertArrayHasKey('html', $content);
        $this->assertArrayHasKey('css', $content);
        $this->assertArrayHasKey('js', $content);
    }
    
    /**
     * Widget önbellek işlevini test et
     */
    public function test_widget_cache_works()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        // Önbelleklemeyi aktifleştir
        config(['studio.cache.enable' => true]);
        
        // Önbellekten önce veritabanı sorgusu sayısını al
        $beforeQueries = count(\DB::getQueryLog());
        
        // İlk çağrı - DB'den yüklenip önbelleğe alınmalı
        $widgets1 = $this->widgetService->getAllWidgets();
        
        // İki çağrı arası sorgu sayısını kontrol et
        $midQueries = count(\DB::getQueryLog());
        $this->assertGreaterThan($beforeQueries, $midQueries);
        
        // İkinci çağrı - önbellekten alınmalı, yeni sorgu olmamalı
        $widgets2 = $this->widgetService->getAllWidgets();
        
        // İkinci çağrıdan sonra sorgu sayısını kontrol et
        $afterQueries = count(\DB::getQueryLog());
        $this->assertEquals($midQueries, $afterQueries);
        
        // Önbelleği temizle
        $this->widgetService->clearCache();
        
        // Üçüncü çağrı - önbellek temizlendiği için DB'den yüklenmeli
        $widgets3 = $this->widgetService->getAllWidgets();
        
        // Üçüncü çağrıdan sonra sorgu sayısını kontrol et
        $finalQueries = count(\DB::getQueryLog());
        $this->assertGreaterThan($afterQueries, $finalQueries);
    }
    
    /**
     * Widget kaydetme işlevini test et
     */
    public function test_can_save_widget()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        // Tüm widgetları al
        $widgets = $this->widgetService->getAllWidgets();
        
        // İlk widget ID'sini al
        $widgetId = $widgets[0]['id'];
        
        // Yeni içerik verisi
        $newData = [
            'html' => '<div class="updated-widget">Updated Widget Content</div>',
            'css' => '.updated-widget { color: green; }',
            'js' => 'console.log("Updated Widget");',
            'name' => 'Updated Widget Name',
            'description' => 'Updated widget description',
            'category' => 'updated-category'
        ];
        
        // Widget içeriğini güncelle
        $result = $this->widgetService->saveWidget($widgetId, $newData);
        
        // Kaydetme işleminin başarılı olduğunu doğrula
        $this->assertTrue($result);
        
        // Değişiklikleri doğrula
        $updatedWidget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
        $this->assertEquals('Updated Widget Name', $updatedWidget->name);
        $this->assertEquals('<div class="updated-widget">Updated Widget Content</div>', $updatedWidget->content_html);
        $this->assertEquals('.updated-widget { color: green; }', $updatedWidget->content_css);
    }
    
    /**
     * Kategori getirme işlevini test et
     */
    public function test_can_get_categories()
    {
        // Kategorileri al
        $categories = $this->widgetService->getCategories();
        
        // Sonucun bir dizi olduğunu doğrula
        $this->assertIsArray($categories);
        
        // En az bir kategori olduğunu doğrula
        $this->assertGreaterThanOrEqual(1, count($categories));
    }
    
    /**
     * Widgetları blok olarak getirme işlevini test et
     */
    public function test_can_get_widgets_as_blocks()
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
        }
        
        // Widgetları blok olarak al
        $blocks = $this->widgetService->getWidgetsAsBlocks();
        
        // Sonucun bir dizi olduğunu doğrula
        $this->assertIsArray($blocks);
        
        // En az bir blok olduğunu doğrula
        $this->assertGreaterThanOrEqual(2, count($blocks));
        
        // Blok yapısını kontrol et
        $firstBlock = $blocks[0];
        $this->assertArrayHasKey('id', $firstBlock);
        $this->assertArrayHasKey('label', $firstBlock);
        $this->assertArrayHasKey('category', $firstBlock);
        $this->assertArrayHasKey('content', $firstBlock);
    }
}