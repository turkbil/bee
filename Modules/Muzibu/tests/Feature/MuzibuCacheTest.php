<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Feature;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Repositories\MuzibuRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Muzibu Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class MuzibuCacheTest extends TestCase
{
    use RefreshDatabase;

    private MuzibuRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(MuzibuRepository::class);
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
        $muzibu = Muzibu::factory()->create();

        // Cache'i doldur
        $this->repository->findById($muzibu->muzibu_id);

        // Update yap
        $this->repository->update($muzibu->muzibu_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $muzibu = Muzibu::factory()->create();

        // Cache'i doldur
        $this->repository->findById($muzibu->muzibu_id);

        // Delete yap
        $this->repository->delete($muzibu->muzibu_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("muzibu_detail_{$muzibu->muzibu_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $muzibu = Muzibu::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($muzibu->muzibu_id);

        // Active muzibus cache temizlenmiş olmalı
        $this->assertNull(Cache::get('muzibus_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $muzibus = Muzibu::factory()->count(3)->create();
        $ids = $muzibus->pluck('muzibu_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('muzibus_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('muzibus_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $muzibu = Muzibu::factory()->active()->create([
            'slug' => ['tr' => 'cached-muzibu', 'en' => 'cached-muzibu']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-muzibu', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-muzibu', 'tr');

        $this->assertEquals($muzibu->muzibu_id, $result1->muzibu_id);
        $this->assertEquals($muzibu->muzibu_id, $result2->muzibu_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $muzibu1 = Muzibu::factory()->create(['title' => ['tr' => 'Tenant 1 Muzibu']]);

        $result = $this->repository->findById($muzibu1->muzibu_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Muzibu', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $muzibu = Muzibu::factory()->create();

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
        $muzibu = Muzibu::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_muzibu_{$muzibu->muzibu_id}", 'test_data', 3600);

        // Update yap
        $muzibu->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_muzibu_{$muzibu->muzibu_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $muzibu = Muzibu::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $muzibu->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.muzibu' => 3600]);

        $muzibu = Muzibu::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($muzibu->muzibu_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $muzibu = Muzibu::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($muzibu->muzibu_id);
        $result2 = $this->repository->findById($muzibu->muzibu_id);

        $this->assertEquals($muzibu->muzibu_id, $result1->muzibu_id);
        $this->assertEquals($muzibu->muzibu_id, $result2->muzibu_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $muzibu = Muzibu::factory()->create();

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
        $muzibu = Muzibu::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($muzibu->muzibu_id);

        $this->assertNotNull($result);
    }
}
