<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Feature;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use App\Models\User;
use Livewire\Livewire;
use Modules\Blog\App\Http\Livewire\Admin\{BlogComponent, BlogManageComponent};

/**
 * Blog Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class BlogAdminTest extends TestCase
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
    public function admin_can_view_blogs_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(BlogComponent::class);
    }

    /** @test */
    public function guest_cannot_view_blogs_list(): void
    {
        $response = $this->get(route('admin.blog.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_blogs_in_list(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog']
        ]);

        Livewire::test(BlogComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_blogs(): void
    {
        $this->actingAs($this->admin);

        Blog::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Blog::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(BlogComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_blogs(): void
    {
        $this->actingAs($this->admin);

        Blog::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Blog::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(BlogComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_blog(): void
    {
        $this->actingAs($this->admin);

        Blog::factory()->count(20)->create();

        Livewire::test(BlogComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_blog_status(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->active()->create();

        Livewire::test(BlogComponent::class)
            ->call('toggleActive', $blog->blog_id)
            ->assertDispatched('toast');

        $this->assertFalse($blog->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_blog_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(BlogManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_blog_form(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create();

        $response = $this->get(route('admin.blog.manage', $blog->blog_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(BlogManageComponent::class);
    }

    /** @test */
    public function admin_can_create_blog(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Blog')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('blogs', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Blog'
        ]);
    }

    /** @test */
    public function admin_can_update_blog(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(BlogManageComponent::class, ['id' => $blog->blog_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $blog->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
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

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', $longTitle)
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */

    /** @test */

    /** @test */

    /** @test */

    /** @test */
    public function admin_can_change_language_in_form(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $blogs = Blog::factory()->count(3)->create();

        Livewire::test(BlogComponent::class)
            ->set('selectedItems', $blogs->pluck('blog_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Blog')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('blogs', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-blog'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('blogs', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function blog_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Blog'],
            'is_active' => true
        ]);

        Livewire::test(BlogManageComponent::class, ['id' => $blog->blog_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Blog')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_blog(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->assertSet('blogId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_blog_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('blog-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BlogManageComponent::class)
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

        Livewire::test(BlogManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $blog = Blog::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $blog->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
