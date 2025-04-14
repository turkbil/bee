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
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->widgetService = app(StudioWidgetService::class);
        
        // Test için önbelleği temizle
        Cache::flush();
    }
    
    /** @test */
    public function can_get_categories()
    {
        $categories = $this->widgetService->getCategories();
        
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
        
        // Varsayılan kategorileri kontrol et
        $this->assertArrayHasKey('widget', $categories);
    }
    
    /** @test */
    public function can_get_widgets()
    {
        // Widget modülü kontrol et
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
            return;
        }
        
        // Test widget oluştur
        $widget = \Modules\WidgetManagement\App\Models\Widget::create([
            'name' => 'Test Widget',
            'slug' => 'test-widget',
            'description' => 'Test widget açıklaması',
            'type' => 'html',
            'content_html' => '<div>Test Widget</div>',
            'content_css' => '.test { color: red; }',
            'content_js' => 'console.log("test");',
            'is_active' => true,
            'data' => ['category' => 'widget']
        ]);
        
        // Widget listesini al
        $widgets = $this->widgetService->getAllWidgets();
        
        // Test widget kontrol et
        $this->assertIsArray($widgets);
        $this->assertNotEmpty($widgets);
        
        // Test widget'ının listede olup olmadığını kontrol et
        $testWidget = null;
        foreach ($widgets as $w) {
            if ($w['id'] == $widget->id) {
                $testWidget = $w;
                break;
            }
        }
        
        $this->assertNotNull($testWidget);
        $this->assertEquals('Test Widget', $testWidget['name']);
        $this->assertEquals('test-widget', $testWidget['slug']);
        $this->assertEquals('<div>Test Widget</div>', $testWidget['content_html']);
    }
    
    /** @test */
    public function can_get_widget_content()
    {
        // Widget modülü kontrol et
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
            return;
        }
        
        // Test widget oluştur
        $widget = \Modules\WidgetManagement\App\Models\Widget::create([
            'name' => 'Test Widget',
            'slug' => 'test-widget',
            'description' => 'Test widget açıklaması',
            'type' => 'html',
            'content_html' => '<div>Test Widget</div>',
            'content_css' => '.test { color: red; }',
            'content_js' => 'console.log("test");',
            'is_active' => true,
            'data' => ['category' => 'widget']
        ]);
        
        // Widget içeriği al
        $content = $this->widgetService->getWidgetContent($widget->id);
        
        // İçerik kontrolü yap
        $this->assertIsArray($content);
        $this->assertEquals('<div>Test Widget</div>', $content['html']);
        $this->assertEquals('.test { color: red; }', $content['css']);
        $this->assertEquals('console.log("test");', $content['js']);
    }
    
    /** @test */
    public function widget_cache_works()
    {
        // Widget modülü kontrol et
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->markTestSkipped('WidgetManagement modülü yüklü değil.');
            return;
        }
        
        // Önbelleklemeyi aktifleştir
        config(['studio.cache.enable' => true]);
        
        // Test widget oluştur
        $widget = \Modules\WidgetManagement\App\Models\Widget::create([
            'name' => 'Test Widget',
            'slug' => 'test-widget',
            'description' => 'Test widget açıklaması',
            'type' => 'html',
            'content_html' => '<div>Test Widget</div>',
            'is_active' => true,
            'data' => ['category' => 'widget']
        ]);
        
        // Widget listesini çağırarak önbellekle
        $widgets = $this->widgetService->getAllWidgets();
        
        // Widgetı güncelle - önbellekte bu değişiklik hemen yansımaz
        $widget->name = 'Updated Test Widget';
        $widget->save();
        
        // Önbellekten widget listesini al
        $cachedWidgets = $this->widgetService->getAllWidgets();
        
        // İlk widget'ı bul
        $testWidget = null;
        foreach ($cachedWidgets as $w) {
            if ($w['id'] == $widget->id) {
                $testWidget = $w;
                break;
            }
        }
        
        // Değişiklik yansımadı çünkü önbellekten geldi
        $this->assertEquals('Test Widget', $testWidget['name']);
        
        // Önbelleği temizle
        $this->widgetService->clearCache();
        
        // Yeniden listele
        $freshWidgets = $this->widgetService->getAllWidgets();
        
        // Güncel widget'ı bul
        $updatedWidget = null;
        foreach ($freshWidgets as $w) {
            if ($w['id'] == $widget->id) {
                $updatedWidget = $w;
                break;
            }
        }
        
        // Şimdi değişiklik yansımalı
        $this->assertEquals('Updated Test Widget', $updatedWidget['name']);
    }
}