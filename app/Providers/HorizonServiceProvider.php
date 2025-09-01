<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            // Allow access in local environment
            if (app()->environment('local')) {
                return true;
            }
            
            // Check for authenticated admin users with appropriate permissions
            if (!$user) {
                return false;
            }
            
            // Allow users with root or admin roles to access Horizon
            return $user->hasRole(['root', 'admin']) || 
                   in_array($user->email, [
                       'nurullah@nurullah.net',
                       'info@turkbilisim.com.tr',
                       'laravel@test'
                   ]);
        });
    }
}
