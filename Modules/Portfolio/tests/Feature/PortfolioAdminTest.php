<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Feature;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use App\Models\User;
use Livewire\Livewire;
use Modules\Portfolio\App\Http\Livewire\Admin\{PortfolioComponent, PortfolioManageComponent};

/**
 * Portfolio Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class PortfolioAdminTest extends TestCase
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
    public function admin_can_view_portfolios_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.portfolio.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PortfolioComponent::class);
    }

    /** @test */
    public function guest_cannot_view_portfolios_list(): void
    {
        $response = $this->get(route('admin.portfolio.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_portfolios_in_list(): void
    {
        $this->actingAs($this->admin);

        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio']
        ]);

        Livewire::test(PortfolioComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_portfolios(): void
    {
        $this->actingAs($this->admin);

        Portfolio::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Portfolio::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(PortfolioComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_portfolios(): void
    {
        $this->actingAs($this->admin);

        Portfolio::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Portfolio::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(PortfolioComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_portfolio(): void
    {
        $this->actingAs($this->admin);

        Portfolio::factory()->count(20)->create();

        Livewire::test(PortfolioComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_portfolio_status(): void
    {
        $this->actingAs($this->admin);

        $portfolio = Portfolio::factory()->active()->create();

        Livewire::test(PortfolioComponent::class)
            ->call('toggleActive', $portfolio->portfolio_id)
            ->assertDispatched('toast');

        $this->assertFalse($portfolio->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_portfolio_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.portfolio.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PortfolioManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_portfolio_form(): void
    {
        $this->actingAs($this->admin);

        $portfolio = Portfolio::factory()->create();

        $response = $this->get(route('admin.portfolio.manage', $portfolio->portfolio_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PortfolioManageComponent::class);
    }

    /** @test */
    public function admin_can_create_portfolio(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Portfolio')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('portfolios', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Portfolio'
        ]);
    }

    /** @test */
    public function admin_can_update_portfolio(): void
    {
        $this->actingAs($this->admin);

        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(PortfolioManageComponent::class, ['id' => $portfolio->portfolio_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $portfolio->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
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

        Livewire::test(PortfolioManageComponent::class)
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

        Livewire::test(PortfolioManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $portfolios = Portfolio::factory()->count(3)->create();

        Livewire::test(PortfolioComponent::class)
            ->set('selectedItems', $portfolios->pluck('portfolio_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Portfolio')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('portfolios', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-portfolio'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('portfolios', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function portfolio_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Portfolio'],
            'is_active' => true
        ]);

        Livewire::test(PortfolioManageComponent::class, ['id' => $portfolio->portfolio_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Portfolio')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_portfolio(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->assertSet('portfolioId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_portfolio_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('portfolio-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PortfolioManageComponent::class)
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

        Livewire::test(PortfolioManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $portfolio = Portfolio::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $portfolio->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
