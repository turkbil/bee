<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Unit;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Services\AnnouncementService;
use Modules\Announcement\App\Repositories\AnnouncementRepository;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Announcement\App\Exceptions\{AnnouncementNotFoundException, AnnouncementProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * AnnouncementService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class AnnouncementServiceTest extends TestCase
{
    use RefreshDatabase;

    private AnnouncementService $service;
    private AnnouncementRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(AnnouncementRepositoryInterface::class);
        $this->service = app(AnnouncementService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_announcement_by_id(): void
    {
        $announcement = Announcement::factory()->create();

        $result = $this->service->getPage($announcement->announcement_id);

        $this->assertInstanceOf(Announcement::class, $result);
        $this->assertEquals($announcement->announcement_id, $result->announcement_id);
    }

    /** @test */
    public function it_throws_exception_when_announcement_not_found(): void
    {
        $this->expectException(AnnouncementNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_announcement_by_slug(): void
    {
        $announcement = Announcement::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Announcement::class, $result);
        $this->assertEquals($announcement->announcement_id, $result->announcement_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(AnnouncementNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_announcements(): void
    {
        Announcement::factory()->active()->count(5)->create();
        Announcement::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_announcement_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Announcement::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_announcement(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-announcement', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_announcement_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Announcement created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_announcement_successfully(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($announcement->announcement_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $announcement->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_announcement(): void
    {
        $this->expectException(AnnouncementNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_announcement(): void
    {
        $announcement = Announcement::factory()->create();

        $result = $this->service->deletePage($announcement->announcement_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('announcements', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_announcement_status(): void
    {
        $announcement = Announcement::factory()->active()->create();

        $result = $this->service->togglePageStatus($announcement->announcement_id);

        $this->assertTrue($result->success);
        $this->assertFalse($announcement->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_announcements(): void
    {
        $announcements = Announcement::factory()->count(5)->create();
        $ids = $announcements->pluck('announcement_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_announcements_can_be_deleted(): void
    {
        $homeannouncement = Announcement::factory()->create();

        $result = $this->service->bulkDeletePages([$homeannouncement->announcement_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $announcements = Announcement::factory()->active()->count(5)->create();
        $ids = $announcements->pluck('announcement_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_announcements(): void
    {
        Announcement::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Announcement::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement'],
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
    public function it_can_prepare_announcement_for_form(): void
    {
        $announcement = Announcement::factory()->create();

        $formData = $this->service->preparePageForForm($announcement->announcement_id, 'tr');

        $this->assertArrayHasKey('announcement', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_announcement(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['announcement']);
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
            ->with('Announcement cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Announcement::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Announcement::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->announcement_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($announcement->announcement_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $announcement->fresh()->getTranslated('slug', 'tr'));
    }
}
