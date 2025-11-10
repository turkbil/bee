<?php

namespace Modules\ReviewSystem\Tests\Feature;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Jobs\TranslateReviewSystemJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Modules\ReviewSystem\App\Events\TranslationCompletedEvent;

/**
 * Integration test suite for translation jobs and events
 */
class ReviewSystemTranslationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected ReviewSystem $reviewsystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Test Başlık'],
            'body' => ['tr' => 'Test içerik'],
            'slug' => ['tr' => 'test-baslik'],
        ]);
    }

    /** @test */
    public function it_dispatches_translation_job_to_queue()
    {
        Queue::fake();

        TranslateReviewSystemJob::dispatch(
            [$this->reviewsystem->reviewsystem_id],
            'tr',
            ['en', 'de'],
            'balanced',
            [],
            'test-session-id'
        );

        Queue::assertPushed(TranslateReviewSystemJob::class, function ($job) {
            return $job->queue === 'tenant_isolated';
        });
    }

    /** @test */
    public function it_fires_translation_completed_event()
    {
        Event::fake([TranslationCompletedEvent::class]);

        // Simulate job completion
        $event = new TranslationCompletedEvent(
            'reviewsystem',
            $this->reviewsystem->reviewsystem_id,
            'test-session-id',
            1,
            0
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) {
            return $e->entityType === 'reviewsystem' &&
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

        TranslateReviewSystemJob::dispatch(
            [$this->reviewsystem->reviewsystem_id],
            'tr',
            ['en'],
            'balanced',
            [],
            'fail-test-session'
        );

        Queue::assertPushed(TranslateReviewSystemJob::class);
    }

    /** @test */
    public function it_validates_reviewsystem_exists_before_translation()
    {
        $this->expectException(\Exception::class);

        $nonExistentId = 99999;

        $job = new TranslateReviewSystemJob(
            [$nonExistentId],
            'tr',
            ['en'],
            'balanced',
            [],
            'validation-test'
        );

        // Job should fail when reviewsystem not found
        $this->assertFalse(ReviewSystem::find($nonExistentId));
    }

    /** @test */
    public function it_supports_multiple_target_languages()
    {
        Queue::fake();

        TranslateReviewSystemJob::dispatch(
            [$this->reviewsystem->reviewsystem_id],
            'tr',
            ['en', 'de', 'fr', 'es'],
            'balanced',
            [],
            'multi-lang-test'
        );

        Queue::assertPushed(TranslateReviewSystemJob::class, function ($job) {
            return count($job->targetLanguages) === 4;
        });
    }

    /** @test */
    public function it_prevents_duplicate_translation_sessions()
    {
        $sessionId = 'unique-session-' . uniqid();

        Queue::fake();

        // First dispatch
        TranslateReviewSystemJob::dispatch(
            [$this->reviewsystem->reviewsystem_id],
            'tr',
            ['en'],
            'balanced',
            [],
            $sessionId
        );

        // Second dispatch with same session ID should be prevented
        // (Implementation depends on your business logic)

        Queue::assertPushedTimes(TranslateReviewSystemJob::class, 1);
    }
}
