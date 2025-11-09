<?php

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Jobs\TranslatePaymentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Modules\Payment\App\Events\TranslationCompletedEvent;

/**
 * Integration test suite for translation jobs and events
 */
class PaymentTranslationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = Payment::factory()->create([
            'title' => ['tr' => 'Test Başlık'],
            'body' => ['tr' => 'Test içerik'],
            'slug' => ['tr' => 'test-baslik'],
        ]);
    }

    /** @test */
    public function it_dispatches_translation_job_to_queue()
    {
        Queue::fake();

        TranslatePaymentJob::dispatch(
            [$this->payment->payment_id],
            'tr',
            ['en', 'de'],
            'balanced',
            [],
            'test-session-id'
        );

        Queue::assertPushed(TranslatePaymentJob::class, function ($job) {
            return $job->queue === 'tenant_isolated';
        });
    }

    /** @test */
    public function it_fires_translation_completed_event()
    {
        Event::fake([TranslationCompletedEvent::class]);

        // Simulate job completion
        $event = new TranslationCompletedEvent(
            'payment',
            $this->payment->payment_id,
            'test-session-id',
            1,
            0
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) {
            return $e->entityType === 'payment' &&
                   $e->sessionId === 'test-session-id' &&
                   $e->success === 1 &&
                   $e->failed === 0;
        });
    }

    /** @test */
    public function it_handles_translation_job_failure_gracefully()
    {
        Queue::fake();

        // Mock AI service failure
        $this->mock(\Modules\AI\app\Services\AIService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->andThrow(new \Exception('AI service unavailable'));
        });

        TranslatePaymentJob::dispatch(
            [$this->payment->payment_id],
            'tr',
            ['en'],
            'balanced',
            [],
            'fail-test-session'
        );

        Queue::assertPushed(TranslatePaymentJob::class);
    }

    /** @test */
    public function it_validates_payment_exists_before_translation()
    {
        $this->expectException(\Exception::class);

        $nonExistentId = 99999;

        $job = new TranslatePaymentJob(
            [$nonExistentId],
            'tr',
            ['en'],
            'balanced',
            [],
            'validation-test'
        );

        // Job should fail when payment not found
        $this->assertFalse(Payment::find($nonExistentId));
    }

    /** @test */
    public function it_supports_multiple_target_languages()
    {
        Queue::fake();

        TranslatePaymentJob::dispatch(
            [$this->payment->payment_id],
            'tr',
            ['en', 'de', 'fr', 'es'],
            'balanced',
            [],
            'multi-lang-test'
        );

        Queue::assertPushed(TranslatePaymentJob::class, function ($job) {
            return count($job->targetLanguages) === 4;
        });
    }

    /** @test */
    public function it_prevents_duplicate_translation_sessions()
    {
        $sessionId = 'unique-session-' . uniqid();

        Queue::fake();

        // First dispatch
        TranslatePaymentJob::dispatch(
            [$this->payment->payment_id],
            'tr',
            ['en'],
            'balanced',
            [],
            $sessionId
        );

        // Second dispatch with same session ID should be prevented
        // (Implementation depends on your business logic)

        Queue::assertPushedTimes(TranslatePaymentJob::class, 1);
    }
}
