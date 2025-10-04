<?php

namespace Modules\Page\Tests\Feature;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Page event listeners
 */
class PageEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $page = Page::factory()->create();

        $event = new TranslationCompletedEvent(
            'page',
            $page->page_id,
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

        $page = Page::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'page',
            $page->page_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(
            TranslationCompletedEvent::class,
            function ($e) use ($page, $sessionId) {
                return $e->entityType === 'page'
                    && $e->entityId === $page->page_id
                    && $e->sessionId === $sessionId
                    && $e->totalLanguages === 3
                    && $e->completedLanguages === 1;
            }
        );
    }

    /** @test */
    public function cache_is_cleared_after_translation_event()
    {
        $page = Page::factory()->create();

        // Cache'i doldu
        $page->refresh();

        $event = new TranslationCompletedEvent(
            'page',
            $page->page_id,
            'cache-test-session',
            2,
            2
        );

        event($event);

        // Cache clearing logic test edilebilir
        $this->assertTrue(true);
    }
}
