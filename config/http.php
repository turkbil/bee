<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the HTTP client settings for your application.
    |
    */

    'timeout' => env('HTTP_TIMEOUT', 300),
    'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 30),
    'retry' => [
        'times' => env('HTTP_RETRY_TIMES', 3),
        'sleep' => env('HTTP_RETRY_SLEEP', 100),
    ],
];