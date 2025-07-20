<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Global SEO Form Component'i kaydet
        Livewire::component('seo-form-component', \App\Http\Livewire\Components\SeoFormComponent::class);
    }
}