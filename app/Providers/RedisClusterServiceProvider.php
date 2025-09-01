<?php

namespace App\Providers;

use App\Services\RedisClusterService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class RedisClusterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(RedisClusterService::class, function ($app) {
            return new RedisClusterService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Redis cluster health monitoring
        $this->app->booted(function () {
            if (config('redis_cluster.clustering.enabled') && config('redis_cluster.monitoring.enabled')) {
                $this->scheduleHealthChecks();
            }
        });

        // RedisCluster service provider booted
    }

    /**
     * Redis cluster health check'lerini zamanla
     */
    protected function scheduleHealthChecks()
    {
        $service = app(RedisClusterService::class);
        
        // Her 30 saniyede bir health check
        $this->app['events']->listen('Illuminate\Console\Events\CommandFinished', function () use ($service) {
            try {
                $health = $service->healthCheck();
                
                if ($health['status'] === 'critical') {
                    Log::critical('Redis cluster critical health status', $health);
                } elseif ($health['status'] === 'degraded') {
                    Log::warning('Redis cluster degraded health status', $health);
                }
                
                // Rebalancing check
                $rebalancing = $service->checkRebalancing();
                if ($rebalancing['needs_rebalancing']) {
                    Log::warning('Redis cluster needs rebalancing', $rebalancing);
                }
                
            } catch (\Exception $e) {
                Log::error('Redis cluster health check failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}