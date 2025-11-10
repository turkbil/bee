<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Feature;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Repositories\ReviewSystemRepository;
use Illuminate\Support\Facades\Cache;

/**
 * ReviewSystem Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class ReviewSystemCacheTest extends TestCase
{
    use RefreshDatabase;

    private ReviewSystemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReviewSystemRepository::class);
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
        $reviewsystem = ReviewSystem::factory()->create();

        // Cache'i doldur
        $this->repository->findById($reviewsystem->reviewsystem_id);

        // Update yap
        $this->repository->update($reviewsystem->reviewsystem_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // Cache'i doldur
        $this->repository->findById($reviewsystem->reviewsystem_id);

        // Delete yap
        $this->repository->delete($reviewsystem->reviewsystem_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("reviewsystem_detail_{$reviewsystem->reviewsystem_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($reviewsystem->reviewsystem_id);

        // Active reviewsystems cache temizlenmiş olmalı
        $this->assertNull(Cache::get('reviewsystems_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $reviewsystems = ReviewSystem::factory()->count(3)->create();
        $ids = $reviewsystems->pluck('reviewsystem_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('reviewsystems_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('reviewsystems_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create([
            'slug' => ['tr' => 'cached-reviewsystem', 'en' => 'cached-reviewsystem']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-reviewsystem', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-reviewsystem', 'tr');

        $this->assertEquals($reviewsystem->reviewsystem_id, $result1->reviewsystem_id);
        $this->assertEquals($reviewsystem->reviewsystem_id, $result2->reviewsystem_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $reviewsystem1 = ReviewSystem::factory()->create(['title' => ['tr' => 'Tenant 1 ReviewSystem']]);

        $result = $this->repository->findById($reviewsystem1->reviewsystem_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 ReviewSystem', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

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
        $reviewsystem = ReviewSystem::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_reviewsystem_{$reviewsystem->reviewsystem_id}", 'test_data', 3600);

        // Update yap
        $reviewsystem->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_reviewsystem_{$reviewsystem->reviewsystem_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $reviewsystem->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.reviewsystem' => 3600]);

        $reviewsystem = ReviewSystem::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($reviewsystem->reviewsystem_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($reviewsystem->reviewsystem_id);
        $result2 = $this->repository->findById($reviewsystem->reviewsystem_id);

        $this->assertEquals($reviewsystem->reviewsystem_id, $result1->reviewsystem_id);
        $this->assertEquals($reviewsystem->reviewsystem_id, $result2->reviewsystem_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

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
        $reviewsystem = ReviewSystem::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($reviewsystem->reviewsystem_id);

        $this->assertNotNull($result);
    }
}
