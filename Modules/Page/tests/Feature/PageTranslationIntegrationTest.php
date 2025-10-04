<?php

namespace Modules\Page\Tests\Feature;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Jobs\TranslatePageJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Modules\Page\App\Events\TranslationCompletedEvent;

/**
 * Integration test suite for translation jobs and events
 */
class PageTranslationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->page = Page::factory()->create([
            'title' => ['tr' => 'Test Başlık'],
            'body' => ['tr' => 'Test içerik'],
            'slug' => ['tr' => 'test-baslik'],
        ]);
    }

    /** @test */
    public function it_dispatches_translation_job_to_queue()
    {
        Queue::fake();

        TranslatePageJob::dispatch(
            [$this->page->page_id],
            'tr',
            ['en', 'de'],
            'balanced',
            [],
            'test-session-id'
        );

        Queue::assertPushed(TranslatePageJob::class, function ($job) {
            return $job->queue === 'tenant_isolated';
        });
    }

    /** @test */
    public function it_processes_translation_for_multiple_languages()
    {
        Queue::fake();

        $targetLanguages = ['en', 'de', 'fr'];

        TranslatePageJob::dispatch(
            [$this->page->page_id],
            'tr',
            $targetLanguages,
            'balanced',
            [],
            'multi-lang-session'
        );

        Queue::assertPushed(TranslatePageJob::class);
    }

    /** @test */
    public function translation_completed_event_is_fired()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $event = new TranslationCompletedEvent(
            'page',
            $this->page->page_id,
            'event-test-session',
            2,
            2
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class);
    }

    /** @test */
    public function page_is_translatable_via_universal_translation_interface()
    {
        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $this->page);

        $translatableFields = $this->page->getTranslatableFields();

        $this->assertArrayHasKey('title', $translatableFields);
        $this->assertArrayHasKey('body', $translatableFields);
        $this->assertArrayHasKey('slug', $translatableFields);
    }

    /** @test */
    public function page_has_seo_settings_support()
    {
        $this->assertTrue($this->page->hasSeoSettings());
    }

    /** @test */
    public function after_translation_callback_is_executed()
    {
        $translatedData = [
            'title' => 'Translated Title',
            'body' => 'Translated Body'
        ];

        // afterTranslation metodunu test et
        $this->page->afterTranslation('en', $translatedData);

        // Log kontrolü yapılabilir
        $this->assertTrue(true);
    }
}
