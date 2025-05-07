<?php
namespace Modules\AI\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Modules\AI\Events\AIMessageSent' => [
            'Modules\AI\Listeners\LogMessageSent',
        ],
        'Modules\AI\Events\AIMessageReceived' => [
            'Modules\AI\Listeners\LogMessageReceived',
        ],
        'Modules\AI\Events\AILimitExceeded' => [
            'Modules\AI\Listeners\LogLimitExceeded',
            'Modules\AI\Listeners\NotifyAdminForLimitExceeded',
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