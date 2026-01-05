<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // CORS middleware artık domains tablosundan spesifik domain listesi kullanıyor
    // Wildcard (*) yerine gerçek domain listesi kullanıldığı için
    // supports_credentials=true ile uyumlu hale geldi
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'storage/*',
        'stream/*',  // HLS streaming endpoints
        'hls/*',     // Alternative HLS path
        'admin/ai/profile/generate-story-stream',  // AI streaming endpoint
    ],

    'allowed_methods' => ['*'],

    // Wildcard - tüm tenant domain'leri için geçerli
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
