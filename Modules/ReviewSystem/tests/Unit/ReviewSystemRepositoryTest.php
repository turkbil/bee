<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Unit;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Repositories\ReviewSystemRepository;
use Modules\ReviewSystem\App\Contracts\ReviewSystemRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * ReviewSystemRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class ReviewSystemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ReviewSystemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReviewSystemRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_reviewsystem_by_id(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $found = $this->repository->findById($reviewsystem->reviewsystem_id);

        $this->assertNotNull($found);
        $this->assertEquals($reviewsystem->reviewsystem_id, $found->reviewsystem_id);
        $this->assertEquals($reviewsystem->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_reviewsystem_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_reviewsystem_by_id_with_seo(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $found = $this->repository->findByIdWithSeo($reviewsystem->reviewsystem_id);

        $this->assertNotNull($found);
        $this->assertEquals($reviewsystem->reviewsystem_id, $found->reviewsystem_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_reviewsystem_by_slug(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-reviewsystem']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-reviewsystem', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($reviewsystem->reviewsystem_id, $foundTr->reviewsystem_id);
        $this->assertEquals($reviewsystem->reviewsystem_id, $foundEn->reviewsystem_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_reviewsystems_by_slug(): void
    {
        ReviewSystem::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-reviewsystem']
        ]);

        $found = $this->repository->findBySlug('inactive-reviewsystem', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_reviewsystems(): void
    {
        ReviewSystem::factory()->active()->count(3)->create();
        ReviewSystem::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($reviewsystem) {
            $this->assertTrue($reviewsystem->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_reviewsystems(): void
    {
        ReviewSystem::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_reviewsystems_with_filters(): void
    {
        ReviewSystem::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test ReviewSystem']
        ]);
        ReviewSystem::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP ReviewSystem']
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
        ReviewSystem::factory()->active()->count(3)->create();
        ReviewSystem::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_reviewsystems(): void
    {
        $reviewsystem1 = ReviewSystem::factory()->create(['created_at' => now()->subDays(2)]);
        $reviewsystem2 = ReviewSystem::factory()->create(['created_at' => now()->subDay()]);
        $reviewsystem3 = ReviewSystem::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($reviewsystem1->reviewsystem_id, $ascending->items()[0]->reviewsystem_id);
        $this->assertEquals($reviewsystem3->reviewsystem_id, $descending->items()[0]->reviewsystem_id);
    }

    /** @test */
    public function it_can_search_reviewsystems(): void
    {
        ReviewSystem::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        ReviewSystem::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_reviewsystem(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New ReviewSystem'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-reviewsystem'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $reviewsystem = $this->repository->create($data);

        $this->assertInstanceOf(ReviewSystem::class, $reviewsystem);
        $this->assertEquals('Yeni Sayfa', $reviewsystem->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('reviewsystems', ['reviewsystem_id' => $reviewsystem->reviewsystem_id]);
    }

    /** @test */
    public function it_can_update_reviewsystem(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($reviewsystem->reviewsystem_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $reviewsystem->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_reviewsystem(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_reviewsystem(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $result = $this->repository->delete($reviewsystem->reviewsystem_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('reviewsystems', ['reviewsystem_id' => $reviewsystem->reviewsystem_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_reviewsystem(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_reviewsystem_active_status(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create();
        $this->assertTrue($reviewsystem->is_active);

        $this->repository->toggleActive($reviewsystem->reviewsystem_id);
        $this->assertFalse($reviewsystem->fresh()->is_active);

        $this->repository->toggleActive($reviewsystem->reviewsystem_id);
        $this->assertTrue($reviewsystem->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_reviewsystems(): void
    {
        $reviewsystems = ReviewSystem::factory()->count(5)->create();
        $ids = $reviewsystems->pluck('reviewsystem_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('reviewsystems', ['reviewsystem_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $reviewsystems = ReviewSystem::factory()->active()->count(3)->create();
        $ids = $reviewsystems->pluck('reviewsystem_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($reviewsystems as $reviewsystem) {
            $this->assertFalse($reviewsystem->fresh()->is_active);
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
        $reviewsystem = ReviewSystem::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($reviewsystem->reviewsystem_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($reviewsystem->reviewsystem_id);
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
        ReviewSystem::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $reviewsystem) {
            $this->assertTrue($reviewsystem->relationLoaded('seoSetting'));
        }
    }
}
