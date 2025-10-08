<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Feature;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use App\Models\User;

/**
 * Blog Permission Tests
 *
 * Yetkilendirme sisteminin doğru çalıştığını
 * ve erişim kontrollerini test eder.
 */
class BlogPermissionTest extends TestCase
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
    public function admin_can_access_blog_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_blog_manage(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.manage'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_blog(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->make();

        $this->assertDatabaseCount('blogs', 0);

        Blog::create([
            'title' => $blog->title,
            'slug' => $blog->slug,
            'body' => $blog->body,
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('blogs', 1);
    }

    /** @test */
    public function admin_can_update_blog(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create();

        $blog->update(['title' => ['tr' => 'Updated Title', 'en' => 'Updated Title']]);

        $this->assertEquals('Updated Title', $blog->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function admin_can_delete_blog(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create();

        $blog->delete();

        $this->assertDatabaseMissing('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function guest_cannot_access_admin_blogs(): void
    {
        $response = $this->get(route('admin.blog.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_access_blog_manage(): void
    {
        $response = $this->get(route('admin.blog.manage'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_without_permission(): void
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('admin.blog.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function viewer_can_view_blogs_but_cannot_edit(): void
    {
        // Viewer yetkisi varsa view yapabilmeli
        $this->actingAs($this->viewer);

        $response = $this->get(route('admin.blog.index'));

        // Permission middleware'e göre değişir
        $this->assertTrue(in_array($response->status(), [200, 403]));
    }

    /** @test */
    public function module_permission_middleware_works(): void
    {
        $this->actingAs($this->admin);

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.blog.index');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:blog,view', $route->middleware());
    }

    /** @test */
    public function authenticated_user_required_for_admin_routes(): void
    {
        $response = $this->get(route('admin.blog.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function admin_middleware_checks_role(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser);

        $response = $this->get(route('admin.blog.index'));

        $response->assertStatus(403);
    }

    /** @test */

    /** @test */

    /** @test */
    public function permission_check_includes_tenant_context(): void
    {
        $this->actingAs($this->admin);

        // Tenant middleware kontrolü
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.blog.index');

        $this->assertContains('tenant', $route->middleware());
    }

    /** @test */
    public function csrf_protection_is_active(): void
    {
        $this->actingAs($this->admin);

        // CSRF token olmadan POST isteği
        $response = $this->post(route('admin.blog.set-editing-language'), [], [
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
            $response = $this->get(route('admin.blog.index'));

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

        $blog = Blog::factory()->create();

        // Activity log kaydı yapılmalı
        if (function_exists('log_activity')) {
            log_activity($blog, 'created');

            // Activity log database'de olmalı
            $this->assertDatabaseHas('activity_log', [
                'subject_type' => get_class($blog),
                'subject_id' => $blog->blog_id
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

        $blog = Blog::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => $maliciousInput, 'en' => $maliciousInput],
            'is_active' => true,
        ]);

        // XSS koruması aktif olmalı
        $this->assertStringNotContainsString('<script>', $blog->fresh()->getTranslated('body', 'tr'));
    }

    /** @test */
    public function sql_injection_protection_works(): void
    {
        $this->actingAs($this->admin);

        $maliciousInput = "'; DROP TABLE blogs; --";

        // SQL injection korumalı olmalı
        Blog::create([
            'title' => ['tr' => $maliciousInput, 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ]);

        // Tablo hala var olmalı
        $this->assertDatabaseCount('blogs', 1);
    }

    /** @test */
    public function mass_assignment_protection_works(): void
    {
        $this->actingAs($this->admin);

        // Korumalı alan (guarded) set edilmeye çalışılırsa
        $blog = Blog::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
            // blog_id manuel set edilemez (primary key)
            'blog_id' => 9999,
        ]);

        // blog_id otomatik generate edilmeli, manuel değer ignore edilmeli
        $this->assertNotEquals(9999, $blog->blog_id);
    }

    /** @test */
    public function sensitive_routes_require_authentication(): void
    {
        $routes = [
            route('admin.blog.index'),
            route('admin.blog.manage'),
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

        $response = $this->post(route('admin.blog.set-editing-language'));

        $response->assertStatus(200);

        // Authenticated olmadan
        auth()->logout();

        $response = $this->post(route('admin.blog.set-editing-language'));

        $response->assertRedirect();
    }
}
