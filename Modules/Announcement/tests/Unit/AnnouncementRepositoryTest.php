<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Unit;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Repositories\AnnouncementRepository;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * AnnouncementRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class AnnouncementRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AnnouncementRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AnnouncementRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_announcement_by_id(): void
    {
        $announcement = Announcement::factory()->create();

        $found = $this->repository->findById($announcement->announcement_id);

        $this->assertNotNull($found);
        $this->assertEquals($announcement->announcement_id, $found->announcement_id);
        $this->assertEquals($announcement->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_announcement_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_announcement_by_id_with_seo(): void
    {
        $announcement = Announcement::factory()->create();

        $found = $this->repository->findByIdWithSeo($announcement->announcement_id);

        $this->assertNotNull($found);
        $this->assertEquals($announcement->announcement_id, $found->announcement_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_announcement_by_slug(): void
    {
        $announcement = Announcement::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-announcement']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-announcement', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($announcement->announcement_id, $foundTr->announcement_id);
        $this->assertEquals($announcement->announcement_id, $foundEn->announcement_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_announcements_by_slug(): void
    {
        Announcement::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-announcement']
        ]);

        $found = $this->repository->findBySlug('inactive-announcement', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_announcements(): void
    {
        Announcement::factory()->active()->count(3)->create();
        Announcement::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($announcement) {
            $this->assertTrue($announcement->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_announcements(): void
    {
        Announcement::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_announcements_with_filters(): void
    {
        Announcement::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Announcement']
        ]);
        Announcement::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Announcement']
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
        Announcement::factory()->active()->count(3)->create();
        Announcement::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_announcements(): void
    {
        $announcement1 = Announcement::factory()->create(['created_at' => now()->subDays(2)]);
        $announcement2 = Announcement::factory()->create(['created_at' => now()->subDay()]);
        $announcement3 = Announcement::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($announcement1->announcement_id, $ascending->items()[0]->announcement_id);
        $this->assertEquals($announcement3->announcement_id, $descending->items()[0]->announcement_id);
    }

    /** @test */
    public function it_can_search_announcements(): void
    {
        Announcement::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Announcement::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_announcement(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Announcement'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-announcement'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $announcement = $this->repository->create($data);

        $this->assertInstanceOf(Announcement::class, $announcement);
        $this->assertEquals('Yeni Sayfa', $announcement->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('announcements', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */
    public function it_can_update_announcement(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($announcement->announcement_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $announcement->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_announcement(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_announcement(): void
    {
        $announcement = Announcement::factory()->create();

        $result = $this->repository->delete($announcement->announcement_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('announcements', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_announcement(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_announcement_active_status(): void
    {
        $announcement = Announcement::factory()->active()->create();
        $this->assertTrue($announcement->is_active);

        $this->repository->toggleActive($announcement->announcement_id);
        $this->assertFalse($announcement->fresh()->is_active);

        $this->repository->toggleActive($announcement->announcement_id);
        $this->assertTrue($announcement->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_announcements(): void
    {
        $announcements = Announcement::factory()->count(5)->create();
        $ids = $announcements->pluck('announcement_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('announcements', ['announcement_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $announcements = Announcement::factory()->active()->count(3)->create();
        $ids = $announcements->pluck('announcement_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($announcements as $announcement) {
            $this->assertFalse($announcement->fresh()->is_active);
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
        $announcement = Announcement::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($announcement->announcement_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $announcement = Announcement::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($announcement->announcement_id);
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
        Announcement::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $announcement) {
            $this->assertTrue($announcement->relationLoaded('seoSetting'));
        }
    }
}
