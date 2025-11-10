<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Unit;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Services\ReviewSystemService;
use Modules\ReviewSystem\App\Repositories\ReviewSystemRepository;
use Modules\ReviewSystem\App\Contracts\ReviewSystemRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\ReviewSystem\App\Exceptions\{ReviewSystemNotFoundException, ReviewSystemProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * ReviewSystemService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class ReviewSystemServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReviewSystemService $service;
    private ReviewSystemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ReviewSystemRepositoryInterface::class);
        $this->service = app(ReviewSystemService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_reviewsystem_by_id(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $result = $this->service->getPage($reviewsystem->reviewsystem_id);

        $this->assertInstanceOf(ReviewSystem::class, $result);
        $this->assertEquals($reviewsystem->reviewsystem_id, $result->reviewsystem_id);
    }

    /** @test */
    public function it_throws_exception_when_reviewsystem_not_found(): void
    {
        $this->expectException(ReviewSystemNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_reviewsystem_by_slug(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(ReviewSystem::class, $result);
        $this->assertEquals($reviewsystem->reviewsystem_id, $result->reviewsystem_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(ReviewSystemNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_reviewsystems(): void
    {
        ReviewSystem::factory()->active()->count(5)->create();
        ReviewSystem::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_reviewsystem_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(ReviewSystem::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_reviewsystem(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-reviewsystem', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_reviewsystem_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_reviewsystem_successfully(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($reviewsystem->reviewsystem_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $reviewsystem->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_reviewsystem(): void
    {
        $this->expectException(ReviewSystemNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_reviewsystem(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $result = $this->service->deletePage($reviewsystem->reviewsystem_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('reviewsystems', ['reviewsystem_id' => $reviewsystem->reviewsystem_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_reviewsystem_status(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create();

        $result = $this->service->togglePageStatus($reviewsystem->reviewsystem_id);

        $this->assertTrue($result->success);
        $this->assertFalse($reviewsystem->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_reviewsystems(): void
    {
        $reviewsystems = ReviewSystem::factory()->count(5)->create();
        $ids = $reviewsystems->pluck('reviewsystem_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_reviewsystems_can_be_deleted(): void
    {
        $homereviewsystem = ReviewSystem::factory()->create();

        $result = $this->service->bulkDeletePages([$homereviewsystem->reviewsystem_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $reviewsystems = ReviewSystem::factory()->active()->count(5)->create();
        $ids = $reviewsystems->pluck('reviewsystem_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_reviewsystems(): void
    {
        ReviewSystem::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        ReviewSystem::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem'],
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
    public function it_can_prepare_reviewsystem_for_form(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $formData = $this->service->preparePageForForm($reviewsystem->reviewsystem_id, 'tr');

        $this->assertArrayHasKey('reviewsystem', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_reviewsystem(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['reviewsystem']);
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
            ->with('ReviewSystem cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = ReviewSystem::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = ReviewSystem::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->reviewsystem_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($reviewsystem->reviewsystem_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $reviewsystem->fresh()->getTranslated('slug', 'tr'));
    }
}
