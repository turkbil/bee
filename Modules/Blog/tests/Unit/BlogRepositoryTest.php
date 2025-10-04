<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Unit;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Repositories\BlogRepository;
use Modules\Blog\App\Contracts\BlogRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * BlogRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class BlogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private BlogRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BlogRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_blog_by_id(): void
    {
        $blog = Blog::factory()->create();

        $found = $this->repository->findById($blog->blog_id);

        $this->assertNotNull($found);
        $this->assertEquals($blog->blog_id, $found->blog_id);
        $this->assertEquals($blog->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_blog_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_blog_by_id_with_seo(): void
    {
        $blog = Blog::factory()->create();

        $found = $this->repository->findByIdWithSeo($blog->blog_id);

        $this->assertNotNull($found);
        $this->assertEquals($blog->blog_id, $found->blog_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_blog_by_slug(): void
    {
        $blog = Blog::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-blog']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-blog', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($blog->blog_id, $foundTr->blog_id);
        $this->assertEquals($blog->blog_id, $foundEn->blog_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_blogs_by_slug(): void
    {
        Blog::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-blog']
        ]);

        $found = $this->repository->findBySlug('inactive-blog', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_blogs(): void
    {
        Blog::factory()->active()->count(3)->create();
        Blog::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($blog) {
            $this->assertTrue($blog->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_blogs(): void
    {
        Blog::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_blogs_with_filters(): void
    {
        Blog::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Blog']
        ]);
        Blog::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Blog']
        ]);

        $result = $this->repository->getPaginated([
            'search' => 'Laravel',
            'locales' => ['tr', 'en']
        ], 10);

        $this->assertEquals(1, $result->total());
    }

    /** @test */
    public function it_can_filter_by_status(): void
    {
        Blog::factory()->active()->count(3)->create();
        Blog::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_blogs(): void
    {
        $blog1 = Blog::factory()->create(['created_at' => now()->subDays(2)]);
        $blog2 = Blog::factory()->create(['created_at' => now()->subDay()]);
        $blog3 = Blog::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($blog1->blog_id, $ascending->items()[0]->blog_id);
        $this->assertEquals($blog3->blog_id, $descending->items()[0]->blog_id);
    }

    /** @test */
    public function it_can_search_blogs(): void
    {
        Blog::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Blog::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_blog(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Blog'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-blog'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $blog = $this->repository->create($data);

        $this->assertInstanceOf(Blog::class, $blog);
        $this->assertEquals('Yeni Sayfa', $blog->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function it_can_update_blog(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($blog->blog_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $blog->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_blog(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_blog(): void
    {
        $blog = Blog::factory()->create();

        $result = $this->repository->delete($blog->blog_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_blog(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_blog_active_status(): void
    {
        $blog = Blog::factory()->active()->create();
        $this->assertTrue($blog->is_active);

        $this->repository->toggleActive($blog->blog_id);
        $this->assertFalse($blog->fresh()->is_active);

        $this->repository->toggleActive($blog->blog_id);
        $this->assertTrue($blog->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_blogs(): void
    {
        $blogs = Blog::factory()->count(5)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('blogs', ['blog_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $blogs = Blog::factory()->active()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($blogs as $blog) {
            $this->assertFalse($blog->fresh()->is_active);
        }
    }

    /** @test */

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->repository->create($data);
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $blog = Blog::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($blog->blog_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $blog = Blog::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($blog->blog_id);
    }

    /** @test */
    public function it_can_clear_cache_manually(): void
    {
        $this->repository->clearCache();

        $this->assertTrue(true); // Cache temizleme exception fırlatmamalı
    }

    /** @test */
    public function it_eager_loads_seo_setting_in_paginated_results(): void
    {
        Blog::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $blog) {
            $this->assertTrue($blog->relationLoaded('seoSetting'));
        }
    }
}
