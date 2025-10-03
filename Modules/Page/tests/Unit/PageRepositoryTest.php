<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Unit;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Repositories\PageRepository;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * PageRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class PageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PageRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_page_by_id(): void
    {
        $page = Page::factory()->create();

        $found = $this->repository->findById($page->page_id);

        $this->assertNotNull($found);
        $this->assertEquals($page->page_id, $found->page_id);
        $this->assertEquals($page->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_page_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_page_by_id_with_seo(): void
    {
        $page = Page::factory()->create();

        $found = $this->repository->findByIdWithSeo($page->page_id);

        $this->assertNotNull($found);
        $this->assertEquals($page->page_id, $found->page_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_page_by_slug(): void
    {
        $page = Page::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-page']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-page', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($page->page_id, $foundTr->page_id);
        $this->assertEquals($page->page_id, $foundEn->page_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_pages_by_slug(): void
    {
        Page::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-page']
        ]);

        $found = $this->repository->findBySlug('inactive-page', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_pages(): void
    {
        Page::factory()->active()->count(3)->create();
        Page::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($page) {
            $this->assertTrue($page->is_active);
        });
    }

    /** @test */
    public function it_can_get_homepage(): void
    {
        Page::factory()->count(2)->create();
        $homepage = Page::factory()->homepage()->create();

        $found = $this->repository->getHomepage();

        $this->assertNotNull($found);
        $this->assertEquals($homepage->page_id, $found->page_id);
        $this->assertTrue($found->is_homepage);
    }

    /** @test */
    public function it_returns_null_when_no_homepage_exists(): void
    {
        Page::factory()->count(3)->create(['is_homepage' => false]);

        $found = $this->repository->getHomepage();

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_paginated_pages(): void
    {
        Page::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_pages_with_filters(): void
    {
        Page::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Page']
        ]);
        Page::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Page']
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
        Page::factory()->active()->count(3)->create();
        Page::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_pages(): void
    {
        $page1 = Page::factory()->create(['created_at' => now()->subDays(2)]);
        $page2 = Page::factory()->create(['created_at' => now()->subDay()]);
        $page3 = Page::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($page1->page_id, $ascending->items()[0]->page_id);
        $this->assertEquals($page3->page_id, $descending->items()[0]->page_id);
    }

    /** @test */
    public function it_can_search_pages(): void
    {
        Page::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Page::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_page(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Page'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-page'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
            'is_homepage' => false,
        ];

        $page = $this->repository->create($data);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('Yeni Sayfa', $page->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('pages', ['page_id' => $page->page_id]);
    }

    /** @test */
    public function it_can_update_page(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($page->page_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $page->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_page(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_page(): void
    {
        $page = Page::factory()->create();

        $result = $this->repository->delete($page->page_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('pages', ['page_id' => $page->page_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_page(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_page_active_status(): void
    {
        $page = Page::factory()->active()->create();
        $this->assertTrue($page->is_active);

        $this->repository->toggleActive($page->page_id);
        $this->assertFalse($page->fresh()->is_active);

        $this->repository->toggleActive($page->page_id);
        $this->assertTrue($page->fresh()->is_active);
    }

    /** @test */
    public function it_cannot_deactivate_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $result = $this->repository->toggleActive($homepage->page_id);

        $this->assertFalse($result);
        $this->assertTrue($homepage->fresh()->is_active);
    }

    /** @test */
    public function it_can_bulk_delete_pages(): void
    {
        $pages = Page::factory()->count(5)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('pages', ['page_id' => $id]);
        }
    }

    /** @test */
    public function it_does_not_delete_homepage_in_bulk_delete(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $regularPage = Page::factory()->create();

        $deletedCount = $this->repository->bulkDelete([
            $homepage->page_id,
            $regularPage->page_id
        ]);

        $this->assertEquals(1, $deletedCount);
        $this->assertDatabaseHas('pages', ['page_id' => $homepage->page_id]);
        $this->assertDatabaseMissing('pages', ['page_id' => $regularPage->page_id]);
    }

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $pages = Page::factory()->active()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($pages as $page) {
            $this->assertFalse($page->fresh()->is_active);
        }
    }

    /** @test */
    public function it_excludes_homepage_from_bulk_toggle(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $regularPage = Page::factory()->active()->create();

        $affectedCount = $this->repository->bulkToggleActive([
            $homepage->page_id,
            $regularPage->page_id
        ]);

        $this->assertEquals(1, $affectedCount);
        $this->assertTrue($homepage->fresh()->is_active);
        $this->assertFalse($regularPage->fresh()->is_active);
    }

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
        $page = Page::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($page->page_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $page = Page::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($page->page_id);
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
        Page::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $page) {
            $this->assertTrue($page->relationLoaded('seoSetting'));
        }
    }
}