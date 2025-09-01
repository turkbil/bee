<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\QueueManager;
use App\Queue\ResilientRedisQueue;

class QueueResilienceServiceProvider extends ServiceProvider
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
        // Replace default Redis queue with resilient version
        $this->app->resolving('queue', function (QueueManager $manager) {
            $manager->extend('redis', function ($config, $name) {
                $redis = $this->app['redis']->connection($config['connection'] ?? 'default');
                
                return new ResilientRedisQueue(
                    $redis,
                    $config['queue'],
                    $config['connection'] ?? 'default',
                    $config['retry_after'] ?? 90,
                    $config['block_for'] ?? null,
                    $config['after_commit'] ?? null
                );
            });
        });
    }
}