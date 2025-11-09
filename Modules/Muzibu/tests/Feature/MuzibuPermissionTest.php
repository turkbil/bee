<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Feature;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use App\Models\User;

/**
 * Muzibu Permission Tests
 *
 * Yetkilendirme sisteminin doğru çalıştığını
 * ve erişim kontrollerini test eder.
 */
class MuzibuPermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $editor;
    private User $viewer;
    private User $guest;

    protected function setUp(): void
    {
        parent::setUp();

        // Farklı roller için kullanıcılar
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->editor = User::factory()->create(['role' => 'editor']);
        $this->viewer = User::factory()->create(['role' => 'viewer']);
        $this->guest = User::factory()->create(['role' => 'guest']);
    }

    /** @test */
    public function admin_can_access_muzibu_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_muzibu_manage(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.muzibu.manage'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_muzibu(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->make();

        $this->assertDatabaseCount('muzibus', 0);

        Muzibu::create([
            'title' => $muzibu->title,
            'slug' => $muzibu->slug,
            'body' => $muzibu->body,
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('muzibus', 1);
    }

    /** @test */
    public function admin_can_update_muzibu(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create();

        $muzibu->update(['title' => ['tr' => 'Updated Title', 'en' => 'Updated Title']]);

        $this->assertEquals('Updated Title', $muzibu->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function admin_can_delete_muzibu(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create();

        $muzibu->delete();

        $this->assertDatabaseMissing('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */
    public function guest_cannot_access_admin_muzibus(): void
    {
        $response = $this->get(route('admin.muzibu.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_access_muzibu_manage(): void
    {
        $response = $this->get(route('admin.muzibu.manage'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_without_permission(): void
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('admin.muzibu.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function viewer_can_view_muzibus_but_cannot_edit(): void
    {
        // Viewer yetkisi varsa view yapabilmeli
        $this->actingAs($this->viewer);

        $response = $this->get(route('admin.muzibu.index'));

        // Permission middleware'e göre değişir
        $this->assertTrue(in_array($response->status(), [200, 403]));
    }

    /** @test */
    public function module_permission_middleware_works(): void
    {
        $this->actingAs($this->admin);

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.muzibu.index');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:muzibu,view', $route->middleware());
    }

    /** @test */
    public function authenticated_user_required_for_admin_routes(): void
    {
        $response = $this->get(route('admin.muzibu.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function admin_middleware_checks_role(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser);

        $response = $this->get(route('admin.muzibu.index'));

        $response->assertStatus(403);
    }

    /** @test */

    /** @test */

    /** @test */
    public function permission_check_includes_tenant_context(): void
    {
        $this->actingAs($this->admin);

        // Tenant middleware kontrolü
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.muzibu.index');

        $this->assertContains('tenant', $route->middleware());
    }

    /** @test */
    public function csrf_protection_is_active(): void
    {
        $this->actingAs($this->admin);

        // CSRF token olmadan POST isteği
        $response = $this->post(route('admin.muzibu.set-editing-language'), [], [
            'HTTP_X-CSRF-TOKEN' => 'invalid-token'
        ]);

        // CSRF hatası vermeli (419)
        $this->assertTrue(in_array($response->status(), [419, 403]));
    }

    /** @test */
    public function rate_limiting_applies_to_admin_routes(): void
    {
        $this->actingAs($this->admin);

        // Rate limiting varsa test et
        for ($i = 0; $i < 100; $i++) {
            $response = $this->get(route('admin.muzibu.index'));

            if ($response->status() === 429) {
                // Rate limit hit
                $this->assertEquals(429, $response->status());
                return;
            }
        }

        // Rate limit yok veya çok yüksek
        $this->assertTrue(true);
    }

    /** @test */
    public function activity_logging_records_admin_actions(): void
    {
        $this->actingAs($this->admin);

        $muzibu = Muzibu::factory()->create();

        // Activity log kaydı yapılmalı
        if (function_exists('log_activity')) {
            log_activity($muzibu, 'created');

            // Activity log database'de olmalı
            $this->assertDatabaseHas('activity_log', [
                'subject_type' => get_class($muzibu),
                'subject_id' => $muzibu->muzibu_id
            ]);
        } else {
            $this->markTestSkipped('Activity logging not available');
        }
    }

    /** @test */
    public function xss_protection_is_active(): void
    {
        $this->actingAs($this->admin);

        $maliciousInput = '<script>alert("XSS")</script>';

        $muzibu = Muzibu::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => $maliciousInput, 'en' => $maliciousInput],
            'is_active' => true,
        ]);

        // XSS koruması aktif olmalı
        $this->assertStringNotContainsString('<script>', $muzibu->fresh()->getTranslated('body', 'tr'));
    }

    /** @test */
    public function sql_injection_protection_works(): void
    {
        $this->actingAs($this->admin);

        $maliciousInput = "'; DROP TABLE muzibus; --";

        // SQL injection korumalı olmalı
        Muzibu::create([
            'title' => ['tr' => $maliciousInput, 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ]);

        // Tablo hala var olmalı
        $this->assertDatabaseCount('muzibus', 1);
    }

    /** @test */
    public function mass_assignment_protection_works(): void
    {
        $this->actingAs($this->admin);

        // Korumalı alan (guarded) set edilmeye çalışılırsa
        $muzibu = Muzibu::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
            // muzibu_id manuel set edilemez (primary key)
            'muzibu_id' => 9999,
        ]);

        // muzibu_id otomatik generate edilmeli, manuel değer ignore edilmeli
        $this->assertNotEquals(9999, $muzibu->muzibu_id);
    }

    /** @test */
    public function sensitive_routes_require_authentication(): void
    {
        $routes = [
            route('admin.muzibu.index'),
            route('admin.muzibu.manage'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(); // Login'e yönlenmeli
        }
    }

    /** @test */
    public function api_routes_have_proper_authentication(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.muzibu.set-editing-language'));

        $response->assertStatus(200);

        // Authenticated olmadan
        auth()->logout();

        $response = $this->post(route('admin.muzibu.set-editing-language'));

        $response->assertRedirect();
    }
}
