<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Feature;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;
use Modules\Shop\App\Http\Livewire\Admin\{ShopComponent, ShopManageComponent};

/**
 * Shop Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class ShopAdminTest extends TestCase
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
    public function admin_can_view_shops_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.shop.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ShopComponent::class);
    }

    /** @test */
    public function guest_cannot_view_shops_list(): void
    {
        $response = $this->get(route('admin.shop.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_shops_in_list(): void
    {
        $this->actingAs($this->admin);

        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop']
        ]);

        Livewire::test(ShopComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_shops(): void
    {
        $this->actingAs($this->admin);

        Shop::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Shop::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(ShopComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_shops(): void
    {
        $this->actingAs($this->admin);

        Shop::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Shop::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(ShopComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_shop(): void
    {
        $this->actingAs($this->admin);

        Shop::factory()->count(20)->create();

        Livewire::test(ShopComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_shop_status(): void
    {
        $this->actingAs($this->admin);

        $shop = Shop::factory()->active()->create();

        Livewire::test(ShopComponent::class)
            ->call('toggleActive', $shop->shop_id)
            ->assertDispatched('toast');

        $this->assertFalse($shop->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_shop_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.shop.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ShopManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_shop_form(): void
    {
        $this->actingAs($this->admin);

        $shop = Shop::factory()->create();

        $response = $this->get(route('admin.shop.manage', $shop->shop_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ShopManageComponent::class);
    }

    /** @test */
    public function admin_can_create_shop(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Shop')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('shops', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Shop'
        ]);
    }

    /** @test */
    public function admin_can_update_shop(): void
    {
        $this->actingAs($this->admin);

        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(ShopManageComponent::class, ['id' => $shop->shop_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $shop->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
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

        Livewire::test(ShopManageComponent::class)
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

        Livewire::test(ShopManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $shops = Shop::factory()->count(3)->create();

        Livewire::test(ShopComponent::class)
            ->set('selectedItems', $shops->pluck('shop_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Shop')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('shops', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-shop'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('shops', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function shop_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Shop'],
            'is_active' => true
        ]);

        Livewire::test(ShopManageComponent::class, ['id' => $shop->shop_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Shop')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_shop(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->assertSet('shopId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_shop_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('shop-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ShopManageComponent::class)
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

        Livewire::test(ShopManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $shop = Shop::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $shop->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
