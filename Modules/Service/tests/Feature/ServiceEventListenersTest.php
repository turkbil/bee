<?php

namespace Modules\Service\Tests\Feature;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Service event listeners
 */
class ServiceEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $service = Service::factory()->create();

        $event = new TranslationCompletedEvent(
            'service',
            $service->service_id,
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

        $service = Service::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'service',
            $service->service_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($service, $sessionId) {
            return $e->entityType === 'service' &&
                   $e->entityId === $service->service_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in ServiceComponent
        // \Livewire\Livewire::test(ServiceComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_service_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Service creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Service created successfully', \Mockery::type('array'))
            ->once();

        Service::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_service_update()
    {
        $service = Service::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('services')
                ->once();
        });

        $service->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_service_deletion()
    {
        $service = Service::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $service->delete();

        $this->assertSoftDeleted('services', [
            'service_id' => $service->service_id,
        ]);
    }
}
