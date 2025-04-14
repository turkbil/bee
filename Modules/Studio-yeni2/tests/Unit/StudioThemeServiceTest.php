<?php

namespace Modules\Studio\Tests\Unit;

use Tests\TestCase;
use Modules\Studio\App\Services\StudioThemeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

class StudioThemeServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $themeService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->themeService = app(StudioThemeService::class);
        
        // Test için önbelleği temizle
        Cache::flush();
    }
    
    /** @test */
    public function can_get_themes()
    {
        $themes = $this->themeService->getAllThemes();
        
        $this->assertIsArray($themes);
        $this->assertNotEmpty($themes);
        
        // Varsayılan tema var mı?
        $defaultExists = false;
        foreach ($themes as $theme) {
            if ($theme['folder_name'] === 'default') {
                $defaultExists = true;
                break;
            }
        }
        
        $this->assertTrue($defaultExists, 'Varsayılan tema bulunamadı');
    }
    
    /** @test */
    public function can_get_default_theme()
    {
        $defaultTheme = $this->themeService->getDefaultTheme();
        
        $this->assertIsArray($defaultTheme);
        $this->assertNotEmpty($defaultTheme);
        $this->assertArrayHasKey('is_default', $defaultTheme);
        $this->assertTrue($defaultTheme['is_default']);
    }
    
    /** @test */
    public function can_get_theme_templates()
    {
        // Tema modülü yüklüyse
        if (class_exists('Modules\ThemeManagement\App\Models\Theme')) {
            // Test tema oluştur
            $theme = \Modules\ThemeManagement\App\Models\Theme::create([
                'name' => 'test-theme',
                'title' => 'Test Tema',
                'description' => 'Test tema açıklaması',
                'folder_name' => 'default', // Mevcut bir klasör kullan
                'is_default' => false,
                'is_active' => true
            ]);
            
            // Tema şablonlarını al
            $templates = $this->themeService->getTemplatesForTheme($theme->folder_name);
            
            // Şablon yapısını kontrol et
            $this->assertIsArray($templates);
            $this->assertArrayHasKey('headers', $templates);
            $this->assertArrayHasKey('footers', $templates);
            $this->assertArrayHasKey('sections', $templates);
        } else {
            // Dosya sistemi tarafından test et
            $templates = $this->themeService->getTemplatesForTheme('default');
            
            // Şablon yapısını kontrol et
            $this->assertIsArray($templates);
            $this->assertArrayHasKey('headers', $templates);
            $this->assertArrayHasKey('footers', $templates);
        }
    }
    
    /** @test */
    public function theme_cache_works()
    {
        // Tema modülü yüklüyse
        if (!class_exists('Modules\ThemeManagement\App\Models\Theme')) {
            $this->markTestSkipped('ThemeManagement modülü yüklü değil.');
            return;
        }
        
        // Önbelleklemeyi aktifleştir
        config(['studio.cache.enable' => true]);
        
        // Test tema oluştur
        $theme = \Modules\ThemeManagement\App\Models\Theme::create([
            'name' => 'test-theme',
            'title' => 'Test Tema',
            'description' => 'Test tema açıklaması',
            'folder_name' => 'default', // Mevcut bir klasör kullan
            'is_default' => false,
            'is_active' => true
        ]);
        
        // Tema listesini çağırarak önbellekle
        $themes = $this->themeService->getAllThemes();
        
        // Temayı güncelle - önbellekte bu değişiklik hemen yansımaz
        $theme->title = 'Updated Test Tema';
        $theme->save();
        
        // Önbellekten tema listesini al
        $cachedThemes = $this->themeService->getAllThemes();
        
        // Test temasını bul
        $testTheme = null;
        foreach ($cachedThemes as $t) {
            if ($t['id'] == $theme->id) {
                $testTheme = $t;
                break;
            }
        }
        
        // Değişiklik yansımadı çünkü önbellekten geldi
        $this->assertEquals('Test Tema', $testTheme['title']);
        
        // Önbelleği temizle
        $this->themeService->clearCache();
        
        // Yeniden listele
        $freshThemes = $this->themeService->getAllThemes();
        
        // Güncel temayı bul
        $updatedTheme = null;
        foreach ($freshThemes as $t) {
            if ($t['id'] == $theme->id) {
                $updatedTheme = $t;
                break;
            }
        }
        
        // Şimdi değişiklik yansımalı
        $this->assertEquals('Updated Test Tema', $updatedTheme['title']);
    }
}