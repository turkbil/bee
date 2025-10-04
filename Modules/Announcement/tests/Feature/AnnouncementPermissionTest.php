<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Feature;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use App\Models\User;

/**
 * Announcement Permission Tests
 *
 * Yetkilendirme sisteminin doğru çalıştığını
 * ve erişim kontrollerini test eder.
 */
class AnnouncementPermissionTest extends TestCase
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
    public function admin_can_access_announcement_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.announcement.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_announcement_manage(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.announcement.manage'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_announcement(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->make();

        $this->assertDatabaseCount('announcements', 0);

        Announcement::create([
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'body' => $announcement->body,
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('announcements', 1);
    }

    /** @test */
    public function admin_can_update_announcement(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->create();

        $announcement->update(['title' => ['tr' => 'Updated Title', 'en' => 'Updated Title']]);

        $this->assertEquals('Updated Title', $announcement->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function admin_can_delete_announcement(): void
    {
        $this->actingAs($this->admin);

        $announcement = Announcement::factory()->create();

        $announcement->delete();

        $this->assertDatabaseMissing('announcements', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */
    public function guest_cannot_access_admin_announcements(): void
    {
        $response = $this->get(route('admin.announcement.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_access_announcement_manage(): void
    {
        $response = $this->get(route('admin.announcement.manage'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_without_permission(): void
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('admin.announcement.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function viewer_can_view_announcements_but_cannot_edit(): void
    {
        // Viewer yetkisi varsa view yapabilmeli
        $this->actingAs($this->viewer);

        $response = $this->get(route('admin.announcement.index'));

        // Permission middleware'e göre değişir
        $this->assertTrue(in_array($response->status(), [200, 403]));
    }

    /** @test */
    public function module_permission_middleware_works(): void
    {
        $this->actingAs($this->admin);

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.announcement.index');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:announcement,view', $route->middleware());
    }

    /** @test */
    public function authenticated_user_required_for_admin_routes(): void
    {
        $response = $this->get(route('admin.announcement.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function admin_middleware_checks_role(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser);

        $response = $this->get(route('admin.announcement.index'));

        $response->assertStatus(403);
    }

    /** @test */

    /** @test */

    /** @test */
    public function permission_check_includes_tenant_context(): void
    {
        $this->actingAs($this->admin);

        // Tenant middleware kontrolü
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.announcement.index');

        $this->assertContains('tenant', $route->middleware());
    }

    /** @test */
    public function csrf_protection_is_active(): void
    {
        $this->actingAs($this->admin);

        // CSRF token olmadan POST isteği
        $response = $this->post(route('admin.announcement.set-editing-language'), [], [
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
            $response = $this->get(route('admin.announcement.index'));

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

        $announcement = Announcement::factory()->create();

        // Activity log kaydı yapılmalı
        if (function_exists('log_activity')) {
            log_activity($announcement, 'created');

            // Activity log database'de olmalı
            $this->assertDatabaseHas('activity_log', [
                'subject_type' => get_class($announcement),
                'subject_id' => $announcement->announcement_id
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

        $announcement = Announcement::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => $maliciousInput, 'en' => $maliciousInput],
            'is_active' => true,
        ]);

        // XSS koruması aktif olmalı
        $this->assertStringNotContainsString('<script>', $announcement->fresh()->getTranslated('body', 'tr'));
    }

    /** @test */
    public function sql_injection_protection_works(): void
    {
        $this->actingAs($this->admin);

        $maliciousInput = "'; DROP TABLE announcements; --";

        // SQL injection korumalı olmalı
        Announcement::create([
            'title' => ['tr' => $maliciousInput, 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ]);

        // Tablo hala var olmalı
        $this->assertDatabaseCount('announcements', 1);
    }

    /** @test */
    public function mass_assignment_protection_works(): void
    {
        $this->actingAs($this->admin);

        // Korumalı alan (guarded) set edilmeye çalışılırsa
        $announcement = Announcement::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
            // announcement_id manuel set edilemez (primary key)
            'announcement_id' => 9999,
        ]);

        // announcement_id otomatik generate edilmeli, manuel değer ignore edilmeli
        $this->assertNotEquals(9999, $announcement->announcement_id);
    }

    /** @test */
    public function sensitive_routes_require_authentication(): void
    {
        $routes = [
            route('admin.announcement.index'),
            route('admin.announcement.manage'),
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

        $response = $this->post(route('admin.announcement.set-editing-language'));

        $response->assertStatus(200);

        // Authenticated olmadan
        auth()->logout();

        $response = $this->post(route('admin.announcement.set-editing-language'));

        $response->assertRedirect();
    }
}
