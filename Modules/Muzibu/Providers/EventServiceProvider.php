<?php

namespace Modules\Muzibu\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
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
}
