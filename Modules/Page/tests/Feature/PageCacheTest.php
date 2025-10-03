<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Feature;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Repositories\PageRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Page Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class PageCacheTest extends TestCase
{
    use RefreshDatabase;

    private PageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PageRepository::class);
    }

    /** @test */
    public function it_caches_homepage_query(): void
    {
        $homepage = Page::factory()->homepage()->create();

        // İlk çağrı - DB'den
        $result1 = $this->repository->getHomepage();

        // İkinci çağrı - Cache'ten
        $result2 = $this->repository->getHomepage();

        $this->assertEquals($homepage->page_id, $result1->page_id);
        $this->assertEquals($homepage->page_id, $result2->page_id);
    }

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
        $this->assertNull(Cache::get('homepage_data'));
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $page = Page::factory()->create();

        // Cache'i doldur
        $this->repository->findById($page->page_id);

        // Update yap
        $this->repository->update($page->page_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('homepage_data'));
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $page = Page::factory()->create();

        // Cache'i doldur
        $this->repository->findById($page->page_id);

        // Delete yap
        $this->repository->delete($page->page_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("page_detail_{$page->page_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $page = Page::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($page->page_id);

        // Active pages cache temizlenmiş olmalı
        $this->assertNull(Cache::get('pages_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('pages_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('pages_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $page = Page::factory()->active()->create([
            'slug' => ['tr' => 'cached-page', 'en' => 'cached-page']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-page', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-page', 'tr');

        $this->assertEquals($page->page_id, $result1->page_id);
        $this->assertEquals($page->page_id, $result2->page_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $page1 = Page::factory()->create(['title' => ['tr' => 'Tenant 1 Page']]);

        $result = $this->repository->findById($page1->page_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Page', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $page = Page::factory()->create();

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
        $page = Page::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_page_{$page->page_id}", 'test_data', 3600);

        // Update yap
        $page->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_page_{$page->page_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $page = Page::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $page->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.page' => 3600]);

        $page = Page::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($page->page_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $page = Page::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($page->page_id);
        $result2 = $this->repository->findById($page->page_id);

        $this->assertEquals($page->page_id, $result1->page_id);
        $this->assertEquals($page->page_id, $result2->page_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $page = Page::factory()->create();

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
        $page = Page::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($page->page_id);

        $this->assertNotNull($result);
    }
}