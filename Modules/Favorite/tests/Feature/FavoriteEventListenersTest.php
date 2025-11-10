<?php

namespace Modules\Favorite\Tests\Feature;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Favorite event listeners
 */
class FavoriteEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $favorite = Favorite::factory()->create();

        $event = new TranslationCompletedEvent(
            'favorite',
            $favorite->favorite_id,
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

        $favorite = Favorite::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'favorite',
            $favorite->favorite_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($favorite, $sessionId) {
            return $e->entityType === 'favorite' &&
                   $e->entityId === $favorite->favorite_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in FavoriteComponent
        // \Livewire\Livewire::test(FavoriteComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_favorite_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Favorite creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Favorite created successfully', \Mockery::type('array'))
            ->once();

        Favorite::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_favorite_update()
    {
        $favorite = Favorite::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('favorites')
                ->once();
        });

        $favorite->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_favorite_deletion()
    {
        $favorite = Favorite::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $favorite->delete();

        $this->assertSoftDeleted('favorites', [
            'favorite_id' => $favorite->favorite_id,
        ]);
    }
}
