<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Muzibu Music Platform Configuration
    |--------------------------------------------------------------------------
    |
    | Tenant 1001 (muzibu.com.tr) için özel ayarlar
    |
    */

    // 🎵 Stream Settings
    'stream' => [
        'preview_duration' => 30,  // Preview süresi (saniye) - Guest & Free
        'hls_timeout' => 6,        // HLS fallback timeout (saniye)
        'preview_chunks' => 3,     // Preview için chunk sayısı
        'buffer_chunks' => 1,      // Buffer chunk sayısı
    ],

    // 🔐 Session Settings
    'session' => [
        'polling_interval' => 30000,  // Session polling (milliseconds)
        'ttl' => 7200,                // Redis session TTL (saniye)
    ],

    // ⚡ Cache Settings
    'cache' => [
        'premium_status_ttl' => 300,  // Premium cache (saniye)
        'song_cache_ttl' => 86400,    // Song cache (saniye - 24 saat)
    ],

    // 📱 Device Settings
    'device' => [
        'default_limit' => 1,  // Fallback device limit
    ],
];
