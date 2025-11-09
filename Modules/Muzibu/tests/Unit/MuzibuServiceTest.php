<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Unit;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Services\MuzibuService;
use Modules\Muzibu\App\Repositories\MuzibuRepository;
use Modules\Muzibu\App\Contracts\MuzibuRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Muzibu\App\Exceptions\{MuzibuNotFoundException, MuzibuProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * MuzibuService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class MuzibuServiceTest extends TestCase
{
    use RefreshDatabase;

    private MuzibuService $service;
    private MuzibuRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(MuzibuRepositoryInterface::class);
        $this->service = app(MuzibuService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_muzibu_by_id(): void
    {
        $muzibu = Muzibu::factory()->create();

        $result = $this->service->getPage($muzibu->muzibu_id);

        $this->assertInstanceOf(Muzibu::class, $result);
        $this->assertEquals($muzibu->muzibu_id, $result->muzibu_id);
    }

    /** @test */
    public function it_throws_exception_when_muzibu_not_found(): void
    {
        $this->expectException(MuzibuNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_muzibu_by_slug(): void
    {
        $muzibu = Muzibu::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Muzibu::class, $result);
        $this->assertEquals($muzibu->muzibu_id, $result->muzibu_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(MuzibuNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_muzibus(): void
    {
        Muzibu::factory()->active()->count(5)->create();
        Muzibu::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_muzibu_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Muzibu::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_muzibu(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-muzibu', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_muzibu_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_muzibu_successfully(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($muzibu->muzibu_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $muzibu->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_muzibu(): void
    {
        $this->expectException(MuzibuNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_muzibu(): void
    {
        $muzibu = Muzibu::factory()->create();

        $result = $this->service->deletePage($muzibu->muzibu_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_muzibu_status(): void
    {
        $muzibu = Muzibu::factory()->active()->create();

        $result = $this->service->togglePageStatus($muzibu->muzibu_id);

        $this->assertTrue($result->success);
        $this->assertFalse($muzibu->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_muzibus(): void
    {
        $muzibus = Muzibu::factory()->count(5)->create();
        $ids = $muzibus->pluck('muzibu_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_muzibus_can_be_deleted(): void
    {
        $homemuzibu = Muzibu::factory()->create();

        $result = $this->service->bulkDeletePages([$homemuzibu->muzibu_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $muzibus = Muzibu::factory()->active()->count(5)->create();
        $ids = $muzibus->pluck('muzibu_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_muzibus(): void
    {
        Muzibu::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Muzibu::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu'],
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
    public function it_can_prepare_muzibu_for_form(): void
    {
        $muzibu = Muzibu::factory()->create();

        $formData = $this->service->preparePageForForm($muzibu->muzibu_id, 'tr');

        $this->assertArrayHasKey('muzibu', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_muzibu(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['muzibu']);
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
            ->with('Muzibu cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Muzibu::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Muzibu::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->muzibu_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($muzibu->muzibu_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $muzibu->fresh()->getTranslated('slug', 'tr'));
    }
}
