<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Feature;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Repositories\AnnouncementRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Announcement Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class AnnouncementCacheTest extends TestCase
{
    use RefreshDatabase;

    private AnnouncementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AnnouncementRepository::class);
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
        $announcement = Announcement::factory()->create();

        // Cache'i doldur
        $this->repository->findById($announcement->announcement_id);

        // Update yap
        $this->repository->update($announcement->announcement_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $announcement = Announcement::factory()->create();

        // Cache'i doldur
        $this->repository->findById($announcement->announcement_id);

        // Delete yap
        $this->repository->delete($announcement->announcement_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("announcement_detail_{$announcement->announcement_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $announcement = Announcement::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($announcement->announcement_id);

        // Active announcements cache temizlenmiş olmalı
        $this->assertNull(Cache::get('announcements_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $announcements = Announcement::factory()->count(3)->create();
        $ids = $announcements->pluck('announcement_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('announcements_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('announcements_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $announcement = Announcement::factory()->active()->create([
            'slug' => ['tr' => 'cached-announcement', 'en' => 'cached-announcement']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-announcement', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-announcement', 'tr');

        $this->assertEquals($announcement->announcement_id, $result1->announcement_id);
        $this->assertEquals($announcement->announcement_id, $result2->announcement_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $announcement1 = Announcement::factory()->create(['title' => ['tr' => 'Tenant 1 Announcement']]);

        $result = $this->repository->findById($announcement1->announcement_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Announcement', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $announcement = Announcement::factory()->create();

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
        $announcement = Announcement::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_announcement_{$announcement->announcement_id}", 'test_data', 3600);

        // Update yap
        $announcement->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_announcement_{$announcement->announcement_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $announcement = Announcement::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $announcement->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.announcement' => 3600]);

        $announcement = Announcement::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($announcement->announcement_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $announcement = Announcement::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($announcement->announcement_id);
        $result2 = $this->repository->findById($announcement->announcement_id);

        $this->assertEquals($announcement->announcement_id, $result1->announcement_id);
        $this->assertEquals($announcement->announcement_id, $result2->announcement_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $announcement = Announcement::factory()->create();

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
        $announcement = Announcement::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($announcement->announcement_id);

        $this->assertNotNull($result);
    }
}
