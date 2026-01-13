<?php

namespace Modules\SettingManagement\Providers;

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

        // Ek başlatma kodlarınızı buraya yazabilirsiniz
    }
    /**
     * Do not configure email verification - handled by app EventServiceProvider
     */
    protected function configureEmailVerification(): void
    {
        // Override to prevent duplicate email verification listeners
    }

}