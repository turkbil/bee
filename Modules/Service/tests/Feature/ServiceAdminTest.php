<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Feature;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use App\Models\User;
use Livewire\Livewire;
use Modules\Service\App\Http\Livewire\Admin\{ServiceComponent, ServiceManageComponent};

/**
 * Service Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class ServiceAdminTest extends TestCase
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
    public function admin_can_view_services_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.service.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ServiceComponent::class);
    }

    /** @test */
    public function guest_cannot_view_services_list(): void
    {
        $response = $this->get(route('admin.service.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_services_in_list(): void
    {
        $this->actingAs($this->admin);

        $service = Service::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service']
        ]);

        Livewire::test(ServiceComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_services(): void
    {
        $this->actingAs($this->admin);

        Service::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Service::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(ServiceComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_services(): void
    {
        $this->actingAs($this->admin);

        Service::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Service::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(ServiceComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_service(): void
    {
        $this->actingAs($this->admin);

        Service::factory()->count(20)->create();

        Livewire::test(ServiceComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_service_status(): void
    {
        $this->actingAs($this->admin);

        $service = Service::factory()->active()->create();

        Livewire::test(ServiceComponent::class)
            ->call('toggleActive', $service->service_id)
            ->assertDispatched('toast');

        $this->assertFalse($service->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_service_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.service.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ServiceManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_service_form(): void
    {
        $this->actingAs($this->admin);

        $service = Service::factory()->create();

        $response = $this->get(route('admin.service.manage', $service->service_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ServiceManageComponent::class);
    }

    /** @test */
    public function admin_can_create_service(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Service')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('services', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Service'
        ]);
    }

    /** @test */
    public function admin_can_update_service(): void
    {
        $this->actingAs($this->admin);

        $service = Service::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(ServiceManageComponent::class, ['id' => $service->service_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $service->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
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

        Livewire::test(ServiceManageComponent::class)
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

        Livewire::test(ServiceManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $services = Service::factory()->count(3)->create();

        Livewire::test(ServiceComponent::class)
            ->set('selectedItems', $services->pluck('service_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Service')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('services', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-service'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('services', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function service_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $service = Service::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Service'],
            'is_active' => true
        ]);

        Livewire::test(ServiceManageComponent::class, ['id' => $service->service_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Service')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_service(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->assertSet('serviceId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_service_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('service-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceManageComponent::class)
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

        Livewire::test(ServiceManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $service = Service::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $service->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
