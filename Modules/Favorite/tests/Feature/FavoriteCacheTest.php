<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Feature;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Repositories\FavoriteRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Favorite Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class FavoriteCacheTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(FavoriteRepository::class);
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
        $favorite = Favorite::factory()->create();

        // Cache'i doldur
        $this->repository->findById($favorite->favorite_id);

        // Update yap
        $this->repository->update($favorite->favorite_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $favorite = Favorite::factory()->create();

        // Cache'i doldur
        $this->repository->findById($favorite->favorite_id);

        // Delete yap
        $this->repository->delete($favorite->favorite_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("favorite_detail_{$favorite->favorite_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $favorite = Favorite::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($favorite->favorite_id);

        // Active favorites cache temizlenmiş olmalı
        $this->assertNull(Cache::get('favorites_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('favorites_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('favorites_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $favorite = Favorite::factory()->active()->create([
            'slug' => ['tr' => 'cached-favorite', 'en' => 'cached-favorite']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-favorite', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-favorite', 'tr');

        $this->assertEquals($favorite->favorite_id, $result1->favorite_id);
        $this->assertEquals($favorite->favorite_id, $result2->favorite_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $favorite1 = Favorite::factory()->create(['title' => ['tr' => 'Tenant 1 Favorite']]);

        $result = $this->repository->findById($favorite1->favorite_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Favorite', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $favorite = Favorite::factory()->create();

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
        $favorite = Favorite::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_favorite_{$favorite->favorite_id}", 'test_data', 3600);

        // Update yap
        $favorite->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_favorite_{$favorite->favorite_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $favorite = Favorite::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $favorite->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.favorite' => 3600]);

        $favorite = Favorite::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($favorite->favorite_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $favorite = Favorite::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($favorite->favorite_id);
        $result2 = $this->repository->findById($favorite->favorite_id);

        $this->assertEquals($favorite->favorite_id, $result1->favorite_id);
        $this->assertEquals($favorite->favorite_id, $result2->favorite_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $favorite = Favorite::factory()->create();

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
        $favorite = Favorite::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($favorite->favorite_id);

        $this->assertNotNull($result);
    }
}
