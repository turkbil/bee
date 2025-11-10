<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Feature;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use App\Models\User;
use Livewire\Livewire;
use Modules\Favorite\App\Http\Livewire\Admin\{FavoriteComponent, FavoriteManageComponent};

/**
 * Favorite Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class FavoriteAdminTest extends TestCase
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
    public function admin_can_view_favorites_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.favorite.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(FavoriteComponent::class);
    }

    /** @test */
    public function guest_cannot_view_favorites_list(): void
    {
        $response = $this->get(route('admin.favorite.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_favorites_in_list(): void
    {
        $this->actingAs($this->admin);

        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Favorite']
        ]);

        Livewire::test(FavoriteComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_favorites(): void
    {
        $this->actingAs($this->admin);

        Favorite::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Favorite::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(FavoriteComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_favorites(): void
    {
        $this->actingAs($this->admin);

        Favorite::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Favorite::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(FavoriteComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_favorite(): void
    {
        $this->actingAs($this->admin);

        Favorite::factory()->count(20)->create();

        Livewire::test(FavoriteComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_favorite_status(): void
    {
        $this->actingAs($this->admin);

        $favorite = Favorite::factory()->active()->create();

        Livewire::test(FavoriteComponent::class)
            ->call('toggleActive', $favorite->favorite_id)
            ->assertDispatched('toast');

        $this->assertFalse($favorite->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_favorite_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.favorite.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(FavoriteManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_favorite_form(): void
    {
        $this->actingAs($this->admin);

        $favorite = Favorite::factory()->create();

        $response = $this->get(route('admin.favorite.manage', $favorite->favorite_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(FavoriteManageComponent::class);
    }

    /** @test */
    public function admin_can_create_favorite(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Favorite')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('favorites', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Favorite'
        ]);
    }

    /** @test */
    public function admin_can_update_favorite(): void
    {
        $this->actingAs($this->admin);

        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(FavoriteManageComponent::class, ['id' => $favorite->favorite_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $favorite->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
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

        Livewire::test(FavoriteManageComponent::class)
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

        Livewire::test(FavoriteManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $favorites = Favorite::factory()->count(3)->create();

        Livewire::test(FavoriteComponent::class)
            ->set('selectedItems', $favorites->pluck('favorite_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Favorite')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('favorites', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-favorite'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('favorites', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function favorite_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Favorite'],
            'is_active' => true
        ]);

        Livewire::test(FavoriteManageComponent::class, ['id' => $favorite->favorite_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Favorite')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_favorite(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->assertSet('favoriteId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_favorite_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('favorite-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FavoriteManageComponent::class)
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

        Livewire::test(FavoriteManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $favorite = Favorite::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $favorite->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
