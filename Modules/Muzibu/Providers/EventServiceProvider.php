<?php

namespace Modules\Muzibu\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Indicates if events should be discovered.
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Event ve Listener tanımları buraya gelir
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Model Observers
        \Modules\Muzibu\App\Models\Album::observe(\Modules\Muzibu\App\Observers\AlbumObserver::class);
    }
    /**
     * Do not configure email verification - handled by app EventServiceProvider
     */
    protected function configureEmailVerification(): void
    {
        // Override to prevent duplicate email verification listeners
    }

}
