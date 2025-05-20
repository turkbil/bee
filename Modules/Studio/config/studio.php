<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Studio Temel Ayarları
    |--------------------------------------------------------------------------
    */
    'name' => 'Studio Builder',
    'prefix' => 'studio',    // Route öneki
    'middleware' => ['web', 'auth', 'tenant'],  // Modül middleware'i
    
    /*
    |--------------------------------------------------------------------------
    | Editor Ayarları
    |--------------------------------------------------------------------------
    */
    'editor' => [
        'cdn' => [
            'enabled' => false,  // CDN kullanımı
            'version' => '0.21.8', // GrapesJS versiyonu
        ],
        'storage' => [
            'driver' => 'tenant',  // Depolama sürücüsü (tenant veya local)
            'path' => 'studio/assets', // Depolama yolu
        ],
        'panels' => [
            'blocks' => true,   // Blok paneli
            'styles' => true,   // Stil paneli
            'layers' => true,   // Katman paneli
            'traits' => true,   // Özellik paneli
        ],
        'devices' => [
            'desktop' => [
                'width' => '',  // Boş string tam genişlik
                'name' => 'Masaüstü',
            ],
            'tablet' => [
                'width' => '768px',
                'widthMedia' => '992px',
                'name' => 'Tablet',
            ],
            'mobile' => [
                'width' => '320px',
                'widthMedia' => '480px',
                'name' => 'Mobil',
            ],
        ],
        'canvas' => [
            'styles' => [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            ],
            'scripts' => [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js',
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Blok Ayarları
    |--------------------------------------------------------------------------
    */
    'blocks' => [
        'categories' => [],  // Kategoriler boş bırakılıyor, widgetmanagement'dan çekilecek
        'defaults' => [
            'enabled' => false,  // Varsayılan blokları etkinleştirme
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Widget Ayarları
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'enabled' => true,  // Widget entegrasyonu
        'cache' => [
            'enabled' => true,  // Önbellek etkin
            'ttl' => 3600,      // Saniye cinsinden TTL (1 saat)
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Önbellek Ayarları
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'driver' => 'redis', // Redis, file, array vb.
        'prefix' => 'studio_',
        'ttl' => 3600,      // Saniye cinsinden TTL (1 saat)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Güvenlik Ayarları
    |--------------------------------------------------------------------------
    */
    'security' => [
        'sanitize_html' => true, // HTML sanitizasyonu
        'allowed_tags' => '<p><div><span><h1><h2><h3><h4><h5><h6><ul><ol><li><img><a><br><table><tr><td><th><blockquote><b><i><strong><em>', // İzin verilen HTML etiketleri
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Geliştirici Ayarları
    |--------------------------------------------------------------------------
    */
    'dev' => [
        'debug' => env('APP_DEBUG', false),
        'auto_publish_assets' => env('APP_ENV') !== 'production',
    ],
];