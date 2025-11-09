<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use App\Models\User;
use Livewire\Livewire;
use Modules\Payment\App\Http\Livewire\Admin\{PaymentComponent, PaymentManageComponent};

/**
 * Payment Admin Feature Tests
 *
 * Livewire component'lerin admin panelindeki
 * tüm işlevlerini test eder.
 */
class PaymentAdminTest extends TestCase
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
    public function admin_can_view_payments_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentComponent::class);
    }

    /** @test */
    public function guest_cannot_view_payments_list(): void
    {
        $response = $this->get(route('admin.payment.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_see_payments_in_list(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment']
        ]);

        Livewire::test(PaymentComponent::class)
            ->assertSee('Test Sayfası');
    }

    /** @test */
    public function admin_can_search_payments(): void
    {
        $this->actingAs($this->admin);

        Payment::factory()->create([
            'title' => ['tr' => 'Laravel Eğitimi', 'en' => 'Laravel Tutorial']
        ]);
        Payment::factory()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        Livewire::test(PaymentComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Eğitimi')
            ->assertDontSee('PHP Temelleri');
    }

    /** @test */
    public function admin_can_sort_payments(): void
    {
        $this->actingAs($this->admin);

        Payment::factory()->create(['title' => ['tr' => 'A Sayfası']]);
        Payment::factory()->create(['title' => ['tr' => 'Z Sayfası']]);

        Livewire::test(PaymentComponent::class)
            ->call('sortBy', 'title')
            ->assertSet('sortField', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    /** @test */
    public function admin_can_change_per_payment(): void
    {
        $this->actingAs($this->admin);

        Payment::factory()->count(20)->create();

        Livewire::test(PaymentComponent::class)
            ->set('perPage', 25)
            ->assertSet('perPage', 25);
    }

    /** @test */
    public function admin_can_toggle_payment_status(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->active()->create();

        Livewire::test(PaymentComponent::class)
            ->call('toggleActive', $payment->payment_id)
            ->assertDispatched('toast');

        $this->assertFalse($payment->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function admin_can_view_create_payment_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.manage'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentManageComponent::class);
    }

    /** @test */
    public function admin_can_view_edit_payment_form(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->create();

        $response = $this->get(route('admin.payment.manage', $payment->payment_id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PaymentManageComponent::class);
    }

    /** @test */
    public function admin_can_create_payment(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
            ->set('multiLangInputs.en.title', 'New Payment')
            ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
            ->set('multiLangInputs.en.body', '<p>Content</p>')
            ->set('inputs.is_active', true)
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('payments', [
            'title->tr' => 'Yeni Sayfa',
            'title->en' => 'New Payment'
        ]);
    }

    /** @test */
    public function admin_can_update_payment(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        Livewire::test(PaymentManageComponent::class, ['id' => $payment->payment_id])
            ->set('multiLangInputs.tr.title', 'Yeni Başlık')
            ->set('multiLangInputs.en.title', 'New Title')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertEquals('Yeni Başlık', $payment->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function title_is_required_for_main_language(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', '')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.tr.title']);
    }

    /** @test */
    public function title_min_length_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
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

        Livewire::test(PaymentManageComponent::class)
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

        Livewire::test(PaymentManageComponent::class)
            ->assertSet('currentLanguage', 'tr')
            ->call('handleLanguageChange', 'en')
            ->assertSet('currentLanguage', 'en');
    }

    /** @test */
    public function admin_can_select_bulk_items(): void
    {
        $this->actingAs($this->admin);

        $payments = Payment::factory()->count(3)->create();

        Livewire::test(PaymentComponent::class)
            ->set('selectedItems', $payments->pluck('payment_id')->toArray())
            ->assertSet('bulkActionsEnabled', false); // İlk state
    }

    /** @test */
    public function slug_is_generated_automatically(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.en.title', 'Test Payment')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('payments', [
            'slug->tr' => 'test-sayfasi',
            'slug->en' => 'test-payment'
        ]);
    }

    /** @test */
    public function admin_can_provide_custom_slug(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test Sayfası')
            ->set('multiLangInputs.tr.slug', 'ozel-slug')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save');

        $this->assertDatabaseHas('payments', [
            'slug->tr' => 'ozel-slug'
        ]);
    }

    /** @test */
    public function payment_manage_loads_existing_data(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Mevcut Sayfa', 'en' => 'Existing Payment'],
            'is_active' => true
        ]);

        Livewire::test(PaymentManageComponent::class, ['id' => $payment->payment_id])
            ->assertSet('multiLangInputs.tr.title', 'Mevcut Sayfa')
            ->assertSet('multiLangInputs.en.title', 'Existing Payment')
            ->assertSet('inputs.is_active', true);
    }

    /** @test */
    public function form_initializes_empty_inputs_for_new_payment(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->assertSet('paymentId', null)
            ->assertSet('multiLangInputs.tr.title', '')
            ->assertSet('multiLangInputs.en.title', '');
    }

    /** @test */
    public function it_dispatches_payment_saved_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', '<p>Test</p>')
            ->call('save')
            ->assertDispatched('payment-saved');
    }

    /** @test */
    public function it_syncs_tinymce_before_save(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManageComponent::class)
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

        Livewire::test(PaymentManageComponent::class)
            ->set('multiLangInputs.tr.title', 'Test')
            ->set('multiLangInputs.tr.body', $maliciousHtml)
            ->call('save');

        $payment = Payment::where('title->tr', 'Test')->first();

        // Script tag temizlenmeli
        $this->assertStringNotContainsString('<script>', $payment->getTranslated('body', 'tr'));
    }

    /** @test */

    /** @test */
}
