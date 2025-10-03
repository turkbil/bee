<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Feature;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use App\Models\User;
use Livewire\Livewire;
use Modules\Page\App\Http\Livewire\Admin\{PageComponent, PageManageComponent};

/**
 * Page Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class PageAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin kullanıcı oluştur
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function admin_can_view_pages_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PageComponent::class);
    }

    /** @test */
    public function guest_cannot_view_pages_list(): void
    {
        $response = $this->get(route('admin.page.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_pages_in_list(): void
    {
        $this->actingAs($this->admin);

        $page = Page::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page']
        ]);

        Livewire::test(PageComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_pages(): void
    {
        $this->actingAs($this->admin);

        Page::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Page::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(PageComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_pages(): void
    {
        $this->actingAs($this->admin);

        Page::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Page::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(PageComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_page(): void
    {
        $this->actingAs($this->admin);

        Page::factory()->count(20)->create();

        Livewire::test(PageComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_page_status(): void
    {
        $this->actingAs($this->admin);

        $page = Page::factory()->active()->create();

        Livewire::test(PageComponent::class)
            ->call('toggleActive', $page->page_id)
            ->assertDispatched('toast');

        $this->assertFalse($page->fresh()->is_active);
    }

    /** @test */
    public function admin_cannot_deactivate_homepage_via_toggle(): void
    {
        $this->actingAs($this->admin);

        $homepage = Page::factory()->homepage()->create();

        Livewire::test(PageComponent::class)
            ->call('toggleActive', $homepage->page_id)
            ->assertDispatched('toast');

        $this->assertTrue($homepage->fresh()->is_active);
    }

    /** @test */
    public function admin_can_view_create_page_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.page.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PageManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_page_form(): void
    {
        $this->actingAs($this->admin);

        $page = Page::factory()->create();

        $response = $this->get(route('admin.page.manage', $page->page_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PageManageComponent::class);
    }

    /** @test */
    public function admin_can_create_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Page')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Page'
        ]);
    }

    /** @test */
    public function admin_can_update_page(): void
    {
        $this->actingAs($this->admin);

        $page = Page::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(PageManageComponent::class, ['id' => $page->page_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $page->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'ab') // 2 karakter
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_max_length_validation(): void
    {
        $this->actingAs($this->admin);

        $longTitle = str_repeat('a', 300);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', $longTitle)
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function admin_can_save_custom_css(): void
    {
        $this->actingAs($this->admin);

        $css = '.custom-class { color: red; }';

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->set('inputs.css', $css)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', [
            'css' => $css
        ]);
    }

    /** @test */
    public function admin_can_save_custom_js(): void
    {
        $this->actingAs($this->admin);

        $js = 'console.log("test");';

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->set('inputs.js', $js)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', [
            'js' => $js
        ]);
    }

    /** @test */
    public function admin_can_set_page_as_homepage(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Anasayfa')
            ->set('multiLangInputs.tr.body', '<p>Anasayfa içeriği</p>')
            ->set('inputs.is_homepage', true)
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', [
            'is_homepage' => true
        ]);
    }

    /** @test */
    public function admin_cannot_deactivate_homepage_on_save(): void
    {
        $this->actingAs($this->admin);

        $homepage = Page::factory()->homepage()->create();

        Livewire::test(PageManageComponent::class, ['id' => $homepage->page_id])
            ->set('inputs.is_active', false)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertTrue($homepage->fresh()->is_active);
    }

    /** @test */
    public function admin_can_change_language_in_form(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $pages = Page::factory()->count(3)->create();

        Livewire::test(PageComponent::class)
            ->set('selectedItems', $pages->pluck('page_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Page')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('pages', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-page'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('pages', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function page_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $page = Page::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Page'],
            'css' => '.test { color: blue; }',
            'is_active' => true
        ]);

        Livewire::test(PageManageComponent::class, ['id' => $page->page_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Page')
            ->assertSet('inputs.css', '.test { color: blue; }')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->assertSet('pageId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_page_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('page-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('sync-tinymce-content');
    }

    /** @test */
    public function malicious_html_is_sanitized(): void
    {
        $this->actingAs($this->admin);

        $maliciousHtml = '<script>alert("XSS")</script><p>Safe content</p>';

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $page = Page::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $page->getTranslated('body', 'tr'));
    }

    /** @test */
    public function malicious_css_is_blocked(): void
    {
        $this->actingAs($this->admin);

        $maliciousCss = 'body { behavior: url(xss.htc); }';

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->set('inputs.css', $maliciousCss)
            ->call('save')
            ->assertDispatched('toast');

        // Validation hatası vermeli
        $this->assertDatabaseMissing('pages', [
            'css' => $maliciousCss
        ]);
    }

    /** @test */
    public function malicious_js_is_blocked(): void
    {
        $this->actingAs($this->admin);

        $maliciousJs = 'eval("malicious code");';

        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->set('inputs.js', $maliciousJs)
            ->call('save')
            ->assertDispatched('toast');

        // Validation hatası vermeli
        $this->assertDatabaseMissing('pages', [
            'js' => $maliciousJs
        ]);
    }
}