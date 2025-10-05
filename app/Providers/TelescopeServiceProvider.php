<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Illuminate\Support\ServiceProvider;

class TelescopeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists('Laravel\Telescope\Telescope')) {
            // Telescope::night();

            $this->hideSensitiveRequestDetails();

            $isLocal = $this->app->environment('local');

            Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
                // Local ortamda her şeyi kaydet
                if ($isLocal) {
                    return true;
                }

                // Production'da da her şeyi kaydet (admin kullanıcılar için)
                // Sadece önemli verileri filtrelemek için ignore_paths kullanacağız
                return true;
            });
        }
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if (!class_exists('Laravel\Telescope\Telescope')) {
            return;
        }
        
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->gate();
        $this->authorization();
    }

    /**
     * Configure the Telescope authorization services.
     */
    protected function authorization(): void
    {
        // Middleware zaten kontrol yapıyor, burada sadece true dön
        Telescope::auth(function ($request) {
            return true;
        });
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            // Local ortamda herkes erişebilir
            if (app()->environment('local')) {
                return true;
            }

            // Root ve Admin rolüne sahip kullanıcılar erişebilir
            return $user->hasAnyRole(['root', 'admin']);
        });
    }
}
