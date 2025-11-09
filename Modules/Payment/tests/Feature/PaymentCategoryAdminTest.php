<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\PaymentCategory;
use App\Models\User;
use Livewire\Livewire;
use Modules\Payment\App\Http\Livewire\Admin\{PaymentCategoryComponent, PaymentCategoryManageComponent};
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PaymentCategory Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class PaymentCategoryAdminTest extends TestCase
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

        $response = $this->get(route('admin.payment.category.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentCategoryComponent::class);
    }

    /** @test */
    public function guest_cannot_view_categories_list(): void
    {
        $response = $this->get(route('admin.payment.category.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_categories_in_list(): void
    {
        $this->actingAs($this->admin);

        $category = PaymentCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);

        Livewire::test(PaymentCategoryComponent::class)
            ->assertSee('Web Geliştirme');
    }

    /** @test */
    public function admin_can_search_categories(): void
    {
        $this->actingAs($this->admin);

        PaymentCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);
        PaymentCategory::factory()->create([
            'title' => ['tr' => 'Mobil Uygulama', 'en' => 'Mobile App']
        ]);

        Livewire::test(PaymentCategoryComponent::class)
            ->set('search', 'Web')
            ->assertSee('Web Geliştirme')
            ->assertDontSee('Mobil Uygulama');
    }

    /** @test */
    public function admin_can_sort_categories(): void
    {
        $this->actingAs($this->admin);

        PaymentCategory::factory()->create(['title' => ['tr' => 'A Kategori']]);
        PaymentCategory::factory()->create(['title' => ['tr' => 'Z Kategori']]);

        Livewire::test(PaymentCategoryComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_page(): void
    {
        $this->actingAs($this->admin);

        PaymentCategory::factory()->count(20)->create();

        Livewire::test(PaymentCategoryComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_category_status(): void
    {
        $this->actingAs($this->admin);

        $category = PaymentCategory::factory()->active()->create();

        Livewire::test(PaymentCategoryComponent::class)
            ->call('toggleActive', $category->category_id)
            ->assertDispatched('toast');

        $this->assertFalse($category->fresh()->is_active);
    }

    /** @test */
    public function admin_can_view_create_category_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.category.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentCategoryManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_category_form(): void
    {
        $this->actingAs($this->admin);

        $category = PaymentCategory::factory()->create();

        $response = $this->get(route('admin.payment.category.manage', $category->category_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentCategoryManageComponent::class);
    }

    /** @test */
    public function admin_can_create_new_category(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentCategoryManageComponent::class)
            ->set('title.tr', 'Yeni Kategori')
            ->set('title.en', 'New Category')
            ->set('description.tr', 'Açıklama')
            ->set('description.en', 'Description')
            ->set('is_active', true)
            ->call('submit')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('payment_categories', [
            'title->tr' => 'Yeni Kategori'
        ]);
    }

    /** @test */
    public function admin_can_update_category(): void
    {
        $this->actingAs($this->admin);

        $category = PaymentCategory::factory()->create([
            'title' => ['tr' => 'Eski Başlık']
        ]);

        Livewire::test(PaymentCategoryManageComponent::class, ['categoryId' => $category->category_id])
            ->set('title.tr', 'Güncellenmiş Başlık')
            ->call('submit')
            ->assertDispatched('toast');

        $this->assertEquals('Güncellenmiş Başlık', $category->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function admin_can_delete_category(): void
    {
        $this->actingAs($this->admin);

        $category = PaymentCategory::factory()->create();

        Livewire::test(PaymentCategoryComponent::class)
            ->call('delete', $category->category_id)
            ->assertDispatched('toast');

        $this->assertSoftDeleted('payment_categories', [
            'category_id' => $category->category_id
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_categories(): void
    {
        $this->actingAs($this->admin);

        $category1 = PaymentCategory::factory()->create();
        $category2 = PaymentCategory::factory()->create();
        $category3 = PaymentCategory::factory()->create();

        Livewire::test(PaymentCategoryComponent::class)
            ->set('selected', [$category1->category_id, $category2->category_id, $category3->category_id])
            ->call('bulkDelete')
            ->assertDispatched('toast');

        $this->assertSoftDeleted('payment_categories', ['category_id' => $category1->category_id]);
        $this->assertSoftDeleted('payment_categories', ['category_id' => $category2->category_id]);
        $this->assertSoftDeleted('payment_categories', ['category_id' => $category3->category_id]);
    }

    /** @test */
    public function admin_can_bulk_toggle_status(): void
    {
        $this->actingAs($this->admin);

        $category1 = PaymentCategory::factory()->inactive()->create();
        $category2 = PaymentCategory::factory()->inactive()->create();

        Livewire::test(PaymentCategoryComponent::class)
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

        Livewire::test(PaymentCategoryManageComponent::class)
            ->set('title.tr', '')
            ->call('submit')
            ->assertHasErrors(['title.tr']);
    }

    /** @test */
    public function category_title_cannot_be_too_short(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentCategoryManageComponent::class)
            ->set('title.tr', 'A')
            ->call('submit')
            ->assertHasErrors(['title.tr']);
    }

    /** @test */
    public function admin_can_filter_by_status(): void
    {
        $this->actingAs($this->admin);

        PaymentCategory::factory()->active()->count(5)->create();
        PaymentCategory::factory()->inactive()->count(3)->create();

        Livewire::test(PaymentCategoryComponent::class)
            ->set('filters.is_active', true)
            ->assertCount('categories', 5);
    }

    /** @test */
    public function admin_can_reorder_categories(): void
    {
        $this->actingAs($this->admin);

        $category1 = PaymentCategory::factory()->create(['sort_order' => 1]);
        $category2 = PaymentCategory::factory()->create(['sort_order' => 2]);

        Livewire::test(PaymentCategoryComponent::class)
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

        $parent = PaymentCategory::factory()->create([
            'title' => ['tr' => 'Ana Kategori'],
            'parent_id' => null
        ]);

        $child = PaymentCategory::factory()->create([
            'title' => ['tr' => 'Alt Kategori'],
            'parent_id' => $parent->category_id
        ]);

        Livewire::test(PaymentCategoryComponent::class)
            ->assertSee('Ana Kategori')
            ->assertSee('Alt Kategori');
    }
}
