<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Storage\DatabaseEntriesRepository;

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

            // 🔥 Multi-tenant FIX: Telescope storage'ı HER ZAMAN central DB kullanmalı
            // Tenant context'inde bile central'a yazması için repository'yi override et
            $this->app->singleton(
                \Laravel\Telescope\Contracts\EntriesRepository::class,
                function ($app) {
                    return new DatabaseEntriesRepository(
                        'central', // Her zaman central connection kullan
                        config('telescope.storage.database.chunk', 1000)
                    );
                }
            );

            // Telescope tag'lerine tenant bilgisi ekle
            Telescope::tag(function (IncomingEntry $entry) {
                $tags = [];

                // Tenant bilgisini tag olarak ekle
                if (function_exists('tenant') && tenant()) {
                    $tags[] = 'tenant:' . tenant()->id;

                    // Domain bilgisini de ekle
                    $domains = tenant()->domains ?? [];
                    if (!empty($domains)) {
                        $domain = is_object($domains[0]) ? $domains[0]->domain : $domains[0];
                        $tags[] = 'domain:' . $domain;
                    }
                } else {
                    $tags[] = 'tenant:central';
                }

                return $tags;
            });

            $isLocal = $this->app->environment('local');

            Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
                // Local ortamda her şeyi kaydet
                if ($isLocal) {
                    return true;
                }

                // Production'da da her şeyi kaydet (admin kullanıcılar için)
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
