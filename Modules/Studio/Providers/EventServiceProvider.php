<?php
namespace Modules\Studio\Providers;

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
        'Modules\Studio\App\Events\ContentSaved' => [
            'Modules\Studio\App\Listeners\ContentSavedListener',
        ],
        'Modules\Studio\App\Events\EditorOpened' => [
            'Modules\Studio\App\Listeners\LogEditorActivity',
        ],
        'Modules\Studio\App\Events\WidgetUpdated' => [
            'Modules\Studio\App\Listeners\ContentSavedListener',
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
    /**
     * Do not configure email verification - handled by app EventServiceProvider
     */
    protected function configureEmailVerification(): void
    {
        // Override to prevent duplicate email verification listeners
    }

}