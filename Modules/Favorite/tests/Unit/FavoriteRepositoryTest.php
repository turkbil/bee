<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Unit;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Repositories\FavoriteRepository;
use Modules\Favorite\App\Contracts\FavoriteRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * FavoriteRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class FavoriteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(FavoriteRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_favorite_by_id(): void
    {
        $favorite = Favorite::factory()->create();

        $found = $this->repository->findById($favorite->favorite_id);

        $this->assertNotNull($found);
        $this->assertEquals($favorite->favorite_id, $found->favorite_id);
        $this->assertEquals($favorite->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_favorite_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_favorite_by_id_with_seo(): void
    {
        $favorite = Favorite::factory()->create();

        $found = $this->repository->findByIdWithSeo($favorite->favorite_id);

        $this->assertNotNull($found);
        $this->assertEquals($favorite->favorite_id, $found->favorite_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_favorite_by_slug(): void
    {
        $favorite = Favorite::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-favorite']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-favorite', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($favorite->favorite_id, $foundTr->favorite_id);
        $this->assertEquals($favorite->favorite_id, $foundEn->favorite_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_favorites_by_slug(): void
    {
        Favorite::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-favorite']
        ]);

        $found = $this->repository->findBySlug('inactive-favorite', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_favorites(): void
    {
        Favorite::factory()->active()->count(3)->create();
        Favorite::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($favorite) {
            $this->assertTrue($favorite->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_favorites(): void
    {
        Favorite::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_favorites_with_filters(): void
    {
        Favorite::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Favorite']
        ]);
        Favorite::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Favorite']
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
        Favorite::factory()->active()->count(3)->create();
        Favorite::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_favorites(): void
    {
        $favorite1 = Favorite::factory()->create(['created_at' => now()->subDays(2)]);
        $favorite2 = Favorite::factory()->create(['created_at' => now()->subDay()]);
        $favorite3 = Favorite::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($favorite1->favorite_id, $ascending->items()[0]->favorite_id);
        $this->assertEquals($favorite3->favorite_id, $descending->items()[0]->favorite_id);
    }

    /** @test */
    public function it_can_search_favorites(): void
    {
        Favorite::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Favorite::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_favorite(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Favorite'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-favorite'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $favorite = $this->repository->create($data);

        $this->assertInstanceOf(Favorite::class, $favorite);
        $this->assertEquals('Yeni Sayfa', $favorite->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('favorites', ['favorite_id' => $favorite->favorite_id]);
    }

    /** @test */
    public function it_can_update_favorite(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($favorite->favorite_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $favorite->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_favorite(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_favorite(): void
    {
        $favorite = Favorite::factory()->create();

        $result = $this->repository->delete($favorite->favorite_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('favorites', ['favorite_id' => $favorite->favorite_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_favorite(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_favorite_active_status(): void
    {
        $favorite = Favorite::factory()->active()->create();
        $this->assertTrue($favorite->is_active);

        $this->repository->toggleActive($favorite->favorite_id);
        $this->assertFalse($favorite->fresh()->is_active);

        $this->repository->toggleActive($favorite->favorite_id);
        $this->assertTrue($favorite->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_favorites(): void
    {
        $favorites = Favorite::factory()->count(5)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('favorites', ['favorite_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $favorites = Favorite::factory()->active()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($favorites as $favorite) {
            $this->assertFalse($favorite->fresh()->is_active);
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
        $favorite = Favorite::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($favorite->favorite_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $favorite = Favorite::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($favorite->favorite_id);
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
        Favorite::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $favorite) {
            $this->assertTrue($favorite->relationLoaded('seoSetting'));
        }
    }
}
