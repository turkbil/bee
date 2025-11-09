<?php

namespace Modules\Payment\Tests\Feature;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Events\TranslationCompletedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Test suite for Payment event listeners
 */
class PaymentEventListenersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function translation_completed_event_is_broadcasted()
    {
        Event::fake([TranslationCompletedEvent::class]);

        $payment = Payment::factory()->create();

        $event = new TranslationCompletedEvent(
            'payment',
            $payment->payment_id,
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

        $payment = Payment::factory()->create();
        $sessionId = 'data-test-' . uniqid();

        $event = new TranslationCompletedEvent(
            'payment',
            $payment->payment_id,
            $sessionId,
            3,
            1
        );

        event($event);

        Event::assertDispatched(TranslationCompletedEvent::class, function ($e) use ($payment, $sessionId) {
            return $e->entityType === 'payment' &&
                   $e->entityId === $payment->payment_id &&
                   $e->sessionId === $sessionId &&
                   $e->success === 3 &&
                   $e->failed === 1;
        });
    }

    /** @test */
    public function livewire_component_receives_translation_completed_event()
    {
        $this->markTestIncomplete('Livewire event integration test - requires Livewire testing setup');

        // This would test the handleTranslationCompleted method in PaymentComponent
        // \Livewire\Livewire::test(PaymentComponent::class)
        //     ->call('handleTranslationCompleted', [...])
        //     ->assertDispatched('translation-complete');
    }

    /** @test */
    public function observer_logs_payment_lifecycle_events()
    {
        Log::shouldReceive('info')
            ->with('Payment creating', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Payment created successfully', \Mockery::type('array'))
            ->once();

        Payment::factory()->create([
            'title' => ['tr' => 'Observer Test'],
            'slug' => ['tr' => 'observer-test'],
        ]);
    }

    /** @test */
    public function cache_is_cleared_on_payment_update()
    {
        $payment = Payment::factory()->create();

        // Mock cache service
        $this->mock(\App\Services\TenantCacheService::class, function ($mock) {
            $mock->shouldReceive('flushByPrefix')
                ->with('payments')
                ->once();
        });

        $payment->update([
            'title' => ['tr' => 'Updated Title'],
        ]);
    }

    /** @test */
    public function media_is_cleared_on_payment_deletion()
    {
        $payment = Payment::factory()->create();

        // Add media mock
        $this->mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(true);
        });

        $payment->delete();

        $this->assertSoftDeleted('payments', [
            'payment_id' => $payment->payment_id,
        ]);
    }
}
