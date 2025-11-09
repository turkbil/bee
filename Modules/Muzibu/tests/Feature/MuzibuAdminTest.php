<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Feature;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use App\Models\User;
use Livewire\Livewire;
use Modules\Muzibu\App\Http\Livewire\Admin\{MuzibuComponent, MuzibuManageComponent};

/**
 * Muzibu Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class MuzibuAdminTest extends TestCase
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
    public function admin_can_view_muzibus_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuComponent::class);
    }

    /** @test */
    public function guest_cannot_view_muzibus_list(): void
    {
        $response = $this->get(route('admin.muzibu.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_muzibus_in_list(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu']
        ]);

        Livewire::test(MuzibuComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_muzibus(): void
    {
        $this->actingAs($this->admin);

        Muzibu::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Muzibu::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(MuzibuComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_muzibus(): void
    {
        $this->actingAs($this->admin);

        Muzibu::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Muzibu::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(MuzibuComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_muzibu(): void
    {
        $this->actingAs($this->admin);

        Muzibu::factory()->count(20)->create();

        Livewire::test(MuzibuComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_muzibu_status(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->active()->create();

        Livewire::test(MuzibuComponent::class)
            ->call('toggleActive', $muzibu->muzibu_id)
            ->assertDispatched('toast');

        $this->assertFalse($muzibu->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_muzibu_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_muzibu_form(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create();

        $response = $this->get(route('admin.muzibu.manage', $muzibu->muzibu_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuManageComponent::class);
    }

    /** @test */
    public function admin_can_create_muzibu(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Muzibu')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('muzibus', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Muzibu'
        ]);
    }

    /** @test */
    public function admin_can_update_muzibu(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(MuzibuManageComponent::class, ['id' => $muzibu->muzibu_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $muzibu->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
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

        Livewire::test(MuzibuManageComponent::class)
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

        Livewire::test(MuzibuManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $muzibus = Muzibu::factory()->count(3)->create();

        Livewire::test(MuzibuComponent::class)
            ->set('selectedItems', $muzibus->pluck('muzibu_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Muzibu')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('muzibus', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-muzibu'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('muzibus', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function muzibu_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Muzibu'],
            'is_active' => true
        ]);

        Livewire::test(MuzibuManageComponent::class, ['id' => $muzibu->muzibu_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Muzibu')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_muzibu(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->assertSet('muzibuId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_muzibu_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('muzibu-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuManageComponent::class)
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

        Livewire::test(MuzibuManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $muzibu = Muzibu::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $muzibu->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
