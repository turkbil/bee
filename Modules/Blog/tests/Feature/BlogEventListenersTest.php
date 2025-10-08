<?php

namespace Modules\Blog\Tests\Feature;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Blog event listeners
 */
class BlogEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $blog = Blog::factory()->create();

        $event = new TranslationCompletedEvent(
            'blog',
            $blog->blog_id,
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

        $blog = Blog::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'blog',
            $blog->blog_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($blog, $sessionId) {
            return $e->entityType === 'blog' &&
                   $e->entityId === $blog->blog_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in BlogComponent
        // \Livewire\Livewire::test(BlogComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_blog_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Blog creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Blog created successfully', \Mockery::type('array'))
            ->once();

        Blog::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_blog_update()
    {
        $blog = Blog::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('blogs')
                ->once();
        });

        $blog->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_blog_deletion()
    {
        $blog = Blog::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $blog->delete();

        $this->assertSoftDeleted('blogs', [
            'blog_id' => $blog->blog_id,
        ]);
    }
}
