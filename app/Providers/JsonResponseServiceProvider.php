<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;
use App\Http\SafeJsonResponse;

/**
 * JSON Response Service Provider
 *
 * Overrides Laravel's default JsonResponse with our SafeJsonResponse
 * that automatically sanitizes UTF-8 encoding issues
 */
class JsonResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Override the response factory's json method
        $this->app->singleton(ResponseFactory::class, function ($app) {
            return new class($app) extends ResponseFactory {
                /**
                 * Create a new JSON response instance.
                 *
                 * @param  mixed  $data
                 * @param  int  $status
                 * @param  array  $headers
                 * @param  int  $options
                 * @return \App\Http\SafeJsonResponse
                 */
                public function json($data = [], $status = 200, array $headers = [], $options = 0)
                {
                    return new SafeJsonResponse($data, $status, $headers, $options);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Bind SafeJsonResponse as the default JsonResponse
        $this->app->bind(
            \Illuminate\Http\JsonResponse::class,
            SafeJsonResponse::class
        );
    }
}
