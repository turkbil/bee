<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Feature;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use App\Models\User;
use Livewire\Livewire;
use Modules\Announcement\App\Http\Livewire\Admin\{AnnouncementComponent, AnnouncementManageComponent};

/**
 * Announcement Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class AnnouncementAdminTest extends TestCase
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

        $response = $this->get(route('admin.announcement.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(AnnouncementComponent::class);
    }

    /** @test */
    public function guest_cannot_view_pages_list(): void
    {
        $response = $this->get(route('admin.announcement.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_pages_in_list(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement']
        ]);

        Livewire::test(AnnouncementComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_pages(): void
    {
        $this->actingAs($this->admin);

        Announcement::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Announcement::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(AnnouncementComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_pages(): void
    {
        $this->actingAs($this->admin);

        Announcement::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Announcement::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(AnnouncementComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_page(): void
    {
        $this->actingAs($this->admin);

        Announcement::factory()->count(20)->create();

        Livewire::test(AnnouncementComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_page_status(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->active()->create();

        Livewire::test(AnnouncementComponent::class)
            ->call('toggleActive', $announcement->announcement_id)
            ->assertDispatched('toast');

        $this->assertFalse($announcement->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_page_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.announcement.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(AnnouncementManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_page_form(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->create();

        $response = $this->get(route('admin.announcement.manage', $announcement->announcement_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(AnnouncementManageComponent::class);
    }

    /** @test */
    public function admin_can_create_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Announcement')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Announcement'
        ]);
    }

    /** @test */
    public function admin_can_update_page(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(AnnouncementManageComponent::class, ['id' => $announcement->announcement_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $announcement->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
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

        Livewire::test(AnnouncementManageComponent::class)
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

        Livewire::test(AnnouncementManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $pages = Announcement::factory()->count(3)->create();

        Livewire::test(AnnouncementComponent::class)
            ->set('selectedItems', $pages->pluck('announcement_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Announcement')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('pages', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-announcement'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
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

        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Announcement'],
            'is_active' => true
        ]);

        Livewire::test(AnnouncementManageComponent::class, ['id' => $announcement->announcement_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Announcement')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
            ->assertSet('pageId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_page_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('announcement-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AnnouncementManageComponent::class)
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

        Livewire::test(AnnouncementManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $announcement = Announcement::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $announcement->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
