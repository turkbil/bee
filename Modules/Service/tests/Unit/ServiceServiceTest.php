<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Services\ServiceService;
use Modules\Service\App\Repositories\ServiceRepository;
use Modules\Service\App\Contracts\ServiceRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Service\App\Exceptions\{ServiceNotFoundException, ServiceProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * ServiceService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class ServiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ServiceService $service;
    private ServiceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ServiceRepositoryInterface::class);
        $this->service = app(ServiceService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_service_by_id(): void
    {
        $service = Service::factory()->create();

        $result = $this->service->getPage($service->service_id);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertEquals($service->service_id, $result->service_id);
    }

    /** @test */
    public function it_throws_exception_when_service_not_found(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_service_by_slug(): void
    {
        $service = Service::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Service::class, $result);
        $this->assertEquals($service->service_id, $result->service_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_services(): void
    {
        Service::factory()->active()->count(5)->create();
        Service::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_service_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Service::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_service(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-service', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_service_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Service created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_service_successfully(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($service->service_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $service->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_service(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_service(): void
    {
        $service = Service::factory()->create();

        $result = $this->service->deletePage($service->service_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('services', ['service_id' => $service->service_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_service_status(): void
    {
        $service = Service::factory()->active()->create();

        $result = $this->service->togglePageStatus($service->service_id);

        $this->assertTrue($result->success);
        $this->assertFalse($service->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_services(): void
    {
        $services = Service::factory()->count(5)->create();
        $ids = $services->pluck('service_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_services_can_be_deleted(): void
    {
        $homeservice = Service::factory()->create();

        $result = $this->service->bulkDeletePages([$homeservice->service_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $services = Service::factory()->active()->count(5)->create();
        $ids = $services->pluck('service_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_services(): void
    {
        Service::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Service::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'seo' => [
                'tr' => [
                    'meta_title' => 'SEO Başlık',
                    'meta_description' => 'SEO Açıklama',
                ],
                'en' => [
                    'meta_title' => 'SEO Title',
                    'meta_description' => 'SEO Description',
                ]
            ]
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_filters_empty_seo_values(): void
    {
        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'seo' => [
                'tr' => [
                    'meta_title' => 'Başlık',
                    'meta_description' => '', // Boş değer
                    'meta_keywords' => null, // Null değer
                ]
            ]
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_can_prepare_service_for_form(): void
    {
        $service = Service::factory()->create();

        $formData = $this->service->preparePageForForm($service->service_id, 'tr');

        $this->assertArrayHasKey('service', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_service(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['service']);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
    }

    /** @test */
    public function it_provides_validation_rules(): void
    {
        $rules = $this->service->getValidationRules(['tr', 'en']);

        $this->assertArrayHasKey('inputs.css', $rules);
        $this->assertArrayHasKey('inputs.js', $rules);
        $this->assertArrayHasKey('multiLangInputs.tr.title', $rules);
        $this->assertArrayHasKey('multiLangInputs.en.title', $rules);
    }

    /** @test */
    public function it_clears_cache(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Service cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Service::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Service::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->service_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($service->service_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $service->fresh()->getTranslated('slug', 'tr'));
    }
}
