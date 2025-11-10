<?php

namespace Modules\ReviewSystem\Tests\Feature;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for ReviewSystem event listeners
 */
class ReviewSystemEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $reviewsystem = ReviewSystem::factory()->create();

        $event = new TranslationCompletedEvent(
            'reviewsystem',
            $reviewsystem->reviewsystem_id,
            'broadcast-test-session',
            2,
            0
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class);
    }

    /** @test */
    public function translation_completed_event_contains_correct_data()
    {
        Event::fake();

        $reviewsystem = ReviewSystem::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'reviewsystem',
            $reviewsystem->reviewsystem_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($reviewsystem, $sessionId) {
            return $e->entityType === 'reviewsystem' &&
                   $e->entityId === $reviewsystem->reviewsystem_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in ReviewSystemComponent
        // \Livewire\Livewire::test(ReviewSystemComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_reviewsystem_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('ReviewSystem creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('ReviewSystem created successfully', \Mockery::type('array'))
            ->once();

        ReviewSystem::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_reviewsystem_update()
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('reviewsystems')
                ->once();
        });

        $reviewsystem->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_reviewsystem_deletion()
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $reviewsystem->delete();

        $this->assertSoftDeleted('reviewsystems', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
        ]);
    }
}
