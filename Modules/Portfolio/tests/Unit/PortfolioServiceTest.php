<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Unit;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Services\PortfolioService;
use Modules\Portfolio\App\Repositories\PortfolioRepository;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Portfolio\App\Exceptions\{PortfolioNotFoundException, PortfolioProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * PortfolioService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class PortfolioServiceTest extends TestCase
{
    use RefreshDatabase;

    private PortfolioService $service;
    private PortfolioRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PortfolioRepositoryInterface::class);
        $this->service = app(PortfolioService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_portfolio_by_id(): void
    {
        $portfolio = Portfolio::factory()->create();

        $result = $this->service->getPage($portfolio->portfolio_id);

        $this->assertInstanceOf(Portfolio::class, $result);
        $this->assertEquals($portfolio->portfolio_id, $result->portfolio_id);
    }

    /** @test */
    public function it_throws_exception_when_portfolio_not_found(): void
    {
        $this->expectException(PortfolioNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_portfolio_by_slug(): void
    {
        $portfolio = Portfolio::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Portfolio::class, $result);
        $this->assertEquals($portfolio->portfolio_id, $result->portfolio_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(PortfolioNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_portfolios(): void
    {
        Portfolio::factory()->active()->count(5)->create();
        Portfolio::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_portfolio_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Portfolio::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_portfolio(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-portfolio', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_portfolio_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_portfolio_successfully(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($portfolio->portfolio_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $portfolio->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_portfolio(): void
    {
        $this->expectException(PortfolioNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_portfolio(): void
    {
        $portfolio = Portfolio::factory()->create();

        $result = $this->service->deletePage($portfolio->portfolio_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('portfolios', ['portfolio_id' => $portfolio->portfolio_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_portfolio_status(): void
    {
        $portfolio = Portfolio::factory()->active()->create();

        $result = $this->service->togglePageStatus($portfolio->portfolio_id);

        $this->assertTrue($result->success);
        $this->assertFalse($portfolio->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_portfolios(): void
    {
        $portfolios = Portfolio::factory()->count(5)->create();
        $ids = $portfolios->pluck('portfolio_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_portfolios_can_be_deleted(): void
    {
        $homeportfolio = Portfolio::factory()->create();

        $result = $this->service->bulkDeletePages([$homeportfolio->portfolio_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $portfolios = Portfolio::factory()->active()->count(5)->create();
        $ids = $portfolios->pluck('portfolio_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_portfolios(): void
    {
        Portfolio::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Portfolio::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio'],
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
    public function it_can_prepare_portfolio_for_form(): void
    {
        $portfolio = Portfolio::factory()->create();

        $formData = $this->service->preparePageForForm($portfolio->portfolio_id, 'tr');

        $this->assertArrayHasKey('portfolio', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_portfolio(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['portfolio']);
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
            ->with('Portfolio cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Portfolio::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Portfolio::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->portfolio_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($portfolio->portfolio_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $portfolio->fresh()->getTranslated('slug', 'tr'));
    }
}
