<?php

namespace Modules\Favorite\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Favorite\App\Services\FavoriteService;

class FavoriteServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Favorite';
    protected string $moduleNameLower = 'favorite';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->singleton(FavoriteService::class, function ($app) {
            return new FavoriteService();
        });
    }
}
