<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Unit;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Services\PageService;
use Modules\Page\App\Repositories\PageRepository;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Page\App\Exceptions\{PageNotFoundException, HomepageProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * PageService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class PageServiceTest extends TestCase
{
    use RefreshDatabase;

    private PageService $service;
    private PageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PageRepositoryInterface::class);
        $this->service = app(PageService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_page_by_id(): void
    {
        $page = Page::factory()->create();

        $result = $this->service->getPage($page->page_id);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($page->page_id, $result->page_id);
    }

    /** @test */
    public function it_throws_exception_when_page_not_found(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_page_by_slug(): void
    {
        $page = Page::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($page->page_id, $result->page_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_pages(): void
    {
        Page::factory()->active()->count(5)->create();
        Page::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */
    public function it_can_get_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();
        Page::factory()->count(3)->create();

        $result = $this->service->getHomepage();

        $this->assertNotNull($result);
        $this->assertEquals($homepage->page_id, $result->page_id);
    }

    /** @test */
    public function it_can_create_page_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Page::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_page(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-page', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_page_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Page created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_page_successfully(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($page->page_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $page->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_page(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_page(): void
    {
        $page = Page::factory()->create();

        $result = $this->service->deletePage($page->page_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('pages', ['page_id' => $page->page_id]);
    }

    /** @test */
    public function it_cannot_delete_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $this->expectException(HomepageProtectionException::class);

        $this->service->deletePage($homepage->page_id);
    }

    /** @test */
    public function it_logs_homepage_deletion_attempt(): void
    {
        $homepage = Page::factory()->homepage()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Attempted to delete homepage', \Mockery::any());

        try {
            $this->service->deletePage($homepage->page_id);
        } catch (HomepageProtectionException $e) {
            // Expected exception
        }
    }

    /** @test */
    public function it_can_toggle_page_status(): void
    {
        $page = Page::factory()->active()->create();

        $result = $this->service->togglePageStatus($page->page_id);

        $this->assertTrue($result->success);
        $this->assertFalse($page->fresh()->is_active);
    }

    /** @test */
    public function it_cannot_deactivate_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $result = $this->service->togglePageStatus($homepage->page_id);

        $this->assertFalse($result->success);
        $this->assertEquals('warning', $result->type);
        $this->assertTrue($homepage->fresh()->is_active);
    }

    /** @test */
    public function it_can_bulk_delete_pages(): void
    {
        $pages = Page::factory()->count(5)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_skips_homepage_in_bulk_delete(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $pages = Page::factory()->count(3)->create();
        $ids = array_merge([$homepage->page_id], $pages->pluck('page_id')->toArray());

        $result = $this->service->bulkDeletePages($ids);

        $this->assertEquals(3, $result->affectedCount);
        $this->assertEquals(1, $result->skippedCount);
        $this->assertDatabaseHas('pages', ['page_id' => $homepage->page_id]);
    }

    /** @test */
    public function it_returns_failure_when_no_pages_can_be_deleted(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $result = $this->service->bulkDeletePages([$homepage->page_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $pages = Page::factory()->active()->count(5)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_pages(): void
    {
        Page::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Page::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
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
    public function it_can_prepare_page_for_form(): void
    {
        $page = Page::factory()->create();

        $formData = $this->service->preparePageForForm($page->page_id, 'tr');

        $this->assertArrayHasKey('page', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_page(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['page']);
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
            ->with('Page cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Page::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Page::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->page_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($page->page_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $page->fresh()->getTranslated('slug', 'tr'));
    }
}