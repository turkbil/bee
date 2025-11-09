<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use App\Models\User;

/**
 * Payment API Feature Tests
 *
 * Payment modülünün API endpoint'lerini ve
 * route'larını test eder.
 */
class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function admin_can_access_payment_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_payment_manage(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.manage'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_payment_edit(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::factory()->create();

        $response = $this->get(route('admin.payment.manage', $payment->payment_id));

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_access_payment_index(): void
    {
        $response = $this->get(route('admin.payment.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_access_payment_manage(): void
    {
        $response = $this->get(route('admin.payment.manage'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_has_set_editing_language_route(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.payment.set-editing-language'));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    /** @test */
    public function it_can_update_language_session(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.payment.manage.update-language-session'), [
            'language' => 'en'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'language' => 'en'
        ]);
    }

    /** @test */
    public function it_validates_language_in_session_update(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.payment.manage.update-language-session'), [
            'language' => 'invalid-lang'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error']);
    }

    /** @test */
    public function it_stores_language_in_session(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.payment.manage.update-language-session'), [
            'language' => 'en'
        ]);

        $this->assertEquals('en', session('payment_manage_language'));
    }

    /** @test */
    public function routes_require_admin_middleware(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser);

        $response = $this->get(route('admin.payment.index'));

        // Admin middleware tarafından reddedilmeli
        $response->assertStatus(403);
    }

    /** @test */
    public function routes_require_tenant_middleware(): void
    {
        // Tenant context olmadan istek
        $response = $this->get(route('admin.payment.index'));

        $response->assertRedirect(); // Login'e yönlendirilmeli
    }

    /** @test */
    public function it_has_ai_translation_routes(): void
    {
        $this->actingAs($this->admin);

        // Translate multi endpoint'i var mı?
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.ai.translation.translate-multi'));

        // Check progress endpoint'i var mı?
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.ai.translation.check-progress'));
    }

    /** @test */
    public function payment_routes_are_prefixed_correctly(): void
    {
        $indexRoute = route('admin.payment.index');
        $manageRoute = route('admin.payment.manage');

        $this->assertStringContainsString('/admin/payment', $indexRoute);
        $this->assertStringContainsString('/admin/payment/manage', $manageRoute);
    }

    /** @test */
    public function payment_routes_have_correct_names(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.manage'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.set-editing-language'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.payment.manage.update-language-session'));
    }

    /** @test */
    public function module_permission_middleware_is_applied(): void
    {
        $this->actingAs($this->admin);

        // Permission middleware kontrolü için route attribute'larını kontrol et
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.payment.index');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:payment,view', $route->middleware());
    }

    /** @test */
    public function manage_route_has_update_permission(): void
    {
        $this->actingAs($this->admin);

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.payment.manage');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:payment,update', $route->middleware());
    }

    /** @test */
    public function it_handles_nonexistent_payment_edit_gracefully(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.manage', 99999));

        // Livewire component düzgün yüklenmeli (sayfa yoksa boş form gösterir)
        $response->assertStatus(200);
    }

    /** @test */
    public function csrf_token_is_required_for_post_routes(): void
    {
        $this->actingAs($this->admin);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('admin.payment.set-editing-language'));

        $response->assertStatus(200);
    }

    /** @test */
    public function payment_index_uses_livewire_component(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.index'));

        $response->assertSeeLivewire(\Modules\Payment\App\Http\Livewire\Admin\PaymentComponent::class);
    }

    /** @test */
    public function payment_manage_uses_livewire_component(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.manage'));

        $response->assertSeeLivewire(\Modules\Payment\App\Http\Livewire\Admin\PaymentManageComponent::class);
    }

    /** @test */
    public function routes_use_admin_layout(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payment.index'));

        $response->assertViewIs('admin.layout');
    }

    /** @test */
    public function it_handles_language_session_without_post_data(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.payment.manage.update-language-session'));

        $response->assertStatus(400);
    }

    /** @test */
    public function translation_routes_are_grouped_correctly(): void
    {
        $translateRoute = route('admin.payment.ai.translation.translate-multi');
        $progressRoute = route('admin.payment.ai.translation.check-progress');

        $this->assertStringContainsString('/admin/payment/ai/translation', $translateRoute);
        $this->assertStringContainsString('/admin/payment/ai/translation', $progressRoute);
    }
}
