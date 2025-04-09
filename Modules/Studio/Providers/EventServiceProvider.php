<?php
namespace Modules\Studio\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Modules\Studio\Events\StudioEditorOpened' => [
            'Modules\Studio\Listeners\StudioEditorOpenedListener',
        ],
        'Modules\Studio\Events\StudioContentSaved' => [
            'Modules\Studio\Listeners\StudioContentSavedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}