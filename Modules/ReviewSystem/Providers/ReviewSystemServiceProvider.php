<?php

namespace Modules\ReviewSystem\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ReviewSystem\App\Services\ReviewService;

class ReviewSystemServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ReviewSystem';
    protected string $moduleNameLower = 'reviewsystem';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->singleton(ReviewService::class, function ($app) {
            return new ReviewService();
        });
    }
}
