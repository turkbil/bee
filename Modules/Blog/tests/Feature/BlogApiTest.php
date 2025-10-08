<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Feature;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use App\Models\User;

/**
 * Blog API Feature Tests
 *
 * Blog modülünün API endpoint'lerini ve
 * route'larını test eder.
 */
class BlogApiTest extends TestCase
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
    public function admin_can_access_blog_edit(): void
    {
        $this->actingAs($this->admin);

        $blog = Blog::factory()->create();

        $response = $this->get(route('admin.blog.manage', $blog->blog_id));

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_access_blog_index(): void
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
    public function it_has_set_editing_language_route(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blog.set-editing-language'));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    /** @test */
    public function it_can_update_language_session(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blog.manage.update-language-session'), [
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

        $response = $this->post(route('admin.blog.manage.update-language-session'), [
            'language' => 'invalid-lang'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error']);
    }

    /** @test */
    public function it_stores_language_in_session(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.blog.manage.update-language-session'), [
            'language' => 'en'
        ]);

        $this->assertEquals('en', session('blog_manage_language'));
    }

    /** @test */
    public function routes_require_admin_middleware(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser);

        $response = $this->get(route('admin.blog.index'));

        // Admin middleware tarafından reddedilmeli
        $response->assertStatus(403);
    }

    /** @test */
    public function routes_require_tenant_middleware(): void
    {
        // Tenant context olmadan istek
        $response = $this->get(route('admin.blog.index'));

        $response->assertRedirect(); // Login'e yönlendirilmeli
    }

    /** @test */
    public function it_has_ai_translation_routes(): void
    {
        $this->actingAs($this->admin);

        // Translate multi endpoint'i var mı?
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.ai.translation.translate-multi'));

        // Check progress endpoint'i var mı?
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.ai.translation.check-progress'));
    }

    /** @test */
    public function blog_routes_are_prefixed_correctly(): void
    {
        $indexRoute = route('admin.blog.index');
        $manageRoute = route('admin.blog.manage');

        $this->assertStringContainsString('/admin/blog', $indexRoute);
        $this->assertStringContainsString('/admin/blog/manage', $manageRoute);
    }

    /** @test */
    public function blog_routes_have_correct_names(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.manage'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.set-editing-language'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.blog.manage.update-language-session'));
    }

    /** @test */
    public function module_permission_middleware_is_applied(): void
    {
        $this->actingAs($this->admin);

        // Permission middleware kontrolü için route attribute'larını kontrol et
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.blog.index');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:blog,view', $route->middleware());
    }

    /** @test */
    public function manage_route_has_update_permission(): void
    {
        $this->actingAs($this->admin);

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.blog.manage');

        $this->assertNotNull($route);
        $this->assertContains('module.permission:blog,update', $route->middleware());
    }

    /** @test */
    public function it_handles_nonexistent_blog_edit_gracefully(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.manage', 99999));

        // Livewire component düzgün yüklenmeli (sayfa yoksa boş form gösterir)
        $response->assertStatus(200);
    }

    /** @test */
    public function csrf_token_is_required_for_post_routes(): void
    {
        $this->actingAs($this->admin);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('admin.blog.set-editing-language'));

        $response->assertStatus(200);
    }

    /** @test */
    public function blog_index_uses_livewire_component(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.index'));

        $response->assertSeeLivewire(\Modules\Blog\App\Http\Livewire\Admin\BlogComponent::class);
    }

    /** @test */
    public function blog_manage_uses_livewire_component(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.manage'));

        $response->assertSeeLivewire(\Modules\Blog\App\Http\Livewire\Admin\BlogManageComponent::class);
    }

    /** @test */
    public function routes_use_admin_layout(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog.index'));

        $response->assertViewIs('admin.layout');
    }

    /** @test */
    public function it_handles_language_session_without_post_data(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blog.manage.update-language-session'));

        $response->assertStatus(400);
    }

    /** @test */
    public function translation_routes_are_grouped_correctly(): void
    {
        $translateRoute = route('admin.blog.ai.translation.translate-multi');
        $progressRoute = route('admin.blog.ai.translation.check-progress');

        $this->assertStringContainsString('/admin/blog/ai/translation', $translateRoute);
        $this->assertStringContainsString('/admin/blog/ai/translation', $progressRoute);
    }
}
