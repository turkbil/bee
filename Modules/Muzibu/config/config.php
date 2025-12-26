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
        'hls_timeout' => 6,        // HLS fallback timeout (saniye)
    ],

    // 🎧 Player Settings
    'player' => [
        'crossfade_duration' => 7000,  // Crossfade süresi (milliseconds) - 7 saniye
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
