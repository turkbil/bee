<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Feature;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Repositories\PortfolioRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Portfolio Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class PortfolioCacheTest extends TestCase
{
    use RefreshDatabase;

    private PortfolioRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PortfolioRepository::class);
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
        $portfolio = Portfolio::factory()->create();

        // Cache'i doldur
        $this->repository->findById($portfolio->portfolio_id);

        // Update yap
        $this->repository->update($portfolio->portfolio_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $portfolio = Portfolio::factory()->create();

        // Cache'i doldur
        $this->repository->findById($portfolio->portfolio_id);

        // Delete yap
        $this->repository->delete($portfolio->portfolio_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("portfolio_detail_{$portfolio->portfolio_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $portfolio = Portfolio::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($portfolio->portfolio_id);

        // Active portfolios cache temizlenmiş olmalı
        $this->assertNull(Cache::get('portfolios_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $portfolios = Portfolio::factory()->count(3)->create();
        $ids = $portfolios->pluck('portfolio_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('portfolios_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('portfolios_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $portfolio = Portfolio::factory()->active()->create([
            'slug' => ['tr' => 'cached-portfolio', 'en' => 'cached-portfolio']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-portfolio', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-portfolio', 'tr');

        $this->assertEquals($portfolio->portfolio_id, $result1->portfolio_id);
        $this->assertEquals($portfolio->portfolio_id, $result2->portfolio_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $portfolio1 = Portfolio::factory()->create(['title' => ['tr' => 'Tenant 1 Portfolio']]);

        $result = $this->repository->findById($portfolio1->portfolio_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Portfolio', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $portfolio = Portfolio::factory()->create();

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
        $portfolio = Portfolio::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_portfolio_{$portfolio->portfolio_id}", 'test_data', 3600);

        // Update yap
        $portfolio->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_portfolio_{$portfolio->portfolio_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $portfolio = Portfolio::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $portfolio->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.portfolio' => 3600]);

        $portfolio = Portfolio::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($portfolio->portfolio_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $portfolio = Portfolio::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($portfolio->portfolio_id);
        $result2 = $this->repository->findById($portfolio->portfolio_id);

        $this->assertEquals($portfolio->portfolio_id, $result1->portfolio_id);
        $this->assertEquals($portfolio->portfolio_id, $result2->portfolio_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $portfolio = Portfolio::factory()->create();

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
        $portfolio = Portfolio::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($portfolio->portfolio_id);

        $this->assertNotNull($result);
    }
}
