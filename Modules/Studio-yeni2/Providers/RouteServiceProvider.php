<?php

namespace Modules\Studio\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Studio';
    
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'tenant'])
            ->group(module_path($this->moduleName, 'Routes/web.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateful and protected.
     */
    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'tenant'])
            ->prefix('admin')
            ->name('admin.')
            ->group(module_path($this->moduleName, 'Routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware(['api', 'tenant'])
            ->prefix('api/v1')
            ->name('api.')
            ->group(module_path($this->moduleName, 'Routes/api.php'));
    }
}