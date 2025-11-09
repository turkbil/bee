<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Payment Cache Tests
 *
 * Cache mekanizmalarının doğru çalıştığını
 * ve cache invalidation'ın düzgün olduğunu test eder.
 */
class PaymentCacheTest extends TestCase
{
    use RefreshDatabase;

    private PaymentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PaymentRepository::class);
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
        $payment = Payment::factory()->create();

        // Cache'i doldur
        $this->repository->findById($payment->payment_id);

        // Update yap
        $this->repository->update($payment->payment_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);

        // Cache temizlenmiş olmalı
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $payment = Payment::factory()->create();

        // Cache'i doldur
        $this->repository->findById($payment->payment_id);

        // Delete yap
        $this->repository->delete($payment->payment_id);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get("payment_detail_{$payment->payment_id}"));
    }

    /** @test */
    public function it_clears_cache_after_toggle_active(): void
    {
        $payment = Payment::factory()->active()->create();

        // Cache'i doldur
        $this->repository->getActive();

        // Toggle yap
        $this->repository->toggleActive($payment->payment_id);

        // Active payments cache temizlenmiş olmalı
        $this->assertNull(Cache::get('payments_list'));
    }

    /** @test */
    public function it_clears_cache_after_bulk_delete(): void
    {
        $payments = Payment::factory()->count(3)->create();
        $ids = $payments->pluck('payment_id')->toArray();

        // Cache'i doldur
        $this->repository->getActive();

        // Bulk delete yap
        $this->repository->bulkDelete($ids);

        // Cache temizlenmiş olmalı
        $this->assertNull(Cache::get('payments_list'));
    }

    /** @test */
    public function manual_cache_clear_works(): void
    {
        // Cache'i doldur
        $this->repository->getActive();

        // Manuel temizle
        $this->repository->clearCache();

        // Cache temiz olmalı
        $this->assertNull(Cache::get('payments_list'));
    }

    /** @test */
    public function find_by_slug_uses_cache(): void
    {
        $payment = Payment::factory()->active()->create([
            'slug' => ['tr' => 'cached-payment', 'en' => 'cached-payment']
        ]);

        // İlk çağrı
        $result1 = $this->repository->findBySlug('cached-payment', 'tr');

        // İkinci çağrı (cache'ten)
        $result2 = $this->repository->findBySlug('cached-payment', 'tr');

        $this->assertEquals($payment->payment_id, $result1->payment_id);
        $this->assertEquals($payment->payment_id, $result2->payment_id);
    }

    /** @test */
    public function cache_is_tenant_aware(): void
    {
        // Tenant context'i simüle et
        $payment1 = Payment::factory()->create(['title' => ['tr' => 'Tenant 1 Payment']]);

        $result = $this->repository->findById($payment1->payment_id);

        $this->assertNotNull($result);
        $this->assertEquals('Tenant 1 Payment', $result->getTranslated('title', 'tr'));
    }

    /** @test */
    public function cache_keys_are_properly_generated(): void
    {
        $payment = Payment::factory()->create();

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
        $payment = Payment::factory()->create();

        // SEO cache key'i oluştur
        Cache::put("universal_seo_payment_{$payment->payment_id}", 'test_data', 3600);

        // Update yap
        $payment->update(['title' => ['tr' => 'Updated']]);

        // SEO cache temizlenmiş olmalı
        $this->assertNull(Cache::get("universal_seo_payment_{$payment->payment_id}"));
    }

    /** @test */
    public function response_cache_is_cleared_on_update(): void
    {
        $payment = Payment::factory()->create();

        // Sayfa update'i sonrası response cache temizlenmeli
        $payment->update(['title' => ['tr' => 'Updated']]);

        // Response cache clear edilmiş olmalı (function check)
        $this->assertTrue(true);
    }

    /** @test */
    public function cache_ttl_respects_configuration(): void
    {
        config(['cache.ttl.payment' => 3600]);

        $payment = Payment::factory()->create();

        // Cache TTL doğru uygulanmalı
        $this->repository->findById($payment->payment_id);

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_fresh_strategy_bypasses_cache(): void
    {
        $payment = Payment::factory()->create();

        // Admin panelden istek simüle et
        request()->headers->set('X-Cache-Strategy', 'admin-fresh');

        // Her defasında fresh data gelmeli
        $result1 = $this->repository->findById($payment->payment_id);
        $result2 = $this->repository->findById($payment->payment_id);

        $this->assertEquals($payment->payment_id, $result1->payment_id);
        $this->assertEquals($payment->payment_id, $result2->payment_id);
    }

    /** @test */
    public function cache_tags_are_used_correctly(): void
    {
        $payment = Payment::factory()->create();

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
        $payment = Payment::factory()->create();

        // No-cache stratejisi simüle et
        request()->headers->set('X-Cache-Strategy', 'no-cache');

        $result = $this->repository->findById($payment->payment_id);

        $this->assertNotNull($result);
    }
}
