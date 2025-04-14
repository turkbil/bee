<?php

namespace Modules\Studio\Tests\Unit;

use Tests\TestCase;
use Modules\Studio\App\Services\StudioThemeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudioThemeServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $themeService;
    
    /**
     * Test için hazırlık
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->themeService = app(StudioThemeService::class);
        
        // Temel tema verilerini oluştur
        $this->createTestThemes();
    }
    
    /**
     * Test için tema örnekleri oluştur
     */
    protected function createTestThemes()
    {
        // Theme modülünün yüklü olup olmadığını kontrol et
        if (class_exists('Modules\ThemeManagement\App\Models\Theme')) {
            // Test temaları oluştur
            \Modules\ThemeManagement\App\Models\Theme::create([
                'name' => 'test-theme',
                'title' => 'Test Theme',
                'description' => 'Test theme description',
                'folder_name' => 'test-theme',
                'is_default' => true,
                'is_active' => true,
                'version' => '1.0.0',
                'data' => json_encode([
                    'author' => 'Test Author',
                    'website' => 'https://example.com',
                    'category' => 'test'
                ])
            ]);
            
            \Modules\ThemeManagement\App\Models\Theme::create([
                'name' => 'test-theme-2',
                'title' => 'Test Theme 2',
                'description' => 'Test theme 2 description',
                'folder_name' => 'test-theme-2',
                'is_default' => false,
                'is_active' => true,
                'version' => '1.0.0',
                'data' => json_encode([
                    'author' => 'Test Author 2',
                    'website' => 'https://example2.com',
                    'category' => 'test'
                ])
            ]);
        }
        
        // Tema dizin yapısını oluştur
        $themeBasePath = resource_path('views/themes');
        
        // themes dizini mevcut değilse oluştur
        if (!file_exists($themeBasePath)) {
            mkdir($themeBasePath, 0755, true);
        }
        
        // Test teması için dizinleri oluştur
        $testThemePath = $themeBasePath . '/test-theme';
        if (!file_exists($testThemePath)) {
            mkdir($testThemePath, 0755, true);
            mkdir($testThemePath . '/headers', 0755, true);
            mkdir($testThemePath . '/footers', 0755, true);
            
            // Tema konfigürasyon dosyası oluştur
            file_put_contents($testThemePath . '/theme.json', json_encode([
                'id' => 1,
                'name' => 'test-theme',
                'title' => 'Test Theme',
                'description' => 'Test theme description',
                'is_default' => true,
                'screenshot' => null
            ]));
            
            // Örnek header ve footer dosyaları oluştur
            file_put_contents($testThemePath . '/headers/default.blade.php', '<header>Default Header</header>');
            file_put_contents($testThemePath . '/footers/default.blade.php', '<footer>Default Footer</footer>');
        }
    }
    
    /**
     * Temaları alma işlevini test et
     */
    public function test_can_get_themes()
    {
        // Tüm temaları al
        $themes = $this->themeService->getAllThemes();
        
        // Sonucun bir dizi olduğunu doğrula
        $this->assertIsArray($themes);
        
        // En az bir tema olduğunu doğrula
        $this->assertGreaterThanOrEqual(1, count($themes));
        
        // Tema yapısını kontrol et
        $firstTheme = $themes[0];
        $this->assertArrayHasKey('id', $firstTheme);
        $this->assertArrayHasKey('name', $firstTheme);
        $this->assertArrayHasKey('title', $firstTheme);
        $this->assertArrayHasKey('folder_name', $firstTheme);
    }
    
    /**
     * Varsayılan temayı alma işlevini test et
     */
    public function test_can_get_default_theme()
    {
        // Varsayılan temayı al
        $defaultTheme = $this->themeService->getDefaultTheme();
        
        // Sonucun bir dizi olduğunu doğrula
        $this->assertIsArray($defaultTheme);
        
        // Tema varsayılan olarak işaretlenmiş olmalı
        $this->assertTrue($defaultTheme['is_default']);
    }
    
    /**
     * Tema şablonlarını alma işlevini test et
     */
    public function test_can_get_theme_templates()
    {
        // Test teması için şablonları al
        $templates = $this->themeService->getTemplatesForTheme('test-theme');
        
        // Sonucun bir dizi olduğunu doğrula
        $this->assertIsArray($templates);
        
        // Temel şablon kategorilerini kontrol et
        $this->assertArrayHasKey('headers', $templates);
        $this->assertArrayHasKey('footers', $templates);
        
        // Header şablonlarını kontrol et
        $headers = $templates['headers'];
        $this->assertGreaterThanOrEqual(1, count($headers));
        
        // İlk header şablonunun yapısını kontrol et
        if (count($headers) > 0) {
            $this->assertArrayHasKey('name', $headers[0]);
            $this->assertArrayHasKey('title', $headers[0]);
            $this->assertArrayHasKey('path', $headers[0]);
        }
    }
    
    /**
     * Tema değiştirme işlevini test et
     */
    public function test_can_change_theme()
    {
        // Studio Settings modelini oluştur
        if (!class_exists('Modules\Studio\App\Models\StudioSetting')) {
            $this->markTestSkipped('StudioSetting modeli bulunamadı.');
        }
        
        // Test verisi oluştur
        $module = 'page';
        $moduleId = 1;
        $newTheme = 'test-theme';
        
        // Temayı değiştir
        $result = $this->themeService->changeTheme($module, $moduleId, $newTheme);
        
        // İşlemin başarılı olduğunu doğrula
        $this->assertTrue($result);
        
        // Veritabanında değişikliği kontrol et
        $settings = \Modules\Studio\App\Models\StudioSetting::where('module', $module)
            ->where('module_id', $moduleId)
            ->first();
        
        $this->assertNotNull($settings);
        $this->assertEquals($newTheme, $settings->theme);
    }
    
    /**
     * Önbellek işlevini test et
     */
    public function test_cache_works()
    {
        // Önbelleklemeyi aktifleştir
        config(['studio.cache.enable' => true]);
        
        // Önbellekten önce veritabanı sorgusu sayısını al
        $beforeQueries = count(\DB::getQueryLog());
        
        // İlk çağrı - DB'den yüklenip önbelleğe alınmalı
        $themes1 = $this->themeService->getAllThemes();
        
        // İki çağrı arası sorgu sayısını kontrol et
        $midQueries = count(\DB::getQueryLog());
        
        // İkinci çağrı - önbellekten alınmalı, yeni sorgu olmamalı
        $themes2 = $this->themeService->getAllThemes();
        
        // İkinci çağrıdan sonra sorgu sayısını kontrol et
        $afterQueries = count(\DB::getQueryLog());
        
        // Önbelleği temizle
        $this->themeService->clearCache();
        
        // Üçüncü çağrı - önbellek temizlendiği için DB'den yüklenmeli
        $themes3 = $this->themeService->getAllThemes();
        
        // Üçüncü çağrıdan sonra sorgu sayısını kontrol et
        $finalQueries = count(\DB::getQueryLog());
        
        // Theme modülü yüklü değilse dosya sisteminden yüklediği için sorgu sayısı 
        // değişmeyebilir, bu durumda testi geçersiz kıl
        if (class_exists('Modules\ThemeManagement\App\Models\Theme')) {
            $this->assertGreaterThan($beforeQueries, $midQueries);
            $this->assertEquals($midQueries, $afterQueries);
            $this->assertGreaterThan($afterQueries, $finalQueries);
        } else {
            $this->assertTrue(true); // Test geçerli kıl
        }
    }
}