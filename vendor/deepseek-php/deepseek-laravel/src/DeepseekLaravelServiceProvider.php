<?php

namespace DeepSeek\DeepseekLaravel;

use DeepSeek\DeepSeekClient;
use Illuminate\Support\ServiceProvider;
use DeepSeek\DeepseekLaravel\Exceptions\ApiKeyIsMissing;

class DeepseekLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/deepseek.php' => config_path('deepseek.php'),
            ], 'deepseek');

        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deepseek.php', 'deepseek');

        $this->app->singleton(DeepSeekClient::class, function () {
            $apiKey = config('deepseek.api_key');
            $baseUrl = config('deepseek.base_url');
            $timeout = config('deepseek.timeout');

            if (! is_string($apiKey)) {
                throw ApiKeyIsMissing::create();
            }

            return DeepSeekClient::build($apiKey, $baseUrl, $timeout);
        });
    }
}
