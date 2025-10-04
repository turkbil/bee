<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Unit;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Repositories\PortfolioRepository;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * PortfolioRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class PortfolioRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PortfolioRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PortfolioRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_portfolio_by_id(): void
    {
        $portfolio = Portfolio::factory()->create();

        $found = $this->repository->findById($portfolio->portfolio_id);

        $this->assertNotNull($found);
        $this->assertEquals($portfolio->portfolio_id, $found->portfolio_id);
        $this->assertEquals($portfolio->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_portfolio_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_portfolio_by_id_with_seo(): void
    {
        $portfolio = Portfolio::factory()->create();

        $found = $this->repository->findByIdWithSeo($portfolio->portfolio_id);

        $this->assertNotNull($found);
        $this->assertEquals($portfolio->portfolio_id, $found->portfolio_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_portfolio_by_slug(): void
    {
        $portfolio = Portfolio::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-portfolio']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-portfolio', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($portfolio->portfolio_id, $foundTr->portfolio_id);
        $this->assertEquals($portfolio->portfolio_id, $foundEn->portfolio_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_portfolios_by_slug(): void
    {
        Portfolio::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-portfolio']
        ]);

        $found = $this->repository->findBySlug('inactive-portfolio', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_portfolios(): void
    {
        Portfolio::factory()->active()->count(3)->create();
        Portfolio::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($portfolio) {
            $this->assertTrue($portfolio->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_portfolios(): void
    {
        Portfolio::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_portfolios_with_filters(): void
    {
        Portfolio::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Portfolio']
        ]);
        Portfolio::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Portfolio']
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
        Portfolio::factory()->active()->count(3)->create();
        Portfolio::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_portfolios(): void
    {
        $portfolio1 = Portfolio::factory()->create(['created_at' => now()->subDays(2)]);
        $portfolio2 = Portfolio::factory()->create(['created_at' => now()->subDay()]);
        $portfolio3 = Portfolio::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($portfolio1->portfolio_id, $ascending->items()[0]->portfolio_id);
        $this->assertEquals($portfolio3->portfolio_id, $descending->items()[0]->portfolio_id);
    }

    /** @test */
    public function it_can_search_portfolios(): void
    {
        Portfolio::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Portfolio::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_portfolio(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Portfolio'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-portfolio'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $portfolio = $this->repository->create($data);

        $this->assertInstanceOf(Portfolio::class, $portfolio);
        $this->assertEquals('Yeni Sayfa', $portfolio->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('portfolios', ['portfolio_id' => $portfolio->portfolio_id]);
    }

    /** @test */
    public function it_can_update_portfolio(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($portfolio->portfolio_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $portfolio->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_portfolio(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_portfolio(): void
    {
        $portfolio = Portfolio::factory()->create();

        $result = $this->repository->delete($portfolio->portfolio_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('portfolios', ['portfolio_id' => $portfolio->portfolio_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_portfolio(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_portfolio_active_status(): void
    {
        $portfolio = Portfolio::factory()->active()->create();
        $this->assertTrue($portfolio->is_active);

        $this->repository->toggleActive($portfolio->portfolio_id);
        $this->assertFalse($portfolio->fresh()->is_active);

        $this->repository->toggleActive($portfolio->portfolio_id);
        $this->assertTrue($portfolio->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_portfolios(): void
    {
        $portfolios = Portfolio::factory()->count(5)->create();
        $ids = $portfolios->pluck('portfolio_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('portfolios', ['portfolio_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $portfolios = Portfolio::factory()->active()->count(3)->create();
        $ids = $portfolios->pluck('portfolio_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($portfolios as $portfolio) {
            $this->assertFalse($portfolio->fresh()->is_active);
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
        $portfolio = Portfolio::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($portfolio->portfolio_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $portfolio = Portfolio::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($portfolio->portfolio_id);
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
        Portfolio::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $portfolio) {
            $this->assertTrue($portfolio->relationLoaded('seoSetting'));
        }
    }
}
