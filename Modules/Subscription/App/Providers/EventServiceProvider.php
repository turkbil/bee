<?php

namespace Modules\Subscription\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\Subscription\App\Events\SubscriptionExpired::class => [
            \Modules\Subscription\App\Listeners\SendSubscriptionExpiredNotification::class,
        ],
        \Modules\Subscription\App\Events\TrialEnding::class => [
            \Modules\Subscription\App\Listeners\SendTrialEndingNotification::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
