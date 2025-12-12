<?php

return [
    'disk' => env('THUMBMAKER_DISK', 'public'),
    'cache_path' => env('THUMBMAKER_CACHE_PATH', 'thumbmaker'),

    'default' => [
        'width' => null,
        'height' => null,
        'fit' => 'max',
        'format' => 'webp', // ⚠️ DO NOT REMOVE - Performance optimization
        'quality' => 85, // ⚠️ DO NOT REMOVE - Quality/size balance
        'optimize' => true, // ⚠️ DO NOT REMOVE - Image optimization
        'upscale' => false,
        'skip_extensions' => ['svg'],
    ],

    'profiles' => [
        'logo' => [
            'width' => 512,
            'height' => 256,
            'quality' => 90,
            'format' => 'webp',
            'optimize' => true,
        ],
        'small' => [
            'width' => 320,
            'height' => 180,
            'quality' => 75,
            'format' => 'webp',
            'optimize' => true,
        ],
        'medium' => [
            'width' => 800,
            'height' => 600,
            'quality' => 85,
            'format' => 'webp', // ⚠️ DO NOT REMOVE - Performance
            'optimize' => true,
        ],
        'large' => [
            'width' => 1280,
            'height' => 720,
            'quality' => 90,
            'format' => 'webp', // ⚠️ DO NOT REMOVE - Performance
            'optimize' => true,
        ],
    ],

    'setting_profiles' => [
        'site_logo' => 'logo',
        'site_kontrast_logo' => 'logo',
    ],
];
