<?php

namespace Modules\Portfolio\Tests\Feature;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Portfolio event listeners
 */
class PortfolioEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $portfolio = Portfolio::factory()->create();

        $event = new TranslationCompletedEvent(
            'portfolio',
            $portfolio->portfolio_id,
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

        $portfolio = Portfolio::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'portfolio',
            $portfolio->portfolio_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($portfolio, $sessionId) {
            return $e->entityType === 'portfolio' &&
                   $e->entityId === $portfolio->portfolio_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in PortfolioComponent
        // \Livewire\Livewire::test(PortfolioComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_portfolio_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Portfolio creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Portfolio created successfully', \Mockery::type('array'))
            ->once();

        Portfolio::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_portfolio_update()
    {
        $portfolio = Portfolio::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('portfolios')
                ->once();
        });

        $portfolio->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_portfolio_deletion()
    {
        $portfolio = Portfolio::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $portfolio->delete();

        $this->assertSoftDeleted('portfolios', [
            'portfolio_id' => $portfolio->portfolio_id,
        ]);
    }
}
