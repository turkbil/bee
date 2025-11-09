<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Unit;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PaymentService;
use Modules\Payment\App\Repositories\PaymentRepository;
use Modules\Payment\App\Contracts\PaymentRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use Modules\Payment\App\Exceptions\{PaymentNotFoundException, PaymentProtectionException};
use Illuminate\Support\Facades\Log;

/**
 * PaymentService Unit Tests
 *
 * Business logic katmanının tüm operasyonlarını,
 * exception handling ve slug generation gibi işlemleri test eder.
 */
class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;
    private PaymentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PaymentRepositoryInterface::class);
        $this->service = app(PaymentService::class);

        Log::spy(); // Log facade'ini mock et
    }

    /** @test */
    public function it_can_get_payment_by_id(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->service->getPage($payment->payment_id);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->payment_id, $result->payment_id);
    }

    /** @test */
    public function it_throws_exception_when_payment_not_found(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $this->service->getPage(999);
    }

    /** @test */
    public function it_can_get_payment_by_slug(): void
    {
        $payment = Payment::factory()->active()->create([
            'slug' => ['tr' => 'test-slug', 'en' => 'test-slug']
        ]);

        $result = $this->service->getPageBySlug('test-slug', 'tr');

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->payment_id, $result->payment_id);
    }

    /** @test */
    public function it_throws_exception_when_slug_not_found(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $this->service->getPageBySlug('nonexistent-slug', 'tr');
    }

    /** @test */
    public function it_can_get_active_payments(): void
    {
        Payment::factory()->active()->count(5)->create();
        Payment::factory()->inactive()->count(3)->create();

        $result = $this->service->getActivePages();

        $this->assertCount(5, $result);
    }

    /** @test */

    /** @test */
    public function it_can_create_payment_successfully(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(Payment::class, $result->data);
        $this->assertEquals('Test Sayfası', $result->data->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_generates_slug_automatically_when_creating_payment(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment'],
            'body' => ['tr' => '<p>İçerik</p>', 'en' => '<p>Content</p>'],
            'is_active' => true,
        ];

        $result = $this->service->createPage($data);

        $this->assertEquals('test-sayfasi', $result->data->getTranslated('slug', 'tr'));
        $this->assertEquals('test-payment', $result->data->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_logs_payment_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Payment created', \Mockery::any());

        $data = [
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true,
        ];

        $this->service->createPage($data);
    }

    /** @test */
    public function it_can_update_payment_successfully(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title']
        ]);

        $result = $this->service->updatePage($payment->payment_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('Yeni Başlık', $payment->fresh()->getTranslated('title', 'tr'));
    }

    /** @test */
    public function it_throws_exception_when_updating_nonexistent_payment(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $this->service->updatePage(999, ['title' => ['tr' => 'Test']]);
    }

    /** @test */
    public function it_can_delete_regular_payment(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->service->deletePage($payment->payment_id);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('payments', ['payment_id' => $payment->payment_id]);
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_toggle_payment_status(): void
    {
        $payment = Payment::factory()->active()->create();

        $result = $this->service->togglePageStatus($payment->payment_id);

        $this->assertTrue($result->success);
        $this->assertFalse($payment->fresh()->is_active);
    }

    /** @test */

    /** @test */
    public function it_can_bulk_delete_payments(): void
    {
        $payments = Payment::factory()->count(5)->create();
        $ids = $payments->pluck('payment_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */

    /** @test */
    public function it_returns_failure_when_no_payments_can_be_deleted(): void
    {
        $homepayment = Payment::factory()->create();

        $result = $this->service->bulkDeletePages([$homepayment->payment_id]);

        $this->assertFalse($result->success);
    }

    /** @test */
    public function it_can_bulk_toggle_status(): void
    {
        $payments = Payment::factory()->active()->count(5)->create();
        $ids = $payments->pluck('payment_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);
    }

    /** @test */
    public function it_can_search_payments(): void
    {
        Payment::factory()->active()->create([
            'title' => ['tr' => 'Laravel Öğreniyorum', 'en' => 'Learning Laravel']
        ]);
        Payment::factory()->active()->create([
            'title' => ['tr' => 'PHP Temelleri', 'en' => 'PHP Basics']
        ]);

        $results = $this->service->searchPages('Laravel', ['tr', 'en']);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_prepares_seo_data_correctly(): void
    {
        $data = [
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment'],
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
    public function it_can_prepare_payment_for_form(): void
    {
        $payment = Payment::factory()->create();

        $formData = $this->service->preparePageForForm($payment->payment_id, 'tr');

        $this->assertArrayHasKey('payment', $formData);
        $this->assertArrayHasKey('seoData', $formData);
        $this->assertArrayHasKey('tabCompletion', $formData);
        $this->assertArrayHasKey('tabConfig', $formData);
        $this->assertArrayHasKey('seoLimits', $formData);
    }

    /** @test */
    public function it_returns_empty_form_data_for_new_payment(): void
    {
        $formData = $this->service->getEmptyFormData('tr');

        $this->assertNull($formData['payment']);
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
            ->with('Payment cache cleared', \Mockery::any());

        $this->service->clearCache();
    }

    /** @test */
    public function it_handles_slug_uniqueness_in_updates(): void
    {
        $existingPage = Payment::factory()->create([
            'slug' => ['tr' => 'existing-slug', 'en' => 'existing-slug']
        ]);

        $newPage = Payment::factory()->create([
            'slug' => ['tr' => 'new-slug', 'en' => 'new-slug']
        ]);

        // Existing slug'ı kullanmaya çalış - sistem otomatik unique yapmalı
        $result = $this->service->updatePage($newPage->payment_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_preserves_existing_slugs_when_updating(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug']
        ]);

        $result = $this->service->updatePage($payment->payment_id, [
            'title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']
        ]);

        // Slug değişmeden kalmalı
        $this->assertEquals('custom-slug', $payment->fresh()->getTranslated('slug', 'tr'));
    }
}
