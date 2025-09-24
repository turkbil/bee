<?php

namespace Tests\Unit\Page;

use Tests\TestCase;
use Modules\Page\App\Repositories\PageRepository;
use Modules\Page\App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PageRepository $pageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageRepository = app(PageRepository::class);
    }

    /** @test */
    public function it_can_find_page_by_id()
    {
        $page = Page::factory()->create();

        $foundPage = $this->pageRepository->findById($page->page_id);

        $this->assertInstanceOf(Page::class, $foundPage);
        $this->assertEquals($page->page_id, $foundPage->page_id);
    }

    /** @test */
    public function it_returns_null_for_non_existent_page()
    {
        $foundPage = $this->pageRepository->findById(999);

        $this->assertNull($foundPage);
    }

    /** @test */
    public function it_can_get_active_pages()
    {
        Page::factory()->create(['is_active' => true]);
        Page::factory()->create(['is_active' => false]);
        Page::factory()->create(['is_active' => true]);

        $activePages = $this->pageRepository->getActive();

        $this->assertInstanceOf(Collection::class, $activePages);
        $this->assertCount(2, $activePages);
        $this->assertTrue($activePages->every(fn($page) => $page->is_active));
    }

    /** @test */
    public function it_can_get_paginated_pages()
    {
        Page::factory()->count(15)->create();

        $paginatedPages = $this->pageRepository->getPaginated([], 10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedPages);
        $this->assertCount(10, $paginatedPages->items());
        $this->assertEquals(15, $paginatedPages->total());
    }

    /** @test */
    public function it_can_search_pages_by_title()
    {
        Page::factory()->create(['title' => json_encode(['tr' => 'Laravel Test', 'en' => 'Laravel Test'])]);
        Page::factory()->create(['title' => json_encode(['tr' => 'PHP Tutorial', 'en' => 'PHP Tutorial'])]);

        $filters = [
            'search' => 'Laravel',
            'locales' => ['tr', 'en']
        ];

        $searchResults = $this->pageRepository->getPaginated($filters, 10);

        $this->assertCount(1, $searchResults->items());
    }

    /** @test */
    public function it_applies_eager_loading_with_seo_setting()
    {
        Page::factory()->create();

        $paginatedPages = $this->pageRepository->getPaginated([], 5);

        // Check that seoSetting relationship is loaded
        $firstPage = $paginatedPages->items()[0];
        $this->assertTrue($firstPage->relationLoaded('seoSetting'));
    }
}