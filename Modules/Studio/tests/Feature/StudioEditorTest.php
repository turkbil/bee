<?php

namespace Modules\Studio\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

class StudioEditorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    /**
     * Temel bir kullanıcı oluştur
     */
    protected function createUser($role = 'admin')
    {
        $user = User::factory()->create();
        
        if ($role === 'admin') {
            $user->assignRole('admin');
        } elseif ($role === 'super-admin') {
            $user->assignRole('super-admin');
        }
        
        return $user;
    }
    
    /**
     * Editör sayfasının görüntülenip görüntülenemediğini test et
     */
    public function test_editor_page_can_be_rendered()
    {
        // Spatie PageManagement modülünün yüklü olduğunu varsayar
        if (!class_exists('Modules\Page\App\Models\Page')) {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
        
        // Bir sayfa oluştur
        $page = \Modules\Page\App\Models\Page::factory()->create();
        
        // Admin yetkisiyle oturum aç
        $user = $this->createUser('admin');
        $this->actingAs($user);
        
        // Editör sayfasını çağır
        $response = $this->get(route('admin.studio.editor', ['module' => 'page', 'id' => $page->id]));
        
        // Sayfanın başarıyla yüklendiğini doğrula
        $response->assertStatus(200);
        
        // Editör bileşeninin yüklendiğini kontrol et
        $response->assertSeeLivewire('studio-editor');
    }
    
    /**
     * İçerik kaydetme işlevini test et
     */
    public function test_can_save_content()
    {
        // Spatie PageManagement modülünün yüklü olduğunu varsayar
        if (!class_exists('Modules\Page\App\Models\Page')) {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
        
        // Bir sayfa oluştur
        $page = \Modules\Page\App\Models\Page::factory()->create();
        
        // Admin yetkisiyle oturum aç
        $user = $this->createUser('admin');
        $this->actingAs($user);
        
        // Test edilecek içerikler
        $htmlContent = '<div class="test-content"><h1>Test Başlık</h1><p>Bu bir test içeriğidir.</p></div>';
        $cssContent = '.test-content { color: #333; }';
        $jsContent = 'console.log("Test JavaScript");';
        
        // İçeriği kaydet
        $response = $this->post(route('admin.studio.save', ['module' => 'page', 'id' => $page->id]), [
            'content' => $htmlContent,
            'css' => $cssContent,
            'js' => $jsContent,
            'theme' => 'default',
            'header_template' => 'themes.default.headers.default',
            'footer_template' => 'themes.default.footers.default',
            'settings' => [
                'show_title' => true,
                'show_breadcrumbs' => false
            ]
        ]);
        
        // Başarı yanıtını doğrula
        $response->assertJson(['success' => true]);
        
        // Veritabanındaki içeriği kontrol et
        $updatedPage = \Modules\Page\App\Models\Page::find($page->id);
        $this->assertStringContainsString('test-content', $updatedPage->body);
        $this->assertStringContainsString('.test-content', $updatedPage->css);
        
        // Studio ayarlarının kaydedildiğini kontrol et
        $settings = \Modules\Studio\App\Models\StudioSetting::where('module', 'page')
            ->where('module_id', $page->id)
            ->first();
        
        $this->assertNotNull($settings);
        $this->assertEquals('default', $settings->theme);
    }
    
    /**
     * İçerik doğrulama işlevini test et
     */
    public function test_content_validation()
    {
        // Spatie PageManagement modülünün yüklü olduğunu varsayar
        if (!class_exists('Modules\Page\App\Models\Page')) {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
        
        // Bir sayfa oluştur
        $page = \Modules\Page\App\Models\Page::factory()->create();
        
        // Admin yetkisiyle oturum aç
        $user = $this->createUser('admin');
        $this->actingAs($user);
        
        // İçerik olmadan POST isteği gönder
        $response = $this->post(route('admin.studio.save', ['module' => 'page', 'id' => $page->id]), [
            'css' => '.test { color: red; }',
            'js' => 'console.log("test");'
            // content alanı eksik
        ]);
        
        // Doğrulama hatasını doğrula
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }
    
    /**
     * Widgetları getirme işlevini test et
     */
    public function test_can_get_widgets()
    {
        // Admin yetkisiyle oturum aç
        $user = $this->createUser('admin');
        $this->actingAs($user);
        
        // Widgetları getir
        $response = $this->get(route('admin.studio.api.widgets'));
        
        // Başarılı yanıtı doğrula
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Widget ve kategori verilerinin döndüğünü kontrol et
        $response->assertJsonStructure([
            'success',
            'widgets',
            'categories'
        ]);
    }
    
    /**
     * Temaları getirme işlevini test et
     */
    public function test_can_get_themes()
    {
        // Admin yetkisiyle oturum aç
        $user = $this->createUser('admin');
        $this->actingAs($user);
        
        // Temaları getir
        $response = $this->get(route('admin.studio.api.themes'));
        
        // Başarılı yanıtı doğrula
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Tema ve şablon verilerinin döndüğünü kontrol et
        $response->assertJsonStructure([
            'success',
            'themes',
            'defaultTheme',
            'templates'
        ]);
    }
}