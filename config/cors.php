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

    // Note: HLS endpoints moved to /hls/* path (not /api/*) to avoid CORS middleware conflict
    // Laravel CORS with supports_credentials=true adds Access-Control-Allow-Credentials: true
    // This conflicts with Access-Control-Allow-Origin: * - browsers reject this combination
    // HLS routes now handle CORS directly in controller without middleware interference
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [
        // Dinamik: Tüm tenant domain'lerini kabul et (supports_credentials=true için gerekli)
        '/^https?:\/\/(www\.)?[a-zA-Z0-9\-]+\.(com|com\.tr|net|org)(:\d+)?$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
