<?php

return [
    /*
     * Determine if the response cache middleware should be enabled.
     */
    'enabled' => env('RESPONSE_CACHE_ENABLED', true), // AUTH-AWARE CACHE AKTİF

    /*
     *  The given class will determinate if a request should be cached. The
     *  default class will cache all successful GET-requests.
     *
     *  You can provide your own class given that it implements the
     *  CacheProfile interface.
     */
    'cache_profile' => \App\Services\TenantCacheProfile::class,

    /*
     *  Optionally, you can specify a header that will force a cache bypass.
     *  This can be useful to monitor the performance of your application.
     */
    'cache_bypass_header' => [
        'name' => env('CACHE_BYPASS_HEADER_NAME', null),
        'value' => env('CACHE_BYPASS_HEADER_VALUE', null),
    ],

    /*
     * When using the default CacheRequestFilter this setting controls the
     * default number of seconds responses must be cached.
     */
    'cache_lifetime_in_seconds' => (int) env('RESPONSE_CACHE_LIFETIME', 60 * 60 * 24 * 7),

    /*
     * This setting determines if a http header named with the cache time
     * should be added to a cached response. This can be handy when
     * debugging.
     */
    'add_cache_time_header' => true,

    /*
     * This setting determines the name of the http header that contains
     * the time at which the response was cached
     */
    'cache_time_header_name' => env('RESPONSE_CACHE_HEADER_NAME', 'laravel-responsecache'),

    /*
     * This setting determines if a http header named with the cache age
     * should be added to a cached response. This can be handy when
     * debugging.
     * ONLY works when "add_cache_time_header" is also active!
     */
    'add_cache_age_header' => env('RESPONSE_CACHE_AGE_HEADER', false),

    /*
     * This setting determines the name of the http header that contains
     * the age of cache
     */
    'cache_age_header_name' => env('RESPONSE_CACHE_AGE_HEADER_NAME', 'laravel-responsecache-age'),

    /*
     * Here you may define the cache store that should be used to store
     * requests. This can be the name of any store that is
     * configured in app/config/cache.php
     */
    'cache_store' => env('RESPONSE_CACHE_DRIVER', 'redis'),

    /*
     * Here you may define replacers that dynamically replace content from the response.
     * Each replacer must implement the Replacer interface.
     */
    'replacers' => [
        \Spatie\ResponseCache\Replacers\CsrfTokenReplacer::class,
    ],

    /*
     * If the cache driver you configured supports tags, you may specify a tag name
     * here. All responses will be tagged. When clearing the responsecache only
     * items with that tag will be flushed.
     *
     * You may use a string or an array here.
     */
    'cache_tag' => 'central_response_cache', // Default: central, tenant'lar kendi tag'ini alır

    /*
     * This class is responsible for generating a hash for a request. This hash
     * is used to look up a cached response.
     */
    'hasher' => \App\Services\AuthAwareHasher::class,
    
    /*
     * Enable role-based cache variation (experimental)
     */
    'role_based_cache' => env('RESPONSE_CACHE_ROLE_BASED', false),

    /*
     * This class is responsible for serializing responses.
     */
    'serializer' => \Spatie\ResponseCache\Serializers\DefaultSerializer::class,

    /*
     * These paths will not be cached
     */
    'skip_caching_paths' => [
        'admin/*',
        'livewire/*',
    ],
    
    /*
     * Configurable excluded paths for TenantCacheProfile
     */
    'excluded_paths' => [
        'admin/*',
        'language/*',
        'debug-lang/*',
        'debug/*',
        'test/*',
        'livewire/*',
        'api/*',
        'auth/*',
        'login',
        'logout',
        'register',
        'password/*',
        'forgot-password',
        'reset-password',
        'profile',
        'profile/*',
        'avatar/*',
        'user/*',
        'account/*'
    ],
];