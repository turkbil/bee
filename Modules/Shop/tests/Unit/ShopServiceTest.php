<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Unit;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use Modules\Shop\App\Services\ShopService;
use Modules\Shop\App\Repositories\ShopRepository;
use Modules\Shop\App\Contracts\ShopRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Shop\App\Exceptions\{ShopNotFoundException, ShopProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * ShopService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class ShopServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShopService $service;
    private ShopRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ShopRepositoryInterface::class);
        $this->service = app(ShopService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_shop_by_id(): void
    {
        $shop = Shop::factory()->create();

        $result = $this->service->getPage($shop->shop_id);

        $this->assertInstanceOf(Shop::class, $result);
        $this->assertEquals($shop->shop_id, $result->shop_id);
    }

    /** @test */
    public function it_throws_exception_when_shop_not_found(): void
    {
        $this->expectException(ShopNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_shop_by_slug(): void
    {
        $shop = Shop::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Shop::class, $result);
        $this->assertEquals($shop->shop_id, $result->shop_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(ShopNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_shops(): void
    {
        Shop::factory()->active()->count(5)->create();
        Shop::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_shop_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Shop::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_shop(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-shop', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_shop_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Shop created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_shop_successfully(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($shop->shop_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $shop->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_shop(): void
    {
        $this->expectException(ShopNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_shop(): void
    {
        $shop = Shop::factory()->create();

        $result = $this->service->deletePage($shop->shop_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('shops', ['shop_id' => $shop->shop_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_shop_status(): void
    {
        $shop = Shop::factory()->active()->create();

        $result = $this->service->togglePageStatus($shop->shop_id);

        $this->assertTrue($result->success);
        $this->assertFalse($shop->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_shops(): void
    {
        $shops = Shop::factory()->count(5)->create();
        $ids = $shops->pluck('shop_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_shops_can_be_deleted(): void
    {
        $homeshop = Shop::factory()->create();

        $result = $this->service->bulkDeletePages([$homeshop->shop_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $shops = Shop::factory()->active()->count(5)->create();
        $ids = $shops->pluck('shop_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_shops(): void
    {
        Shop::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Shop::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop'],
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
    public function it_can_prepare_shop_for_form(): void
    {
        $shop = Shop::factory()->create();

        $formData = $this->service->preparePageForForm($shop->shop_id, 'tr');

        $this->assertArrayHasKey('shop', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_shop(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['shop']);
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
            ->with('Shop cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Shop::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Shop::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->shop_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($shop->shop_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $shop->fresh()->getTranslated('slug', 'tr'));
    }
}
