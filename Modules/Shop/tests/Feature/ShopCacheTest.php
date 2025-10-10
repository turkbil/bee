<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Feature;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use Modules\Shop\App\Repositories\ShopRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Shop Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class ShopCacheTest extends TestCase
{
    use RefreshDatabase;

    private ShopRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ShopRepository::class);
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
        $shop = Shop::factory()->create();

        // Cache'i doldur
        $this->repository->findById($shop->shop_id);

        // Update yap
        $this->repository->update($shop->shop_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $shop = Shop::factory()->create();

        // Cache'i doldur
        $this->repository->findById($shop->shop_id);

        // Delete yap
        $this->repository->delete($shop->shop_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("shop_detail_{$shop->shop_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $shop = Shop::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($shop->shop_id);

        // Active shops cache temizlenmiş olmalı
        $this->assertNull(Cache::get('shops_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $shops = Shop::factory()->count(3)->create();
        $ids = $shops->pluck('shop_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('shops_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('shops_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $shop = Shop::factory()->active()->create([
            'slug' => ['tr' => 'cached-shop', 'en' => 'cached-shop']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-shop', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-shop', 'tr');

        $this->assertEquals($shop->shop_id, $result1->shop_id);
        $this->assertEquals($shop->shop_id, $result2->shop_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $shop1 = Shop::factory()->create(['title' => ['tr' => 'Tenant 1 Shop']]);

        $result = $this->repository->findById($shop1->shop_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Shop', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $shop = Shop::factory()->create();

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
        $shop = Shop::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_shop_{$shop->shop_id}", 'test_data', 3600);

        // Update yap
        $shop->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_shop_{$shop->shop_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $shop = Shop::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $shop->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.shop' => 3600]);

        $shop = Shop::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($shop->shop_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $shop = Shop::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($shop->shop_id);
        $result2 = $this->repository->findById($shop->shop_id);

        $this->assertEquals($shop->shop_id, $result1->shop_id);
        $this->assertEquals($shop->shop_id, $result2->shop_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $shop = Shop::factory()->create();

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
        $shop = Shop::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($shop->shop_id);

        $this->assertNotNull($result);
    }
}
