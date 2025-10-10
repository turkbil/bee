<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Unit;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use Modules\Shop\App\Repositories\ShopRepository;
use Modules\Shop\App\Contracts\ShopRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * ShopRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class ShopRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ShopRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ShopRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_shop_by_id(): void
    {
        $shop = Shop::factory()->create();

        $found = $this->repository->findById($shop->shop_id);

        $this->assertNotNull($found);
        $this->assertEquals($shop->shop_id, $found->shop_id);
        $this->assertEquals($shop->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_shop_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_shop_by_id_with_seo(): void
    {
        $shop = Shop::factory()->create();

        $found = $this->repository->findByIdWithSeo($shop->shop_id);

        $this->assertNotNull($found);
        $this->assertEquals($shop->shop_id, $found->shop_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_shop_by_slug(): void
    {
        $shop = Shop::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-shop']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-shop', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($shop->shop_id, $foundTr->shop_id);
        $this->assertEquals($shop->shop_id, $foundEn->shop_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_shops_by_slug(): void
    {
        Shop::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-shop']
        ]);

        $found = $this->repository->findBySlug('inactive-shop', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_shops(): void
    {
        Shop::factory()->active()->count(3)->create();
        Shop::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($shop) {
            $this->assertTrue($shop->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_shops(): void
    {
        Shop::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_shops_with_filters(): void
    {
        Shop::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Shop']
        ]);
        Shop::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Shop']
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
        Shop::factory()->active()->count(3)->create();
        Shop::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_shops(): void
    {
        $shop1 = Shop::factory()->create(['created_at' => now()->subDays(2)]);
        $shop2 = Shop::factory()->create(['created_at' => now()->subDay()]);
        $shop3 = Shop::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($shop1->shop_id, $ascending->items()[0]->shop_id);
        $this->assertEquals($shop3->shop_id, $descending->items()[0]->shop_id);
    }

    /** @test */
    public function it_can_search_shops(): void
    {
        Shop::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Shop::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_shop(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Shop'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-shop'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $shop = $this->repository->create($data);

        $this->assertInstanceOf(Shop::class, $shop);
        $this->assertEquals('Yeni Sayfa', $shop->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('shops', ['shop_id' => $shop->shop_id]);
    }

    /** @test */
    public function it_can_update_shop(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($shop->shop_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $shop->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_shop(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_shop(): void
    {
        $shop = Shop::factory()->create();

        $result = $this->repository->delete($shop->shop_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('shops', ['shop_id' => $shop->shop_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_shop(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_shop_active_status(): void
    {
        $shop = Shop::factory()->active()->create();
        $this->assertTrue($shop->is_active);

        $this->repository->toggleActive($shop->shop_id);
        $this->assertFalse($shop->fresh()->is_active);

        $this->repository->toggleActive($shop->shop_id);
        $this->assertTrue($shop->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_shops(): void
    {
        $shops = Shop::factory()->count(5)->create();
        $ids = $shops->pluck('shop_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('shops', ['shop_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $shops = Shop::factory()->active()->count(3)->create();
        $ids = $shops->pluck('shop_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($shops as $shop) {
            $this->assertFalse($shop->fresh()->is_active);
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
        $shop = Shop::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($shop->shop_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $shop = Shop::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($shop->shop_id);
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
        Shop::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $shop) {
            $this->assertTrue($shop->relationLoaded('seoSetting'));
        }
    }
}
