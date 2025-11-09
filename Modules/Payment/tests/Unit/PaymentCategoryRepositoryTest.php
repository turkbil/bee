<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Unit;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Repositories\PaymentCategoryRepository;
use Modules\Payment\App\Models\PaymentCategory;
use Illuminate\Support\Facades\Cache;

/**
 * PaymentCategoryRepository Unit Tests
 */
class PaymentCategoryRepositoryTest extends TestCase
{
    private PaymentCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PaymentCategoryRepository::class);
    }

    /** @test */
    public function it_finds_category_by_id(): void
    {
        $category = PaymentCategory::factory()->create();

        $result = $this->repository->findById($category->category_id);

        $this->assertNotNull($result);
        $this->assertEquals($category->category_id, $result->category_id);
    }

    /** @test */
    public function it_returns_null_when_category_not_found(): void
    {
        $result = $this->repository->findById(99999);

        $this->assertNull($result);
    }

    /** @test */
    public function it_finds_category_with_seo(): void
    {
        $category = PaymentCategory::factory()->create();
        $category->getOrCreateSeoSetting();

        $result = $this->repository->findByIdWithSeo($category->category_id);

        $this->assertNotNull($result);
        $this->assertNotNull($result->seoSetting);
    }

    /** @test */
    public function it_finds_category_by_slug(): void
    {
        $category = PaymentCategory::factory()->create([
            'slug' => ['tr' => 'web-gelistirme', 'en' => 'web-development'],
            'is_active' => true
        ]);

        $result = $this->repository->findBySlug('web-gelistirme', 'tr');

        $this->assertNotNull($result);
        $this->assertEquals($category->category_id, $result->category_id);
    }

    /** @test */
    public function it_does_not_find_inactive_category_by_slug(): void
    {
        PaymentCategory::factory()->create([
            'slug' => ['tr' => 'pasif-kategori'],
            'is_active' => false
        ]);

        $result = $this->repository->findBySlug('pasif-kategori', 'tr');

        $this->assertNull($result);
    }

    /** @test */
    public function it_gets_active_categories(): void
    {
        PaymentCategory::factory()->active()->count(5)->create();
        PaymentCategory::factory()->inactive()->count(3)->create();

        $result = $this->repository->getActive();

        $this->assertCount(5, $result);
        $result->each(function ($category) {
            $this->assertTrue($category->is_active);
        });
    }

    /** @test */
    public function it_orders_active_categories_by_sort_order(): void
    {
        PaymentCategory::factory()->create(['sort_order' => 30, 'is_active' => true]);
        PaymentCategory::factory()->create(['sort_order' => 10, 'is_active' => true]);
        PaymentCategory::factory()->create(['sort_order' => 20, 'is_active' => true]);

        $result = $this->repository->getActive();

        $this->assertEquals(10, $result->first()->sort_order);
        $this->assertEquals(30, $result->last()->sort_order);
    }

    /** @test */
    public function it_creates_category(): void
    {
        $data = [
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development'],
            'slug' => ['tr' => 'web-gelistirme', 'en' => 'web-development'],
            'description' => ['tr' => 'Açıklama', 'en' => 'Description'],
            'is_active' => true,
            'sort_order' => 1,
        ];

        $result = $this->repository->create($data);

        $this->assertNotNull($result);
        $this->assertDatabaseHas('payment_categories', [
            'category_id' => $result->category_id
        ]);
    }

    /** @test */
    public function it_updates_category(): void
    {
        $category = PaymentCategory::factory()->create([
            'title' => ['tr' => 'Eski Başlık']
        ]);

        $result = $this->repository->update($category->category_id, [
            'title' => ['tr' => 'Yeni Başlık']
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Yeni Başlık', $category->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_deletes_category(): void
    {
        $category = PaymentCategory::factory()->create();

        $result = $this->repository->delete($category->category_id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('payment_categories', [
            'category_id' => $category->category_id
        ]);
    }

    /** @test */
    public function it_toggles_active_status(): void
    {
        $category = PaymentCategory::factory()->create(['is_active' => false]);

        $result = $this->repository->toggleActive($category->category_id);

        $this->assertTrue($result);
        $this->assertTrue($category->fresh()->is_active);
    }

    /** @test */
    public function it_searches_categories(): void
    {
        PaymentCategory::factory()->create([
            'title' => ['tr' => 'Laravel Framework', 'en' => 'Laravel Framework']
        ]);

        PaymentCategory::factory()->create([
            'title' => ['tr' => 'React Development', 'en' => 'React Development']
        ]);

        $result = $this->repository->search('Laravel', ['tr', 'en']);

        $this->assertCount(1, $result);
    }

    /** @test */
    public function it_bulk_deletes_categories(): void
    {
        $category1 = PaymentCategory::factory()->create();
        $category2 = PaymentCategory::factory()->create();
        $category3 = PaymentCategory::factory()->create();

        $ids = [$category1->category_id, $category2->category_id, $category3->category_id];

        $result = $this->repository->bulkDelete($ids);

        $this->assertEquals(3, $result);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('payment_categories', ['category_id' => $id]);
        }
    }

    /** @test */
    public function it_bulk_toggles_active_status(): void
    {
        $category1 = PaymentCategory::factory()->create(['is_active' => false]);
        $category2 = PaymentCategory::factory()->create(['is_active' => false]);

        $ids = [$category1->category_id, $category2->category_id];

        $result = $this->repository->bulkToggleActive($ids);

        $this->assertEquals(2, $result);
        $this->assertTrue($category1->fresh()->is_active);
        $this->assertTrue($category2->fresh()->is_active);
    }

    /** @test */
    public function it_clears_cache(): void
    {
        Cache::shouldReceive('tags')
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        $this->repository->clearCache();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_gets_paginated_categories(): void
    {
        PaymentCategory::factory()->count(25)->create();

        $result = $this->repository->getPaginated([], 10);

        $this->assertEquals(10, $result->count());
        $this->assertEquals(25, $result->total());
    }

    /** @test */
    public function it_filters_paginated_categories_by_status(): void
    {
        PaymentCategory::factory()->active()->count(10)->create();
        PaymentCategory::factory()->inactive()->count(5)->create();

        $result = $this->repository->getPaginated(['is_active' => true], 20);

        $this->assertEquals(10, $result->total());
    }

    /** @test */
    public function it_filters_paginated_categories_by_search(): void
    {
        PaymentCategory::factory()->create([
            'title' => ['tr' => 'Laravel Geliştirme']
        ]);

        PaymentCategory::factory()->count(5)->create();

        $result = $this->repository->getPaginated(['search' => 'Laravel'], 20);

        $this->assertEquals(1, $result->total());
    }
}
