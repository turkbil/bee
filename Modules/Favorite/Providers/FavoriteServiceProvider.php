<?php

namespace Modules\Favorite\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Favorite\App\Services\FavoriteService;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteComponent;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteStatisticsComponent;

class FavoriteServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Favorite';
    protected string $moduleNameLower = 'favorite';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));

        // Route'ları yükle
        $this->loadRoutesFrom(module_path($this->moduleName, 'routes/admin.php'));

        // Views'ları yükle
        $this->loadViewsFrom(module_path($this->moduleName, 'resources/views'), $this->moduleNameLower);

        // Livewire Component'leri kaydet
        Livewire::component('favorite-component', FavoriteComponent::class);
        Livewire::component('favorite-statistics-component', FavoriteStatisticsComponent::class);
    }

    public function register(): void
    {
        $this->app->singleton(FavoriteService::class, function ($app) {
            return new FavoriteService();
        });
    }
}
