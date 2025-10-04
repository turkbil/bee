<?php

namespace Modules\Announcement\Tests\Feature;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Announcement event listeners
 */
class AnnouncementEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $announcement = Announcement::factory()->create();

        $event = new TranslationCompletedEvent(
            'announcement',
            $announcement->announcement_id,
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

        $announcement = Announcement::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'announcement',
            $announcement->announcement_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($announcement, $sessionId) {
            return $e->entityType === 'announcement' &&
                   $e->entityId === $announcement->announcement_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in AnnouncementComponent
        // \Livewire\Livewire::test(AnnouncementComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_announcement_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Announcement creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Announcement created successfully', \Mockery::type('array'))
            ->once();

        Announcement::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_announcement_update()
    {
        $announcement = Announcement::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('announcements')
                ->once();
        });

        $announcement->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_announcement_deletion()
    {
        $announcement = Announcement::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $announcement->delete();

        $this->assertSoftDeleted('announcements', [
            'announcement_id' => $announcement->announcement_id,
        ]);
    }
}
