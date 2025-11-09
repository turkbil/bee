<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Feature;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\MuzibuCategory;
use App\Models\User;
use Livewire\Livewire;
use Modules\Muzibu\App\Http\Livewire\Admin\{MuzibuCategoryComponent, MuzibuCategoryManageComponent};
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * MuzibuCategory Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class MuzibuCategoryAdminTest extends TestCase
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
    public function admin_can_view_categories_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.category.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuCategoryComponent::class);
    }

    /** @test */
    public function guest_cannot_view_categories_list(): void
    {
        $response = $this->get(route('admin.muzibu.category.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_categories_in_list(): void
    {
        $this->actingAs($this->admin);

        $category = MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);

        Livewire::test(MuzibuCategoryComponent::class)
            ->assertSee('Web Geliştirme');
    }

    /** @test */
    public function admin_can_search_categories(): void
    {
        $this->actingAs($this->admin);

        MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);
        MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Mobil Uygulama', 'en' => 'Mobile App']
        ]);

        Livewire::test(MuzibuCategoryComponent::class)
            ->set('search', 'Web')
            ->assertSee('Web Geliştirme')
            ->assertDontSee('Mobil Uygulama');
    }

    /** @test */
    public function admin_can_sort_categories(): void
    {
        $this->actingAs($this->admin);

        MuzibuCategory::factory()->create(['title' => ['tr' => 'A Kategori']]);
        MuzibuCategory::factory()->create(['title' => ['tr' => 'Z Kategori']]);

        Livewire::test(MuzibuCategoryComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_page(): void
    {
        $this->actingAs($this->admin);

        MuzibuCategory::factory()->count(20)->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_category_status(): void
    {
        $this->actingAs($this->admin);

        $category = MuzibuCategory::factory()->active()->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->call('toggleActive', $category->category_id)
            ->assertDispatched('toast');

        $this->assertFalse($category->fresh()->is_active);
    }

    /** @test */
    public function admin_can_view_create_category_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.category.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuCategoryManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_category_form(): void
    {
        $this->actingAs($this->admin);

        $category = MuzibuCategory::factory()->create();

        $response = $this->get(route('admin.muzibu.category.manage', $category->category_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MuzibuCategoryManageComponent::class);
    }

    /** @test */
    public function admin_can_create_new_category(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuCategoryManageComponent::class)
            ->set('title.tr', 'Yeni Kategori')
            ->set('title.en', 'New Category')
            ->set('description.tr', 'Açıklama')
            ->set('description.en', 'Description')
            ->set('is_active', true)
            ->call('submit')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('muzibu_categories', [
            'title->tr' => 'Yeni Kategori'
        ]);
    }

    /** @test */
    public function admin_can_update_category(): void
    {
        $this->actingAs($this->admin);

        $category = MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Eski Başlık']
        ]);

        Livewire::test(MuzibuCategoryManageComponent::class, ['categoryId' => $category->category_id])
            ->set('title.tr', 'Güncellenmiş Başlık')
            ->call('submit')
            ->assertDispatched('toast');

        $this->assertEquals('Güncellenmiş Başlık', $category->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function admin_can_delete_category(): void
    {
        $this->actingAs($this->admin);

        $category = MuzibuCategory::factory()->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->call('delete', $category->category_id)
            ->assertDispatched('toast');

        $this->assertSoftDeleted('muzibu_categories', [
            'category_id' => $category->category_id
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_categories(): void
    {
        $this->actingAs($this->admin);

        $category1 = MuzibuCategory::factory()->create();
        $category2 = MuzibuCategory::factory()->create();
        $category3 = MuzibuCategory::factory()->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->set('selected', [$category1->category_id, $category2->category_id, $category3->category_id])
            ->call('bulkDelete')
            ->assertDispatched('toast');

        $this->assertSoftDeleted('muzibu_categories', ['category_id' => $category1->category_id]);
        $this->assertSoftDeleted('muzibu_categories', ['category_id' => $category2->category_id]);
        $this->assertSoftDeleted('muzibu_categories', ['category_id' => $category3->category_id]);
    }

    /** @test */
    public function admin_can_bulk_toggle_status(): void
    {
        $this->actingAs($this->admin);

        $category1 = MuzibuCategory::factory()->inactive()->create();
        $category2 = MuzibuCategory::factory()->inactive()->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->set('selected', [$category1->category_id, $category2->category_id])
            ->call('bulkToggleActive')
            ->assertDispatched('toast');

        $this->assertTrue($category1->fresh()->is_active);
        $this->assertTrue($category2->fresh()->is_active);
    }

    /** @test */
    public function category_title_is_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuCategoryManageComponent::class)
            ->set('title.tr', '')
            ->call('submit')
            ->assertHasErrors(['title.tr']);
    }

    /** @test */
    public function category_title_cannot_be_too_short(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(MuzibuCategoryManageComponent::class)
            ->set('title.tr', 'A')
            ->call('submit')
            ->assertHasErrors(['title.tr']);
    }

    /** @test */
    public function admin_can_filter_by_status(): void
    {
        $this->actingAs($this->admin);

        MuzibuCategory::factory()->active()->count(5)->create();
        MuzibuCategory::factory()->inactive()->count(3)->create();

        Livewire::test(MuzibuCategoryComponent::class)
            ->set('filters.is_active', true)
            ->assertCount('categories', 5);
    }

    /** @test */
    public function admin_can_reorder_categories(): void
    {
        $this->actingAs($this->admin);

        $category1 = MuzibuCategory::factory()->create(['sort_order' => 1]);
        $category2 = MuzibuCategory::factory()->create(['sort_order' => 2]);

        Livewire::test(MuzibuCategoryComponent::class)
            ->call('updateOrder', [
                ['id' => $category2->category_id, 'order' => 1],
                ['id' => $category1->category_id, 'order' => 2]
            ])
            ->assertDispatched('toast');

        $this->assertEquals(1, $category2->fresh()->sort_order);
        $this->assertEquals(2, $category1->fresh()->sort_order);
    }

    /** @test */
    public function admin_sees_hierarchical_category_display(): void
    {
        $this->actingAs($this->admin);

        $parent = MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Ana Kategori'],
            'parent_id' => null
        ]);

        $child = MuzibuCategory::factory()->create([
            'title' => ['tr' => 'Alt Kategori'],
            'parent_id' => $parent->category_id
        ]);

        Livewire::test(MuzibuCategoryComponent::class)
            ->assertSee('Ana Kategori')
            ->assertSee('Alt Kategori');
    }
}
