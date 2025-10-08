<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Unit;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Services\BlogService;
use Modules\Blog\App\Repositories\BlogRepository;
use Modules\Blog\App\Contracts\BlogRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Blog\App\Exceptions\{BlogNotFoundException, BlogProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * BlogService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class BlogServiceTest extends TestCase
{
    use RefreshDatabase;

    private BlogService $service;
    private BlogRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(BlogRepositoryInterface::class);
        $this->service = app(BlogService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_blog_by_id(): void
    {
        $blog = Blog::factory()->create();

        $result = $this->service->getPage($blog->blog_id);

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals($blog->blog_id, $result->blog_id);
    }

    /** @test */
    public function it_throws_exception_when_blog_not_found(): void
    {
        $this->expectException(BlogNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_blog_by_slug(): void
    {
        $blog = Blog::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals($blog->blog_id, $result->blog_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(BlogNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_blogs(): void
    {
        Blog::factory()->active()->count(5)->create();
        Blog::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_blog_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Blog::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_blog(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-blog', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_blog_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Blog created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_blog_successfully(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($blog->blog_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $blog->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_blog(): void
    {
        $this->expectException(BlogNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_blog(): void
    {
        $blog = Blog::factory()->create();

        $result = $this->service->deletePage($blog->blog_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_blog_status(): void
    {
        $blog = Blog::factory()->active()->create();

        $result = $this->service->togglePageStatus($blog->blog_id);

        $this->assertTrue($result->success);
        $this->assertFalse($blog->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_blogs(): void
    {
        $blogs = Blog::factory()->count(5)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_blogs_can_be_deleted(): void
    {
        $homeblog = Blog::factory()->create();

        $result = $this->service->bulkDeletePages([$homeblog->blog_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $blogs = Blog::factory()->active()->count(5)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_blogs(): void
    {
        Blog::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Blog::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'seo' => [
                'tr' => [
                    'meta_title' => 'SEO Başlık',
                    'meta_description' => 'SEO Açıklama',
                ],
                'en' => [
                    'meta_title' => 'SEO Title',
                    'meta_description' => 'SEO Description',
                ]
            ]
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_filters_empty_seo_values(): void
    {
        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'seo' => [
                'tr' => [
                    'meta_title' => 'Başlık',
                    'meta_description' => '', // Boş değer
                    'meta_keywords' => null, // Null değer
                ]
            ]
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_can_prepare_blog_for_form(): void
    {
        $blog = Blog::factory()->create();

        $formData = $this->service->preparePageForForm($blog->blog_id, 'tr');

        $this->assertArrayHasKey('blog', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_blog(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['blog']);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
    }

    /** @test */
    public function it_provides_validation_rules(): void
    {
        $rules = $this->service->getValidationRules(['tr', 'en']);

        $this->assertArrayHasKey('inputs.css', $rules);
        $this->assertArrayHasKey('inputs.js', $rules);
        $this->assertArrayHasKey('multiLangInputs.tr.title', $rules);
        $this->assertArrayHasKey('multiLangInputs.en.title', $rules);
    }

    /** @test */
    public function it_clears_cache(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Blog cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Blog::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Blog::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->blog_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($blog->blog_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $blog->fresh()->getTranslated('slug', 'tr'));
    }
}
