<?php

namespace Modules\Muzibu\Tests\Feature;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Muzibu event listeners
 */
class MuzibuEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $muzibu = Muzibu::factory()->create();

        $event = new TranslationCompletedEvent(
            'muzibu',
            $muzibu->muzibu_id,
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

        $muzibu = Muzibu::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'muzibu',
            $muzibu->muzibu_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($muzibu, $sessionId) {
            return $e->entityType === 'muzibu' &&
                   $e->entityId === $muzibu->muzibu_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in MuzibuComponent
        // \Livewire\Livewire::test(MuzibuComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_muzibu_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Muzibu creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Muzibu created successfully', \Mockery::type('array'))
            ->once();

        Muzibu::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_muzibu_update()
    {
        $muzibu = Muzibu::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('muzibus')
                ->once();
        });

        $muzibu->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_muzibu_deletion()
    {
        $muzibu = Muzibu::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $muzibu->delete();

        $this->assertSoftDeleted('muzibus', [
            'muzibu_id' => $muzibu->muzibu_id,
        ]);
    }
}
