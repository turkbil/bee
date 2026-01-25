<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Repositories\ServiceRepository;
use Modules\Service\App\Contracts\ServiceRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * ServiceRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class ServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ServiceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ServiceRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_service_by_id(): void
    {
        $service = Service::factory()->create();

        $found = $this->repository->findById($service->service_id);

        $this->assertNotNull($found);
        $this->assertEquals($service->service_id, $found->service_id);
        $this->assertEquals($service->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_service_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_service_by_id_with_seo(): void
    {
        $service = Service::factory()->create();

        $found = $this->repository->findByIdWithSeo($service->service_id);

        $this->assertNotNull($found);
        $this->assertEquals($service->service_id, $found->service_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_service_by_slug(): void
    {
        $service = Service::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-service']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-service', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($service->service_id, $foundTr->service_id);
        $this->assertEquals($service->service_id, $foundEn->service_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_services_by_slug(): void
    {
        Service::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-service']
        ]);

        $found = $this->repository->findBySlug('inactive-service', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_services(): void
    {
        Service::factory()->active()->count(3)->create();
        Service::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($service) {
            $this->assertTrue($service->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_services(): void
    {
        Service::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_services_with_filters(): void
    {
        Service::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Service']
        ]);
        Service::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Service']
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
        Service::factory()->active()->count(3)->create();
        Service::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_services(): void
    {
        $service1 = Service::factory()->create(['created_at' => now()->subDays(2)]);
        $service2 = Service::factory()->create(['created_at' => now()->subDay()]);
        $service3 = Service::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($service1->service_id, $ascending->items()[0]->service_id);
        $this->assertEquals($service3->service_id, $descending->items()[0]->service_id);
    }

    /** @test */
    public function it_can_search_services(): void
    {
        Service::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Service::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_service(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Service'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-service'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $service = $this->repository->create($data);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('Yeni Sayfa', $service->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('services', ['service_id' => $service->service_id]);
    }

    /** @test */
    public function it_can_update_service(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($service->service_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $service->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_service(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_service(): void
    {
        $service = Service::factory()->create();

        $result = $this->repository->delete($service->service_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('services', ['service_id' => $service->service_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_service(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_service_active_status(): void
    {
        $service = Service::factory()->active()->create();
        $this->assertTrue($service->is_active);

        $this->repository->toggleActive($service->service_id);
        $this->assertFalse($service->fresh()->is_active);

        $this->repository->toggleActive($service->service_id);
        $this->assertTrue($service->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_services(): void
    {
        $services = Service::factory()->count(5)->create();
        $ids = $services->pluck('service_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('services', ['service_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $services = Service::factory()->active()->count(3)->create();
        $ids = $services->pluck('service_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($services as $service) {
            $this->assertFalse($service->fresh()->is_active);
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
        $service = Service::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($service->service_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $service = Service::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($service->service_id);
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
        Service::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $service) {
            $this->assertTrue($service->relationLoaded('seoSetting'));
        }
    }
}
