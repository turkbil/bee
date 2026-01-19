<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Services\ServiceCategoryService;
use Modules\Service\App\Repositories\ServiceCategoryRepository;
use Modules\Service\App\Models\ServiceCategory;
use Modules\Service\App\Models\Service;
use Illuminate\Support\Facades\Log;
use Mockery;

/**
 * ServiceCategoryService Unit Tests
 */
class ServiceCategoryServiceTest extends TestCase
{
    private ServiceCategoryService $service;
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ServiceCategoryRepository::class);
        $this->service = new ServiceCategoryService($this->repositoryMock);
    }

    /** @test */
    public function it_gets_paginated_categories(): void
    {
        $filters = ['is_active' => true];
        $perPage = 15;

        $this->repositoryMock
            ->shouldReceive('getPaginated')
            ->once()
            ->with($filters, $perPage)
            ->andReturn(collect([]));

        $result = $this->service->getPaginatedCategories($filters, $perPage);

        $this->assertNotNull($result);
    }

    /** @test */
    public function it_finds_category_by_id(): void
    {
        $category = ServiceCategory::factory()->make(['category_id' => 1]);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $this->service->findCategory(1);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->category_id);
    }

    /** @test */
    public function it_finds_category_with_seo(): void
    {
        $category = ServiceCategory::factory()->make(['category_id' => 1]);

        $this->repositoryMock
            ->shouldReceive('findByIdWithSeo')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $this->service->findCategoryWithSeo(1);

        $this->assertNotNull($result);
    }

    /** @test */
    public function it_finds_category_by_slug(): void
    {
        $category = ServiceCategory::factory()->make([
            'slug' => ['tr' => 'web-gelistirme']
        ]);

        $this->repositoryMock
            ->shouldReceive('findBySlug')
            ->once()
            ->with('web-gelistirme', 'tr')
            ->andReturn($category);

        $result = $this->service->findBySlug('web-gelistirme', 'tr');

        $this->assertNotNull($result);
    }

    /** @test */
    public function it_gets_active_categories(): void
    {
        $categories = collect([
            ServiceCategory::factory()->make(['is_active' => true]),
            ServiceCategory::factory()->make(['is_active' => true]),
        ]);

        $this->repositoryMock
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($categories);

        $result = $this->service->getActiveCategories();

        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_creates_category_and_logs(): void
    {
        Log::shouldReceive('info')->once();

        $data = [
            'title' => ['tr' => 'Web Geliştirme'],
            'slug' => ['tr' => 'web-gelistirme'],
            'is_active' => true,
        ];

        $category = ServiceCategory::factory()->make([
            'category_id' => 1,
            ...$data
        ]);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($category);

        $result = $this->service->createCategory($data);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->category_id);
    }

    /** @test */
    public function it_updates_category_and_logs(): void
    {
        Log::shouldReceive('info')->once();

        $data = ['title' => ['tr' => 'Güncellenmiş Başlık']];

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn(true);

        $result = $this->service->updateCategory(1, $data);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_does_not_log_when_update_fails(): void
    {
        Log::shouldReceive('info')->never();

        $data = ['title' => ['tr' => 'Güncellenmiş Başlık']];

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn(false);

        $result = $this->service->updateCategory(1, $data);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_deletes_category_without_services(): void
    {
        Log::shouldReceive('info')->once();

        $category = ServiceCategory::factory()->make(['category_id' => 1]);
        $category->shouldReceive('services')->andReturnSelf();
        $category->shouldReceive('count')->andReturn(0);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->service->deleteCategory(1);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_prevents_deleting_category_with_services(): void
    {
        Log::shouldReceive('warning')->once();

        $category = ServiceCategory::factory()->make(['category_id' => 1]);
        $category->shouldReceive('services')->andReturnSelf();
        $category->shouldReceive('count')->andReturn(5);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $this->service->deleteCategory(1);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_returns_false_when_deleting_non_existent_category(): void
    {
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->service->deleteCategory(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_toggles_category_status_successfully(): void
    {
        Log::shouldReceive('info')->once();

        $category = ServiceCategory::factory()->make([
            'category_id' => 1,
            'is_active' => false
        ]);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $this->repositoryMock
            ->shouldReceive('toggleActive')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->service->toggleCategoryStatus(1);

        $this->assertTrue($result['success']);
        $this->assertEquals('success', $result['type']);
        $this->assertTrue($result['meta']['new_status']);
    }

    /** @test */
    public function it_returns_error_when_category_not_found_for_toggle(): void
    {
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->service->toggleCategoryStatus(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('error', $result['type']);
    }

    /** @test */
    public function it_searches_categories(): void
    {
        $term = 'web';
        $locales = ['tr', 'en'];

        $this->repositoryMock
            ->shouldReceive('search')
            ->once()
            ->with($term, $locales)
            ->andReturn(collect([]));

        $result = $this->service->searchCategories($term, $locales);

        $this->assertNotNull($result);
    }

    /** @test */
    public function it_bulk_deletes_categories(): void
    {
        $ids = [1, 2, 3];

        $this->repositoryMock
            ->shouldReceive('bulkDelete')
            ->once()
            ->with($ids)
            ->andReturn(3);

        $result = $this->service->bulkDeleteCategories($ids);

        $this->assertEquals(3, $result);
    }

    /** @test */
    public function it_bulk_toggles_active_status(): void
    {
        $ids = [1, 2, 3];

        $this->repositoryMock
            ->shouldReceive('bulkToggleActive')
            ->once()
            ->with($ids)
            ->andReturn(3);

        $result = $this->service->bulkToggleActive($ids);

        $this->assertEquals(3, $result);
    }

    /** @test */
    public function it_clears_cache(): void
    {
        $this->repositoryMock
            ->shouldReceive('clearCache')
            ->once();

        $this->service->clearCache();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_prepares_category_for_form(): void
    {
        $category = ServiceCategory::factory()->make([
            'category_id' => 1,
            'title' => ['tr' => 'Web Geliştirme'],
            'seoSetting' => null
        ]);

        $this->repositoryMock
            ->shouldReceive('findByIdWithSeo')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $this->service->prepareCategoryForForm(1, 'tr');

        $this->assertNotNull($result['category']);
        $this->assertArrayHasKey('tabCompletion', $result);
        $this->assertTrue($result['tabCompletion']['general']);
        $this->assertFalse($result['tabCompletion']['seo']);
    }

    /** @test */
    public function it_returns_empty_data_when_category_not_found_for_form(): void
    {
        $this->repositoryMock
            ->shouldReceive('findByIdWithSeo')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->service->prepareCategoryForForm(999, 'tr');

        $this->assertNull($result['category']);
        $this->assertEmpty($result['tabCompletion']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
