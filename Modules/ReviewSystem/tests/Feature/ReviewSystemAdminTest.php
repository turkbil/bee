<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Feature;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use App\Models\User;
use Livewire\Livewire;
use Modules\ReviewSystem\App\Http\Livewire\Admin\{ReviewSystemComponent, ReviewSystemManageComponent};

/**
 * ReviewSystem Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class ReviewSystemAdminTest extends TestCase
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
    public function admin_can_view_reviewsystems_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reviewsystem.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ReviewSystemComponent::class);
    }

    /** @test */
    public function guest_cannot_view_reviewsystems_list(): void
    {
        $response = $this->get(route('admin.reviewsystem.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_reviewsystems_in_list(): void
    {
        $this->actingAs($this->admin);

        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem']
        ]);

        Livewire::test(ReviewSystemComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_reviewsystems(): void
    {
        $this->actingAs($this->admin);

        ReviewSystem::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        ReviewSystem::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(ReviewSystemComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_reviewsystems(): void
    {
        $this->actingAs($this->admin);

        ReviewSystem::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        ReviewSystem::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(ReviewSystemComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_reviewsystem(): void
    {
        $this->actingAs($this->admin);

        ReviewSystem::factory()->count(20)->create();

        Livewire::test(ReviewSystemComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_reviewsystem_status(): void
    {
        $this->actingAs($this->admin);

        $reviewsystem = ReviewSystem::factory()->active()->create();

        Livewire::test(ReviewSystemComponent::class)
            ->call('toggleActive', $reviewsystem->reviewsystem_id)
            ->assertDispatched('toast');

        $this->assertFalse($reviewsystem->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_reviewsystem_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reviewsystem.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ReviewSystemManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_reviewsystem_form(): void
    {
        $this->actingAs($this->admin);

        $reviewsystem = ReviewSystem::factory()->create();

        $response = $this->get(route('admin.reviewsystem.manage', $reviewsystem->reviewsystem_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ReviewSystemManageComponent::class);
    }

    /** @test */
    public function admin_can_create_reviewsystem(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New ReviewSystem')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('reviewsystems', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New ReviewSystem'
        ]);
    }

    /** @test */
    public function admin_can_update_reviewsystem(): void
    {
        $this->actingAs($this->admin);

        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(ReviewSystemManageComponent::class, ['id' => $reviewsystem->reviewsystem_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $reviewsystem->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
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

        Livewire::test(ReviewSystemManageComponent::class)
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

        Livewire::test(ReviewSystemManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $reviewsystems = ReviewSystem::factory()->count(3)->create();

        Livewire::test(ReviewSystemComponent::class)
            ->set('selectedItems', $reviewsystems->pluck('reviewsystem_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test ReviewSystem')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('reviewsystems', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-reviewsystem'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('reviewsystems', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function reviewsystem_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing ReviewSystem'],
            'is_active' => true
        ]);

        Livewire::test(ReviewSystemManageComponent::class, ['id' => $reviewsystem->reviewsystem_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing ReviewSystem')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_reviewsystem(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->assertSet('reviewsystemId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_reviewsystem_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('reviewsystem-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ReviewSystemManageComponent::class)
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

        Livewire::test(ReviewSystemManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $reviewsystem = ReviewSystem::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $reviewsystem->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
