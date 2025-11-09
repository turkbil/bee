<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Unit;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Repositories\PaymentRepository;
use Modules\Payment\App\Contracts\PaymentRepositoryInterface;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * PaymentRepository Unit Tests
 *
 * Repository katmanının tüm CRUD operasyonlarını,
 * cache mekanizmalarını ve özel metodlarını test eder.
 */
class PaymentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PaymentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PaymentRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_payment_by_id(): void
    {
        $payment = Payment::factory()->create();

        $found = $this->repository->findById($payment->payment_id);

        $this->assertNotNull($found);
        $this->assertEquals($payment->payment_id, $found->payment_id);
        $this->assertEquals($payment->title, $found->title);
    }

    /** @test */
    public function it_returns_null_when_payment_not_found_by_id(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_find_payment_by_id_with_seo(): void
    {
        $payment = Payment::factory()->create();

        $found = $this->repository->findByIdWithSeo($payment->payment_id);

        $this->assertNotNull($found);
        $this->assertEquals($payment->payment_id, $found->payment_id);
        $this->assertTrue($found->relationLoaded('seoSetting'));
    }

    /** @test */
    public function it_can_find_payment_by_slug(): void
    {
        $payment = Payment::factory()->active()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-payment']
        ]);

        $foundTr = $this->repository->findBySlug('test-sayfasi', 'tr');
        $foundEn = $this->repository->findBySlug('test-payment', 'en');

        $this->assertNotNull($foundTr);
        $this->assertNotNull($foundEn);
        $this->assertEquals($payment->payment_id, $foundTr->payment_id);
        $this->assertEquals($payment->payment_id, $foundEn->payment_id);
    }

    /** @test */
    public function it_returns_null_when_slug_not_found(): void
    {
        $found = $this->repository->findBySlug('nonexistent-slug', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_does_not_find_inactive_payments_by_slug(): void
    {
        Payment::factory()->inactive()->create([
            'slug' => ['tr' => 'inactive-payment']
        ]);

        $found = $this->repository->findBySlug('inactive-payment', 'tr');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_get_active_payments(): void
    {
        Payment::factory()->active()->count(3)->create();
        Payment::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getActive();

        $this->assertCount(3, $activePages);
        $activePages->each(function ($payment) {
            $this->assertTrue($payment->is_active);
        });
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_get_paginated_payments(): void
    {
        Payment::factory()->count(15)->create();

        $paginated = $this->repository->getPaginated([], 10);

        $this->assertEquals(15, $paginated->total());
        $this->assertEquals(10, $paginated->perPage());
        $this->assertCount(10, $paginated->items());
    }

    /** @test */
    public function it_can_search_payments_with_filters(): void
    {
        Payment::factory()->create([
            'title' => ['tr' => 'Laravel Test Sayfası', 'en' => 'Laravel Test Payment']
        ]);
        Payment::factory()->create([
            'title' => ['tr' => 'PHP Sayfası', 'en' => 'PHP Payment']
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
        Payment::factory()->active()->count(3)->create();
        Payment::factory()->inactive()->count(2)->create();

        $activePages = $this->repository->getPaginated(['is_active' => true], 10);
        $inactivePages = $this->repository->getPaginated(['is_active' => false], 10);

        $this->assertEquals(3, $activePages->total());
        $this->assertEquals(2, $inactivePages->total());
    }

    /** @test */
    public function it_can_sort_payments(): void
    {
        $payment1 = Payment::factory()->create(['created_at' => now()->subDays(2)]);
        $payment2 = Payment::factory()->create(['created_at' => now()->subDay()]);
        $payment3 = Payment::factory()->create(['created_at' => now()]);

        $ascending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'asc'
        ], 10);

        $descending = $this->repository->getPaginated([
            'sortField' => 'created_at',
            'sortDirection' => 'desc'
        ], 10);

        $this->assertEquals($payment1->payment_id, $ascending->items()[0]->payment_id);
        $this->assertEquals($payment3->payment_id, $descending->items()[0]->payment_id);
    }

    /** @test */
    public function it_can_search_payments(): void
    {
        Payment::factory()->active()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);
        Payment::factory()->active()->create([
            'title' => ['tr' => 'PHP Programlama', 'en' => 'PHP Programming']
        ]);

        $results = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_create_payment(): void
    {
        $data = [
            'title' => ['tr' => 'Yeni Sayfa', 'en' => 'New Payment'],
            'slug' => ['tr' => 'yeni-sayfa', 'en' => 'new-payment'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,

        ];

        $payment = $this->repository->create($data);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('Yeni Sayfa', $payment->getTranslated('title', 'tr'));
        $this->assertDatabaseHas('payments', ['payment_id' => $payment->payment_id]);
    }

    /** @test */
    public function it_can_update_payment(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->repository->update($payment->payment_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $payment->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_returns_false_when_updating_nonexistent_payment(): void
    {
        $result = $this->repository->update(999, ['title' => ['tr' => 'Test']]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_payment(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->repository->delete($payment->payment_id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('payments', ['payment_id' => $payment->payment_id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_payment(): void
    {
        $result = $this->repository->delete(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_toggle_payment_active_status(): void
    {
        $payment = Payment::factory()->active()->create();
        $this->assertTrue($payment->is_active);

        $this->repository->toggleActive($payment->payment_id);
        $this->assertFalse($payment->fresh()->is_active);

        $this->repository->toggleActive($payment->payment_id);
        $this->assertTrue($payment->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_payments(): void
    {
        $payments = Payment::factory()->count(5)->create();
        $ids = $payments->pluck('payment_id')->toArray();

        $deletedCount = $this->repository->bulkDelete($ids);

        $this->assertEquals(5, $deletedCount);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('payments', ['payment_id' => $id]);
        }
    }

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_active_status(): void
    {
        $payments = Payment::factory()->active()->count(3)->create();
        $ids = $payments->pluck('payment_id')->toArray();

        $affectedCount = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(3, $affectedCount);
        foreach ($payments as $payment) {
            $this->assertFalse($payment->fresh()->is_active);
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
        $payment = Payment::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->update($payment->payment_id, [
            'title' => ['tr' => 'Updated', 'en' => 'Updated']
        ]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $payment = Payment::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once();

        $this->repository->delete($payment->payment_id);
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
        Payment::factory()->count(3)->create();

        $paginated = $this->repository->getPaginated([], 10);

        foreach ($paginated->items() as $payment) {
            $this->assertTrue($payment->relationLoaded('seoSetting'));
        }
    }
}
