<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Unit;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Repositories\MuzibuRepository;
use Modules\Muzibu\App\Contracts\MuzibuRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * MuzibuRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class MuzibuRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private MuzibuRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(MuzibuRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_muzibu_by_id(): void
    {
        $muzibu = Muzibu::factory()->create();

        $found = $this->repository->findById($muzibu->muzibu_id);

        $this->assertNotNull($found);
        $this->assertEquals($muzibu->muzibu_id, $found->muzibu_id);
        $this->assertEquals($muzibu->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_muzibu_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_muzibu_by_id_with_seo(): void
    {
        $muzibu = Muzibu::factory()->create();

        $found = $this->repository->findByIdWithSeo($muzibu->muzibu_id);

        $this->assertNotNull($found);
        $this->assertEquals($muzibu->muzibu_id, $found->muzibu_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_muzibu_by_slug(): void
    {
        $muzibu = Muzibu::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-muzibu']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-muzibu', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($muzibu->muzibu_id, $foundTr->muzibu_id);
        $this->assertEquals($muzibu->muzibu_id, $foundEn->muzibu_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_muzibus_by_slug(): void
    {
        Muzibu::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-muzibu']
        ]);

        $found = $this->repository->findBySlug('inactive-muzibu', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_muzibus(): void
    {
        Muzibu::factory()->active()->count(3)->create();
        Muzibu::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($muzibu) {
            $this->assertTrue($muzibu->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_muzibus(): void
    {
        Muzibu::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_muzibus_with_filters(): void
    {
        Muzibu::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Muzibu']
        ]);
        Muzibu::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Muzibu']
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
        Muzibu::factory()->active()->count(3)->create();
        Muzibu::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_muzibus(): void
    {
        $muzibu1 = Muzibu::factory()->create(['created_at' => now()->subDays(2)]);
        $muzibu2 = Muzibu::factory()->create(['created_at' => now()->subDay()]);
        $muzibu3 = Muzibu::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($muzibu1->muzibu_id, $ascending->items()[0]->muzibu_id);
        $this->assertEquals($muzibu3->muzibu_id, $descending->items()[0]->muzibu_id);
    }

    /** @test */
    public function it_can_search_muzibus(): void
    {
        Muzibu::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Muzibu::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_muzibu(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Muzibu'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-muzibu'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $muzibu = $this->repository->create($data);

        $this->assertInstanceOf(Muzibu::class, $muzibu);
        $this->assertEquals('Yeni Sayfa', $muzibu->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */
    public function it_can_update_muzibu(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($muzibu->muzibu_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $muzibu->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_muzibu(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_muzibu(): void
    {
        $muzibu = Muzibu::factory()->create();

        $result = $this->repository->delete($muzibu->muzibu_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_muzibu(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_muzibu_active_status(): void
    {
        $muzibu = Muzibu::factory()->active()->create();
        $this->assertTrue($muzibu->is_active);

        $this->repository->toggleActive($muzibu->muzibu_id);
        $this->assertFalse($muzibu->fresh()->is_active);

        $this->repository->toggleActive($muzibu->muzibu_id);
        $this->assertTrue($muzibu->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_muzibus(): void
    {
        $muzibus = Muzibu::factory()->count(5)->create();
        $ids = $muzibus->pluck('muzibu_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('muzibus', ['muzibu_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $muzibus = Muzibu::factory()->active()->count(3)->create();
        $ids = $muzibus->pluck('muzibu_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($muzibus as $muzibu) {
            $this->assertFalse($muzibu->fresh()->is_active);
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
        $muzibu = Muzibu::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($muzibu->muzibu_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $muzibu = Muzibu::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($muzibu->muzibu_id);
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
        Muzibu::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $muzibu) {
            $this->assertTrue($muzibu->relationLoaded('seoSetting'));
        }
    }
}
