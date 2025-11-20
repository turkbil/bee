<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stock Photo Providers
    |--------------------------------------------------------------------------
    |
    | API keys for stock photo providers
    |
    */

    'providers' => [
        'pexels' => [
            'api_key' => env('PEXELS_API_KEY'),
        ],

        'unsplash' => [
            'access_key' => env('UNSPLASH_ACCESS_KEY'),
        ],

        'pixabay' => [
            'api_key' => env('PIXABAY_API_KEY'),
        ],

        'dalle' => [
            'api_key' => env('OPENAI_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Strategy
    |--------------------------------------------------------------------------
    |
    | Default provider strategy when tenant settings not found
    |
    | Options: 'free', 'random', 'specific', 'fallback'
    |
    */

    'default_strategy' => env('MEDIA_PROVIDER_STRATEGY', 'free'),

    /*
    |--------------------------------------------------------------------------
    | Default Providers
    |--------------------------------------------------------------------------
    |
    | Default provider order (comma-separated)
    |
    */

    'default_providers' => env('MEDIA_PROVIDERS', 'pexels,unsplash,pixabay'),

];
