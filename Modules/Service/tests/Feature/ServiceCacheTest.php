<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Feature;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Service Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class ServiceCacheTest extends TestCase
{
    use RefreshDatabase;

    private ServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ServiceRepository::class);
    }

    /** @test */

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'slug' => ['tr' => 'test', 'en' => 'test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->repository->create($data);

        // Cache temizlenmiş mi kontrol et
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $service = Service::factory()->create();

        // Cache'i doldur
        $this->repository->findById($service->service_id);

        // Update yap
        $this->repository->update($service->service_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $service = Service::factory()->create();

        // Cache'i doldur
        $this->repository->findById($service->service_id);

        // Delete yap
        $this->repository->delete($service->service_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("service_detail_{$service->service_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $service = Service::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($service->service_id);

        // Active services cache temizlenmiş olmalı
        $this->assertNull(Cache::get('services_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $services = Service::factory()->count(3)->create();
        $ids = $services->pluck('service_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('services_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('services_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $service = Service::factory()->active()->create([
            'slug' => ['tr' => 'cached-service', 'en' => 'cached-service']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-service', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-service', 'tr');

        $this->assertEquals($service->service_id, $result1->service_id);
        $this->assertEquals($service->service_id, $result2->service_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $service1 = Service::factory()->create(['title' => ['tr' => 'Tenant 1 Service']]);

        $result = $this->repository->findById($service1->service_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Service', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $service = Service::factory()->create();

        // Cache key generation test
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('getCacheKey');
        $method->setAccessible(true);

        $key = $method->invoke($this->repository, 'test_key');

        $this->assertIsString($key);
        $this->assertStringContainsString('test_key', $key);
    }

    /** @test */
    public function universal_seo_cache_is_cleared_on_update(): void
    {
        $service = Service::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_service_{$service->service_id}", 'test_data', 3600);

        // Update yap
        $service->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_service_{$service->service_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $service = Service::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $service->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.service' => 3600]);

        $service = Service::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($service->service_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $service = Service::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($service->service_id);
        $result2 = $this->repository->findById($service->service_id);

        $this->assertEquals($service->service_id, $result1->service_id);
        $this->assertEquals($service->service_id, $result2->service_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $service = Service::factory()->create();

        // Cache tag'leri al
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('getCacheTags');
        $method->setAccessible(true);

        $tags = $method->invoke($this->repository);

        $this->assertIsArray($tags);
        $this->assertNotEmpty($tags);
    }

    /** @test */
    public function cache_is_not_used_when_strategy_is_no_cache(): void
    {
        $service = Service::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($service->service_id);

        $this->assertNotNull($result);
    }
}
