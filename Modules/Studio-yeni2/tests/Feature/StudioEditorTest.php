<?php

namespace Modules\Studio\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Studio\App\Http\Livewire\StudioEditor;
use Livewire\Livewire;
use App\Models\User;

class StudioEditorTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Test kullanıcısı oluştur
        $user = User::factory()->create();
        
        // Kullanıcıya rol ve yetki ata
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
            $permission = \Spatie\Permission\Models\Permission::create(['name' => 'view-page']);
            $role->givePermissionTo($permission);
            $user->assignRole($role);
        }
        
        $this->actingAs($user);
    }
    
    /** @test */
    public function editor_page_can_be_rendered()
    {
        // Page modülü yüklüyse
        if (class_exists('\Modules\Page\App\Models\Page')) {
            // Test sayfası oluştur
            $page = \Modules\Page\App\Models\Page::create([
                'title' => 'Test Sayfası',
                'slug' => 'test-sayfasi',
                'body' => '<p>Test içeriği</p>',
                'status' => 'published'
            ]);
            
            // Sayfaya erişim testi
            $response = $this->get(route('admin.studio.editor', ['module' => 'page', 'id' => $page->id]));
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
    }
    
    /** @test */
    public function livewire_component_can_be_rendered()
    {
        // Page modülü yüklüyse
        if (class_exists('\Modules\Page\App\Models\Page')) {
            // Test sayfası oluştur
            $page = \Modules\Page\App\Models\Page::create([
                'title' => 'Test Sayfası',
                'slug' => 'test-sayfasi',
                'body' => '<p>Test içeriği</p>',
                'status' => 'published'
            ]);
            
            // Livewire bileşeni test et
            Livewire::test(StudioEditor::class, ['module' => 'page', 'id' => $page->id])
                ->assertSet('module', 'page')
                ->assertSet('moduleId', $page->id)
                ->assertSet('content', '<p>Test içeriği</p>')
                ->assertViewIs('studio::livewire.studio-editor');
        } else {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
    }
    
    /** @test */
    public function can_save_content()
    {
        // Page modülü yüklüyse
        if (class_exists('\Modules\Page\App\Models\Page')) {
            // Test sayfası oluştur
            $page = \Modules\Page\App\Models\Page::create([
                'title' => 'Test Sayfası',
                'slug' => 'test-sayfasi',
                'body' => '<p>Test içeriği</p>',
                'status' => 'published'
            ]);
            
            // İçerik kaydetme testi
            $newContent = '<p>Güncellenmiş test içeriği</p>';
            $newCss = 'body { color: red; }';
            $newJs = 'console.log("test");';
            
            Livewire::test(StudioEditor::class, ['module' => 'page', 'id' => $page->id])
                ->set('content', $newContent)
                ->set('css', $newCss)
                ->set('js', $newJs)
                ->call('save');
            
            // Kaydedilen içeriği kontrol et
            $page->refresh();
            $this->assertEquals($newContent, $page->body);
            $this->assertEquals($newCss, $page->css);
            $this->assertEquals($newJs, $page->js);
        } else {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
    }
    
    /** @test */
    public function content_validation_works()
    {
        // Page modülü yüklüyse
        if (class_exists('\Modules\Page\App\Models\Page')) {
            // Test sayfası oluştur
            $page = \Modules\Page\App\Models\Page::create([
                'title' => 'Test Sayfası',
                'slug' => 'test-sayfasi',
                'body' => '<p>Test içeriği</p>',
                'status' => 'published'
            ]);
            
            // Tehlikeli içerikli test
            $maliciousContent = '<script>alert("XSS");</script><p>Test</p>';
            $expectedContent = '<p>Test</p>'; // Script etiketi temizlenmiş olmalı
            
            Livewire::test(StudioEditor::class, ['module' => 'page', 'id' => $page->id])
                ->set('content', $maliciousContent)
                ->call('save');
            
            // Kaydedilen içeriği kontrol et - script etiketi temizlenmiş olmalı
            $page->refresh();
            $this->assertEquals($expectedContent, $page->body);
        } else {
            $this->markTestSkipped('Page modülü yüklü değil.');
        }
    }
}