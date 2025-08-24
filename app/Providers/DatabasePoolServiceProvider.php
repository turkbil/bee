<?php

namespace App\Providers;

use App\Services\DatabaseConnectionPoolService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class DatabasePoolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(DatabaseConnectionPoolService::class, function ($app) {
            try {
                return new DatabaseConnectionPoolService();
            } catch (\Exception $e) {
                Log::warning('DatabaseConnectionPoolService could not be instantiated: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Connection pool cleanup scheduler
        $this->app->booted(function () {
            if (config('database_pool.tenant_pool_settings.cleanup_enabled', true)) {
                $this->scheduleCleanup();
            }
        });

        Log::info('DatabaseConnectionPool service provider booted');
    }

    /**
     * Idle connection cleanup zamanla
     */
    protected function scheduleCleanup()
    {
        $service = app(DatabaseConnectionPoolService::class);
        
        // Her 5 dakikada bir idle connection'ları temizle
        $this->app['events']->listen('Illuminate\Console\Events\CommandFinished', function () use ($service) {
            $service->cleanupIdleConnections();
        });
    }
}